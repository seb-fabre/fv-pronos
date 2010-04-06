<?php
	require_once('../includes/init.php');

	function getMatchRow(&$teams, $homeId, $awayId, $isEditable = true)
	{
		$select = '<p class="center"><select name="pr_home_team_id[]"' . (!$isEditable ? ' disabled="disabled"' : '') . '>';
		foreach ($teams as $team)
			$select .= '<option value="' . $team->id . '"' . ($team->id == $homeId ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		$select .= '</select>&nbsp;-&nbsp;<select name="pr_away_team_id[]"' . (!$isEditable ? ' disabled="disabled"' : '') . '>';
		foreach ($teams as $team)
			$select .= '<option value="' . $team->id . '"' . ($team->id == $awayId ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		$select .= '</select></p>';

		return $select;
	}

	$day = Day::find(GETorPOST('pr_day_id'));

	$season = Season::find($day->pr_season_id);;

	$league = $season->getLeague();

	$teams = $season->getTeams();

	$matches = $day->getMatches();

	$isEditable = !$day->hasPronos() && !$day->hasCompletedMatches();

	$select = '';
	foreach ($matches as $m)
	{
		$select .= getMatchRow($teams, $m->pr_home_team_id, $m->pr_away_team_id, $isEditable);
	}
?>
<div class="hidden" id="selectTeams"><?php echo getMatchRow($teams, -1, -1) ?></div>
<form action="/ajax/save_match.php" method="get">
	<fieldset>
		<legend>Modifier les matches</legend>
		<p class="center bold"><?php echo $league->name ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<div id="matches"><?php echo $select ?></div>
		<?php if ($isEditable && isset($_SESSION['user'])) { ?>
			<p class="center"><input type="button" onclick="addMatch()" value="ajouter un match" /></p>
		<?php } ?>
		<p class="submit">
			<?php if ($isEditable && isset($_SESSION['user'])) { ?>
				<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
				<input type="hidden" name="pr_day_id" value="<?php echo GETorPOST('pr_day_id') ?>" />
				<input type="submit" value="enregistrer" />
				<input type="button" value="annuler" onclick="$.modal.close()" />
			<?php } else { ?>
				<input type="button" value="fermer" onclick="$.modal.close()" />
			<?php } ?>
		</p>
	</fieldset>
</form>