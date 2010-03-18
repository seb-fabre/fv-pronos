<?php
	require_once('includes/init.php');
	
	if (!isset($_GET['league']))
	{
		$adresse = preg_match('/classement-([0-9]+)(-([0-9]+))?/', $_SERVER['REQUEST_URI'], $matches);
		$_GET['league'] = $matches[1];
	
		if (count($matches) == 0)
			die;
	}

	$season = Season::find($_GET['league']);
	$league = $season->getLeague();

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
	
	$teams = Team::getAll();

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

		return strcmp(strtolower($users[$x['total']['id']]->name), strtolower($users[$y['total']['id']]->name));
	}

	function sortPrevious($x, $y)
	{
		global $users, $sort;
		$sort = 'previous';

		if ($x['total'][$sort] != $y['total'][$sort])
			return $x['total'][$sort] < $y['total'][$sort];

		if ($x['total']['3pts'] != $y['total']['3pts'])
			return $x['total']['3pts'] < $y['total']['3pts'];

		if ($x['played'] != $y['played'])
			return $x['played'] > $y['played'];

		return strcmp(strtolower($users[$x['total']['id']]->name), strtolower($users[$y['total']['id']]->name));
	}

	function sortAvg($x, $y)
	{
		global $users;

		if ($x['avg'] != $y['avg'])
			return $x['avg'] < $y['avg'];

		if ($x['played'] != $y['played'])
			return $x['played'] > $y['played'];

		return strcmp(strtolower($users[$x['total']['id']]->name), strtolower($users[$y['total']['id']]->name));
	}

	function sortDay($x, $y)
	{
		global $users, $sort;

		if ($x[$sort]['total'] != $y[$sort]['total'])
			return $x[$sort]['total'] < $y[$sort]['total'];

		return strcmp(strtolower($users[$x['total']['id']]->name), strtolower($users[$y['total']['id']]->name));
	}

	$maxDay = -1;
	foreach ($days as $i => $day)
	{
		if (!$day->hasCompletedMatches())
		{
			unset($days[$i]);
			continue;
		}
	
		if ($maxDay < $day->number)
			$maxDay = $day->number;
	}

	$scores = array();
	$high = array();
	foreach ($users as $user)
	{
		$scores[$user->id] = array();
		foreach ($days as $day)
			$scores[$user->id][$day->id] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0);
		$scores[$user->id]['total'] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0, 'previous' => 0);
		$scores[$user->id]['high'] = 0;
		$scores[$user->id]['played'] = array();
		$high[$user->id] = 0;
	}

	foreach ($pronos as $prono)
	{
		$match = $matches[$prono->pr_match_id];

		if (!isset($days[$match->pr_day_id]))
			continue;

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
				if ($maxDay > $days[$match->pr_day_id]->number)
					$scores[$prono->pr_user_id]['total']['previous'] += 3;
			}
			else
			{
				$scores[$prono->pr_user_id][$match->pr_day_id]['1pt'] ++;
				$scores[$prono->pr_user_id][$match->pr_day_id]['total'] ++;
				$scores[$prono->pr_user_id]['total']['1pt'] ++;
				$scores[$prono->pr_user_id]['total']['total'] ++;
				if ($maxDay > $days[$match->pr_day_id]->number)
					$scores[$prono->pr_user_id]['total']['previous'] ++;
			}
		}
		if ($scores[$prono->pr_user_id][$match->pr_day_id]['total'] > $high[$prono->pr_user_id])
			$high[$prono->pr_user_id] = $scores[$prono->pr_user_id][$match->pr_day_id]['total'];

	}

	// post traitement
	foreach ($scores as $user => $score)
	{
		$scores[$user]['played'] = count($scores[$user]['played']);
		$scores[$user]['avg'] = $scores[$user]['played'] != 0 ? round($scores[$user]['total']['total'] / $scores[$user]['played'], 2) : 0;
		
		if ($scores[$user]['total']['total'] == 0)
		{
			unset($scores[$user]);
			unset($users[$user]);
		}
	}
	
	$previous = $scores;
	uasort($scores, 'sortUsers');
	uasort($previous, 'sortPrevious');
	
	// compute the previous
	$tmp = array();
	$i = 1;
	foreach ($previous as $u => $p)
	{
		$tmp[$u] = $i;
		$i++;
	}
	$previous = $tmp;
	
	$height = ceil(count($users) / 2) * 30 + 21;
	$image = imagecreate(860, $height);
	
	$white = imagecolorallocate($image, 255, 255, 255);
	$red = imagecolorallocate($image, 255, 50, 50);
	$blood = imagecolorallocate($image, 255, 0, 0);
	$black = imagecolorallocate($image, 0, 0, 0);
	$blue = imagecolorallocate($image, 50, 100, 255);
	$grey = imagecolorallocate($image, 150, 150, 150);
	$yellow = imagecolorallocate($image, 255, 255, 0);
	$orange = imagecolorallocate($image, 255, 120, 0);
	$green = imagecolorallocate($image, 100, 255, 100);
	$khaki = imagecolorallocate($image, 95, 158, 160);
	$green8 = imagecolorallocate($image, 111, 255, 250);
	$green9 = imagecolorallocate($image, 0, 255, 0);
	$green10 = imagecolorallocate($image, 173, 255, 47);
	
	imagefilledrectangle($image, 0, 0, 429, 20, $black);
	imagefilledrectangle($image, 0, 0, 20, $height, $black);
	imagefilledrectangle($image, 145, 21, 194, $height, $yellow);
	imagefilledrectangle($image, 230, 21, 269, $height, $blue);
	imagefilledrectangle($image, 270, 21, 309, $height, $orange);
	
	imagefilledrectangle($image, 441, 0, 859, 20, $black);
	imagefilledrectangle($image, 431, 0, 450, $height, $black);
	imagefilledrectangle($image, 576, 21, 624, $height, $yellow);
	imagefilledrectangle($image, 661, 21, 699, $height, $blue);
	imagefilledrectangle($image, 701, 21, 739, $height, $orange);
	
	imagestring($image, 3, 3, 2, utf8_decode("Classement"), $white);
	imagestring($image, 3, 150, 2, utf8_decode("Total"), $white);
	imagestring($image, 3, 200 + 2, 2, utf8_decode("+/-"), $white);
	imagestring($image, 3, 240 - 1, 2, utf8_decode("3pts"), $white);
	imagestring($image, 3, 280, 2, utf8_decode("1pts"), $white);
	imagestring($image, 3, 319, 2, utf8_decode("Part"), $white);
	imagestring($image, 3, 364, 2, utf8_decode("AVG"), $white);
	imagestring($image, 3, 399, 2, utf8_decode("Top"), $white);
	
	imagestring($image, 3, 443, 2, utf8_decode("Classement"), $white);
	imagestring($image, 3, 580, 2, utf8_decode("Total"), $white);
	imagestring($image, 3, 630 + 2, 2, utf8_decode("+/-"), $white);
	imagestring($image, 3, 670 - 1, 2, utf8_decode("3pts"), $white);
	imagestring($image, 3, 710, 2, utf8_decode("1pts"), $white);
	imagestring($image, 3, 749, 2, utf8_decode("Part"), $white);
	imagestring($image, 3, 794, 2, utf8_decode("AVG"), $white);
	imagestring($image, 3, 829, 2, utf8_decode("Top"), $white);
	
	imageline($image, 55, 21, 55, $height, $grey);
	imageline($image, 145, 21, 145, $height, $grey);
	imageline($image, 195, 21, 195, $height, $grey);
	imageline($image, 230, 21, 230, $height, $grey);
	imageline($image, 270, 21, 270, $height, $grey);
	imageline($image, 310, 21, 310, $height, $grey);
	imageline($image, 350, 21, 350, $height, $grey);
	imageline($image, 395, 21, 395, $height, $grey);
	imageline($image, 429, 21, 429, $height, $grey);
	
	imageline($image, 485, 21, 485, $height, $grey);
	imageline($image, 575, 21, 575, $height, $grey);
	imageline($image, 625, 21, 625, $height, $grey);
	imageline($image, 660, 21, 660, $height, $grey);
	imageline($image, 700, 21, 700, $height, $grey);
	imageline($image, 740, 21, 740, $height, $grey);
	imageline($image, 780, 21, 780, $height, $grey);
	imageline($image, 825, 21, 825, $height, $grey);
	imageline($image, 859, 21, 859, $height, $grey);

	// echo '<table id="rankings">';
	// echo '<tr><th>&nbsp;</th><th>Journées</th>';
	// echo '<th title="score total">Total</th>';
	// echo '<th title="score total">Précédent</th>';
	// echo '<th title="nombre de participations">Part.</th>';
	// echo '<th title="nombre de scores exacts">3pts</th>';
	// echo '<th title="nombre de bon résultats">1pt</th>';
	// echo '</tr>';
	$i = 1;
	$top = -5;
	$half = false;
	foreach ($scores as $user => $scores)
	{
		if (!$scores)
			continue;
		$top += 30;
		$left = 20;
		if ($i > ceil(count($users)/2))
		{
			$left = 450;
			if (!$half)
			{
				$top = 25;
				$half = true;
			}
		}
		
		$u = $users[$user];
		if ($u->pr_team_id)
		{
			$logo = imagecreatefromgif("./logos/" . strtolower($teams[$u->pr_team_id]->name) . ".gif");
			imagecopyresized($image, $logo, $left + 4, $top + 1 - 5, 0, 0, 30, 30, 60, 60) ? 'Y' : 'N';
		}
		
		if ($previous[$user] == $i)
			$diff = '=';
		else if ($previous[$user] > $i)
		{
			$diff = '+' . ($previous[$user] - $i);
			imagefilledrectangle($image, 176 + $left, $top + 1 - 5, 209 + $left, $top + 30 - 5, $green);
		}
		else
		{
			$diff = '-' . ($i - $previous[$user]);
			imagefilledrectangle($image, 176 + $left, $top + 1 - 5, 209 + $left, $top + 30 - 5, $red);
		}
		
		if ($scores['avg'] < 5)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $blood);
		else if ($scores['avg'] < 6)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $orange);
		else if ($scores['avg'] < 7)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $khaki);
		else if ($scores['avg'] < 8)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $green8);
		else if ($scores['avg'] < 9)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $green9);
		else
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $green10);
		
		imagestring($image, 3, $left + 4 - 20, $top + 3, $i, $white);
		imagestring($image, 3, $left + 40, $top + 3, utf8_decode($users[$user]->name), $black);
		imagestring($image, 3, $left + 138, $top + 3, str_pad($scores['total']['total'], 3, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 195 - 4 * strlen($diff), $top + 3, $diff, $black);
		imagestring($image, 3, $left + 225, $top + 3, str_pad($scores['total']['3pts'], 2, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 264, $top + 3, str_pad($scores['total']['1pt'], 3, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 304, $top + 3, str_pad($scores['played'], 2, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 341, $top + 3, str_pad($scores['avg'], 3, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 384, $top + 3, str_pad($high[$user], 2, ' ', STR_PAD_LEFT), $black);
		
		imageline($image, $left + 1, $top + 30 - 5, $left + 409, $top + 30 - 5, $grey);
		$i++;
	}
	header ("Content-type: image/png");
	
	imagepng($image);
?>