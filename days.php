<?php
	require_once('includes/init.php');

	$leagues = League::getAll();

	$season = GETorPOST('season');

	if (!empty($season))
	{
		$season = Season::find(GETorPOST('season'));
		if ($season)
			$seasons = array(GETorPOST('season') => Season::find(GETorPOST('season')));
	}

	if (empty($season))
		$seasons = Season::getAll();

	if (empty($season))
		$days = Day::getAll('pr_season_id DESC, number DESC');
	else
		$days = Day::search(array(array('pr_season_id', $season->id)), 'pr_season_id DESC, number DESC');
	
	$daysBySeason = array();
	foreach ($days as $day)
	{
		if (!array_key_exists($day->pr_season_id, $daysBySeason))
			$daysBySeason[$day->pr_season_id] = array();
		
		$daysBySeason[$day->pr_season_id] []= $day;
	}

	echoHTMLHead('Liste des journées');
?>

<body>
	<?php echoMenu(); ?>
	<div id="content">
		<h1>Liste des journées</h1>
		<?php if (!empty($_SESSION['user'])) { ?>
			<div class="add"><a href="javascript:;" onclick="openPopup(-1)">Ajouter une journée</a></div>
		<?php } ?>
		<table>
			<thead>
				<tr>
					<th>Championnat</th>
					<th>Numéro</th>
					<th>Modifier</th>
					<?php if (!empty($_SESSION['user'])) { ?>
						<th>Ajouter des matches</th>
					<?php } ?>
					<th>Saisir les scores</th>
					<th>Pronos</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($daysBySeason) != 0): ?>
					<?php foreach ($daysBySeason as $season => $days): ?>
						<?php $i=0; ?>
						<?php $league = $seasons[$season]->getLeague(); ?>
						<?php foreach ($days as $day): ?>
							<tr>
								<?php if (++$i == 1) echo '<td rowspan="' . count($days) . '">' . $league->name . '<br/>' . $seasons[$day->pr_season_id]->label . '</td>'; ?>
								<td><?php echo $day->number ?></td>
								<td class="center"><a href="javascript:;" onclick="openPopup(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a></td>
								<?php if (!empty($_SESSION['user'])) { ?>
									<td class="center">
										<p><a href="javascript:;" onclick="addMatches(<?php echo $day->id ?>, -1)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> modifier </a></p>
										<p><a href="javascript:;" onclick="parseMatches(<?php echo $day->id ?>, <?php echo $day->pr_season_id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> parser</a></p>
									</td>
								<?php } ?>
								<td class="center">
									<?php if (!empty($_SESSION['user'])) { ?>
										<p><a href="javascript:;" onclick="addScores(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> saisir</a></p>
										<p><a href="javascript:;" onclick="parseScores(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> parser</a></p>
									<?php } else { ?>
										<p><a href="javascript:;" onclick="addScores(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> voir</a></p>
									<?php } ?>
								</td>
								<td class="center">
									<p><a href="<?=APPLICATION_URL?>pronos.php?id=<?php echo $day->id ?>"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> voir</a></p>
									<p><a href="javascript:;" onclick="printScores(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> imprimer (BBCode)</a></p>
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
<?php if (!empty($_SESSION['user'])) { ?>
		function openPopup(id)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/add_day.php',
				data: {id: id},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_day.php',
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

		function addMatch()
		{
			var select = $('#selectTeams').html();
			$('#matches').append(select);
		}

		function addMatches(day, id)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/add_match.php',
				data: {id: id, pr_day_id: day},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_match.php',
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

		function parseMatches(day, id)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/parse_matches.php',
				data: {id: id, pr_day_id: day},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_matches.php',
						success: function (response) {
							$('#matches').parent().html(response);
							$('#parser').val('enregistrer');
							$('#popup form').ajaxForm({
								url: '<?=APPLICATION_URL?>ajax/save_match.php',
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
			});
		}

		function parseScores(day, id)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/parse_scores.php',
				data: {id: day},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_score.php',
						success: function (response) {
							$('#matches').parent().html(response);
							$('#parser').val('enregistrer');
							$('#popup form').ajaxForm({
								url: '<?=APPLICATION_URL?>ajax/save_scores.php',
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
			});
		}
		
<?php } ?>

		function addScores(day)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/add_scores.php',
				data: {id: day},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '<?=APPLICATION_URL?>ajax/save_scores.php',
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
		
		function printScores(day)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/print_pronos.php',
				data: {pr_day_id: day},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
				}
			});
		}
	</script>

	<?php echoHTMLFooter(); ?>
</body>
</html>