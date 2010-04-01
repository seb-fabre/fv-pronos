<?php
	require_once('../includes/init.php');

	$season = Season::find(GETorPOST('id'));
	if (!$season)
		$season = new Season();

	$league = $season->getLeague();
?>
<form action="/ajax/save_league.php" method="get">
	<fieldset>
		<?php if (GETorPOST('id') != -1): ?>
			<legend>Edition d'un championnat</legend>
		<?php else: ?>
			<legend>Création d'un championnat</legend>
		<?php endif; ?>
		<p><label>Nom</label><input type="text" name="name" value="<?php echo $league->name ?>" /></p>
		<p><label>Saison</label><input type="text" name="season" value="<?php echo $season->label ?>" /></p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>