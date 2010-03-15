<?php 
	session_start();
	require_once('../mysql_connexion.php');
	require_once('../includes.php');
	
	$user = User::find($_GET['id']);
	if (!$user)
		$user = new User();
		
	$teams = Team::getAll();
?>
<form action="/ajax/save_user.php" method="get">
	<fieldset>
		<?php if ($_GET['id'] != -1): ?>
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
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>