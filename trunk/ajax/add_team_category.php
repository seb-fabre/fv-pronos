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
<p id="popup_message" style="margin: 0; padding: 0;"><?=$message?></p>
<form action="<?=APPLICATION_URL?>ajax/add_team_category.php" method="post" id="ajaxForm" class="nyroModal">
	<fieldset>
		<?php if ($id != -1): ?>
			<legend>Edition d'une catégorie d'équipe</legend>
		<?php else: ?>
			<legend>Création d'une catégorie d'équipe</legend>
		<?php endif; ?>
		<p><label>Nom</label><input type="text" name="name" value="<?php echo $name ? $name : $teamCategory->name ?>" /></p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
</form>

<?
	if (!empty($saveOk))
		echo '<script type="text/javascript">
					$(".nyroModalClose").click();
			</script>';