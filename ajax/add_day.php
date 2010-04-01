<?php
	require_once('../includes/init.php');

	$leagues = League::getAll();

	$seasons = Season::getAll();

	$day = Day::find(GETorPOST('id'));
	if (!$day)
		$day = new Day();
?>
<form action="/ajax/save_team.php" method="get">
	<fieldset>
		<?php if (GETorPOST('id') != -1): ?>
			<legend>Edition d'une journée</legend>
		<?php else: ?>
			<legend>Création d'une journée</legend>
		<?php endif; ?>
		<p><label>Championnat</label>
			<select name="pr_season_id">
				<?php foreach ($seasons as $season) echo '<option value="' . $season->id . '"' . ($season->id == $day->pr_season_id ? ' selected="selected"' : '') . '>' . $leagues[$season->pr_league_id]->name . ' - ' . $season->label . '</option>'; ?>
			</select>
		</p>
		<p><label>Number</label><input type="text" name="number" value="<?php echo $day->number ?>" /></p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>