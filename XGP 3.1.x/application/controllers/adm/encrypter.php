<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FunctionsLib as Functions;
use application\libraries\adm\AdministrationLib as Administration;

class Encrypter extends Controller
{

    private array  $user;
    private string $encrypted   = '';
    private string $unencrypted = '';


    /* SUMMARY
     * 
     * constructor
     * buildPage
     * runAction
     *
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load Language
        parent::loadLang(['adm/global', 'adm/encrypter']);

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

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/encrypter_view',
                array_merge($this->langs->language,
                    [
                        'unencrypted' => $this->unencrypted ?? '',
                        'encrypted'   => $this->encrypted   ?? '',
                    ]
                )
            )
        );
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $unencrypted = filter_input(INPUT_POST, 'unencrypted');

        if ($unencrypted) {
            $this->unencrypted = $unencrypted;
            $this->encrypted   = Functions::hash($unencrypted);
        }
    }

}
