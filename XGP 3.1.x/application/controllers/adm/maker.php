<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\PlanetTypesEnumerator;
use application\core\enumerators\UserRanksEnumerator as UserRanks;

use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib as Functions;
use application\libraries\adm\AdministrationLib as Administration;

class Maker extends Controller
{
    private string $alert = '';
    private array $user;

    /* SUMMARY
     * 
     * constructor
     * buildAllianceUsersCombo
     * buildLevelCombo
     * buildPage
     * buildPlanetCombo
     * buildUsersCombo
     * generatePassword
     * makeAlliance
     * makeMoon
     * makePlanet
     * makeUser
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/maker');
        parent::loadLang(['adm/global', 'adm/maker']);

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

    // ------------------------------------------------ buildAllianceUsersCombo

    private function buildAllianceUsersCombo(): string
    {
        $combo_rows = '';
        $users = $this->Maker_Model->getUsersWithoutAlliance();

        foreach ($users as $users_row) {
            $combo_rows .= 
                '<option value="' . $users_row['user_id'] . '">' 
                    . $users_row['user_name'] . 
                '</option>';
        }

        return $combo_rows;
    }

    // -------------------------------------------------------- buildLevelCombo

    private function buildLevelCombo(): array
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
                'name' => $this->langs->language['user_level'][$rank_id],
            ];
        }

        return [
            'user_levels' => $user_levels,
        ];
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/maker_view',
                array_merge(
                    $this->langs->language,
                    $this->makeUser(),
                    $this->makeAlliace(),
                    $this->makePlanet(),
                    $this->makeMoon(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    // ------------------------------------------------------- buildPlanetCombo

    private function buildPlanetCombo(): string
    {
        $combo_rows = '';
        $planets = $this->Maker_Model->getAllActivePlanets();

        foreach ($planets as $planets_row) {
            if (
                isset($_GET['planet']) 
                && $_GET['planet'] > 0
            ) {
                $combo_rows .= 
                    '<option value="' 
                        . $planets_row['planet_id'] . '"' 
                        . $_GET['planet'] == $planets_row['planet_id'] ? 'selected' : '' 
                        . ' >' 
                        . $planets_row['planet_name'] . 
                        ' [' 
                            . $planets_row['planet_galaxy'] 
                            . ':' 
                            . $planets_row['planet_system'] 
                            . ':' 
                            . $planets_row['planet_planet'] 
                        . ']' . 
                    '</option>';
            } else {
                $combo_rows .= 
                    '<option value="' . $planets_row['planet_id'] . '">' 
                        . $planets_row['planet_name'] 
                        . ' ' 
                        . Format::prettyCoords(
                            (int) $planets_row['planet_galaxy'], 
                            (int) $planets_row['planet_system'], 
                            (int) $planets_row['planet_planet']) . 
                    '</option>';
            }
        }

        return $combo_rows;
    }

    // -------------------------------------------------------- buildUsersCombo

    private function buildUsersCombo(): string
    {
        $combo_rows = '';
        $users = $this->Maker_Model->getAllServerUsers();

        foreach ($users as $users_row) {
            if (
                isset($_GET['user']) 
                && $_GET['user'] > 0
            ) {
                $combo_rows .= 
                    '<option value="' 
                        . $users_row['user_id'] . '"' 
                        . $_GET['user'] == $users_row['user_id'] ? ' selected' : ''
                        . '>' 
                        . $users_row['user_name'] . 
                    '</option>';
            } else {
                $combo_rows .= 
                    '<option value="' . $users_row['user_id'] . '">' 
                        . $users_row['user_name'] . 
                    '</option>';
            }
        }

        return $combo_rows;
    }

    // ------------------------------------------------------- generatePassword 
    private function generatePassword(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count      = strlen($characters);
        
        $new_pass = '';
        $lenght   = 16;

        srand((int) microtime() * 1000000);

        for ($i = 0; $i < $lenght; $i++) {
            $character_boucle = mt_rand(0, $count - 1);
            $new_pass = $new_pass . substr($characters, $character_boucle, 1);
        }

        return $new_pass;
    }

    // ----------------------------------------------------------- makeAlliance

    private function makeAlliance(): array
    {
        $parse['founders_combo'] = $this->buildAllianceUsersCombo();

        if (
            isset($_POST['add_alliance']) 
            && $_POST['add_alliance']
        ) {
            $alliance_name = (string) $_POST['name'];
            $alliance_tag  = (string) $_POST['tag'];

            $alliance_founder = (int) $_POST['founder'];

            $check_alliance = $this->Maker_Model->checkAlliance(
                $alliance_name, 
                $alliance_tag
            );

            if (
                !$check_alliance 
                && !empty($alliance_founder) 
                && $alliance_founder > 0
            ) {
                $this->Maker_Model->createAlliance(
                    $alliance_name, 
                    $alliance_tag, 
                    $alliance_founder, 
                    $this->langs->line('mk_alliance_founder_rank')
                );

                $this->alert = Administration::saveMessage(
                    'ok', 
                    $this->langs->line('mk_alliance_added')
                );
            } else {
                $this->alert = Administration::saveMessage(
                    'warning', 
                    $this->langs->line('mk_alliance_all_fields')
                );
            }
        }

        return $parse;
    }

    // --------------------------------------------------------------- makeMoon

    private function makeMoon(): array
    {
        $parse['planets_combo'] = $this->buildPlanetCombo();

        if (
            isset($_POST['add_moon']) 
            && $_POST['add_moon']
        ) {
            $planet_id  = (int) $_POST['planet'];
            $diameter   = (int) $_POST['planet_diameter'];
            $temp_min   = (int) $_POST['planet_temp_min'];
            $temp_max   = (int) $_POST['planet_temp_max'];
            $max_fields = (int) $_POST['planet_field_max'];

            $moon_name = (string) $_POST['name'];

            $moon_planet = $this->Maker_Model->checkMoon($planet_id);

            if (
                $moon_planet 
                && is_numeric($planet_id)
            ) {

                if (
                    $moon_planet['id_moon'] == NULL 
                    && $moon_planet['planet_type'] == PlanetTypesEnumerator::PLANET 
                    && $moon_planet['planet_destroyed'] == 0
                ) {
                    $galaxy = (int) $moon_planet['planet_galaxy'];
                    $system = (int) $moon_planet['planet_system'];
                    $planet = (int) $moon_planet['planet_planet'];
                    $owner  = (int) $moon_planet['planet_user_id'];

                    $size    = 0;
                    $errors  = 0;
                    $mintemp = 0;
                    $maxtemp = 0;

                    if (
                        !isset($_POST['diameter_check'])
                    ) {
                        if (
                            is_numeric($diameter)
                        ) {
                            $size = $diameter;
                        } else {
                            $errors++;
                            $this->alert = Administration::saveMessage(
                                'warning', 
                                $this->langs->line('mk_moon_only_numbers')
                            );
                        }
                    }

                    if (
                        !isset($_POST['temp_check'])
                    ) {
                        if (
                            is_numeric($temp_max) 
                            && is_numeric($temp_min)
                        ) {
                            $mintemp = $temp_min;
                            $maxtemp = $temp_max;
                        } else {
                            $errors++;
                            $this->alert = Administration::saveMessage(
                                'warning', 
                                $this->langs->line('mk_moon_only_numbers')
                            );
                        }
                    }

                    if (
                        $errors == 0
                    ) {
                        $this->Maker_Model->createNewMoon(
                            $galaxy,
                            $system,
                            $planet,
                            $owner,
                            $moon_name,
                            $size,
                            $max_fields,
                            $mintemp,
                            $maxtemp
                        );

                        $this->alert = Administration::saveMessage(
                            'ok', 
                            $this->langs->line('mk_moon_added')
                        );
                    }
                } else {
                    $this->alert = Administration::saveMessage(
                        'warning', 
                        $this->langs->line('mk_moon_add_errors')
                    );
                }

            } else {
                $this->alert = Administration::saveMessage(
                    'error', 
                    $this->langs->line('mk_moon_planet_doesnt_exist')
                );
            }
        }

        return $parse;
    }

    // ------------------------------------------------------------- makePlanet

    private function makePlanet(): array
    {
        $parse['users_combo'] = $this->buildUsersCombo();

        if (
            isset($_POST['add_planet']) 
            && $_POST['add_planet']
        ) {
            $user_id   = (int) $_POST['user'];
            $galaxy    = (int) $_POST['galaxy'];
            $system    = (int) $_POST['system'];
            $planet    = (int) $_POST['planet'];
            $field_max = (int) $_POST['planet_field_max'];
            $name      = (string) $_POST['name'];

            $i = 0;

            $check_planet = $this->Maker_Model->checkPlanet(
                $galaxy, 
                $system, 
                $planet
            );

            $user_query   = $this->Maker_Model->checkUserById($user_id);

            if (
                $check_planet['count'] == 0 
                && $user_query
            ) {

                if (
                    $galaxy < 1 
                    || $system < 1 
                    || $planet < 1 
                    || !is_numeric($galaxy)
                    || !is_numeric($system)
                    || !is_numeric($planet)
                ) {
                    $error = $this->langs->line('mk_planet_unavailable_coords');
                    $i++;
                }

                if (
                    $galaxy > MAX_GALAXY_IN_WORLD 
                    || $system > MAX_SYSTEM_IN_GALAXY 
                    || $planet > MAX_PLANET_IN_SYSTEM
                ) {
                    $error .= $this->langs->line('mk_planet_wrong_coords');
                    $i++;
                }

                if (
                    $i == 0
                ) {
                    if (
                        $field_max <= 0 
                        && !is_numeric($field_max)
                    ) {
                        $field_max = '163';
                    }

                    if (
                        strlen($name) <= 0
                    ) {
                        $name = $this->langs->line('mk_planet_default_name');
                    }

                    $this->Maker_Model->createNewPlanet(
                        $galaxy, 
                        $system, 
                        $planet, 
                        $user_id, 
                        $field_max, 
                        $name
                    );

                    $this->alert = Administration::saveMessage(
                        'ok', 
                        $this->langs->line('mk_planet_added')
                    );
                } else {
                    $this->alert = Administration::saveMessage(
                        'warning', 
                        $error
                    );
                }
            } else {
                $this->alert = Administration::saveMessage(
                    'warning', 
                    $this->langs->line('mk_planet_unavailable_coords')
                );
            }
        }

        return $parse;
    }

    // --------------------------------------------------------------- makeUser

    private function makeUser(): array
    {
        $parse = $this->buildLevelCombo();

        if (
            isset($_POST['add_user']) 
            && $_POST['add_user']
        ) {
            $name  = (string) $_POST['name'];
            $pass  = (string) $_POST['password'];
            $email = (string) $_POST['email'];

            $galaxy = (int) $_POST['galaxy'];
            $system = (int) $_POST['system'];
            $planet = (int) $_POST['planet'];
            $auth   = (int) $_POST['authlevel'];

            $i     = 0;
            $error = '';

            $check_user   = $this->Maker_Model->checkUserName($name);
            $check_email  = $this->Maker_Model->checkUserEmail($email);
            $check_planet = $this->Maker_Model->checkPlanet(
                $galaxy, 
                $system, 
                $planet
            );

            if (
                !is_numeric($galaxy) 
                && !is_numeric($system) 
                && !is_numeric($planet)
            ) {
                $error = $this->langs->line('mk_user_only_numbers');
                $i++;
            } elseif (
                $galaxy > MAX_GALAXY_IN_WORLD 
                || $system > MAX_SYSTEM_IN_GALAXY 
                || $planet > MAX_PLANET_IN_SYSTEM 
                || $galaxy < 1 
                || $system < 1 
                || $planet < 1
            ) {
                $error = $this->langs->line('mk_user_wrong_coords');
                $i++;
            }

            if (
                !$name
                || !$email 
                || !$galaxy 
                || !$system 
                || !$planet
            ) {
                $error .= $this->langs->line('mk_user_complete_all');
                $i++;
            }

            if (
                !Functions::validEmail(strip_tags($email))
            ) {
                $error .= $this->langs->line('mk_user_invalid_email');
                $i++;
            }

            if (
                $check_user
            ) {
                $error .= $this->langs->line('mk_user_existing_name');
                $i++;
            }

            if (
                $check_email
            ) {
                $error .= $this->langs->line('mk_user_existing_email');
                $i++;
            }

            if (
                $check_planet['count'] != 0
            ) {
                $error .= $this->langs->line('mk_user_existing_planet');
                $i++;
            }

            if (
                isset($_POST['password_check']) 
                && $_POST['password_check']
            ) {
                $pass = Functions::generatePassword();
            } else {

                if (
                    strlen($pass) < 4
                ) {
                    $error .= $this->langs->line('mk_user_invalid_password');
                    $i++;
                }

            }

            if (
                $i == 0
            ) {
                $this->Maker_Model->createNewUser(
                    $name, 
                    $email, 
                    $auth, 
                    $pass, 
                    $galaxy, 
                    $system, 
                    $planet
                );

                $this->alert = Administration::saveMessage(
                    'ok', 
                    strtr($this->langs->line('mk_user_added'), ['%s' => $pass])
                );
            } else {
                $this->alert = Administration::saveMessage(
                    'warning', 
                    '<br/>' . $error
                );
            }
        }

        return $parse;
    }

}