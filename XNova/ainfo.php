<?php

declare(strict_types = 1);

define('INSIDE' , TRUE);
define('INSTALL', FALSE);

require dirname(__FILE__) .'/common.php';

$dpath = (!$userrow['dpath']) ? DEFAULT_SKINPATH : $userrow['dpath'];

//si a n'est pas un numerique ou qu'il n'existe pas
if (
    !is_numeric($_GET['a'])
    || !$_GET['a']
){
    message("qu'est ce que tu fait", 'erreur');
}

$allyrow = doquery("SELECT ally_name, ally_tag, ally_description, ally_web,ally_image 
                    FROM {{table}} 
                    WHERE id=".$_GET['a'], 'alliance', TRUE);

if (
    !$allyrow
){
    message('Alliance non trouv&eacute;e', 'Erreur');
}

$count = doquery("SELECT COUNT(DISTINCT(id)) 
                  FROM {{table}} 
                  WHERE ally_id = ".$_GET['a'] . ';', 'users', TRUE);

$ally_member_scount = $count[0];

$page .= "<table width=100%>
            <tr>
                <td class=c colspan=2>
                    Informations sur l'alliance
                </td>
            </tr>";

if (
    $allyrow['ally_image'] != ''
){
    $page .= "<tr>
                <th colspan=2>
                    <img src=\"" . $allyrow['ally_image'] . "\">
                </th>
            </tr>";
}

$page .= "<tr>
            <th>
                Tag
            </th>
            
            <th>"
                . $allyrow['ally_tag'] .
            "</th>
        </tr>

        <tr>
            <th>
                Nom
            </th>
            
            <th>"
                . $allyrow['ally_name'] ."
            </th>
        </tr>

        <tr>
            <th>
                Membres
            </th>

            <th>
                $ally_member_scount
            </th>
        </tr>";

if (
    $allyrow['ally_description'] != ''
){
    $page .= "<tr>
                <th colspan=2 height=100>"
                    . $allyrow['ally_description'] .
                "</th>
            </tr>";
}

if (
    $allyrow['ally_web'] != ''
){
    $page .="<tr>
                <th>
                    Site internet
                </th>

                <th>
                    <a href=\"" . $allyrow['ally_web'] ."\">"
                        . $allyrow['ally_web'] .
                    "</a>
                </th>
            </tr>";
}

$page .= "</table>";

display($page, 
        "Information sur l'alliance [" . $allyrow['ally_name'] . ']', 
        TRUE
);

?>