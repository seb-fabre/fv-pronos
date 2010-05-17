<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id', -1);

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

	$categories = TeamCategory::getAll('name ASC');
	$options = array(
		'value' => $league->pr_team_category_id,
		'name' => 'pr_team_category_id',
		'empty' => '&nbsp;',
	);
	$categoriesSelect = Tools::objectsToSelect($categories, 'name', $options);

?>
<p id="popup_message" style="margin: 0; padding: 0;"></p>
<form action="/ajax/save_league.php" method="get" id="ajaxForm">
	<fieldset>
		<?php if ($id != -1): ?>
			<legend>Edition d'un championnat</legend>
		<?php else: ?>
			<legend>Création d'un championnat</legend>
		<?php endif; ?>
		<p><label>Nom</label><input type="text" name="name" value="<?php echo $league->name ?>" /></p>
		<p><label>Saison</label><input type="text" name="season" value="<?php echo $season->label ?>" /></p>
		<p><label>Nombre d'équipes</label><input type="text" name="teams" value="<?php echo $season->teams ?>" /></p>
		<p><label>Catégorie d'équipes</label><?=$categoriesSelect?></p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="button" onclick="saveLeague()" value="enregistrer" />
			<input type="button" value="annuler" class="nyroModalClose"/>
		</p>
	</fieldset>
</form>