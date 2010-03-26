<?php
$GLOBALS["classes"]["Prono"] = array("classname" => "_Prono", "tablename" => "pr_prono");

class Prono extends _Prono
{
	function findByDayUser($day, $user)
	{
		$results = array();
		
		$req = mysql_query('SELECT pr_prono.* FROM pr_prono INNER JOIN pr_match ON pr_match.id=pr_match_id WHERE pr_day_id=' . $day . ' AND pr_user_id=' . $user);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Prono($res);
		
		return $results;
	}
	
	function findByDay($day)
	{
		$results = array();
		
		$req = mysql_query('SELECT pr_prono.* FROM pr_prono INNER JOIN pr_match ON pr_match.id=pr_match_id WHERE pr_day_id=' . $day);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Prono($res);
		
		return $results;
	}
}
