<?php

declare(strict_types=1);

require('./view/header.php');
require('./view/navbar.php');
require('./view/user_counter.php');

?>

<div class="row">
    <div class="col-lg-4 col-xl-4">

        <?php

            require('./view/left-panel.php');

        ?>

    </div>

    <div class="col-lg-8 col-xl-8">
        <div class="card-box card-details">

            <h5 class="mb-3 text-uppercase bg-light p-2">
                <i class="ti-check-box mr-1"></i>
                Manage your friends requests
            </h5>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                        <?php

                        include('./controller/form_handlers/requests_handler.php');

                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

require('./view/footer.php');

?>

</body>
</html>