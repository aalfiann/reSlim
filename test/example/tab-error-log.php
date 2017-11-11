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
                                echo Core::getMessage('success','Process Clear Log Successfuly!');
                                echo '</div>';
                            } else {
                                echo '<div class="col-lg-12">';
                                echo Core::getMessage('danger','Process Clear Log Failed!',$data->{'message'});
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="col-lg-12">';
                            echo Core::getMessage('danger','Process Clear Log Failed!','Can not connected to the server!');
                            echo '</div>';
                        }
                    } 
                ?>
                <div class="header">
                    <h4 class="title text-uppercase">Error Log in API Server</h4>
                    <p class="category">Here is your data log which is recorded from API Server</p>
                </div>
                <div class="content">
                    <form method="post" action="<?php $_SERVER['PHP_SELF']?>">
                        <div class="form-group">
                            <?php 
                                $api = Core::getInstance()->api;
                                $url = parse_url($api, PHP_URL_SCHEME).'://'.parse_url($api, PHP_URL_HOST).'/logs/app.log';
                                echo '<textarea id="textarea_1" name="content" class="form-control" rows="20" >'.Core::execGetRequest($url).'</textarea>';
                            ?>
                        </div>
                        <hr>
                        <div class="form-group text-center">
                            <button name="clearlog" type="submit" class="btn btn-fill btn-wd ">Clear Log</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>