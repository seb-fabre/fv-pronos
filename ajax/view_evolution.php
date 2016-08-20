<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');

	$season = Season::find($id);
	$league = $season->getLeague();

	$users = User::getAll('name ASC');

	echo '<div style="margin: 0 1.5em 0.5em">';
	echo 'Joueurs : ';
	echo Tools::objectsToSelect($users, 'name', array('id' => 'user_select'));
	echo '&nbsp;&nbsp;<input type="button" value="ajouter le joueur" onclick="addUser()" />';
	echo '&nbsp;&nbsp;<input type="button" value="recharger le graphe" onclick="reloadGraph(' . $season->id . ')" />';
	echo '</div>';
?>

<ul class="input_holder" id="users_holder"></ul>
<div style="clear: both; width: 100%;">&nbsp;</div>
<div id="evolutionGraph"></div>