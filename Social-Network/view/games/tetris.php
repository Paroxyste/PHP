<div class="row">
    <div class="col-lg-9 col-xl-9">
        <div class="card-box card-details">
            <h3 class="text-uppercase">
                <img src="../view/images/games/tetris.png"
                     class="rounded-circle mr-1"
                     height="80px"/>
                    Tetris
            </h3>

            <hr class="mb-3"/>

            <div class="row justify-content-md-center mb-3">
                <div class="col-4">
                    <p class="text-muted font-15 mb-1 text-uppercase">
                        <i class="mr-1 ti-cup"></i>
                        Score :

                        <span id="score">
                            0
                        </span>
                    </p>
                </div>

                <div class="col-4">
                    <p class="text-muted font-15 mb-1 text-uppercase">
                        <i class="mr-1 ti-heart"></i>
                        Lives :

                        <span class="lives"> 
                            0
                        </span>
                    </p>
                </div>

                <p id="startBtn" 
                   class="font-15 mb-1 text-uppercase btn btn-outline-primary">
                    Start Game
                </p>
            </div>

            <div class="row justify-content-md-center">
                <canvas id="tetris" width="200" height="400"></canvas>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-xl-3">

        <?php

        include('./view/dashboard/message_box.php');

        ?>

    </div>
</div>

<script src="../view/js/tetromino.js"></script>
<script src="../view/js/tetris.js"></script>