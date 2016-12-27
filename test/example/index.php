<?php
// Load all classes
spl_autoload_register(function ($classname) {require ( $classname . ".php");});
// Verify session, cookies and token
$datalogin = Core::checkSessions();
// Redirect to dashboard page
Core::goToPage('modul-dashboard.php?m=3');
?>