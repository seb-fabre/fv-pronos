<?php
	require_once('includes/init.php');

	$leagues = League::getAll();

	$seasons = Season::getAll();
	$tmp = array();
	foreach ($seasons as $season)
		$tmp[$season->pr_league_id] []= $season;
	$seasons = $tmp;
	unset($tmp);

	echoHTMLHead('Liste des championnats');
?>

<body>
	<div class="container">
		<?php echoMenu(); ?>
		<h1>Liste des championnats</h1>
		
		<?php if (!empty($_SESSION['user'])) { ?>
			<button type="button" class="btn btn-primary nyroModal" href="<?=APPLICATION_URL?>ajax/add_league.php" rev="modal">Ajouter un championnat</button>
		<?php } ?>
			
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Nom</th>
					<th>Saison</th>
					<?php if (!empty($_SESSION['user'])) { ?>
						<th>Modifier</th>
					<?php } ?>
					<th>Equipes</th>
					<th>Classement</th>
					<th>Pronostics</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($leagues) != 0) { ?>
					<?php foreach ($leagues as $league) { ?>
						<?php if (empty($seasons[$league->id])) continue; ?>
						<?php foreach ($seasons[$league->id] as $i => $season) { ?>
							<tr>
								<?php if ($i==0): ?>
									<td rowspan="<?php echo count($seasons[$league->id]) ?>"><?php echo $league->name ?></td>
								<?php endif; ?>
								<td><?php echo $season->label ?></td>
								<?php if (!empty($_SESSION['user'])) { ?>
									<td class="center">
										<a href="<?=APPLICATION_URL?>ajax/add_league.php?id=<?php echo $season->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/edit.png" alt="[edit]" /></a>
									</td>
								<?php } ?>
								<td>
									<a href="<?=APPLICATION_URL?>ajax/show_teams.php?id=<?php echo $season->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/link-external.png" alt="[edit]" /> voir/modifier les équipes</a>
								</td>
								<td>
									<a href="<?=APPLICATION_URL?>ajax/show_rankings.php?id=<?php echo $season->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/link-external.png" alt="[view]" /> classement des équipes</a>
								</td>
								<td>
									<p><a href="<?=APPLICATION_URL?>scores/season-<?php echo $season->id ?>"><img src="<?=APPLICATION_URL?>images/link-external.png" alt="[view]" /> classement détaillé</a></p>
									<p><a href="javascript:;" onclick="previewRankings(<?php echo $season->id ?>, -1);"><img src="<?=APPLICATION_URL?>images/link-external.png" alt="[view]" /> aperçu du classement</a></p>
									<p><a href="<?=APPLICATION_URL?>evolution_classement/season-<?php echo $season->id ?>"><img src="<?=APPLICATION_URL?>images/link-external.png" alt="[view]" /> évolution du classement</a></p>
									<p><a href="<?=APPLICATION_URL?>ajax/view_stats.php?id=<?php echo $season->id ?>" class="nyroModal"><img src="<?=APPLICATION_URL?>images/link-external.png" alt="[view]" /> statistiques</a></p>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>
				<?php } else { ?>
					<tr><td colspan="5">Aucun résultat trouvé</td></tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<div id="popupImage" style="display:none"></div></div>
	<div id="popup_large"><div id="popup_content"></div></div>

	<script type="text/javascript">
		function saveLeague()
		{
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/save_league.php',
				data: $('#ajaxForm').serialize(),
				dataType: 'json',
				success: function (response) {
					if (response.success == 1)
						window.location.reload();
					else
						$('#popup_message').html(response.message);
					resizeModal();
				}
			});
		}

		function saveSeasonTeams()
		{
			$.ajax({
				url: '<?=APPLICATION_URL?>ajax/save_season_teams.php',
				data: $('#ajaxForm').serialize(),
				dataType: 'json',
				type: 'post',
				success: function (response) {
					if (response.success == 1)
						window.location.reload();
					else
						$('#popup_message').html(response.message);
					resizeModal();
				}
			});
		}

		function finishPreviewRankings()
		{
			if (!$('#classement')[0].complete || !$('.spinner')[0].complete)
			{
				setTimeout('finishPreviewRankings()', 500);
			}
			else
			{
				$.fn.nyroModalManual({content: $('#popupImage').html()});
			}
		}

		function previewRankings(id)
		{
			$('#popupImage').html('<img src="<?=APPLICATION_URL?>classement/season-' + id + '.png" id="classement" />');

			$.fn.nyroModalManual({content: '<?=SPINNER_TAG?>', endShowContent: finishPreviewRankings});
		}

		function addUser()
		{
			var selected = $('#user_select option:selected');

			// check if user was already added
			var oldLi = $('#li_box' + selected.val());

			if (oldLi.length != 0)
				return;

			var li = $('<li class="li_box" id="li_box' + selected.val() + '"><span>' + selected.text() + '</span> <input type="hidden" name="users[]" value="' + selected.val() + '" /><a href="javascript:;" onclick="closeBox(' + selected.val() + ');" class="closeBox"></a></li>');

			$('#users_holder').append(li);
			li.corner();
			$('#simplemodal-container').css('height', 'auto');
		}

		function closeBox(id)
		{
			$('#li_box' + id).remove();
		}

		function finishReloadGraph()
		{
			if ($('#evolutionImg').length == 0 || !$('#evolutionImg')[0].complete)
			{
				setTimeout('finishReloadGraph()', 500);
			}
			else
			{
				$('#evolutionGraph .spinner').remove();
				resizeModal($('#evolutionImg').width() + 20, null);
			}
		}

		function reloadGraph(seasonId)
		{
			var ids = '';
			var inputs = $('#users_holder input[type=hidden]');
			for (var i=0; i<inputs.length; i++)
				ids += $(inputs[i]).val() + '|';

			$('#evolutionGraph').html('<?=SPINNER_TAG?><img src="<?=APPLICATION_URL?>evolution/season-' + seasonId + '-users-' + ids + '.png" id="evolutionImg" />');

			setTimeout('finishReloadGraph()', 500);
		}

		$(document).ready(function(){
			
		});
	</script>

	<?php echoHTMLFooter();?>
</body>
</html>