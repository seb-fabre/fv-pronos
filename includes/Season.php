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
}
