<?php
$GLOBALS["classes"]["Match"] = array("classname" => "Match", "tablename" => "pr_match");

class Match extends _Match
{
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
