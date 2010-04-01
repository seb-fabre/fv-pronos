<?php
	require_once('../includes/init.php');

	$season = Season::find(GETorPOST('id'));

	$teams = GETorPOST('team');

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