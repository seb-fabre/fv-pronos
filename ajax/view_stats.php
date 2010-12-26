<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');

	$season = Season::find($id);
	$teams = $season->getTeams();

	$sql = " SELECT match_id, home_team, away_team, SUM(CASE WHEN three_points THEN 3 WHEN one_point THEN 1 ELSE 0 END) score
					FROM
					(
						SELECT
							pr_match.id match_id,
							pr_home_team_id home_team,
							pr_away_team_id away_team,
							SIGN(CONVERT(pr_match.home_goals, SIGNED) -
							CONVERT(pr_match.away_goals, SIGNED)) =
							SIGN(CONVERT(pr_prono.home_goals, SIGNED) -
							CONVERT(pr_prono.away_goals, SIGNED)) one_point,
							IF(pr_match.home_goals = pr_prono.home_goals AND pr_match.away_goals =
							pr_prono.away_goals, 3, 0) three_points
						FROM `pr_prono`
						INNER JOIN pr_match ON pr_prono.pr_match_id=pr_match.id
						INNER JOIN pr_day ON pr_day.id=pr_day_id AND pr_season_id=$id
					) t
					GROUP BY match_id";

	$result = mysql_query($sql);

	$teamsScores = array();

	while ($res = mysql_fetch_array($result))
	{
		if (empty($teamsScores[$res['home_team']]))
			$teamsScores[$res['home_team']] = 0;

		if (empty($teamsScores[$res['away_team']]))
			$teamsScores[$res['away_team']] = 0;

		$teamsScores[$res['home_team']] += $res['score'];
		$teamsScores[$res['away_team']] += $res['score'];
	}

	arsort($teamsScores);

?>

<fieldset>
	<legend>Equipes les plus généreuses</legend>

	<p><i>(Equipes ayant rapporté le plus de points aux pronostiqueurs sur l'ensemble de la saison)</i></p>

	<table style="width: 100%;">

		<?php foreach ($teamsScores as $team => $s) { ?>
			<tr>
				<td><?= $teams[$team]->name?></td>
				<td><?= $s?></td>
			</tr>
		<?php } ?>

	</table>

</fieldset>
