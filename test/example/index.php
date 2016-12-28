<?php
// Load all classes
spl_autoload_register(function ($classname) {require ( $classname . ".php");});
// Verify session, cookies and token
$datalogin = Core::checkSessions();
// Redirect to dashboard page
if (Core::getRole($datalogin['token']) != '3') {
    Core::goToPage('modul-dashboard.php?m=3');
} else {
    Core::goToPage('modul-user-profile.php?m=4');
}

?>