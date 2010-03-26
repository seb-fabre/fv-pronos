<?php
$GLOBALS["classes"]["Season"] = array("classname" => "Season", "tablename" => "pr_season");

class Season extends _Season
{
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
