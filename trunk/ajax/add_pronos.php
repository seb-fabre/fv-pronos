<?php
	require_once('../includes/init.php');

	$day = Day::find($_GET['id']);

	$user = User::find($_GET['user']);

	$season = $day->getSeason();

	$league = $season->getLeague();

	$teams = $season->getTeams();

	$matches = $day->getMatches();

	$pronos = Prono::findByDayUser($day->id, $user->id);
	$tmp = array();
	foreach ($pronos as $prono)
		$tmp[$prono->pr_match_id] = $prono;
	$pronos = $tmp;
?>
<form action="/ajax/save_pronos.php" method="get">
	<fieldset>
		<legend>Saisie des pronos</legend>
		<p class="center bold"><?php echo $league->name ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<?php foreach ($matches as $match): ?>
			<p class="clear">
				<span class="scoreLeft"><?php echo $teams[$match->pr_home_team_id]->name ?> <input name="home_goals[<?php echo $match->id ?>]" value="<?php echo (array_key_exists($match->id, $pronos) ? $pronos[$match->id]->home_goals : '') ?>" size="2" maxlength="1" /></span>
				<span class="scoreCenter"> - </span>
				<span class="scoreRight"><input name="away_goals[<?php echo $match->id ?>]" size="2" maxlength="1" value="<?php echo (array_key_exists($match->id, $pronos) ? $pronos[$match->id]->away_goals : '') ?>" /> <?php echo $teams[$match->pr_away_team_id]->name ?></span>
		<?php endforeach; ?>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
			<input type="hidden" name="user" value="<?php echo $_GET['user'] ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>