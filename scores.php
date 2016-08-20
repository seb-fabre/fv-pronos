<?php
	require_once('includes/init.php');

	$pathinfo = $_SERVER['PATH_INFO'];

	$urlMatches = false;
	$match = preg_match('@^/season-([0-9]+)(/day-([0-9]+))?(/sort-([^/]+))?$@', $pathinfo, $urlMatches);

	if (!isset($urlMatches[1]))
		header('location: ' . APPLICATION_URL);

	if (isset($urlMatches[5]))
		$sort = $urlMatches[5];
	else
		$sort = 'total';

	if (empty($sort) || !in_array($sort, array('avg', '1pt', '3pts', 'total')))
		$sort = 'total';

	if ($sort == 'total' || $sort == '3pts' || $sort == '1pt')
		$sortCallback = 'sortUsers';
	else if ($sort == 'avg')
		$sortCallback = 'sortAvg';
	else
		$sortCallback = 'sortDay';

	$season = Season::find($urlMatches[1]);

	if (!empty($urlMatches[3]))
		$max = $urlMatches[3];
	else
	{
		$teams = $season->getTeams();
		$max = (count($teams) - 1) * 2;
	}

	$cache = ScoresCache::Search(array(
		array('pr_season_id', $season->id),
		array('sort_callback', $sortCallback),
		array('max_day', isset($urlMatches[3]) ? $urlMatches[3] : 0),
	));

	$cache = reset($cache);
	
	$league = $season->getLeague();

	if (empty($cache) || !$season->modification_date || $season->modification_date > $cache->cache_date)
	{
		if (!$league || !$season)
		  {
			echo 'paramètres invalides ... ';
			die;
		  }

		$daysObjects = $season->getDays('number');

		$days = array();
		foreach ($daysObjects as $day)
			$days[$day->id] = array('id' => $day->id, 'number' => $day->number);

		$usersObjects = User::getAll();

		$users = array();
		foreach ($usersObjects as $user)
			$users[$user->id] = array('id' => $user->id, 'name' => $user->name);

		$pronos = $season->getPronos();

		$matches = $season->getMatchs();

		if (preg_match('/^[0-9]+$/', $sort))
		{
			$found = false;
			foreach ($days as $day)
				if ($day['number'] == $sort)
				{
					$sort = $day['id'];
					$found = true;
					break;
				}
			if (!$found)
				$sort = 'total';
		}

		function sign($x)
		{
			if (strlen($x->home_goals) == 0 || strlen($x->away_goals) == 0)
				return -5;
			if ($x->home_goals == $x->away_goals)
				return 0;
			if ($x->home_goals > $x->away_goals)
				return 1;
			return -1;
		}

		function sortUsers($x, $y)
		{
			global $users, $sort;

			if ($x['total'][$sort] != $y['total'][$sort])
				return $x['total'][$sort] < $y['total'][$sort];

			if ($x['total']['3pts'] != $y['total']['3pts'])
				return $x['total']['3pts'] < $y['total']['3pts'];

			if ($x['played'] != $y['played'])
				return $x['played'] > $y['played'];

			return $users[$x['total']['id']]['name'] < $users[$y['total']['id']]['name'];
		}

		function sortAvg($x, $y)
		{
			global $users;

			if ($x['avg'] != $y['avg'])
				return $x['avg'] < $y['avg'];

			if ($x['played'] != $y['played'])
				return $x['played'] > $y['played'];

			return $users[$x['total']['id']]['name'] < $users[$y['total']['id']]['name'];
		}

		function sortDay($x, $y)
		{
			global $users, $sort;

			if ($x[$sort]['total'] != $y[$sort]['total'])
				return $x[$sort]['total'] < $y[$sort]['total'];

			return $users[$x['total']['id']]['name'] < $users[$y['total']['id']]['name'];
		}

		$scores = array();
		foreach ($users as $user)
		{
			$scores[$user['id']] = array();
			foreach ($days as $day)
				$scores[$user['id']][$day['id']] = array('id' => $user['id'], '3pts' => 0, '1pt' => 0, 'total' => 0);
			$scores[$user['id']]['total'] = array('id' => $user['id'], '3pts' => 0, '1pt' => 0, 'total' => 0);
			$scores[$user['id']]['high'] = 0;
			$scores[$user['id']]['played'] = array();
		}

		$high = array();
		foreach ($days as $day)
			$high[$day['id']] = -1;

		foreach ($pronos as $prono)
		{
			$match = $matches[$prono->pr_match_id];

			if ($days[$match->pr_day_id]['number'] > $max)
				continue;

			if (is_null($match->home_goals) || is_null($match->away_goals))
				continue;

			$scores[$prono->pr_user_id][$match->pr_day_id]['played'] = 1;
			$scores[$prono->pr_user_id]['total']['played'] = 1;
			$scores[$prono->pr_user_id]['played'][$match->pr_day_id] = 1;

			if (sign($match) == sign($prono))
			{
				if ($match->home_goals == $prono->home_goals && $match->away_goals == $prono->away_goals)
				{
					$scores[$prono->pr_user_id][$match->pr_day_id]['3pts'] ++;
					$scores[$prono->pr_user_id][$match->pr_day_id]['total'] += 3;
					$scores[$prono->pr_user_id]['total']['3pts'] ++;
					$scores[$prono->pr_user_id]['total']['total'] += 3;
				}
				else
				{
					$scores[$prono->pr_user_id][$match->pr_day_id]['1pt'] ++;
					$scores[$prono->pr_user_id][$match->pr_day_id]['total'] ++;
					$scores[$prono->pr_user_id]['total']['1pt'] ++;
					$scores[$prono->pr_user_id]['total']['total'] ++;
				}
			}
			if ($scores[$prono->pr_user_id][$match->pr_day_id]['total'] > $high[$match->pr_day_id])
				$high[$match->pr_day_id] = $scores[$prono->pr_user_id][$match->pr_day_id]['total'];

		}

		// post traitement
		foreach ($scores as $user => $score)
		{
			$scores[$user]['played'] = count($scores[$user]['played']);
			$scores[$user]['avg'] = $scores[$user]['played'] != 0 ? round($scores[$user]['total']['total'] / $scores[$user]['played'], 2) : 0;

			if ($scores[$user]['played'] == 0 || $scores[$user]['total']['total'] == 0)
				unset($scores[$user]);
		}

		uasort($scores, $sortCallback);

		if (empty($cache))
			$cache = new ScoresCache();

		$cache->pr_season_id = $season->id;
		$cache->sort_callback = $sortCallback;
		$cache->max_day = !empty($urlMatches[3]) ? $urlMatches[3] : NULL;
		$cache->cache_data = serialize(array($scores, $days, $high, $users));
		$cache->save();
	}
	else
	{
		list($scores, $days, $high, $users) = unserialize($cache->cache_data);
	}

	echoHTMLHead('Classement');
?>
<body>
	<div class="container">
		<?php echoMenu(); ?>
			
		<h1><?php echo $league->name ?> - <?php echo $season->label ?></h1>
			
		<?php
			echo '<table id="rankings" class="table table-bordered table-condensed">';

			$maxday = !empty($urlMatches[3]) ? '/day-' . $urlMatches[3] : '';

			echo '<tr>';
			echo '<th>&nbsp;</th><th>Journées</th>';
			foreach ($days as $day)
				echo '<th><a href="' . APPLICATION_URL . 'scores/season-' . $season->id . '/day-' . $day['number'] . '/sort-' . $sortCallback . '">' . $day['number'] . '</a></th>';
			echo '<th title="score total"><a href="' . APPLICATION_URL . 'scores/season-' . $season->id . $maxday . '/sort-total">Total</a></th>';
			echo '<th title="nombre de participations">Part.</th>';
			echo '<th title="moyenne de points par journée"><a href="' . APPLICATION_URL . 'scores/season-' . $season->id . $maxday . '/sort-avg">AVG</a></th>';
			echo '<th title="nombre de scores exacts"><a href="' . APPLICATION_URL . 'scores/season-' . $season->id . $maxday . '/sort-3pts">3pts</a></th>';
			echo '<th title="nombre de bon résultats"><a href="' . APPLICATION_URL . 'scores/season-' . $season->id . $maxday . '/sort-1pt">1pt</a></th>';
			echo '</tr>';

			$i = 1;
			foreach ($scores as $user => $scores)
			{
				if ($scores['total']['total'] == 0)
					continue;
				echo '<tr onclick="toggleSelectRow(this)">';
				echo '<td><strong>' . $i . '</strong></td>';
				echo '<td><strong>' . $users[$user]['name'] . '</strong></td>';
				foreach ($days as $day)
				{
					if ($high[$day['id']] == $scores[$day['id']]['total'])
						echo '<td class="text-right tooltipped"><strong>' . $scores[$day['id']]['total'] . '</strong></td>';
					else
						echo '<td class="text-right tooltipped">' . $scores[$day['id']]['total'] . '</td>';
				}
				echo '<td class="text-right">' . $scores['total']['total'] . '</td>';
				echo '<td class="text-right">' . $scores['played'] . '</td>';
				echo '<td class="text-right">' . $scores['avg'] . '</td>';
				echo '<td class="text-right">' . $scores['total']['3pts'] . '</td>';
				echo '<td class="text-right">' . $scores['total']['1pt'] . '</td>';
				echo '</tr>';
				$i++;
			}
			echo '</table>';
		?>
	<script type="text/javascript">
		function toggleSelectRow(theTr)
		{
			$('#rankings tr').removeClass('selectedRow');
			$(theTr).addClass('selectedRow');
		}
	</script>
	</div>
</body>
</html>