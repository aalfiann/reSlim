<div class="sidebar-wrapper">
            <div class="logo">
                <a href="<?=Core::$basepath;?>" class="simple-text">
                    reSlim
                </a>
                <div class="text-center">v.1.0.0</div>
            </div>

            <ul class="nav">
            <?php if (!empty($datalogin['username'])) 
            { ?>
                <li <?php if (!empty($_GET['m'])) if($_GET['m']==3) echo 'class="active"';?> >
                    <a href="modul-data-user.php?m=3">
                        <i class="ti-pencil"></i>
                        <p>Data User</p>
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