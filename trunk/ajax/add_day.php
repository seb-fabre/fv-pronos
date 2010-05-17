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
<form action="/ajax/save_team.php" method="post"id="ajaxForm">
	<fieldset>
		<?php if ($id != -1): ?>
			<legend>Edition d'une journée</legend>
		<?php else: ?>
			<legend>Création d'une journée</legend>
		<?php endif; ?>
		<p><label>Championnat</label>
			<select name="pr_season_id">
				<?php foreach ($seasons as $season) echo '<option value="' . $season->id . '"' . ($season->id == $day->pr_season_id ? ' selected="selected"' : '') . '>' . $leagues[$season->pr_league_id]->name . ' - ' . $season->label . '</option>'; ?>
			</select>
		</p>
		<p><label>Numéro</label><input type="text" name="number" value="<?php echo $day->number ?>" tabindex="1"/></p>
		<p><label>Label</label><input type="text" name="label" value="<?php echo $day->label ?>" tabindex="1"/></p>
		<p><label>Nombre de matches</label><input type="text" name="count_matches" id="count_matches" value="<?php echo $countMatches ?>" /></p>
		<p><label>Date limite</label><input type="text" name="limit_date" id="limit_date" value="<?php echo $limitDate ?>" /></p>
		<p class="infos">Seul l'administrateur pourra saisir des pronostics après la limite. Cette date peut être surchargée au niveau de chaque match.</p>
		<p><label>Heure limite</label><input type="text" name="limit_time" id="limit_date" value="<?php echo $limitTime ?>" /></p>
		<p class="infos">Format acceptés pour l'heure : "9:15", ou "9"</p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<?php if ($isEditable) { ?>
				<input type="submit" value="enregistrer" <?=(count($seasons)==0 ? ' disabled="disabled"' : '')?>/>
			<?php } ?>
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
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
