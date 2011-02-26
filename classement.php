<?php
	$GLOBALS['HEADER_CONTENT_TYPE'] = "Content-type: image/png";

	require_once('includes/init.php');

	$pathinfo = $_SERVER['PATH_INFO'];

  $matches = false;
  $match = preg_match('@^/season-([0-9]+)\.png?$@', $pathinfo, $matches);

	if (!isset($matches[1]))
		header('location: ' . APPLICATION_URL);

	$season = $matches[1];

	$season = Season::find($season);
	$league = $season->getLeague();

	$days = $season->getDays();

	$teams = Team::getAll();

	$users = User::getAll();

	$pronos = $season->getPronos();

	$matches = $season->getMatchs();

	$maxDay = $season->getLastDayWithCompletedMatches()->number;

	$userScores = $season->getClassementDetails();
	$classementDetails = array_flip(array_keys($userScores));

	$classementPrevious =  array_flip(array_keys($season->getClassementDetails($maxDay - 1)));

	$higScores = $season->getMaxPointsForADayForAUser();

	$height = ceil(count($userScores) / 2) * 30 + 21;
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

	foreach ($userScores as $userId => $userTotal)
	{
		$top += 30;
		$left = 20;
		if ($i > ceil(count($userScores)/2))
		{
			$left = 450;
			if (!$half)
			{
				$top = 25;
				$half = true;
			}
		}

		$user = $users[$userId];
		if ($user->pr_team_id && file_exists("./logos/" . strtolower($teams[$user->pr_team_id]->id) . ".gif"))
		{
			$logo = imagecreatefromgif("./logos/" . strtolower($teams[$user->pr_team_id]->id) . ".gif");
			imagecopyresized($image, $logo, $left + 4, $top + 1 - 5, 0, 0, 30, 30, 60, 60) ? 'Y' : 'N';
		}

		$classementPrevious[$userId]++;

		if ($classementPrevious[$userId] == $i)
			$diff = '=';
		else if ($classementPrevious[$userId] > $i)
		{
			$diff = '+' . ($classementPrevious[$userId] - $i);
			imagefilledrectangle($image, 176 + $left, $top + 1 - 5, 209 + $left, $top + 30 - 5, $green);
		}
		else
		{
			$diff = '-' . ($i - $classementPrevious[$userId]);
			imagefilledrectangle($image, 176 + $left, $top + 1 - 5, 209 + $left, $top + 30 - 5, $red);
		}

		if ($userTotal['average'] < 5)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $blood);
		else if ($userTotal['average'] < 6)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $orange);
		else if ($userTotal['average'] < 7)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $khaki);
		else if ($userTotal['average'] < 8)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $green8);
		else if ($userTotal['average'] < 9)
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $green9);
		else
			imagefilledrectangle($image, 331 + $left, $top + 1 - 5, 374 + $left, $top + 30 - 6, $green10);

		imagestring($image, 3, $left + 4 - 20, $top + 3, $i, $white);
		imagestring($image, 3, $left + 40, $top + 3, stripslashes(utf8_decode($users[$userId]->name)), $black);
		imagestring($image, 3, $left + 138, $top + 3, str_pad($userTotal['points'], 3, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 195 - 4 * strlen($diff), $top + 3, $diff, $black);
		imagestring($image, 3, $left + 225, $top + 3, str_pad($userTotal['three_points'], 2, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 264, $top + 3, str_pad($userTotal['one_point'], 3, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 304, $top + 3, str_pad($userTotal['played'], 2, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 341, $top + 3, str_pad(round($userTotal['average'], 2), 4, ' ', STR_PAD_LEFT), $black);
		imagestring($image, 3, $left + 384, $top + 3, str_pad($higScores[$userId], 2, ' ', STR_PAD_LEFT), $black);

		imageline($image, $left + 1, $top + 30 - 5, $left + 409, $top + 30 - 5, $grey);
		$i++;
	}

	imagepng($image);
