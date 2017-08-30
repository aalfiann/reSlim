<div class="content">
            <div class="container-fluid">
                <div class="row">
<?php 
    $url = Core::getInstance()->api.'/user/profile/'.((empty($_GET['username']))?'':$_GET['username']).'/?apikey='.Core::getInstance()->apikey;
    $data = json_decode(Core::execGetRequest($url));

    if (!empty($data))
        {
            if ($data->{'status'} == "success")
            {
                    echo '<div class="col-lg-12 col-md-12">
                        <div class="card card-user">
                            <div class="image">
                                <img src="assets/img/background.jpg" alt="..."/>
                            </div>
                            <div class="content">
                                <div class="author">
                                  <img class="avatar border-white lazyload" data-src="'.((empty($data->result[0]->{'Avatar'}))?'assets/img/faces/face-0.jpg':$data->result[0]->{'Avatar'}).'" alt="'.$data->result[0]->{'Username'}.'"/>
                                  <h4 class="title">'.$data->result[0]->{'Fullname'}.'<br />
                                     <a href="'.Core::getInstance()->basepath.'/modul-view-profile.php?username='.$data->result[0]->{'Username'}.'"><small>@'.$data->result[0]->{'Username'}.'</small></a>
                                  </h4>
                                </div>
                                <p class="description text-center">
                                    '.$data->result[0]->{'Aboutme'}.'
                                </p>
                            </div>
                            <hr>
                            <div class="text-center">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>'.Core::lang('tb_role').'<br /><small>'.$data->result[0]->{'Role'}.'</small></h5>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>'.Core::lang('status').'<br /><small>'.$data->result[0]->{'Status'}.'</small></h5>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>'.Core::lang('registered').'<br /><small>'.$data->result[0]->{'Created_at'}.'</small></h5>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>'.Core::lang('last_updated').'<br /><small>'.$data->result[0]->{'Updated_at'}.'</small></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">'.Core::lang('info').' '.Core::lang('profile').'</h4>
                            </div>
                            <div class="content">
                                <form>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>'.Core::lang('tb_username').'</label>
                                                <input type="text" name="username" class="form-control border-input" value="'.$data->result[0]->{'Username'}.'" maxlength="50" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>'.Core::lang('fullname').'</label>
                                                <input type="text" name="fullname" class="form-control border-input" placeholder="'.Core::lang('input_fullname').'" value="'.$data->result[0]->{'Fullname'}.'" maxlength="50" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>'.Core::lang('address').'</label>
                                                <textarea name="address" rows="3" class="form-control border-input" placeholder="'.Core::lang('input_address').'" maxlength="255">'.$data->result[0]->{'Address'}.'</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>'.Core::lang('phone').'</label>
                                                <input type="text" name="phone" class="form-control border-input" placeholder="'.Core::lang('input_phone').'" maxlength="15" value="'.$data->result[0]->{'Phone'}.'" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>'.Core::lang('email_address').'</label>
                                                <input type="email" name="email" class="form-control border-input" placeholder="'.Core::lang('input_email').'" maxlength="50" value="'.$data->result[0]->{'Email'}.'" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>'.Core::lang('about_me').'</label>
                                                <textarea name="aboutme" rows="5" class="form-control border-input" placeholder="'.Core::lang('input_about_me').'">'.$data->result[0]->{'Aboutme'}.'</textarea>
                                            </div>
                                        </div>
                                    </div>

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

                