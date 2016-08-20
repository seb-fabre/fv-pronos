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
	<div class="container">
	<?php echoMenu(); ?>
	<h1>Connexion</h1>
	<?php echoNotifications(); ?>
	
		<form method="post" class="form-inline">
			
			<div class="form-group">
				<label class="sr-only">Identifiant</label>
				<input type="text" name="login" class="form-control" placeholder="Identifiant" />
			</div>
			
			<div class="form-group">
				<label class="sr-only">Mot de passe</label>
				<input type="password" name="password" class="form-control" placeholder="Mot de passe" />
			</div>
			
			<button type="submit" class="btn btn-default btn-sm">OK</button>
		</form>
	</div>
	<?php echoHTMLFooter(); ?>
</body>
</html>
