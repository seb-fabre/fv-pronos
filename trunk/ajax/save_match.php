<?php 
	require_once('../includes/init.php');
	
	$id = GETorPOST('id');
	$day = GETorPOST('pr_day_id');
	$Day = Day::find($day);
	$homes = GETorPOST('pr_home_team_id');
	$aways = GETorPOST('pr_away_team_id');
	
	
	// check if one of the teams is already booked for this day
	$query = mysql_query('SELECT * FROM pr_match WHERE pr_day_id=' . $day . ' AND (pr_home_team_id IN (' . implode(', ', array_merge($homes, $aways)) . ') OR pr_away_team_id IN (' . implode(', ', array_merge($homes, $aways)) . '))');
	if (mysql_num_rows($query) != 0)
	{
		echo json_encode(array('success' => 0, 'message' => 'Une des équipes a déjà un match prévu pour cette journée.'));
		exit;
	}
	
	foreach ($homes as $key => $home)
	{
		$away = $aways[$key];
		
		if ($home == $away)
		{
			echo json_encode(array('success' => 0, 'message' => "Une équipe ne peut pas jouer en même temps à domicile et à l'extérieur."));
			exit;
		}
	}
	
	foreach ($homes as $key => $home)
	{
		$away = $aways[$key];
		
		$match = new Match();
		$match->pr_day_id = $day;
		$match->pr_home_team_id = $home;
		$match->pr_away_team_id = $away;
		$match->save();
	}
	
	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Championnat enregistré', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Championnat enregistré'));
?>
