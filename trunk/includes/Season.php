<?php
/**
 * Description of Season
 *
 * @author arteau
 */

$GLOBALS["classes"]["Season"] = array("classname" => "Season", "tablename" => "pr_season");
	
class Season extends _Season
{

	public function getPronos()
	{
		$results = array();

		$req = mysql_query('SELECT DISTINCT pr_prono.* FROM pr_prono INNER JOIN pr_match ON pr_match.id=pr_match_id INNER JOIN pr_day ON pr_day_id=pr_day.id WHERE pr_season_id=' . $this->id);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Prono($res);

		return $results;
	}

	public function getMatchs()
	{
		$results = array();

		$req = mysql_query('SELECT DISTINCT pr_match.* FROM pr_match INNER JOIN pr_day ON pr_day_id=pr_day.id WHERE pr_season_id=' . $this->id);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Match($res);

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
}
