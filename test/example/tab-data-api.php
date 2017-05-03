<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <?php 
                        if (isset($_POST['submitnewapi']))
                        {
                            $post_array = array(
                                'Username' => $datalogin['username'],
                                'Token' => $datalogin['token'],
                                'Domain' => filter_var($_POST['domain'],FILTER_SANITIZE_STRING)
                            );
                            Core::createNewAPI(Core::getInstance()->api.'/user/keys/create',$post_array);
                        }
                    ?>
                    <!-- Start Modal -->
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add new API Keys</h4>
                              </div>
                              <form method="post" action="<?php $_SERVER['PHP_SELF']?>">
                              <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Domain</label>
                                            <input name="domain" type="text" placeholder="Input your domain here..." class="form-control border-input" required>
                                        </div>
                                    </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submitnewapi" class="btn btn-primary">Submit</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    <!-- End Modal -->
<?php 
    $url = Core::getInstance()->api.'/user/'.$datalogin['username'].'/keys/data/search/'.$_GET['page'].'/'.$_GET['itemsperpage'].'/'.$datalogin['token'].'/?query='.$_GET['search'];
    $data = json_decode(Core::execGetRequest($url));

    // Data Status
    $urlstatus = Core::getInstance()->api.'/user/status/'.$datalogin['token'];
    $datastatus = json_decode(Core::execGetRequest($urlstatus));

    if (!empty($data))
        {
            if ($data->{'status'} == "success")
            {
                foreach ($data->results as $row => $value) {
                    if (isset($_POST['submitupdateapi'.$value->{'ApiKey'}]))
                    {
                        $post_array = array(
                            'Username' => $datalogin['username'],
                            'Token' => $datalogin['token'],
                            'ApiKey' => $_POST['apikey'],
                            'Status' => $_POST['status']
                        );
                        Core::updateAPI(Core::getInstance()->api.'/user/keys/update',$post_array);
                        echo Core::reloadPage();
                    }
                }

                foreach ($data->results as $row => $value) {
                    if (isset($_POST['submitdeleteapi'.$value->{'ApiKey'}]))
                    {
                        $post_array = array(
                            'Username' => $datalogin['username'],
                            'Token' => $datalogin['token'],
                            'ApiKey' => $_POST['apikey']
                        );
                        Core::deleteAPI(Core::getInstance()->api.'/user/keys/delete',$post_array);
                        echo Core::reloadPage();
                    }
                }

                echo '<div class="col-md-12">
                        <div class="card card-plain">
                            <div class="header">
                                <h4 class="title text-uppercase">Data API Keys</h4>
                                <p class="category">Message: '.$data->{'message'}.'<br>
                                Shows no: '.$data->metadata->{'number_item_first'}.' - '.$data->metadata->{'number_item_last'}.' from total data: '.$data->metadata->{'records_total'}.'</p>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		    				    			<p><i class="ti-zip"></i> Export Data <b class="caret"></b></p>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onClick ="$(\'#export\').tableExport({type:\'excel\',escape:\'false\'});">Export XLS</a></li>
                                            <li><a href="#" onClick ="$(\'#export\').tableExport({type:\'doc\',escape:\'false\'});">Export DOC</a></li>
                                            <li><a href="#" onClick ="$(\'#export\').tableExport({type:\'txt\',escape:\'false\'});">Export TXT</a></li>
                                            <li><a href="#" onClick ="$(\'#export\').tableExport({type:\'csv\',escape:\'false\'});">Export CSV</a></li>
                                            <li><a href="#" onClick ="$(\'#export\').tableExport({type:\'pdf\',pdfFontSize:\'7\',escape:\'false\'});">Export PDF</a></li>
                                            <li><a href="#" onClick ="$(\'#export\').tableExport({type:\'sql\'});">Export SQL</a></li>
                                            <li><a href="#" onClick ="$(\'#export\').tableExport({type:\'xml\',escape:\'false\'});">Export XML</a></li>
                                            <li><a href="#" onClick ="$(\'#export\').tableExport({type:\'json\',escape:\'false\'});">Export JSON</a></li>
                                        </ul>
                                    </div>
                                <button type="submit" class="btn btn-wd" data-toggle="modal" data-target="#myModal">Add new API Keys</button>
                            
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table id="export" class="table table-striped">
                                    <thead>
                                        <th>No</th>
                                    	<th>Domain</th>
                                    	<th>API Key</th>
                                    	<th>Status</th>
                                        <th>Username</th>
                                        <th>Created</th>
                                        <th>Updated at</th>
                                        <th>Updated by</th>
                                        <th>Manage</th>
                                    </thead>
                                    <tbody>';
                $n=$data->metadata->{'number_item_first'};
                foreach ($data->results as $name => $value) 
	            {
                    echo '<tr>';
                    echo '<td>' . $n++ .'</td>';
                    echo '<td>' . $value->{'Domain'} .'</td>';
			        echo '<td>' . $value->{'ApiKey'} .'</td>';
        			echo '<td>' . $value->{'Status'} .'</td>';
                	echo '<td>' . $value->{'Username'} .'</td>';
                	echo '<td>' . $value->{'Created_at'} .'</td>';
            	    echo '<td>' . $value->{'Updated_at'} .'</td>';
                    echo '<td>' . $value->{'Updated_by'} .'</td>';
                    echo '<td><a href="#" data-toggle="modal" data-target="#'.$value->{'ApiKey'}.'"><i class="ti-pencil"></i> Edit</a></td>';
	    	    	echo '</tr>';              
                }
                echo '</tbody>
                </table>';

                echo '</div>
                </div>';

                $pagination = new Pagination;
                echo $pagination->makePagination($data,$_SERVER['PHP_SELF'].'?m=5');
                
                echo '</div>';
                foreach ($data->results as $name=>$value){
                    echo '<!-- Start Modal -->
                        <div class="modal fade" id="'.$value->{'ApiKey'}.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add new API Keys</h4>
                              </div>
                              <form method="post" action="'.$_SERVER['PHP_SELF'].'?m=7&page='.$_GET['page'].'&itemsperpage='.$_GET['itemsperpage'].'&search='.$_GET['search'].'">
                              <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Created</label>
                                            <input name="created" type="text" placeholder="Input your domain here..." class="form-control border-input" value="'.$value->{'Created_at'}.'" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Domain</label>
                                            <input name="domain" type="text" placeholder="Input your domain here..." class="form-control border-input" value="'.$value->{'Domain'}.'" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>API Key</label>
                                            <textarea name="apikey" rows="3" type="text" placeholder="Input your domain here..." class="form-control border-input" readonly>'.$value->{'ApiKey'}.'</textarea>
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
                                        </div>
                                    </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="submitdeleteapi'.$value->{'ApiKey'}.'" class="btn btn-danger pull-left">Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submitupdateapi'.$value->{'ApiKey'}.'" class="btn btn-primary">Update</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    <!-- End Modal -->';
                }
            }
            else
            {
                echo '<div class="col-md-12">
                        <div class="card card-plain">
                            <div class="header">
                                <h4 class="title">Message: '.$data->{'message'}.'</h4>
                            </div>
                        </div>
                    </div>';
            } 
        }
?>
                            </div>
                        </div>
                    </div>

                