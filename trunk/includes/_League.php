<?php
/**
 * Description of _League
 *
 * @author arteau
 */

$GLOBALS["classes"]["League"] = array("classname" => "_League", "tablename" => "pr_league");

class _League extends ArtObject
{
	protected $_data = array('id' => null, 'name' => null);

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
	 * @return League
	 */
	public static function find($id)
	{
		return parent::find("League", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("League", array(), $order);
	}

	/**
	 * @return League
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("League", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("League", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("League");
	}

	/**
	 * @return int
	 */
	public static function count()
	{
		return parent::count("League", array(), "", 1, true);
	}

	public function delete()
	{
		parent::delete("League");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("League", $field, $value, $id);
	}
}
