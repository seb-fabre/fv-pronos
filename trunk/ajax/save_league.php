<?php
	session_start();

	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	$id = $_GET['id'];
	$name = $_GET['name'];
	$label = $_GET['season'];

	if ($id == -1)
		$season = new Season();
	else
		$season = Season::find($id);

	$league = League::findBy('name', $name);
	if (!$league)
		$league = new League();

	$league->name = $name;
	$league->save();

	$season->label = $label;
	$season->pr_league_id = $league->id;
	$season->save();

	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Championnat enregistré', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Championnat enregistré'));
?>
