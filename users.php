<?php 
	require_once('includes/init.php');
	
	$users = User::getAll('name asc');
	
	$teams = Team::getAll();

	echoHTMLHead('Liste des utilisateurs');
?>

<body>
	<?php echoMenu(); ?>
	<div id="content">
		<h1>Liste des utilisateurs</h1>
		<?php if (!empty($_SESSION['user'])) { ?>
			<div class="add"><a href="javascript:;" onclick="openPopup(-1)">Ajouter un utilisateur</a></div>
		<?php } ?>
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<th>Club</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($users) != 0): ?>
					<?php foreach ($users as $user): ?>
						<tr>
							<td><?php echo $user->name ?></td>
							<td>
								<?php
									if ($user->pr_team_id && isset($teams[$user->pr_team_id]) && $teams[$user->pr_team_id]->has_logo)
										echo $teams[$user->pr_team_id]->getLogo();
									else
										echo '&nbsp;';
								?>
							</td>
							<td class="center">
								<?php if (!empty($_SESSION['user'])) { ?>
									<a href="javascript:;" onclick="openPopup(<?php echo $user->id ?>)"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a>
								<?php } ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr><td colspan="2">Aucun utilisateur trouvé</td></tr>
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
				url: '<?=APPLICATION_URL?>ajax/add_user.php',
				data: {id: id},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_user.php',
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