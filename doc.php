<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */

include "ui/session.php";

define('CLASS_DIR', 'modele/');
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

include "utils/toujours.php";
include "ui/html.class.php";
include "ui/js.class.php";

if(isset($_GET['t'])) $type=$_GET['t'];
if(isset($_POST['t'])) $type=$_POST['t'];

include "ctl/doc/$type.php";
?>
