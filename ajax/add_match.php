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

		$select = '<div class="col-md-6 text-center"><div class="col-xs-5"><select name="pr_home_team_id' . $name . '"' . (!$isEditable ? ' disabled="disabled"' : '') . ' class="form-control">';
		foreach ($teams as $team)
			$select .= '<option value="' . $team->id . '"' . ($team->id == $homeId ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		$select .= '</select></div>'
						. '<div class="col-xs-1 text-center">-</div>'
						. '<div class="col-xs-5"><select name="pr_away_team_id' . $name . '"' . (!$isEditable ? ' disabled="disabled"' : '') . ' class="form-control">';
		foreach ($teams as $team)
			$select .= '<option value="' . $team->id . '"' . ($team->id == $awayId ? ' selected="selected"' : '') . '>' . $team->name . '</option>';
		$select .= '</select></div><div class="col-xs-1"> </div></div>';

		return $select;
	}

	$day = Day::find(GETorPOST('pr_day_id', -1));
	$matches = GETorPOST('matches');

	$season = Season::find($day->pr_season_id);;

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
			
			$match = new Match(array('pr_home_team_id' => $v[0], 'pr_away_team_id' => $v[1]));

			$parsedData []= $match;
		}
	}
	
	$matches = $day->getMatches();

	$isEditable = !$day->hasPronos() && !$day->hasCompletedMatches();

	$select = '';
	foreach ($matches as $m)
		$select .= getMatchRow($titims, $m, $isEditable);

	foreach ($parsedData as $p)
		$select .= getMatchRow($titims, $p, $isEditable);

?>
<div class="hidden" id="selectTeams"><?php echo getMatchRow($titims) ?></div>
<form action="/ajax/save_match.php" method="post" id="ajaxForm" class="form-horizontal" style="width:800px">

	<h4 class="well">
		Modifier les matches
		<br/>
		<small><?php echo $league->name ?>, <?php echo $day->number ? $day->number . '<sup>e</sup> journée' : $day->label ?></small>
	</h4>

	<div class="panel-body">

		<div id="matches" class="container-fluid"><div class="row"><?php echo $select ?></div></div>

		<br/><br/>

		<p class="submit">
			<?php if ($isEditable && isset($_SESSION['user'])) { ?>
				<button type="button" class="btn btn-default btn-sm" onclick="addMatch()">Ajouter un match</button>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
				<input type="hidden" name="pr_day_id" value="<?php echo GETorPOST('pr_day_id') ?>" />
				<button type="submit" class="btn btn-default btn-sm">Enregistrer</button>
				<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
			<?php } else { ?>
				<button type="button" class="btn btn-default btn-sm nyroModalClose">Fermer</button>
			<?php } ?>
		</p>
	</div>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_match.php',
			dataType: 'json',
			method: 'post',
			success: function (response) {
				if (response.success == 1)
				{
					window.location.reload();
					return;
				}
				
				showError(response.message);
				resizeModal();
			}
		});
	</script>

<?php } ?>