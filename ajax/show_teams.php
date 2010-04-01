<?php
	require_once('../includes/init.php');

	$season = Season::find(GETorPOST('id'));

	$league = $season->getLeague();

	$seasonTeams = array_values($season->getTeams());
	
	$teams = Team::getAll();
?>
<form action="/ajax/save_season_teams.php" method="get">
	<fieldset>
		<legend><?php echo $league->name . ', ' . $season->label ?> : Equipes</legend>
		<table>
		<?php
			for ($i=0; $i<$league->teams; $i++)
			{
				if ($i%2 == 0)
					$j = $i / 2;
				else
					$j = floor($league->teams / 2) + floor($i / 2);

				if (isset($seasonTeams[$j]))
					$team = $seasonTeams[$j];
				else
					$team = new Team();
				if ($i%2 == 0)
					echo '<tr class="noborder">';
				echo '<td class="center">';
				echo '<select name="team[]">';
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
			<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="fermer" onclick="$.modal.close()" />
		</p>

</fieldset>
</form>