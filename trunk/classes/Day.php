<?php
class Day
{
	private $_data = array('id' => null, 'pr_season_id' => null, 'number' => null, 'date' => null, 'limit_date' => null);

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

		$req = mysql_query('SELECT * FROM pr_day WHERE id=' . $id) or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new Day(mysql_fetch_array($req));
		return false;
	}

	public static function getAll($order='name ASC')
	{
		$results = array();

		$req = mysql_query('SELECT * FROM pr_day ORDER BY ' . $order);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Day($res);

		return $results;
	}

	public static function findBy($field, $value)
	{
		$req = mysql_query('SELECT * FROM pr_day WHERE ' . $field . ' LIKE "' . $value . '"') or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new Day(mysql_fetch_array($req));
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
			$query = 'UPDATE pr_day SET ';

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
			$query = 'INSERT INTO pr_day(';
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

	public function getMatches()
	{
		$results = array();

		$req = mysql_query('SELECT * FROM pr_match WHERE pr_day_id=' . $this->id);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Match($res);

		return $results;
	}

	public function getPronos()
	{
		$results = array();

		$req = mysql_query('SELECT pr_prono.* FROM pr_prono INNER JOIN pr_match ON pr_match_id=pr_match.id WHERE pr_day_id=' . $this->id);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Prono($res);

		return $results;
	}

	public function getSeason()
	{
		if ($this->pr_season_id)
			return Season::find($this->pr_season_id);
		return new Season();
	}
	
	public function hasCompletedMatches()
	{
		$req = mysql_query('SELECT COUNT(1) c FROM pr_match WHERE home_goals IS NOT NULL AND away_goals IS NOT NULL AND pr_day_id=' . $this->id);
		$res = mysql_fetch_array($req);
		return $res['c'] != 0;
	}
}
?>