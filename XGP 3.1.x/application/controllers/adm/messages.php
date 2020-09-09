<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\MessagesEnumerator;

use application\libraries\TimingLibrary as Timing;
use application\libraries\adm\AdministrationLib as Administration;

class Messages extends Controller
{
    private string $alert;
    private array  $results;
    private array  $user;

    /* SUMMARY
     * 
     * constructor
     * buildMessageTypeBlock
     * buildPage
     * deleteMessage
     * deleteMessages
     * doSearch
     * runAction
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/messages');
        parent::loadLang(['adm/global', 'adm/messages']);

        // Set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (
            !Administration::authorization(
                __CLASS__, 
                (int) $this->user['user_authlevel'])
        ) {
            die(Administration::noAccessMessage(
                $this->langs->line('no_permissions')
            ));
        }

        // Time to do something
        $this->runAction();

        // Build the page
        $this->buildPage();
    }

    // -------------------------------------------------- buildMessageTypeBlock

    private function buildMessageTypeBlock(): array
    {
        $options_list = [];
        $message_types = [
            MessagesEnumerator::ESPIO,
            MessagesEnumerator::COMBAT,
            MessagesEnumerator::EXP,
            MessagesEnumerator::ALLY,
            MessagesEnumerator::USER,
            MessagesEnumerator::GENERAL,
        ];

        foreach ($message_types as $type) {
            $options_list[] = [
                'value' => $type,
                'name'  => $this->langs->language['mg_types'][$type],
            ];
        }

        return [
            'type_options' => $options_list,
        ];
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/messages_view',
                array_merge( 
                    $this->langs->language,
                    $this->buildMessageTypeBlock(),
                    [
                        'alert'        => $this->alert,
                        'results'      => $this->results,
                        'show_search'  => $this->results ? '' : 'show',
                        'show_results' => $this->results ? 'show' : '',
                    ]
                )
            )
        );
    }

    // --------------------------------------------------------------- doSearch

    private function doSearch(array $to_search): void
    {
        // Build the query, run the query and return the result
        $search_results = 
            $this->Messages_Model->getAllMessagesFiltered($to_search);

        $results_list = [];

        if ($search_results) {

            foreach ($search_results as $result) {
                $results_list[] = array_merge(
                    $this->langs->language,
                    $result,
                    [
                        'message_time' => Timing::formatExtendedDate(
                            $result['message_time']
                        ),

                        'message_type' => 
                            $this->langs->language['mg_types'][$result['message_type']],

                        'message_text' => nl2br($result['message_text']),
                    ]
                );
            }

            $this->results = $results_list;

        } else {
            $this->alert = Administration::saveMessage(
                'warning', 
                $this->langs->line('mg_no_results')
            );
        }
    }

    // ---------------------------------------------------------- deleteMessage

    private function deleteMessage(int $message_id): void
    {
        $this->Messages_Model->deleteAllMessagesByIds([$message_id]);

        $this->alert = Administration::saveMessage(
            'ok', 
            $this->langs->line('mg_delete_ok')
        );
    }

    // --------------------------------------------------------- deleteMessages

    private function deleteMessages(array $messages): void
    {
        $ids = [];

        /* Build the ID's list to delete, we're going to delete them all 
           in one single query
        */

        foreach ($messages as $message_id => $delete_status) {

            if (
                $delete_status == 'on' 
                && $message_id > 0 
                && is_numeric($message_id)
            ) {
                $ids[] = $message_id;
            }

        }

        $this->Messages_Model->deleteAllMessagesByIds($ids);

        $this->alert = Administration::saveMessage(
            'ok', 
            $this->langs->line('mg_delete_ok')
        );
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $action        = filter_input_array(INPUT_POST);
        $single_delete = filter_input_array(INPUT_GET, [
            'action'    => FILTER_SANITIZE_STRING,
            'messageId' => [
                'filter'  => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
        ]);

        if ($action) {
            $filtered_action = array_filter(
                $action,
                function ($value) {
                    return !is_null($value) && $value !== false && $value !== '';
                }
            );

            if (isset($filtered_action['search'])) {
                $this->doSearch($filtered_action);
            }

            if (isset($filtered_action['delete_messages'])) {
                $this->deleteMessages($filtered_action['delete_messages']);
            }
        }

        if (
            isset($single_delete['action']) == 'delete'
            && isset($single_delete['messageId'])
        ) {
            $this->deleteMessage($single_delete['messageId']);
        }
    }

}