<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id', -1);

	$team = Team::find($id);
	if (!$team)
		$team = new Team();

	$categories = TeamCategory::getAll('name ASC');
	$options = array(
		'value' => $team->pr_team_category_id,
		'name' => 'pr_team_category_id',
		'empty' => '&nbsp;',
	);
	$categoriesSelect = Tools::objectsToSelect($categories, 'name', $options, true);
?>
<form action="<?=APPLICATION_URL?>ajax/save_team.php" method="post" id="ajaxForm" class="form-horizontal">
	
	<h4 class="well"><?php if ($id != -1) echo "Edition d'une équipe"; else echo "Création d'une équipe"; ?></h4>
		
	<div class="panel-body">

		<div class="form-group">
			<label class="col-sm-3 control-label">Nom</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="name" value="<?php echo $team->name ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label">Aliases</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="aliases" value="<?php echo $team->aliases ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label">Catégorie</label>
			<div class="col-sm-8">
				<?=$categoriesSelect?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label">Logo</label>
			<div class="col-sm-8">
				<?php if (!$team->has_logo) { ?>
					<input type="file" name="logo" id="logoInput<?=$team->id?>" />
				<?php } else { ?>
					<input type="file" name="logo" id="logoInput<?=$team->id?>" style="display:none;" disabled="disabled" />
					<span id="logoTeam<?=$team->id?>">
						<?php echo $team->getLogo('style="float:left;"') ?>
						<button type="button" class="btn btn-link" onclick="$('#logoInput<?=$team->id?>').show().enable();$('#logoTeam<?=$team->id?>').hide();$('#remove_logo').val(1);">Supprimer ce logo</button>
					</span>
				<?php } ?>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-8">
				<input type="hidden" name="id" value="<?php echo $id ?>" />
				<input type="hidden" name="remove_logo" id="remove_logo" value="0" />
				<button type="submit" class="btn btn-default btn-sm">Enregistrer</button>
				<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
			</div>
		</div>
	</div>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_team.php',
			dataType: 'json',
			method: 'post',
			success: function (response) {
				if (response.success == 1)
				{
					window.location.reload();
					return;
				}
				
				showError(response.message);
				resizeModal();
			}
		});
	</script>

<?php } ?>