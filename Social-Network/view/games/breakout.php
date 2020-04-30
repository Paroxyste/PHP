<div class="row">
    <div class="col-lg-9 col-xl-9">
        <div class="card-box card-details">
            <h3 class="text-uppercase">
                <img src="../view/images/games/breakout.jpeg"
                     class="rounded-circle mr-1"
                     height="80px"/>
                    Breakout
            </h3>

            <hr class="mb-3"/>

            <div class="row justify-content-md-center mb-3">
                <div class="col-4">
                    <p class="text-muted font-15 mb-1 text-uppercase">
                        <i class="mr-1 ti-cup"></i>
                        Score :

                        <span class="score"> 
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
                <div id="breakout">
                    <div id="ball"></div>
                    <div id="paddle"></div>
                    <div id="gameover">
                    <div class="alert alert-primary text-center">
                        <h2 class="text-uppercase mb-4">
                            Welcome to Breakout Game !
                        </h2>

                        <h5 class="text-uppercase mb-4">
                            To play use the <b>arrows</b> on your 
                            <b>keyboard</b> :

                            <br /><br />

                            Throw the ball :
                            <i class="ti-arrow-up ml-1 mr-3"></i>

                            Move the cursor : 
                            <i class="ti-arrow-left ml-1 mr-2"></i>
                            <i class="ti-arrow-right"></i>
                        </h5>

                        <h5 class="text-uppercase">Click <b>Start Game Button</b> to begin !</h5>
                    </div>
                </div>
            </div>
        </div></div></div>
            <br />


    <div class="col-lg-3 col-xl-3">

        <?php

        include('./view/dashboard/message_box.php');

        ?>

    </div>
</div>

<script src="../view/js/breakout.js"></script>