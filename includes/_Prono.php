<?php
$GLOBALS["classes"]["Prono"] = array("classname" => "_Prono", "tablename" => "pr_prono");

class _Prono extends ArtObject
{
	protected $_data = array('id' => null, 'pr_match_id' => null, 'pr_user_id' => null, 'home_goals' => null, 'away_goals' => null, 'updated_at' => null);

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
	 * @return Prono
	 */
	public static function find($id)
	{
		return parent::find("Prono", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("Prono", array(), $order);
	}

	/**
	 * @return Prono
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("Prono", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("Prono", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("Prono");
	}

	/**
	 * @return int
	 */
	public static function count()
	{
		return parent::count("Prono", array(), "", 1, true);
	}

	public function delete()
	{
		return parent::delete("Prono");
	}
}
