<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\libraries\adm\AdministrationLib as Administration;


class Languages extends Controller
{

    private string $alert;
    private string $current_file;
    private array  $user;

    /* SUMMARY
     *
     * constructor
     * buildPage
     * doFileAction
     * doSaveAction
     * getContents
     * getFiles
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
        parent::loadLang(['adm/global', 'adm/languages']);

        //Set data
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
            $this->getTemplate()->set('adm/languages_view', array_merge(
                $this->langs->language,
                $this->getFiles(),
                $this->getContents(),
                [
                    'edit_file' => $this->current_file,
                    'alert'     => $this->alert ?? '',
                ]
            ))
        );
    }

    // ----------------------------------------------------------- doFileAction

    private function doFileAction(string $file): void
    {
        $this->current_file = $file;
    }

    // ----------------------------------------------------------- doSaveAction

    private function doSaveAction(string $file_data): void
    {
        // Get the file
        $file = XGP_ROOT . LANG_PATH . DIRECTORY_SEPARATOR . $this->current_file;

        // Open the file
        $fs = @fopen($file, 'w');

        if (
            $fs 
            && $file_data != NULL
        ) {
            fwrite($fs, $file_data);

            fclose($fs);
        }

        $this->alert = Administration::saveMessage(
            'ok', 
            $this->langs->line('le_all_ok_message')
        );
    }

    // ------------------------------------------------------------ getContents

    private function getContents(): array
    {
        $file = XGP_ROOT . LANG_PATH . DIRECTORY_SEPARATOR . $this->current_file;

        // Open the file
        $fs = @fopen($file, 'a+');
        $contents = '';

        if ($fs) {
            while (!feof($fs)) {
                $contents .= fgets($fs, 1024);
            }

            fclose($fs);
        }

        if (
            !$contents 
            && $this->current_file != NULL
        ) {
            $this->alert = Administration::saveMessage(
                'error', 
                $this->langs->line('le_all_error_reading')
            );
        }

        return [
            'contents' => $contents ?? '',
        ];
    }

    // --------------------------------------------------------------- getFiles

    private function getFiles(): array
    {
        chdir(XGP_ROOT . LANG_PATH);

        $langs_files  = glob('{,*/,*/*/,*/*/*/}*.php', GLOB_BRACE);
        $lang_options = [];

        foreach ($langs_files as $file) {
            $lang_options[] = [
                'lang_file' => $file,
                'selected'  => ($this->current_file == $file) ? 'selected = selected' : '',
            ];
        }

        return ['language_files' => $lang_options];
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $action = filter_input_array(INPUT_POST);

        if ($action) {

            if (isset($action['file'])) {
                $this->doFileAction($action['file']);
            }

            if (
                isset($action['save']) 
                && $action['save'] != NULL
            ) {
                $this->doSaveAction($action['save']);
            }

        }
    }

}