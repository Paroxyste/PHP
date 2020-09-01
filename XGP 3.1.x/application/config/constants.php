<?php

declare(strict_types = 1);

/* ----------------------------------------------------------------------------
** Game files version
*/

define('SYSTEM_VERSION', '3.1.0');

/* ----------------------------------------------------------------------------
** Debug mode
*/

define('DEBUG_MODE', FALSE);

/* ----------------------------------------------------------------------------
** System paths configuration
*/

define('REQUEST_SCHEME', $_SERVER['REQUEST_SCHEME']);
define('HTTPS'         , $_SERVER['HTTPS']);
define('SERVER_PORT'   , $_SERVER['SERVER_PORT']);

if (
    !empty(REQUEST_SCHEME) && REQUEST_SCHEME == 'https'
    || !empty(HTTPS) && HTTPS == 'on'
    || !empty(SERVER_PORT) && SERVER_PORT == '443'
) {
    define('PROTOCOL', 'https://');
} else {
    define('PROTOCOL', 'https://');
}

/* ----------------------------------------------------------------------------
** Base path
*/

define('HTTP_HOST'  , $_SERVER['HTTP_HOST']);
define('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);

define('BASE_PATH', 
       HTTP_HOST . str_replace('/' . basename(SCRIPT_NAME), '', SCRIPT_NAME)
);

/* ----------------------------------------------------------------------------
** System root ignoring public
*/

define('SYSTEM_ROOT', 
       PROTOCOL . strtr(BASE_PATH, ['public' => '', 'public/' => ''])
);

/* ----------------------------------------------------------------------------
** Game URL
*/

define('GAMEURL', PROTOCOL . HTTP_HOST . '/');

/* ----------------------------------------------------------------------------
** Admin path
*/

define('ADM_URL', 
       PROTOCOL . strtr(BASE_PATH, ['public' => '', 'public/' => '']));

/* ----------------------------------------------------------------------------
** Global directory structure
*/

define('APP_PATH'   , 'application' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', 'public'      . DIRECTORY_SEPARATOR);
define('SYSTEM_PATH', 'system'      . DIRECTORY_SEPARATOR);

/* ----------------------------------------------------------------------------
** Application directory structure
*/

define('CONFIGS_PATH'    , APP_PATH . 'config'      . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', APP_PATH . 'controllers' . DIRECTORY_SEPARATOR);
define('CORE_PATH'       , APP_PATH . 'core'        . DIRECTORY_SEPARATOR);
define('HELPERS_PATH'    , APP_PATH . 'helpers'     . DIRECTORY_SEPARATOR);
define('LANG_PATH'       , APP_PATH . 'language'    . DIRECTORY_SEPARATOR);
define('LIB_PATH'        , APP_PATH . 'libraries'   . DIRECTORY_SEPARATOR);
define('MODELS_PATH'     , APP_PATH . 'models'      . DIRECTORY_SEPARATOR);
define('TEMPLATE_DIR'    , APP_PATH . 'views'       . DIRECTORY_SEPARATOR);
define('VENDOR_PATH'     , APP_PATH . 'third_party' . DIRECTORY_SEPARATOR);

/* ----------------------------------------------------------------------------
** Controllers directory structure
*/

define('ADMIN_PATH'  , CONTROLLERS_PATH . 'adm'     . DIRECTORY_SEPARATOR);
define('AJAX_PATH'   , CONTROLLERS_PATH . 'ajax'    . DIRECTORY_SEPARATOR);
define('GAME_PATH'   , CONTROLLERS_PATH . 'game'    . DIRECTORY_SEPARATOR);
define('HOME_PATH'   , CONTROLLERS_PATH . 'home'    . DIRECTORY_SEPARATOR);
define('INSTALL_PATH', CONTROLLERS_PATH . 'install' . DIRECTORY_SEPARATOR);

/* ----------------------------------------------------------------------------
** Public directory structure
*/

define('ADMIN_PUBLIC_PATH', PUBLIC_PATH . 'admin'   . DIRECTORY_SEPARATOR);
define('CSS_PATH'         , PUBLIC_PATH . 'css'     . DIRECTORY_SEPARATOR);
define('IMG_PATH'         , PUBLIC_PATH . 'images'  . DIRECTORY_SEPARATOR);
define('JS_PATH'          , PUBLIC_PATH . 'js'      . DIRECTORY_SEPARATOR);
define('PUB_INS_PATH'     , PUBLIC_PATH . 'install' . DIRECTORY_SEPARATOR);
define('UPLOAD_PATH'      , PUBLIC_PATH . 'upload'  . DIRECTORY_SEPARATOR);

/* ----------------------------------------------------------------------------
** Skin directory structure
*/

define('DEFAULT_SKINPATH', SKIN_PATH   . 'xgproyect' . DIRECTORY_SEPARATOR);
define('SKIN_PATH'       , UPLOAD_PATH . 'skins'     . DIRECTORY_SEPARATOR);
define('DPATH'           , DEFAULT_SKINPATH);

/* ----------------------------------------------------------------------------
** Timing constants
*/

define('ONE_DAY'  , (60 * 60 * 24));
define('ONE_WEEK' , (ONE_DAY * 7));
define('ONE_MONTH', (ONE_DAY * 30));

/* ----------------------------------------------------------------------------
** Universe data, galaxy, systems, planets
*/

define('MAX_GALAXY_IN_WORLD' , 9);
define('MAX_SYSTEM_IN_GALAXY', 499);
define('MAX_PLANET_IN_SYSTEM', 15);

/* ----------------------------------------------------------------------------
** Fields for each level of the lunar base
*/

define('FIELDS_BY_MOONBASIS_LEVEL', 3);

/* ----------------------------------------------------------------------------
** Fields for each level if the terraformer
*/

define('FIELDS_BY_TERRAFORMER', 5);

/* ----------------------------------------------------------------------------
** Number of buildings that can go in the construction queue
*/

define('MAX_BUILDING_QUEUE_SIZE', 5);

/* ----------------------------------------------------------------------------
** Number of ships/defenses that can build for once
*/

define('MAX_FLEET_OR_DEFS_PER_ROW', 9999);

/* ----------------------------------------------------------------------------
** Max results to show in search
*/

define('MAX_SEARCH_RESULTS', 25);

/* ----------------------------------------------------------------------------
** Planet size multiplier
*/

define('PLANETSIZE_MULTIPLER', 1);

/* ----------------------------------------------------------------------------
** Initial resource of new planets
*/

define('BUILD_METAL'    , 500);
define('BUILD_CRISTAL'  , 500);
define('BUILD_DEUTERIUM', 0);

/* ----------------------------------------------------------------------------
** Officiers default values
*/

define('AMIRAL'           , 2);
define('ENGINEER_DEFENSE' , 2);
define('ENGINEER_ENERGY'  , 0.5);
define('GEOLOGUE'         , 0.1);
define('TECHNOCRATE_SPY'  , 2);
define('TECHNOCRATE_SPEED', 0.25);

/* ----------------------------------------------------------------------------
** Invisibles debris
*/

define('DEBRIS_LIFE_TIME'       , ONE_WEEK);
define('DEBRIS_MIN_VISIBLE_SIZE', 300);

/* ----------------------------------------------------------------------------
** Destroyed planets life time (in hours)
*/

define('PLANETS_LIFE_TIME', 24);

/* ----------------------------------------------------------------------------
** Vacantion time that an user has to be on vacation mode before it can 
** remove it (in days) 
*/

define('VACATION_TIME_FORCED', 2);

/* ----------------------------------------------------------------------------
** Resource market
*/

define('BASIC_RESOURCE_MARKET_DM', 
      [
        'metal'     => 4500,
        'crystal'   => 9000,
        'deuterium' => 13500,
      ]
);

/* ----------------------------------------------------------------------------
** Phalanx cost
*/

define('PHALANX_COST', 10000);

/* ----------------------------------------------------------------------------
** Tables
*/

define('ACS'                , '{xgp_prefix}acs');
define('ACS_MEMBERS'        , '{xgp_prefix}acs_members');
define('ALLIANCE'           , '{xgp_prefix}alliance');
define('ALLIANCE_STATISTICS', '{xgp_prefix}alliance_statistics');
define('BANNED'             , '{xgp_prefix}banned');
define('BUDDY'              , '{xgp_prefix}buddys');
define('BUILDINGS'          , '{xgp_prefix}buildings');
define('CHANGELOG'          , '{xgp_prefix}changelog');
define('DEFENSES'           , '{xgp_prefix}defenses');
define('FLEETS'             , '{xgp_prefix}fleets');
define('LANGUAGES'          , '{xgp_prefix}languages');
define('MESSAGES'           , '{xgp_prefix}messages');
define('NOTES'              , '{xgp_prefix}notes');
define('OPTIONS'            , '{xgp_prefix}options');
define('PLANETS'            , '{xgp_prefix}planets');
define('PREFERENCES'        , '{xgp_prefix}preferences');
define('PREMIUM'            , '{xgp_prefix}premium');
define('RESEARCH'           , '{xgp_prefix}research');
define('REPORTS'            , '{xgp_prefix}reports');
define('SESSIONS'           , '{xgp_prefix}sessions');
define('SHIPS'              , '{xgp_prefix}ships');
define('USERS'              , '{xgp_prefix}users');
define('USERS_STATISTICS'   , '{xgp_prefix}users_statistics');

/* ----------------------------------------------------------------------------
** Mailing
*/

$charset = 'UTF-8';

ini_set('default_charset', $charset);

/* 
mbstring.internal_encoding is deprecated starting with PHP 5.6 and it's 
usage triggers E_DEPRECATED messages. This is required for 
mb_convert_encoding() to strip invalid characters. That's utilized by CI_UTF_8, 
but it's also done for consistency with iconv.
*/

if (
    extension_loaded('mbstring')
) {
    define('MB_ENABLED', TRUE);

    @ini_set('mbstring.internal_encoding', $charset);

    mb_substitute_character('none');
} else {
    define('MB_ENABLED', FALSE);
}

/*
There's an ICONV_IMPL constant, but the PHP manual says that using iconv's 
predefined constants is 'strongly discouraged'. iconv.internal_encoding is 
deprecated starting with PHP 5.6 and it's usage triggers E_DEPRECATED messages.
*/

if (
    extension_loaded('iconv')
) {
    define('ICONV_ENABLED', TRUE);

    @ini_set('iconv.internal_encoding', $charset);
} else {
    define('ICONV_ENABLED', FALSE);
}