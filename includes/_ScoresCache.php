<?php
/**
 * Description of _ScoresCache
 *
 * @author arteau
 */

$GLOBALS["classes"]["ScoresCache"] = array("classname" => "_ScoresCache", "tablename" => "pr_scores_cache");

class _ScoresCache extends ArtObject
{
	protected $_data = array('id' => null, 'pr_season_id' => null, 'sort_callback' => null, 'max_day' => null, 'cache_date' => null, 'cache_data' => null);

	protected $_editedFields = array();

	/**
	 * @return ScoresCache
	 */
	public static function find($id)
	{
		return parent::find("ScoresCache", $id);
	}

	/**
	 * @return array
	 */
	public static function getAll($order='')
	{
		return parent::search("ScoresCache", array(), $order);
	}

	/**
	 * @return ScoresCache
	 */
	public static function findBy($field, $value)
	{
		return parent::findBy("ScoresCache", $field, $value);
	}

	/**
	 * @return array
	 */
	public static function search($criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		return parent::search("ScoresCache", $criteria, $order, $limit, $onlyCount);
	}

	public function save()
	{
		$this->cache_date = date('Y-m-d H:i:s');
		return parent::save("ScoresCache");
	}

	/**
	 * @return int
	 */
	public static function count($criteria = array())
	{
		return parent::count("ScoresCache", $criteria, "", 1, true);
	}

	public function delete()
	{
		parent::delete("ScoresCache");
	}

	/**
	 * @return boolean
	 */
	public static function isUnique($field, $value, $id=false)
	{
		return parent::isUnique("ScoresCache", $field, $value, $id);
	}
}
