<?php

declare(strict_types=1);

require('./view/header.php');

header('Location:' . strip_tags($userLoggedIn));

?>
