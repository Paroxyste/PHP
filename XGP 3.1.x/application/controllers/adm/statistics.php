<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\UserRanksEnumerator as UserRanks;

use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\FunctionsLib;


class Statistics extends Controller
{
    const STATISTICS_SETTINGS = [
        'stat_points' => [
            'filter'  => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 1
            ],
        ],

        'stat_update_time' => [
            'filter'  => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 1
            ],
        ],

        'stat_admin_level' => [
            'filter'  => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => UserRanks::PLAYER, 
                'max_range' => UserRanks::ADMIN
            ],
        ],
    ];

    private string $alert;
    private array  $user;
    private int    $user_level;

    /* SUMMARY
     *
     * constructor
     * buildPage
     * getStatisticsSettings
     * userLevels
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
        parent::loadLang(['adm/global', 'adm/statistics']);

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
                'adm/statistics_view',
                array_merge(
                    $this->langs->language,
                    $this->getStatisticsSettings(),
                    $this->userLevels(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    // -------------------------------------------------- getStatisticsSettings

    private function getStatisticsSettings(): array
    {
        return array_filter(
            FunctionsLib::readConfig('', TRUE),
            function ($value, $key) {
                if ($key == 'stat_admin_level') {
                    $this->user_level = $value;
                }

                return array_key_exists($key, self::STATISTICS_SETTINGS);
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    // ------------------------------------------------------------- userLevels

    private function userLevels(): array
    {
        $user_levels = [];
        $ranks = [
            UserRanks::PLAYER,
            UserRanks::GO,
            UserRanks::SGO,
            UserRanks::ADMIN,
        ];

        foreach ($ranks as $rank_id) {
            $user_levels[] = [
                'id'   => $rank_id,
                'sel'  => $this->user_level == $rank_id ? 'selected="selected"' : '',
                'name' => $this->langs->language['user_level'][$rank_id],
            ];
        }

        return [
            'user_levels' => $user_levels,
        ];
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $data = filter_input_array(INPUT_POST, self::STATISTICS_SETTINGS);

        if ($data) {
            $data = array_diff($data, [null, false]);

            foreach ($data as $option => $value) {
                FunctionsLib::updateConfig($option, $value);
            }

            $this->alert = Administration::saveMessage(
                'ok', 
                $this->langs->line('cs_all_ok_message')
            );
        }
    }

}