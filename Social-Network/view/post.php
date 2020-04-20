<?php

declare(strict_types=1);

require('./view/header.php');
require('./view/navbar.php');
require('./view/user_counter.php');

if (
    isset($_GET['id'])
) {
    $id = $_GET['id'];
} else {
    $id = 0;
}

?>

<div class="row">
    <div class="col-lg-4 col-xl-4">

        <?php

        require('./view/left-panel.php');

        ?>

    </div>

    <div class="col-lg-8 col-xl-8">
        <div class="card-box card-details">

            <?php
                $post = new Post($con, $userLoggedIn);
                $post->GetSinglePost($id);
            ?>

        </div>
    </div>
</div>

<?php

require('./view/footer.php');

?>

</body>
</html>