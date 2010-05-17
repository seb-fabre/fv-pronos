<?php
if (!empty($GLOBALS["ROOTPATH"]))
	$relativePath = $GLOBALS["ROOTPATH"] . "includes/";
else
	$relativePath = "./";
require_once($relativePath . "ArtObject.php");
require_once($relativePath . "_Day.php");
require_once($relativePath . "Day.php");
require_once($relativePath . "_League.php");
require_once($relativePath . "League.php");
require_once($relativePath . "_Match.php");
require_once($relativePath . "Match.php");
require_once($relativePath . "_MigrationVersion.php");
require_once($relativePath . "MigrationVersion.php");
require_once($relativePath . "_Prono.php");
require_once($relativePath . "Prono.php");
require_once($relativePath . "_Season.php");
require_once($relativePath . "Season.php");
require_once($relativePath . "_SeasonTeams.php");
require_once($relativePath . "SeasonTeams.php");
require_once($relativePath . "_Team.php");
require_once($relativePath . "Team.php");
require_once($relativePath . "_TeamCategory.php");
require_once($relativePath . "TeamCategory.php");
require_once($relativePath . "_User.php");
require_once($relativePath . "User.php");
