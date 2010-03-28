<?php
  if (!mysql_connect($GLOBALS['conf']['mysql_host'], $GLOBALS['conf']['mysql_login'], $GLOBALS['conf']['mysql_password']))
	{
		die ('Paramètres de connexion à la base de données invalides.');
	}
	mysql_select_db($GLOBALS['conf']['mysql_database']);
?>