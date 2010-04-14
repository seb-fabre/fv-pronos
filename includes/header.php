<div id="header">
	<div id="tabsF">
		<ul>
			<li><a href="<?=APPLICATION_URL?>leagues"><span>Championnats</span></a></li>
			<li><a href="<?=APPLICATION_URL?>teams"><span>Equipes</span></a></li>
			<li><a href="<?=APPLICATION_URL?>days"><span>Journées</span></a></li>
			<li><a href="<?=APPLICATION_URL?>users"><span>Utilisateurs</span></a></li>
			<?php if (!empty($_SESSION['user'])) { ?>
			<li><a href="<?=APPLICATION_URL?>validations"><span>Vérifications</span></a></li>
			<?php } else { ?>
				<li><a href="<?=APPLICATION_URL?>login"><span>Connexion</span></a></li>
			<?php } ?>
		</ul>
	</div>
</div>