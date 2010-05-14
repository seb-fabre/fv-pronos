<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id', -1);

	$day = Day::find($id);

	$season = $day->getSeason();

	$league = $season->getLeague();
?>
<p id="popup_message" style="margin: 0; padding: 0;"></p>
<form method="post" id="ajaxForm" class="nyroModal" action="<?=APPLICATION_URL?>ajax/save_prono.php">
	<fieldset>
		<legend>Parser les pronos</legend>
		<p class="center bold"><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<p class="center"><textarea id="matches" name="matches"></textarea>
		<p class="submit">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="submit" value="parser" />
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
</form>