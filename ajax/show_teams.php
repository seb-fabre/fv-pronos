<?php
	require_once('../includes/init.php');

	$season = Season::find(GETorPOST('id'));

	$league = $season->getLeague();

	$seasonTeams = array_values($season->getTeams());
	
	$teams = Team::getAll('name ASC');

	$isEditable = !$season->hasMatches();
?>
<p id="popup_message" style="margin: 0; padding: 0;"></p>
<form action="/ajax/save_season_teams.php" method="get" id="ajaxForm">
	<fieldset>
		<legend><?php echo $league->name . ', ' . $season->label ?> : Equipes</legend>
		<table>
		<?php
			for ($i=0; $i<$season->teams; $i++)
			{
				if ($i%2 == 0)
					$j = $i / 2;
				else
					$j = floor($season->teams / 2) + floor($i / 2);

				if (isset($seasonTeams[$j]))
					$team = $seasonTeams[$j];
				else
					$team = new Team();
				if ($i%2 == 0)
					echo '<tr class="noborder">';
				echo '<td class="center">';

				if ($isEditable)
					echo '<select name="team[]">';
				else
					echo '<select name="team[]" disabled="disabled">';

				foreach ($teams as $t)
				{
					if ($t->id == $team->id)
						echo '<option value="' . $t->id . '" selected="selected">' . $t->name . '</option>';
					else
						echo '<option value="' . $t->id . '">' . $t->name . '</option>';
				}
				echo '</select>';
				echo '</td>';
				if ($i%2 == 1)
					echo '</tr>';
			}
			if ($i%2 == 1)
				echo '</tr>';
		?>
		</table>
		<p class="submit">
			<?php if ($isEditable && isset($_SESSION['user'])) { ?>
				<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
				<input type="button" value="enregistrer" onclick="saveSeasonTeams()" />
				<input type="button" value="annuler" class="nyroModalClose" />
			<?php } else { ?>
				<input type="button" value="fermer" class="nyroModalClose" />
			<?php } ?>
		</p>

</fieldset>
</form>