<div class="row">
    <div class="col-lg-9 col-xl-9">
        <div class="card-box card-details">
            <h3 class="text-uppercase">
                <img src="../view/images/games/flappy_bird.png"
                     class="rounded-circle mr-1"
                     height="80px"/>
                Flappy Bird
            </h3>

            <hr class="mb-3"/>

            <div class="row justify-content-md-center">
                <canvas id="canvas" width="340" height="512"></canvas>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-xl-3">

        <?php

        include('./view/dashboard/message_box.php');

        ?>

    </div>
</div>

<script src="../view/js/flappyBird.js"></script>