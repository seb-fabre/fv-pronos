<?php
class Tools
{
	public static $lastQuery = '';

	/**
	 * Returns the disclaimer displayed on the home page
	 *
	 * @return string
	 */
	public static function getDisclaimer()
	{
		$dis = Config::getValue('disclaimer');
		$dis = str_replace('{count_images}', Image::count(), $dis);
		$dis = str_replace('{count_videos}', Video::count(), $dis);
		return $dis;
	}

	/**
	 * Remove all bad characters from an url or encode it
	 *
	 * @param string $url
	 * @return string
	 */
	public static function cleanLink($url)
	{
		$url = urlencode($url);
		$url = str_replace('+', '-', $url);
		$url = preg_replace('/-+/', '-', $url);
		return strtolower($url);
	}

	public static function postSortFunction($x, $y)
	{
		return strcmp($x->$GLOBALS['postSortFunction_field'], $y->$GLOBALS['postSortFunction_field']) > 0;
	}

	/**
	 * Function called after sorting arrays
	 *
	 * @param array $values
	 * @param string $field
	 * @return array
	 */
	public static function postSort($values, $field)
	{
		$GLOBALS['postSortFunction_field'] = $field;
		uasort($values, 'postSortFunction');
		unset($GLOBALS['postSortFunction_field']);
		return $values;
	}

	/**
	 * Initialization function to browse the hierarchy of galleries
	 *
	 * @param int $id
	 * @return string the html code of the gallery tree
	 */
	public static function generateGalleryTree($id)
	{
		$GLOBALS['hierarchy'] = Gallery::getHierarchy($id);
		$html = Tools::browseGalleryTree(0);
		unset($GLOBALS['hierarchy']);
		return $html;
	}

	/**
	 * Recursive function to browse the hierarchy of galleries
	 *
	 * @param int $i the id of the parent gallery where to start browsing
	 * @return string the html code of the gallery tree
	 */
	public static function browseGalleryTree($i)
	{
		$hierarchy = $GLOBALS['hierarchy'];

		$id = $hierarchy[$i];

		$dir = '<ul class="galleryTree">';

		if ($id == '0')
			$galleries = Gallery::search(array(array('gallery_id', NULL)));
		else
			$galleries = Gallery::search(array(array('gallery_id', $id)));
		$galleries = Tools::postSort($galleries, 'name');

		foreach ($galleries as $gal)
		{
			if (isset($hierarchy[$i+1]) && $gal->id === $hierarchy[$i+1])
				$dir .= '<li class="directory collapsed"><a href="/gallery/' . $gal->id . '/' . Tools::cleanLink($gal->name) . '" class="active">' . $gal->name . '</a>' . Tools::browseGalleryTree($i + 1) . '</li>';
			else
				$dir .= '<li class="directory collapsed"><a href="/gallery/' . $gal->id . '/' . Tools::cleanLink($gal->name) . '">' . $gal->name . '</a></li>';
		}
		$dir .= '</ul>';
		return $dir;
	}

	/**
	 * Generate the hierarchy of children galleries of a given gallery
	 *
	 * @param int $id the id of the parent gallery
	 * @return string the html code of the gallery tree
	 */
	public static function listGalleryTree($id)
	{
		$galleries = Gallery::search(array(array('gallery_id', $id)));
		$galleries = Tools::postSort($galleries, 'name');
		$html = '<ul class="galleryTree" id="galleryTree' . (!is_null($id) ? $id . '" style="display: none' : 0) . '">';

		foreach ($galleries as $gal)
		{
			$html .= '<li class="directory collapsed' . ($gal->has_images ? ' hasImages' : '') . '"><a id="linkToGallery' . $gal->id . '" href="javascript:;" onclick="selectGallery(' . $gal->id . ')">' . $gal->name . '</a></li>';
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * Returns the translation of a string, if it exists
	 * if no translation found, return the original string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function translate($string)
	{
		global $dico;
		if (isset($dico[$string]))
			return $dico[$string];
		return $string;
	}


	/**
	 * Returns true if the parameter is a valid username
	 *
	 * @param string $login
	 * @return boolean
	 */
	public static function isUsernameValid($login)
	{
		return preg_match('/^[a-zA-Z0-9_\- ]+$/', $login);
	}


	/**
	 * Returns true if the parameter is a valid email
	 *
	 * @param string $email
	 * @return boolean
	 */
	public static function isEmailValid($email)
	{
		return preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $email);
	}

	/**
	 * Extract photos from a zip archive
	 *
	 * @param string $zipFile
	 * @param strig $sourceDir
	 * @param string $destDir
	 * @param array $files
	 */
	public static function extractPhotosFromZip($zipFile, $sourceDir, $destDir, &$files = array())
	{
		$zip = new PclZip($sourceDir . $zipFile);
		$zip->extract(PCLZIP_OPT_PATH, $sourceDir);

		$list = $zip->listContent();
		foreach ($list as $item)
			$files []= $item['filename'];
	}

	/**
	 * Returns true if the parameter is a valid title
	 *
	 * @param string $title
	 * @return boolean
	 */
	public static function isTitleValid($title)
	{
		return strpos($title, '"') === false;
	}

	/**
	 * Print the html head (<head>, <title>, <metas>...)
	 *
	 * @param string $title
	 */
	public static function echoHTMLHead($title, $additionalHtml=array())
	{
		$additionalJs = '';
		$additionalCss = '';
		$additionalMetas = '';

		if (isset($additionalHtml['js']))
			foreach ($additionalHtml['js'] as $file)
			{
				$additionalJs .= '<script src="' . APPLICATION_URL . $file . '" type="text/javascript"></script>' . "\n";
			}

		if (isset($additionalHtml['css']))
			foreach ($additionalHtml['css'] as $file)
			{
				$additionalCss .= '<link href="' . APPLICATION_URL . $file . '" rel="stylesheet" type="text/css" />' . "\n";
			}

		if (isset($additionalHtml['meta']))
			foreach ($additionalHtml['meta'] as $httpEquiv => $content)
			{
				$additionalMetas .= '<meta http-equiv="' . $httpEquiv . '" content="' . $content . '" />' . "\n";
			}

		$url = APPLICATION_URL;


		echo <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="{$url}css/process_css.php?name=site" rel="stylesheet" type="text/css" />
    <title>$title</title>
    <script src="/js/jquery-1.3.2.min.js" type="text/javascript"></script>

    $additionalCss
    $additionalJs
    $additionalMetas
</head>
HTML;
	}

	/**
	 * Print the html header
	 *
	 */
	public static function echoHeader()
	{
		echo '<div id="header">
      <img src="' . Tools::getImage('banniere.jpg') . '" alt="banniere du site" />';
    require_once(INCLUDE_PATH . '_menu.php');
    echo '</div>';
	}

	/**
	 * Print the html footer
	 *
	 */
	public static function echoFooter()
	{
		$footerJs = '<script type="text/javascript">
			$().ready(function(){
				' . $GLOBALS['FooterJS'] . '
			});
		</script>';

		echo <<<HTML
<div id="footer">
	Site créé et designé par arteau<br>
	pour toute réclamation : arteau <img alt="(a)" src="/img/aro.gif"> free.fr<br />
	Site optimisé pour Firefox<img id="ff" alt="" src="/img/ff.png">

	<p>
		<a href="http://validator.w3.org/check?uri=referer"><img width="88" height="31" alt="Valid XHTML 1.0 Strict" src="http://www.w3.org/Icons/valid-xhtml10"></a>
		<a href="http://jigsaw.w3.org/css-validator/check?uri=referer"><img alt="Valid CSS 2.1" src="http://jigsaw.w3.org/css-validator/images/vcss"></a>
	</p>
</div>
$footerJs
HTML;
	}

	/**
	 * Reindex an array, using a field as keys
	 *
	 * @param array $values the array of values to reindex
	 * @param string $field the field to reindex by
	 * @return array
	 */
	public static function reindexBy($values, $field)
	{
		$tmp = array();
		foreach ($values as $value)
		{
			if (isset($value[$field]))
				$tmp[$value[$field]] = $value;
			else
				$tmp []= $value;
		}
		return $tmp;
	}

	/**
	 * Returns an image absolute url from a file name
	 *
	 * @param string $name
	 * @return string
	 */
	public static function getImage($name)
	{
		return APPLICATION_URL . 'images/' . $name;
	}

	/**
	 * Browse directories, recursively, and return a list of files
	 * This function is used only to generate the language file
	 * @param string $directory the directory to search in
	 * @param string $extension filter the results by file extension
	 * @param boolean $full_path if the returned results must have an absolute path or not
	 * @return array the list of files found
	 */
	public static function browseDirectory($directory, $extension = "", $full_path = true)
	{
		$array_items = array();

		if (strpos($directory, '.svn') !== false
			|| strpos($directory, '/.') !== false
			|| strpos($directory, '/photos') !== false
			|| strpos($directory, '/sessions') !== false
			|| strpos($directory, '/css') !== false
			|| strpos($directory, '/js') !== false
			|| strpos($directory, '/images') !== false
			|| strpos($directory, '/img') !== false)
			return array();

		$directory = trim($directory, '/');

		if ($handle = opendir($directory))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != "..")
				{
					if (is_dir($directory . "/" . $file))
					{
						$array_items = array_merge($array_items, self::browseDirectory($directory . "/" . $file, $extension, $full_path));
					}
					else
					{
						if (!$extension || (ereg("." . $extension, $file)))
						{
							if ($full_path)
							{
								$array_items[] = $directory . "/" . $file;
							}
							else
							{
								$array_items[] = $file;
							}
						}
					}
				}
			}

			closedir($handle);
		}

		return $array_items;
	}

	/**
	 * Parse all the php files from the project, list all the translated strings and return them in an array
	 * This method is used to initialize or update the language files
	 * @return array the list of strings to be translated
	 */
	public static function getLanguageStrings()
	{
		$files = self::browseDirectory(ROOT_PATH, 'php');
		$strings = array();

		foreach ($files as $file)
		{
			$f = fopen($file, 'r');

			while ($line = fgets($f))
			{
				if (preg_match_all('/Tools::translate\(([^\)]*)\)/', $line, $matches))
				{
					foreach ($matches[1] as $match)
					{
						$match = substr($match, 1, strlen($match) - 2);
						$match = stripslashes($match);
						$strings[$match] = true;
					}
				}
			}

			fclose($f);
		}

		return array_keys($strings);
	}

	/**
	 * The translation strings are loaded from a php file, this function generates the file
	 * and returns the new dictionary in an array
	 * @return array
	 */
	public static function initDictionary()
	{
		$dico = array();
		$strings = self::getLanguageStrings();

		$defaultDico = array_combine($strings, $strings);

		foreach ($GLOBALS['LANGUAGES'] as $l)
		{
			$dico[$l] = $defaultDico;
			$dico['disclaimer'] = DEFAULT_DISCLAIMER;
		}

		$f = fopen(ROOT_PATH . 'includes/lang.php', 'w+');

		fwrite($f, '<?php $dico = ' . var_export($dico, true) . ';');

		fclose($f);

		return $dico;
	}

	/**
	 * recursive array_values (returns all the values of a given array, recursively)
	 * @param array $array
	 * @param array $flat
	 * @return array
	 */
	public static function array_flatten($array, $flat = false)
  {
    if (!is_array($array) || empty($array))
    	return false;

    if (empty($flat))
    	$flat = array();

    foreach ($array as $val)
    {
      if (is_array($val))
      	$flat = array_flatten($val, $flat);
      else
      	$flat[] = $val;
    }

    return $flat;
  }

  /**
   * Prints a javascript link with an icon and an onclick
   * @param string $function
   * @param string $label
   */
  public static function echoJSAction($function, $label)
  {
  	echo '<p class="linkWithArrow"><a href="javascript:;" onclick="' . $function . '">' . $label . '</a></p>';
  }

  /**
   * Prints a link with an icon
   * @param string $url
   * @param string $label
   */
  public static function echoAction($url, $label)
  {
  	echo '<p class="linkWithArrow"><a href="' . $url . '">' . $label . '</a></p>';
  }

  public static function implodeParams($params)
  {
  	$str = '';

  	foreach ($params as $key => $value)
  	{
  		$str .= " $key=\"$value\"";
  	}

  	return $str;
  }

	public static function objectsToSelect($objects, $nameField='name', $options = array())
	{
		$id = (!empty($options['id']) ? ' id="' . $options['id'] . '"' : '');
		$name = (!empty($options['name']) ? ' name="' . $options['name'] . '"' : '');
		$value = (!empty($options['value']) ? $options['value'] : false);

		$html = '<select' . $id . $name . ' class="form-control">';
		if (!empty($options['empty']))
			$html .= '<option value="">' . $options['empty'] . '</option>';

		foreach ($objects as $o)
			$html .= '<option value="' . $o->id . '"' . ($value !== false && $o->id == $value ? ' selected="selected"' : '') . '>' . $o->$nameField . '</option>';

		$html .= '</select>';

		return $html;
	}

	public static function mysqlError()
	{
		echo '<pre>' . print_r(mysql_error(), true) . '</pre>';
		echo '<pre>' . print_r(self::$lastQuery, true) . '</pre>';
		echo '<pre>';debug_print_backtrace();echo '</pre>';
	}

	public static function mysqlQuery($sql)
	{
		self::$lastQuery = $sql;
		return mysql_query($sql);
	}
}

/**
 * Define function json_decode if the json module is disabled
 */
if (!function_exists('json_decode'))
{
	function json_decode($content, $assoc=false)
	{
		if ($assoc)
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		else
			$json = new Services_JSON;
		return $json->decode($content);
	}
}

/**
 * Define function json_decode if the json module is disabled
 */
if (!function_exists('json_encode'))
{
	function json_encode($content)
	{
		$json = new Services_JSON;
		return $json->encode($content);
	}
}

function echoFieldsetStart($legend)
{
	echo '<div class="fieldset">';
	echo '<div class="legend">' . $legend . '</div>';
}

function echoFieldsetEnd()
{
	echo '</div>';
}
