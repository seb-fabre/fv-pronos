<?php
	require_once('../includes/init.php');

	$day = Day::find(GETorPOST('id'));
	
	$pr_users = GETorPOST('pr_user_id');
	
	$homes = GETorPOST('home_goals');
	$aways = GETorPOST('away_goals');
	
	$matches = $day->getMatches();
	
	$users = User::getAll();
	
	foreach ($homes as $user => $home_matches)
	{
		if (isset($users[$user]))
			$userId = $users[$user]->id;
		else if (isset($pr_users[$user]))
			$userId = $pr_users[$user];
		
		if ($userId <= 0)
		{
			echo json_encode(array('success' => 0, 'message' => 'Au moins un utilisateur est vide'));
			exit;
		}
		
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
