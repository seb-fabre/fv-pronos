<?php
/**
 * Description of Migration
 *
 * @author arteau
 */
class Migration {

	// array of migrations
	static $migrations = null;

	public static function initMigrations()
	{
		self::$migrations = array(
			'',	// not meant to be used, so leave empty
			'ALTER TABLE  `pr_user` ADD  `role` VARCHAR( 10 ) NOT NULL DEFAULT "user";',
		);
	}

	public static function migrate($fromVersion)
	{
		self::initMigrations();

		if ($fromVersion == count(self::$migrations))
			return;

		self::startMaintenance();

		for ($i=$fromVersion; $i<count(self::$migrations); $i++)
		{
			mysql_query(self::$migrations[$i]);
		}

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
