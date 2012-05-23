<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */

while(ob_get_level()) ob_end_clean();
header('Connection: close');
ignore_user_abort();
ob_start();
$size = ob_get_length();
header("Content-Length: $size");
ob_end_flush();
flush();

define('CLASS_MODELE_DIR', 'modele/');
define('CLASS_CACHE_DIR', 'ui/cache/');
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_MODELE_DIR.PATH_SEPARATOR.CLASS_CACHE_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

include "utils/toujours.php";
include "ui/html.class.php";
include "ui/js.class.php";
if (isset($_POST['cle'])) $cle=$_POST['cle'];
if (isset($_POST['action'])) $action=$_POST['action'];
if ($cle=="1A2Z3E4R5T6Y")
	include "$action.php";
?>
