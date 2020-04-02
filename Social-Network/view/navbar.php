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
                                                    '<?php echo $userLoggedIn; ?>'
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
data-toggle="dropdown" href="#" role="button"
aria-haspopup="false" aria-expanded="false">
<i class="ti-email noti-icon"></i>
<span class="badge badge-danger rounded-circle
noti-icon-badge">
9
</span>
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
class="dropdown-item notify-item active">
<div class="notify-icon">
<img src="./view/images/users/user-1.jpg"
class="img-fluid rounded-circle"
alt="Messages Icon" />
</div>

<p class="notify-details">
Cristina Pride
</p>

<p class="text-muted mb-0 user-msg">
<small>
Hi, How are you? What about our next meeting
</small>
</p>
</a>

<!-- item-->
<a href="javascript:void(0);"
class="dropdown-item notify-item">
<div class="notify-icon bg-primary">
<i class="mdi mdi-comment-account-outline"></i>
</div>
<p class="notify-details">
Caleb Flakelar commented on Admin
<small class="text-muted">
1 min ago
</small>
</p>
</a>

<!-- View all messages btn -->
<a href="javascript:void(0);"
class="dropdown-item text-center text-primary
notify-item notify-all">
View all messages
<i class="fi-arrow-right"></i>
</a>
</div>
<!-- End messages dropdown -->
</li>
<!-- End messages -->

<!-- Start notifications -->
<li class="dropdown notification-list">
<a class="nav-link dropdown-toggle"
data-toggle="dropdown" href="#" role="button"
aria-haspopup="false" aria-expanded="false">
<i class="ti-bell noti-icon"></i>
<span class="badge badge-danger rounded-circle
noti-icon-badge">
9
</span>
</a>

<!-- Start notifications dropdown-->
<div class="dropdown-menu dropdown-menu-right dropdown-lg">
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
<img src="../../view/images/users/user-1.jpg"
class="img-fluid rounded-circle"
alt="Notification Icon" />
</div>

<p class="notify-details">
Cristina Pride
</p>

<p class="text-muted mb-0 user-msg">
<small>
Hi, How are you? What about our next meeting
</small>
</p>
</a>

<!-- item-->
<a href="javascript:void(0);"
class="dropdown-item notify-item">
<div class="notify-icon bg-primary">
<i class="mdi mdi-comment-account-outline"></i>
</div>
<p class="notify-details">
Caleb Flakelar commented on Admin
<small class="text-muted">
1 min ago
</small>
</p>
</a>

<!-- item-->
<a href="javascript:void(0);"
class="dropdown-item notify-item">
<div class="notify-icon">
<img src="../../view/images/users/user-4.jpg"
class="img-fluid rounded-circle"
alt="" />
</div>

<p class="notify-details">
Karen Robinson
</p>

<p class="text-muted mb-0 user-msg">
<small>
Wow ! this admin looks good and awesome design
</small>
</p>
</a>

<!-- View all notification btn -->
<a href="javascript:void(0);"
class="dropdown-item text-center text-primary
notify-item notify-all">
View all notifications
<i class="fi-arrow-right"></i>
</a>
</div>
<!-- End notifications dropdown -->
</li>
<!-- End notifications -->

<li class="dropdown notification-list">
<a href="javascript:void(0);"
class="nav-link right-bar-toggle">
<i class="ti-user noti-icon"></i>
<span class="badge badge-danger rounded-circle
noti-icon-badge">
9
</span>
</a>
</li>

<!-- Start user -->
<li class="dropdown notification-list">
<a class="nav-link dropdown-toggle nav-user mr-0"
data-toggle="dropdown" href="#" role="button"
aria-haspopup="false" aria-expanded="false">
<img src="<?php echo strip_tags($user['profile_pic']); ?>"
class="rounded-circle"
alt="user-image" />

<span class="pro-user-name ml-1">

<?php
echo strip_tags($user['first_name']);
?>

<i class="ti-angle-down"
style="font-size: 0.8695vh;"></i>
</span>
</a>

<!-- Start user dropdown -->
<div class="dropdown-menu dropdown-menu-right profile-dropdown">
<a href="./logout.php"
class="dropdown-item notify-item">
<i class="ti-power-off"></i>
<span>Logout</span>
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