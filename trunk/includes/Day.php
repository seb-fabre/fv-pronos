<?php
$GLOBALS["classes"]["Day"] = array("classname" => "Day", "tablename" => "pr_day");

class Day extends _Day
{
	public function getMatches()
	{
		$results = array();

		$criteria = array();
		$criteria []= array('pr_day_id', $this->id);
		$results = Match::search($criteria);

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

	public function hasPronos()
	{
		$results = array();

		$criteria = array();
		$criteria []= array('pr_day_id', $this->id);
		$results = Prono::count($criteria);

		return $results;
	}
}
