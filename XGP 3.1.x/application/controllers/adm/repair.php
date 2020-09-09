<?php

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\adm\AdministrationLib as Administration;

class Repair extends Controller
{
    private $current_user;

    /* SUMMARY
     * 
     * constructor
     * buildPage
     *
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + langugae
        parent::loadModel('adm/repair');
        parent::loadLang(['adm/global', 'adm/repair']);

        $this->current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (
            !Administration::authorization(
                __CLASS__, 
                (int) $this->current_user['user_authlevel'])
        ) {
            die(Administration::noAccessMessage(
                $this->langs->line('no_permissions')
            ));
        }

        $this->buildPage();
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage()
    {
        $parse = $this->langs->language;
        $parse['alert'] = '';

        if (!$_POST) {
            $tables = $this->Repair_Model->getAllTables();

            $parse['display']    = 'block';
            $parse['tables']     = '';
            $parse['np_general'] = '';
            $parse['results']    = '';
            $parse['head']       = $this->getTemplate()->set(
                'adm/repair_row_head_view', 
                $this->langs->language
            );

            foreach ($tables as $row) {
                $row['row']          = $row['table_name'];
                $row['status_style'] = 'text-info';

                $row['data']     = FormatLib::prettyBytes($row['data_length']);
                $row['index']    = FormatLib::prettyBytes($row['index_length']);
                $row['overhead'] = FormatLib::prettyBytes($row['data_free']);
                

                $parse['tables'] .= $this->getTemplate()->set(
                    'adm/repair_row_view',
                    array_merge(
                        $row,
                        $this->langs->language
                    )
                );
            }

        } else {
            $parse['display']    = 'none';
            $parse['tables']     = '';
            $parse['np_general'] = '';
            $parse['head'] = $this->getTemplate()->set(
                'adm/repair_result_head_view', 
                $this->langs->language
            );

            if (
                isset($_POST['table']) 
                && is_array($_POST['table'])
            ) {
                $result_rows = '';

                foreach ($_POST['table'] as $key => $table) {
                    $parse['row'] = $table;

                    $this->Repair_Model->checkTable($table);
                    $parse['result'] = $this->langs->line('db_check_ok');
                    $result_rows    .= $this->getTemplate()->set(
                        'adm/repair_result_view', 
                        $parse
                    );

                    if (
                        isset($_POST['Optimize'])
                        && $_POST['Optimize'] == 'yes'
                    ) {
                        $this->Repair_Model->optimizeTable($table);
                        $parse['result'] = $this->langs->line('db_opt');
                        $result_rows    .= $this->getTemplate()->set(
                            'adm/repair_result_view', 
                            $parse
                        );
                    }

                    if (
                        isset($_POST['Repair']) 
                        && $_POST['Repair'] == 'yes'
                    ) {
                        $this->Repair_Model->repairTable($table);
                        $parse['result'] = $this->langs->line('db_rep');
                        $result_rows    .= $this->getTemplate()->set(
                            'adm/repair_result_view', 
                            $parse
                        );
                    }
        
                }

                $parse['results'] = $result_rows;

            } else {
                FunctionsLib::redirect('admin.php?page=repair');
            }
        }

        parent::$page->displayAdmin($this->getTemplate()->set(
            'adm/repair_view',
            $parse
        ));
    }

}