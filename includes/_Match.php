<?php
$GLOBALS["classes"]["Match"] = array("classname" => "_Match", "tablename" => "pr_match");

class _Match extends ArtObject
{
	protected $_data = array('id' => null, 'pr_day_id' => null, 'pr_away_team_id' => null, 'pr_home_team_id' => null, 'home_goals' => null, 'away_goals' => null);

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
	 * @return Match
	 */
	public static function find($id)
	{
		return parent::find("Match", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("Match", array(), $order);
	}

	/**
	 * @return Match
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("Match", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("Match", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("Match");
	}

	/**
	 * @return int
	 */
	public static function count()
	{
		return parent::count("Match", array(), "", 1, true);
	}

	public function delete()
	{
		return parent::delete("Match");
	}
}
