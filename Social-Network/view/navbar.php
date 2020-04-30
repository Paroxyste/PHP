<?php

// Get the number of unread messages
$msg    = new Message($con, $userLoggedIn);
$numMsg = $msg->UnreadMsgNumber();

// Get the number of friends requests
$userObj = new User($con, $userLoggedIn);
$numReq  = $userObj->GetFriendRequest();

?>

<header id="topnav">
    <div class="navbar-custom">
        <div class="container-fluid">
            <ul class="list-unstyled topnav-menu float-right mb-0">
                <li class="d-none d-sm-block">

                    <form class="app-search"
                          action="search.php"
                          method="GET"
                          name="search_form">

                        <div class="app-search-box">
                            <div class="input-group search-bar">
                                <input type="text"
                                       class="form-control"
                                       placeholder="Search Friends ..."
                                       name="user_search" 
                                       id="search_text_input" 
                                       autocomplete="off"
                                       minlength="1"
                                       maxlength="45"

                                       onkeyup="getLiveSearchUsers(
                                            this.value, 
                                            '<?php echo strip_tags($userLoggedIn); ?>'
                                        )" />

                                <div class="input-group-append">
                                    <button class="btn" type="submit">
                                        <i class="ti-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- User search results -->
                    <div class="search_results"></div>
                </li>

                <!-- Start games -->
                <li class="dropdown notification-list">

                    <!-- Start num messages badge -->
                    <a class="nav-link dropdown-toggle"
                       id="message_dropdown" href="javascript:void(0);"
                       data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false">

                        <i class="ti-game noti-icon"></i>
                    </a>
                    <!-- End num games badge -->

                    <!-- Start games dropdown-->
                    <div class="dropdown-menu dropdown-menu-right dropdown-lg"
                         aria-labelledby="message_dropdown">
                        <div class="dropdown-item noti-title ">
                            <h5 class="m-0">
                                Games
                            </h5>
                        </div>
                        
                        <!-- Start flappy bird game -->
                        <a href="./flappy.php"
                           class="dropdown-item notify-item">
                            <div class="notify-icon">
                                <img src="./view/images/games/flappy_bird.png"
                                      class="img-fluid rounded-circle"
                                      alt="Flappy Bird" />
                            </div>

                            <p class="notify-details">
                                Flappy Bird

                                <small class="text-muted">
                                    <i>
                                        Arcade game
                                    </i>
                                </small>
                            </p>
                        </a>
                        <!-- End flappy bird -->

                        <!-- Start breakout game -->
                        <a href="./breakout.php"
                           class="dropdown-item notify-item">
                            <div class="notify-icon">
                                <img src="./view/images/games/breakout.jpeg"
                                     class="img-fluid rounded-circle"
                                     alt="Breakout" />
                            </div>

                            <p class="notify-details">
                                Breakout

                                <small class="text-muted">
                                    <i>
                                        Arcade game
                                    </i>
                                </small>
                            </p>
                        </a>
                        <!-- End breakout -->

                        <!-- Start tetris game -->
                        <a href="./tetris.php"
                           class="dropdown-item notify-item">
                            <div class="notify-icon">
                                <img src="./view/images/games/tetris.png"
                                     class="img-fluid rounded-circle"
                                     alt="Tetris" />
                            </div>

                            <p class="notify-details">
                                Tetris

                                <small class="text-muted">
                                    <i>
                                        Arcade game
                                    </i>
                                </small>
                            </p>
                        </a>
                        <!-- End tetris -->
                    </div>
                    <!-- End games dropdown -->
                </li>
                <!-- End games -->

                <!-- Start messages -->
                <li class="dropdown notification-list">

                    <!-- Start num messages badge -->
                    <a class="nav-link dropdown-toggle"
                       id="message_dropdown" href="javascript:void(0);"
                       data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false"
                       onclick="getNumMsg(
                            '<?php echo strip_tags($userLoggedIn); ?>', 
                            'message')">

                        <i class="ti-email noti-icon"></i>

                        <?php

                        if (
                            $numMsg > 0
                        ) {
                            echo "
                                <span class='badge badge-danger rounded-circle
                                             noti-icon-badge'
                                      id='unread_message'>"
                                    . $numMsg . 
                                "</span>
                            ";
                        }

                        ?>
                    </a>
                    <!-- End num messages badge -->

                    <!-- Start messages dropdown-->
                    <div class="dropdown-menu dropdown-menu-right dropdown-lg"
                         aria-labelledby="message_dropdown">
                        <div class="dropdown-item noti-title ">
                            <h5 class="m-0">
                                Messages
                            </h5>
                        </div>

                        <div class="dropdown_data_window"></div>

                        <input type="hidden" value="" 
                               id="dropdown_data_type" />
                    </div>
                    <!-- End messages dropdown -->
                </li>
                <!-- End messages -->

                <!-- Start num friends requests badge -->
                <li class="dropdown notification-list">
                    <a href="requests.php"
                       class="nav-link right-bar-toggle">

                        <i class="ti-user noti-icon"></i>

                        <?php

                        if (
                            $numReq > 0
                        ) {
                            echo "
                                <span class='badge badge-danger rounded-circle
                                             noti-icon-badge'
                                      id='unread_request'>"
                                    . $numReq . 
                                "</span>
                            ";
                        }

                        ?>

                    </a>
                </li>
                <!-- End num friends requests badge -->

                <!-- Start user menu -->
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle nav-user mr-0"
                       data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">

                        <img src="
                                <?php 
                                    echo strip_tags($user['profile_pic']);
                                ?>"
                             class="rounded-circle"
                             alt="user-image" />

                        <span class="pro-user-name ml-1">
                            <?php
                                echo strip_tags($user['first_name']);
                            ?>

                            <i class="ti-angle-down ml-1"
                               style="font-size: 0.8695vh;"></i>
                        </span>
                    </a>

                    <!-- Start user dropdown -->
                    <div class="dropdown-menu dropdown-menu-right 
                                profile-dropdown">

                        <a href="./index.php"
                           class="dropdown-item notify-item">

                            <i class="ti-home"></i>
                            <span>
                                Home
                            </span>
                        </a>

                        <a href="./settings.php"
                           class="dropdown-item notify-item">

                            <i class="ti-settings"></i>
                            <span>
                                Settings
                            </span>
                        </a>

                        <a href="./logout.php"
                           class="dropdown-item notify-item">

                            <i class="ti-power-off"></i>
                            <span>
                                Logout
                            </span>
                        </a>
                    </div>
                    <!-- End user dropdown -->
                </li>
                <!-- End user menu -->
            </ul>

            <!-- Start Logo -->
            <div class="logo-box">
                <a href="index.php"
                   class="logo text-center">

                   <span class="logo-lg">
                        <span class="logo-lg-text-dark">
                            My Social Network
                        </span>
                    </span>
                </a>
            </div>
            <!-- End Logo -->

        </div>
    </div>
</header>

<script>
(function($, window, document) {

    $(function() {
        let userLoggedIn = '<?php echo strip_tags($userLoggedIn); ?>';
        let dropdownInProgress = false;

        $('.dropdown_data_window').scroll(function() {
            let bottomElement = $('.dropdown_data_window a').last();
            let noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

            if (isElementInView(bottomElement[0]) && noMoreData == 'false') {
                loadMsgData();
            };
        });
    });

}(window.jQuery, window, document));
</script>