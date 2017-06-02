<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <?php
                        if (isset($_POST['submitupdate']))
                        {
                            $post_array = array(
                            	'Username' => $_POST['username'],
                                'Fullname' => $_POST['fullname'],
                                'Address' => $_POST['address'],
                                'Phone' => $_POST['phone'],
                                'Email' => $_POST['email'],
                                'Aboutme' => $_POST['aboutme'],
                                'Avatar' => $_POST['avatar'],
                                'Role' => Core::getRole($datalogin['token']),
                                'Status' => '1',
                                'Token' => $datalogin['token']
                            );
                            Core::update(Core::getInstance()->api.'/user/update',$post_array);
                        } 
                    ?>
<?php 
    $url = Core::getInstance()->api.'/user/profile/'.$datalogin['username'].'/'.$datalogin['token'];
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
                                        <h5>Role<br /><small>'.$data->result[0]->{'Role'}.'</small></h5>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Status<br /><small>'.$data->result[0]->{'Status'}.'</small></h5>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Registered<br /><small>'.$data->result[0]->{'Created_at'}.'</small></h5>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Last Updated<br /><small>'.$data->result[0]->{'Updated_at'}.'</small></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Profile</h4>
                            </div>
                            <div class="content">
                                <form action="'.$_SERVER['PHP_SELF'].'?m=4" method="post">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" name="username" class="form-control border-input" value="'.$data->result[0]->{'Username'}.'" maxlength="50" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Full Name</label>
                                                <input type="text" name="fullname" class="form-control border-input" placeholder="Input your fullname" value="'.$data->result[0]->{'Fullname'}.'" maxlength="50" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <textarea name="address" rows="3" class="form-control border-input" placeholder="Here can be your address ..." maxlength="255">'.$data->result[0]->{'Address'}.'</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="phone" class="form-control border-input" placeholder="Input your phone number" maxlength="15" value="'.$data->result[0]->{'Phone'}.'" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control border-input" placeholder="Input your email address" maxlength="50" value="'.$data->result[0]->{'Email'}.'" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>About Me</label>
                                                <textarea name="aboutme" rows="5" class="form-control border-input" placeholder="Here can be your description">'.$data->result[0]->{'Aboutme'}.'</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Avatar</label>
                                                <input type="text" name="avatar" class="form-control border-input" placeholder="Please input url image for Your Avatar." value="'.$data->result[0]->{'Avatar'}.'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button name="submitupdate" type="submit" class="btn btn-info btn-fill btn-wd">Update Profile</button>
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

                