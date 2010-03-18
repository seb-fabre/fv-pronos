<?php 
	require_once('../includes/init.php');
	
	$day = Day::find($_GET['id']);
	
	$user = User::find($_GET['user']);
	
	$home_goals = $_GET['home_goals'];
	$away_goals = $_GET['away_goals'];
	
	$digits = array(0,1,2,3,4,5,6,7,8,9);
	
	// check positive int values
	foreach ($home_goals as $goals)
		if ($goals && !ctype_digit($goals))
		{
			echo json_encode(array('success' => 0, 'message' => 'Nombre de buts invalide.'));
			exit;
		}
	foreach ($away_goals as $goals)
		if ($goals && !ctype_digit($goals))
		{
			echo json_encode(array('success' => 0, 'message' => 'Nombre de buts invalide.'));
			exit;
		}
	
	$matches = $day->getMatches();
	
	$pronos = Prono::findByDayUser($day->id, $user->id);
	$tmp = array();
	foreach ($pronos as $prono)
		$tmp[$prono->pr_match_id] = $prono;
	$pronos = $tmp;
	
	foreach ($matches as $match)
	{
		if (!array_key_exists($match->id, $pronos))
			$prono = new Prono(array('pr_match_id' => $match->id, 'pr_user_id' => $user->id));
		else
			$prono = $pronos[$match->id];
		
		if (!array_key_exists($match->id, $home_goals) || !array_key_exists($match->id, $away_goals))
			continue;
		$prono->home_goals = $home_goals[$match->id];
		$prono->away_goals = $away_goals[$match->id];
		$prono->save();
	}
	echo json_encode(array('success' => 1));
?>