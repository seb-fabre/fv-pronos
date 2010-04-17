<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');
	$matches = GETorPOST('matches');

	$day = Day::find($id);

	$season = $day->getSeason();

	$league = $season->getLeague();

	$users = User::getAll('name asc');
	$tmp = array();
	foreach ($users as $user)
		$tmp[strtolower($user->name)] = $user->id;
	$namedUsers = $tmp;

	$teams = $season->getTeams();
	$tmpTeams = array();
	foreach ($teams as $team)
		$tmpTeams [strtolower($team->name)] = $team;
	$titims = $teams;
	$teams = $tmpTeams;

	$matches = explode("\n", $matches);
	$parsedData = array();
	$currentUser = false;
	foreach ($matches as $match)
	{
		$line = array();
		$match = strtolower($match);
		if (preg_match('/(.*) - (.*) *(arrow.gif|:) ([0-9]) ?- ?([0-9]).*/', $match, $line) == 0)
			preg_match('/(.*) - (.*)( )([0-9]) ?- ?([0-9]).*/', $match, $line);

		// line containing scores
		if (count($line) == 6)
		{
			$data = array();

			if (array_key_exists(trim($line[1]), $tmpTeams))
				$data['home_team'] = $tmpTeams[trim($line[1])]->id;
			else
				$data['home_team'] = null;

			if (array_key_exists(trim($line[2]), $tmpTeams))
				$data['away_team'] = $tmpTeams[trim($line[2])]->id;
			else
				$data['away_team'] = null;

			$data['home_goals'] = $line[4];
			$data['away_goals'] = $line[5];

			$parsedData[$currentUser] []= $data;
		}
		else if (preg_match('/[Pp]ronos? +(.*)/', $match, $line))
		{
			if (array_key_exists(strtolower($line[1]), $namedUsers))
				$currentUser = $namedUsers[strtolower($line[1])];
			else
				$currentUser = strtolower($line[1]);

			$parsedData[$currentUser] = array();
		}
	}

	$matches = $day->getMatches();
	$tmp = array();
	foreach ($matches as $match)
		$tmp[$match->pr_home_team_id][$match->pr_away_team_id] = $match->id;
	$matches = $tmp;

	echo '<table class="noborder" style="width: 100%">';
	
	foreach ($parsedData as $user => $scores)
	{
		echo '<tr><td colspan="3" align="center" style="text-align: center">';
		echo '<select name="pr_user_id[' . $user . ']">';
		echo '<option value="-1"> --- </option>';
		foreach ($users as $u)
			echo '<option value="' . $u->id . '"' . ($u->id == $user ? ' selected="selected"' : '') . '>' . $u->name . '</option>';
		echo '</select>';
		if (!array_key_exists($user, $users))
			echo ' (' . $user . ')';
		echo '</p>';
		echo '</td></tr>';

		foreach ($scores as $score)
		{
			if (!isset($matches[$score['home_team']][$score['away_team']]))
				continue;
			$match = $matches[$score['home_team']][$score['away_team']];
			echo '<tr>';
			echo '<td class="right">' . $titims[$score['home_team']]->name . '</td>';
			echo '<td class="center" style="width: 100px;">';
			echo '<input type="text" name="home_goals[' . $user . '][' . $match . ']" value="' . $score['home_goals'] . '" size="2" maxlength="1" />&nbsp;-&nbsp;';
			echo '<input type="text" name="away_goals[' . $user . '][' . $match . ']" value="' . $score['away_goals'] . '" size="2" maxlength="1" />&nbsp';
			echo '</td>';
			echo '<td>' . $titims[$score['away_team']]->name . '</td>';
			echo '</tr>';
		}
		echo '<tr><td colspan="3"><hr/></td></tr>';
	}
	echo '</table>';
?>
