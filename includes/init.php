<?php
	$GLOBALS['ROOTPATH'] = str_replace('includes/' . basename(__FILE__), '', str_replace('\\', '/', __FILE__));

	session_start();

	require_once($GLOBALS['ROOTPATH'] . 'includes/includes.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/conf.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/mysql_connexion.php');
?>
