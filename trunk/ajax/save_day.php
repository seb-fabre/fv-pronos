<?php
	require_once('../includes/init.php');

	$id = $_GET['id'];
	$season = $_GET['pr_season_id'];
	$number = $_GET['number'];

	if (!$season)
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Championnat invalide'));
		exit;
	}

	if ($id == -1)
		$day = new Day();
	else
		$day = Day::find($id);

	if (!$day)
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Equipe invalide'));
		exit;
	}

	$day->pr_season_id = $season;
	$day->number = $number;
	$day->save();

	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Journée enregistrée', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Journée enregistrée'));
?>
