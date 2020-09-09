<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FunctionsLib;
use application\libraries\adm\AdministrationLib as Administration;

class Modules extends Controller
{
    private string $alert;
    private array  $user;

    /* SUMMARY
     *
     * constructor
     * buildModulesList
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

        // load language
        parent::loadLang(['adm/global', 'adm/modules']);

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

    // ------------------------------------------------------- buildModulesList

    private function buildModulesList(): array
    {
        $modules_list = [];

        $modules = explode(';', FunctionsLib::readConfig('modules'));

        if ($modules) {

            foreach ($modules as $module => $status) {

                if ($status != NULL) {
                    $modules_list[] = [
                        'module'       => $module,
                        'module_name'  => $this->langs->language['mdl_modules'][$module],
                        'module_value' => ($status == 1) ? 'checked' : '',
                        'color'        => ($status == 1) ? 'success' : 'danger',
                    ];
                }

            }

        }

        return $modules_list;
    }

    // -------------------------------------------------------------- biuldPage

    private function buildPage(): void
    {

        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/modules_view',
                array_merge(
                    $this->langs->language,
                    [
                        'alert'   => $this->alert ?? '',
                        'modules' => $this->buildModulesList(),
                    ]
                )
            )
        );
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $modules = filter_input_array(INPUT_POST);

        if ($modules) {
            $modules_count = 
                count(explode(';', FunctionsLib::readConfig('modules')));

            for ($i = 0; $i < $modules_count; $i++) {
                $modules_set[] = isset($modules["status{$i}"]) ? 1 : 0;
            }

            FunctionsLib::updateConfig('modules', join(';', $modules_set));

            $this->alert = Administration::saveMessage(
                'ok', 
                $this->langs->line('mdl_all_ok_message')
            );

        }
    }

}