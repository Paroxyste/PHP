<?php

declare(strict_types=1);

class Post
{
    private $con;
    private $userObj;

    // ------------------------------------------------------------ constructor

    public function __construct($con, $user)
    {

        $this->con = $con;
        $this->userObj = new User($con, $user);

    }

    // -------------------------------------------------------------- LoadPosts

    public function LoadPosts($data, $limit)
    {

        $page = $data['page'];

        $profileUser = $data['profileUsername'];

        $userLoggedIn = $this->userObj->GetUsername();

        if (
            $page == 1
        ) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $str = NULL;

        $status   = 'no';
        $userNone = 'none';

        $loadPostsQuery = "SELECT id, post_body, posted_by, date_added, image
                           FROM posts
                           WHERE (removed='$status' 
                           AND posted_by='$profileUser' 
                           AND posted_to='$userNone')
                           OR (removed='$status' 
                           AND posted_to='$profileUser')
                           ORDER BY id
                           DESC";

        $loadPosts = $this->con->query($loadPostsQuery);

        if (
            $loadPosts->num_rows > 0
        ) {
            $numIterations = 0;
            $count = 1;

            while ($row = $loadPosts->fetch_assoc()) {
                $id        = $row['id'];
                $postBody  = $row['post_body'];
                $postedBy  = $row['posted_by'];
                $dateTime  = $row['date_added'];
                $imagePath = $row['image'];

                if (
                    $numIterations++ < $start
                ) {
                    continue;
                }

                // Once $limit posts have been loaded, break
                if (
                    $count > $limit
                ) {
                    break;
                } else {
                    $count++;
                }

                if (
                    $userLoggedIn == $postedBy
                ) {
                    $deleteBtn = "
                        <button id='post". strip_tags($id) ."'
                                class='btn btn-sm btn-link text-danger btn-del'
                                type='button' data-toggle='modal'
                                data-target='.deletePostModal' >

                            <i class='ti-trash mr-1'></i>
                            Delete
                        </button>
                    ";
                } else {
                    $deleteBtn = NULL;
                }

                $userDataQuery = "SELECT first_name, last_name, profile_pic
                                  FROM users
                                  WHERE (username='$postedBy')";

                $userData = $this->con->query($userDataQuery);

                $userRow = $userData->fetch_assoc();

                $firstName  = $userRow['first_name'];
                $lastName   = $userRow['last_name'];
                $profilePic = $userRow['profile_pic'];

                ?>

                <script>
                function toggle<?php echo strip_tags($id); ?>() {

                    let element = 'toggleComment<?php echo strip_tags($id); ?>'
                    let eleID = document.getElementById(element);

                    if (eleID.style.display == 'block') 
                        eleID.style.display = 'none';
                    else
                        eleID.style.display= 'block';
                };
                </script>

                <?php

                $getCommentsQuery = "SELECT *
                                     FROM comments
                                     WHERE (post_id='$id')";

                $getComments = $this->con->query($getCommentsQuery);

                $getCommentsNum = $getComments->num_rows;

                // Include Timeframe
                include('../../controller/handlers/timeframe.php');

                if (
                    $imagePath != NULL
                ) {
                    $imageDiv = "
                        <img src='". strip_tags($imagePath) ."'
                             class='rounded shadow-lg mr-1'
                             height = '120'
                             alt= 'post-img' />
                    ";
                } else {
                    $imageDiv = NULL;
                }

                // Style of the posts shown in the profile
                $str .= "
                    <div class='status_post'>
                        <div class='border border-light p-2 mb-3'>
                            <div class='media'>
                                <img src='". strip_tags($profilePic) ."'
                                     class='mr-2 avatar-sm rounded-circle'
                                     alt='Generic placeholder image' />

                                <div class='media-body'>
                                    <h5 class='m-0'>
                                        <a href='". strip_tags($postedBy) ."'>"
                                            . strip_tags($firstName) .
                                            ' ' . strip_tags($lastName) .
                                        "</a>
                                    </h5>

                                    <p class='text-muted'>
                                        <small>"
                                            . strip_tags($timeMsg) .
                                        "</small>
                                    </p>
                                </div>
                            </div>

                            <p>
                                $postBody
                            </p>

                            $imageDiv

                            <div id='like' class='mb-2'>
                                <button onClick='javascript:toggle"
                                                 . strip_tags($id) . "()'
                                        class='btn btn-sm btn-link text-muted 
                                               btn-reply mr-1'>

                                    <i class='ti-share-alt mr-1'></i>
                                    Reply ($getCommentsNum)
                                </button>

                                <iframe class='mt-2'
                                        src='like.php?post_id="
                                             . strip_tags($id) ."'>
                                </iframe>
                                
                                $deleteBtn

                            </div>

                            <div id='toggleComment". strip_tags($id) ."'
                                 style='display:none;'>
                                <iframe src='comments.php?post_id="
                                             . strip_tags($id) ."'
                                        id='comment_iframe'
                                        frameborder='0'
                                        style='width: 100%;'>
                                </iframe>
                            </div>
                        </div>
                    </div>
                ";

                ?>

                <script>
                (function($, window, document) {

                  $(function() {

                    let confirmMsg = 'Are you sure you want to delete this post ?';

                    $('#post<?php echo strip_tags($id); ?>').on('click', function() {

                      bootbox.confirm(confirmMsg, function(result) {

                        let path   = '../../controller/form_handlers/';
                        let file   = 'delete_post_handler.php';
                        let postId = '?post_id=<?php echo strip_tags($id); ?>'

                        let link   = path + file + postId;

                        $.post(link, { result:result } );

                        if (result) {
                          setTimeout(function() { location.reload(); }, 300);
                        }

                      });

                    });

                  });

                }(window.jQuery, window, document));
                </script>

                <?php

                } // End while($row = $loadPosts->fetch_assoc())

                if (
                    $count > $limit
                ) {
                    $str .= "
                        <input type='hidden' class='nextPage'
                               value='" . ($page + 1) . "' />

                        <input type='hidden' class='noMorePosts'
                               value='FALSE' />
                    ";
                } else {
                    $str .= "
                        <input type='hidden' class='noMorePosts'
                               value='TRUE' />

                        <div class='text-center'>
                            <span class='text-danger'>
                                <i class='ti-na mr-1'></i>
                                No more posts to show ...
                            </span>
                        </div>
                    ";
                }
            } // End if($loadPosts->num_rows > 0)

            echo $str;

    }

    // ------------------------------------------------------------- SubmitPost

    public function SubmitPost($postBody, $postedTo, $imageName)
    {

        $postBody = strip_tags($postBody);
        $postBody = str_replace('\r\n', '\n', $postBody);
        $postBody = nl2br($postBody);

        $checkEmpty = preg_replace('/\s+/', '', $postBody);

        if (
            $checkEmpty != NULL
        ) {
            $postBodyArray = preg_split('/\s+/', $postBody);

            foreach ($postBodyArray as $key => $value) {
                // Youtube classic link
                if (
                    strpos($value, 'www.youtube.com/watch?v=') !== FALSE
                ) {
                    $link = preg_split('!&!', $value);

                    $value = preg_replace('!watch\?v=!', 'embed/', $link[0]);
                    $value = "<iframe src=". $value ."></iframe>";

                    $postBodyArray[$key] = $value;

                }

                // Youtube short link
                elseif (
                    strpos($value, 'youtu.be/') !== FALSE
                ) {
                    $link    = preg_split('!\.be/!', $value);

                    $pattern = 'https://www.youtube.com/embed/';

                    $value = "<iframe src=". $pattern . $link[1] ."></iframe>";

                    $postBodyArray[$key] = $value;
                }
            } // End foreach

            $postBody  = implode(' ', $postBodyArray);
            $dateAdded = date('Y-m-d H:i:s');
            $postedBy  = $this->userObj->GetUsername();
            $likes     = '0';
            $removed   = $userClosed = 'no';

            // Escape string and insert posts
            $postBody   = $this->con->real_escape_string($postBody);
            $postedBy   = $this->con->real_escape_string($postedBy);
            $postedTo   = $this->con->real_escape_string($postedTo);
            $dateAdded  = $this->con->real_escape_string($dateAdded);
            $imageName  = $this->con->real_escape_string($imageName);
            $likes      = $this->con->real_escape_string($likes);
            $removed    = $this->con->real_escape_string($removed);
            $imageName  = $this->con->real_escape_string($imageName);
            $userClosed = $this->con->real_escape_string($userClosed);

            $insPostQuery = "INSERT INTO posts
                             VALUES (0, '$postBody', '$postedBy',
                                     '$postedTo', '$dateAdded',
                                     '$imageName', '$likes', '$removed',
                                     '$userClosed')";

            $insPost = $this->con->query($insPostQuery);

            $returnedId = $this->con->insert_id;

            // Escape string and update post count for user
            $numPosts = $this->userObj->GetNumPosts();
            $numPosts++;

            $updPostCounterQuery = "UPDATE users
                                    SET num_posts='$numPosts'
                                    WHERE (username='$postedBy')";

            $updPostCounter = $this->con->query($updPostCounterQuery);

        } // End if($checkEmpty != '')

    }

} // End Post class

?>
