<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FunctionsLib;
use application\libraries\adm\AdministrationLib as Administration;

class Premium extends Controller
{
    const PREMIUM_SETTINGS = [
        'premium_url'                     => FILTER_VALIDATE_URL,
        'merchant_price'                  => FILTER_VALIDATE_FLOAT,
        'merchant_base_min_exchange_rate' => FILTER_VALIDATE_FLOAT,
        'merchant_base_max_exchange_rate' => FILTER_VALIDATE_FLOAT,
        'merchant_metal_multiplier'       => FILTER_VALIDATE_FLOAT,
        'merchant_crystal_multiplier'     => FILTER_VALIDATE_FLOAT,
        'merchant_deuterium_multiplier'   => FILTER_VALIDATE_FLOAT,
        'registration_dark_matter'        => FILTER_VALIDATE_INT,
    ];


    private string $alert = '';
    private array  $user;

    /* SUMMARY
     * 
     * constructor
     * buildPage
     * getPremiumSettings
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
        parent::loadLang(['adm/global', 'adm/premium']);

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
                'adm/premium_view',
                array_merge(
                    $this->langs->language,
                    $this->getPremiumSettings(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    // ----------------------------------------------------- getPremiumSettings

    private function getPremiumSettings(): array
    {
        return array_filter(
            FunctionsLib::readConfig('', TRUE),
            function ($key) {
                return array_key_exists($key, self::PREMIUM_SETTINGS);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $data = filter_input_array(INPUT_POST, self::PREMIUM_SETTINGS);

        if ($data) {
            $data = array_diff($data, [null, false]);

            foreach ($data as $option => $value) {

                if (
                    is_numeric($value) 
                    && $value >= 0 
                    || is_string($value)
                ) {
                    FunctionsLib::updateConfig($option, $value);
                }

            }

            $this->alert = Administration::saveMessage(
                'ok', 
                $this->langs->line('pr_all_ok_message')
            );

        }
    }

}