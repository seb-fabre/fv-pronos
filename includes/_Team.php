<?php
/**
 * Description of _Team
 *
 * @author arteau
 */

$GLOBALS["classes"]["Team"] = array("classname" => "_Team", "tablename" => "pr_team");

class _Team extends ArtObject
{
	protected $_data = array('id' => null, 'name' => null, 'has_logo' => null);

	protected $_editedFields = array();

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
	public static function count($criteria = array())
	{
		return parent::count("Team", $criteria, "", 1, true);
	}

	public function delete()
	{
		parent::delete("Team");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("Team", $field, $value, $id);
	}
}
