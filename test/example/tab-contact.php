            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                        <?php
                            $aaa=rand(0,5);$bbb=rand(3,9);
                            if (isset($_POST['submitcontact']))
                            {
                                if (($_POST['aaa'] + $_POST['bbb']) == $_POST['key']){
                                    $post_array = array(
                                    	'To' => Core::$email,
                                    	'Subject' => filter_var($_POST['subject'],FILTER_SANITIZE_STRING),
                                        'Message' => $_POST['message'],
                                        'From' => filter_var($_POST['email'],FILTER_SANITIZE_EMAIL),
                                        'FromName' => filter_var($_POST['name'],FILTER_SANITIZE_STRING),
                                        'Html' => true,
                                        'CC' => '',
                                        'BCC' => '',
                                        'Attachment' => ''
                                    );
                                    Core::sendMail(Core::$api.'/mail/send',$post_array);
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Process Send Message Failed,</strong> Wrong security key! 
                                        </div>';
                                }
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title">Form Contact Us</h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input name="name" type="text" placeholder="Please input Your Name" class="form-control border-input" maxlength="50" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email Address</label>
                                            <input name="email" type="text" placeholder="Please input Your Email Address" class="form-control border-input" maxlength="50" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Subject</label>
                                            <input name="subject" type="text" placeholder="Please input the subject" class="form-control border-input" maxlength="50" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea name="message" rows="3" placeholder="Please input the message here..." class="form-control border-input" maxlength="255" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Security Key: </label>
                                            <b><?=$aaa?> + <?=$bbb?> = ?</b><input name="key" type="text" placeholder="Please answer this question" class="form-control border-input" maxlength="15" required>
                                            <input type="text" name="aaa" value="<?=$aaa?>" hidden>
            								<input type="text" name="bbb" value="<?=$bbb?>" hidden>
                                        </div>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitcontact" type="submit" class="btn btn-fill btn-wd ">Send Message</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>