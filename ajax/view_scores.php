<?php 
	require_once('../includes/init.php');
	
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
		if ($x['total'] != $y['total'])
			return $x['total'] < $y['total'];
		
		if ($x['3pts'] != $y['3pts'])
			return $x['3pts'] < $y['3pts'];
		
		if ($x['played'] != $y['played'])
			return $x['played'] > $y['played'];
		
		return $users[$x['id']]->name < $users[$y['id']]->name;
	}
	
	$league = League::find($_GET['league']);
	
	$days = $league->getDays();
	
	if (isset($_GET['user']))
		$users = array($_GET['user'] => User::find($_GET['user']));
	else
		$users = User::getAll();
	
	$pronos = $league->getPronos();
	
	$matches = $league->getMatches();
	
	$scores = array();
	foreach ($users as $user)
	{
		$scores[$user->id] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0,'played' => array());
	}
	
	foreach ($pronos as $prono)
	{
		$match = $matches[$prono->pr_match_id];
		
		if (!isset($scores[$prono->pr_user_id]['played'][$match->pr_day_id]))
			$scores[$prono->pr_user_id]['played'][$match->pr_day_id] = 0;
		
		if (sign($match) == sign($prono))
		{
			if ($match->home_goals == $prono->home_goals && $match->away_goals == $prono->away_goals)
			{
				$scores[$prono->pr_user_id]['3pts'] ++;
				$scores[$prono->pr_user_id]['total'] += 3;
				$scores[$prono->pr_user_id]['played'][$match->pr_day_id] += 3;
			}
			else
			{
				$scores[$prono->pr_user_id]['1pt'] ++;
				$scores[$prono->pr_user_id]['total'] ++;
				$scores[$prono->pr_user_id]['played'][$match->pr_day_id] ++;
			}
		}
	}
	
	// post traitement
	foreach ($scores as $user => $score)
	{
		$scores[$user]['high'] = max($scores[$user]['played']);
		$scores[$user]['played'] = count($scores[$user]['played']);
	}
	
	uasort($scores, 'sortUsers');
	
	echo '<table id="rankings">';
	echo '<tr><th>&nbsp;</th><th>Joueur</th><th>Total</th><th>3 pts</th><th>1 pt</th><th>Joués</th><th>Record</th></tr>';
	$i = 1;
	foreach ($scores as $user => $scores)
	{
		echo '<tr>';
		echo '<td>' . $i . '</td>';
		echo '<td>' . $users[$user]->name . '</td>';
		echo '<td class="right">' . $scores['total'] . '</td>';
		echo '<td class="right">' . $scores['3pts'] . '</td>';
		echo '<td class="right">' . $scores['1pt'] . '</td>';
		echo '<td class="right">' . $scores['played'] . '</td>';
		echo '<td class="right">' . $scores['high'] . '</td>';
		echo '</tr>';
		$i++;
	}
	echo '</table>';
?>