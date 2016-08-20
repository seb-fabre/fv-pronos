<?php
	require_once('includes/init.php');

	$teams = Team::getAll('name');

	echoHTMLHead('Liste des équipes');
?>

<body>
	<div class="container">
	<?php echoMenu(); ?>
		<h1>Liste des équipes</h1>

		<?php if (!empty($_SESSION['user'])) { ?>
			<button type="button" class="btn btn-primary nyroModal" href="<?=APPLICATION_URL?>ajax/add_team.php">Ajouter une équipe</button>
			<button type="button" class="btn btn-primary nyroModal" href="<?=APPLICATION_URL?>ajax/add_team_category.php">Ajouter une catégorie</button>
		<?php } ?>
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class="col-sm-3">Nom</th>
					<th class="col-sm-3">Catégorie</th>
					<th class="col-sm-3">Noms alternatifs</th>
					<th class="col-sm-2">Logo</th>
					<th class="col-sm-1">Actions</th>
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
							<td><?php echo $team->aliases ?></td>
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
									<a href="<?=APPLICATION_URL?>ajax/add_team.php?id=<?php echo $team->id ?>" class="nyroModal" rev="modal"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a>
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