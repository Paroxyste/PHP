<?php

$msgObj = new Message($con, $userLoggedIn);

?>

<div class="card-box card-details">
    <h4 class="header-title mb-3">
        Messages
    </h4>

    <hr />

    <div class="inbox-widget slimscroll"
         style="max-height: auto;">

        <?php

        if (
            $msgObj->GetConvers() == NULL
        ) {
            echo "
                <div class='alert alert-warning alert-dismissible 
                            fade show' 
                     role='alert'>

                    <button type='button' 
                            class='close' 
                            data-dismiss='alert' 
                            aria-label='Close'>

                        <span aria-hidden='true'>
                            &times;
                        </span>
                    </button>

                    Oh, you haven't start a conversation with your friends.
                </div>

                <div class='text-center'>
                    <a class='btn btn-blue waves-effect waves-light 
                              text-white mt-2'
                       href='messages.php?u=new'>
                        Start a conversation
                    </a>
                </div>
            ";
        } else {
            echo $msgObj->GetConvers();

            echo "
                <div class='text-center mt-1'>
                    <a class='btn btn-blue waves-effect waves-light 
                              text-white mt-2'
                       href='messages.php?u=new'>
                        Start a conversation
                    </a>
                </div>
            ";
        }

        ?>

    </div>
</div>
