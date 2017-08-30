<div class="sidebar-wrapper">
            <div class="logo">
                <a href="<?php echo Core::getInstance()->basepath;?>" class="simple-text">
                    <?php echo Core::getInstance()->title?>
                </a>
                <div class="text-center">v.<?php echo Core::getInstance()->version?></div>
            </div>

            <ul class="nav">
            <?php if (!empty($datalogin['username'])) 
            { ?>
                <?php if (Core::getRole($datalogin['token']) != 3) { // SuperUser and Admin Menu ?>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==3) echo 'class="active"';?> >
                        <a href="modul-dashboard.php?m=3">
                            <i class="ti-panel"></i>
                            <p><?php echo Core::lang('dashboard')?></p>
                        </a>
                    </li>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==5) echo 'class="active"';?> >
                        <a href="modul-data-user.php?m=5&page=1&itemsperpage=10&search=">
                            <i class="ti-id-badge"></i>
                            <p><?php echo Core::lang('data')?> <?php echo Core::lang('user')?></p>
                        </a>
                    </li>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==6) echo 'class="active"';?> >
                        <a href="modul-explore.php?m=6&page=1&itemsperpage=12&search=">
                            <i class="ti-cloud-up"></i>
                            <p><?php echo Core::lang('explore')?></p>
                        </a>
                    </li>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==7) echo 'class="active"';?> >
                        <a href="modul-data-api.php?m=7&page=1&itemsperpage=10&search=">
                            <i class="ti-lock"></i>
                            <p><?php echo Core::lang('api_keys')?></p>
                        </a>
                    </li>
                <?php } else {  // Member Menu ?>
                    <li <?php if (!empty($_GET['m'])) if($_GET['m']==7) echo 'class="active"';?> >
                        <a href="modul-data-api.php?m=7&page=1&itemsperpage=10&search=">
                            <i class="ti-lock"></i>
                            <p><?php echo Core::lang('api_keys')?></p>
                        </a>
                    </li>
                <?php } ?>
        <?php } else if (pathinfo(basename($_SERVER['REQUEST_URI']), PATHINFO_FILENAME) == "modul-view-profile") { // Guest ?>
                    <li class="active">
                        <a href="<?php basename($_SERVER['REQUEST_URI'])?>">
                            <i class="ti-search"></i>
                            <p><?php echo Core::lang('view_profile')?></p>
                        </a>
                    </li>
        <?php } else { ?>
                <li <?php if (!empty($_GET['m'])) if($_GET['m']==1) echo 'class="active"';?> >
                    <a href="modul-login.php?m=1">
                        <i class="ti-user"></i>
                        <p><?php echo Core::lang('login')?></p>
                    </a>
                </li>
                <li <?php if (!empty($_GET['m'])) if($_GET['m']==2) echo 'class="active"';?> >
                    <a href="modul-register.php?m=2">
                        <i class="ti-pencil-alt"></i>
                        <p><?php echo Core::lang('register')?></p>
                    </a>
                </li>
                <li <?php if (!empty($_GET['m'])) if($_GET['m']==7) echo 'class="active"';?> >
                    <a href="modul-contact.php?m=7">
                        <i class="ti-email"></i>
                        <p><?php echo Core::lang('contact_us')?></p>
                    </a>
                </li>
            <?php } ?>
            </ul>
    	</div>