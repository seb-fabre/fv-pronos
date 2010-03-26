<?php
	require_once('includes/init.php');

	$teams = Team::getAll();

	echoHTMLHead('Liste des équipes');
?>

<body>
	<?php echoMenu(); ?>
	<div id="content">
		<h1>Liste des équipes</h1>
		<div class="add"><a href="javascript:;" onclick="openPopup(-1)">Ajouter une équipe</a></div>
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<th>Logo</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($teams) != 0): ?>
					<?php $i=0 ?>
					<?php foreach ($teams as $team): ?>
						<tr>
							<td><?php echo $team->name ?></td>
							<td>
								<?php
									if ($team->has_logo)
										echo '<img src="' . APPLICATION_URL . 'logos/' . $team->id . '.gif" />';
									else
										echo '&nbsp;';
								?>
							</td>
							<td class="center">
								<?php if (!empty($_SESSION['user'])) { ?>
									<a href="javascript:;" onclick="openPopup(<?php echo $team->id ?>)"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a>
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

	<div id="popup"><div id="popup_message"></div><div id="popup_content"></div></div>

	<script type="text/javascript">
		function openPopup(id)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/add_team.php',
				data: {id: id},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_team.php',
						dataType: 'json',
						success: function (response) {
							if (response.success == 1)
								window.location.reload();
							else
								$('#popup_message').html(response.message);
						}
					});
				}
			});
		}

		$(document).ready(function(){

		});
	</script>

	<div id="loading">
		<div id="subloading">Chargement</div>
	</div>
</body>
</html>