<?php 
//Validation url param
$search = filter_var((empty($_GET['search'])?'':$_GET['search']),FILTER_SANITIZE_STRING);
$page = filter_var((empty($_GET['page'])?'1':$_GET['page']),FILTER_SANITIZE_STRING);
$itemsperpage = filter_var((empty($_GET['itemsperpage'])?'12':$_GET['itemsperpage']),FILTER_SANITIZE_STRING);
?>
<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <form method="get" action="<?php $_SERVER['PHP_SELF'].'?search='.filter_var($search,FILTER_SANITIZE_STRING)?>">
                        <div class="col-lg-10 col-md-9 col-sm-9 col-xs-12">
                            <div class="form-group">
                                <input name="search" type="text" placeholder="Search here..." class="form-control border-input" value="<?php echo $search?>">
                            </div>
                            <div class="form-group hidden">
                                <input name="m" type="text" class="form-control border-input" value="6" hidden>
                                <input name="page" type="text" class="form-control border-input" value="1" hidden>
                                <input name="itemsperpage" type="text" class="form-control border-input" value="10" hidden>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-1 col-xs-2">
                            <div class="form-group">
                                <button name="submitsearch" type="submit" class="btn btn-fill btn-wd ">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><hr>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group text-center">
                            <?php 
                                if (isset($_POST['submitupload']))
                                {
                                    $cfile = new CURLFile(realpath($_FILES['uploadfile']['tmp_name']),$_FILES['uploadfile']['type'],$_FILES['uploadfile']['name']);
                                    $post_array = array(
                                    	'Username' => $datalogin['username'],
                                    	'Token' => $datalogin['token'],
                                        'Datafile' => $cfile,
                                        'Title' => filter_var($_POST['title'],FILTER_SANITIZE_STRING),
                                        'Alternate' => filter_var($_POST['alternate'],FILTER_SANITIZE_STRING),
                                        'External' => filter_var($_POST['external'],FILTER_SANITIZE_URL)
                                    );
                                    Core::uploadFile(Core::getInstance()->api.'/user/upload',$post_array);
                                }
                            ?>
                            <button name="submitupload" type="submit" class="btn btn-wd" data-toggle="modal" data-target="#myModal"><i class="ti-cloud-up"></i> Upload files here...</button>
                        </div>
                    </div>
                </div><hr>
                <!-- Start Modal -->
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Upload file to server</h4>
                              </div>
                              <form method="post" action="<?php $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
                              <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input name="title" type="text" placeholder="Title of your file ..." class="form-control border-input" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Alternate</label>
                                            <input name="alternate" type="text" placeholder="Alternate of your file ..." class="form-control border-input">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>External Link</label>
                                            <input name="external" type="text" placeholder="External Link" class="form-control border-input">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Browse File</label>
                                            <input name="uploadfile" type="file" placeholder="Choose File" class="form-control border-input" required>
                                        </div>
                                    </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submitupload" class="btn btn-primary">Upload Now</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <!-- End Modal -->
                <div class="row">
<?php 
    $url = Core::getInstance()->api.'/user/'.$datalogin['username'].'/upload/data/search/'.$page.'/'.$itemsperpage.'/'.$datalogin['token'].'/?query='.rawurlencode($search);
    $data = json_decode(Core::execGetRequest($url));

    // Data Status
    $urlstatus = Core::getInstance()->api.'/user/status/'.$datalogin['token'];
    $datastatus = json_decode(Core::execGetRequest($urlstatus));

    if (!empty($data))
        {
            if ($data->{'status'} == "success")
            {
                foreach ($data->results as $row => $value) {
                    if (isset($_POST['submitupdate'.$value->{'ItemID'}]))
                    {
                        $post_array = array(
                            'Username' => $datalogin['username'],
                            'Token' => $datalogin['token'],
                            'ItemID' => $_POST['itemid'],
                            'Title' => filter_var($_POST['title'],FILTER_SANITIZE_STRING),
                            'Alternate' => filter_var($_POST['alternate'],FILTER_SANITIZE_STRING),
                            'External' => filter_var($_POST['externallink'],FILTER_SANITIZE_URL),
                            'Status' => $_POST['status']
                        );
                        Core::updateFile(Core::getInstance()->api.'/user/upload/update',$post_array);
                        echo Core::reloadPage();
                    }
                }

                foreach ($data->results as $row => $value) {
                    if (isset($_POST['submitdelete'.$value->{'ItemID'}]))
                    {
                        $post_array = array(
                            'Username' => $datalogin['username'],
                            'Token' => $datalogin['token'],
                            'ItemID' => $_POST['itemid']
                        );
                        Core::deleteFile(Core::getInstance()->api.'/user/upload/delete',$post_array);
                        echo Core::reloadPage();
                    }
                }

                $i=1;
                foreach ($data->results as $name => $value) 
	            {
                    echo '<!-- Start Data Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="icon-big icon-warning text-center">';
                                        if ($value->{'Filetype'} == 'image/png' || $value->{'Filetype'} == 'image/apng' || $value->{'Filetype'} == 'image/bmp' || $value->{'Filetype'} == 'image/jpg' || $value->{'Filetype'} == 'image/jpeg' || $value->{'Filetype'} == 'image/gif') {
                                            echo '<a href="" data-toggle="modal" data-target="#'.$value->{'ItemID'}.'"><img class="lazyload" data-src="'.Core::getInstance()->api.'/'.$value->{'Filepath'}.'" height="64" width="100%"></a>';
                                        } else {
                                            echo '<a href="" data-toggle="modal" data-target="#'.$value->{'ItemID'}.'"><i class="ti-file"></i></a>';
                                        }
                                        echo '</div>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="numbers">
                                            <p>
                                            '.(empty($value->{'Title'})?$value->{'Filename'}:$value->{'Title'}).'</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer">
                                    <hr />
                                    <div class="stats">
                                        <i class="ti-user"></i> '.$value->{'Upload_by'}.' | '.$value->{'Filetype'}.' | <a href="#" data-toggle="modal" data-target="#'.$value->{'ItemID'}.'">show details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Start Modal -->
                        <div class="modal fade" id="'.$value->{'ItemID'}.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Detail File</h4>
                              </div>
                              <form method="post" action="'.$_SERVER['PHP_SELF'].'?m=6&page='.$page.'&itemsperpage='.$itemsperpage.'&search='.$search.'">
                              <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">';
                                        if ($value->{'Filetype'} == 'image/png' || $value->{'Filetype'} == 'image/apng' || $value->{'Filetype'} == 'image/bmp' || $value->{'Filetype'} == 'image/jpg' || $value->{'Filetype'} == 'image/jpeg' || $value->{'Filetype'} == 'image/gif') {
                                            echo '<a href="#"><img class="lazyload" data-src="'.Core::getInstance()->api.'/'.$value->{'Filepath'}.'" width="100%"></a>';
                                        } 
                                        echo '</div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Item ID</label>
                                            <input name="itemid" type="text" placeholder="Item ID of your file" class="form-control border-input" value="'.$value->{'ItemID'}.'" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Date Uploaded</label>
                                            <input name="date" type="text" placeholder="Date uploaded" class="form-control border-input" value="'.$value->{'Date_Upload'}.'" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Uploaded By</label>
                                            <input name="uploadedby" type="text" placeholder="Uploaded by" class="form-control border-input" value="'.$value->{'Upload_by'}.'" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>File Type</label>
                                            <input name="filetype" type="text" placeholder="Type of your file" class="form-control border-input" value="'.$value->{'Filetype'}.'" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Direct Link</label>
                                            <textarea name="link" type="text" rows="3" placeholder="Direct Link of your file" class="form-control border-input" readonly>'.Core::getInstance()->api.'/'.$value->{'Filepath'}.'</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input name="title" type="text" placeholder="Title of your file ..." class="form-control border-input" value="'.$value->{'Title'}.'" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Alternate</label>
                                            <input name="alternate" type="text" placeholder="Alternate of your file ..." class="form-control border-input" value="'.$value->{'Alternate'}.'">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>External Link</label>
                                            <input name="externallink" type="text" placeholder="External Link" class="form-control border-input" value="'.$value->{'External_link'}.'">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" type="text" style=\'max-height:200px; overflow-y:scroll; overflow-x:hidden;\' class="form-control border-input">';
                                                if (!empty($datastatus)) {
                                                            foreach ($datastatus->result as $name => $valuestatus) {
                                                                echo '<option value="'.$valuestatus->{'StatusID'}.'" '.(($valuestatus->{'StatusID'} == $value->{'StatusID'})?'selected':'').'>'.$valuestatus->{'Status'}.'</option>';
                                                            }
                                                        }
                                                    echo '</select>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                              </div>
                              <div class="modal-footer">';
                                if ($value->{'Upload_by'} == $datalogin['username']){
                                    echo '<button name="submitdelete'.$value->{'ItemID'}.'" type="submit" class="btn btn-danger pull-left">Delete</button>';
                                }

                                echo '<button type="button" class="btn btn-default text-right" data-dismiss="modal">Cancel</button>';
                                if ($value->{'Upload_by'} == $datalogin['username']){
                                    echo '<button name="submitupdate'.$value->{'ItemID'}.'" type="submit" class="btn btn-success text-right">Update</button>';
                                }

                                echo '                                                            
                              </div>
                            </div>
                            </form>
                          </div>
                        </div>
                        <!-- End Modal -->
                    </div>
                    <!-- End Data Card -->';
                    if ($i%4==0){
						echo '<div class="clearfix visible-lg-block"></div>';
					}
					if ($i%2==0){
						echo '<div class="clearfix visible-md-block"></div>';
					}
					$i++;
                }
                echo '
                    <div class="col-lg-12">';
                    
                    $pagination = new Pagination;
                    echo $pagination->makePagination($data,$_SERVER['PHP_SELF'].'?m=6&search='.rawurlencode($search));
                    
                    echo '</div>
                ';
            } else {
                echo '<div class="col-md-12">
                        <div class="card card-plain">
                            <div class="header">
                                <h4 class="title">Message: '.$data->{'message'}.'</h4>
                            </div>
                        </div>
                    </div>';
            }
        } else {
            echo '<div class="col-md-12">
                        <div class="card card-plain">
                            <div class="header">
                                <h4 class="title">Message: '.$data->{'message'}.'</h4>
                            </div>
                        </div>
                    </div>';
        }
    ?>                    

                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group text-center">
                            <button name="submitupload" type="submit" class="btn btn-wd" data-toggle="modal" data-target="#myModal"><i class="ti-cloud-up"></i> Upload files here...</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
