<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe utilisateurs permet de lire et d'écrire dans la base les données
 * concernant les utilisateurs
 */

							
class Utilisateurs {
	function tous() {
		$id=$this->id;
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select * from utilisateurs";
		$res = $base->query($sql);
		$utilisateurs=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$utilisateurs[$tab['rowid']]=$tab;
		}
		$base->close();
		return $utilisateurs;
	}
	function ok($login,$mdp) {
		$login=SQLite3::escapeString($login);
		$mdp=SQLite3::escapeString($mdp);
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select * from utilisateurs where login='$login' and mdp='$mdp'";
		error_log(date('d/m/Y H:i:s')." - $sql \n", 3, "tmp/auth.log");
		$res = $base->query($sql);
		$utilisateurs=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$utilisateurs[]=$tab;
		}
		$base->close();
		error_log(date('d/m/Y H:i:s')." - tentative pour $login : ".count($utilisateurs)."\n", 3, "tmp/auth.log");
		return count($utilisateurs)==1 ? $utilisateurs[0]['rowid'] : 0;
	}
}
?>
