<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib;
use application\libraries\adm\AdministrationLib as Administration;

use JsonException;

class Home extends Controller
{
    private array $user;

    /* SUMMARY
     * 
     * constructor
     * buildAlertsBlock
     * buildPage
     * checkUpdates
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/home');
        parent::loadLang(['adm/global', 'adm/home']);

        // Set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (
            !Administration::haveAccess($this->user['user_authlevel'])
        ) {
            Administration::noAccessMessage(
                $this->langs->line('no_permissions')
            );
        }

        // Build the page
        $this->buildPage();
    }

    // ------------------------------------------------------- buildAlertsBlock

    private function buildAlertsBlock(): array
    {
        $alert = [];

        if (
            $this->user['user_authlevel'] >= 3
        ) {

            if (
                (bool) (@fileperms(XGP_ROOT . CONFIGS_PATH . 'config.php') 
                & 0x0002)
            ) {
                $alert[] = $this->langs->line('hm_config_file_writable');
            }

            if (
                $this->checkUpdates()
            ) {
                $alert[] = $this->langs->line('hm_old_version');
            }

            if (
                Administration::installDirExists()
            ) {
                $alert[] = $this->langs->line('hm_install_file_detected');
            }

            if (
                FunctionsLib::readConfig('version') != SYSTEM_VERSION
            ) {
                $alert[] = $this->langs->line('hm_update_required');
            }

        }

        $alerts_count = count($alert);

        $messages = $second_style = $error_type = NULL;

        if (
            $alerts_count > 1
        ) {
            $messages     = join('<br>', $alert);
            $second_style = 'alert-danger';
            $error_type   = $this->langs->line('hm_error');
        }

        if (
            $alerts_count == 1
        ) {
            $messages     = join('<br>', $alert);
            $second_style = 'alert-warning';
            $error_type   = $this->langs->line('hm_warning');
        }

        return [
            'error_message' => $messages     ?? $this->langs->line('hm_all_ok'),
            'second_style'  => $second_style ?? 'alert-success',
            'error_type'    => $error_type   ?? $this->langs->line('hm_ok'),
        ];
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        $server_stats = $this->Home_Model->getUsersStats();

        parent::$page->displayAdmin($this->getTemplate()->set(
                'adm/home_view',
                array_merge($this->langs->language, $server_stats,
                    [
                        'alert' => [
                            $this->buildAlertsBlock()
                        ],

                        'average_user_points' => Format::shortlyNumber(
                            $server_stats['average_user_points']
                        ),

                        'average_alliance_points' => Format::shortlyNumber(
                            $server_stats['average_alliance_points']
                        ),

                        'database_size' => Format::prettyBytes(
                            $this->Home_Model->getDbSize()['db_size']
                        ),

                        'database_server' => $this->Home_Model->getDbVersion(),
                        'php_version'     => PHP_VERSION,
                        'server_version'  => SYSTEM_VERSION,
                    ]
                )
            )
        );
    }

    // ----------------------------------------------------------- checkUpdates

    private function checkUpdates(): bool
    {
        try {
            if (
                function_exists('file_get_contents')
            ) {
                $url = 'https://updates.xgproyect.org/latest.php';

                $file_data = @file_get_contents(
                    $url, 
                    FALSE, 
                    stream_context_create(
                        [
                            // On second
                            'https' => ['timeout' => 1,],
                        ]
                    )
                );

                if (
                    $file_data
                ) {
                    $system_v = FunctionsLib::readConfig('version');
                    $last_v   = 
                        @json_decode(
                            $file_data,
                            FALSE,
                            512,
                            JSON_THROW_ON_ERROR
                        )->version;

                    return version_compare($system_v, $last_v, '<');
                }
            }

            return FALSE;
        } catch (JsonException $e) {
            return FALSE;
        }
    }

}