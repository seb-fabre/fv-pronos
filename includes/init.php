<?php
	$GLOBALS['ROOTPATH'] = str_replace('includes/' . basename(__FILE__), '', str_replace('\\', '/', __FILE__));

	require_once($GLOBALS['ROOTPATH'] . 'includes/__classes.php');

	require_once($GLOBALS['ROOTPATH'] . 'includes/class.migration.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/class.tools.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/class.notification.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/JSON.php');

	session_start();

	if (empty($GLOBALS['HEADER_CONTENT_TYPE']))
		header('Content-type: text/html; charset=UTF-8');
	else
		header($GLOBALS['HEADER_CONTENT_TYPE']);

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

	define('SPINNER_URL', APPLICATION_URL . 'js/nyroModal-1.6.2/img/ajaxLoader.gif');
	define('SPINNER_TAG', '<img src="' . SPINNER_URL . '" style="position: absolute; top: 50%; left: 50%; margin-top: -21px; margin-left: -21px;" class="spinner" />');
	$GLOBALS['FooterJS'] .= 'var spinnerUrl = "' . SPINNER_URL . '";';
