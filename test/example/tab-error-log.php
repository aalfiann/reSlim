<div class="content">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-plain">
                <?php
                    if (isset($_POST['clearlog'])){
                        $data = json_decode(Core::execGetRequest(Core::getInstance()->api.'/logs/data/clear/'.$datalogin['username'].'/'.$datalogin['token']));
                        if (!empty($data)){
                            if ($data->{'status'} == "success"){
                                echo '<div class="col-lg-12">';
                                echo Core::getMessage('success',Core::lang('core_clear_log_success'));
                                echo '</div>';
                            } else {
                                echo '<div class="col-lg-12">';
                                echo Core::getMessage('danger',Core::lang('core_clear_log_failed'),$data->{'message'});
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="col-lg-12">';
                            echo Core::getMessage('danger',Core::lang('core_clear_log_failed'),Core::lang('core_not_connected'));
                            echo '</div>';
                        }
                    } 
                ?>
                <div class="header">
                    <h4 class="title text-uppercase"><?php echo Core::lang('error_log_title')?></h4>
                    <p class="category"><?php echo Core::lang('error_log_description')?></p>
                </div>
                <div class="content">
                    <form method="post" action="<?php $_SERVER['PHP_SELF']?>">
                        <div class="form-group">
                            <?php 
                                $urlarray = explode("/",Core::getInstance()->api);
                                array_pop($urlarray);
                                $urlhost = implode('/', $urlarray);
                                $url = $urlhost.'/logs/app.log';
                                echo '<textarea id="textarea_1" name="content" class="form-control" rows="20" >'.Core::execGetRequest($url).'</textarea>';
                            ?>
                        </div>
                        <hr>
                        <div class="form-group text-center">
                            <button name="clearlog" type="submit" class="btn btn-fill btn-wd "><?php echo Core::lang('clear_log')?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>