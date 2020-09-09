<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\FunctionsLib;

class Registration extends Controller
{
    const REGISTRATION_SETTINGS = [
        'reg_enable'          => FILTER_SANITIZE_STRING,
        'reg_welcome_message' => FILTER_SANITIZE_STRING,
        'reg_welcome_email'   => FILTER_SANITIZE_STRING,
    ];

    private string $alert;
    private array  $user;

    /* SUMMARY
     *
     * constructor
     * buildPage
     * getNewUserRegistrationSettings
     * runAction
     * setChecked
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load language
        parent::loadLang(['adm/global', 'adm/registration']);

        // Set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (
            !Administration::authorization
            (__CLASS__, 
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

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/registration_view',
                array_merge(
                    $this->langs->language,
                    $this->getNewUserRegistrationSettings(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    // ----------------------------------------- getNewUserRegistrationSettings
    private function getNewUserRegistrationSettings()
    {
        return $this->setChecked(
            array_filter(
                FunctionsLib::readConfig('', TRUE),
                function ($key) {
                    return array_key_exists($key, self::REGISTRATION_SETTINGS);
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $data = filter_input_array(INPUT_POST, 
                                   self::REGISTRATION_SETTINGS, 
                                   TRUE);

        if ($data) {

            foreach ($data as $option => $value) {
                FunctionsLib::updateConfig($option, $value == 'on' ? 1 : 0);
            }

            $this->alert = Administration::saveMessage(
                'ok', 
                $this->langs->line('ur_all_ok_message')
            );

        }
    }

    // ------------------------------------------------------------- setChecked

    private function setChecked(array $settings): array
    {

        foreach ($settings as $key => $value) {
            $settings[$key] = $value == 1 ? 'checked="checked"' : '';
        }

        return $settings;
    }

}