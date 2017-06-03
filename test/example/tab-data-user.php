<?php 
//Validation url param
$search = filter_var((empty($_GET['search'])?'':$_GET['search']),FILTER_SANITIZE_STRING);
$page = filter_var((empty($_GET['page'])?'1':$_GET['page']),FILTER_SANITIZE_STRING);
$itemsperpage = filter_var((empty($_GET['itemsperpage'])?'10':$_GET['itemsperpage']),FILTER_SANITIZE_STRING);
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
                                <input name="m" type="text" class="form-control border-input" value="5" hidden>
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
            <div class="container-fluid">
                <div class="row">
               
<?php 
    $url = Core::getInstance()->api.'/user/data/search/'.$page.'/'.$itemsperpage.'/'.$datalogin['token'].'/?query='.rawurlencode($search);
    $data = json_decode(Core::execGetRequest($url));

    if (!empty($data))
        {
            if ($data->{'status'} == "success")
            {
                echo '<div class="col-md-12">
                        <div class="card card-plain">
                            <div class="header">
                                <h4 class="title text-uppercase">Data User</h4>
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
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table id="export" class="table table-striped">
                                    <thead>
                                        <th>No</th>
                                    	<th>Username</th>
                                    	<th>Fullname</th>
                                    	<th>Address</th>
                                    	<th>Phone</th>
                                        <th>Email</th>
                                        <th>Aboutme</th>
                                    	<th>Avatar</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                        <th>Manage</th>
                                    </thead>
                                    <tbody>';
                $n=$data->metadata->{'number_item_first'};
                foreach ($data->results as $name => $value) 
	            {
                    echo '<tr>';
                    echo '<td>' . $n++ .'</td>';
                    echo '<td>' . $value->{'Username'} .'</td>';
			        echo '<td>' . $value->{'Fullname'} .'</td>';
        			echo '<td>' . $value->{'Address'} .'</td>';
                	echo '<td>' . $value->{'Phone'} .'</td>';
                	echo '<td>' . $value->{'Email'} .'</td>';
            	    echo '<td>' . $value->{'Aboutme'} .'</td>';
    	    		echo '<td>' . $value->{'Avatar'} .'</td>';
                    echo '<td>' . $value->{'Role'} .'</td>';
                    echo '<td>' . $value->{'Status'} .'</td>';
                    echo '<td>' . $value->{'Created_at'} .'</td>';
                    echo '<td>' . $value->{'Updated_at'} .'</td>';
                    echo '<td><a href="modul-user-profile-edit.php?m=5&username=' . $value->{'Username'} .'"><i class="ti-pencil"></i> Edit</a></td>';
	    	    	echo '</tr>';              
                }
                echo '</tbody>
                </table>';

                echo '</div>
                </div>';

                $pagination = new Pagination;
                echo $pagination->makePagination($data,$_SERVER['PHP_SELF'].'?m=5&search='.rawurlencode($search));
                
                echo '</div>';
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

                