<?php
/**
 * Description of ArtObject
 *
 * @author arteau
 */
class ArtObject {

	protected $_data = array();
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
	 * @return ArtObject
	 */
	public static function find($class, $id)
	{
		if (!$id)
			return false;

		$req = mysql_query('SELECT * FROM ' . $GLOBALS['classes'][$class]['tablename'] . ' WHERE id=' . $id) or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new $GLOBALS['classes'][$class]['classname'](mysql_fetch_array($req));
		return false;
	}

	/**
	 * @return array
	 */
	public static function getAll($class, $order='')
	{
		return self::search($class, array(), $order);
	}

	/**
	 * @return ArtObject
	 */
	public static function findBy($class, $field, $value)
	{
		$req = mysql_query('SELECT * FROM ' . $GLOBALS['classes'][$class]['tablename'] . ' WHERE ' . $field . ' LIKE "' . $value . '"') or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new $GLOBALS['classes'][$class]['classname'](mysql_fetch_array($req));
		return false;
	}
	/**
	 * @return array
	 */
	public static function search($class, $criteria = array(), $order="", $limit=false, $onlyCount=false)
	{
		$results = array();

		if (!$onlyCount)
			$query = 'SELECT * FROM ' . $GLOBALS['classes'][$class]['tablename'];
		else
			$query = 'SELECT COUNT(1) AS count FROM ' . $GLOBALS['classes'][$class]['tablename'];

		if (count($criteria) != 0)
		{
			$query .= " WHERE ";
			$glu = "";
			foreach ($criteria as $criterion)
			{
				if (!isset($criterion[2]))
					$criterion[2] = "=";
				if ($criterion[1] !== NULL)
					$query .= $glu . $criterion[0]	. $criterion[2] . '"' . mysql_escape_string($criterion[1]) . "\" ";
				else
					$query .= $glu . $criterion[0]	. " IS NULL ";
				$glu = "AND ";
			}
		}

		if ($onlyCount)
		{
			$query = mysql_query($query);
			$res = mysql_fetch_array($query);
			return $res["count"];
		}

		if ($order != "")
			$query .= " ORDER BY " . $order;
		if (!empty($limit))
			$query .= " LIMIT " . $limit;

		$req = mysql_query($query);
		while ($res = mysql_fetch_array($req))
			$results[$res['id']] = new $GLOBALS['classes'][$class]['classname']($res);

		return $results;
	}

	public function __get($key)
	{
		if (array_key_exists($key, $this->_data))
		{
			$j = @json_decode($this->_data[$key], true);
			if ($j !== false && is_array($j) && isset($j[$_SESSION["l"]]))
				return utf8_encode($j[$_SESSION["l"]]);
			else
				return utf8_encode($this->_data[$key]);
		}
		return false;
	}

	public function __set($key, $value)
	{
		if (array_key_exists($key, $this->_data))
			if ($this->_data[$key] != $value)
			{
				$this->_editedFields []= $key;
				$this->_data[$key] = $value;
			}
	}

	public function save($class)
	{
		unset($this->_editedFields['id']);

		if (count($this->_editedFields) == 0)
			return;

		if ($this->_data['id'])
		{
			$query = 'UPDATE ' . $GLOBALS['classes'][$class]['tablename'] . ' SET ';

			$glu = '';
			foreach ($this->_editedFields as $field)
			{
				$query .= $glu . $field . '="' . addslashes($this->_data[$field]) . '"';
				$glu = ', ';
			}
			$query .= ' WHERE id=' . $this->id;
		}
		else
		{
			$query = 'INSERT INTO ' . $GLOBALS['classes'][$class]['tablename'] . '(';
			$glu = '';
			foreach ($this->_editedFields as $field)
			{
				$query .= $glu . $field;
				$glu = ', ';
			}
			$query .= ') VALUES("';
			$glu = '';
			foreach ($this->_editedFields as $field)
			{
				$query .= $glu . addslashes($this->_data[$field]);
				$glu = '", "';
			}
			$query .= '")';
		}
		mysql_query($query) or die (mysql_error());

		$id = mysql_insert_id();
		$this->_editedFields = array();
		$new = self::find($GLOBALS['classes'][$class]['tablename'], $id);
		$data = $new->toArray();
		$this->_data = $data;
		foreach ($data as $key => $value)
			$this->$key = $value;
	}

	/**
	 * @return int
	 */
	public static function count($class)
	{
		return self::search($class, array(), "", 1, true);
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->_data;
	}

	public function delete($class)
	{
		mysql_query("DELETE FROM " . $GLOBALS['classes'][$class]['tablename'] . " WHERE id=" . $this->id);
	}
}
