<?php 
spl_autoload_register(function ($classname) {require ( $classname . ".php");});
Core::logout();
?>