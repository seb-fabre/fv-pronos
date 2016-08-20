<?php
require_once('../includes/init.php');

$id = GETorPOST('id', -1);
$name = GETorPOST('name','');

$message = '';

$teamCategory = TeamCategory::find($id);
if (!$teamCategory)
	$teamCategory = new TeamCategory();

if (!empty($name))
{
	if (!TeamCategory::isUnique('name', $name, $teamCategory->id))
	{
		echo json_encode(array('sucess' => 0, 'message' => 'Ce nom est déjà utilisé.'));
		exit;
	}

	$teamCategory->name = $name;
	$teamCategory->save();

	echo json_encode(array('success' => 1, 'message' => 'Catégorie enregistrée'));
	exit;
}

echo json_encode(array('success' => 1, 'message' => 'Données manquantes'));
