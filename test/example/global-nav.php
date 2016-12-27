<div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <?php if (!empty($datalogin['username'])) {
                            echo '<li>
                                    <a href="modul-user-profile.php?m=4">
    				    				<i class="ti-user"></i>
	        							<p>'.$datalogin['username'].'</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="logout.php">
                                        <i class="ti-power-off"></i>
        								<p>Logout</p>
                                    </a>
                                </li>';
                        };?>
                    </ul>

                </div>