<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib as Functions;
use application\libraries\Statistics_library as Statistics;
use application\libraries\adm\AdministrationLib as Administration;


class RebuildHighscores extends Controller
{
    private array $result;
    private array $user;

    /* SUMMARY
     *
     * constructor
     * buildPage
     * getStatisticsResult
     * runAction
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load language
        parent::loadLang(['adm/global', 'adm/rebuildhighscores']);

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
                'adm/rebuildhighscores_view',
                array_merge(
                    $this->langs->language,
                    $this->getStatisticsResult()
                )
            )
        );
    }

    // ---------------------------------------------------- getStatisticsResult

    private function getStatisticsResult(): array
    {

        return [
            'memory_p' => strtr('%i / %m', [
                '%i' => Format::prettyBytes($this->result['memory_peak'][0]),
                '%m' => Format::prettyBytes($this->result['memory_peak'][0]),
            ]),

            'memory_i' => strtr('%i / %m', [
                '%i' => Format::prettyBytes($this->result['initial_memory'][0]),
                '%m' => Format::prettyBytes($this->result['initial_memory'][0]),
            ]),

            'memory_e' => strtr('%i / %m', [
                '%i' => Format::prettyBytes($this->result['end_memory'][0]),
                '%m' => Format::prettyBytes($this->result['end_memory'][0]),
            ]),

            'alert' => Administration::saveMessage('ok', strtr(
                $this->langs->line('sb_stats_update'),
                [
                    '%t' => $this->result['totaltime']
                ]
            )),
        ];
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $stObject     = new Statistics();
        $this->result = $stObject->makeStats();

        Functions::updateConfig(
            'stat_last_update', 
            $this->result['stats_time']
        );
    }

}