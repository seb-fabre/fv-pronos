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
<form action="<?=APPLICATION_URL?>ajax/save_league.php" method="get" id="ajaxForm" class="form-horizontal">
	
		<h4 class="well">
			<?php if ($id != -1) { ?>
				Edition d'un championnat
			<?php } else { ?>
				Création d'un championnat
			<?php } ?>
		</h4>
	
	<div class="panel-body">

		<div class="form-group">
			<label class="col-sm-3 control-label">Nom</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="name" value="<?php echo $league->name ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label">Saison</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="season" value="<?php echo $season->label ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label">Nombre d'équipes</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="teams" value="<?php echo $season->teams ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label">Catégorie d'équipes</label>
			<div class="col-sm-8">
				<?=$categoriesSelect?>
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-8">
				<input type="hidden" name="id" value="<?php echo $id ?>" />
				<button type="submit" class="btn btn-default btn-sm" onclick="saveLeague()">Enregistrer</button>
				<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
			</div>
		</div>
	</div>
</form>