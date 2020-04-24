<?php

require('./controller/form_handlers/posts_handler.php');

$loggedInUserObj = new User($con, $userLoggedIn);

if (
    $loggedInUserObj->isFriend($username)
 ) {
    echo "
        <form method='POST' action='". strip_tags($username) ."'
              enctype='multipart/form-data'
              class='comment-area-box mt-2 mb-3'>
    
            <span class='input-icon'>
                <textarea rows='4' class='form-control' name='post_text'
                          placeholder='Write something ...'></textarea>
            </span>

            <input type='hidden' name='posted_by'
                   value='". strip_tags($userLoggedIn) ."' />

            <input type='hidden' name='posted_to'
                   value='". strip_tags($username) ."' />

            <div class='comment-area-btn'>
                <div class='float-right'>
                    <button type='submit' name='post_btn' id='post_btn'
                            class='btn btn-sm btn-dark waves-effect
                                       waves-light post-btn'>
                        Post
                    </button>
                </div>

                <div>
                    <input type='file' name='fileToUpload'
                           class='btn btn-sm btn-light file-btn' />
                </div>
            </div>
        </form>

        <div class='posts_area'></div>

        <img id='loading' src='./view/images/loading.gif'
             alt='loader' class='loader' />
    ";
} else {
    echo "
        <div class='card'>
            <div class='card-body p-4'>
                <div class='error-ghost text-center'>
                    <svg class='ghost' version='1.1' id='Layer_1'
                         xmlns='http://www.w3.org/2000/svg'
                         xmlns:xlink='http://www.w3.org/1999/xlink'
                         x='0px' y='0px' width='127.433px'
                         height='132.743px'
                         viewBox='0 0 127.433 132.743'
                         enable-background='new 0 0 127.433 132.743'
                         xml:space='preserve'>

                        <path fill='#f79fac'
                              d='M116.223,125.064c1.032-1.183,
                                 1.323-2.73,1.391-3.747V54.76c0,
                                 0-4.625-34.875-36.125-44.375 s-66,
                                 6.625-72.125,44l-0.781,63.219c0.062,
                                 4.197,1.105,6.177,1.808,7.006c1.94,1.811,
                                 5.408,3.465,10.099-0.6c7.5-6.5,8.375-10,
                                 12.75-6.875s5.875,9.75,13.625,9.25s12.75-9,
                                 13.75-9.625s4.375-1.875,7,1.25s5.375,8.25,
                                 12.875,7.875s12.625-8.375,
                                 12.625-8.375s2.25-3.875,7.25,
                                 0.375s7.625,9.75,14.375,8.125C114.739,126.01,
                                 115.412,125.902,116.223,125.064z' />

                        <circle fill='#013E51' cx='86.238' cy='57.885'
                                r='6.667' />
                                
                        <circle fill='#013E51' cx='40.072' cy='57.885'
                                r='6.667' />

                        <circle fill='#FCEFED' stroke='#FEEBE6'
                                stroke-miterlimit='10' cx='18.614'
                                cy='99.426' r='3.292' />

                        <circle fill='#FCEFED' stroke='#FEEBE6'
                                stroke-miterlimit='10' cx='95.364'
                                cy='28.676' r='3.291' />

                        <circle fill='#FCEFED' stroke='#FEEBE6'
                                stroke-miterlimit='10' cx='24.739'
                                cy='93.551' r='2.667' />

                        <circle fill='#FCEFED' stroke='#FEEBE6'
                                stroke-miterlimit='10' cx='101.489'
                                cy='33.051' r='2.666' />

                        <circle fill='#FCEFED' stroke='#FEEBE6'
                                stroke-miterlimit='10' cx='18.738'
                                cy='87.717' r='2.833' />

                        <path fill='#FCEFED' stroke='#FEEBE6'
                              stroke-miterlimit='10'
                              d='M116.279,55.814c-0.021-0.286-2.323-28.744-
                                 30.221-41.012c-7.806-3.433-15.777-5.173-
                                 23.691-5.173c-16.889,0-30.283,7.783-37.187,
                                 15.067c-9.229,9.736-13.84,26.712-14.191,
                                 30.259l-0.748,62.332c0.149,2.133,1.389,6.167,
                                 5.019,6.167c1.891,0,4.074-1.083,
                                 6.672-3.311c4.96-4.251,7.424-6.295,9.226-
                                 6.295c1.339,0,2.712,1.213,5.102,3.762c4.121,
                                 4.396,7.461,6.355,10.833,6.355c2.713,
                                 0,5.311-1.296,7.942-3.962c3.104-3.145,
                                 5.701-5.239,8.285-5.239c2.116,0,4.441,1.421,
                                 7.317,4.473c2.638,2.8,5.674,4.219,9.022,
                                 4.219c4.835,0,8.991-2.959,11.27-5.728l0.086-
                                 0.104c1.809-2.2,3.237-3.938,5.312-3.938c2.208,
                                 0,5.271,1.942,9.359,5.936c0.54,0.743,3.552,
                                 4.674,6.86,4.674c1.37,0,2.559-0.65,
                                 3.531-1.932l0.203-0.268L116.279,
                                 55.814z M114.281,121.405c-0.526,0.599-1.096,
                                 0.891-1.734,0.891c-2.053,0-4.51-2.82-5.283-
                                 3.907l-0.116-0.136c-4.638-4.541-7.975-6.566-
                                 10.82-6.566c-3.021,0-4.884,2.267-6.857,
                                 4.667l-0.086,0.104c-1.896,2.307-5.582,
                                 4.999-9.725,4.999c-2.775,0-5.322-1.208-
                                 7.567-3.59c-3.325-3.528-6.03-5.102-8.772-
                                 5.102c-3.278,0-6.251,2.332-9.708,5.835c-
                                 2.236,2.265-4.368,3.366-6.518,3.366c-2.772,
                                 0-5.664-1.765-9.374-5.723c-2.488-2.654-4.29-4.
                                 395-6.561-4.395c-2.515,0-5.045,2.077-10.527,
                                 6.777c-2.727,2.337-4.426,2.828-5.37,
                                 2.828c-2.662,0-3.017-4.225-3.021-4.225l0.745-
                                 62.163c0.332-3.321,4.767-19.625,13.647-
                                 28.995c3.893-4.106,10.387-8.632,
                                 18.602-11.504c-0.458,0.503-0.744,1.165-0.744,
                                 1.898c0,1.565,1.269,2.833,2.833,2.833c1.564,0,
                                 2.833-1.269,2.833-2.833c0-1.355-0.954-2.485-
                                 2.226-2.764c4.419-1.285,9.269-2.074,14.437-
                                 2.074c7.636,0,15.336,1.684,22.887,
                                 5.004c26.766,11.771,29.011,39.047,29.027,
                                 39.251V121.405z' />
                    </svg>
                </div>

                <div class='text-center'>
                    <h3 class='mt-4 mb-4 text-uppercase'>
                        <i class='ti-lock mr-1'></i>
                            Privacy Protection
                    </h3>

                    <p class='text-muted mb-0'>
                        This user's content is protected. If you wish to 
                        view or communicate with this user, please add this 
                        person to your friends.
                    </p>
                </div>
            </div>
        </div>

        <div class='row mt-3'>
            <div class='col-12 text-center'>
                <p class=''>
                    Return to

                    <a class='ml-1' href='". strip_tags($userLoggedIn) ."'>
                        <b>My Profile</b>
                    </a>
                </p>
            </div>
        </div>
    ";
}

?>

<script>
(function($, window, document) {

    $(function() {

        let userLoggedIn    = '<?php echo strip_tags($userLoggedIn); ?>';
        let profileUsername = '<?php echo strip_tags($username); ?>';
        let inProgress      = false;

        loadPosts();

        function loadPosts() {
            if (inProgress) {
                return;
            };

            inProgress = true;
            $('#loading').show();

            let page = $('.posts_area').find('.nextPage').val() || 1;

            $.ajax({
                url:  'controller/handlers/ajax_load_posts.php',
                type: 'POST',
                data: 'page=' + page + '&userLoggedIn=' + userLoggedIn +
                      '&profileUsername=' + profileUsername,
                cache: false,

                success: function(response) {
                    $('.posts_area').find('.nextPage').remove();
                    $('.posts_area').find('.noMorePosts').remove();
                    $('#loading').hide();
                    $('.posts_area').append(response);

                    inProgress = false;
                }
            });
        };
    });

}(window.jQuery, window, document));
</script>