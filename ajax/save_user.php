<?php 
	require_once('../includes/init.php');
	
	$id = GETorPOST('id', -1);
	$name = GETorPOST('name');
	$team = GETorPOST('pr_team_id');
	
	if ($id == -1)
		$user = new User();
	else
		$user = User::find($id);

	// check if username is available
	if (!User::isUnique('name', $name, $user->id))
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Le nom ' . $name . ' est déjà utilisé'));
		exit;
	}
	
	$user->name = $name;
	$user->pr_team_id = $team;
	$user->save();
	
	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Utilisateur enregistré', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Utilisateur enregistré'));

