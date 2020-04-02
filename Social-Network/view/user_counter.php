<?php

//------------------------------------------------------------------------------
// Counters

$numFriends = substr_count($user['friend_array'], ',') - 1;
$numLikes   = $user['num_likes'];
$numPosts   = $user['num_posts'];

/*
//------------------------------------------------------------------------------
// API ipinfo.io

function get_client_ip() {
    $ipaddress = '';

    if (
        isset($_SERVER['HTTP_CLIENT_IP'])
    ) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    if (
        isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    ) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    if (
        isset($_SERVER['HTTP_X_FORWARDED'])
    ) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    }

    if (
        isset($_SERVER['HTTP_FORWARDED_FOR'])
    ) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }

    if (
        isset($_SERVER['HTTP_FORWARDED'])
    ) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }

    if (
        isset($_SERVER['REMOTE_ADDR'])
    ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = 'UNKNOWN';
    }

    return $ip;
}

$publicIp = get_client_ip();

$iploc = file_get_contents('http://ipinfo.io/$publicIp/geo');
$iploc = json_decode($iploc, TRUE);

$country  = $iploc['country'];
$city     = $iploc['city'];
$postal   = $iploc['postal'];

// -----------------------------------------------------------------------------
// API openweathermap.org

$openWeather = 'http://api.openweathermap.org/data/2.5/weather?q=';
$appID = '&appid='

$request = $openWeather . $city . $appID;

$temploc  = file_get_contents($request);
$temploc  = json_decode($temploc, TRUE);

$temp    = $temploc['main'];
$weather = $temploc['weather'];

print($temp);
print($weather);
*/
?>

    <div class="wrapper">
      <div class="container-fluid">
        <div class="row">

          <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
              <div class="row">
                <div class="col-6">
                  <div class="avatar-lg rounded-circle bg-soft-warning
                              border-warning border">
                    <i class="ti-user font-22 avatar-title
                              text-warning"></i>
                  </div>
                </div>

                <div class="col-6">
                  <div class="text-right">
                    <h3 class="text-dark mt-1">
                      <span data-plugin="counterup">
                        <?php
                          echo strip_tags($numFriends);
                        ?>
                      </span>
                    </h3>

                    <p class="text-muted mb-1 text-truncate">
                      Number of Friends
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
              <div class="row">
                <div class="col-6">
                  <div class="avatar-lg rounded-circle bg-soft-danger
                              border-danger border">
                    <i class="ti-heart font-22 avatar-title text-danger"></i>
                  </div>
                </div>

                <div class="col-6">
                  <div class="text-right">
                    <h3 class="text-dark mt-1">
                      <span data-plugin="counterup">
                        <?php
                          echo strip_tags($numLikes);
                        ?>
                      </span>
                    </h3>

                    <p class="text-muted mb-1 text-truncate">
                      Total Likes
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
              <div class="row">
                <div class="col-6">
                  <div class="avatar-lg rounded-circle bg-soft-info
                              border-info border">
                    <i class="ti-comment-alt font-22 avatar-title
                              text-info"></i>
                  </div>
                </div>

                <div class="col-6">
                  <div class="text-right">
                    <h3 class="text-dark mt-1">
                      <span data-plugin="counterup">
                        <?php
                          echo strip_tags($numPosts);
                        ?>
                      </span>
                    </h3>

                    <p class="text-muted mb-1 text-truncate">
                      Number of Posts
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
              <div class="row">
                <div class="col-6">
                  <div class="avatar-lg rounded-circle bg-soft-light
                              border-secondary border">
                    <i class="ti-shine font-22 avatar-title text-secondary"></i>
                  </div>
                </div>

                <div class="col-6">
                  <div class="text-right">
                    <h3 class="text-dark mt-1">
                      <span data-plugin="counterup">
                        78
                      </span>
                      &deg;C
                    </h3>

                    <p class="text-muted mb-1 text-truncate">
                      Temperature in <?php strip_tags($city); ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end row-->
