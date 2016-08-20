<?php
	require_once('includes/init.php');

	$leagues = League::getAll('id DESC');
	$seasons = Season::getAll('id DESC');
	$users = User::getAll('id DESC');

	echoHTMLHead('Statistiques');
	
	$sql = "SELECT d.id, p1.pr_user_id 'user1', p2.pr_user_id 'user2', SUM(p1.home_goals = p2.home_goals AND p1.away_goals = p2.away_goals) 'common'
					FROM pr_day d 
					INNER JOIN pr_match m ON d.id = m.pr_day_id
					INNER JOIN pr_prono p1 ON p1.pr_match_id = m.id
					INNER JOIN pr_prono p2 ON p2.pr_match_id = m.id AND p1.pr_user_id != p2.pr_user_id
					GROUP BY d.id, p1.pr_user_id, p2.pr_user_id
					HAVING common > 5
					ORDER BY d.id DESC";
	
?>

<body>
	<div class="container">
		<?php echoMenu(); ?>
		<h1>Statistiques</h1>
		
		<?php
			
			$req = mysql_query($sql);
			$currentDay = false;
			
			while ($row = mysql_fetch_array($req))
			{
				$day = Day::find($row['id']);
				$season = $day->getSeason();
				$league = $season->getLeague();
				
				$countMatches = count($day->getMatches());
				
				$user1 = $users[$row['user1']];
				$user2 = $users[$row['user2']];
				
				$sim = round($row['common'] * 100 / $countMatches);
				
				if ($currentDay != $day->id)
					echo '<h3>' . $league->name . ' - ' . $season->label . ' - journée ' . $day->number . '</h3>';
				
				echo 'Joueurs : ' . $user1->name . ' & ' . $user2->name . '<br/>';
				echo 'Similarité des pronos : <b>' . $sim . '%</b>';
				
				$currentDay = $day->id;
				
				echo '<hr />';
			}
			
		?>
	</div>
</body>
</html>