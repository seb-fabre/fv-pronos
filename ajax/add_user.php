<?php 
	require_once('../includes/init.php');
	
	$user = User::find(GETorPOST('id'));
	if (!$user)
		$user = new User();
		
	$teams = Team::getAll('name ASC');
?>

<form action="/ajax/save_user.php" method="get" id="ajaxForm" class="form-horizontal">
	
		<h4 class="well">
			<?php 
				if (GETorPOST('id') != -1) 
					echo "Edition d'un utilisateur";
				else
					echo "Ajout d'un utilisateur";
			?>
		</h4>
		
		<div class="panel-body">
		
			<div class="form-group">
				<label class="col-sm-2 control-label">Nom</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="name" value="<?php echo $user->name ?>" />
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">Club</label>
				<div class="col-sm-10">
					<select name="pr_team_id" class="form-control">
					<?php 
					if (is_null($user->pr_team_id))
						echo '<option selected="selected">' . $team->name . '</option>';
					else
						echo '<option>' . $team->name . '</option>';
					 
					foreach ($teams as $team)
					{
						if ($team->id == $user->pr_team_id)
							echo '<option value="' . $team->id . '" selected="selected">' . $team->name . '</option>';
						else
							echo '<option value="' . $team->id . '">' . $team->name . '</option>';
					}
					?>
					</select>
				</div>
			</div>
		
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
					<button type="subbmit" class="btn btn-default btn-sm">Enregistrer</button>
					<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
			</div>
		</div>
	</div>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_user.php',
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