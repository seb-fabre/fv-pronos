<?php
	require_once('mysql_connexion.php');
	require_once('includes.php');

	if (!isset($_GET['league']))
  {
    echo 'paramètres invalides ... ';
    die;
  }

  $season = Season::find($_GET['league']);
	$league = $season->getLeague();

	if (!$league || !$season)
  {
    echo 'paramètres invalides ... ';
    die;
  }

	if (isset($_GET['sort']))
		$sort = $_GET['sort'];
	else
		$sort = 'total';

	$days = $season->getDays();

	if (isset($_GET['day']))
		$max = $_GET['day'];
	else
	{
    $teams = $season->getTeams();
    $max = (count($teams) - 1) * 2;
	}

	$users = User::getAll();

	$pronos = $season->getPronos();

	$matches = $season->getMatches();

	if (preg_match('/^[0-9]+$/', $sort))
	{
		$found = false;
		foreach ($days as $day)
			if ($day->number == $sort)
			{
				$sort = $day->id;
				$found = true;
				break;
			}
		if (!$found)
			$sort = 'total';
	}

	function sign($x)
	{
		if (is_null($x->home_goals) || is_null($x->away_goals))
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

		return $users[$x['total']['id']]->name < $users[$y['total']['id']]->name;
	}

	function sortAvg($x, $y)
	{
		global $users;

		if ($x['avg'] != $y['avg'])
			return $x['avg'] < $y['avg'];

		if ($x['played'] != $y['played'])
			return $x['played'] > $y['played'];

		return $users[$x['total']['id']]->name < $users[$y['total']['id']]->name;
	}

	function sortDay($x, $y)
	{
		global $users, $sort;

		if ($x[$sort]['total'] != $y[$sort]['total'])
			return $x[$sort]['total'] < $y[$sort]['total'];

		return $users[$x['total']['id']]->name < $users[$y['total']['id']]->name;
	}

	$scores = array();
	foreach ($users as $user)
	{
		$scores[$user->id] = array();
		foreach ($days as $day)
			$scores[$user->id][$day->id] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0);
		$scores[$user->id]['total'] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0);
		$scores[$user->id]['high'] = 0;
		$scores[$user->id]['played'] = array();
	}

	$high = array();
	foreach ($days as $day)
		$high[$day->id] = -1;

	foreach ($pronos as $prono)
	{
		$match = $matches[$prono->pr_match_id];

		if ($days[$match->pr_day_id]->number > $max)
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

	if ($sort == 'total' || $sort == '3pts' || $sort == '1pt')
		uasort($scores, 'sortUsers');
	else if ($sort == 'avg')
		uasort($scores, 'sortAvg');
	else
		uasort($scores, 'sortDay');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Classement</title>
	<link rel="stylesheet" href="/pronos/css/screen.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="/pronos/css/pronos.css" type="text/css" media="screen" />
	<script type="text/javascript" src="/pronos/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="/pronos/js/jquery.simplemodal-1.2.3.js"></script>
	<script type="text/javascript" src="/pronos/js/jquery.form-2.24.js"></script>
	<script type="text/javascript" src="/pronos/js/jquery.qtip-1.0.0-rc3.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
</head>

<body style="width: auto">
	<?php
		echo '<table id="rankings">';
		echo '<tr><th>&nbsp;</th><th>Journées</th>';
		foreach ($days as $day)
			echo '<th><a href="scores.php?league=' . $season->id . '&day=' . $day->number . '">' . $day->number . '</a></th>';
		echo '<th title="score total"><a href="scores.php?league=' . $season->id . '&sort=total">Total</a></th>';
		echo '<th title="nombre de participations">Part.</th>';
		echo '<th title="moyenne de points par journée"><a href="scores.php?league=' . $season->id . '&sort=avg">AVG</a></th>';
		echo '<th title="nombre de scores exacts"><a href="scores.php?league=' . $season->id . '&sort=3pts">3pts</a></th>';
		echo '<th title="nombre de bon résultats"><a href="scores.php?league=' . $season->id . '&sort=1pt">1pt</a></th>';
		echo '</tr>';
		$i = 1;
		foreach ($scores as $user => $scores)
		{
			if ($scores['total']['total'] == 0)
				continue;
			echo '<tr>';
			echo '<td class="bold">' . $i . '</td>';
			echo '<td class="bold">' . $users[$user]->name . '</td>';
			foreach ($days as $day)
			{
				if ($high[$day->id] == $scores[$day->id]['total'])
					echo '<td class="right topscore tooltipped">' . $scores[$day->id]['total'] . '</td>';
				else
					echo '<td class="right tooltipped">' . $scores[$day->id]['total'] . '</td>';
			}
			echo '<td class="right">' . $scores['total']['total'] . '</td>';
			echo '<td class="right">' . $scores['played'] . '</td>';
			echo '<td class="right">' . $scores['avg'] . '</td>';
			echo '<td class="right">' . $scores['total']['3pts'] . '</td>';
			echo '<td class="right">' . $scores['total']['1pt'] . '</td>';
			echo '</tr>';
			$i++;
		}
		echo '</table>';
	?>
</body>
</html>