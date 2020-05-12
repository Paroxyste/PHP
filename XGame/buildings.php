<?php

declare(strict_types = 1);

require dirname(__FILE__) . '/common.php';

define('INSIDE',  TRUE);
define('INSTALL', FALSE);

textLang('buildings');

UpdPlanetBuildQueue($planet, $user);
$isWorking = HandleResearchBuild($planet, $user);

switch ($_GET['mode']) {
    case 'defense':
        DefensesBuildPage($planet, $user);
        break;
    case 'fleet':
        FleetsBuildPage($planet, $user);
        // no break
    case 'research':
        ResearchesBuildPage($planet, $user);
        return;
    default:
        BuildingsBuildPage($planet, $user);
        break;
}

?>