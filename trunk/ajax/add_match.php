<?php
	require_once('../includes/init.php');

	$day = Day::find($_GET['pr_day_id']);

	$season = Season::find($day->pr_season_id);;

	$league = $season->getLeague();

	$teams = $season->getTeams();

	$match = Match::find($_GET['id']);
	if (!$match)
		$match = new Match();

	$select = '<p class="center"><select name="pr_home_team_id[]">';
	foreach ($teams as $team)
		$select .= '<option value="' . $team->id . '"' . ($team->id == $match->pr_home_team_id ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
	$select .= '</select>&nbsp;-&nbsp;<select name="pr_away_team_id[]">';
	foreach ($teams as $team)
		$select .= '<option value="' . $team->id . '"' . ($team->id == $match->pr_away_team_id ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
	$select .= '</select></p>';
?>
<div class="hidden" id="selectTeams"><?php echo $select ?></div>
<form action="/ajax/save_match.php" method="get">
	<fieldset>
		<legend>Ajout de matches</legend>
		<p class="center bold"><?php echo $league->name ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<div id="matches"><?php echo $select ?></div>
		<p class="center"><input type="button" onclick="addMatch()" value="ajouter un match" /></p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
			<input type="hidden" name="pr_day_id" value="<?php echo $_GET['pr_day_id'] ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>