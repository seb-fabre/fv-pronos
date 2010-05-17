<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id', -1);
	$name = GETorPOST('name');
	$label = GETorPOST('season');
	$teams = GETorPOST('teams');
	$category = GETorPOST('pr_team_category_id');

	if ($id == -1)
		$season = new Season();
	else
		$season = Season::find($id);

	$league = League::findBy('name', $name);
	if (!$league)
		$league = new League();

	$league->name = $name;
	$league->pr_team_category_id = $category;
	$league->save();

	$season->label = $label;
	$season->pr_league_id = $league->id;
	$season->teams = $teams;
	$season->save();

	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Championnat enregistré', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Championnat enregistré'));
?>
