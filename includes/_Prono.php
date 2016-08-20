<?php
/**
 * Description of _Prono
 *
 * @author arteau
 */

$GLOBALS["classes"]["Prono"] = array("classname" => "_Prono", "tablename" => "pr_prono");

class _Prono extends ArtObject
{
	protected $_data = array('id' => null, 'pr_match_id' => null, 'pr_user_id' => null, 'home_goals' => null, 'away_goals' => null, 'updated_at' => null);

	protected $_editedFields = array();

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
		if (!parent::save("Prono"))
			return false;
		
		return $this->getMatch()->getDay()->getSeason()->save();
	}

	/**
	 * @return int
	 */
	public static function count($criteria = array())
	{
		return parent::count("Prono", $criteria, "", 1, true);
	}

	public function delete()
	{
		parent::delete("Prono");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("Prono", $field, $value, $id);
	}
}
