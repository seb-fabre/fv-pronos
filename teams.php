<?php
	require_once('includes/init.php');

	$teams = Team::getAll('name');

	echoHTMLHead('Liste des équipes');
?>

<body>
	<?php echoMenu(); ?>
	<div id="content">
		<h1>Liste des équipes</h1>

		<?php if (!empty($_SESSION['user'])) { ?>
			<div class="add"><a href="<?=APPLICATION_URL?>ajax/add_team.php" class="nyroModal">Ajouter une équipe</a></div>
			<div class="add"><a href="<?=APPLICATION_URL?>ajax/add_team_category.php" class="nyroModal">Ajouter une catégorie</a></div>
		<?php } ?>
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<th>Catégorie</th>
					<th>Logo</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($teams) != 0): ?>
					<?php $i=0 ?>
					<?php foreach ($teams as $team): ?>
						<?php $cat = $team->getTeamCategory(); ?>
						<tr>
							<td><?php echo $team->name ?></td>
							<td><?php echo ($cat ? $cat->name : '') ?></td>
							<td>
								<?php
									if ($team->has_logo)
										echo $team->getLogo();
									else
										echo '&nbsp;';
								?>
							</td>
							<td class="center">
								<?php if (!empty($_SESSION['user'])) { ?>
									<a href="<?=APPLICATION_URL?>ajax/add_team.php?id=<?php echo $team->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a>
								<?php } ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr><td colspan="3">Aucune équipe trouvée</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<?php echoHTMLFooter(); ?>
</body>
</html>