<?php
	session_start();
	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	$season = Season::find($_POST['id']);

	$teams = $_POST['team'];

	$seasonTeams = $season->getTeams();

	foreach ($teams as $team)
	{
		if (!isset($seasonTeams[$team]))
		{
			$season->addTeam($team);
			$seasonTeams[$team] = 1;
		}
	}
	echo json_encode(array('success' => 1));
?>