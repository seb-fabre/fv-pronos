<?php
	require_once('includes/init.php');

	$leagues = League::getAll();

	if (empty($_GET['season']))
		$seasons = Season::getAll();
	else
		$seasons = array($_GET['season'] => Season::find($_GET['season']));

	$days = Day::getAll('pr_season_id DESC, number DESC');
	$daysBySeason = array();
	foreach ($days as $day)
	{
		if (!empty($_GET['season']) && $day->pr_season_id != $_GET['season'])
			continue;
		
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
		<div class="add"><a href="javascript:;" onclick="openPopup(-1)">Ajouter une journée</a></div>
		<table>
			<thead>
				<tr>
					<th>Championnat</th>
					<th>Numéro</th>
					<th>Modifier</th>
					<th>Liste des matches</th>
					<th>Ajouter des matches</th>
					<th>Saisir les scores</th>
					<th>Pronos</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($daysBySeason) != 0): ?>
					<?php foreach ($daysBySeason as $season => $days): ?>
						<?php 
							if (!empty($_GET['season']) && $season != $_GET['season'])
								continue;
						?>
						<?php $i=0; ?>
						<?php $league = $seasons[$season]->getLeague(); ?>
						<?php foreach ($days as $day): ?>
							<tr>
								<?php if (++$i == 1) echo '<td rowspan="' . count($days) . '">' . $league->name . '<br/>' . $seasons[$day->pr_season_id]->label . '</td>'; ?>
								<td><?php echo $day->number ?></td>
								<td class="center"><a href="javascript:;" onclick="openPopup(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a></td>
								<td class="tooltipped">
									<i>voir les matches</i>
									<div class="hidden center" style="width:100%">
										<?php
											echo '<table class="noborder" width="100%" style="width:100%">';
											foreach ($day->getMatches() as $match)
											{
												if (!is_null($match->home_goals) && !is_null($match->away_goals))
												{
													if ($match->home_goals > $match->away_goals)
														echo '<tr><td class="right" width="40%"><b>' . $match->getHomeTeam()->name . '</b></td><td width="20%" align="center" style="text-align: center"><b>' . $match->home_goals . '</b> - ' . $match->away_goals . '</td><td width="40%">' . $match->getAwayTeam()->name . '</td></tr>';
													else if ($match->home_goals < $match->away_goals)
														echo '<tr><td class="right" width="40%">' . $match->getHomeTeam()->name . '</td><td width="20%" align="center" style="text-align: center">' . $match->home_goals . ' - <b>' . $match->away_goals . '</b></td><td width="40%"><b>' . $match->getAwayTeam()->name . '</b></td></tr>';
													else
														echo '<tr><td class="right" width="40%">' . $match->getHomeTeam()->name . '</td><td width="20%" align="center" style="text-align: center">' . $match->home_goals . ' - ' . $match->away_goals . '</td><td width="40%">' . $match->getAwayTeam()->name . '</td></tr>';
												}
												else
													echo '<tr><td class="right" width="40%">' . $match->getHomeTeam()->name . '</td><td width="20%" align="center" style="text-align: center"> - </td><td width="40%">' . $match->getAwayTeam()->name . '</td></tr>';
											}
											echo '</table>';
										?>
									</div>
								</td>
								<td class="center">
									<p><a href="javascript:;" onclick="addMatches(<?php echo $day->id ?>, -1)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> ajouter </a></p>
									<p><a href="javascript:;" onclick="parseMatches(<?php echo $day->id ?>, <?php echo $day->pr_season_id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> parser</a></p>
								</td>
								<td class="center">
									<p><a href="javascript:;" onclick="addScores(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> ajouter</a></p>
									<p><a href="javascript:;" onclick="parseScores(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> parser</a></p>
								</td>
								<td class="center">
									<p><a href="<?=APPLICATION_URL?>pronos.php?id=<?php echo $day->id ?>"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> voir</a></p>
									<p><a href="javascript:;" onclick="printScores(<?php echo $day->id ?>)"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> imprimer</a></p>
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

		$(document).ready(function(){
			elems = $('.tooltipped');
			elems.each(function(i){
				$(elems[i]).css('cursor', 'help');
				$(elems[i]).qtip({
					content: $(elems[i]).find('.hidden').html(),
					show: 'mouseover',
					hide: { delay: '1000', when: { event: 'mouseout' } },
					style: { name: 'blue', tip: true, 'text-align': 'center', width: 350 },
					show: { solo: true },
					position: {
						corner: { target: 'rightMiddle', tooltip: 'leftMiddle'}
					}
				});
			});
		});
	</script>

	<div id="loading">
		<div id="subloading">Chargement</div>
	</div>
</body>
</html>