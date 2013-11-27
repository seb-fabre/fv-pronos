<?php
	require_once('../includes/init.php');

	function getMatchRow(&$teams, $match = false, $isEditable = true)
	{
		if (!empty($match) && $match->id)
		{
			$homeId = $match->pr_home_team_id;
			$awayId = $match->pr_away_team_id;
			$name = '[exist][' . $match->id . ']';
		}
		else if (!empty($match) && ($match->pr_home_team_id || $match->pr_away_team_id))
		{
			$homeId = $match->pr_home_team_id;
			$awayId = $match->pr_away_team_id;
			$name = '[new][]';
		}
		else
		{
			$homeId = -1;
			$awayId = -1;
			$name = '[new][]';
		}

		$select = '<p class="center"><select name="pr_home_team_id' . $name . '"' . (!$isEditable ? ' disabled="disabled"' : '') . '>';
		foreach ($teams as $team)
			$select .= '<option value="' . $team->id . '"' . ($team->id == $homeId ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		$select .= '</select>&nbsp;-&nbsp;<select name="pr_away_team_id' . $name . '"' . (!$isEditable ? ' disabled="disabled"' : '') . '>';
		foreach ($teams as $team)
			$select .= '<option value="' . $team->id . '"' . ($team->id == $awayId ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		$select .= '</select></p>';

		return $select;
	}

	$day = Day::find(GETorPOST('pr_day_id', -1));

	$season = Season::find($day->pr_season_id);;

	$league = $season->getLeague();

	$teams = $season->getTeams();
	$tmpTeams = array();
	foreach ($teams as $team)
		$tmpTeams [$team->name] = $team;
	$teams = $tmpTeams;

	$matches = GETorPOST('matches');
	$parsedData = array();

	if (!empty($matches))
	{
		$matches = explode("\n", $matches);
		foreach ($matches as $match)
		{
			$parts = explode(' ', $match);
			$parts = array_filter($parts);

			$found = array();

			foreach ($parts as $p)
			{
				$p = trim($p);

				if (isset($teams[$p]))
					$found []= $teams[$p]->id;
			}

			if (count($found) != 2)
				continue;

			$match = new Match(array('pr_home_team_id' => $found[0], 'pr_away_team_id' => $found[1]));

			$parsedData []= $match;
		}
	}

	$matches = $day->getMatches();

	$isEditable = !$day->hasPronos() && !$day->hasCompletedMatches();

	$select = '';
	foreach ($matches as $m)
		$select .= getMatchRow($teams, $m, $isEditable);

	foreach ($parsedData as $p)
		$select .= getMatchRow($teams, $p, $isEditable);

?>
<p id="popup_message" style="margin: 0; padding: 0;"></p>
<div class="hidden" id="selectTeams"><?php echo getMatchRow($teams) ?></div>
<form action="/ajax/save_match.php" method="post" id="ajaxForm">
	<fieldset>
		<legend>Modifier les matches</legend>
		<p class="center bold"><?php echo $league->name ?>, <?php echo $day->number ? $day->number . '<sup>e</sup> journée' : $day->label ?></p>
		<div id="matches"><?php echo $select ?></div>
		<?php if ($isEditable && isset($_SESSION['user'])) { ?>
			<p class="center"><input type="button" onclick="addMatch()" value="ajouter un match" /></p>
		<?php } ?>
		<p class="submit">
			<?php if ($isEditable && isset($_SESSION['user'])) { ?>
				<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
				<input type="hidden" name="pr_day_id" value="<?php echo GETorPOST('pr_day_id') ?>" />
				<input type="submit" value="enregistrer" />
				<input type="button" value="annuler" class="nyroModalClose" />
			<?php } else { ?>
				<input type="button" value="fermer" class="nyroModalClose" />
			<?php } ?>
		</p>
	</fieldset>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_match.php',
			dataType: 'json',
			method: 'post',
			success: function (response) {
				if (response.success == 1)
					window.location.reload();
				else
					$('#popup_message').html(response.message);
				resizeModal();
			}
		});
	</script>

<?php } ?>