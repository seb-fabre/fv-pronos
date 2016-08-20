<?php
	require_once('includes/init.php');

	// TODO : au clic sur une checkbox, faire un appel ajax pour récupérer les codes couleurs des joueurs
	// TODO : faire un timeout sur le clic, pour éviter de charger 10x l'image si on clique sur 10 joueurs pour afficher le graphe

	$pathinfo = $_SERVER['PATH_INFO'];

  $matches = false;
  $match = preg_match('@^/season-([0-9]+)$@', $pathinfo, $matches);

	if (!isset($matches[1]))
		header('location: ' . APPLICATION_URL);

	$season = $matches[1];

	$season = Season::find($season);
	$league = $season->getLeague();

	if (GETorPOST('sort'))
		$sort = GETorPOST('sort');
	else
		$sort = 'total';

	$days = $season->getDays();

	$teams = Team::getAll();

	$users = $season->getUsers('name ASC');

	$pronos = $season->getPronos();

	$matches = $season->getMatchs();

	echoHTMLHead('Evolution du classement');
?>
<body>
	<style>
		#spinner { display: none; position: absolute; top: 50%; left: 50%; }
		#evolutionSpinner, #evolutionGraph { position: absolute; width: 600px; }
		td { vertical-align: top; }
	</style>

	<div class="container">
		<?php echoMenu(); ?>
		<h1>Evolution du classement</h1>

		<div class="row">
			<div class="col-md-3 evolutionCheckboxes">
				<div class="col-md-6">
					<div class="checkbox">
						<?php
							$i=0;
							$count = count($users);
							foreach ($users as $aUser)
							{
								echo '<label id="spanUser' . $aUser->id . '" for="checkboxUser' . $aUser->id . '">';
								echo '	<input type="checkbox" id="checkboxUser' . $aUser->id . '" value="' . $aUser->id . '" onclick="reloadImage();" /> ';
								echo		$aUser->name;
								echo '</label>';
								echo '<br />';

								if (($count%2 == 0 && $i == $count / 2) || ($count%2 == 1 && $i == floor($count / 2)))
									echo '</div></div><div class="col-sm-6"><div class="checkbox">';

								$i++;
							}
						?>
					</div>
				</div>
			</div>
			<div class="col-md-9 text-center">
				<div id="evolutionSpinner"><img src="<?=SPINNER_URL?>" id="spinner" /></div>
				<div id="evolutionGraph"></div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var baseUrl = "<?=APPLICATION_URL ?>evolution/season-<?=$season->id ?>-users-";

		$('#evolutionSpinner').height($('#evolutionSpinner').parent().height());

		function reloadImage()
		{
			$('#spinner').show();
			
			var checkboxes = $('.evolutionCheckboxes input:checked');
			var glu = '';
			var users = '';

			checkboxes.each(function(i) {
				users += glu + $(checkboxes.get(i)).val();
				glu = '|';
			});

			if ($('#evolutionGraph #graph'))
				$('#evolutionGraph #graph').remove();
			
			var img = $('<img src="" id="graph" />');
			img.attr('src', baseUrl + users + '.png');
			$('#evolutionGraph').append(img);

			img.load(function(){
				$('#spinner').hide();
			});
		}
	</script>
</body>
</html>