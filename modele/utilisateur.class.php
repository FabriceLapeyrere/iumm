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
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom from utilisateurs where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom=$tab['nom'];
		}
		$base->close();
		return $nom;
	}
	function mdp() {
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select mdp from utilisateurs where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$mdp=$tab['nom'];
		}
		$base->close();
		return $nom;
	}
}
?>
