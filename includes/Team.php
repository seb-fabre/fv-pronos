<?php
/**
 * Description of Team
 *
 * @author arteau
 */

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

	public function getAliases()
	{
		return array_filter(array_map('trim', explode(',', $this->_data['aliases'])));
	}
}
