<?php 
	require_once('../includes/init.php');
	
	$id = $_GET['id'];
	$name = $_GET['name'];
	$team = $_GET['pr_team_id'];
	
	if ($id == -1)
		$user = new User();
	else
		$user = User::find($id);
	
	$user->name = $name;
	$user->pr_team_id = $team;
	$user->save();
	
	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Utilisateur enregistré', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Utilisateur enregistré'));
?>
