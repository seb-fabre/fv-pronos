<?php
	require_once($GLOBALS['ROOTPATH'] . 'includes/Day.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/League.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/Match.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/Prono.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/Season.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/Team.php');
	require_once($GLOBALS['ROOTPATH'] . 'includes/User.php');

/**
 * Define function json_decode if the json module is disabled
 */
if (!function_exists('json_decode'))
{
	function json_decode($content, $assoc=false)
	{
		if ($assoc)
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		else
			$json = new Services_JSON;
		return $json->decode($content);
	}
}

/**
 * Define function json_decode if the json module is disabled
 */
if (!function_exists('json_encode'))
{
	function json_encode($content)
	{
		$json = new Services_JSON;
		return $json->encode($content);
	}
}

function echoHTMLHead($title='')
{
	$url = APPLICATION_URL;

	echo <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>$title</title>
	<link rel="stylesheet" href="$url/css/screen.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="$url/css/pronos.css" type="text/css" media="screen" />
	<script type="text/javascript" src="$url/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="$url/js/jquery.simplemodal-1.2.3.js"></script>
	<script type="text/javascript" src="$url/js/jquery.form-2.24.js"></script>
	<script type="text/javascript" src="$url/js/jquery.qtip-1.0.0-rc3.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
</head>
HTML;
}

function echoMenu()
{
	require_once($GLOBALS['ROOTPATH'] . 'includes/header.php');
}
?>