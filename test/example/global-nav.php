                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <?php if (Core::getRole($datalogin['token']) == 1) { // SuperUser Only
                            echo '<li>
                                <a href="modul-settings.php?m=8">
                                    <i class="ti-settings"></i>
            						<p>Settings</p>
                                </a>
                            </li>'; 
                            };?>
                        
                        <?php if (!empty($datalogin['username'])) {
                            echo '<li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="ti-user"></i>
                                    
									<p>'.$datalogin['username'].'</p>
									<b class="caret"></b>
                              </a>
                              <ul class="dropdown-menu">
                                <li><a href="modul-user-profile.php?m=4">My Profile</a></li>
                                <li><a href="logout.php">Logout</a></li>
                              </ul>
                        </li>';
                        }?>
                    </ul>

                </div>