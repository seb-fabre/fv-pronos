<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');
	$day = GETorPOST('pr_day_id');
	$matches = GETorPOST('matches');
	
	$season = Season::find($id);

	$league = $season->getLeague();

	$teams = $season->getTeams();
	$tmpTeams = array();
	foreach ($teams as $team)
	{
		$tmpTeams[strtolower($team->name)] = $team;
		foreach ($team->getAliases() as $alias)
			$tmpTeams[strtolower($alias)] = $team;
	}
	$titims = $teams;
	$teams = $tmpTeams;
	
	$teamsRegex = implode('|', array_keys($teams));
	

	$day = Day::find($day);

	$lines = explode("\n", $matches);
	$parsedData = array();
	foreach ($lines as $oneLine)
	{
		$oneLine = strtolower($oneLine);
		
		if (preg_match("/.*($teamsRegex).+($teamsRegex).*/", $oneLine, $matches))
		{
			$v = array();
			$v []= $tmpTeams[trim($matches[1])]->id;
			$v []= $tmpTeams[trim($matches[2])]->id;
			
			$parsedData []= $v;
		}
	}

	foreach ($parsedData as $match)
	{
		echo '<p class="center"><select name="pr_home_team_id[]">';
		echo '<option value="-1"> --- </option>';
		foreach ($teams as $team)
			echo '<option value="' . $team->id . '"' . ($team->id == $match[0] ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		echo '</select>&nbsp;-&nbsp;<select name="pr_away_team_id[]">';
		echo '<option value="-1"> --- </option>';
		foreach ($teams as $team)
			echo '<option value="' . $team->id . '"' . ($team->id == $match[1] ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		echo '</select></p>';
	}
?>
