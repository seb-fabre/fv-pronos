<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id');

	$season = Season::find($id);
	$league = $season->getLeague();

	$users = User::getAll('name ASC');

	echo '<div style="margin: 0 1.5em 1.5em">';
	echo 'Joueurs : ';
	echo Tools::objectsToSelect($users, 'name', array('id' => 'user_select'));
	echo '&nbsp;&nbsp;<input type="button" value="ajouter le joueur" onclick="addUser()" />';
	echo '&nbsp;&nbsp;<input type="button" value="recharger le graphe" onclick="reloadGraph()" />';
	echo '</div>';
?>

<ul class="input_holder" id="users_holder">
	<li class="li_box">toto <a href="closeBox();" class="closeBox"></a></li>
	<li class="li_box">titi <a href="closeBox();" class="closeBox"></a></li>
	<li class="li_box">quequette <a href="closeBox();" class="closeBox"></a></li>
	<li class="li_box">poil <a href="closeBox();" class="closeBox"></a></li>
	<li class="li_box">radis <a href="closeBox();" class="closeBox"></a></li>
	<li class="li_box">casquette <a href="closeBox();" class="closeBox"></a></li>
	<li class="li_box">courgette <a href="closeBox();" class="closeBox"></a></li>
	<li class="li_box">aspirine <a href="closeBox();" class="closeBox"></a></li>
</ul>
<div style="clear: both; width: 100%;">&nbsp;</div>