<?php 
class User
{
	private $_data = array('id' => null, 'name' => null, 'passwd' => null, 'email' => null, 'pr_team_id' => NULL);
	
	private $_editedFields = array();
	
	public function __construct($values = array())
	{
		if ($values)
		foreach ($values as $key => $value)
		{
			if (array_key_exists($key, $this->_data))
				$this->$key = $value;
		}
	}
	
	public static function find($id)
	{
		if (!$id)
			return false;
		
		$req = mysql_query('SELECT * FROM pr_user WHERE id=' . $id) or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new User(mysql_fetch_array($req));
		return false;
	}
	
	public static function getAll($order='name ASC')
	{
		$results = array();
		
		$req = mysql_query('SELECT * FROM pr_user ORDER BY ' . $order);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new User($res);
		
		return $results;
	}
	
	public static function findBy($field, $value)
	{
		$req = mysql_query('SELECT * FROM pr_user WHERE ' . $field . ' LIKE "' . $value . '"') or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new User(mysql_fetch_array($req));
		return false;
	}
	
	public function __get($key)
	{
		if (array_key_exists($key, $this->_data))
			return $this->_data[$key];
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
	
	public function save()
	{
		unset($this->_editedFields['id']);
		
		if (count($this->_editedFields) == 0)
			return;
		
		if ($this->_data['id'])
		{
			$query = 'UPDATE pr_user SET ';
			
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
			$query = 'INSERT INTO pr_user(';
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
	}
}
?>