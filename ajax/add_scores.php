<?php
	session_start();
	require_once('../mysql_connexion.php');
	require_once('../includes.php');

	$day = Day::find($_GET['id']);

	$season = $day->getSeason();

	$league = $season->getLeague();

	$teams = $season->getTeams();

	$matches = $day->getMatches();
?>
<form action="/ajax/save_scores.php" method="get">
	<fieldset>
		<legend>Saisie des scores</legend>
		<p class="center bold"><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<?php foreach ($matches as $match): ?>
			<p class="clear">
				<span class="scoreLeft"><?php echo $teams[$match->pr_home_team_id]->name ?> <input name="home_goals[<?php echo $match->id ?>]" value="<?php echo $match->home_goals ?>" size="2" maxlength="1" /></span>
				<span class="scoreCenter"> - </span>
				<span class="scoreRight"><input name="away_goals[<?php echo $match->id ?>]" size="2" maxlength="1" value="<?php echo $match->away_goals ?>" /> <?php echo $teams[$match->pr_away_team_id]->name ?></span>
		<?php endforeach; ?>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />
			<input type="submit" value="enregistrer" />
			<input type="button" value="annuler" onclick="$.modal.close()" />
		</p>
	</fieldset>
</form>