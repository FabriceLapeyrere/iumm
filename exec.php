<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */
define('CLASS_MODELE_DIR', 'modele/');
define('CLASS_CACHE_DIR', 'ui/cache/');
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_MODELE_DIR.PATH_SEPARATOR.CLASS_CACHE_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

include "conf/conf.php";
include "utils/toujours.php";
include "ui/html.class.php";
include "ui/js.class.php";

if (PHP_SAPI === 'cli')
{
 

	$action=$argv[1];

	include "ctl/exec/cli/$action.php";

}
else {
	if(isset($_GET['a'])) $action=$_GET['a'];
	if(isset($_POST['a'])) $action=$_POST['a'];
	include "ctl/exec/$action.php";
}
?>
