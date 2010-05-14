<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id', -1);

	$team = Team::find($id);
	if (!$team)
		$team = new Team();
?>
<p id="popup_message" style="margin: 0; padding: 0;"></p>
<form action="<?=APPLICATION_URL?>ajax/save_team.php" method="post" id="ajaxForm">
	<fieldset>
		<?php if ($id != -1): ?>
			<legend>Edition d'une équipe</legend>
		<?php else: ?>
			<legend>Création d'une équipe</legend>
		<?php endif; ?>
		<p><label>Nom</label><input type="text" name="name" value="<?php echo $team->name ?>" /></p>
		<p>
			<label>Logo</label>
			<?php if (!$team->has_logo) { ?>
				<input type="file" name="logo" id="logoInput<?=$team->id?>" />
			<?php } else { ?>
				<input type="file" name="logo" id="logoInput<?=$team->id?>" style="display:none;" disabled="disabled"/>
				<span id="logoTeam<?=$team->id?>">
					<?php echo $team->getLogo('style="float:left;" id=logoTeam' . $team->id) ?>
					<a href="javascript:void(0);" onclick="$('#logoInput<?=$team->id?>').show().enable();$('#logoTeam<?=$team->id?>').hide();$('#remove_logo').val(1);">Supprimer ce logo</a>
				</span>
			<?php } ?>
		</p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="remove_logo" id="remove_logo" value="0" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_team.php',
			dataType: 'json',
			method: 'post',
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