<?php spl_autoload_register(function ($classname) { require ( $classname . ".php");});?>
            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-md-offset-3 col-sm-offset-4">
                            <?php 
                                if (isset($_POST['submitlogin']))
                                {
                                    $post_array = array(
                                    	'Username' => $_POST['username'],
                                    	'Password' => $_POST['password'],
                                        'Rememberme' => (!empty($_POST['remember'])?$_POST['remember']:'')
                                    );
                                    Core::login(Core::getInstance()->api.'/user/login',$post_array);
                                }
                            ?>
                            <form method="post" action="<?php $_SERVER['PHP_SELF']?>">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title"><?php echo Core::lang('form_login')?></h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label><?php echo Core::lang('username')?></label>
                                            <input name="username" type="text" placeholder="<?php echo Core::lang('input_username')?>" class="form-control border-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('password')?></label>
                                            <input name="password" type="password" placeholder="<?php echo Core::lang('input_password')?>" class="form-control border-input" required>
                                        </div>
                                        <label class="checkbox" for="checkbox1">
	                                	    <input name="remember" type="checkbox" id="checkbox1" data-toggle="checkbox"><?php echo Core::lang('remember_me')?>
	                                	</label>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitlogin" type="submit" class="btn btn-fill btn-wd "><?php echo Core::lang('login')?></button>
                                            <br><br><label><a href="modul-forgot-password.php"><?php echo Core::lang('forgot_password')?></a></label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>