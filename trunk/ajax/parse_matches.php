<?php
	require_once('../includes/init.php');

	$day = Day::find(GETorPOST('pr_day_id'));

	$season = Season::find($day->pr_season_id);

	$league = $season->getLeague();
?>
<form action="/ajax/save_matches.php" method="get">
	<fieldset>
		<legend>Parser les matches</legend>
		<p class="center bold"><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<p class="center"><textarea cols="20" rows="6" id="matches" name="matches"></textarea>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
			<input type="hidden" name="pr_day_id" value="<?php echo GETorPOST('pr_day_id') ?>" />
			<input type="submit" value="parser" id="parser" />
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
</form>