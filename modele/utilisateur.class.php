<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe casquette permet de lire et d'écrire dans la base les données
 * concernant une casquette
 */

							
class Utilisateur {
	var $id=0;
	function Utilisateur($id) {
		$this->id=$id;
	}
	function nom() {
		$id=$this->id;
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom from utilisateurs where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom=$tab['nom'];
		}
		$base->close();
		return $nom;
	}
	function login() {
		$id=$this->id;
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select login from utilisateurs where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$login=$tab['login'];
		}
		$base->close();
		return $login;
	}
	function droits() {
		$id=$this->id;
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select droits from utilisateurs where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$droits=$tab['droits'];
		}
		$base->close();
		return $droits;
	}
	function mod_mdp($mdp) {
		$id=$this->id;
		$mdp=SQLite3::escapeString($mdp);
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="update utilisateurs set mdp='$mdp' where rowid=$id";
		$base->query($sql);
		$base->close();
	}
	function mod_login($login) {
		$id=$this->id;
		$mdp=SQLite3::escapeString($login);
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="update utilisateurs set login='$login' where rowid=$id";
		$base->query($sql);
		$base->close();
	}
	function mod_nom($nom) {
		$id=$this->id;
		$mdp=SQLite3::escapeString($nom);
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="update utilisateurs set nom='$nom' where rowid=$id";
		$base->query($sql);
		$base->close();
	}
	function mod_droits($droits) {
		$id=$this->id;
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="update utilisateurs set droits=$droits where rowid=$id";
		$base->query($sql);
		$base->close();
	}
	function suppr() {
		$id=$this->id;
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from utilisateurs where rowid=$id";
		$base->query($sql);
		$base->close();
	}
}
?>
