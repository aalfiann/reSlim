<div class="sidebar-wrapper">
            <div class="logo">
                <a href="<?=Core::$basepath;?>" class="simple-text">
                    reSlim
                </a>
                <div class="text-center">v.1.2.0</div>
            </div>

            <ul class="nav">
            <?php if (!empty($datalogin['username'])) 
            { ?>
                <?php // SuperUser and Admin Menu 
                    if (Core::getRole($datalogin['token']) != 3) { ?>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==3) echo 'class="active"';?> >
                        <a href="modul-dashboard.php?m=3">
                            <i class="ti-panel"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==4) echo 'class="active"';?> >
                        <a href="modul-user-profile.php?m=4">
                            <i class="ti-user"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==5) echo 'class="active"';?> >
                        <a href="modul-data-user.php?m=5&page=1&itemsperpage=10">
                            <i class="ti-pencil"></i>
                            <p>Data User</p>
                        </a>
                    </li>
                <?php } else {  // Member Menu ?>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==4) echo 'class="active"';?> >
                        <a href="modul-user-profile.php?m=4">
                            <i class="ti-user"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                <?php } ?>
        <?php } else if (pathinfo(basename($_SERVER['REQUEST_URI']), PATHINFO_FILENAME) == "modul-view-profile") { // Guest ?>
                    <li class="active">
                        <a href="<?php basename($_SERVER['REQUEST_URI'])?>">
                            <i class="ti-search"></i>
                            <p>View Profile</p>
                        </a>
                    </li>
        <?php } else { ?>
                <li <?php if (!empty($_GET['m'])) if($_GET['m']==1) echo 'class="active"';?> >
                    <a href="modul-login.php?m=1">
                        <i class="ti-user"></i>
                        <p>Login</p>
                    </a>
                </li>
                <li <?php if (!empty($_GET['m'])) if($_GET['m']==2) echo 'class="active"';?> >
                    <a href="modul-register.php?m=2">
                        <i class="ti-pencil-alt"></i>
                        <p>Register</p>
                    </a>
                </li>
            <?php } ?>
            </ul>
    	</div>