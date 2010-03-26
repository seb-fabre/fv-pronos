<?php
$GLOBALS["classes"]["Team"] = array("classname" => "_Team", "tablename" => "pr_team");

class _Team extends ArtObject
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
	 * @return Team
	 */
	public static function find($id)
	{
		return parent::find("Team", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("Team", array(), $order);
	}

	/**
	 * @return Team
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("Team", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("Team", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("Team");
	}

	/**
	 * @return int
	 */
	public static function count()
	{
		return parent::count("Team", array(), "", 1, true);
	}

	public function delete()
	{
		return parent::delete("Team");
	}
}
