<?php

	require_once('init.php');

	$conn = mysql_connect($mysqlHost, $mysqlLogin, $mysqlPassword);
	$DB = $mysqlDatabase;

	set_time_limit(0);

	$prefixLength = 3;

	mysql_select_db('information_schema', $conn) or die (mysql_error());

	$queryFK = mysql_query("SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, TABLE_CONSTRAINTS.TABLE_NAME
  	                      FROM TABLE_CONSTRAINTS, KEY_COLUMN_USAGE
  	                      WHERE TABLE_CONSTRAINTS.CONSTRAINT_NAME=KEY_COLUMN_USAGE.CONSTRAINT_NAME
  	                      AND constraint_type='FOREIGN KEY'
  	                      AND TABLE_CONSTRAINTS.TABLE_SCHEMA='" . $DB . "'") or die (mysql_error());

	$foreignKeys = array();
	$invertForeignKeys = array();
	while ($res = mysql_fetch_array($queryFK))
	{
		$foreignKeys[$res['TABLE_NAME']][$res['COLUMN_NAME']] = array('table' => $res['REFERENCED_TABLE_NAME'], 'column' => $res['REFERENCED_COLUMN_NAME']);
		$invertForeignKeys[$res['REFERENCED_TABLE_NAME']] []= array($res['REFERENCED_COLUMN_NAME'] => array('table' => $res['TABLE_NAME'], 'column' => $res['COLUMN_NAME']));
	}

	mysql_select_db($DB, $conn);

	$_includes = fopen('__classes.php', 'w+');
	fwrite($_includes, '<?php
if (!empty($GLOBALS["ROOTPATH"]))
	$relativePath = $GLOBALS["ROOTPATH"];
else
	$relativePath = "./";
');
	fwrite($_includes, 'require_once($relativePath . "ArtObject.php");' . "\n");

	$req = mysql_query('SHOW TABLES;', $conn);

	while ($res = mysql_fetch_array($req))
	{
		$capitalized = str_replace(' ', '', ucwords(str_replace('_', ' ', substr($res[0], $prefixLength))));

		$file = fopen('_' . $capitalized . '.php', 'w+');

		fwrite($file, '<?php
$GLOBALS["classes"]["' . $capitalized . '"] = array("classname" => "_' . $capitalized . '", "tablename" => "' . $res[0] . '");

class _' . $capitalized . ' extends ArtObject
{
');
		$fields = array();

		$q = mysql_query('SHOW COLUMNS FROM ' . $res[0] . ';', $conn);
		while ($r = mysql_fetch_array($q))
		{
			$fields []= $r[0];
			if (substr($r[0], 0, $prefixLength) != 'pr_')
				$n = str_replace(' ', '', ucwords(str_replace('_', ' ', $r[0])));
			else
				$n = str_replace(' ', '', ucwords(str_replace('_', ' ', substr($r[0], $prefixLength))));
		}

		fwrite($file, '	protected $_data = array(\'' . implode("' => null, '", $fields) . '\' => null);

	protected $_editedFields = array();

	public function __construct($values = array())
	{
		if (!empty($values))
		foreach ($values as $key => $value)
		{
			if (array_key_exists($key, $this->_data))
				$this->_data[$key] = $value;
		}
	}

	/**
	 * @return ' . $capitalized . '
	 */
	public static function find($id)
	{
		return parent::find("' . $capitalized . '", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order=\'\')
	{
		return parent::search("' . $capitalized . '", array(), $order);
	}

	/**
	 * @return ' . $capitalized . '
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("' . $capitalized . '", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("' . $capitalized . '", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("' . $capitalized . '");
	}

	/**
	 * @return int
	 */
	public static function count()
	{
		return parent::count("' . $capitalized . '", array(), "", 1, true);
	}

	public function delete()
	{
		return parent::delete("' . $capitalized . '");
	}
');
		if (isset($foreignKeys[$res[0]]))
		foreach ($foreignKeys[$res[0]] as $foreignKey => $infos)
		{
			$cap = str_replace(' ', '', ucwords(str_replace('_', ' ', substr($infos['table'], $prefixLength))));
			$capName = str_replace(' ', '', ucwords(str_replace('_', ' ', substr($foreignKey, $prefixLength, -3))));
			fwrite($file, '
	/**
	 * @return ' . $cap . '
	 */
	public function get' . $capName . '()
	{
		return ' . $cap . '::find($this->' . $foreignKey . ');
	}
');
		}
		if (isset($invertForeignKeys[$res[0]]))
		foreach ($invertForeignKeys[$res[0]] as $foreignKeys)
		foreach ($foreignKeys as $foreignKey => $infos)
		{
			$cap = str_replace(' ', '', ucwords(str_replace('_', ' ', substr($infos['table'], $prefixLength))));
			$capName = str_replace(' ', '', ucwords(str_replace('_', ' ', substr($foreignKey, $prefixLength, -3))));
			fwrite($file, '
	/**
	 * @return array
	 */
	public function get' . $cap . 's()
	{
		return ' . $cap . '::search(array(array("' . $infos['column'] . '", $this->' . $foreignKey . ')));
	}
');
		}

		fwrite($file, '}
');
		fclose($file);

//		echo '<code><pre>' . htmlentities(file_get_contents($capitalized . '.php')) . '</pre></code>';

		fwrite($_includes, 'require_once($relativePath . "_' . $capitalized . '.php");' . "\n");

		if (!file_exists($capitalized . '.php'))
		{
			$file = fopen($capitalized . '.php', 'w+');

			fwrite($file, '<?php
$GLOBALS["classes"]["' . $capitalized . '"] = array("classname" => "' . $capitalized . '", "tablename" => "' . $res[0] . '");
	
	class ' . $capitalized . ' extends _' . $capitalized . '
	{
	');

			fwrite($file, '}
	');

			fclose($file);

//			echo '<code><pre>' . htmlentities(file_get_contents($capitalized . '.php')) . '</pre></code>';
		}

		fwrite($_includes, 'require_once($relativePath . "' . $capitalized . '.php");' . "\n");
	}
	fclose($_includes);
