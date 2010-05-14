<?php
/**
 * Description of _Season
 *
 * @author arteau
 */

$GLOBALS["classes"]["Season"] = array("classname" => "_Season", "tablename" => "pr_season");

class _Season extends ArtObject
{
	protected $_data = array('id' => null, 'pr_league_id' => null, 'label' => null, 'teams' => null);

	protected $_editedFields = array();

	/**
	 * @return Season
	 */
	public static function find($id)
	{
		return parent::find("Season", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("Season", array(), $order);
	}

	/**
	 * @return Season
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("Season", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("Season", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("Season");
	}

	/**
	 * @return int
	 */
	public static function count($criteria = array())
	{
		return parent::count("Season", $criteria, "", 1, true);
	}

	public function delete()
	{
		parent::delete("Season");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("Season", $field, $value, $id);
	}
}
