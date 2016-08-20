<?php
	require_once('../includes/init.php');

	$id = GETorPOST('id', -1);

	$day = Day::find($id);

	$season = $day->getSeason();

	$league = $season->getLeague();
?>
<form method="post" id="ajaxForm" class="nyroModal" action="<?=APPLICATION_URL?>ajax/save_prono.php">
		<h4 class="well">
			Parser les pronos
			<br/>
			<small><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</small>
		</h4>
		
		<div class="form-group">
			<textarea id="matches" name="matches" class="form-control" rows="10"></textarea>
		</div>
		
		<div class="form-group">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<button type="subbmit" class="btn btn-default btn-sm">Parser</button>
			<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
		</div>
	</fieldset>
</form>