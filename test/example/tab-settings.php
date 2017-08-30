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
                                    'Api' => $_POST['api'],
                                    'ApiKey' => $_POST['apikey']
                                );
                                Core::saveSettings($post_array);
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title"><?php echo Core::lang('settings')?> - <?php echo Core::getInstance()->title?></h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label><?php echo Core::lang('title')?></label>
                                            <input name="title" type="text" placeholder="<?php echo Core::lang('input_title_website')?>" class="form-control border-input" maxlength="20" value="<?php echo Core::getInstance()->title?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('email_address')?></label>
                                            <input name="email" type="text" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}$" placeholder="<?php echo Core::lang('input_email')?>" class="form-control border-input" maxlength="50" value="<?php echo Core::getInstance()->email?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('base_path')?></label>
                                            <input name="basepath" type="text" placeholder="<?php echo Core::lang('input_base_path')?>" class="form-control border-input" value="<?php echo Core::getInstance()->basepath?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('url_api')?></label>
                                            <input name="api" type="text" placeholder="<?php echo Core::lang('input_url_api')?>" class="form-control border-input" value="<?php echo Core::getInstance()->api?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Core::lang('api_key')?></label>
                                            <input name="apikey" type="text" placeholder="<?php echo Core::lang('input_api_key')?>" class="form-control border-input" value="<?php echo Core::getInstance()->apikey?>">
                                        </div>
                                        <p class="category"><i class="ti-info-alt"></i> <?php echo Core::lang('no_have_api')?> <a href="modul-data-api.php?m=7&page=1&itemsperpage=10&search="><?php echo Core::lang('here')?></a>.</p>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitsettings" type="submit" class="btn btn-fill btn-wd "><?php echo Core::lang('save')?> <?php echo Core::lang('settings')?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>