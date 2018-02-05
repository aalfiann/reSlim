<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <?php
                        if (isset($_POST['submitnewpage']))
                        {
                            $post_array = array(
                                'Username' => $datalogin['username'],
                                'Token' => $datalogin['token'],
                                'Title' => $_POST['title'],
                                'Image' => $_POST['image'],
                                'Description' => $_POST['description'],
                                'Content' => $_POST['content'],
                                'Tags' => $_POST['tags']                                
                            );
                            Core::processCreate(Core::getInstance()->api.'/page/data/new',$post_array,Core::lang('page'));
                        } 
                    ?>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title"><?php echo Core::lang('form_editor')?></h4>
                            </div>
                            <div class="content">
                                <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><?php echo Core::lang('image')?></label>
                                                <input type="text" name="image" class="form-control border-input" placeholder="<?php echo Core::lang('input_image_page')?>" maxlength="250" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><?php echo Core::lang('tags')?></label>
                                                <input type="text" name="tags" class="form-control border-input" placeholder="<?php echo Core::lang('input_tags_page')?>" maxlength="200" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><?php echo Core::lang('title')?></label>
                                                <input type="text" name="title" class="form-control border-input" placeholder="<?php echo Core::lang('input_title_page')?>" maxlength="100" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><?php echo Core::lang('description')?></label>
                                                <textarea name="description" rows="2" style="resize: vertical;" class="form-control border-input" placeholder="<?php echo Core::lang('input_description_page')?>" maxlength="200" required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><?php echo Core::lang('content')?></label>
                                                <textarea id="summernote" name="content" rows="5" style="resize: vertical;" class="form-control border-input" placeholder="<?php echo Core::lang('input_content_page')?>" maxlength="10000" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    

                                    <div class="text-center">
                                        <button name="submitnewpage" type="submit" class="btn btn-info btn-fill btn-wd"><?php echo Core::lang('save_page')?></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>

                