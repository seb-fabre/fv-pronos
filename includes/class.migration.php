<?php
/**
 * Description of Migration
 *
 * @author arteau
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
			0 => '',	// not meant to be used, so leave empty
			1 => array('sql' => 'ALTER TABLE  `pr_user` ADD  `role` VARCHAR(10) NOT NULL DEFAULT "user";'),
			2 => array('sql' => 'ALTER TABLE `pr_team` ADD `has_logo` TINYINT NOT NULL DEFAULT "0";', 'generate_classes' => true),
		);
	}

	public static function migrate($fromVersion)
	{
		self::initMigrations();

		if ($fromVersion == count(self::$migrations))
			return;

		self::startMaintenance();

		$regenerateClasses = false;

		for ($i=$fromVersion; $i<count(self::$migrations); $i++)
		{
			$migration = self::$migrations[$i];

			mysql_query($migration['sql']);

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
