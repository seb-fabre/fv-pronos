<?php
/**
 * Description of Migration
 *
 * @author arteau
 * @final
 */
class Migration {

	/**
	 * array of migrations
	 * available indexes :
	 * - sql : the sql query to execute
	 * - generate_classes : true to regenerate the php classes (write true if fields have changed in a table)
	 */
	static $migrations = null;

	public static function initMigrations()
	{
		self::$migrations = array(
			1 => array('sql' => 'ALTER TABLE `pr_team` ADD `has_logo` TINYINT NOT NULL DEFAULT "0";', 'generate_classes' => true),
			2 => array('sql' => 'ALTER TABLE `pr_season` ADD `teams` TINYINT UNSIGNED;', 'generate_classes' => true),
			3 => array('sql' => 'UPDATE pr_season SET pr_season.teams = (SELECT pr_league.teams FROM pr_league WHERE pr_league.id=pr_season.pr_league_id LIMIT 1)'),
			4 => array('sql' => 'ALTER TABLE `pr_league` DROP teams;', 'generate_classes' => true),
			5 => array('sql' => 'ALTER TABLE `pr_day` ADD limit_date DATETIME;', 'generate_classes' => true),
			6 => array('sql' => 'ALTER TABLE `pr_match` ADD limit_date DATETIME;', 'generate_classes' => true),
		);
	}

	public static function migrate($fromVersion)
	{
		self::initMigrations();

		if ($fromVersion == count(self::$migrations))
			return;

		self::startMaintenance();

		$regenerateClasses = false;

		for ($i=$fromVersion + 1; $i<=count(self::$migrations); $i++)
		{
			$migration = self::$migrations[$i];

			mysql_query($migration['sql']) or die(mysql_error());

			if (!empty($migration['generate_classes']))
				$regenerateClasses = true;
		}

		if ($regenerateClasses)
			require_once($GLOBALS['ROOTPATH'] . 'includes/generate_classes.php');

		self::endMaintenance();
	}

	public static function startMaintenance()
	{
		$f = fopen($GLOBALS['ROOTPATH'] . 'maintenance.txt', 'w');
		fwrite($f, 'maintenance in progress...');
		fclose($f);
	}

	public static function endMaintenance()
	{
		mysql_query('UPDATE pr_migration_version SET version=' . count(self::$migrations));

		unlink($GLOBALS['ROOTPATH'] . 'maintenance.txt');
	}
}
