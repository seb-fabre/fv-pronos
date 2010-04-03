<?php
$GLOBALS['ROOTPATH'] = str_replace(basename(__FILE__), '', str_replace('\\', '/', __FILE__));

header('Content-type: text/html; charset=UTF-8');

require_once($GLOBALS['ROOTPATH'] . 'includes/__classes.php');

require_once($GLOBALS['ROOTPATH'] . 'includes/class.migration.php');
require_once($GLOBALS['ROOTPATH'] . 'includes/class.notification.php');
require_once($GLOBALS['ROOTPATH'] . 'includes/JSON.php');
require_once($GLOBALS['ROOTPATH'] . 'includes/functions.php');

if (file_exists($GLOBALS['ROOTPATH'] . 'includes/conf.php'))
{
	require_once($GLOBALS['ROOTPATH'] . 'includes/conf.php');
	header('location: ' . APPLICATION_URL);
}

$fieldErrors = array();
$globalErrors = array();

$mandatoryFields = array('db_login', 'db_passwd', 'db_name', 'db_host', 'admin_login', 'admin_passwd', 'admin_email');

foreach ($mandatoryFields as $field)
{
	$$field = '';
}

$reset_db = 1;

$isInstalled= false;

if (!empty($_POST['cparti']))
{
	foreach ($mandatoryFields as $field)
	{
		if (empty($_POST[$field]))
		{
			$fieldErrors[$field][] = 'Ce champ est obligatoire';
		}
	}

	$reset_db = (isset($_POST['reset_db']) ? 1 : 0);

	foreach ($mandatoryFields as $field)
	{
		$$field = $_POST[$field];
	}

	// specific checks
	// check email format
	if (!empty($admin_email) && !preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $admin_email))
		$fieldErrors['admin_email'][] = 'Adresse email invalide';

	// check login format
	if (!empty($admin_login) && !preg_match('#^[A-Za-z0-9_-]+$#', $admin_login))
		$fieldErrors['admin_login'][] = "L'identifiant ne peut contenir que des lettres, des chiffres et des tirets";

	// check password format
	if (!empty($admin_passwd) && !preg_match('#^[A-Za-z0-9_-]+$#', $admin_passwd))
		$fieldErrors['admin_passwd'][] = "Le mot de passe ne peut contenir que des lettres, des chiffres et des tirets";

	// Don't bother doing the rest if there are errors
	if (empty($fieldErrors))
	{
		// try to login to the database
		if (!@mysql_connect($db_host, $db_login, $db_passwd))
		{
			$globalErrors[]= 'Impossible de se connecter à la base de données';
		}

		// check if the given database exists
		if (!@mysql_select_db($db_name))
		{
			$globalErrors[]= 'Base de données invalide ou introuvable';
		}

		if (empty($globalErrors))
		{
			$sql = file_get_contents('scripts/install.sql');

			if ($reset_db)
				$sql .= file_get_contents('scripts/reset.sql');

			// Install the database, one query at a time
			foreach (explode(';', $sql) as $query)
			{
				if (trim($query))
				{
					$query .= ';';
					mysql_query($query) or die(mysql_error());
				}
			}

			// create the super-admin
			$superAdmin = new User();
			$superAdmin->name = $admin_login;
			$superAdmin->passwd = md5($admin_passwd);
			$superAdmin->email = $admin_email;
			$superAdmin->save();

			$url = substr($_SERVER['HTTP_REFERER'], 0, strpos($_SERVER['HTTP_REFERER'], 'install'));

			// write the conf file
			$f = fopen('includes/conf.php', 'w+');
			fwrite($f, '
<?php
$GLOBALS["conf"]["mysql_host"] = "' . addslashes($db_host) . '";
$GLOBALS["conf"]["mysql_login"] = "' . addslashes($db_login) . '";
$GLOBALS["conf"]["mysql_password"] = "' . addslashes($db_passwd) . '";
$GLOBALS["conf"]["mysql_database"] = "' . addslashes($db_name) . '";

define("APPLICATION_URL", "' . $url . '");
?>');
			fclose($f);

			$isInstalled = true;
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Installation</title>
		<link rel="stylesheet" href="./css/screen.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="./css/pronos.css" type="text/css" media="screen" />
		<script type="text/javascript" src="./js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="./js/jquery-ui-1.8.custom.min.js"></script>
		<script type="text/javascript" src="./js/jquery.simplemodal-1.2.3.js"></script>
		<script type="text/javascript" src="./js/jquery.form.js"></script>
		<script type="text/javascript" src="./js/jquery.qtip-1.0.0-rc3.min.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
	</head>
	<body>
		<div id="content">

			<h1>Installation</h1>

			<?php if (!$isInstalled) { ?>

				<form method="post">

					<fieldset class="largeFieldset">
						<legend>Base de données</legend>

						<?php if (!empty($globalErrors)) { ?>
							<div class="error">
								<?php
									foreach ($globalErrors as $err)
										echo '<p>' . $err . '</p>';
								?>
							</div>
						<?php } ?>

						<p><label>Identifiant</label><input type="text" name="db_login" value="<?=$db_login?>" /></p>

						<p><label>Mot de passe</label><input type="password" name="db_passwd" value="<?=$db_passwd?>" /></p>

						<p><label>Nom de la base</label><input type="text" name="db_name" value="<?=$db_name?>" /></p>

						<p><label>Hôte de la base</label><input type="text" name="db_host" value="<?=$db_host?>" /></p>
						<p class="infos">La base de données doit avoir été créée sur le serveur avant de faire cette installation.</p>

						<p><input type="checkbox" value="1" name="reset_db" <?=($reset_db ? ' checked="checked"' : '')?> /> Réinitialiser les données existantes (attention, cela effacera toutes les données présentes dans la base).</p>
					</fieldset>

					<fieldset class="largeFieldset">
						<legend>Super-admin</legend>

						<p><label>Identifiant</label><input type="text" name="admin_login" value="<?=$admin_login?>" /></p>

						<p><label>Mot de passe</label><input type="password" name="admin_passwd" value="<?=$admin_passwd?>" /></p>

						<p><label>Email</label><input type="text" name="admin_email" value="<?=$admin_email?>" /></p>
					</fieldset>

					<p class="submitNarrow" style="width:600px"><input type="submit" value="C'est parti !" name="cparti" value="<?=$cparti?>" /></p>

				</form>

			<?php } else { ?>
				<p>Installation effectuée avec succès.</p>
			<?php } ?>

		</div>

		<script type="text/javascript">
			function addError(name, message)
			{
				var input = $('input[name=' + name + ']');
				var p = input.parents('p');

				var pError = $('<p class="errorForm">' + message + '</p>');

				p.append(pError);
				input.addClass('inputError');
			}

			<?php
				foreach ($fieldErrors as $fieldName => $messages)
					foreach ($messages as $aMessage)
						echo 'addError("' . $fieldName . '", "' . addslashes($aMessage) . '");' . "\n";
			?>
		</script>
	</body>
</html>