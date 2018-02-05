<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <?php
                        //Validation url param
                        $search = filter_var((empty($_GET['search'])?'':$_GET['search']),FILTER_SANITIZE_STRING);
                        $page = filter_var((empty($_GET['page'])?'1':$_GET['page']),FILTER_SANITIZE_STRING);
                        $itemsperpage = filter_var((empty($_GET['itemsperpage'])?'10':$_GET['itemsperpage']),FILTER_SANITIZE_STRING);
                    
                        if (isset($_POST['submitupdate']))
                        {
                            $post_array = array(
                            	'Username' => $datalogin['username'],
                                'Token' => $datalogin['token'],
                                'Title' => $_POST['title'],
                                'Image' => $_POST['image'],
                                'Description' => $_POST['description'],
                                'Content' => $_POST['content'],
                                'Tags' => $_POST['tags'],
                                'StatusID' => $_POST['status'],
                                'PageID' => $_POST['pageid']
                            );
                            Core::processUpdate(Core::getInstance()->api.'/page/data/update',$post_array,Core::lang('page'));
                        }
                    
                        if (isset($_POST['submitdelete']))
                        {
                            $post_array = array(
                            	'Username' => $datalogin['username'],
                                'Token' => $datalogin['token'],
                                'PageID' => $_POST['pageid']
                            );
                            Core::processDelete(Core::getInstance()->api.'/page/data/delete',$post_array,Core::lang('page'));
                            echo Core::redirectPage('modul-data-page.php?m=8&page='.$page.'&itemsperpage='.$itemsperpage.'&search='.rawurlencode($search),2);
                        }
                    ?>
<?php 
    // Data Page
    $pageid = filter_var((empty($_GET['pageid'])?'':$_GET['pageid']),FILTER_SANITIZE_STRING);
    $url = Core::getInstance()->api.'/page/data/read/'.$pageid.'/'.$datalogin['username'].'/'.$datalogin['token'];
    $data = json_decode(Core::execGetRequest($url));

    // Data Status
    $urlstatus = Core::getInstance()->api.'/page/data/status/'.$datalogin['token'];
    $datastatus = json_decode(Core::execGetRequest($urlstatus));

    if (!empty($data))
        {
            if ($data->{'status'} == "success")
            {
                    echo '
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">'.Core::lang('form_editor').'</h4>
                            </div>
                            <div class="content">
                                <form action="'.$_SERVER['PHP_SELF'].'?m=8&do=update&page='.$page.'&itemsperpage='.$itemsperpage.'&search='.$search.'" method="post">                             
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>'.Core::lang('page_id').'</label>
                                                <input type="text" name="pageid" class="form-control border-input" value="'.$data->result[0]->{'PageID'}.'" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>'.Core::lang('status').'</label>
                                                    <select name="status" style="max-height:200px; overflow-y:scroll; overflow-x:hidden;" class="form-control border-input" required>';
                                                        if (!empty($datastatus)) {
                                                            foreach ($datastatus->results as $name => $value) {
                                                                echo '<option value="'.$value->{'StatusID'}.'" '.(($value->{'Status'} == $data->result[0]->{'Status'})?'selected':'').'>'.$value->{'Status'}.'</option>';
                                                            }
                                                        }
                                                    echo '</select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>'.Core::lang('image').'</label>
                                                <input type="text" name="image" class="form-control border-input" placeholder="'.Core::lang('input_image_page').'" maxlength="250" value="'.$data->result[0]->{'Image'}.'" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>'.Core::lang('tags').'</label>
                                                <input type="text" name="tags" class="form-control border-input" placeholder="'.Core::lang('input_tags_page').'" maxlength="200" value="'.$data->result[0]->{'Tags_inline'}.'" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>'.Core::lang('title').'</label>
                                                <input type="text" name="title" class="form-control border-input" placeholder="'.Core::lang('input_title_page').'" maxlength="100" value="'.$data->result[0]->{'Title'}.'" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>'.Core::lang('description').'</label>
                                                <textarea name="description" rows="2" style="resize: vertical;" class="form-control border-input" placeholder="'.Core::lang('input_description_page').'" maxlength="200" required>'.$data->result[0]->{'Description'}.'</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>'.Core::lang('content').'</label>
                                                <textarea id="summernote" name="content" rows="5" style="resize: vertical;" class="form-control border-input" placeholder="'.Core::lang('input_content_page').'" maxlength="10000" required>'.$data->result[0]->{'Content'}.'</textarea>
                                            </div>
                                        </div>
                                    </div>
        
                                    <button name="submitupdate" type="submit" class="btn btn-info btn-fill btn-wd pull-right">'.Core::lang('update_page').'</button>
                                    <button name="submitdelete" type="submit" class="btn btn-danger btn-fill btn-wd pull-left">'.Core::lang('delete').'</button>
                                    
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>';
            }
            else
            {
                echo '<div class="col-md-12">
                        <div class="card card-plain">
                            <div class="header">
                                <h4 class="title">'.Core::lang('message').': '.$data->{'message'}.'</h4>
                            </div>
                        </div>
                    </div>';
            } 
        }
?>
                
                </div>
            </div>
        </div>

                