<?php
class Season
{
	private $_data = array('id' => null, 'pr_league_id' => null, 'label' => null);

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

		$req = mysql_query('SELECT * FROM pr_season WHERE id=' . $id) or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new Season(mysql_fetch_array($req));
		return false;
	}

	public static function getAll($order='label ASC')
	{
		return Season::search(array(), $order);
	}

	public static function findBy($field, $value)
	{
		$req = mysql_query('SELECT * FROM pr_season WHERE ' . $field . ' LIKE "' . $value . '"') or die(mysql_error());
		if (mysql_num_rows($req) != 0)
			return new Season(mysql_fetch_array($req));
		return false;
	}

	public static function search($criteria = array(), $order="")
	{
		$results = array();

		$query = "SELECT * FROM pr_season";

		if (count($criteria) != 0)
		{
			$query .= "WHERE ";
			$glu = "";
			foreach ($criteria as $criterion)
			{
				if (count($criterion) == 2)
					$criterion[1] = "=";
				$query .= $glu . $criterion[0]	. $criterion[2] . $criterion[1] . " ";
				$glu = "AND ";
			}
		}

		if ($order != "")
			$query .= " ORDER BY " . $order;

		$req = mysql_query($query);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Season($res);

		return $results;
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
			$query = 'UPDATE pr_season SET ';

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
			$query = 'INSERT INTO pr_season(';
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

	public function delete()
	{
		mysql_query("DELETE FROM pr_season WHERE id=" . $this->id);
	}

	public function getLeague()
	{
		if ($this->pr_league_id)
			return League::find($this->pr_league_id);
		return new League();
	}

	public function getDays($order='number ASC')
	{
		$results = array();

		$req = mysql_query('SELECT pr_day.* FROM pr_day WHERE pr_season_id=' . $this->id . ' ORDER BY ' . $order);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Day($res);

		return $results;
	}

	public function getMatches()
	{
		$results = array();

		$req = mysql_query('SELECT pr_match.* FROM pr_match INNER JOIN pr_day ON pr_day_id=pr_day.id WHERE pr_season_id=' . $this->id);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Match($res);

		return $results;
	}

	public function getPronos()
	{
		$results = array();

		$req = mysql_query('SELECT pr_prono.* FROM pr_prono INNER JOIN pr_match ON pr_match.id=pr_match_id INNER JOIN pr_day ON pr_day_id=pr_day.id WHERE pr_season_id=' . $this->id);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Prono($res);

		return $results;
	}

	public function getTeams($order='name asc')
	{
		$results = array();

		$req = mysql_query('SELECT DISTINCT pr_team.* FROM pr_team INNER JOIN pr_season_teams ON pr_team_id = pr_team.id AND pr_season_id = ' . $this->id . ' ORDER BY ' . $order);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Team($res);

		return $results;
	}

	public function addTeam($teamId)
	{
		mysql_query('INSERT INTO pr_season_teams(pr_team_id, pr_season_id) VALUES(' . $teamId . ', ' . $this->id . ')');
	}
}
?>