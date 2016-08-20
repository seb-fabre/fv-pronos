<?php
	require_once('../includes/init.php');

	$leagues = League::getAll();

	$seasons = Season::getAll();

	$id = GETorPOST('id');
	$day = Day::find($id);
	
	if (!$day)
	{
		$day = new Day();
		$isEditable = true;
	}
	else
	{
		$isEditable = !$day->hasPronos() && !$day->hasCompletedMatches() && !empty($_SESSION['user']);
	}

	$limitDate = $day->limit_date;
	
	if (!empty($limitDate))
	{
		$limitTime = substr($limitDate, 11, 5);

		$parts = explode('-', substr($limitDate, 0, 10));
		$limitDate = $parts[2] . '/' . $parts[1] . '/' . $parts[0];
	}
	else
	{
		$limitDate = '';
		$limitTime = '';
	}

	$countMatches = $day->count_matches;
?>
<form action="/ajax/save_team.php" method="get" id="ajaxForm" class="form-horizontal">
		
	<h4 class="well">
	<?php 
		if (GETorPOST('id') != -1) 
			echo "Edition d'une journée";
		else
			echo "Ajout d'une journée";
	?>
	</h4>

	<div class="panel-body">

		<div class="form-group">
			<label class="col-sm-2 control-label">Championnat</label>
			<div class="col-sm-10">
				<select name="pr_season_id" class="form-control">
					<?php foreach ($seasons as $season) echo '<option value="' . $season->id . '"' . ($season->id == $day->pr_season_id ? ' selected="selected"' : '') . '>' . $leagues[$season->pr_league_id]->name . ' - ' . $season->label . '</option>'; ?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">Numéro</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="number" value="<?php echo $day->number ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">Label</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="label" value="<?php echo $day->label ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">Nombre de matches</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="count_matches" id="count_matches" value="<?php echo $countMatches ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">Date limite</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="limit_date" value="<?php echo $limitDate ?>" />
			</div>
		</div>

		<div class="alert alert-info">Seul l'administrateur pourra saisir des pronostics après la limite. Cette date peut être surchargée au niveau de chaque match.</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">Heure limite</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="limit_time" value="<?php echo $limitTime ?>" />
			</div>
		</div>

		<div class="alert alert-info">Format acceptés pour l'heure : "9:15", ou "9"</div>

		<div class="submit">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<?php if ($isEditable) { ?>
				<button type="subbmit" class="btn btn-default btn-sm"<?=(count($seasons)==0 ? ' disabled="disabled"' : '')?>>Enregistrer</button>
			<?php } ?>
			<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
		</div>
	</div>
</form>

<script type="text/javascript">
$("#limit_date").datepicker();

<?php if (!empty($_SESSION['user'])) { ?>

		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_day.php',
			dataType: 'json',
			success: function (response) {
				if (response.success == 1)
					window.location.reload();
				else
					$('#popup_message').html(response.message);
				resizeModal();
			}
		});

	<?php } ?>
</script>
