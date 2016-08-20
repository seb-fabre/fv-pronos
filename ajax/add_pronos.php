<?php
	require_once('../includes/init.php');

	$day = Day::find(GETorPOST('id'));

	$user = User::find(GETorPOST('user'));

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
<form method="get" id="ajaxForm" class="form-horizontal">
		<h4 class="well">
			Saisie des pronos
			<br/>
			<small><?php echo $league->name ?>, <?php echo $day->number ?><sup>e</sup> journée</small>
		</h4>
	
	<div class="panel-body">

		<?php foreach ($matches as $match) { ?>
			<div class="row form-group-sm">
				<div class="col-sm-3"><?php echo $teams[$match->pr_home_team_id]->name ?></div>
				<div class="col-sm-2"><input name="home_goals[<?php echo $match->id ?>]" value="<?php echo (array_key_exists($match->id, $pronos) ? $pronos[$match->id]->home_goals : '') ?>" size="2" maxlength="1" class="form-control" /></div>
				<div class="col-sm-1"> - </div>
				<div class="col-sm-2"><input name="away_goals[<?php echo $match->id ?>]" size="2" maxlength="1" value="<?php echo (array_key_exists($match->id, $pronos) ? $pronos[$match->id]->away_goals : '') ?>" class="form-control" /></div>
				<div class="col-sm-3"><?php echo $teams[$match->pr_away_team_id]->name ?></div>
				<div class="col-sm-1"> </div>
			</div>
		<?php } ?>
		
		<br/>
		
		<div class="form-group">
			<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
			<input type="hidden" name="user" value="<?php echo GETorPOST('user') ?>" />
			<button type="submit" class="btn btn-default btn-sm">Enregistrer</button>
			<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
		</div>
	</div>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_pronos.php',
			dataType: 'json',
			method: 'post',
			success: function (response) {
				if (response.success == 1)
				{
					window.location.reload();
					return;
				}
				
				showError(response.message);
				resizeModal();
			}
		});
	</script>

<?php } ?>