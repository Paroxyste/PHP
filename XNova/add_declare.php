<?php

declare(strict_types = 1);

define('INSIDE' , TRUE);
define('INSTALL', FALSE);

require dirname(__FILE__) . '/common.php';

includeLang('admin');

$mode = $_POST['mode'];

$PageTpl = gettemplate('add_declare');
$parse   = $lang;

if (
    $mode == 'addit'
) {
    $declarator       = $user['id'];
    $declarator_name  = addslashes(htmlspecialchars($user['username']));
    $decl1            = addslashes(htmlspecialchars($_POST['dec1']));
    $decl2            = addslashes(htmlspecialchars($_POST['dec2']));
    $decl3            = addslashes(htmlspecialchars($_POST['dec3']));
    $reason1          = addslashes(htmlspecialchars($_POST['reason']));

    $QryDeclare  = "INSERT INTO {{table}} SET ";
    $QryDeclare .= "`declarator` = '". $declarator ."', ";
    $QryDeclare .= "`declarator_name` = '". $declarator_name ."', ";
    $QryDeclare .= "`declared_1` = '". $decl1 ."', ";
    $QryDeclare .= "`declared_2` = '". $decl2 ."', ";
    $QryDeclare .= "`declared_3` = '". $decl3 ."', ";
    $QryDeclare .= "`reason`     = '". $reason1 ."' ";

    doquery( $QryDeclare, 'declared');

    doquery("UPDATE {{table}}
             SET multi_validated ='1' 
             WHERE username='{$user['username']}'",
             'users'
    );

    AdminMessage('Merci, votre demande a ete prise en compte. Les autres 
                  joueurs que vous avez implique doivent egalement et 
                  imperativement suivre cette procedure aussi.', 'Ajout');
}

$Page = parsetemplate($PageTpl, $parse);

display($Page, "Declaration d\'IP partagee", FALSE, '', TRUE);

?>