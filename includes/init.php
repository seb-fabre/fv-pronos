<?php
	$GLOBALS['ROOTPATH'] = str_replace('includes/' . basename(__FILE__), '', str_replace('\\', '/', __FILE__));

	require_once($GLOBALS['ROOTPATH'] . 'includes/__classes.php');

	require_once($GLOBALS['ROOTPATH'] . 'includes/class.migration.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/class.tools.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/class.notification.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/JSON.php');

	session_start();

	header('Content-type: text/html; charset=UTF-8');

	if (!file_exists($GLOBALS['ROOTPATH'] . 'includes/conf.php'))
	{
		die("Vous devez lancer l'installation du site avant de pouvoir y accéder.");
	}

	require_once($GLOBALS['ROOTPATH'] . 'includes/functions.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/conf.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/mysql_connexion.php');

	checkMigrationVersion();

	if (!empty($_SESSION['user']))
	{
		if (get_class($_SESSION['user']) != 'User')
			unset($_SESSION['user']);
	}

	$GLOBALS['FooterJS'] = '';
	
