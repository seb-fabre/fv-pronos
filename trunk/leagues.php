<?php
	session_start();

	require_once('mysql_connexion.php');
	require_once('includes.php');

	$leagues = League::getAll();

	$seasons = Season::getAll();
	$tmp = array();
	foreach ($seasons as $season)
		$tmp[$season->pr_league_id] []= $season;
	$seasons = $tmp;
	unset($tmp);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Liste des championnats</title>
	<link rel="stylesheet" href="/pronos/css/screen.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="/pronos/css/pronos.css" type="text/css" media="screen" />
	<script type="text/javascript" src="/pronos/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="/pronos/js/jquery.simplemodal-1.2.3.js"></script>
	<script type="text/javascript" src="/pronos/js/jquery.form-2.24.js"></script>
	<script type="text/javascript" src="/pronos/js/jquery.qtip-1.0.0-rc3.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
</head>

<body>
	<?php include('header.php'); ?>
	<div id="content">
		<h1>Liste des championnats</h1>
		<div class="add"><a href="javascript:;" onclick="openPopup(-1)">Ajouter un championnat</a></div>
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<th>Saison</th>
					<th>Modifier</th>
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
								<td class="center"><a href="javascript:;" onclick="openPopup(<?php echo $season->id ?>)"><img src="/pronos/images/edit.png" alt="[edit]" /></a></td>
								<td><a href="javascript:;" onclick="showTeams(<?php echo $season->id ?>);"><img src="/pronos/images/fleche.png" alt="[add]" /> afficher les équipes</a></td>
								<td><a href="javascript:;" onclick="showRankings(<?php echo $season->id ?>, -1);"><img src="/pronos/images/fleche.png" alt="[add]" /> afficher le classement</a></td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr><td colspan="2">Aucun résultat trouvé</td></tr>
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
				url: '/pronos/ajax/add_league.php',
				data: {id: id},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup input[type=text]').focus();
					$('#popup form').ajaxForm({
						url: '/pronos/ajax/save_league.php',
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
				url: '/pronos/ajax/show_rankings.php',
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
				url: '/pronos/ajax/show_teams.php',
				data: {id: league},
				success: function (response) {
					$.modal.close();
					$('#popup_content').html(response);
					$('#popup').modal({close: false});
					$('#popup form').ajaxForm({
						url: '/pronos/ajax/save_season_teams.php',
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