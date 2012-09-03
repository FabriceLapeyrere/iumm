<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
if (isset($_POST['login']) AND isset($_POST['passwd'])) {
	$mdp=crypt($_POST['passwd'],"keller");
	$id=Utilisateurs::ok($_POST['login'],$mdp);
	if ($id>0) {
		$u=new Utilisateur($id);
		$_SESSION['user']['nom']=$u->nom();
		$_SESSION['user']['droits']=$u->droits();
		$_SESSION['user']['id']=$id;
	}
}
if (!isset($_SESSION['user'])) {
	include "ui/login.php";
	exit;
}

if (isset($_GET['deconnecte'])) {
	session_destroy();
	include "ui/login.php";
	exit;
}
?>
