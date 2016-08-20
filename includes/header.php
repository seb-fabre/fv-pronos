<?php
	$page = basename(str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));
?>

<nav class="navbar navbar-default">
<div class="navbar-collapse collapse">
<ul class="nav navbar-nav">
	<li <?php if ($page == 'leagues') echo 'class="active"' ?>><a href="<?=APPLICATION_URL?>leagues">Championnats</a></li>
	<li <?php if ($page == 'teams') echo 'class="active"' ?>><a href="<?=APPLICATION_URL?>teams">Equipes</a></li>
	<li <?php if ($page == 'days') echo 'class="active"' ?>><a href="<?=APPLICATION_URL?>days">Journées</a></li>
	<li <?php if ($page == 'users') echo 'class="active"' ?>><a href="<?=APPLICATION_URL?>users">Utilisateurs</a></li>
	<?php if (!empty($_SESSION['user'])) { ?>
	<li <?php if ($page == 'validations') echo 'class="active"' ?>><a href="<?=APPLICATION_URL?>validations">Vérifications</a></li>
	<?php } else { ?>
		<li <?php if ($page == 'members') echo 'class="active"' ?>><a href="<?=APPLICATION_URL?>login">Connexion</a></li>
	<?php } ?>
</ul>
</div>
</nav>