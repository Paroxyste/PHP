<?php

require('./controller/form_handlers/upload_handler.php');

?>

<div class="row">
    <div class="col-lg-12 col-xl-12">
        <div class="card-box card-details">
            <form action="upload.php" method="post" 
                  enctype="multipart/form-data">

                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ti-gallery mr-1"></i>
                    Upload something
                </h5>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="dropify-wrapper text-center mb-4">
                                    <div class="dropify-message">

                                        <h4 class="header-title mt-3">
                                            Select the file to be uploaded ...
                                        </h4>
                                    </div>

                                    <input class="mt-3 mb-3" 
                                           type="file" 
                                           id="image"
                                           name="image" />
                                </div>

                                <div class="clearfix text-right">
                                    <a class="btn btn-danger ml-1"
                                       href="index.php">
                                        Cancel
                                    </a>

                                    <button class="btn btn-success" type="submit"
                                            name="upl_submit">
                                        Submit
                                    </button>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php

                // If an image has been uploaded cropping area ...
                if (
                    $imgSrc
                ) {

                ?>

                <hr class="mb-5" />

                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ti-arrows-corner mr-1"></i>
                    Crop your profile picture
                </h5>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <img src="<?php echo strip_tags($imgSrc); ?>" 
                                     id="jcrop_target" class="jcrop-img" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="btn-box">

                                    <form action="upload.php" method="POST">
                                        <button type="submit" name="upl_cancel"
                                                class="btn btn-danger mr-2">
                                            Cancel
                                        </button>
                                    </form>

                                    <form action="upload.php" method="post" 
                                          onsubmit="return checkCoords();">

                                        <input type="hidden" id="x" name="x" />
                                        <input type="hidden" id="y" name="y" />
                                        <input type="hidden" id="w" name="w" />
                                        <input type="hidden" id="h" name="h" />

                                        <input type="hidden" 
                                               value="jpeg" 
                                               name="type" />

                                        <button type="submit" 
                                                class="btn btn-success mr-3">
                                            Save
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } ?>

            </div>
        </div>
    </div>
</div>

<?php

require('./view/footer.php');

?>
