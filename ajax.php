<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */
$t=microtime(true);

if (!file_exists('conf/conf.php'))
	copy('conf/conf.php.dist','conf/conf.php');
if (!file_exists('conf/ldap.php'))
	copy('conf/ldap.php.dist','conf/ldap.php');
if (!file_exists('conf/mailing.php'))
	copy('conf/mailing.php.dist','conf/mailing.php');

include "conf/conf.php";
include "conf/ldap.php";
include "utils/toujours.php";

define('CLASS_MODELE_DIR', 'modele/');
define('CLASS_CACHE_DIR', 'ui/cache/');
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_MODELE_DIR.PATH_SEPARATOR.CLASS_CACHE_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

include "ctl/ldap.php";
include "ui/session.php";
include "ui/formulaires.class.php";
include "ui/html.class.php";
include "ui/js.class.php";

if (isset($_POST['action'])) $action=$_POST['action'];
if (isset($_GET['action'])) $action=$_GET['action'];
if (file_exists("ui/$action.php")) include "ui/$action.php";
$reponse['temps']=microtime(true)-$t;
echo json_encode($reponse);

?>
