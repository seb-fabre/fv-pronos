<?php
/**
 * Description of Day
 *
 * @author arteau
 */

$GLOBALS["classes"]["Day"] = array("classname" => "Day", "tablename" => "pr_day");
	
class Day extends _Day
{

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
	