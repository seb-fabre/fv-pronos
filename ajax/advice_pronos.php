<?php
	require_once('../includes/init.php');

	$currentDay = Day::find($_GET['id']);

	$season = $currentDay->getSeason();

	$league = $season->getLeague();

	if (GETorPOST('sort'))
		$sort = GETorPOST('sort');
	else
		$sort = 'total';

	$days = $season->getDays();

	$teams = Team::getAll();

	$users = User::getAll();

	$pronos = $season->getPronos();

	$matches = $season->getMatchs();

	$currentDayMatches = $currentDay->getMatches();
	$currentDayMatchesIds = array_keys($currentDayMatches);

	$currentDayPronos = $currentDay->getPronos();

	$scoresWeights = array();

	$classement = $season->getClassement();

	$keys = array_keys($currentDayPronos);
	foreach ($keys as $k)
	{
		$prono = $currentDayPronos[$k];

		$displayed = $prono->home_goals . '-' . $prono->away_goals;

		if (empty($scoresWeights[$prono->pr_match_id][$displayed]))
			$scoresWeights[$prono->pr_match_id][$displayed] = 0;

		$scoresWeights[$prono->pr_match_id][$displayed] += $classement[$prono->pr_user_id];
	}

	$bbcode = '';
?>

		<h4 class="well">Pronos conseillés</h4>
		<table class="table">

<?php
	foreach ($scoresWeights as $matchId => $weights)
	{
		arsort($weights);

		$match = $currentDayMatches[$matchId];
		$homeTeam = $teams[$match->pr_home_team_id];
		$awayTeam = $teams[$match->pr_away_team_id];

		$bestWeight = reset($weights);
		$bestScore = key($weights);

		$sum = array_sum($weights);

		echo '<tr>';
		echo '	<td style="width: 40%">' . $homeTeam->name . '</td>';

		echo '	<td style="width: 30%"><b>';
		foreach ($weights as $d => $w)
		{
			if ($w < $bestWeight / 2 || $sum === 0)
				break;

			$percent = round($w * 100 / $sum);

			echo '<p>' . $d . ' (' . $percent . '%)</p>';
		}
		echo '	</b></td>';

		echo '	<td style="width: 40%">' . $awayTeam->name . '</td>';
		echo '</tr>';

		$bbcode .= $homeTeam->name . ' - ' . $awayTeam->name . ' : ' . $bestScore . PHP_EOL;
	}

?>
			</table>
			<textarea class="form-control"><?=$bbcode?></textarea>
