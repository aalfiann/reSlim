            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7 col-sm-3 col-md-offset-1 col-sm-offset-2">
                        <?php
                            if (isset($_POST['submitsettings']))
                            {
                                $post_array = array(
                                    'Title' => $_POST['title'],
                                    'Email' => $_POST['email'],
                                    'Basepath' => $_POST['basepath'],
                                    'Api' => $_POST['api']
                                );
                                Core::saveSettings($post_array);
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title"><?php echo Core::getInstance()->title?> Settings</h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input name="title" type="text" placeholder="Please input the title of website" class="form-control border-input" maxlength="20" value="<?php echo Core::getInstance()->title?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input name="email" type="text" placeholder="Please input Your Email" class="form-control border-input" maxlength="50" value="<?php echo Core::getInstance()->email?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Base Path</label>
                                            <input name="basepath" type="text" placeholder="Please input url folder of Your website." class="form-control border-input" value="<?php echo Core::getInstance()->basepath?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Url API</label>
                                            <input name="api" type="text" placeholder="Please input url folder of Your Rest API." class="form-control border-input" value="<?php echo Core::getInstance()->api?>" required>
                                        </div>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitsettings" type="submit" class="btn btn-fill btn-wd ">Save Settings</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>