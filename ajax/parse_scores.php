<?php
	require_once('../includes/init.php');

	$day = Day::find(GETorPOST('id'));

	$season = $day->getSeason();

	$league = $season->getLeague();

	$teams = $season->getTeams();
?>
<form action="/ajax/save_score.php" method="get">
	<fieldset>
		<legend>Parser les matches</legend>
		<p class="center bold"><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<p class="center"><textarea cols="20" rows="6" id="matches" name="matches"></textarea>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
			<input type="submit" value="parser" id="parser" />
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
</form>