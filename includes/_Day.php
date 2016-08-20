<?php
/**
 * Description of _Day
 *
 * @author arteau
 */

$GLOBALS["classes"]["Day"] = array("classname" => "_Day", "tablename" => "pr_day");

class _Day extends ArtObject
{
	protected $_data = array('id' => null, 'pr_season_id' => null, 'number' => null, 'label' => null, 'limit_date' => null, 'count_matches' => null);

	protected $_editedFields = array();

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
	public static function count($criteria = array())
	{
		return parent::count("Day", $criteria, "", 1, true);
	}

	public function delete()
	{
		parent::delete("Day");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("Day", $field, $value, $id);
	}
}
