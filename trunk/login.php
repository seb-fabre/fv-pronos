<?php
	require_once('includes/init.php');

	if (!empty($_POST['login']) && !empty($_POST['password']))
	{
		User::tryToLogin($_POST['login'], $_POST['password']);
	}

	if (!empty($_SESSION['user']))
	{
		header('location: ' . APPLICATION_URL . 'members/');
	}

	echoHTMLHead('Connexion');
?>

<body>
	<?php echoMenu(); ?>
	<div id="content">
	<?php echoNotifications(); ?>
		<form method="post">
			<fieldset style="margin-left: auto; margin-right: auto;">
				<legend>Connexion</legend>
				<p><label>Identifiant</label><input type="text" name="login" /></p>
				<p><label>Mot de passe</label><input type="password" name="password" /></p>
				<p class="submitNarrow">
					<input type="submit" value="ok" name="connexion" />
				</p>
			</fieldset>
		</form>
	</div>
	<?php echoHTMLFooter(); ?>
</body>
</html>
