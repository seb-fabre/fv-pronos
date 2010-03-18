<?php
	require_once('includes/init.php');

	if (!isset($_GET['league']) || !isset($_GET['users']))
  {
    echo 'paramètres invalides ... ';
    die;
  }

  $season = Season::find($_GET['league']);
	$league = $season->getLeague();
	
	$userz = $_GET['users'];
	$users = explode('-', $userz);
	$userz = array();
	foreach ($users as $u)
		$userz[$u] = User::find($u);

	if (!$league || !$season || count($userz) == 0)
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
		global $users, $globalDay;

		if ($x[$globalDay]['total'] != $y[$globalDay]['total'])
			return $x[$globalDay]['total'] < $y[$globalDay]['total'];

		if ($x[$globalDay]['3pts'] != $y[$globalDay]['3pts'])
			return $x[$globalDay]['3pts'] < $y[$globalDay]['3pts'];
			
		if ($x[$globalDay]['played'] != $y[$globalDay]['played'])
			return $x[$globalDay]['played'] < $y[$globalDay]['played'];

		return $users[$x[$globalDay]['id']]->name < $users[$y[$globalDay]['id']]->name;
	}

	$scores = array();
	$scoreDays = array();
	foreach ($users as $user)
	{
		$scores[$user->id] = array();
		$scoreDays[$user->id] = array();
		foreach ($days as $day)
		{
			$scores[$user->id][$day->id] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0);
			$scoreDays[$user->id][$day->number] = array('id' => $user->id, '3pts' => 0, '1pt' => 0, 'total' => 0, 'played' => 0);
		}
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
		
		foreach ($days as $day)
		{
			foreach ($days as $d)
			{
				if ($day->number >= $d->number && isset($scores[$user][$d->id]))
				{
					$scoreDays[$user][$day->number]['total'] += $scores[$user][$d->id]['total'];
					$scoreDays[$user][$day->number]['3pts'] += $scores[$user][$d->id]['3pts'];
					$scoreDays[$user][$day->number]['1pt'] += $scores[$user][$d->id]['1pt'];
					$scoreDays[$user][$day->number]['played']++;
				}
			}
		}
	}
	
	$globalRankings = array();
	foreach ($days as $day)
	{
		$globalDay = $day->number;
		$globalRankings[$day->number] = array();
		uasort($scoreDays, 'sortUsers');
		foreach ($scoreDays as $user => $scores)
		{
			$globalRankings[$day->number] []= $user;
		}
	}
	ksort($globalRankings);
	
	header ("Content-type: image/png");
	
	$height = count(reset($globalRankings)) * 10 - 9;
	$width = count($globalRankings) * 20 + 1;
	$image = imagecreate($width, $height);
	
	$white = imagecolorallocate($image, 255, 255, 255);
	$red = imagecolorallocate($image, 255, 50, 50);
	$blood = imagecolorallocate($image, 255, 0, 0);
	$black = imagecolorallocate($image, 0, 0, 0);
	$blue = imagecolorallocate($image, 50, 100, 255);
	$grey = imagecolorallocate($image, 180, 180, 180);
	$yellow = imagecolorallocate($image, 255, 255, 0);
	$orange = imagecolorallocate($image, 255, 120, 0);
	$green = imagecolorallocate($image, 100, 255, 100);
	$khaki = imagecolorallocate($image, 95, 158, 160);
	
	$colors = array($blood, $blue, $green, $yellow);
	
	for ($i=0; $i<count(reset($globalRankings)); $i++)
	{
		imageline($image, 20, 10 * $i, $width, 10 * $i, $grey);
		if ($i%5 == 0 && $i != 0)
			imagestring($image, 2, 2, 10 * $i - 16, str_pad($i, 2, ' ', STR_PAD_LEFT), $black);
		else if ($i == 1)
			imagestring($image, 2, 2, 10 * $i - 10, ' 1', $black);
	}
	
	$i = 0;
	foreach ($userz as $u)
	{
		$color = $colors[$i];
		$prev = false;
		foreach ($globalRankings as $day => $uss)
		{
			imageline($image, 20 * $day, 0, 20 * $day, $height, $grey);
		
			$j = array_search($u->id, $uss);
			if ($prev !== false)
				imageline($image, 20 * $day - 20, 10 * $prev, 20 * $day, 10 * $j, $color);
			imagefilledellipse($image, 20 * $day, 10 * $j, 2, 2, $color);
			$prev = $j;
		}
		$i++;
	}
	
	imagepng($image);
/*
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
		$i = 1;
		echo '<tr>';
		foreach ($globalRankings as $day => $uss)
		{
			echo '<th>' . $day . '</th>';
		}
		echo '</tr>';
		for ($i=0; $i<count(reset($globalRankings)); $i++)
		{
			echo '<tr>';
			foreach ($globalRankings as $day => $u)
			{
				echo '<td>' . $users[$u[$i]]->name . '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	?>
</body>
</html>
*/ ?>