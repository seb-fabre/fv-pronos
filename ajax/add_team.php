<?php
	require_once('../includes/init.php');

	$team = Team::find(GETorPOST('id'));
	if (!$team)
		$team = new Team();
?>
<form action="<?=APPLICATION_URL?>ajax/save_team.php" method="post" enctype="multipart/form-data">
	<fieldset>
		<?php if (GETorPOST('id') != -1): ?>
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
			<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
			<input type="hidden" name="remove_logo" id="remove_logo" value="0" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>