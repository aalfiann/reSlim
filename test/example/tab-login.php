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
                                    Core::login(Core::$api.'/user/login',$post_array);
                                }
                            ?>
                            <form method="post" action="<?php $_SERVER['PHP_SELF']?>">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title">Form Login</h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input name="username" type="username" placeholder="Username Mind Anda" class="form-control border-input text-uppercase" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input name="password" type="password" placeholder="Password Anda" class="form-control border-input" required>
                                        </div>
                                        <label class="checkbox" for="checkbox1">
	                                	<input name="remember" type="checkbox" id="checkbox1" data-toggle="checkbox">
	                                	Selalu ingat Saya
	                            	</label>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitlogin" type="submit" class="btn btn-fill btn-wd ">Login</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>