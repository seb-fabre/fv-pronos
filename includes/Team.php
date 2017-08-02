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
		if (file_exists($GLOBALS['ROOTPATH'] . 'logos/' . $this->id . '.png'))
			return APPLICATION_URL . 'logos/' . $this->id . '.png';

		if (file_exists($GLOBALS['ROOTPATH'] . 'logos/' . $this->id . '.gif'))
			return APPLICATION_URL . 'logos/' . $this->id . '.gif';

		return '';
	}

	public function getLogo($options="")
	{
		return '<img src="' . $this->getLogoUrl() . '" ' . $options . ' width=60 height=60/>';
	}

	public function getAliases()
	{
		return array_filter(array_map('trim', explode(',', $this->_data['aliases'])));
	}
}
