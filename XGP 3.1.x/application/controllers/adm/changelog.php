<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\FunctionsLib as Functions;

use DateTime;
use Exception;


class Changelog extends Controller
{

    private array $user;

    /* SUMMARY
     *
     * constructor
     * addAction
     * buildListOfEntries
     * buildPage
     * deleteAction
     * editAction
     * getActionData
     * getAlertMessage
     * getAllLanguages
     * isValidAction
     * isValidDate
     * isValidVersion
     * runAction
     * saveAction
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/changelog');
        parent::loadLang(['adm/global', 'adm/changelog']);

        // Set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (
            !Administration::authorization(
                __CLASS__, 
                (int) $this->user['user_authlevel']
            )
        ) {
            die(Administration::noAccessMessage(
                $this->langs->line('no_permissions'))
            );
        }

        // Time to do something
        $this->runAction();

        // Build the page
        $this->buildPage();
    }

    // -------------------------------------------------------------- addAction

    private function addAction(): void
    {
        $this->saveAction();

        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/changelog_form_view', 
                array_merge($this->getActionData('add'))
            )
        );
    }

    // ----------------------------------------------------- buildListOfEntries

    private function buildListOfEntries(): array
    {
        $entries = $this->Changelog_Model->getAllEntries();
        $entries_list = [];

        foreach ($entries as $entry) {
            $entries_list[] = array_merge($this->langs->language, $entry);
        }

        return $entries_list;
    }

    // -------------------------------------------------------------- buildPage
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/changelog_view', 
                array_merge($this->langs->language,
                    [
                        'changelog' => $this->buildListOfEntries(),
                        'alert'     => $this->getAlertMessage(),
                    ]
                )
            )
        );
    }

    // ----------------------------------------------------------- deleteAction

    private function deleteAction(int $changelog_id): void
    {
        $this->Changelog_Model->deleteEntry($changelog_id);

        Functions::redirect('admin.php?page=changelog&success=delete');
    }

    // ------------------------------------------------------------- editAction

    private function editAction(int $changelog_id): void
    {
        $this->saveAction();

        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/changelog_form_view',
                $this->getActionData('edit', $changelog_id)
            )
        );
    }

    // ---------------------------------------------------------- getActionData

    private function getActionData(string $action, int $changelog_id = 0): array
    {
        $changelog_lang_id     = 0;
        $changelog_version     = '';
        $changelog_date        = date('Y-m-d');
        $changelog_description = '';

        if (
            $action == 'edit'
        ) {

            if (
                $result = $this->Changelog_Model->getSingleEntry($changelog_id)
            ) {
                $changelog_lang_id     = $result->getChangelogLangId();
                $changelog_version     = $result->getChangelogVersion();
                $changelog_date        = $result->getChangelogDate();
                $changelog_description = $result->getChangelogDescription();
            } else {
                
                Functions::redirect('admin.php?page=changelog');
            }

        }

        return array_merge($this->langs->language,
            [
                'js_path'      => JS_PATH,
                'action'       => $action,
                'changelog_id' => $changelog_id,

                'current_action' => strtr(
                    $this->langs->line('ch_' . $action . '_action'),
                    ['%s' => $changelog_date]
                ),

                'changelog_date'        => $changelog_date,
                'changelog_version'     => $changelog_version,
                'changelog_description' => $changelog_description,

                'languages' => $this->getAllLanguages($changelog_lang_id),
            ]
        );
    }

    // -------------------------------------------------------- getAlertMessage

    private function getAlertMessage(): string
    {
        $action_type = filter_input(INPUT_GET, 'success');
        $alert = '';

        if (
            $action_type
        ) {
            $alert = Administration::saveMessage(
                'ok',
                $this->langs->line('ch_action_' . $action_type . '_done')
            );
        }

        return $alert;
    }

    // -------------------------------------------------------- getAllLanguages

    private function getAllLanguages(int $default_language): array
    {
        $languages = $this->Changelog_Model->getAllLanguages();
        $list_of_languages = [];

        foreach ($languages as $language) {
            $list_of_languages[] = array_merge(
                $language,
                [
                    'selected' => ($default_language == $language['language_id'] ? 'selected' : ''),
                ]
            );
        }

        return $list_of_languages;
    }

    // ---------------------------------------------------------- isValidAction

    private function isValidAction(?string $action): ?string
    {
        if (
            in_array($action, ['add', 'edit'])
        ) {
            return $action;
        }

        return NULL;
    }

    // ------------------------------------------------------------ isValidDate
    private function isValidDate(?string $date): ?string
    {
        try {
            $datetime = new DateTime($date);

            return $datetime->format('Y-m-d');
        } catch (Exception $e) {
            return NULL;
        }
    }

    // --------------------------------------------------------- isValidVersion
    private function isValidVersion(?string $version): ?string
    {
        preg_match_all(
            '/^(0|[1-9]\d*)\.((0|[1-9]\d*)\.)?(0|[1-9]\d*)(-(0|[1-9]\d*|\d*[a-zA-Z][0-9a-zA-Z]*))?$/',
            $version,
            $matches
        );

        if (
            isset($matches[0][0])
        ) {
            return $matches[0][0];
        }

        return NULL;
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        // Route to the right page
        $allowed_actions = ['add', 'edit', 'delete'];

        $sub_page     = filter_input(INPUT_GET, 'action');

        $changelog_id = filter_input(
            INPUT_GET, 'changelogId', FILTER_VALIDATE_INT
        );

        if (
            isset($sub_page) 
            && isset($changelog_id)
        ) {
            $this->{$sub_page . 'Action'}($changelog_id);
        }

        if (
            isset($sub_page) 
            && !isset($changelog_id)
        ) {
            $this->{$sub_page . 'Action'}();
        }
    }

    // ------------------------------------------------------------- saveAction

    private function saveAction(): void
    {
        // Post actions
        $data = filter_input_array(INPUT_POST, [
            'changelog_id' => [
                'filter' => FILTER_VALIDATE_INT,
            ],

            'action' => [
                'filter'  => FILTER_CALLBACK,
                'options' => [$this, 'isValidAction'],
            ],

            'changelog_date' => [
                'filter'  => FILTER_CALLBACK,
                'options' => [$this, 'isValidDate'],
            ],

            'changelog_version' => [
                'filter'  => FILTER_CALLBACK,
                'options' => [$this, 'isValidVersion'],
            ],

            'changelog_language' => [
                'filter'  => FILTER_VALIDATE_INT,
                'options' => [
                    'default'   => 1,
                    'min_range' => 1,
                ],
            ],

            'text' => [
                'filter' => FILTER_SANITIZE_STRING,
            ],
        ]);

        if (
            $data
        ) {
            $valid = TRUE;

            foreach (
                $data as $field => $value
            ) {
                if (
                    $value === FALSE 
                    || $value === NULL
                ) {
                    $valid = FALSE;
                    break;
                }
            }

            if (
                $valid
            ) {

                if (
                    $data['action'] == 'add'
                ) {
                    $this->Changelog_Model->addEntry($data);
                }

                if (
                    $data['action'] == 'edit'
                ) {
                    $this->Changelog_Model->updateEntry($data);
                }

                Functions::redirect(
                    'admin.php?page=changelog&success=' . $data['action']
                );

            }
        }
    }

}
