<?php
/**
 * Description of Notification
 *
 * @author arteau
 * @final
 */
class Notification
{
	/**
	 * @return string
	 */
	public static function clearAll()
	{
		$_SESSION['Notifications'] = array();
	}

	/**
	 * @param
	 */
	public static function Clear($name)
	{
		unset($_SESSION['Notifications'][$name]);
	}

	/**
	 * @param $imageDir
	 * @param $WithScriptaculous
	 * @param $bReturn
	 * @return string
	 */
	public static function display()
	{
		if (empty($_SESSION['Notifications']))
			return '';

		foreach ($_SESSION['Notifications'] as $name => $notification)
		{
			$color = '';
			$bullet = '';

			switch ($notification['type'])
			{
				case 'info':
					$bullet = 'info.png';
					$color = '#FFA';
					break;

				case 'error':
					$bullet = 'error.png';
					$color = '#FAA';
					break;

				case 'warning':
					$bullet = 'warning.png';
					$color = '#FA6';
					break;

				case 'success':
				default:
					$bullet = 'success.png';
					$color = '#CFC';
			}

			$messages = '<p>' . implode('</p><p>', $notification['messages']) . '</p>';

			echo '<div id="notifications_' . $name . '" class="notifications">';
			echo '<img src="' . APPLICATION_URL . 'images/' . $bullet . '" />';
			echo $messages;
			echo '</div>';

			$GLOBALS['FooterJS'] .= '$("#notifications_' . $name . '").animate({"background-color": "' . $color . '"}, 3000);' . "\n"; // jquery color effect on div
		}
	}

	/**
	 * @param $name
	 * @param $message
	 * @param $type
	 */
	public static function add($name, $message, $type = '')
	{
		if (empty($_SESSION['Notifications']))
			$_SESSION['Notifications'] = array();

		if (empty($_SESSION['Notifications'][$name]))
			$_SESSION['Notifications'][$name] = array();

		$_SESSION['Notifications'][$name]['messages'] = array();
		$_SESSION['Notifications'][$name]['messages'] []= $message;

		$_SESSION['Notifications'][$name]['type'] = $type;
	}

	/**
	 * Returns the notifications for the given name
	 *
	 * @param $name
	 * @return array
	 */
	public static function get($name)
	{
		if (!empty($_SESSION['Notifications'][$name]))
			return $_SESSION['Notifications'][$name];

		return null;
	}

	/**
	 * Returns all the notifications in session
	 *
	 * @return array
	 */
	public static function getAll()
	{
		if (!empty($_SESSION['Notifications']))
			return $_SESSION['Notifications'];

		return null;
	}
}

