<?php
$GLOBALS["classes"]["Team"] = array("classname" => "Team", "tablename" => "pr_team");
	
	class Team extends _Team
	{
		public function getLogoUrl()
		{
			return APPLICATION_URL . 'logos/' . $this->id . '.gif';
		}

		public function getLogo($options="")
		{
			return '<img src="' . $this->getLogoUrl() . '" ' . $options . ' />';
		}
	}
	