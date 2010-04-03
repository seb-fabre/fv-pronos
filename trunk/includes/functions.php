<?php
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
	<link rel="stylesheet" href="$url/css/redmond/jquery-ui-1.8.custom.css" type="text/css" media="screen" />
	<script type="text/javascript" src="$url/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="$url/js/jquery-ui-1.8.custom.min.js"></script>
	<script type="text/javascript" src="$url/js/jquery.ui.datepicker-fr.js"></script>
	<script type="text/javascript" src="$url/js/jquery.simplemodal-1.2.3.js"></script>
	<script type="text/javascript" src="$url/js/jquery.form.js"></script>
	<script type="text/javascript" src="$url/js/jquery.qtip-1.0.0-rc3.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
</head>
HTML;
}

function echoMenu()
{
	require_once($GLOBALS['ROOTPATH'] . 'includes/header.php');
}

function echoHTMLFooter()
{
	echo <<<HTML
	<div id="loading">
		<div id="subloading">Chargement</div>
	</div>
HTML;

	Notification::clearAll();

	echo '<script type="text/javascript">';
	echo $GLOBALS['FooterJS'];
	echo '</script>';
}

/**
 * Checks the database migration version, creates the table if doesn't exist,
 * migrates if necessary, and if migrates puts the application in maintenance
 */
function checkMigrationVersion()
{
	// check database version
	$req = mysql_query('SELECT * FROM pr_migration_version');

	$currentVersion = 0;

	// create table if not present
	if (!$req)
	{
		mysql_query("CREATE TABLE `pr_migration_version` (`version` INT UNSIGNED NOT NULL DEFAULT  '1');");
		mysql_query('INSERT INTO pr_migration_version VALUES(0);');
	}
	else if (mysql_num_rows($req) == 0)
	{
		mysql_query('INSERT INTO pr_migration_version VALUES(0);');
	}
	else
	{
		$res = mysql_fetch_assoc($req);
		$currentVersion = $res['version'];
	}

	if (file_exists($GLOBALS['ROOTPATH'] . 'maintenance.txt'))
	{
		// FIXME : redirect to a real maintenance page
		header("HTTP/1.x 503 Temporary undisponible");
		header("Status:503 Temporary undisponible");
		die('maintenance');
	}

	Migration::migrate($currentVersion);
}

function echoNotifications()
{
	Notification::display();
}

/********************************
 * Retro-support of get_called_class()
 * Tested and works in PHP 5.2.4
 * http://www.sol1.com.au/
 ********************************/
if(!function_exists('get_called_class'))
{
	function get_called_class($bt = false,$l = 1)
	{
    if (!$bt) 
			$bt = debug_backtrace();

    if (!isset($bt[$l])) 
			throw new Exception("Cannot find called class -> stack level too deep.");

    if (!isset($bt[$l]['type']))
		{
        throw new Exception ('type not set');
    }
    else
		{
			switch ($bt[$l]['type'])
			{
        case '::':
					$lines = file($bt[$l]['file']);
					$i = 0;
					$callerLine = '';
					do
					{
							$i++;
							$callerLine = $lines[$bt[$l]['line']-$i] . $callerLine;
					}
					while (stripos($callerLine,$bt[$l]['function']) === false);

					preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
											$callerLine,
											$matches);
					if (!isset($matches[1]))
					{
							// must be an edge case.
							throw new Exception ("Could not find caller class: originating method call is obscured.");
					}

					switch ($matches[1])
					{
						case 'self':
						case 'parent':
							return get_called_class($bt,$l+1);
						default:
							return $matches[1];
					}
				// won't get here.
        case '->': switch ($bt[$l]['function'])
				{
					case '__get':
							// edge case -> get class of calling object
							if (!is_object($bt[$l]['object']))
								throw new Exception ("Edge case fail. __get called on non object.");

							return get_class($bt[$l]['object']);
					default:
						return $bt[$l]['class'];
				}

        default:
					throw new Exception ("Unknown backtrace method type");
			}
		}
	}
}

function GETorPOST($name)
{
	if (!empty($_POST[$name]))
		return $_POST[$name];

	if (!empty($_GET[$name]))
		return $_GET[$name];

	return null;
}

function camelCaseToUnderscores($str)
{
	$str[0] = strtolower($str[0]);
	$func = create_function('$c', 'return "_" . strtolower($c[1]);');
	return preg_replace_callback('/([A-Z])/', $func, $str);
}
