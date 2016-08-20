<?php
	require_once('../includes/init.php');

	$day = Day::find(GETorPOST('pr_day_id'));

	$season = Season::find($day->pr_season_id);

	$league = $season->getLeague();
	
	$isEditable = !$day->hasPronos() && !$day->hasCompletedMatches();
?>
<form action="<?=APPLICATION_URL?>ajax/add_match.php" method="post" id="ajaxForm" class="nyroModal form-horizontal">
			
	<h4 class="well">
		Parser les matches
		<br/>
		<?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée
	</h4>
		
	<div class="panel-body">

		<?php if ($isEditable && isset($_SESSION['user'])) { ?>
			<div class="form-group">
				<div class="col-sm-1"></div>
				<div class="col-sm-10">
					<textarea cols="20" rows="6" id="matches" name="matches" class="form-control"></textarea>
				</div>
			</div>

			<p class="submit">
				<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
				<input type="hidden" name="pr_day_id" value="<?php echo GETorPOST('pr_day_id') ?>" />
				<button type="submit" class="btn btn-default btn-sm">Enregistrer</button>
				<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
			</p>
		<?php } else { ?>
			<div class="alert alert-warning">Cette journée n'est pas éditable</div>
			<button type="button" class="btn btn-default btn-sm nyroModalClose">Fermer</button>
		<?php } ?>
	</div>
</form>
