<?php
	session_start();
	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	$team = Team::find($_GET['id']);
	if (!$team)
		$team = new Team();
?>
<form action="/ajax/save_team.php" method="get">
	<fieldset>
		<?php if ($_GET['id'] != -1): ?>
			<legend>Edition d'une équipe</legend>
		<?php else: ?>
			<legend>Création d'une équipe</legend>
		<?php endif; ?>
		<p><label>Nom</label><input type="text" name="name" value="<?php echo $team->name ?>" /></p>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>