<?php 
	require_once('../includes/init.php');
	
	$user = User::find(GETorPOST('id'));
	if (!$user)
		$user = new User();
		
	$teams = Team::getAll('name ASC');
?>
<p id="popup_message" style="margin: 0; padding: 0;"></p>
<form action="/ajax/save_user.php" method="get" id="ajaxForm">
	<fieldset>
		<?php if (GETorPOST('id') != -1): ?>
			<legend>Edition d'un utilisateur</legend>
		<?php else: ?>
			<legend>Ajout d'un utilisateur</legend>
		<?php endif; ?>
		<p><label>Nom</label><input type="text" name="name" value="<?php echo $user->name ?>" /></p>
		<p><label>Club</label><select name="pr_team_id">
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
		</select></p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_user.php',
			dataType: 'json',
			success: function (response) {
				if (response.success == 1)
					window.location.reload();
				else
					$('#popup_message').html(response.message);
				resizeModal();
			}
		});
	</script>

<?php } ?>