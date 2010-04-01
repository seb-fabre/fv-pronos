<?php
/**
 * Description of _MigrationVersion
 *
 * @author arteau
 */

$GLOBALS["classes"]["MigrationVersion"] = array("classname" => "_MigrationVersion", "tablename" => "pr_migration_version");

class _MigrationVersion extends ArtObject
{
	protected $_data = array('version' => null);

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
	 * @return MigrationVersion
	 */
	public static function find($id)
	{
		return parent::find("MigrationVersion", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("MigrationVersion", array(), $order);
	}

	/**
	 * @return MigrationVersion
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("MigrationVersion", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("MigrationVersion", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("MigrationVersion");
	}

	/**
	 * @return int
	 */
	public static function count()
	{
		return parent::count("MigrationVersion", array(), "", 1, true);
	}

	public function delete()
	{
		parent::delete("MigrationVersion");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("MigrationVersion", $field, $value, $id);
	}
}
