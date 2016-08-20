<?php 
	require_once('../includes/init.php');
	
	$id = GETorPOST('id');
	$day = GETorPOST('pr_day_id');
	$Day = Day::find($day);
	$homes = GETorPOST('pr_home_team_id');
	$aways = GETorPOST('pr_away_team_id');
	
	if (empty($homes['exist']))
		$homes['exist'] = array();
	if (empty($homes['new']))
		$homes['new'] = array();
	
	if (empty($aways['exist']))
		$aways['exist'] = array();
	if (empty($aways['new']))
		$aways['new'] = array();

	if (!$Day)
	{
		echo json_encode(array('success' => 0, 'message' => "Journée invalide."));
		exit;
	}

	$allHomes = array_merge($homes['new'], $homes['exist']);
	$allAways = array_merge($aways['new'], $aways['exist']);

	// check post data if a team is used several times
	if (count($allHomes) != count(array_unique($allHomes))
		|| count($allAways) != count(array_unique($allAways))
		|| count(array_merge($allHomes, $allAways)) != count(array_unique(array_merge($allHomes, $allAways))))
	{
		echo json_encode(array('success' => 0, 'message' => "Chaque équipe ne peut être utilisée qu'une fois par journée."));
		exit;
	}

	// update existing matches
	foreach ($homes['exist'] as $matchId => $homeTeamId)
	{
		$awayTeamId = $aways['exist'][$matchId];

		$match = Match::find($matchId);
		if (!$match)
			continue;
		$match->pr_day_id = $day;
		$match->pr_home_team_id = $homeTeamId;
		$match->pr_away_team_id = $awayTeamId;
		$match->save();
	}

	// create new matches
	foreach ($homes['new'] as $matchId => $homeTeamId)
	{
		$awayTeamId = $aways['new'][$matchId];

		$match = new Match();
		$match->pr_day_id = $day;
		$match->pr_home_team_id = $homeTeamId;
		$match->pr_away_team_id = $awayTeamId;
		$match->save();
	}
	
	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Matches enregistrés', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Matches enregistrés'));
?>
