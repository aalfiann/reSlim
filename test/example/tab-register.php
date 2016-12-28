            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7 col-sm-3 col-md-offset-1 col-sm-offset-2">
                        <?php
                            if (isset($_POST['submitregister']))
                            {
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
                                    Core::register(Core::$api.'/user/register',$post_array);
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Process Register Failed,</strong> Your password is not match! 
                                        </div>';
                                }
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title">Form Register</h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input name="username" type="username" placeholder="Please input Your Username" class="form-control border-input" maxlength="20" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input name="password1" type="password" placeholder="Please input Your Password" class="form-control border-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password</label>
                                            <input name="password2" type="password" placeholder="Please repeat Your Password" class="form-control border-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Fullname</label>
                                            <input name="fullname" type="text" placeholder="Please input Your Fullname" class="form-control border-input" maxlength="50" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="address" rows="3" class="form-control border-input" placeholder="Here can be your address ..." maxlength="255"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input name="phone" type="text" placeholder="Please input Your Phone" class="form-control border-input" maxlength="15" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input name="email" type="text" placeholder="Please input Your Email" class="form-control border-input" maxlength="50" required>
                                        </div>
                                        <div class="form-group">
                                            <label>About Me</label>
                                            <textarea name="aboutme" rows="5" class="form-control border-input" placeholder="Here can be your description ..." maxlength="255"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Avatar</label>
                                            <input name="avatar" type="text" placeholder="Please input url image for Your Avatar." class="form-control border-input">
                                        </div>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitregister" type="submit" class="btn btn-fill btn-wd ">Register</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>