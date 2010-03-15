<?php
	session_start();
	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	function sortByPoints($x, $y)
	{
		global $teams;

		if ($x['points'] != $y['points'])
			return $x['points'] < $y['points'];

		if ($x['diff'] != $y['diff'])
			return $x['diff'] < $y['diff'];

		if ($x['victories'] != $y['victories'])
			return $x['victories'] < $y['victories'];

		return $teams[$x['id']]->name < $teams[$y['id']]->name;
	}

	$season = Season::find($_GET['id']);

	$league = $season->getLeague();

	$matches = $season->getMatches();

	$days = $season->getDays();

	$teams = $season->getTeams();

	$max = $_GET['max'];

	if ($max == -1)
	{
		foreach ($days as $day)
			if ($max < $day->number)
				$max = $day->number;
	}

	$req = mysql_query('SELECT pr_match.* FROM pr_match INNER JOIN pr_day ON pr_day_id=pr_day.id WHERE pr_season_id=' . $season->id . ' AND number <= ' . $max);

	$rankings = array();

	foreach ($teams as $team)
	{
		$rankings [$team->id] = array('id' => $team->id,
			'points' => 0, 'victories' => 0, 'nulls' => 0, 'diff' => 0, 'played' => 0, 'goals+' => 0, 'goals-' => 0,
			'home_points' => 0, 'home_victories' => 0, 'home_nulls' => 0, 'home_diff' => 0, 'home_played' => 0, 'home_goals+' => 0, 'home_goals-' => 0,
			'away_points' => 0, 'away_victories' => 0, 'away_nulls' => 0, 'away_diff' => 0, 'away_played' => 0, 'away_goals+' => 0, 'away_goals-' => 0
		);
	}

	while($res = mysql_fetch_array($req))
	{
		if (is_null($res['home_goals']) || is_null($res['away_goals']))
			continue;

		$home_goals = intval($res['home_goals']);
		$away_goals = intval($res['away_goals']);
		$diff = $home_goals - $away_goals;
		$home = $res['pr_home_team_id'];
		$away = $res['pr_away_team_id'];

		$rankings[$home]['diff'] += $diff;
		$rankings[$away]['diff'] -= $diff;
		$rankings[$home]['played'] ++;
		$rankings[$away]['played'] ++;
		$rankings[$home]['home_played'] ++;
		$rankings[$away]['away_played'] ++;

		$rankings[$home]['goals+'] += $home_goals;
		$rankings[$home]['goals-'] += $away_goals;
		$rankings[$home]['home_goals+'] += $home_goals;
		$rankings[$home]['home_goals-'] += $away_goals;

		$rankings[$away]['goals+'] += $away_goals;
		$rankings[$away]['goals-'] += $home_goals;
		$rankings[$away]['away_goals+'] += $away_goals;
		$rankings[$away]['away_goals-'] += $home_goals;

		if ($res['home_goals'] > $res['away_goals'])
		{
			$rankings[$home]['points'] += 3;
			$rankings[$home]['victories'] ++;
			$rankings[$home]['home_victories'] ++;
			$rankings[$home]['home_points'] +=3;
		}
		else if ($res['home_goals'] < $res['away_goals'])
		{
			$rankings[$away]['points'] += 3;
			$rankings[$away]['victories'] ++;
			$rankings[$away]['away_victories'] ++;
			$rankings[$away]['away_points'] ++;
		}
		else
		{
			$rankings[$home]['points'] ++;
			$rankings[$away]['points'] ++;
			$rankings[$home]['nulls'] ++;
			$rankings[$away]['nulls'] ++;
			$rankings[$home]['home_nulls'] ++;
			$rankings[$away]['away_nulls'] ++;
			$rankings[$home]['home_points'] ++;
			$rankings[$away]['away_points'] ++;
		}
	}

	uasort($rankings, 'sortByPoints');

	echo 'choisir une journée : ';
	echo '<select onchange="showRankings(' . $league->id . ', $(this).val());">';
	foreach ($days as $day)
		echo '<option value="' . $day->number . '"' . ($day->number == $max ? ' selected="selected"' : '') . '>' . $day->number . '</option>';
	echo '</select>';

	echo '<table id="rankings">';
	echo '<tr><th>&nbsp;</th><th>Equipe</th><th>Joués</th><th>Points</th><th>Victoires</th><th>Nuls</th><th>Buts pour</th><th>Buts contre</th><th>Diff</th></tr>';
	$i = 1;
	foreach ($rankings as $team => $rank)
	{
		echo '<tr>';
		echo '<td>' . $i . '</td>';
		echo '<td>' . $teams[$team]->name . '</td>';
		echo '<td class="right">' . $rank['played'] . '</td>';
		echo '<td class="right">' . $rank['points'] . '</td>';
		echo '<td class="right">' . $rank['victories'] . '</td>';
		echo '<td class="right">' . $rank['nulls'] . '</td>';
		echo '<td class="right">' . $rank['goals+'] . '</td>';
		echo '<td class="right">' . $rank['goals-'] . '</td>';
		echo '<td class="right">' . $rank['diff'] . '</td>';
		echo '</tr>';
		$i++;
	}
	echo '</table>';
?>
<p class="center">
<input type="button" value="fermer" onclick="$.modal.close()" />
</p>