<?php
/**
 * Description of ArtObject
 *
 * @author arteau
 */
class ArtObject {

	/**
	 * The object's data values
	 *
	 * @var array an array of values, indexed by field name
	 */
	protected $_data = array();

	/**
	 * The list of field names that have been edited (for the save function)
	 *
	 * @var array
	 */
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
	 * Retrieve an object by its ID
	 *
	 * @param string$class the name of the class
	 * @param integer $id
	 *
	 * @return ArtObject
	 */
	public static function find($class, $id)
	{
		if (!$id)
			return false;

		$req = mysql_query('SELECT * FROM ' . $GLOBALS['classes'][$class]['tablename'] . ' WHERE id=' . mysql_escape_string($id)) or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new $GLOBALS['classes'][$class]['classname'](mysql_fetch_array($req));
		return false;
	}

	/**
	 * Retrieve all the objects in database
	 *
	 * @param string $class the name of the class to search
	 * @param string $order order by clause
	 *
	 * @return array the array of objects
	 */
	public static function getAll($class, $order='')
	{
		return self::search($class, array(), $order);
	}

	/**
	 * Search an object by a specific field and value
	 *
	 * @param string $class the class name
	 * @param string $field the field name
	 * @param string $value the value
	 *
	 * @return ArtObject
	 */
	public static function findBy($class, $field, $value)
	{
		$req = mysql_query('SELECT * FROM ' . $GLOBALS['classes'][$class]['tablename'] . ' WHERE ' . $field . ' LIKE "' . mysql_escape_string($value) . '"') or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new $GLOBALS['classes'][$class]['classname'](mysql_fetch_array($req));
		return false;
	}

	/**
	 * Retrieve objects from specific criteria
	 *
	 * @param string $class the class name of the object to search
	 * @param array $criteria the search criteria
	 * @param string $order the sort order
	 * @param integer $limit the number of rows to return
	 * @param boolean $onlyCount true to return only the number of results
	 *
	 * @return array the search results in an array of objects
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

		$req = mysql_query($query) or die (mysql_error());
		while ($res = mysql_fetch_array($req))
			$results[$res['id']] = new $GLOBALS['classes'][$class]['classname']($res);

		return $results;
	}

	/**
	 * Override the object's getter
	 *
	 * @param string $key the field to retrieve
	 * 
	 * @return string
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->_data))
		{
			$j = @json_decode($this->_data[$key], true);
			if ($j !== false && is_array($j) && isset($j[$_SESSION["l"]]))
				return $j[$_SESSION["l"]];
			else
				return $this->_data[$key];
		}
		return false;
	}

	/**
	 * Override the object's setter
	 *
	 * @param string $key the field to set
	 * @param string $value the value
	 */
	public function __set($key, $value)
	{
		if (array_key_exists($key, $this->_data))
			if ($this->_data[$key] != $value)
			{
				$this->_editedFields []= $key;
				$this->_data[$key] = $value;
			}
	}

	/**
	 * Update the edited values of an object in the database
	 * Only updates the edited fields
	 *
	 * @param string $class the name of the class of the object to save
	 * 
	 * @return integer the id of the object saved
	 */
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

			mysql_query($query) or die (mysql_error());

			$id = $this->id;
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
				$query .= $glu . mysql_escape_string($this->_data[$field]);
				$glu = '", "';
			}
			$query .= '")';

			mysql_query($query) or die (mysql_error());

			$id = mysql_insert_id();
		}

		$this->_editedFields = array();
		$new = self::find($class, $id);
		$data = $new->toArray();
		$this->_data = $data;
		foreach ($data as $key => $value)
			$this->$key = $value;

		return $id;
	}

	/**
	 * Retrieve the number of rows for a search criteria
	 *
	 * @param string $class the name of the object's class
	 * 
	 * @return integer the number of results
	 */
	public static function count($class)
	{
		return self::search($class, array(), "", 1, true);
	}

	/**
	 * Retrieve the data values of this object in an array
	 *
	 * @return array the array of values, indexed by database field names
	 */
	public function toArray()
	{
		return $this->_data;
	}

	/**
	 * Delete the current object from the database
	 *
	 * @param string $class the name of the object's class
	 */
	public function delete($class)
	{
		mysql_query("DELETE FROM " . $GLOBALS['classes'][$class]['tablename'] . " WHERE id=" . $this->id);
	}

	/**
	 * Check if a couple field/value is unique in a table
	 * optionnaly excluding an id from the search
	 *
	 * @param string $class the name of the object's class
	 * @param string $field the field
	 * @param string $value the value
	 * @param integer $id the optional id to exclude from the search
	 *
	 * @return boolean false if the field and value already exist in the database
	 */
	public static function isUnique($class, $field, $value, $id=false)
	{
		$object = self::findBy($class, $field, $value);

		if (empty($object))
			return true;

		if (!empty($id) && $object->id == $id)
			return true;

		return false;
	}

	public function hasField($field)
	{
		return isset($this->_data[$field]);
	}

	public function __call($methodName, $args)
	{
		// only manage the methods starting by "get"
		if (substr($methodName, 0, 3) != 'get')
			return null;

		$method = substr($methodName, 3);

		$field = 'pr_' . camelCaseToUnderscores($method) . '_id';

		// if function name can be related to a classname, search the related class
		if (array_key_exists($field, $this->_data) && isset($GLOBALS['classes'][$method]))
		{
			return ArtObject::find($method, $this->_data[$field]);
		}
//		else if (isset($this->_data[$field]) && !empty($args) && isset($GLOBALS['classes'][$args[0]]))
//		{
//			return ArtObject::find($args[0], $this->_data[$field]);
//		}

		// if function name can be related to a classname+s, search the related class and return an array of objects
		$lastChar = $methodName[strlen($methodName)-1];
		if ($lastChar == 's')
		{
			$method = substr($methodName, 3, -1);

			$backtrace = debug_backtrace();

			if (isset($backtrace[1]['class']) && isset($GLOBALS['classes'][$backtrace[1]['class']]) && isset($GLOBALS['classes'][$method]))
			{
				$classname = $backtrace[1]['class'];
				$field = 'pr_' . camelCaseToUnderscores($classname) . '_id';
				return ArtObject::search($method, array(array($field, $this->id)));
			}
		}

		throw new Exception('Invalid method ' . $methodName . '.');
	}
}
