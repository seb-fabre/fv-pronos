<?php
	mysql_connect('localhost', 'root', '');
	mysql_select_db('pronos');
	
	$req = mysql_query('SHOW TABLES;');
	while ($res = mysql_fetch_array($req))
	{
		$file = fopen(ucwords(substr($res[0], 3)) . '.php', 'w+');
		
		fwrite($file, '<?php 
class ' . ucwords(substr($res[0], 3)) . '
{
');
		$fields = array();
		
		$q = mysql_query('SHOW COLUMNS FROM ' . $res[0] . ';');
		while ($r = mysql_fetch_array($q))
		{
			$fields []= $r[0];
			if (substr($r[0], 0, 3) != 'pr_')
				$n = str_replace(' ', '', ucwords(str_replace('_', ' ', $r[0])));
			else
				$n = str_replace(' ', '', ucwords(str_replace('_', ' ', substr($r[0], 3))));
		}
		fwrite($file, '	private $_data = array(\'' . implode("' => null, '", $fields) . '\' => null);
	
	private $_editedFields = array();
	
	public function __construct($values = array())
	{
		if ($values)
		foreach ($values as $key => $value)
		{
			if (array_key_exists($key, $this->_data))
				$this->_data[$key] = $value;
		}
	}
	
	public static function find($id)
	{
		if (!$id)
			return false;
		
		$req = mysql_query(\'SELECT * FROM ' . $res[0] . ' WHERE id=\' . $id) or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new ' . ucwords(substr($res[0], 3)) . '(mysql_fetch_array($req));
		return false;
	}
	
	public static function getAll($order=\'name ASC\')
	{
		$results = array();
		
		$req = mysql_query(\'SELECT * FROM ' . $res[0] . ' ORDER BY \' . $order);
		while ($res = mysql_fetch_array($req))
			$results [$res[\'id\']] = new ' . ucwords(substr($res[0], 3)) . '($res);
		
		return $results;
	}
	
	public static function findBy($field, $value)
	{
		$req = mysql_query(\'SELECT * FROM ' . $res[0] . ' WHERE \' . $field . \' LIKE "\' . $value . \'"\') or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new ' . ucwords(substr($res[0], 3)) . '(mysql_fetch_array($req));
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
		unset($this->_editedFields[\'id\']);
		
		if (count($this->_editedFields) == 0)
			return;
		
		if ($this->_data[\'id\'])
		{
			$query = \'UPDATE ' . $res[0] . ' SET \';
			
			$glu = \'\';
			foreach ($this->_editedFields as $field)
			{
				$query .= $glu . $field . \'="\' . addslashes($this->_data[$field]) . \'"\';
				$glu = \', \';
			}
			$query .= \' WHERE id=\' . $this->id;
		}
		else
		{
			$query = \'INSERT INTO ' . $res[0] . '(\';
			$glu = \'\';
			foreach ($this->_editedFields as $field)
			{
				$query .= $glu . $field;
				$glu = \', \';
			}
			$query .= \') VALUES("\';
			$glu = \'\';
			foreach ($this->_editedFields as $field)
			{
				$query .= $glu . addslashes($this->_data[$field]);
				$glu = \'", "\';
			}
			$query .= \'")\';
		}
		mysql_query($query) or die (mysql_error());
	}
');
		fwrite($file, '}
?>');
		fclose($file);
		echo '<code><pre>' . htmlentities(file_get_contents(ucwords(substr($res[0], 3)) . '.php')) . '</pre></code>';
	}
?>