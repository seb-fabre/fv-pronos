<?php
	require_once('../includes/init.php');

	$day = Day::find(GETorPOST('id'));

	$season = $day->getSeason();

	$league = $season->getLeague();

	$teams = $season->getTeams();

	$matches = $day->getMatches();

	$isEditable = !empty($_SESSION['user']);
	if ($isEditable)
		$editableStr = '';
	else
		$editableStr = ' disabled="disabled"';

	if (count($matches) == 0)
	{
?>
		<div class="panel-body">
			<h4 class="well">
				Saisie des scores
				<br/>
				<small><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</small>
			</h4>
			<div class="alert alert-warning">Aucun match n'a été trouvé.</div>
			<button type="button" class="btn btn-default btn-sm nyroModalClose">Fermer</button>
		</div>
<?php
		exit;
	}
?>
<form action="<?=APPLICATION_URL?>ajax/save_scores.php" method="get" id="ajaxForm" class="form-horizontal">
	
	<h4 class="well">
		Saisie des scores
		<br/>
		<small><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</small>
	</h4>
	
	<div class="panel-body">

		<table class="table" style="width: 100%">
			<tr>
				<?php foreach (array_values($matches) as $i => $match) { ?>
					<td class="right team"><?php echo $teams[$match->pr_home_team_id]->name ?></td>
					<td class="center"><input name="home_goals[<?php echo $match->id ?>]" size="1" maxlength="1" value="<?php echo $match->home_goals ?>" style="text-align:center" <?=$editableStr?>/></td>
					<td class="center"> - </td>
					<td class="center"><input name="away_goals[<?php echo $match->id ?>]" size="1" maxlength="1" value="<?php echo $match->away_goals ?>" style="text-align:center" <?=$editableStr?>/></td>
					<td class="team"><?php echo $teams[$match->pr_away_team_id]->name ?></td>

					<?php if ($i%2 == 0) { ?>
						<td>&nbsp;&nbsp;&nbsp;</td>
					<?php } else { ?>
						</tr><tr>
					<?php } ?>
				<?php } ?>
			</tr>
		</table>

		<div class="form-group">
			<?php if ($isEditable && isset($_SESSION['user'])) { ?>
				<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
				<button type="submit" class="btn btn-default btn-sm">Enregistrer</button>
			<?php } ?>
			<button type="button" class="btn btn-default btn-sm nyroModalClose">Annuler</button>
		</div>

	</div>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_scores.php',
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