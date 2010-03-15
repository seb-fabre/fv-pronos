<?php
	session_start();
	
	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	$day = Day::find($_POST['id']);
	
	$pr_users = $_POST['pr_user_id'];
	
	$homes = $_POST['home_goals'];
	$aways = $_POST['away_goals'];
	
	$matches = $day->getMatches();
	
	$users = User::getAll();
	
	foreach ($homes as $user => $home_matches)
	{
		if (!isset($users[$user]))
			$userId = $pr_users[$user];
		else
			$userId = $user;
		
		$away_matches = $aways[$user];
		
		$pronos = Prono::findByDayUser($day->id, $userId);
		$tmp = array();
		foreach ($pronos as $prono)
			$tmp[$prono->pr_user_id][$prono->pr_match_id] = $prono;
		$pronos = $tmp;
		
		foreach ($home_matches as $match => $home_goals)
		{
			$away_goals = $away_matches[$match];
			
			if (isset($pronos[$userId][$match]))
				$prono = $pronos[$userId][$match];
			else
				$prono = new Prono(array('pr_match_id' => $match, 'pr_user_id' => $userId));
			
			$prono->home_goals = $home_goals;
			$prono->away_goals = $away_goals;
			$prono->save();
		}
	}
	
	echo json_encode(array('success' => 1));
?>