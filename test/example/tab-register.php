            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7 col-sm-3 col-md-offset-1 col-sm-offset-2">
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
                                            Core::register(Core::$api.'/user/register',$post_array);
                                        } else {
                                            echo '<div class="alert alert-danger" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <strong>Process Register Failed,</strong> Your password is not match! 
                                            </div>';
                                        }
                                    } else {
                                        echo '<div class="alert alert-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Process Register Failed,</strong> Wrong security key! 
                                        </div>';
                                    }
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <strong>Process Register Failed,</strong> You are not agree with the terms of service! 
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
                                        <div class="form-group">
                                            <label>Security Key: </label>
                                            <b><?=$aaa?> + <?=$bbb?> = ?</b><input name="key" type="text" placeholder="Please answer this question" class="form-control border-input" maxlength="15" required>
                                            <input type="text" name="aaa" value="<?=$aaa?>" hidden>
            								<input type="text" name="bbb" value="<?=$bbb?>" hidden>
                                        </div>
                                        <label class="checkbox" for="checkbox1">
	                                	    <input name="agree" type="checkbox" id="checkbox1" data-toggle="checkbox">I agree to the <a href="#" data-toggle="modal" data-target="#termsofservice">terms of service</a>
	                                	</label>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitregister" type="submit" class="btn btn-fill btn-wd ">Register</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Start Modal Terms of Service -->
                            <div class="modal fade" id="termsofservice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Terms Of Service</h4>
                                        </div>
                                        <div class="modal-body">
                                        <p>You agree, through your use of this service, that you will not use this
application to post any material which is knowingly false and/or defamatory,
inaccurate, abusive, vulgar, hateful, harassing, obscene, profane, sexually
oriented, threatening, invasive of a person's privacy, or otherwise violative
of any law. You agree not to post any copyrighted material unless the
copyright is owned by you.</p>

<p>We as owner of this application also reserve the right to reveal your identity (or
whatever information we know about you) in the event of a complaint or legal
action arising from any message posted by you. We log all internet protocol
addresses accessing this web site.</p>

<p>Please note that advertisements, chain letters, pyramid schemes, and
solicitations are inappropriate on this application.</p>

<p>We reserve the right to remove any content for any reason or no reason at
all. We reserve the right to terminate any membership for any reason or no
reason at all.</p>

<p>You must be at least 13 years of age to use this service.</p>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal Terms of Service -->
                        </div>
                    </div>
                </div>
            </div>