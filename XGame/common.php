<?php

declare(strict_types = 1);

session_start();

ini_set('display_errors', TRUE);
ini_set('date.timezone', 'Europe/Paris');

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('VERSION', '0.0.1');

$req  = substr($_SERVER['REQUEST_URI'], 0);
$frag = explode('/', $req);
$fnum = count($frag);

unset($frag[intval(0)]);
unset($frag[intval($fnum - 1)]);

if (
    filesize(ROOT_PATH . 'config.php' === 0)
    && !defined('IN_INSTALL')
) {
    header('Location: install/');
    die();
}

if (
    defined('IN_ADMIN')
) {
    // Delete /admin/ path
    if (
        $_SERVER['HTTP_HOST'] == '127.0.0.1'
        || $_SERVER['HTTP_HOST'] == 'localhost'
    ) {
        unset($frag[intval(2)]);
    } else {
        unset($frag[intval($fnum - 2)]);
    }
}

$baseURL = implode('/', $frag);

if (
    $_SERVER['HTTP_HOST'] == '127.0.0.1'
    || $_SERVER['HTTP_HOST'] == 'localhost'
) {
    define('WEB_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/' . $baseURL . '/');
} else {
    define('WEB_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/' . $baseURL . '');
}

$gameConfig = array();
$userConfig = array();
$langConfig = array();
$IsUserVerf = FALSE;

define('DEFAULT_DESIGN', realpath(ROOT_PATH . '/design/'));
define('TEMPLATES_DIR' , realpath(ROOT_PATH . '/templates/'));
define('DEFAULT_LANG'  , 'french');

include(ROOT_PATH . 'includes/constants.php');
include(ROOT_PATH . 'includes/db.php');
include(ROOT_PATH . 'includes/debug_mode.php');
include(ROOT_PATH . 'includes/fleets_control.php');
include(ROOT_PATH . 'includes/functions.php');
include(ROOT_PATH . 'includes/strings.php');
include(ROOT_PATH . 'includes/unlocalised.php');

include(ROOT_PATH . 'language/' . DEFAULT_LANG . '/lang_info.cfg');

$debug = new Debug();

$config_query = doQuery('SELECT * FROM {{ table }}', 
                        'config');


while (
    $rc = $config_query->fetch_assoc()
) {
    $gameConfig[$rc['$config_name']] = $rc['config_value'];
}

if (
    !defined('DISABLE_IDENTITY_CHECK')
) {
    $results    = CheckTheUser($IsUserVerf);
    $IsUserVerf = $results['state'];
    $userConfig = $results['record'];
} 

elseif (
    !defined('DISABLE_IDENTITY_CHECK')
) {
    $parse  = array();
    $logout = parsetemplate(gettemplate('redir_login'), $parse);

    display($logout, 'Deconnexion');
}

// Get fleet start time datas
$fleets_query = doQuery('SELECT * 
                         FROM {{ table }} 
                         WHERE fleet_start_time <= UNIX_TIMESTAMP()', 
                        'fleets');

while (
    $fleet = $fleets_query->fetch_assoc()
) {
    $array                = array();
    $array['galaxy']      = $fleet['fleet_start_galaxy'];
    $array['system']      = $fleet['fleet_start_system'];
    $array['planet']      = $fleet['fleet_start_planet'];
    $array['planet_type'] = $fleet['fleet_start_type'];

    $temp = FlyingFleetHandler($array);
}

// Get fleet end time datas
$fleets_query = doQuery('SELECT * 
                         FROM {{ table }} 
                         WHERE fleet_end_time <= UNIX_TIMESTAMP()', 
                        'fleets');

while (
    $fleet = $fleets_query->fetch_assoc()
) {
    $array                = array();
    $array['galaxy']      = $fleet['fleet_end_galaxy'];
    $array['system']      = $fleet['fleet_end_system'];
    $array['planet']      = $fleet['fleet_end_planet'];
    $array['planet_type'] = $fleet['fleet_end_type'];

    $temp = FlyingFleetHandler($array);
}

unset($fleets_query);

include(ROOT_PATH . 'rak.php');

$dpath = DEFAULT_DESIGN;

SetSelectedPlanet($userConfig);

$planet = doQuery("SELECT *
                   FROM {{ table }}
                   WHERE id = '" . $userConfig['current_planet'] . "'; ",
                  'planets',
                  TRUE);

$galaxy = doQuery("SELECT *
                   FROM {{ table }}
                   WHERE id_planet = '" . $planet['id'] . "'; ",
                  'galaxy',
                  TRUE);

CheckPlanetUsedFields($planet);

?>