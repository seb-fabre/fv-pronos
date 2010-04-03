<?php
	require_once('includes/init.php');

	$leagues = League::getAll();

	$seasons = Season::getAll();
	$tmp = array();
	foreach ($seasons as $season)
		$tmp[$season->pr_league_id] []= $season;
	$seasons = $tmp;
	unset($tmp);

	echoHTMLHead('Liste des championnats');
?>

<body>
	<?php echoMenu(); ?>
	<div id="content">
		<h1>Liste des championnats</h1>
		<?php if (!empty($_SESSION['user'])) { ?>
			<div class="add"><a href="javascript:;" onclick="openPopup(-1)">Ajouter un championnat</a></div>
		<?php } ?>
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<th>Saison</th>
					<?php if (!empty($_SESSION['user'])) { ?>
						<th>Modifier</th>
					<?php } ?>
					<th>Equipes</th>
					<th>Classement</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($leagues) != 0): ?>
					<?php foreach ($leagues as $league): ?>
						<?php foreach ($seasons[$league->id] as $i => $season): ?>
							<tr>
								<?php if ($i==0): ?>
									<td rowspan="<?php echo count($seasons[$league->id]) ?>"><?php echo $league->name ?></td>
								<?php endif; ?>
								<td><?php echo $season->label ?></td>
								<?php if (!empty($_SESSION['user'])) { ?>
									<td class="center">
										<a href="javascript:;" onclick="openPopup(<?php echo $season->id ?>)"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a>
									</td>
								<?php } ?>
								<td>
									<a href="javascript:;" onclick="showTeams(<?php echo $season->id ?>);"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> voir/modifier les équipes</a>
								</td>
								<td>
									<a href="javascript:;" onclick="showRankings(<?php echo $season->id ?>, -1);"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> afficher le classement</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr><td colspan="5">Aucun résultat trouvé</td></tr>
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
				url: '<?=APPLICATION_URL?>ajax/add_league.php',
				data: {id: id},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_league.php',
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

		function showRankings(league, max)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/show_rankings.php',
				data: {id: league, max: max},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
				}
			});
		}

		function showTeams(league)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/show_teams.php',
				data: {id: league},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_season_teams.php',
						dataType: 'json',
						type: 'post',
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