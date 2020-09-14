<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\helpers\UrlHelper;
use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib as Functions;
use application\libraries\TimingLibrary as Timing;
use application\libraries\adm\AdministrationLib as Administration;

class Tasks extends Controller
{
    private array $user;

    /* SUMMARY
     *
     * constructor
     * buildPage
     * buildUpdatesBlock
     * getLastBackupActions
     * getLastCleanupActions
     * getStatLastUpdateActions
     * getTaskData
     * isTaskScheduled
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load language
        parent::loadLang(['adm/global', 'adm/tasks']);

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

        // Build the page
        $this->buildPage();
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/tasks_view',
                array_merge(
                    $this->langs->language,
                    $this->buildUpdatesBlock()
                )
            )
        );
    }

    // ------------------------------------------------------ buildUpdatesBlock

    private function buildUpdatesBlock(): array
    {
        $update_tasks  = ['stat_last_update', 'last_backup', 'last_cleanup'];
        $update_blocks = [];

        foreach ($update_tasks as $task) {
            $update_blocks[] = $this->getTaskData($task);
        }

        return ['tasks_list' => $update_blocks];
    }

    // --------------------------------------------------- getLastBackupActions

    private function getLastBackupActions(): string
    {
        return UrlHelper::setUrl(
            'admin.php?page=backup',
            '<i class="fas fa-cogs" 
                data-toggle="popover" 
                data-placement="top"
                data-trigger="hover" 
                data-content="' . $this->langs->line('ta_backup_title') 
            . '"></i>',
            $this->langs->line('ta_backup_title')
        );
    }

    // -------------------------------------------------- getLastCleanupActions

    private function getLastCleanupActions(): string
    {
        return '';
    }

    // ----------------------------------------------- getStatLastUpdateActions

    private function getStatLastUpdateActions(): string
    {
        return UrlHelper::setUrl(
            'admin.php?page=rebuildhighscores',
            '<i class="fas fa-play" 
                data-toggle="popover" 
                data-placement="top"
                data-trigger="hover" 
                data-content="' . $this->langs->line('ta_buildstats_title') 
            . '"></i>',
            $this->langs->line('ta_buildstats_title')
        );
    }

    // ------------------------------------------------------------ getTaskData

    private function getTaskData(string $task): array
    {
        $next_run = '-';
        $last_run = '-';

        if ($this->isTaskScheduled($task)) {
            $task_time = Functions::readConfig($task);
            $next_run  = Timing::formatExtendedDate($task_time);
            $last_run  = Format::prettyTime(time() - $task_time);
        }

        return [
            'name'     => $this->langs->line('ta_' . $task),
            'next_run' => $next_run,
            'last_run' => $last_run,
            'actions'  => $this->{'get' . ucwords(strtr($task, ['_' => ''])) . 'Actions'}(),
        ];
    }

    // -------------------------------------------------------- isTaskScheduled

    private function isTaskScheduled(string $task): bool
    {
        return !($task == 'last_backup' && Functions::readConfig('auto_backup') == 0);
    }

}
