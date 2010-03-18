<?php
	require_once('includes/init.php');

	$id = $_GET['id'];

	if (!$id)
		header('location: /days.php');

	$day = Day::find($id);

	if (!$day)
		header('location: /days.php');

	$season = $day->getSeason();

	$league = $season->getLeague();

	$matches = $day->getMatches();

	$teams = $season->getTeams();

	$pronos = $day->getPronos();

	$users = User::getAll();

	$pronosByUser = array();
	foreach ($pronos as $prono)
	{
		if (!array_key_exists($prono->pr_user_id, $pronosByUser))
			$pronosByUser[$prono->pr_user_id] = array();
		$pronosByUser[$prono->pr_user_id][$prono->pr_match_id] = $prono;

	}

	echoHTMLHead('Liste des pronos');
?>

<body>
	<?php echoMenu(); ?>
	<div id="content">
		<h1>Liste des pronos</h1>
		<h2><?php echo $league->name ?> - <?php echo $season->label ?>, Journée n°<?php echo $day->number ?></h2>
		<div class="add"><a href="javascript:;" onclick="parsePronos(<?php echo $day->id ?>)">Saisir l'ensemble des pronos</a></div>
		<p><?php echo count($pronosByUser) ?> joueurs ont pronostiqué cette journée</p>
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<th>Modifier</th>
					<th>Afficher</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($users) != 0): ?>
					<?php foreach ($users as $user): ?>
						<tr>
							<td><?php echo $user->name ?></td>
							<td class="center"><a href="javascript:;" onclick="openPopup(<?php echo $day->id ?>, <?php echo $user->id ?>)"><img src="/pronos/images/fleche.png" alt="[edit]" /> saisir les pronos</a></td>
							<td class="tooltipped">

								<?php
								if (array_key_exists($user->id, $pronosByUser))
								{
									echo '<i>' . (count($pronosByUser[$user->id]) != 10 ? '<img src="/pronos/images/warning.png" style="vertical-align:middle" /> ' : '') . count($pronosByUser[$user->id]) . ' pronos</i>';
									echo '<div class="hidden">';
									echo '<table class="noborder" style="width: 100%">';
									foreach ($pronosByUser[$user->id] as $match => $prono)
									{
										if (!is_null($prono->home_goals) && !is_null($prono->away_goals))
										{
											if ($prono->home_goals > $prono->away_goals)
												echo '<tr><td class="right"><b>' . $teams[$matches[$match]->pr_home_team_id]->name . '</b></td><td><b>' . $prono->home_goals . '</b> - ' . $prono->away_goals . '</td><td>' . $teams[$matches[$match]->pr_away_team_id]->name . '</td></tr>';
											else if ($prono->home_goals < $prono->away_goals)
												echo '<tr><td class="right">' . $teams[$matches[$match]->pr_home_team_id]->name . '</td><td>' . $prono->home_goals . ' - <b>' . $prono->away_goals . '</b></td><td><b>' . $teams[$matches[$match]->pr_away_team_id]->name . '</b></td></tr>';
											else
												echo '<tr><td class="right">' . $teams[$matches[$match]->pr_home_team_id]->name . '</td><td>' . $prono->home_goals . ' - ' . $prono->away_goals . '</td><td>' . $teams[$matches[$match]->pr_away_team_id]->name . '</td></tr>';
										}
										else
											echo '<tr><td class="right">' . $teams[$matches[$match]->pr_home_team_id]->name . '</td><td> * - * </td><td>' . $teams[$matches[$match]->pr_away_team_id]->name . '</td></tr>';
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

	<div id="popup"><div id="popup_message"></div><div id="popup_content"></div></div>

	<script type="text/javascript">
		function openPopup(id, user)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '/pronos/ajax/add_pronos.php',
				data: {id: id, user: user},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '/pronos/ajax/save_pronos.php',
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

		function parsePronos(id)
		{
			$('#loading').modal({close: false});
			$.ajax({
				url: '/pronos/ajax/parse_pronos.php',
				data: {id: id},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false, persist: true});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '/pronos/ajax/save_prono.php',
						success: function (response) {
							$('#matches').parent().css('height', 400).css('overflow-y', 'scroll');
							$('#matches').parent().html(response);
							//$.modal.close();
							//$('#popup').modal({close: false});
							$('#parser').val('enregistrer');
							$('#popup form').ajaxForm({
								url: '/pronos/ajax/save_parsed_pronos.php',
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

	<div id="loading">
		<div id="subloading">Chargement</div>
	</div>
</body>
</html>