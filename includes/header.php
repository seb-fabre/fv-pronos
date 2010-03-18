<div id="header">
	<div id="tabsF">
		<ul>
			<li><a href="<?=APPLICATION_URL?>leagues.php"><span>Championnats</span></a></li>
			<li><a href="<?=APPLICATION_URL?>teams.php"><span>Equipes</span></a></li>
			<li><a href="<?=APPLICATION_URL?>days.php"><span>Journées</span></a></li>
			<li><a href="<?=APPLICATION_URL?>users.php"><span>Utilisateurs</span></a></li>
			<li><a href="<?=APPLICATION_URL?>validations.php"><span>Vérifications</span></a></li>
			<?php if (empty($_SESSION['user'])) { ?>
				<li><a href="<?=APPLICATION_URL?>login.php"><span>Connexion</span></a></li>
			<?php } ?>
		</ul>
	</div>
</div>