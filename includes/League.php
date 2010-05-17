<?php
/**
 * Description of League
 *
 * @author arteau
 */

$GLOBALS["classes"]["League"] = array("classname" => "League", "tablename" => "pr_league");
	
class League extends _League
{
	public function getAvailableTeams($order='name ASC')
	{
		if (!$this->pr_team_category_id)
			return Team::getAll($order);

		$results = array();
		$req = mysql_query('SELECT DISTINCT pr_team.* FROM pr_team WHERE pr_team.pr_team_category_id=' . $this->pr_team_category_id .' ORDER BY ' . $order);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Team($res);

		return $results;
	}
}
	