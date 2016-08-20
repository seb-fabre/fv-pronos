<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');
	$lines = GETorPOST('matches');

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
	{
		$tmpTeams[strtolower($team->name)] = $team;
		foreach ($team->getAliases() as $alias)
			$tmpTeams[strtolower($alias)] = $team;
	}
	$titims = $teams;
	$teams = $tmpTeams;
	
	$teamsRegex = implode('|', array_keys($teams));
	
	$lines = explode("\n", $lines);
	array_unshift($lines, ''); // add fake element to avoid skipping the first line with next()
	$parsedData = array();
	$currentUser = false;
	$moreUsers = 0;
	
	//foreach ($lines as $oneLine)
	while (($oneLine = next($lines)) !== false)
	{
		$matches = array();
		$data = array();
		$oneLine = strtolower($oneLine);
		$parsedUser = false;
		
		if ($oneLine == 'options v')
		{
			$parsedUser = strtolower(next($lines));
		}
		
		if ($oneLine == '+quote post')
		{
			$parsedUser = strtolower(next($lines));
		}
		
		if (preg_match('/[Pp]ronos? +(.*):?/', $oneLine, $matches))
		{
			$parsedUser = strtolower($matches[1]);
		}
		
		if ($parsedUser)
		{
			if (array_key_exists($parsedUser, $namedUsers))
				$currentUser = $namedUsers[$parsedUser];
			else
				$currentUser = $parsedUser;
				
			if (empty($parsedData[$currentUser]))
				$parsedData[$currentUser] = array();
		}
		
		if (preg_match("/($teamsRegex).+($teamsRegex) *[^ ]*[^a-z]+[^ ]* *([0-9]) ?\W ?([0-9]).*/", $oneLine, $matches))
		{
			if (count($matches) !== 5)
				continue;
			
			$data['home_goals'] = $matches[3];
			$data['away_goals'] = $matches[4];
			
			$data['home_team'] = $tmpTeams[trim($matches[1])]->id;
			$data['away_team'] = $tmpTeams[trim($matches[2])]->id;
		}
		
		if (empty($data) && preg_match("/($teamsRegex).*([0-9]) ?- ?([0-9]).*($teamsRegex).*/", $oneLine, $matches))
		{
			if (count($matches) !== 5)
				continue;
			
			$data['home_goals'] = $matches[2];
			$data['away_goals'] = $matches[3];
			
			$data['home_team'] = $tmpTeams[trim($matches[1])]->id;
			$data['away_team'] = $tmpTeams[trim($matches[4])]->id;
		}
		
		if (empty($data))
			continue;
		
		$key = $data['home_team'] . '-' . $data['away_team'];
		
		if (isset($parsedData[$currentUser]) && isset($parsedData[$currentUser][$key]))
		{
			$moreUsers--;
			$currentUser = $moreUsers;
			$parsedData[$currentUser] = array();
		}
		
		$parsedData[$currentUser][$key] = $data;
	}

	$matches = $day->getMatches();
	$tmp = array();
	foreach ($matches as $match)
		$tmp[$match->pr_home_team_id][$match->pr_away_team_id] = $match->id;
	$matches = $tmp;

	echo '<form method="post" id="ajaxForm" class="nyroModal form-horizontal" action="' . APPLICATION_URL . 'ajax/save_prono.php">
		
		<h4 class="well">
			Parser les pronos
			<br/>
			<small>' . $league->name . ' - ' . $season->label . ', ' . $day->number . '<sup>e</sup> journée</small>
		</h4>';
	
	echo '<div class="panel-body">';
		
	foreach ($parsedData as $user => $scores)
	{
		if (empty($scores))
			continue;
		
		echo '<div class="row form-group">';
		echo '<div class="col-sm-6">';
		echo '<select name="pr_user_id[' . $user . ']" class="form-control">';
		echo '<option value="-1"> --- </option>';
		foreach ($users as $u)
			echo '<option value="' . $u->id . '"' . ($u->id == $user ? ' selected="selected"' : '') . '>' . $u->name . '</option>';
		echo '</select>';
		echo '</div>';
		echo '<div class="col-sm-6">&nbsp;</div>';
		echo '</div>';

		if (!array_key_exists($user, $users) && !is_numeric($user))
			echo ' (' . $user . ')';

		foreach ($scores as $score)
		{
			if (!isset($matches[$score['home_team']][$score['away_team']]))
				continue;

			$match = $matches[$score['home_team']][$score['away_team']];
			echo '<div class="row form-group-sm">';
			echo '<div class="col-sm-3">' . $titims[$score['home_team']]->name . '</div>';
			echo '<div class="col-sm-2">';
			echo '	<input type="text" name="home_goals[' . $user . '][' . $match . ']" value="' . $score['home_goals'] . '" size="2" maxlength="1" class="form-control" />';
			echo '</div>';
			echo '<div class="col-sm-1">&nbsp;-&nbsp;</div>';
			echo '<div class="col-sm-2">';
			echo '	<input type="text" name="away_goals[' . $user . '][' . $match . ']" value="' . $score['away_goals'] . '" size="2" maxlength="1" class="form-control" />';
			echo '</div>';
			echo '<div class="col-sm-3">' . $titims[$score['away_team']]->name . '</div>';
			echo '<div class="col-sm-1"></div>';
			echo '</div>';
		}
		
		echo '<hr/>';
	}
	
?>
		<div class="form-group">
			<input type="hidden" name="id" value="<?=$id?>" />
			<button type="subbmit" class="btn btn-default btn-sm">Enregistrer</button>
			<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
		</div>
	</div>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_parsed_pronos.php',
			dataType: 'json',
			beforeSubmit: function(){
				showSpinner();
			},
			method: 'post',
			success: function (response) {
				hideSpinner();
				if (response.success == 1)
					window.location.reload();
				else
				{
					$('#popup_message').html(response.message);
					$('#popup').modal({overlayClose: true});
				}
				resizeModal();
			}
		});
	</script>

<?php } ?>
