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
	$parsedData = array();
	$currentUser = false;
	
	foreach ($lines as $oneLine)
	{
		$matches = array();
		$oneLine = strtolower($oneLine);
		
		if (preg_match('/[Pp]ronos? +(.*)/', $oneLine, $matches))
		{
			if (array_key_exists(strtolower($matches[1]), $namedUsers))
				$currentUser = $namedUsers[strtolower($matches[1])];
			else
				$currentUser = strtolower($matches[1]);

			$parsedData[$currentUser] = array();
			continue;
		}
		
		if (preg_match('/(.*) *[^ ]*[^a-z]+[^ ]* *([0-9]) ?- ?([0-9]).*/', $oneLine, $matches))
		{
			if (count($matches) !== 4)
				continue;
			
			$data['home_goals'] = $matches[2];
			$data['away_goals'] = $matches[3];
			
			$strTeams = $matches[1];
			
			if (!preg_match("/($teamsRegex).+($teamsRegex)/", $strTeams, $submatches))
				continue;
			
			$data['home_team'] = $tmpTeams[trim($submatches[1])]->id;
			$data['away_team'] = $tmpTeams[trim($submatches[2])]->id;
			
			$parsedData[$currentUser] []= $data;
		}
	}

	$matches = $day->getMatches();
	$tmp = array();
	foreach ($matches as $match)
		$tmp[$match->pr_home_team_id][$match->pr_away_team_id] = $match->id;
	$matches = $tmp;

	echo '<p id="popup_message" style="margin: 0; padding: 0;"></p>
<form method="post" id="ajaxForm" class="nyroModal" action="' . APPLICATION_URL . 'ajax/save_prono.php">
	<fieldset>
		<legend>Parser les pronos</legend>
		<p class="center bold">' . $league->name . ' - ' . $season->label . ', ' . $day->number . '<sup>e</sup> journée</p>
		<p class="center">';

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
		<p class="submit">
			<input type="hidden" name="id" value="<?=$id?>" />
			<input type="submit" value="parser" />
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
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
				if (response.success == 1)
					window.location.reload();
				else
				{
					$('#popup_message').html(response.message);
					hideSpinner();
				}
				resizeModal();
			}
		});
	</script>

<?php } ?>
