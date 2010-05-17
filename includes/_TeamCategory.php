<?php
/**
 * Description of _TeamCategory
 *
 * @author arteau
 */

$GLOBALS["classes"]["TeamCategory"] = array("classname" => "_TeamCategory", "tablename" => "pr_team_category");

class _TeamCategory extends ArtObject
{
	protected $_data = array('id' => null, 'name' => null);

	protected $_editedFields = array();

	/**
	 * @return TeamCategory
	 */
	public static function find($id)
	{
		return parent::find("TeamCategory", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("TeamCategory", array(), $order);
	}

	/**
	 * @return TeamCategory
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("TeamCategory", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("TeamCategory", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("TeamCategory");
	}

	/**
	 * @return int
	 */
	public static function count($criteria = array())
	{
		return parent::count("TeamCategory", $criteria, "", 1, true);
	}

	public function delete()
	{
		parent::delete("TeamCategory");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("TeamCategory", $field, $value, $id);
	}
}
