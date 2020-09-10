<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\helpers\UrlHelper;

use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib as Functions;
use application\libraries\adm\AdministrationLib as Administration;

use DateTime;
use DateTimeZone;

class Server extends Controller
{
    private array $game_config;
    private array $user;

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/server');
        parent::loadLang(['adm/global', 'adm/server']);

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

        /* 
        Time to do something
        $this->runAction();
        */

        // Build the page
        $this->buildPage();
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        /* --------------------------------------------------------------------
        ** SERVER SETTINGS
        */

        // Name
        if (
            isset($_POST['game_logo']) 
            && $_POST['game_logo'] != NULL
        ) {
            $this->game_config['game_logo'] = $_POST['game_logo'];
        }

        // Logo
        if (
            isset($_POST['game_name']) 
            && $_POST['game_name'] != NULL
        ) {
            $this->game_config['game_name'] = $_POST['game_name'];
        }

        // Language
        if (isset($_POST['language'])) {
            $this->game_config['lang'] = $_POST['language'];
        } else {
            $this->game_config['lang'];
        }

        // General rate
        if (
            isset($_POST['game_speed']) 
            && is_numeric($_POST['game_speed'])
        ) {
            $this->game_config['game_speed'] = (2500 * $_POST['game_speed']);
        }

        // Speed of fleet

        if (
            isset($_POST['fleet_speed']) 
            && is_numeric($_POST['fleet_speed'])
        ) {
            $this->game_config['fleet_speed'] = (2500 * $_POST['fleet_speed']);
        }

        // Speed of production
        if (
            isset($_POST['resource_multiplier']) 
            && is_numeric($_POST['resource_multiplier'])
        ) {
            $this->game_config['resource_multiplier'] = $_POST['resource_multiplier'];
        }

        // Admin email
        if (
            isset($_POST['admin_email']) 
            && $_POST['admin_email'] != NULL 
            && Functions::validEmail($_POST['admin_email'])
        ) {
            $this->game_config['admin_email'] = $_POST['admin_email'];
        }

        // Forum link
        if (
            isset($_POST['forum_url']) 
            && $_POST['forum_url'] != NULL
        ) {
            $this->game_config['forum_url'] = UrlHelper::prepUrl($_POST['forum_url']);
        }

        // Activate server
        if (
            isset($_POST['closed']) 
            && $_POST['closed'] == 'on'
        ) {
            $this->game_config['game_enable'] = 1;
        } else {
            $this->game_config['game_enable'] = 0;
        }

        // Game close msg
        if (
            isset($_POST['close_reason']) 
            && $_POST['close_reason'] != NULL
        ) {
            $this->game_config['close_reason'] = addslashes($_POST['close_reason']);
        }

        /* --------------------------------------------------------------------
        ** DATE AND TIME PARAMETERS
        */

        // Short date
        if (
            isset($_POST['date_time_zone']) 
            && $_POST['date_time_zone'] != NULL
        ) {
            $this->game_config['date_time_zone'] = $_POST['date_time_zone'];
        }

        if (
            isset($_POST['date_format']) 
            && $_POST['date_format'] != NULL
        ) {
            $this->game_config['date_format'] = $_POST['date_format'];
        }

        // Extended date
        if (
            isset($_POST['date_format_extended']) 
            && $_POST['date_format_extended'] != NULL
        ) {
            $this->game_config['date_format_extended'] = $_POST['date_format_extended'];
        }

        /* --------------------------------------------------------------------
        ** SEVERAL PARAMETERS
        */

        // Protection
        if (
            isset($_POST['adm_attack']) 
            && $_POST['adm_attack'] == 'on'
        ) {
            $this->game_config['adm_attack'] = 1;
        } else {
            $this->game_config['adm_attack'] = 0;
        }

        // Ships to debris
        if (
            isset($_POST['Fleet_Cdr']) 
            && is_numeric($_POST['Fleet_Cdr'])
        ) {

            if ($_POST['Fleet_Cdr'] < 0) {
                $this->game_config['fleet_cdr'] = 0;
                $Number2 = 0;
            } else {
                $this->game_config['fleet_cdr'] = $_POST['Fleet_Cdr'];
                $Number2 = $_POST['Fleet_Cdr'];
            }

        }

        // Defenses to debris
        if (
            isset($_POST['Defs_Cdr']) 
            && is_numeric($_POST['Defs_Cdr'])
        ) {

            if ($_POST['Defs_Cdr'] < 0) {
                $this->game_config['defs_cdr'] = 0;
                $Number = 0;
            } else {
                $this->game_config['defs_cdr'] = $_POST['Defs_Cdr'];
                $Number = $_POST['Defs_Cdr'];
            }

        }

        // Protection for novices
        if (
            isset($_POST['noobprotection']) 
            && $_POST['noobprotection'] == 'on'
        ) {
            $this->game_config['noobprotection'] = 1;
        } else {
            $this->game_config['noobprotection'] = 0;
        }

        // Protection N. points
        if (
            isset($_POST['noobprotectiontime']) 
            && is_numeric($_POST['noobprotectiontime'])
        ) {
            $this->game_config['noobprotectiontime'] = $_POST['noobprotectiontime'];
        }

        // Protection N. limit points
        if (
            isset($_POST['noobprotectionmulti']) 
            && is_numeric($_POST['noobprotectionmulti'])
        ) {
            $this->game_config['noobprotectionmulti'] = $_POST['noobprotectionmulti'];
        }
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        $this->game_config = $this->Server_Model->readAllConfigs();
        $parse = $this->langs->language;
        $parse['alert'] = '';

        if (
            isset($_POST['opt_save']) 
            && $_POST['opt_save'] == '1'
        ) {
            // Check before save
            $this->runAction();

            // Update all the settings
            $this->Server_Model->updateConfigs($this->game_config);

            $parse['alert'] = Administration::saveMessage(
                'ok', 
                $this->langs->line('se_all_ok_message')
            );
        }

        $parse['game_name'] = $this->game_config['game_name'];
        $parse['game_logo'] = $this->game_config['game_logo'];

        $parse['game_speed']          = $this->game_config['game_speed'] / 2500;
        $parse['fleet_speed']         = $this->game_config['fleet_speed'] / 2500;
        $parse['resource_multiplier'] = $this->game_config['resource_multiplier'];

        $parse['admin_email'] = $this->game_config['admin_email'];
        $parse['forum_url']   = $this->game_config['forum_url'];

        $parse['closed']       = $this->game_config['game_enable'] == 1 ? " checked = 'checked' " : "";
        $parse['close_reason'] = stripslashes(
                                    $this->game_config['close_reason']
                                );

        $parse['date_time_zone']       = $this->timeZonePicker();
        $parse['date_format']          = $this->game_config['date_format'];
        $parse['date_format_extended'] = $this->game_config['date_format_extended'];

        $parse['adm_attack'] = $this->game_config['adm_attack'] == 1 ? " checked = 'checked' " : "";
        $parse['ships']      = $this->percentagePicker(
            $this->game_config['fleet_cdr']
        );

        $parse['defenses']   = $this->percentagePicker(
            $this->game_config['defs_cdr']
        );

        $parse['noobprot']  = $this->game_config['noobprotection'] == 1 ? " checked = 'checked' " : "";
        $parse['noobprot2'] = $this->game_config['noobprotectiontime'];
        $parse['noobprot3'] = $this->game_config['noobprotectionmulti'];

        $parse['language_settings'] = Functions::getLanguages(
            $this->game_config['lang']
        );

        parent::$page->displayAdmin(
            $this->getTemplate()->set('adm/server_view', $parse)
        );
    }

    // --------------------------------------------------------- timeZonePicker

    private function timeZonePicker(): string
    {
        $utc = new DateTimeZone('UTC');
        $dt  = new DateTime('now', $utc);

        $time_zones        = '';
        $current_time_zone = $this->Server_Model->readConfig('date_time_zone');

        // Get the data
        foreach (DateTimeZone::listIdentifiers() as $tz) {
            $current_tz = new DateTimeZone($tz);
            $offset     = $current_tz->getOffset($dt);
            $transition = $current_tz->getTransitions($dt->getTimestamp());

            foreach ($transition as $element => $data) {
                $time_zones_data[$data['offset']][] = $tz;
            }
        }

        // Sort by key
        ksort($time_zones_data);

        // Build the combo
        foreach ($time_zones_data as $offset => $tz) {
            $time_zones .= '<optgroup label="GMT' . $this->formatOffset($offset) . '">';

            foreach ($tz as $key => $zone) {
                $time_zones .= '
                    <option value="' 
                        . $zone . '"' 
                        . ($current_time_zone == $zone ? ' selected' : '') 
                        . ' >' 
                        . $zone . 
                    '</option>';
            }

            $time_zones .= '</optgroup>';
        }

        // Return data
        return $time_zones;
    }

    // ----------------------------------------------------------- formatOffset

    private function formatOffset($offset): string
    {
        $hours     = $offset / 3600;
        $remainder = $offset % 3600;
 
        $sign = $hours > 0 ? '+' : '-';

        $hour    = (int) abs($hours);
        $minutes = (int) abs($remainder / 60);

        if (
            $hour == 0 
            && $minutes == 0
        ) {
            $sign = ' ';
        }

        return $sign . str_pad((string) $hour, 2, '0', STR_PAD_LEFT) 
                     . ':' 
                     . str_pad((string) $minutes, 2, '0');
    }

    // ------------------------------------------------------- percentagePicker

    private function percentagePicker($current_percentage): string
    {
        $options = '';

        for ($i = 0; $i <= 10; $i++) {
            $selected = '';

            if ($i * 10 == $current_percentage) {
                $selected = ' selected = selected ';
            }

            $options .= 
                '<option value="' 
                    . $i * 10 . '"' . $selected . '>' 
                    . $i * 10 . 
                '%</option>';
        }

        return $options;
    }
}