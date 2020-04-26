<?php

declare(strict_types=1);

include('./view/header.php');

echo "
    <script>
        location.href='". strip_tags($userLoggedIn) ."'
    </script>
";

?>
