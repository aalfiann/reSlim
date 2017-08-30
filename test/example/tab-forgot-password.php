            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-2">
                        <?php
                            if (isset($_POST['submitforgot']))
                            {
                                $post_array = array(
                                    'Email' => $_POST['email']
                                );
                                Core::forgotPassword($post_array);
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title"><?php echo Core::lang('form_reset_password')?></h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label><?php echo Core::lang('email_address')?></label>
                                            <input name="email" type="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}$" placeholder="<?php echo Core::lang('input_email')?>" class="form-control border-input" maxlength="50" required>
                                        </div>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitforgot" type="submit" class="btn btn-fill btn-wd "><?php echo Core::lang('submit_reset_password')?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>