                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <?php if (Core::getRole($datalogin['token']) == 1) { // SuperUser Only
                            echo '<li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="ti-settings"></i>
                                    <p>Tools</p>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="modul-settings.php">'.Core::lang('settings').'</a></li>
                                    <li><a href="modul-error-log.php">Error Log</a></li>
                                </ul>
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
                                <li><a href="modul-user-profile.php?m=4">'.Core::lang('my_profile').'</a></li>
                                <li><a href="logout.php">'.Core::lang('logout').'</a></li>
                              </ul>
                        </li>';
                        }?>
                    </ul>

                </div>