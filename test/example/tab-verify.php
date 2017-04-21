            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7 col-sm-3 col-md-offset-1 col-sm-offset-2">
                        <?php
                            if (isset($_POST['submitnewpassword']))
                            {
                                if ($_POST['password1'] == $_POST['password2']){
                                    $post_array = array(
                                    	'PassKey' => (empty($_GET['passkey'])?'':$_GET['passkey']),
                                    	'NewPassword' => $_POST['password2']
                                    );
                                    Core::verifyPassKey(Core::$api.'/user/verifypasskey',$post_array);
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Process Change Password Failed,</strong> Your password is not match! 
                                        </div>';
                                }
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title">Change Password</h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label>New Password</label>
                                            <input name="password1" type="password" placeholder="Please input Your Password" class="form-control border-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm New Password</label>
                                            <input name="password2" type="password" placeholder="Please repeat Your Password" class="form-control border-input" required>
                                        </div>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitnewpassword" type="submit" class="btn btn-fill btn-wd ">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>