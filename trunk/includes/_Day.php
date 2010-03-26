<?php
$GLOBALS["classes"]["Day"] = array("classname" => "_Day", "tablename" => "pr_day");

class _Day extends ArtObject
{
	protected $_data = array('id' => null, 'pr_season_id' => null, 'number' => null);

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
	 * @return Day
	 */
	public static function find($id)
	{
		return parent::find("Day", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("Day", array(), $order);
	}

	/**
	 * @return Day
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("Day", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("Day", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("Day");
	}

	/**
	 * @return int
	 */
	public static function count()
	{
		return parent::count("Day", array(), "", 1, true);
	}

	public function delete()
	{
		return parent::delete("Day");
	}
}
