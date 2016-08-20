<?php
/**
 * Description of _SeasonTeams
 *
 * @author arteau
 */

$GLOBALS["classes"]["SeasonTeams"] = array("classname" => "_SeasonTeams", "tablename" => "pr_season_teams");

class _SeasonTeams extends ArtObject
{
	protected $_data = array('id' => null, 'pr_team_id' => null, 'pr_season_id' => null);

	protected $_editedFields = array();

	/**
	 * @return SeasonTeams
	 */
	public static function find($id)
	{
		return parent::find("SeasonTeams", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("SeasonTeams", array(), $order);
	}

	/**
	 * @return SeasonTeams
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("SeasonTeams", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("SeasonTeams", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("SeasonTeams");
	}

	/**
	 * @return int
	 */
	public static function count($criteria = array())
	{
		return parent::count("SeasonTeams", $criteria, "", 1, true);
	}

	public function delete()
	{
		parent::delete("SeasonTeams");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("SeasonTeams", $field, $value, $id);
	}
}
