<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');
	$name = GETorPOST('name');

	if ($id == -1)
		$team = new Team();
	else
		$team = Team::find($id);

	if (!$team)
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Equipe invalide'));
		exit;
	}

	if (!Team::isUnique('name', $name, $team->id))
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Le nom ' . $name . ' est déjà utilisé'));
		exit;
	}

	$team->name = $name;
	$team->save();

	if (!empty($_FILES['logo']))
	{
		$target_path = $GLOBALS['ROOTPATH'] . "logos/" . $team->id . '.gif';

		if (file_exists($target_path))
			unlink($target_path);

		if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_path))
		{
			$team->has_logo = 1;
		}
		else
		{
			$team->has_logo = 0;
		}
	}
	else if (GETorPOST('remove_logo') == '1')
	{
		$team->has_logo = 0;
	}

	$team->save();

	if ($id == -1)
		echo json_encode(array('success' => 1, 'message' => 'Equipe enregistrée', 'create' => 1));
	else
		echo json_encode(array('success' => 1, 'message' => 'Equipe enregistrée'));
?>
