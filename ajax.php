<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */
$t=microtime(true);
include "ctl/dbs.php";
include "ui/session.php";

include "utils/toujours.php";
define('CLASS_MODELE_DIR', 'modele/');
define('CLASS_CACHE_DIR', 'ui/cache/');
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_MODELE_DIR.PATH_SEPARATOR.CLASS_CACHE_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

include "conf/conf.php";
include "ui/formulaires.class.php";
include "ui/html.class.php";
include "ui/js.class.php";

if (isset($_POST['action'])) $action=$_POST['action'];
if (isset($_GET['action'])) $action=$_GET['action'];
if (file_exists("ui/$action.php")) include "ui/$action.php";
$reponse['temps']=microtime(true)-$t;
echo json_encode($reponse);

?>
