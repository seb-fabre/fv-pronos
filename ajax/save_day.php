<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');
	$season = GETorPOST('pr_season_id');
	$number = GETorPOST('number');
	$limitDate = GETorPOST('limit_date');
	$limitTime = GETorPOST('limit_time');

	if (!$season)
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Championnat invalide'));
		exit;
	}

	if ($id == -1)
		$day = new Day();
	else
		$day = Day::find($id);

	if (!$day)
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Journée invalide'));
		exit;
	}

	$isEditable = !$day->hasPronos() && !$day->hasCompletedMatches() && !empty($_SESSION['user']);
	if (!$isEditable)
	{
		echo json_encode(array('sucess' => 0, 'message' => "Cette journée n'est pas éditable"));
		exit;
	}

	$errors = array();

	if (!$number)
	{
		$errors []= 'Numéro de journée invalide';
	}

	// FIXME : change this format when add localisations
	if (!empty($limitDate) && !preg_match('@^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})$@', $limitDate, $matches))
	{
		$errors []= 'Format de date invalide';
	}

	// FIXME : change this format when add localisations
	if (!empty($limitTime) && !preg_match('@^[0-2]?[0-9](:[0-5][0-9])?$@', $limitTime))
	{
		$errors []= "Format d'heure invalide";
	}

	if (!empty($errors))
	{
		echo json_encode(array('sucess' => 0, 'message' => implode('<br/>', $errors)));
		exit;
	}
	
	if (empty($limitTime))
		$limitTime = '23:59:00';
	else if (strpos($limitTime, ':') !== false)
		$limitTime .= ':00';
	else
		$limitTime .= '00::00';

	// FIXME : change format when add localisations
	if (!empty($matches))
		$limit_date = $matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' ' . $limitTime;
	else
		$limit_date = NULL;

	$day->pr_season_id = $season;
	$day->number = $number;
	$day->limit_date = $limit_date;
	$day->save();

	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Journée enregistrée', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Journée enregistrée'));
?>
