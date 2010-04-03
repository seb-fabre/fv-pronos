<?php
  if (empty($GLOBALS['conf']['mysql_host'])
					|| empty($GLOBALS['conf']['mysql_login'])
					|| empty($GLOBALS['conf']['mysql_password'])
					|| empty($GLOBALS['conf']['mysql_database'])
					|| !@mysql_connect($GLOBALS['conf']['mysql_host'], $GLOBALS['conf']['mysql_login'], $GLOBALS['conf']['mysql_password']))
	{
		die ('Les paramètres de connexion à la base de données sont incomplets ou invalides.');
	}

	if (!mysql_select_db($GLOBALS['conf']['mysql_database']))
	{
		die ('Impossible de se connecter à la base de données.');
	}
?>