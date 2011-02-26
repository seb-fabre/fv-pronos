<?php
/**
 * Description of Season
 *
 * @author arteau
 */

$GLOBALS["classes"]["Season"] = array("classname" => "Season", "tablename" => "pr_season");
	
class Season extends _Season
{
	// FIXME : redo with the refactoring of the search criteria (joins)
	public function getPronos()
	{
		$results = array();

		$req = mysql_query('SELECT DISTINCT pr_prono.* FROM pr_prono INNER JOIN pr_match ON pr_match.id=pr_match_id INNER JOIN pr_day ON pr_day_id=pr_day.id WHERE pr_season_id=' . $this->id);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Prono($res);

		return $results;
	}

	// FIXME : redo with the refactoring of the search criteria (joins)
	public function getMatchs()
	{
		$results = array();

		$req = mysql_query('SELECT DISTINCT pr_match.* FROM pr_match INNER JOIN pr_day ON pr_day_id=pr_day.id WHERE pr_season_id=' . $this->id);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new Match($res);

		return $results;
	}

	/**
	 * Returns only the users that have played in this season
	 */
	public function getUsers($order='')
	{
		if ($order != '')
			$order = ' ORDER BY ' . $order;

		$results = array();

		$req = mysql_query('SELECT DISTINCT pr_user.* 
												FROM pr_user
												INNER JOIN pr_prono ON pr_user_id=pr_user.id
												INNER JOIN pr_match ON pr_match_id=pr_match.id
												INNER JOIN pr_day ON pr_day_id=pr_day.id
												WHERE pr_season_id=' . $this->id .
												$order);
		while ($res = mysql_fetch_array($req))
			$results [$res['id']] = new User($res);

		return $results;
	}

	public function getMatches()
	{
		return $this->getMatchs();
	}

	// FIXME : redo with the refactoring of the search criteria (joins)
	public function hasMatches()
	{
		return count($this->getMatchs()) > 0;
	}

	// FIXME : redo with the refactoring of the search criteria (joins)
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
		$seasonTeam = new SeasonTeams();
		$seasonTeam->pr_team_id = $teamId;
		$seasonTeam->pr_season_id = $this->id;
		$seasonTeam->save();
	}

	public static function objectsToSelect($objects, $options = array())
	{
		$id = (!empty($options['id']) ? ' id="' . $options['id'] . '"' : '');
		$name = (!empty($options['name']) ? ' name="' . $options['name'] . '"' : '');
		$value = (!empty($options['value']) ? $options['value'] : false);

		$html = '<select' . $id . $name . '>';
		if (!empty($options['empty']))
			$html .= '<option value="">' . $options['empty'] . '</option>';

		foreach ($objects as $o)
			$html .= '<option value="' . $o->id . '"' . ($value !== false && $o->id == $value ? ' selected="selected"' : '') . '>' . $o->getLeague()->name . ' - ' . $o->label . '</option>';

		$html .= '</select>';

		return $html;
	}

	public function getClassement($dayMax=false)
	{
		if (empty($dayMax))
			$dayMaxSQL = '';
		else
			$dayMaxSQL = "AND pr_day.number <= $dayMax";

		$sql = "SELECT pr_user_id, SUM(points) AS points
						FROM (
							SELECT
								pr_prono.pr_user_id,
								pr_match.id match_id,
								pr_home_team_id home_team,
								pr_away_team_id away_team,
								CASE WHEN pr_match.home_goals = pr_prono.home_goals AND pr_match.away_goals =
								pr_prono.away_goals THEN 3 WHEN SIGN(CONVERT(pr_match.home_goals, SIGNED) -
								CONVERT(pr_match.away_goals, SIGNED)) =
								SIGN(CONVERT(pr_prono.home_goals, SIGNED) -
								CONVERT(pr_prono.away_goals, SIGNED)) THEN 1 ELSE 0 END points
							FROM `pr_prono`
							INNER JOIN pr_match ON pr_prono.pr_match_id=pr_match.id
							INNER JOIN pr_day ON pr_day.id=pr_day_id AND pr_season_id={$this->id}
							WHERE 1=1
								$dayMaxSQL
							) t
						GROUP BY pr_user_id
						ORDER BY points DESC";

		$results = array();
		$req = Tools::mysqlQuery($sql) or die (Tools::mysqlError());
		while ($res = mysql_fetch_array($req))
			$results[$res['pr_user_id']] = $res['points'];

		return $results;
	}

	public function getClassementDetails($dayMax=false)
	{
		if (empty($dayMax))
			$dayMaxSQL = '';
		else
			$dayMaxSQL = "AND pr_day.number <= $dayMax";

		$sql = "SELECT pr_user_id, SUM(one_point) AS one_point, SUM(three_points) AS three_points, SUM(points) AS points, COUNT(DISTINCT pr_day_id) AS played, ROUND(SUM(points) / COUNT(DISTINCT pr_day_id), 2) AS average
						FROM (
							SELECT
								pr_prono.pr_user_id,
								pr_user.name,
								pr_day.id AS pr_day_id,
								CASE WHEN pr_match.home_goals = pr_prono.home_goals AND pr_match.away_goals =
								pr_prono.away_goals THEN 1 ELSE 0 END three_points,
								CASE WHEN (pr_match.home_goals != pr_prono.home_goals OR pr_match.away_goals !=
								pr_prono.away_goals) AND SIGN(CONVERT(pr_match.home_goals, SIGNED) -
								CONVERT(pr_match.away_goals, SIGNED)) =
								SIGN(CONVERT(pr_prono.home_goals, SIGNED) -
								CONVERT(pr_prono.away_goals, SIGNED)) THEN 1 ELSE 0 END one_point,
									CASE WHEN pr_match.home_goals = pr_prono.home_goals AND pr_match.away_goals =
									pr_prono.away_goals THEN 3 WHEN SIGN(CONVERT(pr_match.home_goals, SIGNED) -
									CONVERT(pr_match.away_goals, SIGNED)) =
									SIGN(CONVERT(pr_prono.home_goals, SIGNED) -
									CONVERT(pr_prono.away_goals, SIGNED)) THEN 1 ELSE 0 END points
							FROM `pr_prono`
							INNER JOIN pr_match ON pr_prono.pr_match_id=pr_match.id AND pr_match.home_goals IS NOT NULL
							INNER JOIN pr_day ON pr_day.id=pr_day_id AND pr_season_id={$this->id}
							INNER JOIN pr_user ON pr_user.id=pr_user_id
							WHERE 1=1
								$dayMaxSQL
						) t
						GROUP BY pr_user_id
						ORDER BY points DESC, three_points DESC, average DESC, name ASC";

		$results = array();
		$req = Tools::mysqlQuery($sql) or die (Tools::mysqlError());
		while ($res = mysql_fetch_assoc($req))
			$results[$res['pr_user_id']] = $res;

		return $results;
	}

	/**
	 *
	 * @return Day
	 */
	public function getLastDayWithCompletedMatches()
	{
		$sql = "SELECT pr_day.id, pr_day.number, COUNT(pr_match.id) AS count
						FROM pr_day
						LEFT JOIN pr_match ON pr_day_id=pr_day.id AND home_goals IS NOT NULL AND away_goals IS NOT NULL
						WHERE pr_season_id={$this->id}
						GROUP BY pr_day.id
						HAVING count > 0
						ORDER BY number DESC";

		$results = array();
		$req = Tools::mysqlQuery($sql) or die (Tools::mysqlError());
		$res = mysql_fetch_array($req);

		return Day::find($res['id']);
	}

	public function getMaxPointsForADayForAUser()
	{
		$sql = "SELECT pr_user_id, MAX(points) AS points
						FROM (
							SELECT pr_user_id, pr_day_id, SUM(points) AS points
							FROM (
								SELECT
									pr_prono.pr_user_id,
									pr_day.id AS pr_day_id,
										CASE WHEN pr_match.home_goals = pr_prono.home_goals AND pr_match.away_goals =
										pr_prono.away_goals THEN 3 WHEN SIGN(CONVERT(pr_match.home_goals, SIGNED) -
										CONVERT(pr_match.away_goals, SIGNED)) =
										SIGN(CONVERT(pr_prono.home_goals, SIGNED) -
										CONVERT(pr_prono.away_goals, SIGNED)) THEN 1 ELSE 0 END points
								FROM `pr_prono`
								INNER JOIN pr_match ON pr_prono.pr_match_id=pr_match.id
								INNER JOIN pr_day ON pr_day.id=pr_day_id AND pr_season_id={$this->id}
							)t
							GROUP BY pr_user_id, pr_day_id
						) tt
						GROUP BY pr_user_id";

		$results = array();
		$req = Tools::mysqlQuery($sql) or die (Tools::mysqlError());
		while ($res = mysql_fetch_assoc($req))
			$results[$res['pr_user_id']] = $res['points'];

		return $results;
	}
}
