<?php
	require_once('../includes/init.php');

	$season = Season::find(GETorPOST('id'));

	$league = $season->getLeague();

	$seasonTeams = array_values($season->getTeams());
	
	$teams = $league->getAvailableTeams();

	$isEditable = !$season->hasMatches();
?>
<form action="<?=APPLICATION_URL?>ajax/save_season_teams.php" method="get" id="ajaxForm" class="form-horizontal">
	
	<h4 class="well"><?php echo $league->name . ', ' . $season->label ?> : Equipes</h4>
	
	<div class="panel-body">
	
		<table class="table table-condensed">
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
					echo '<div class="row form-group-sm">';
				
				echo '<div class="col-sm-6">';

				if ($isEditable)
					echo '<select name="team[]" class="form-control">';
				else
					echo '<select name="team[]" disabled="disabled" class="form-control">';

				foreach ($teams as $t)
				{
					if ($t->id == $team->id)
						echo '<option value="' . $t->id . '" selected="selected">' . $t->name . '</option>';
					else
						echo '<option value="' . $t->id . '">' . $t->name . '</option>';
				}
				
				echo '</select>';
				echo '</div>';
				
				if ($i%2 == 1)
					echo '</div>';
			}
			if ($i%2 == 1)
				echo '</tr>';
		?>
		</table>
		
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-8">
				<?php if ($isEditable && isset($_SESSION['user'])) { ?>
					<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
				<button type="submit" class="btn btn-default btn-sm" onclick="saveSeasonTeams()">Enregistrer</button>
					<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
				<?php } else { ?>
					<button type="button" class="btn btn-default btn-sm nyroModalClose">Fermer</button>
				<?php } ?>
			</div>
		</div>

	</div>
</form>