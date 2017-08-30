            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-2">
                        <?php
                            if (isset($_POST['submitnewpassword']))
                            {
                                if ($_POST['password1'] == $_POST['password2']){
                                    $post_array = array(
                                    	'PassKey' => (empty($_GET['passkey'])?'':$_GET['passkey']),
                                    	'NewPassword' => $_POST['password2']
                                    );
                                    Core::verifyPassKey(Core::getInstance()->api.'/user/verifypasskey',$post_array);
                                } else {
                                    echo Core::getMessage('danger',Core::lang('core_change_password_failed'),Core::lang('not_match_password'));
                                }
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title"><?php echo Core::lang('change_password')?></h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label><?php echo Core::lang('new_password')?></label>
                                            <input name="password1" type="password" placeholder="<?php echo Core::lang('input_password')?>" class="form-control border-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('confirm_new_password')?></label>
                                            <input name="password2" type="password" placeholder="<?php echo Core::lang('input_confirm_password')?>" class="form-control border-input" required>
                                        </div>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitnewpassword" type="submit" class="btn btn-fill btn-wd "><?php echo Core::lang('submit')?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>