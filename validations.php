<?php
	require_once('includes/init.php');

	$leagues = League::getAll();

	$seasons = Season::getAll();

	echoHTMLHead('Vérification de la base');
?>

<body>
	<div class="container">
		<?php echoMenu(); ?>
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
</body>
</html>