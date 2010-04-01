<?php
/**
 * Description of Match
 *
 * @author arteau
 */

$GLOBALS["classes"]["Match"] = array("classname" => "Match", "tablename" => "pr_match");
	
	class Match extends _Match
	{
		/**
		 * @return Team
		 */
		public function getHomeTeam()
		{
			return ArtObject::find('Team', $this->pr_home_team_id);
		}

		/**
		 * @return Team
		 */
		public function getAwayTeam()
		{
			return ArtObject::find('Team', $this->pr_away_team_id);
		}

		/**
		 * @return Day
		 */
		public function getDay()
		{
			return ArtObject::find('Team', $this->pr_away_team_id);
		}
	}
	