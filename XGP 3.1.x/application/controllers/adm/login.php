<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FunctionsLib;
use application\libraries\adm\AdministrationLib as Administration;

class Login extends Controller
{
    /* SUMMARY
     *
     * constructor
     * buildPage
     * getAlert
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
        parent::loadModel('adm/login');
        parent::loadLang('adm/login');

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
                'adm/login_view',
                array_merge(
                    $this->langs->language,
                    [
                        'alert'    => $this->getAlert(),
                        'redirect' => filter_input(INPUT_GET, 
                                                   'redirect', 
                                                   FILTER_SANITIZE_STRING),
                    ]
                )
            ),
            FALSE,
            FALSE,
            FALSE
        );
    }

    // --------------------------------------------------------------- getAlert

    private function getAlert(): string
    {
        $error = filter_input(INPUT_GET, 'error', FILTER_VALIDATE_INT);

        if ($error == 1) {
            return Administration::saveMessage(
                'error', 
                $this->langs->line('lg_error_wrong_data'), 
                FALSE
            );
        }

        return '';
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $login_data = filter_input_array(INPUT_POST, [
            'inputEmail'    => FILTER_VALIDATE_EMAIL,
            'inputPassword' => FILTER_SANITIZE_STRING,
        ]);

        if ($login_data) {
            $login = $this->Login_Model->getLoginData(
                $login_data['inputEmail']
            );

            if ($login) {

                if (
                    password_verify(
                        $login_data['inputPassword'], 
                        $login['user_password']
                    )
                    && Administration::adminLogin(
                        $login['user_id'], 
                        $login['user_password']
                    )
                ) {
                    $redirect = filter_input(
                        INPUT_GET, 
                        'redirect', 
                        FILTER_SANITIZE_STRING) ?? 'home';

                    if ($redirect == NULL) {
                        $redirect = 'home';
                    }

                    // Redirect to panel home
                    FunctionsLib::redirect(
                        SYSTEM_ROOT . 'admin.php?page=' . $redirect
                    );
                }

            }

            // If login fails
            FunctionsLib::redirect(
                SYSTEM_ROOT . 'admin.php?page=login&error=1'
            );

        }
    }

}