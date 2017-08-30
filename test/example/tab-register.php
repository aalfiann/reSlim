            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-2">
                        <?php
                            $aaa=rand(0,5);$bbb=rand(3,9);
                            if (isset($_POST['submitregister']))
                            {
                                if (!empty($_POST['agree'])){
                                    if (($_POST['aaa'] + $_POST['bbb']) == $_POST['key']){
                                        if ($_POST['password1'] == $_POST['password2']){
                                            $post_array = array(
                                            	'Username' => $_POST['username'],
                                            	'Password' => $_POST['password2'],
                                                'Fullname' => $_POST['fullname'],
                                                'Address' => $_POST['address'],
                                                'Phone' => $_POST['phone'],
                                                'Email' => $_POST['email'],
                                                'Aboutme' => $_POST['aboutme'],
                                                'Avatar' => $_POST['avatar'],
                                                'Role' => '3'
                                            );
                                            Core::register(Core::getInstance()->api.'/user/register',$post_array);
                                        } else {
                                            echo Core::getMessage('danger',Core::lang('core_register_failed'),Core::lang('not_match_password'));
                                        }
                                    } else {
                                        echo Core::getMessage('danger',Core::lang('core_register_failed'),Core::lang('wrong_scurity_key'));
                                    }
                                } else {
                                    echo Core::getMessage('danger',Core::lang('core_register_failed'),Core::lang('not_agree'));
                                }
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title"><?php echo Core::lang('form_register')?></h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label><?php echo Core::lang('username')?></label>
                                            <input name="username" type="text" placeholder="<?php echo Core::lang('input_username')?>" class="form-control border-input" maxlength="20" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('password')?></label>
                                            <input name="password1" type="password" placeholder="<?php echo Core::lang('input_password')?>" class="form-control border-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('confirm_password')?></label>
                                            <input name="password2" type="password" placeholder="<?php echo Core::lang('input_confirm_password')?>" class="form-control border-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('fullname')?></label>
                                            <input name="fullname" type="text" placeholder="<?php echo Core::lang('input_fullname')?>" class="form-control border-input" maxlength="50" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('address')?></label>
                                            <textarea name="address" rows="3" class="form-control border-input" placeholder="<?php echo Core::lang('input_address')?>" maxlength="255"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('phone')?></label>
                                            <input name="phone" type="text" placeholder="<?php echo Core::lang('input_phone')?>" class="form-control border-input" maxlength="15" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('email_address')?></label>
                                            <input name="email" type="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}$" placeholder="<?php echo Core::lang('input_email')?>" class="form-control border-input" maxlength="50" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('about_me')?></label>
                                            <textarea name="aboutme" rows="5" class="form-control border-input" placeholder="<?php echo Core::lang('input_about_me')?>" maxlength="255"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('avatar')?></label>
                                            <input name="avatar" type="text" placeholder="<?php echo Core::lang('input_avatar')?>" class="form-control border-input">
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('security_key')?> </label>
                                            <b><?php echo $aaa?> + <?php echo $bbb?> = ?</b><input name="key" type="text" placeholder="<?php echo Core::lang('input_security_key')?>" class="form-control border-input" maxlength="15" required>
                                            <input type="text" name="aaa" value="<?php echo $aaa?>" hidden>
            								<input type="text" name="bbb" value="<?php echo $bbb?>" hidden>
                                        </div>
                                        <label class="checkbox" for="checkbox1">
	                                	    <input name="agree" type="checkbox" id="checkbox1" data-toggle="checkbox"><?php echo Core::lang('i_agree')?> <a href="#" data-toggle="modal" data-target="#termsofservice"><?php echo Core::lang('terms')?></a>
	                                	</label>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitregister" type="submit" class="btn btn-fill btn-wd "><?php echo Core::lang('register')?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Start Modal Terms of Service -->
                <div class="modal fade" id="termsofservice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel"><?php echo Core::lang('terms')?></h4>
                            </div>
                            <div class="modal-body">
                            <?php echo Core::lang('modal_terms')?>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Core::lang('close')?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal Terms of Service -->