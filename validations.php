<?php
	session_start();

	require_once('mysql_connexion.php');
	require_once('includes.php');

	$leagues = League::getAll();

	$seasons = Season::getAll();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Vérification de la base</title>
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
		<h1>Vérification de la base</h1>
		<?php
			foreach ($seasons as $season)
			{
				$league = $leagues[$season->pr_league_id];

				$teams = $season->getTeams();

				$days = $season->getDays();

				echo '<h2>' . $league->name . '</h2>';
				echo '<h3>trouvé ' . count($teams) . ' équipes</h3>';
				echo '<h3>trouvé ' . count($days) . ' journées</h3>';
				echo '<ul>';
				foreach ($days as $day)
				{
					$matches = $day->getMatches();
					echo '<li>';
					echo '<p>journée ' . $day->number . ' : trouvé ' . count($matches) . ' matches</p>';
					foreach ($matches as $match)
					{
						if (is_null($match->home_goals) || is_null($match->away_goals))
							echo '<i>le score du match ' . $teams[$match->pr_home_team_id]->name . ' - ' . $teams[$match->pr_away_team_id]->name . ' est incomplet</i><br/>';
					}
					echo '</li>';
				}
				echo '</ul>';
			}
		?>
	</div>

	<div id="popup"><div id="popup_message"></div><div id="popup_content"></div></div>

	<div id="loading">
		<div id="subloading">Chargement</div>
	</div>
</body>
</html>