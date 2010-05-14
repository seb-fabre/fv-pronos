<?php
	require_once('includes/init.php');

	$pathinfo = $_SERVER['PATH_INFO'];

  $matches = false;
  $match = preg_match('@^/day-([0-9]+)$@', $pathinfo, $matches);

	if (!isset($matches[1]))
		header('location: /days');

	$day = Day::find($matches[1]);

	if (!$day)
		header('location: /days');

	$season = $day->getSeason();

	$league = $season->getLeague();

	$matches = $day->getMatches();

	$teams = $season->getTeams();

	$pronos = $day->getPronos();

	$users = User::getAll('name asc');

	$pronosByUser = array();
	foreach ($pronos as $prono)
	{
		if (!array_key_exists($prono->pr_user_id, $pronosByUser))
			$pronosByUser[$prono->pr_user_id] = array();
		$pronosByUser[$prono->pr_user_id][$prono->pr_match_id] = $prono;
	}

	$isEditable = !empty($_SESSION['user']);

	echoHTMLHead('Liste des pronos');
?>

<body>
	<?php echoMenu(); ?>
	<div id="content">
		<h1>Liste des pronos</h1>
		<h2><?php echo $league->name ?> - <?php echo $season->label ?>, Journée n°<?php echo $day->number ?></h2>
		<?php if ($isEditable) { ?>
			<div class="add"><a href="<?=APPLICATION_URL?>ajax/parse_pronos.php?id=<?php echo $day->id ?>" class="nyroModal">Saisir l'ensemble des pronos</a></div>
		<?php } ?>
		<p><?php echo count($pronosByUser) ?> joueurs ont pronostiqué cette journée</p>
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<?php if ($isEditable) { ?>
						<th>Modifier</th>
					<?php } ?>
					<th>Afficher</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($users) != 0): ?>
					<?php foreach ($users as $user): ?>
						<tr>
							<td><?php echo $user->name ?></td>
							<?php if ($isEditable) { ?>
								<td class="center"><a href="<?=APPLICATION_URL?>ajax/add_pronos.php?id=<?php echo $day->id ?>&user=<?php echo $user->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/fleche.png" alt="[edit]" /> saisir les pronos</a></td>
							<?php } ?>
							<td class="tooltipped">

								<?php
								if (array_key_exists($user->id, $pronosByUser))
								{
									echo '<i>' . (count($pronosByUser[$user->id]) != 10 ? '<img src="' . APPLICATION_URL . 'images/warning.png" style="vertical-align:middle" /> ' : '') . count($pronosByUser[$user->id]) . ' pronos</i>';
									echo '<div class="hidden">';
									echo '<table class="noborder scoreTable" style="width: 100%">';
									foreach ($pronosByUser[$user->id] as $match => $prono)
									{
										if (!is_null($prono->home_goals) && !is_null($prono->away_goals))
										{
											if ($prono->home_goals > $prono->away_goals)
												echo '<tr><td class="right team"><b>' . $teams[$matches[$match]->pr_home_team_id]->name . '</b></td><td class="center"><b>' . $prono->home_goals . '</b> - ' . $prono->away_goals . '</td><td class="team">' . $teams[$matches[$match]->pr_away_team_id]->name . '</td></tr>';
											else if ($prono->home_goals < $prono->away_goals)
												echo '<tr><td class="right team">' . $teams[$matches[$match]->pr_home_team_id]->name . '</td><td class="center">' . $prono->home_goals . ' - <b>' . $prono->away_goals . '</b></td><td class="team"><b>' . $teams[$matches[$match]->pr_away_team_id]->name . '</b></td></tr>';
											else
												echo '<tr><td class="right team">' . $teams[$matches[$match]->pr_home_team_id]->name . '</td><td class="center">' . $prono->home_goals . ' - ' . $prono->away_goals . '</td><td class="team">' . $teams[$matches[$match]->pr_away_team_id]->name . '</td></tr>';
										}
										else
											echo '<tr><td class="right team">' . $teams[$matches[$match]->pr_home_team_id]->name . '</td><td class="center"> * - * </td><td class="team">' . $teams[$matches[$match]->pr_away_team_id]->name . '</td></tr>';
									}
									echo '</table>';
									echo '</div>';
								}
								else
								{
									echo '<i>aucun prono</i>';
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr><td colspan="2">Aucun résultat trouvé</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			elems = $('.tooltipped');
			elems.each(function(i){
				if ($(elems[i]).find('.hidden').length)
				{
					$(elems[i]).css('cursor', 'help');
					$(elems[i]).qtip({
						content: $(elems[i]).find('.hidden').html(),
						show: 'mouseover',
						hide: { delay: '10000', when: { event: 'mouseout' } },
						style: { name: 'blue', tip: true, 'text-align': 'center', width: 350 },
						show: { solo: true },
						position: {
							corner: { target: 'rightMiddle', tooltip: 'leftMiddle'}
						}
					});
				} 
			});
		});
	</script>

	<?php echoHTMLFooter();?>
</body>
</html>