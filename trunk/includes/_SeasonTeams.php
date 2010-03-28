<?php
$GLOBALS["classes"]["SeasonTeams"] = array("classname" => "_SeasonTeams", "tablename" => "pr_season_teams");

class _SeasonTeams extends ArtObject
{
	protected $_data = array('id' => null, 'pr_team_id' => null, 'pr_season_id' => null);

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
	public static function count()
	{
		return parent::count("SeasonTeams", array(), "", 1, true);
	}

	public function delete()
	{
		return parent::delete("SeasonTeams");
	}

	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("SeasonTeams", $field, $value, $id);
	}
}
