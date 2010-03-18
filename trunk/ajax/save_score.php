<?php
	require_once('../includes/init.php');

	$id = $_GET['id'];
	$scores = $_GET['matches'];

	$day = Day::find($id);

	$league = League::find($day->pr_league_id);

	$matches = $day->getMatches();

	$teams = $season->getTeams();
	$tmpTeams = array();
	foreach ($teams as $team)
		$tmpTeams [$team->name] = $team;
	$teams = $tmpTeams;

	$scores = explode("\n", $scores);
	$parsedData = array();
	$goals = array();
	foreach ($scores as $score)
	{
		preg_match('/^(.*) +([0-9]) *- *([0-9]) +(.*)/', $score, $data);

		if (count($data) != 5)
			continue;

		array_shift($data);

		$v = array();
		$g = array();

		if (array_key_exists($data[0], $tmpTeams))
			$v []= $tmpTeams[$data[0]]->id;
		else
			$v []= null;
		$g []= $data[1];

		if (array_key_exists($data[3], $tmpTeams))
			$v []= $tmpTeams[$data[3]]->id;
		else
			$v []= null;
		$g []= $data[2];

		$parsedData []= $v;
		$goals []= $g;
	}

	$tmp = array();
	foreach ($matches as $match)
	{
		$tmp[$match->pr_home_team_id][$match->pr_away_team_id] = $match->id;
	}

	foreach ($parsedData as $i => $score)
	{
		if (!isset($tmp[$score[0]][$score[1]]))
			continue;;
		$match = $tmp[$score[0]][$score[1]];

		echo '<p class="center"><select name="pr_home_team_id[]">';
		echo '<option value="-1"> --- </option>';
		foreach ($teams as $team)
			echo '<option value="' . $team->id . '"' . ($team->id == $score[0] ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		echo '</select>&nbsp;';
		echo '<input type="text" name="home_goals[' . $match . ']" value="' . $goals[$i][0] . '" size="2" maxlength="1" />&nbsp;-&nbsp;';
		echo '<input type="text" name="away_goals[' . $match . ']" value="' . $goals[$i][1] . '" size="2" maxlength="1" />&nbsp;';
		echo '<select name="pr_away_team_id[]">';
		echo '<option value="-1"> --- </option>';
		foreach ($teams as $team)
			echo '<option value="' . $team->id . '"' . ($team->id == $score[1] ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		echo '</select></p>';
	}
?>