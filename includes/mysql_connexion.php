<?php
  if (!mysql_connect($mysqlHost, $mysqlLogin, $mysqlPassword))
	{
		die ('Paramètres de connexion à la base de données invalides.');
	}
	mysql_select_db($mysqlDatabase);
?>