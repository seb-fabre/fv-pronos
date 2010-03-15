<?php
	session_start();
	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	$day = Day::find($_GET['id']);

	$season = $day->getSeason();

	$league = $season->getLeague();
?>
<form method="post">
	<fieldset>
		<legend>Parser les pronos</legend>
		<p class="center bold"><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<p class="center"><textarea cols="20" rows="6" id="matches" name="matches"></textarea>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
			<input type="submit" value="parser" id="parser" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>