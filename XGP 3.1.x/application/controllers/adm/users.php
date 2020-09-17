<?php

declare(strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\PlanetTypesEnumerator;
use application\core\enumerators\UserRanksEnumerator as UserRanks;

use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib as Functions;
use application\libraries\Statistics_library;
use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\users\Shortcuts;

class Users extends Controller
{
    private $_alert_info;
    private $_alert_type;
    private $_stats;
    private $_user_query;

    private int    $_authlevel;
    private array  $_current_user;
    private string $_edit;
    private int    $_id;
    private int    $_moon;
    private int    $_planet;


    /* SUMMARY
     *
     * constructor
     * buildAllianceCombo
     * buildImageCombo
     * buildPage
     * buildPercentCombo
     * buildPlanetCombo
     * buildProcessQueue
     * buildShortcutsCombo
     * buildUsersCombo
     * buildUsersRolesList
     * deleteData
     * deleteMoon
     * deletePlanet
     * editBuildings
     * editDefenses
     * editMain
     * editShips
     * getData
     * getDataInfo
     * getDataMoons
     * getDataPlanets
     * getDataPremium
     * getDataResearch
     * getDataSettings
     * lastActivity
     * moonsTable
     * planetOrderCombo
     * planetSortCombo
     * planetsTable
     * premiumCombo
     * premiumTable
     * refreshPage
     * researchTable
     * saveBuildings
     * saveData
     * saveDefenses
     * saveInfo
     * savePlanet
     * savePremium
     * saveResearch
     * saveSettings
     * saveShips
     * vacationSet
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/users');
        parent::loadLang(['adm/global', 'adm/users']);

        // Set data
        $this->_current_user = $this->getUserData();
        $this->_stats = new Statistics_library();

        // Check if the user is allowed to access
        if (!Administration::authorization(
            __CLASS__, 
            (int) $this->_current_user['user_authlevel'])
        ) {
            die(Administration::noAccessMessage(
                $this->langs->line('no_permissions')
            ));
        }

        // Build the page
        $this->buildPage();
    }

    // ----------------------------------------------------- buildAllianceCombo

    private function buildAllianceCombo($user_data): string
    {
        $combo_rows = '';
        $alliances = $this->Users_Model->getAllAlliances();

        foreach ($alliances as $alliance_row) {
            if ($user_data['user_ally_id'] == $alliance_row['alliance_id']) {
                $combo_rows .= 
                    '<option value="' 
                        . $alliance_row['alliance_id'] 
                        . '" selected>' 
                        . $alliance_row['alliance_name'] 
                        . ' [' 
                        . $alliance_row['alliance_tag'] 
                        . ']' 
                    . '</option>';
            } else {
                $combo_rows .= 
                    '<option value="' 
                        . $alliance_row['alliance_id'] 
                        . '">' 
                        . $alliance_row['alliance_name'] 
                        . ' [' 
                        . $alliance_row['alliance_tag'] 
                        . ']' 
                    . '</option>';
            }
        }

        return $combo_rows;
    }

    // -------------------------------------------------------- buildImageCombo

    private function buildImageCombo($current_image): string
    {
        $images_dir = opendir(XGP_ROOT . DEFAULT_SKINPATH . 'planets');
        $exceptions = array(
                        '.', 
                        '..', 
                        '.htaccess', 
                        'index.html', 
                        '.DS_Store', 
                        'small'
        );

        $images_options = '';

        while ($image_dir = readdir($images_dir) !== FALSE) {

            if (strpos($image_dir, '.jpg')) {
                $images_options .= "<option ";

                if ($current_image . '.jpg' == $image_dir) {
                    $images_options .= "selected = selected";
                }

                $images_options .= 
                    " value=\"" . preg_replace("/\\.[^.\\s]{3,4}$/", 
                    "", 
                    $image_dir) . "\">" . $image_dir . "</option>";
            }

        }

        return $images_options;
    }

    // -------------------------------------------------------------- buildPage
    private function buildPage(): void
    {
        $parse = $this->langs->language;

        $user = isset($_GET['user']) ? trim($_GET['user']) : NULL;
        $type = isset($_GET['type']) ? trim($_GET['type']) : NULL;

        $this->_edit   = isset($_GET['edit'])   ? trim($_GET['edit'])   : '';
        $this->_planet = isset($_GET['planet']) ? trim($_GET['planet']) : 0;
        $this->_moon   = isset($_GET['moon'])   ? trim($_GET['moon'])   : 0;

        $parse['alert'] = '';

        if ($user != NULL) {
            $checked_user = $this->Users_Model->checkUser($user);

            if (!$checked_user) {
                $parse['alert'] = Administration::saveMessage(
                    'error', $this->langs->line('us_nothing_found')
                );

                $user = '';
            } else {
                $this->_id        = $checked_user['user_id'];
                $this->_authlevel = $checked_user['user_authlevel'];

                // Initial data
                $this->_user_query = $this->Users_Model->getUserDataById(
                    $this->_id
                );

                // Save the data
                if (
                    isset($_POST['send_data']) 
                    && $_POST['send_data']
                ) {
                    $this->saveData($type);
                }

                // Get refreshed data
                $this->_user_query = $this->Users_Model->getUserDataById(
                    $this->_id
                );
            }
        }

        // physical delete
        if (
            isset($_GET['mode']) 
            && $_GET['mode'] == 'delete' 
            && $this->_user_query['user_authlevel'] != 3
        ) {
            parent::$users->deleteUser($this->_user_query['user_id']);

            $parse['alert'] = Administration::saveMessage(
                'ok', 
                $this->langs->line('us_user_deleted')
            );
        }

        $parse['type'] = ($type != '') ? $type : 'info';
        $parse['user'] = ($user != '') ? $user : '';
        $parse['status'] = ($user != '') ? '' : ' disabled';
        $parse['status_box'] = ($user != '' && $this->_id != $this->_current_user['user_id']) ? '' : ' disabled';
        $parse['tag'] = ($user != '') ? 'a' : 'button';
        $parse['user_rank'] = $this->langs->language['user_level'][$this->_authlevel];
        $parse['content'] = ($user != '' && $type != '') ? $this->getData($type) : '';

        parent::$page->displayAdmin(
            $this->getTemplate()->set('adm/users_view', $parse)
        );
    }

    // ------------------------------------------------------ buildPercentCombo

    private function buildPercentCombo($current_value): string
    {
        $percent = '';
        $percent_values = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

        foreach ($percent_values as $id => $number) {
            $percent .= 
                "<option value=\"{$id}\"  " 
                    . $current_value == $number ? ' selected' : '' 
                    . ">" 
                    . ($number * 10) 
                . "</option>";
        }

        return $percent;
    }

    // ------------------------------------------------------- buildPlanetCombo

    private function buildPlanetCombo($user_data, $id_field): string
    {
        $combo_rows = '';
        $planets = $this->Users_Model->getAllPlanetsByUserId($this->_id);

        foreach ($planets as $planets_row) {
            if ($user_data[$id_field] == $planets_row['planet_id']) {
                $combo_rows .= 
                    '<option value="' 
                        . $planets_row['planet_id'] 
                        . '" selected>' 
                        . $planets_row['planet_name'] 
                            . ' ['
                            . $planets_row['planet_galaxy'] . ':' 
                            . $planets_row['planet_system'] . ':' 
                            . $planets_row['planet_planet'] 
                            . ']' 
                        . '</option>';
            } else {
                $combo_rows .= 
                    '<option value="' 
                        . $planets_row['planet_id'] 
                        . '">' 
                        . $planets_row['planet_name'] 
                        . ' [' 
                        . $planets_row['planet_galaxy'] . ':' 
                        . $planets_row['planet_system'] . ':' 
                        . $planets_row['planet_planet'] 
                        . ']' 
                    . '</option>';
            }
        }

        return $combo_rows;
    }

    // ------------------------------------------------------ buildProcessQueue

    private function buildProcessQueue($current_queue)
    {
        if (!empty($current_queue)) {
            $queue_list    = '';
            $current_queue = explode(';', $current_queue);

            foreach ($current_queue as $key => $queues) {
                $queue = explode(',', $queues);

                if ($queue[3] <= time()) {
                    $ready = 'OK';
                } else {
                    $ready = date('i:s', $queue[3] - time());
                }

                $queue_list .= 
                    "<option value=\"{$queue[0]}\">" 
                        . $this->langs->language['tech'][$queue[0]] 
                        . " (" . $queue[1] 
                        . "^) (" . date("i:s", $queue[2]) 
                        . ") (" . $ready 
                        . ") [" . $queue[4] 
                        . "] 
                    </option>";
            }

            return $queue_list;
        }
    }

    // ---------------------------------------------------- buildShortcutsCombo

    private function buildShortcutsCombo($shortcuts): string
    {
        if ($shortcuts) {
            $user_shortcuts = new Shortcuts($shortcuts);

            foreach ($user_shortcuts->getAllAsArray() as $key => $value) {
                $shortcut['description'] = 
                    $value['name'] 
                    . ' ' . 
                    Format::prettyCoords($value['g'], $value['s'], $value['p']) 
                    . ' ';

                switch ($value['pt']) {
                    case 1:
                        $shortcut['description'] .= 
                            $this->langs->line('us_planet_shortcut');
                        break;
                    case 2:
                        $shortcut['description'] .= 
                            $this->langs->line('us_debris_shortcut');
                        // no break
                    case 3:
                        return $shortcut['description'] .= 
                            $this->langs->line('us_moon_shortcut');
                    default:
                        $shortcut['description'] .= '';
                        break;
                }

                $shortcut['select']   = 'shortcuts';
                $shortcut['selected'] = '';

                $shortcut['value'] = 
                    $value['g'] . ";" . 
                    $value['s'] . ";" . 
                    $value['p'] . ";" . 
                    $value['pt'];

                $shortcut['title'] = $shortcut['description'];
                $shortcuts .= 
                    '<option value="' 
                        . $shortcut['value'] . '"' 
                        . $shortcut['selected'] 
                        . '>' 
                        . $shortcut['title'] 
                    . '</option>';
            }

            return $shortcuts;
        } else {
            return '<option value="">-</option>';
        }
    }

    // -------------------------------------------------------- buildUsersCombo

    private function buildUsersCombo($user_id): string
    {
        $combo_rows = '';
        $users = $this->Users_Model->getAllUsers();

        foreach ($users as $users_row) {
            $combo_rows .= 
                '<option value="' 
                    . $users_row['user_id'] . '"' 
                    . ($users_row['user_id'] == $user_id ? ' selected' : '') 
                    . '>' 
                    . $users_row['user_name'] 
                . '</option>';
        }

        return $combo_rows;
    }

    // ---------------------------------------------------- buildUsersRolesList

    private function buildUsersRolesList(): array
    {
        $roles_list = [];
        $roles = [
            UserRanks::PLAYER,
            UserRanks::GO,
            UserRanks::SGO,
            UserRanks::ADMIN,
        ];

        foreach ($roles as $role) {
            $roles_list[] = [
                'role_id'   => $role,
                'role_sel'  => ($role == $this->_user_query['user_authlevel'] ? 'selected' : ''),
                'role_name' => $this->langs->language['user_level'][$role],
            ];
        }

        return $roles_list;
    }

    // ------------------------------------------------------------- deleteData

    private function deleteData($type): void
    {
        switch ($type) {
            case 'planet':
                //$this->deletePlanet();
                break;

            case 'moon':
                //$this->deleteMoon();
                break;
        }
    }

    // ------------------------------------------------------------- deleteMoon

    private function deleteMoon($id_moon = 0): void
    {
        if ($id_moon == 0) {
            $id_moon = $this->_moon;
        }

        $this->Users_Model->deleteMoonById($id_moon);
    }

    // ----------------------------------------------------------- deletePlanet

    private function deletePlanet($id_planet = 0): void
    {
        if ($id_planet == 0) {
            $id_planet = $this->_planet;
        }

        $this->deleteMoon();
        $this->Users_Model->deletePlanetById($id_planet);
    }

    // ---------------------------------------------------------- editBuildings

    private function editBuildings($planets_data, $type = 1): array
    {
        $exclude_buildings = array(
                                'building_mondbasis', 
                                'building_phalanx', 
                                'building_jump_gate'
        );

        if ($type == 3) {
            $exclude_buildings = array(
                                    'building_metal_mine', 
                                    'building_crystal_mine', 
                                    'building_deuterium_sintetizer', 
                                    'building_solar_plant', 
                                    'building_fusion_reactor', 
                                    'building_nano_factory', 
                                    'building_laboratory', 
                                    'building_terraformer', 
                                    'building_ally_deposit', 
                                    'building_missile_silo'
            );
        }

        $prepare_table = [];
        $flag = 1;

        foreach ($planets_data as $building => $level) {

            if (
                strpos($building, 'building_') !== FALSE 
                && !in_array($building, $exclude_buildings)
            ) {
                // Skip not required fields
                if ($flag <= 2) {
                    $flag++;
                } else {
                    $parse['building'] = $this->langs->line(
                                            'us_user_' . $building
                    );

                    $parse['field']  = $building;
                    $parse['level']  = $level;
                    $prepare_table[] = $parse;
                }
            }
        }

        return $prepare_table;
    }

    // ----------------------------------------------------------- editDefenses

    private function editDefenses($planets_data, $type = 1): array
    {
        $exclude_buildings = array('');

        if ($type == 3) {
            $exclude_buildings = array(
                                    'defense_anti-ballistic_missile', 
                                    'defense_interplanetary_missile'
            );
        }

        $prepare_table = [];
        $flag = 1;

        foreach ($planets_data as $defense => $amount) {

            if (
                strpos($defense, 'defense_') !== FALSE 
                && !in_array($defense, $exclude_buildings)
            ) {
                // Skip not required fields
                if ($flag <= 2) {
                    $flag++;
                } else {
                    $parse['defense'] = $this->langs->line(
                                            'us_user_' . $defense
                    );

                    $parse['field']  = $defense;
                    $parse['amount'] = $amount;
                    $prepare_table[] = $parse;
                }

            }

        }

        return $prepare_table;
    }

    // --------------------------------------------------------------- editMain

    private function editMain($planets_data)
    {
        $parse  = $this->langs->language;
        $parse += $planets_data;

        $parse['planet_user_id'] = $this->buildUsersCombo(
                                        $parse['planet_user_id']
        );

        $parse['planet_last_update'] = date(
                                        Functions::readConfig('date_format_extended'), 
                                        $parse['planet_last_update']
        );

        $parse['type1'] = $parse['planet_type'] == PlanetTypesEnumerator::PLANET ? ' selected' : '';
        $parse['type2'] = $parse['planet_type'] == PlanetTypesEnumerator::MOON   ? ' selected' : '';

        $parse['dest1'] = $parse['planet_destroyed'] >  0 ? ' selected' : '';
        $parse['dest2'] = $parse['planet_destroyed'] <= 0 ? ' selected' : '';

        $parse['planet_destroyed']  = $parse['planet_destroyed']  > 0 ? date(Functions::readConfig('date_format_extended'), $parse['planet_destroyed'])  : '-';
        $parse['planet_b_building'] = $parse['planet_b_building'] > 0 ? date(Functions::readConfig('date_format_extended'), $parse['planet_b_building']) : '-';

        $parse['planet_b_building_id'] = $this->buildProcessQueue(
                                            $parse['planet_b_building_id']
        );

        $parse['planet_b_tech']   = $parse['planet_b_tech']   > 0 ? date(Functions::readConfig('date_format_extended'), $parse['planet_b_tech'])   : '-';
        $parse['planet_b_hangar'] = $parse['planet_b_hangar'] > 0 ? date(Functions::readConfig('date_format_extended'), $parse['planet_b_hangar']) : '-';

        $parse['planet_image'] = $this->buildImageCombo(
                                    $parse['planet_image']
        );

        $parse['planet_building_metal_mine_percent'] = 
            $this->buildPercentCombo(
                $parse['planet_building_metal_mine_percent']
            );

        $parse['planet_building_crystal_mine_percent'] = 
            $this->buildPercentCombo(
                $parse['planet_building_crystal_mine_percent']
            );

        $parse['planet_building_deuterium_sintetizer_percent'] = 
            $this->buildPercentCombo(
                $parse['planet_building_deuterium_sintetizer_percent']
            );

        $parse['planet_building_solar_plant_percent'] = 
            $this->buildPercentCombo(
                $parse['planet_building_solar_plant_percent']
            );

        $parse['planet_building_fusion_reactor_percent'] = 
            $this->buildPercentCombo(
                $parse['planet_building_fusion_reactor_percent']
            );

        $parse['planet_ship_solar_satellite_percent'] = 
            $this->buildPercentCombo(
                $parse['planet_ship_solar_satellite_percent']
            );

        $parse['planet_last_jump_time']       = $parse['planet_last_jump_time']       > 0 ? date(Functions::readConfig('date_format_extended'), $parse['planet_last_jump_time'])       : '-';
        $parse['planet_invisible_start_time'] = $parse['planet_invisible_start_time'] > 0 ? date(Functions::readConfig('date_format_extended'), $parse['planet_invisible_start_time']) : '-';

        return $parse;
    }

    // -------------------------------------------------------------- editShips
    private function editShips($planets_data): array
    {
        $prepare_table = [];
        $flag = 1;

        foreach ($planets_data as $ship => $amount) {

            if (strpos($ship, 'ship_') !== FALSE) {
                // Skip not required fields
                if ($flag <= 2) {
                    $flag++;
                } else {
                    $parse['ship']   = $this->langs->line('us_user_' . $ship);
                    $parse['field']  = $ship;
                    $parse['amount'] = $amount;

                    $prepare_table[] = $parse;
                }
            }

        }

        return $prepare_table;
    }

    // ---------------------------------------------------------------- getData

    private function getData($type)
    {
        switch ($type) {
            case 'info':
                break;
            case '':
                // no break
            case 'settings':
                $this->getDataSettings();
            case 'research':
                $this->getDataResearch();
            case 'premium':
                $this->getDataPremium();
            case 'planets':
                $this->getDataPlanets();
            case 'moons':
                return $this->getDataMoons();
            default:
                $this->getDataInfo();
                break;
        }
    }

    // ------------------------------------------------------------ getDataInfo

    private function getDataInfo(): string
    {
        $parse  = $this->langs->language;
        $parse += (array) $this->_user_query;

        $parse['information'] = str_replace(
                                    '%s', 
                                    $this->_user_query['user_name'], 
                                    $this->langs->line('us_user_information')
        );

        $parse['main_planet'] = $this->buildPlanetCombo(
                                    $this->_user_query, 
                                    'user_home_planet_id'
        );

        $parse['current_planet'] = $this->buildPlanetCombo(
                                        $this->_user_query, 
                                        'user_current_planet'
        );

        $parse['alliances'] = $this->buildAllianceCombo(
                                    $this->_user_query
        );

        $parse['user_register_time'] = $this->_user_query['user_register_time'] == 0 ? '-' : date(Functions::readConfig('date_format_extended'), $this->_user_query['user_register_time']);

        $parse['user_onlinetime'] = $this->lastActivity(
                                        $this->_user_query['user_onlinetime']
        );

        $parse['user_roles'] = $this->buildUsersRolesList();

        $parse['user_banned']  = $this->_user_query['user_banned'] <= 0 ? '<p class="text-error">' . $this->langs->line('ge_no') : '<p class="text-success">' . $this->langs->line('ge_yes');
        $parse['user_banned'] .= $this->_user_query['user_banned'] >  0 ? $this->langs->line('us_user_information_banned_until') . date(Functions::readConfig('date_format'), $this->_user_query['user_banned']) . '</p>' : '</p>';

        $parse['user_fleet_shortcuts'] = $this->buildShortcutsCombo(
                                            $this->_user_query['user_fleet_shortcuts']
        );

        $parse['alert_info'] = 
            $this->_alert_type != NULL ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set(
            'adm/users_information_view', 
            $parse
        );
    }

    // ----------------------------------------------------------- getDataMoons

    private function getDataMoons()
    {
        $moons_query = $this->Users_Model->getAllMoonsData(
            $this->_id, 
            $this->_moon, 
            $this->_edit
        );

        $parse = $this->langs->language;

        $parse['moons'] = str_replace(
                            '%s', 
                            $this->_user_query['user_name'], 
                            $this->langs->line('us_user_moons')
        );

        // Choose the action
        switch (TRUE) {
            // Moon
            case ($this->_edit == 'moon' && $moons_query):
                $parse += $this->editMain($moons_query[0]);
                $view   = 'adm/users_moons_main_view';
            // Buildings
            case ($this->_edit == 'buildings' && $moons_query):
                $parse['buildings_list'] = $this->editBuildings($moons_query[0], 3);
                $view = 'adm/users_planets_buildings_view';
            // Ships
            case ($this->_edit == 'ships' && $moons_query):
                $parse['ships_list'] = $this->editShips($moons_query[0]);
                $view = 'adm/users_planets_ships_view';
            // Defense
            case ($this->_edit == 'defenses' && $moons_query):
                $parse['defenses_list'] = $this->editDefenses($moons_query[0], 3);
                $view = 'adm/users_planets_defenses_view';
            // Delete Moon
            case ($this->_edit == 'delete'):
                $this->Users_Model->softDeleteMoonById($this->_moon);
                $this->refreshPage();
            case '':
                return;
            default:
                $parse['moons_list'] = $this->moonsTable($moons_query);
                $view = 'adm/users_moons_view';
                break;
        }

        $parse['alert_info'] = $this->_alert_type != NULL ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set($view, $parse);
    }

    // --------------------------------------------------------- getDataPlanets

    private function getDataPlanets()
    {
        $planets_query = $this->Users_Model->getAllPlanetsData(
                                                $this->_id, 
                                                $this->_planet, 
                                                $this->_edit
        );

        $parse = $this->langs->language;

        $parse['planets'] = str_replace(
                                '%s', 
                                $this->_user_query['user_name'], 
                                $this->langs->line('us_user_planets')
        );

        // Choose the action
        switch (TRUE) {
            // Planets
            case $this->_edit == 'planet' && $planets_query:
                $parse += $this->editMain($planets_query[0]);
                $view   = 'adm/users_planets_main_view';
                break;
            // Buildings
            case $this->_edit == 'buildings' && $planets_query:
                $parse['buildings_list'] = $this->editBuildings($planets_query[0], 1);
                $view = 'adm/users_planets_buildings_view';
                // no break
            // Ships
            case $this->_edit == 'ships' && $planets_query:
                $parse['ships_list'] = $this->editShips($planets_query[0]);
                $view = 'adm/users_planets_ships_view';
            // Defenses
            case $this->_edit == 'defenses' && $planets_query:
                $parse['defenses_list'] = $this->editDefenses($planets_query[0], 1);
                $view = 'adm/users_planets_defenses_view';
            // Delete Planet
            case ($this->_edit == 'delete'):
                $this->Users_Model->softDeletePlanetById($this->_planet);
                $this->refreshPage();
            case '':
                return;
            default:
                $parse['planets_list'] = $this->planetsTable($planets_query);
                $view = 'adm/users_planets_view';
                break;
        }

        $parse['alert_info'] = $this->_alert_type != NULL ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set($view, $parse);
    }

    // --------------------------------------------------------- getDataPremium

    private function getDataPremium(): string
    {
        $parse = $this->langs->language;

        $parse['premium'] = str_replace(
                                '%s', 
                                $this->_user_query['user_name'], 
                                $this->langs->line('us_user_premium')
        );

        $parse['premium_dark_matter'] = 
            $this->_user_query['premium_dark_matter'];

        $parse['premium_list'] = $this->premiumTable();

        $parse['alert_info'] = $this->_alert_type != NULL ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set(
            'adm/users_premium_view', 
            $parse
        );
    }

    // -------------------------------------------------------- getDataResearch

    private function getDataResearch(): string
    {
        $parse  = $this->langs->language;
        $parse += (array) $this->_user_query;

        $parse['research'] = 
            str_replace(
                array('%s', '%d'), 
                array($this->_user_query['user_name'], $this->_id), 
                $this->langs->line('us_user_research')
            );

        $parse['technologies_list'] = $this->researchTable();

        $parse['alert_info'] = 
            $this->_alert_type != NULL ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set(
            'adm/users_research_view', 
            $parse
        );
    }

    // -------------------------------------------------------- getDataSettings

    private function getDataSettings(): string
    {
        $parse = $this->langs->language;

        $parse['settings'] = str_replace(
                                '%s', 
                                $this->_user_query['user_name'], 
                                $this->langs->line('us_user_settings')
        );

        $parse['preference_planet_sort']          = $this->planetSortCombo();
        $parse['preference_planet_sort_sequence'] = $this->planetOrderCombo();

        $parse['preference_spy_probes'] = 
            $this->_user_query['preference_spy_probes'];

        $parse['preference_vacations_status'] = $this->_user_query['preference_vacation_mode'] > 0 ? ' checked="checked" ' : '';
        $parse['preference_vacation_mode']    = $this->_user_query['preference_vacation_mode'] > 0 ? $this->vacationSet()  : '';

        $parse['preference_delete_mode'] = 
            $this->_user_query['preference_delete_mode'] ? ' checked="checked" ' : '';

        $parse['alert_info'] = 
            $this->_alert_type != NULL ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set(
            'adm/users_settings_view', 
            $parse
        );
    }

    // ----------------------------------------------------------- lastActivity

    private function lastActivity(int $time): string
    {
        if ($time + 60 * 10 >= time()) {
            return '<p class="text-success">' 
                        . $this->langs->line('us_online') . 
                    '</p>';
        }

        if ($time + 60 * 15 >= time()) {
            return '<p class="text-warning">' 
                        . $this->langs->line('us_minutes') .
                    '</p>';
        }

        return '<p class="text-danger">' 
                    . $this->langs->line('us_offline') . 
                '</p>';
    }

    // ------------------------------------------------------------- moonsTable

    private function moonsTable($moons_data): array
    {
        $parse = $this->langs->language;
        $parse['image_path'] = DEFAULT_SKINPATH . 'planets/small/s_';
        $parse['user'] = $this->_user_query['user_name'];
        $prepare_table = [];

        foreach ($moons_data as $moons) {
            $parse['moon_id']   = $moons['planet_id'];
            $parse['moon_name'] = str_replace(
                                    '%s', 
                                    $moons['planet_name'], 
                                    $this->langs->line('us_user_moon_title')
            );

            $parse['moon_image']  = $moons['planet_image'];
            $parse['moon_status'] = '';

            if ($moons['planet_destroyed'] != 0) {
                $parse['moon_status'] = 
                    '<strong>
                        <a title="' . 
                            $this->langs->line('us_user_planets_destroyed') . 
                        '"> (' . 
                            $this->langs->line('us_user_planets_destroyed_short') . 
                        ')</a>
                    </strong>';

                $parse['moon_image_style'] = 'class="greyout"';
            }

            $prepare_table[] = $parse;
        }

        return $prepare_table;
    }

    // ------------------------------------------------------- planetOrderCombo

    private function planetOrderCombo(): string
    {
        $order = '';
        $order_types = array(
            0 => $this->langs->line(
                    'us_user_preference_planet_sort_sequence_op1'
            ),

            1 => $this->langs->line(
                    'us_user_preference_planet_sort_sequence_op2'
            ),
        );

        foreach ($order_types as $id => $name) {
            $order .= 
                "<option value =\"{$id}\"" 
                    . $this->_user_query['preference_planet_sort_sequence'] == $id ? " selected" : ""
                    . ">{$name}
                </option>";
        }

        return $order;
    }

    // -------------------------------------------------------- planetSortCombo

    private function planetSortCombo(): string
    {
        $sort = '';
        $sort_types = array(
            0 => $this->langs->line('us_user_preference_planet_sort_op1'),
            1 => $this->langs->line('us_user_preference_planet_sort_op2'),
            2 => $this->langs->line('us_user_preference_planet_sort_op3'),
            3 => $this->langs->line('us_user_preference_planet_sort_op4'),
            4 => $this->langs->line('us_user_preference_planet_sort_op5'),
        );

        foreach ($sort_types as $id => $name) {
            $sort .= 
                "<option value =\"{$id}\"" 
                    . $this->_user_query['preference_planet_sort'] == $id ? " selected" : "" 
                    . ">{$name}
                </option>";
        }

        return $sort;
    }

    // ----------------------------------------------------------- planetsTable

    private function planetsTable($planets_data): array
    {
        $parse = $this->langs->language;
        $parse['image_path'] = DEFAULT_SKINPATH . 'planets/small/s_';
        $parse['user'] = $this->_user_query['user_name'];
        $prepare_table = [];

        foreach ($planets_data as $planets) {
            $parse['planet_id']     = $planets['planet_id'];
            $parse['planet_name']   = $planets['planet_name'];
            $parse['planet_image']  = $planets['planet_image'];
            $parse['planet_status'] = '';
            $parse['planet_image_style'] = '';
            $style = '';

            if ($planets['planet_destroyed'] != 0) {
                $parse['planet_status'] = 
                    '<strong>
                        <a title="' 
                            . $this->langs->line('us_user_planets_destroyed') . 
                        '"> (' 
                            . $this->langs->line('us_user_planets_destroyed_short') . 
                        ')</a>
                    </strong>';

                $parse['planet_image_style'] = 'class="greyout"';
            }

            $parse['moon_id']     = '';
            $parse['moon_name']   = '';
            $parse['moon_image']  = '';
            $parse['moon_status'] = '';

            if (isset($planets['moon_id'])) {
                $parse['moon_id']   = $planets['moon_id'];
                $parse['moon_name'] = str_replace(
                                        '%s', 
                                        $planets['moon_name'], 
                                        $this->langs->line('us_user_moon_title')
                );

                if ($planets['moon_destroyed'] != 0) {
                    $parse['moon_status'] = 
                        '<strong>
                            <a title="' 
                                . $this->langs->line('us_user_planets_destroyed') . 
                            '"> (' 
                                . $this->langs->line('us_user_planets_destroyed_short') . 
                            ')</a>
                        </strong>';

                    $style = 'class="greyout"';
                }

                $parse['moon_image'] = 
                    "<img src=\"{$parse['image_path']}{$planets['moon_image']}.jpg\" 
                          alt=\"{$planets['moon_image']}.jpg\" 
                          title=\"{$planets['moon_image']}.jpg\" 
                          border=\"0\" " . $style . ">";
            }

            $prepare_table[] = $parse;
        }

        return $prepare_table;
    }

    // ----------------------------------------------------------- premiumCombo

    private function premiumCombo($expire_date): string
    {
        $premium = '';
        $premium_types = array(
            0 => '-',
            1 => $this->langs->line('us_user_premium_deactivate'),
            2 => $this->langs->line('us_user_premium_activate_one_week'),
            3 => $this->langs->line('us_user_premium_activate_three_month'),
        );

        foreach ($premium_types as $id => $name) {
            $premium .= "<option value=\"{$id}\">{$name}</option>";
        }

        return $premium;
    }

    // ----------------------------------------------------------- premiumTable

    private function premiumTable(): array
    {
        $prepare_table = [];
        $flag = 1;

        foreach ($this->_user_query as $officier => $expire) {

            if (strpos($officier, 'premium_') !== false) {

                // Skip not required fields
                if ($flag <= 2) {
                    $flag++;
                } else {

                    if (NULL === $this->langs->line('us_user_' . $officier)) {
                        continue;
                    }

                    $prepare_table[] = [
                        'premium' => $this->langs->line(
                                        'us_user_' . $officier
                        ),

                        'status_style' => 
                            $expire == 0 ? 'text-danger' : 'text-success',

                        'field' => $officier,

                        'combo' => $this->premiumCombo($expire),

                        'status' => $expire == 0 ? $this->langs->line('us_user_premium_inactive') : ($this->langs->line('us_user_premium_active_until') . date(Functions::readConfig('date_format'), $expire)),
                    ];
                }

            }

        }

        return $prepare_table;
    }

    // ------------------------------------------------------------ refreshPage

    private function refreshPage(): void
    {
        // Set params
        $page = isset($_GET['page']) ? '?page=' . $_GET['page'] : '';
        $type = isset($_GET['type']) ? '&type=' . $_GET['type'] : '';
        $user = isset($_GET['user']) ? '&user=' . $_GET['user'] : '';

        // Redirection
        Functions::redirect("admin.php{$page}{$type}{$user}");
    }

    // ---------------------------------------------------------- researchTable

    private function researchTable(): array
    {
        $prepare_table = [];
        $flag = 1;

        foreach ($this->_user_query as $tech => $level) {

            if (strpos($tech, 'research_') !== FALSE) {

                // Skip not required fields
                if ($flag <= 3) {
                    $flag++;
                } else {
                    $prepare_table[] = [
                        'technology' => $this->langs->line('us_user_' . $tech),
                        'field'      => $tech,
                        'level'      => $level,
                    ];
                }

            }

        }

        return $prepare_table;
    }

    // ---------------------------------------------------------- saveBuildings

    private function saveBuildings($type = 1): void
    {
        $id_get = $this->_planet;

        if ($type == 3) {
            $id_get = $this->_moon;
        }

        $this->Users_Model->saveBuildings($_POST, $id_get);

        // Points rebuild
        $this->_stats->rebuildPoints($this->_id, $id_get, 'buildings');

        // Alert
        $this->_alert_info = $this->langs->line('us_all_ok_message');
        $this->_alert_type = 'ok';
    }

    // --------------------------------------------------------------- saveData

    private function saveData($type): void
    {
        switch ($type) {
            case 'info':
                break;
            case '':
                // no break
            case 'settings':
                $this->saveSettings();

            case 'research':
                $this->saveResearch();

            case 'premium':
                $this->savePremium();

            case 'planets':
                switch ($this->_edit) {
                    case '':
                        // no break
                    case 'planet':
                    case 'buildings':
                        $this->saveBuildings(1);
                    case 'ships':
                        $this->saveShips(1);
                    case 'defenses':
                        return $this->saveDefenses(1);
                    default:
                        $this->savePlanet(1);
                        break;
                }

            case 'moons':
                switch ($this->_edit) {
                    case '':
                        break;
                    case 'moon':
                        // no break
                    case 'buildings':
                        $this->saveBuildings(3);
                    case 'ships':
                        $this->saveShips(3);
                    case 'defenses':
                        return $this->saveDefenses(3);
                    default:
                        $this->savePlanet(3);
                        break;
                }

            default:
                $this->saveInfo();
                break;
        }
    }

    // ----------------------------------------------------------- saveDefenses

    private function saveDefenses($type = 1): void
    {
        $id_get = $this->_planet;

        if ($type == 3) {
            $id_get = $this->_moon;
        }

        $this->Users_Model->saveDefenses($_POST, $id_get);

        // Points rebuild
        $this->_stats->rebuildPoints($this->_id, $id_get, 'defenses');

        // Alert
        $this->_alert_info = $this->langs->line('us_all_ok_message');
        $this->_alert_type = 'ok';
    }

    // --------------------------------------------------------------- saveInfo

    private function saveInfo(): void
    {
        $username = $_POST['username'];
        $username = isset($username) ? $username : '';

        $password = $_POST['password'];
        $password = isset($password) ? $password : '';

        $email = $_POST['email'];
        $email = isset($email) ? $email : '';

        $authlevel = $_POST['authlevel'];
        $authlevel = isset($authlevel) ? $authlevel : -1;

        $id_planet = $_POST['id_planet'];
        $id_planet = isset($id_planet) ? $id_planet : 0;

        $cur_planet = $_POST['current_planet'];
        $cur_planet = isset($cur_planet) ? $cur_planet : 0;

        $ally_id = $_POST['ally_id'];
        $ally_id = isset($ally_id) ? $ally_id : 0;

        $authlevel  = (int) $authlevel;
        $id_planet  = (int) $id_planet;
        $cur_planet = (int) $cur_planet;
        $ally_id    = (int) $ally_id;

        $errors = '';

        if (
            $username == NULL 
            || $this->Users_Model->checkUsername($username, $this->_id)
        ) {
            $errors .= $this->langs->line('us_error_username') . '<br />';
        }

        if ($password != NULL) {
            $password = "'" . Functions::hash($password) . "'";
        } else {
            $password = "`user_password`";
        }

        if (
            $email == NULL 
            || $this->Users_Model->checkEmail($email, $this->_id)
        ) {
            $errors .= $this->langs->line('us_error_email') . '<br />';
        }

        if (
            $authlevel < 0 
            || $authlevel > 3
        ) {
            $errors .= $this->langs->line('us_error_authlevel') . '<br />';
        }

        if ($id_planet <= 0) {
            $errors .= $this->langs->line('us_error_idplanet') . '<br />';
        }

        if ($cur_planet <= 0) {
            $errors .= $this->langs->line('us_error_current_planet') . '<br />';
        }

        if ($ally_id < 0) {
            $errors .= $this->langs->line('us_error_ally_id') . '<br />';
        }

        if ($errors != NULL) {
            $this->_alert_info = $errors;
            $this->_alert_type = 'error';
        } else {
            $this->Users_Model->saveUserData([
                'username'   => $username,
                'password'   => $password,
                'email'      => $email,
                'authlevel'  => $authlevel,
                'id_planet'  => $id_planet,
                'cur_planet' => $cur_planet,
                'ally_id'    => $ally_id,
                'id'         => $this->_id,
            ]);

            if ($this->_current_user['user_id'] != $this->_id) {
                $this->Users_Model->deleteSessionByUserId($this->_id);
            }

            $this->_alert_info = $this->langs->line('us_all_ok_message');
            $this->_alert_type = 'ok';
        }
    }

    // ------------------------------------------------------------- savePlanet

    private function savePlanet($type = 1): void
    {
        $id_get = $this->_planet;

        if ($type == 3) {
            $id_get = $this->_moon;
        }

        if ((int) $id_get <= 0) {
            return;
        }

        $this->Users_Model->savePlanet($_POST, $id_get);

        // Alert
        $this->_alert_info = $this->langs->line('us_all_ok_message');
        $this->_alert_type = 'ok';
    }

    // ------------------------------------------------------------ savePremium

    private function savePremium(): void
    {
        $this->Users_Model->savePremium($_POST, $this->_id, $this->_user_query);

        // alert
        $this->_alert_info = $this->langs->line('us_all_ok_message');
        $this->_alert_type = 'ok';
    }

    // ----------------------------------------------------------- saveResearch

    private function saveResearch(): void
    {
        $this->Users_Model->saveTechnologies($_POST, $this->_id);

        // Points rebuild
        $this->_stats->rebuildPoints($this->_id, 0, 'research');

        // Alert
        $this->_alert_info = $this->langs->line('us_all_ok_message');
        $this->_alert_type = 'ok';
    }

    // ----------------------------------------------------------- saveSettings

    private function saveSettings(): void
    {
        $this->Users_Model->saveUserPreferences(
            $_POST, 
            $this->_id, 
            $this->_user_query
        );

        $this->_alert_info = $this->langs->line('us_all_ok_message');
        $this->_alert_type = 'ok';
    }

    // -------------------------------------------------------------- saveShips

    private function saveShips($type = 1): void
    {
        $id_get = $this->_planet;

        if ($type == 3) {
            $id_get = $this->_moon;
        }

        $this->Users_Model->saveShips($_POST, $id_get);

        // Points rebuild
        $this->_stats->rebuildPoints($this->_id, $id_get, 'ships');

        // Alert
        $this->_alert_info = $this->langs->line('us_all_ok_message');
        $this->_alert_type = 'ok';
    }

    // ------------------------------------------------------------ vacationSet

    private function vacationSet(): string
    {
        return $this->langs->line('us_user_preference_vacations_until') . 
                date(Functions::readConfig('date_format_extended'), 
                     $this->_user_query['preference_vacation_mode']
                );
    }

}