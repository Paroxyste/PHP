<?php

require('./controller/form_handlers/messages_handler.php');

?>

<div class="row">
    <div class='col-lg-9 col-xl-9'>
        <div class="tab-content">
            <div class="card-box tchat-box">

                <?php
                
                // Start new conversation
                if (
                    $userTo == 'new'
                ) {
                    echo "
                        <h4 class='header-title mb-3'>
                            Enter your friend's name :
                        </h4>

                        <div class='message_post'>
                            <div class='input-group m-t-10'>";

                            ?>

                                <input class='form-control' 
                                       placeholder='Albert Einstein' 
                                       type='text'
                                       name='query'
                                       id='search_text_input'
                                       autocomplete='off'
                                       onkeyup='getTchatFriend(
                                            this.value, 
                                            "<?php echo strip_tags($userLoggedIn); ?>")' 
                                />
                                
                            <?php

                            echo "

                                </div>

                            <div class='results mt-1 ml-2 mr-2'></div>
                        </div>
                    ";
                }

                // Load a current conversation
                if (
                    $userTo != "new"
                ) {
                    echo "
                        <div class='message_post'>
                            <form action='' method='POST' >
                                <h4 class='header-title mb-3'>
                                    You're talking with 

                                    <a href='$userTo'>"
                                        . $userToObj->GetFullName() . 
                                    "</a>
                                </h4>

                                <hr class='mb-4'/>

                                <div class='chat-conversation'>
                                    <ul class='conversation-list slimscroll' 
                                        style='max-height: 38vh;'>
                                        <div class='loaded_messages' 
                                            id='scroll_messages'>"
                                        . $msgObj->GetMessages($userTo) . "
                                        </div>
                                    </ul>

                                    <div class='row'>
                                        <div class='col'>
                                            <input type='text' 
                                                class='form-control 
                                                       chat-input' 
                                                placeholder='Enter your text'
                                                name='msg_body'
                                            />
                                        </div>

                                        <div class='col-auto'>
                                            <button type='submit' 
                                                    name='post_msg'
                                                    class='btn btn-blue 
                                                           chat-send btn-block 
                                                           waves-effect 
                                                           waves-light'>
                                                Send
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    ";
                }

                // Redirect if userTo = NULL
                if (
                    $userTo == NULL
                ) {
                    echo "
                        <script>
                            location.href='" . $userLoggedIn . "';
                        </script>
                    ";
                }

                ?>

            </div>
        </div>
    </div>


    <div class="col-lg-3 col-xl-3">

        <?php

        include('./view/dashboard/message_box.php');

        ?>

    </div>
</div>

<script>
    let div = document.getElementById('scroll_messages');
    div.scrollTop = div.scrollHeight;
</script>