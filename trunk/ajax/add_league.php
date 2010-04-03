<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');

	$season = Season::find($id);

	if (!$season)
	{
		$season = new Season();
		$league = new League();
	}
	else
	{
		$league = $season->getLeague();
	}

?>
<form action="/ajax/save_league.php" method="get">
	<fieldset>
		<?php if ($id != -1): ?>
			<legend>Edition d'un championnat</legend>
		<?php else: ?>
			<legend>Création d'un championnat</legend>
		<?php endif; ?>
		<p><label>Nom</label><input type="text" name="name" value="<?php echo $league->name ?>" /></p>
		<p><label>Saison</label><input type="text" name="season" value="<?php echo $season->label ?>" /></p>
		<p><label>Nombre d'équipes</label><input type="text" name="teams" value="<?php echo $season->teams ?>" /></p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>