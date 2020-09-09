<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FunctionsLib as Functions;
use application\libraries\adm\AdministrationLib as Administration;


class Planets extends Controller
{
    const PLANET_SETTINGS = [
        'initial_fields'         => FILTER_VALIDATE_INT,
        'metal_basic_income'     => FILTER_VALIDATE_INT,
        'crystal_basic_income'   => FILTER_VALIDATE_INT,
        'deuterium_basic_income' => FILTER_VALIDATE_INT,
        'energy_basic_income'    => FILTER_VALIDATE_INT,
    ];

    private string $alert;
    private array  $user;

    /* SUMMARY
     * 
     * constructor
     * buildPage
     * getNewPlanetSettings
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
        parent::loadLang(['adm/global', 'adm/planets']);

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
                'adm/planets_view',
                array_merge(
                    $this->langs->language,
                    $this->getNewPlanetSettings(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    // --------------------------------------------------- getNewPlanetSettings

    private function getNewPlanetSettings(): array
    {
        return array_filter(
            Functions::readConfig('', TRUE),
            function ($key) {
                return array_key_exists($key, self::PLANET_SETTINGS);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $data = filter_input_array(INPUT_POST, self::PLANET_SETTINGS);

        if ($data) {
            $data = array_diff($data, [null, false]);

            foreach ($data as $option => $value) {
                Functions::updateConfig($option, $value);
            }

            $this->alert = Administration::saveMessage(
                'ok', 
                $this->langs->line('np_all_ok_message')
            );
        }
    }

}