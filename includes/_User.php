<?php
/**
 * Description of _User
 *
 * @author arteau
 */

$GLOBALS["classes"]["User"] = array("classname" => "_User", "tablename" => "pr_user");

class _User extends ArtObject
{
	protected $_data = array('id' => null, 'name' => null, 'passwd' => null, 'email' => null, 'pr_team_id' => null, 'role' => null);

	protected $_editedFields = array();

	/**
	 * @return User
	 */
	public static function find($id)
	{
		return parent::find("User", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("User", array(), $order);
	}

	/**
	 * @return User
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("User", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("User", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		return parent::save("User");
	}

	/**
	 * @return int
	 */
	public static function count($criteria = array())
	{
		return parent::count("User", $criteria, "", 1, true);
	}

	public function delete()
	{
		parent::delete("User");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("User", $field, $value, $id);
	}
}
