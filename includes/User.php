<?php
/**
 * Description of User
 *
 * @author arteau
 */

$GLOBALS["classes"]["User"] = array("classname" => "User", "tablename" => "pr_user");
	
class User extends _User
{
	public static function tryToLogin($login, $password)
	{
		$user = self::findBy('name', $login);

		if (empty($user) || empty($login) || empty($password) || !$user->checkPassword($password))
		{
			Notification::add('LoginFailed', 'Compte utilisateur non trouvé.', 'error');
			return false;
		}

		$_SESSION['user'] = $user;
	}

	public function checkPassword($password)
	{
		$p = $this->passwd;

		return !is_null($p) && $p === md5($password);
	}
}
	