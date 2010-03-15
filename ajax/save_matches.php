<?php
	session_start();

	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	$id = $_GET['id'];
	$day = $_GET['pr_day_id'];
	$matches = $_GET['matches'];

	$season = Season::find($id);

	$league = $season->getLeague();

	$teams = $season->getTeams();
	$tmpTeams = array();
	foreach ($teams as $team)
		$tmpTeams [$team->name] = $team;
	$teams = $tmpTeams;

	$day = Day::find($day);

	$matches = explode("\n", $matches);
	$parsedData = array();
	foreach ($matches as $match)
	{
		$tab = split(' +- +', $match);
		if (count($tab) != 2)
			continue;
		$home = trim($tab[0]);
		$away = trim($tab[1]);

		$v = array();
		if (array_key_exists($home, $tmpTeams))
			$v []= $tmpTeams[$home]->id;
		else
			$v []= null;

		if (array_key_exists($away, $tmpTeams))
			$v []= $tmpTeams[$away]->id;
		else
			$v []= null;

		$parsedData []= $v;
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
