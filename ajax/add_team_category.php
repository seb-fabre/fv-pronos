<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id', -1);

	$message = '';

	$teamCategory = TeamCategory::find($id);
	if (!$teamCategory)
		$teamCategory = new TeamCategory();

	$name = '';
	if (!empty($_POST['name']))
	{
		$name = $_POST['name'];

		if (!TeamCategory::isUnique('name', $name, $teamCategory->id))
		{
			$message = 'Ce nom est déjà utilisé.';
		}
		else
		{
			$teamCategory->name = $name;
			$teamCategory->save();

			$saveOk = true;
		}
	}
?>
<form action="<?=APPLICATION_URL?>ajax/save_team_category.php" method="post" id="ajaxForm" class="form-horizontal">
	
	<h4 class="well">
		<?php 
			if (GETorPOST('id') != -1) 
				echo "Edition d'une catégorie d'équipe";
			else
				echo "Création d'une catégorie d'équipe";
		?>
	</h4>
		
	<div class="panel-body">
	
		<div class="form-group">
			<label class="col-sm-2 control-label">Nom</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="name" value="<?php echo $name ? $name : $teamCategory->name ?>" />
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<input type="hidden" name="id" value="<?php echo $id ?>" />
				<button type="subbmit" class="btn btn-default btn-sm">Enregistrer</button>
				<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
			</div>
		</div>
	</div>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_team_category.php',
			dataType: 'json',
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