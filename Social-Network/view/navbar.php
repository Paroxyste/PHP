<?php

// Get the number of unread messages
$msg     = new Message($con, $userLoggedIn);
$num_msg = $msg->UnreadMsgNumber();

// Get the number of unread notifications
$notifs     = new Notification($con, $userLoggedIn);
$num_notifs = $notifs->UnreadNotifsNumber();

// Get the number of friends requests
$user_obj     = new User($con, $userLoggedIn);
$num_requests = $user_obj->GetFriendRequest();

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
                                       id="search_text_input" autocomplete="off" 
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

                    <!-- Start user search -->
                    <div class="search_results"></div>
                </li>

                <!-- Start messages -->
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle"
                       id="message_dropdown" href="javascript:void(0);"
                       data-toggle="dropdown" role="button"
                       aria-haspopup="false" aria-expanded="false"
                       onclick="getDropdownData(
                            '<?php echo strip_tags($userLoggedIn); ?>', 
                            'message'
                        )">

                        <i class="ti-email noti-icon"></i>

                        <?php

                        if (
                            $num_msg > 0
                        ) {
                            echo "
                                <span class='badge badge-danger rounded-circle
                                             noti-icon-badge'
                                       id='unread_message'>"
                                    . $num_msg . 
                                "</span>
                            ";
                        }

                        ?>
                    </a>

                    <!-- Start messages dropdown-->
                    <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                Messages
                            </h5>
                        </div>

                        <!-- Messages dropdown item-->
                        <div class="slimscroll noti-scroll">

                            <!-- item-->
                            <a href="javascript:void(0);"
                               class="dropdown-item notify-item">
                                <div class="notify-icon">
                                    <img src="./view/images/users/default.jpg"
                                         class="img-fluid rounded-circle"
                                         alt="Messages Icon" />
                                </div>

                                <p class="notify-details">
                                    Cristina Pride

                                    <small class="text-muted">
                                        <i>
                                            Hi, How are you? What about our 
                                            next meeting
                                        </i>
                                    </small>
                                </p>

                                <p class="text-muted mb-0 user-msg">
                                    <small>
                                        1 min ago
                                    </small>
                                </p>
                            </a>

                            <!-- View all messages btn -->
                            <a href="javascript:void(0);"
                               class="dropdown-item text-center text-primary
                                      notify-item notify-all">
                                View all messages
                            </a>
                        </div>
                        <!-- End messages item -->
                    </div>
                    <!-- End messages dropdown -->
                </li>
                <!-- End messages -->

                <!-- Start notifications -->
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle"
                       data-toggle="dropdown" role="button"
                       id="notification_dropdown" href="javascript:void(0);"
                       aria-haspopup="false" aria-expanded="false"
                       onclick="getDropdownData(
                           '<?php echo strip_tags($userLoggedIn); ?>', 
                           'notification')">

                        <i class="ti-bell noti-icon"></i>

                        <?php

                        if (
                            $num_notifs > 0
                        ) {
                            echo "
                                <span class='badge badge-danger rounded-circle
                                             noti-icon-badge'
                                      id='unread_notification'>"
                                      
                                    . $num_notifs . 
                                "</span>
                            ";
                        }

                        ?>
                    </a>

                    <!-- Start notifications dropdown-->
                    <div class="dropdown-menu dropdown-menu-right 
                                dropdown-lg">
                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                Notification
                            </h5>
                        </div>

                        <!-- Notification dropdown item-->
                        <div class="slimscroll noti-scroll">

                            <!-- item-->
                            <a href="javascript:void(0);"
                               class="dropdown-item notify-item active">

                               <div class="notify-icon">
                                    <img src="../../view/images/users/default.jpg"
                                         class="img-fluid rounded-circle"
                                         alt="Notification Icon" />
                                </div>

                                <p class="notify-details">
                                    Cristina Pride

                                    <small class="text-muted">
                                        <i>
                                            commented on Admin
                                        </i>
                                    </small>
                                </p>

                                <p class="text-muted mb-0 user-msg">
                                    <small>
                                        1 min ago
                                    </small>
                                </p>
                            </a>

                            <!-- View all notification btn -->
                            <a href="javascript:void(0);"
                               class="dropdown-item text-center text-primary
                                      notify-item notify-all">
                                View all notifications
                            </a>
                        </div>
                        <!-- End notifications item -->
                    </div>
                    <!-- End notifications dropdown -->
                </li>
                <!-- End notifications -->

                <li class="dropdown notification-list">
                    <a href="requests.php"
                       class="nav-link right-bar-toggle">

                        <i class="ti-user noti-icon"></i>

                        <?php

                        if (
                            $num_requests > 0
                        ) {
                            echo "
                                <span class='badge badge-danger rounded-circle
                                             noti-icon-badge'
                                      id='unread_request'>"
                                    . $num_requests . 
                                "</span>
                            ";
                        }

                        ?>

                    </a>
                </li>

                <!-- Start user -->
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
                <!-- End user -->
            </ul>

            <!-- LOGO -->
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
        </div>
        <!-- end container-fluid-->
    </div>
    <!-- end Topbar -->
</header>
<!-- End Navigation Bar-->