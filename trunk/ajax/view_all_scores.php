<?php 
	session_start();
	require_once('../mysql_connexion.php');
	require_once('../includes.php');
	
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
		if ($x['total']['total'] != $y['total']['total'])
			return $x['total']['total'] < $y['total']['total'];
		
		if ($x['total']['3pts'] != $y['total']['3pts'])
			return $x['total']['3pts'] < $y['total']['3pts'];
		
		if ($x['total']['played'] != $y['total']['played'])
			return $x['total']['played'] > $y['total']['played'];
		
		return $users[$x['total']['id']]->name < $users[$y['total']['id']]->name;
	}
	
	$league = League::find($_GET['league']);
	
	$days = $league->getDays();
	
	$users = User::getAll();
	
	$pronos = $league->getPronos();
	
	$matches = $league->getMatches();
	
	$scores = array();
	foreach ($users as $user)
	{
		$scores[$user->id] = array();
		foreach ($days as $day)
			$scores[$user->id][$day->id] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0, 'played' => 0);
		$scores[$user->id]['total'] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0, 'played' => 0);
	}
	
	$high = array();
	foreach ($days as $day)
		$high[$day->id] = 0;
	
	foreach ($pronos as $prono)
	{
		$match = $matches[$prono->pr_match_id];
		
		$scores[$prono->pr_user_id][$match->pr_day_id]['played'] = 1;
		$scores[$prono->pr_user_id]['total']['played'] = 1;
		
		if (sign($match) == sign($prono))
		{
			if ($match->home_goals == $prono->home_goals && $match->away_goals == $prono->away_goals)
			{
				$scores[$prono->pr_user_id][$match->pr_day_id]['3pts'] ++;
				$scores[$prono->pr_user_id][$match->pr_day_id]['total'] += 3;
				$scores[$prono->pr_user_id][$match->pr_day_id]['played'][$match->pr_day_id] += 3;
				$scores[$prono->pr_user_id]['total']['3pts'] ++;
				$scores[$prono->pr_user_id]['total']['total'] += 3;
				$scores[$prono->pr_user_id]['total']['played'][$match->pr_day_id] += 3;
			}
			else
			{
				$scores[$prono->pr_user_id][$match->pr_day_id]['1pt'] ++;
				$scores[$prono->pr_user_id][$match->pr_day_id]['total'] ++;
				$scores[$prono->pr_user_id][$match->pr_day_id]['played'][$match->pr_day_id] ++;
				$scores[$prono->pr_user_id]['total']['1pt'] ++;
				$scores[$prono->pr_user_id]['total']['total'] ++;
				$scores[$prono->pr_user_id]['total']['played'][$match->pr_day_id] ++;
			}
		}
		if ($scores[$prono->pr_user_id][$match->pr_day_id]['total'] > $high[$match->pr_day_id])
			$high[$match->pr_day_id] = $scores[$prono->pr_user_id][$match->pr_day_id]['total'];
	}
	
	// post traitement
	foreach ($scores as $user => $score)
	{
		$scores[$user]['high'] = max($scores[$user]['played']);
		$scores[$user]['played'] = count($scores[$user]['played']);
	}
	
	uasort($scores, 'sortUsers');
	
	echo '<table id="rankings">';
	echo '<tr><th>&nbsp;</th><th>Joueur</th>';
	foreach ($days as $day)
		echo '<th>' . $day->number . '</th>';
	echo '<th>Total</th>';
	echo '</tr>';
	$i = 1;
	foreach ($scores as $user => $scores)
	{
		echo '<tr>';
		echo '<td>' . $i . '</td>';
		echo '<td>' . $users[$user]->name . '</td>';
		foreach ($days as $day)
		{
			if ($high[$day->id] == $scores[$day->id]['total'])
				echo '<td class="right bold tooltipped">' . $scores[$day->id]['total'] . '</td>';
			else
				echo '<td class="right tooltipped">' . $scores[$day->id]['total'];
			echo '<div class="hidden">';
			echo '<p>' . $scores[$day->id]['total'] . '</p>';
			echo '<p>' . $scores[$day->id]['3pts'] . '</p>';
			echo '<p>' . $scores[$day->id]['1pt'] . '</p>';
			echo '<p>' . $scores[$day->id]['played'] . '</p>';
			echo '<p>' . $scores[$day->id]['high'] . '</p>';
			echo '</div></td>';
		}
		echo '<td class="right">' . $scores['total']['total'] . '</td>';
		echo '</tr>';
		$i++;
	}
	echo '</table>';
?>