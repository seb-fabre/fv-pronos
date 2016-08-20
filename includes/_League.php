<?php
/**
 * Description of _League
 *
 * @author arteau
 */

$GLOBALS["classes"]["League"] = array("classname" => "_League", "tablename" => "pr_league");

class _League extends ArtObject
{
	protected $_data = array('id' => null, 'name' => null, 'pr_team_category_id' => null, 'b_in_progress' => null);

	protected $_editedFields = array();

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
	public static function count($criteria = array())
	{
		return parent::count("League", $criteria, "", 1, true);
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
