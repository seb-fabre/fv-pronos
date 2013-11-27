<?php
	require_once('../includes/init.php');

	$day = Day::find(GETorPOST('pr_day_id'));

	$season = Season::find($day->pr_season_id);

	$league = $season->getLeague();
?>
<p id="popup_message" style="margin: 0; padding: 0;"></p>
<form action="<?=APPLICATION_URL?>ajax/add_match.php" method="post" id="ajaxForm" class="nyroModal">
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

<?php /* if (!empty($_SESSION['user'])) { ?>

	<script type="text/javascript">
		$('#ajaxForm').ajaxForm({
			url: '<?=APPLICATION_URL?>ajax/save_matches.php',
			dataType: 'json',
			method: 'post',
			success: function (response) {alert(0);
				if (response.success == 1)
					window.location.reload();
				else
					$('#popup_message').html(response.message);
				resizeModal();
			}
		});
	</script>

<?php }*/ ?>