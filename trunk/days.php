<?php
	require_once('includes/init.php');

	$leagueId = GETorPOST('league');

	if (!empty($leagueId))
	{
		$league = League::find($leagueId);
		if ($league)
			$leagues = array($leagueId => $league);
	}

	if (empty($leagueId))
		$leagues = League::getAll();

	$seasonId = GETorPOST('season');

	if (!empty($seasonId))
	{
		$season = Season::find($seasonId);
		if ($season)
			$seasons = array($seasonId => $season);
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
		<form method="get" action="">
			<p style="margin-bottom: 10px">
				Afficher :
				<?=Tools::objectsToSelect($leagues, 'name', array('value' => $leagueId, 'name' => 'league', 'empty' => 'Tous les championnats')) ?>
				<?=Season::objectsToSelect($seasons, array('value' => $seasonId, 'name' => 'season', 'empty' => 'Toutes les saisons')) ?>
				<input type="submit" value="OK" />
			</p>
		</form>
		<?php if (!empty($_SESSION['user'])) { ?>
			<div class="add"><a href="<?=APPLICATION_URL?>ajax/add_day.php" class="nyroModal">Ajouter une journée</a></div>
		<?php } ?>
		<table>
			<thead>
				<tr>
					<th>Championnat</th>
					<th>Numéro</th>
					<?php if (!empty($_SESSION['user'])) { ?>
						<th>Modifier</th>
						<th>Matches</th>
					<?php } ?>
					<th>Scores</th>
					<th>Pronos</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($daysBySeason) != 0): ?>
					<?php foreach ($daysBySeason as $season => $days): ?>
						<?php $i=0; ?>
						<?php $league = $seasons[$season]->getLeague(); ?>
						<?php foreach ($days as $day): ?>
							<?php $isEditable = !$day->hasPronos() && !$day->hasCompletedMatches() && !empty($_SESSION['user']); ?>
							<tr>
								<?php if (++$i == 1) echo '<td rowspan="' . count($days) . '">' . $league->name . '<br/>' . $seasons[$day->pr_season_id]->label . '</td>'; ?>
								<td><?php echo $day->number ?></td>
								<?php if (!empty($_SESSION['user'])) { ?>
									<?php if ($isEditable) { ?>
										<td class="center"><a href="<?=APPLICATION_URL?>ajax/add_day.php?id=<?php echo $day->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a></td>
									<?php } else { ?>
										<td class="center tooltipped">
											<img src="<?=APPLICATION_URL?>images/edit_disabled.png" alt="[edit]" />
											<div class="hidden">Des scores et/ou des pronostics ont été saisis, cette journée n'est pas modifiable.</div>
										</td>
									<?php } ?>
								<?php } ?>
								<?php if (!empty($_SESSION['user'])) { ?>
									<td class="center">
										<p><a href="<?=APPLICATION_URL?>ajax/add_match.php?pr_day_id=<?php echo $day->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> voir/modifier </a></p>
									</td>
								<?php } ?>
								<td class="center">
									<?php if (!empty($_SESSION['user'])) { ?>
										<p><a href="<?=APPLICATION_URL?>ajax/add_scores.php?id=<?php echo $day->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> saisir</a></p>
									<?php } else { ?>
										<p><a href="<?=APPLICATION_URL?>ajax/add_scores.php?id=<?php echo $day->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> voir</a></p>
									<?php } ?>
								</td>
								<td class="center">
									<p><a href="<?=APPLICATION_URL?>pronos/day-<?php echo $day->id ?>"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> voir</a></p>
									<p><a href="<?=APPLICATION_URL?>ajax/print_pronos.php?pr_day_id=<?php echo $day->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[add]" /> imprimer (BBCode)</a></p>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr><td colspan="6">Aucun résultat trouvé</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div id="popup"><div id="popup_message"></div><div id="popup_content"></div></div>

	<script type="text/javascript">
<?php if (!empty($_SESSION['user'])) { ?>
	
		function addMatch()
		{
			var select = $('#selectTeams').html();
			$('#matches').append(select);
		}

<?php } ?>

		$(document).ready(function(){

			elems = $('.tooltipped');
			elems.each(function(i){
				if ($(elems[i]).find('.hidden').length)
				{
					$(elems[i]).css('cursor', 'help');
					$(elems[i]).qtip({
						content: $(elems[i]).find('.hidden').html(),
						show: 'mouseover',
						hide: { fixed: true },
						style: { name: 'blue', tip: true, 'text-align': 'center', width: 300 },
						show: { solo: true },
						position: {
							corner: { target: 'rightMiddle', tooltip: 'leftMiddle'}
						}
					});
				}
			});
		});
	</script>

	<?php echoHTMLFooter(); ?>
</body>
</html>