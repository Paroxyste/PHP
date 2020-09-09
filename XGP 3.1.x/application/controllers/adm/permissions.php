<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\UserRanksEnumerator as UserRanks;

use application\libraries\FunctionsLib as Functions;
use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\adm\Permissions as Per;

class Permissions extends Controller
{
    private string $alert;
    private array  $user;

    /* SUMMARY
     * 
     * constructor
     * buildListOfPermissions
     * buildPage
     * buildRolesList
     * runAction
     * setUpPermissions
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load language
        parent::loadLang(['adm/global', 'adm/permissions', 'adm/menu']);

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

        // Init a new permissions object
        $this->setUpPermissions();

        // Time to do something
        $this->runAction();

        // Build the page
        $this->buildPage();
    }

    // ------------------------------------------------- buildListOfPermissions

    private function buildListOfPermissions(): array
    {
        $sections_list    = [];
        $modules_list     = [];
        $permissions_list = [];

        // Get necessary data
        $sections = $this->permissions->getAdminSections();
        $modules  = $this->permissions->getAdminModules();
        $roles    = $this->buildRolesList();

        // Build sections array
        foreach ($sections as $section_id => $section) {
            // Build modules array
            foreach ($modules[$section_id] as $module) {
                // Build permissions array
                foreach ($roles as $role => $name) {
                    $permissions_list[] = [
                        'module' => $module,
                        'role'   => $role,

                        'permission_checked'  => 
                            $this->permissions->isAccessAllowed($module, $role) ? 'checked' : '',

                        'permission_disabled' => 
                            $role == UserRanks::ADMIN ? 'disabled' : '',
                    ];
                }

                // Put all inside
                $modules_list[] = [
                    'page_module'       => $module,
                    'page_module_title' => $this->langs->language[$module],
                    'permissions_list'  => $permissions_list,
                ];

                unset($permissions_list);

            }

            // Put all inside
            $sections_list[$section_id] = [
                'section_name'  => ucfirst($section),
                'section_title' => $this->langs->language[$section],
                'roles_list'    => $roles,
                'modules_list'  => $modules_list,
            ];

            unset($modules_list);

        }

        return [
            'sections_list' => $sections_list,
        ];
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/permissions_view',
                array_merge(
                    $this->langs->language,
                    [
                        'alert' => $this->alert ?? ''
                    ],
                    $this->buildListOfPermissions()
                )
            )
        );
    }

    // --------------------------------------------------------- buildRolesList

    private function buildRolesList(): array
    {
        $roles_list = [];

        foreach ($this->permissions->getRoles() as $role) {
            $roles_list[$role] = [
                'role_name' => $this->langs->language['user_level'][$role],
            ];
        }

        return $roles_list;
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $permissions = filter_input_array(INPUT_POST);

        if ($permissions) {
            $modules = $this->permissions->getAdminModules();
            $roles   = $this->permissions->getRoles(true);

            foreach ($modules as $module) {
                foreach ($module as $module_name) {
                    foreach ($roles as $role) {

                        if (
                            isset($permissions[$module_name][$role])
                            && $permissions[$module_name][$role] == 'on'
                        ) {
                            $this->permissions->grantAccess($module_name, $role);
                        } else {
                            $this->permissions->removeAccess($module_name, $role);
                        }

                    }
                }
            }

            $this->permissions->savePermissions();

            $this->alert = Administration::saveMessage(
                'ok', 
                $this->langs->line('pr_all_ok_message'));

        }

    }

    // ------------------------------------------------------- setUpPermissions

    private function setUpPermissions(): void
    {
        $this->permissions = new Per(
            Functions::readConfig('admin_permissions')
        );
    }

}