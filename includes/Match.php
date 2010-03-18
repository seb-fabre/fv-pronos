<?php 
class Match
{
	private $_data = array('id' => null, 'pr_day_id' => null, 'pr_away_team_id' => null, 'pr_home_team_id' => null, 'home_goals' => null, 'away_goals' => null, 'date' => null, 'limit_date' => null);
	
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
		
		$req = mysql_query('SELECT * FROM pr_match WHERE id=' . $id) or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new Match(mysql_fetch_array($req));
		return false;
	}
	
	public static function getAll($order='name ASC')
	{
		$results = array();
		
		$req = mysql_query('SELECT * FROM pr_match ORDER BY ' . $order);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Match($res);
		
		return $results;
	}
	
	public static function findBy($field, $value)
	{
		$req = mysql_query('SELECT * FROM pr_match WHERE ' . $field . ' LIKE "' . $value . '"') or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new Match(mysql_fetch_array($req));
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
			$query = 'UPDATE pr_match SET ';
			
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
			$query = 'INSERT INTO pr_match(';
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
	
	public static function exists($day, $home, $away, $date)
	{
    $req = mysql_query('SELECT * FROM pr_match WHERE pr_day_id=' . $day . ' AND pr_home_team_id=' . $home . ' AND pr_away_team_id=' . $away . ' AND date=' . $date . ';');
    return mysql_num_rows($req) != 0;
	}
	
	public function getHomeTeam()
	{
		return Team::find($this->pr_home_team_id);
	}
	
	public function getAwayTeam()
	{
		return Team::find($this->pr_away_team_id);
	}
}
?>