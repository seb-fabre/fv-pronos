<?php
	session_start();

	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	$id = $_GET['id'];
	$name = $_GET['name'];

	if ($id == -1)
		$team = new Team();
	else
		$team = Team::find($id);

	if (!$team)
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Equipe invalide'));
		exit;
	}

	$team->name = $name;
	$team->save();

	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Equipe enregistrée', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Equipe enregistrée'));
?>
