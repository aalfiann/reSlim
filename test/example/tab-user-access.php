<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <?php
                        if (isset($_POST['submitdelete'.(empty($_POST['tokenid'])?'':$_POST['tokenid'])])){
                            $post_array = array(
                            	'Username' => $datalogin['username'],
                                'Token' => $datalogin['token'],
                                'TokenToDelete' => $_POST['tokenid']
                            );
                            Core::processDelete(Core::getInstance()->api.'/user/token/delete',$post_array,'Token');
                        }

                        if (isset($_POST['submitdeleteall'])){
                            $post_array = array(
                            	'Username' => $datalogin['username'],
                                'Token' => $datalogin['token']
                            );
                            Core::processDelete(Core::getInstance()->api.'/user/token/delete/all',$post_array,'Token');
                        }
                    ?>
                    <!-- Chart Issues -->
                    <div class="col-md-12">
                        <div class="card ">
                            
                            <div class="header">
                                <h4 class="title"><?php echo Core::lang('title_access')?></h4>
                                <p class="category"><?php echo Core::lang('info_access')?></p>
                            </div>
                            <div class="content">
                                <div class="content table-responsive table-full-width">
                                
                                    <table id="export" class="table table-striped">
                                        <thead>
                                            <th><?php echo Core::lang('tb_no')?></th>
                                            <th><?php echo Core::lang('username')?></th>
                                            <th><?php echo Core::lang('token')?></th>
                                            <th><?php echo Core::lang('date_login')?></th>
                                            <th><?php echo Core::lang('expired')?></th>
                                            <th><?php echo Core::lang('manage')?></th>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            $url = Core::getInstance()->api.'/user/token/data/'.$datalogin['username'].'/'.$datalogin['token'];
                                            $data = json_decode(Core::execGetRequest($url));
                                            if (!empty($data)){
                                                if ($data->{'status'} == "success"){
                                                    $n=1;
                                                    foreach ($data->results as $name => $value) {
                                                        echo '<tr>';
                                                        echo '<td>' . $n++ .'</td>';
                                                        echo '<td>' . $value->{'Username'} .'</td>';
                                                        echo '<td>' . $value->{'RS_Token'} .'</td>';
                                                        echo '<td>' . $value->{'Created'} .'</td>';
                                                        echo '<td>' . $value->{'Expired'} .'</td>';
                                                        echo '<td>'.(($value->{'RS_Token'} != $datalogin['token'])?'<form action="'.$_SERVER['PHP_SELF'].'" method="post"><input name="tokenid" type="hidden" class="form-control border-input" value="'.$value->{'RS_Token'}.'"><button name="submitdelete'.$value->{'RS_Token'}.'" type="submit" class="btn btn-danger btn-fill btn-wd">'.Core::lang('revoke_access').'</button></form>':'<b class="text-success">'.Core::lang('active_access').'</b>').'</td>';
                                                        echo '</tr>';              
                                                    }
                                                }
                                            }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="footer text-center">
                                    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                                        <button name="submitdeleteall" type="submit" class="btn btn-wd btn-danger"><?php echo Core::lang('revoke_access_all')?></button>
                                    </form>
                                    <hr>
                                    <div class="stats">
                                        <i class="ti-check"></i><?php echo Core::lang('notice_access')?>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                    
                </div>
            </div>
</div>