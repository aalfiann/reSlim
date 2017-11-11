<?php spl_autoload_register(function ($classname) {require ( $classname . ".php");});$datalogin = Core::checkSessions();?>
<!doctype html>
<html lang="en">
<head>
    <title>Error Log - <?php echo Core::getInstance()->title?></title>
    <?php include 'global-meta.php';?>
    <!-- Edit_area -->
	<script language="javascript" type="text/javascript" src="assets/js/edit_area/edit_area_full.js"></script>
   <script language="javascript" type="text/javascript">
    editAreaLoader.init({
	    id : "textarea_1",
	    start_highlight: false,	
	    allow_resize: "y",
    	allow_toggle: true,
        word_wrap: false,
        show_line_colors: true,
	    language: "en",
    	syntax: "html"
    });
 	</script>
</head>
<body>

<div class="wrapper">
	<div class="sidebar" data-background-color="white" data-active-color="danger">
        <?php include 'global-menu.php';?>
    </div>

    <div class="main-panel">
		<nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar bar1"></span>
                        <span class="icon-bar bar2"></span>
                        <span class="icon-bar bar3"></span>
                    </button>
                    <a class="navbar-brand" href="#">Error Log</a>
                </div>
                <?php include 'global-nav.php';?>
            </div>
        </nav>

            <?php include 'tab-error-log.php';?>

            <?php include 'global-footer.php';?>


    </div>
</div>
    <?php include'global-js.php';?>
</body>
</html>
