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
		<fieldset>
			<p class="center bold"><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
			<p>Aucun match n'a été trouvé.</p>
			<p class="submit"><input type="button" value="fermer" class="nyroModalClose" /></p>
		</fieldset>
<?php
		exit;
	}
?>
<p id="popup_message" style="margin: 0; padding: 0;"></p>
<form action="/ajax/save_scores.php" method="get" id="ajaxForm">
	<fieldset>
		<legend>Saisie des scores</legend>
		<p class="center bold"><?php echo $league->name ?> - <?php echo $season->label ?>, <?php echo $day->number ?><sup>e</sup> journée</p>
		<table class="noborder scoreTable" style="width: 100%">
			<?php foreach ($matches as $match): ?>
				<tr>
					<td class="right team"><?php echo $teams[$match->pr_home_team_id]->name ?></td>
					<td class="center"><input name="home_goals[<?php echo $match->id ?>]" size="1" maxlength="1" value="<?php echo $match->home_goals ?>" style="text-align:center" <?=$editableStr?>/></td>
					<td class="center"> - </td>
					<td class="center"><input name="away_goals[<?php echo $match->id ?>]" size="1" maxlength="1" value="<?php echo $match->away_goals ?>" style="text-align:center" <?=$editableStr?>/></td>
					<td class="team"><?php echo $teams[$match->pr_away_team_id]->name ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<p class="submit">
			<?php if ($isEditable) { ?>
				<input type="hidden" name="id" value="<?php echo GETorPOST('id') ?>" />
				<input type="submit" value="enregistrer" />
			<?php } ?>
			<input type="button" value="annuler" class="nyroModalClose" />
		</p>
	</fieldset>
</form>

<?php if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_scores.php',
			dataType: 'json',
			method: 'post',
			success: function (response) {
				if (response.success == 1)
					window.location.reload();
				else
					$('#popup_message').html(response.message);
				resizeModal();
			}
		});
	</script>

<?php } ?>