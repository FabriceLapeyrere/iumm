<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe casquette permet de lire et d'écrire dans la base les données
 * concernant une casquette
 */

							
class Categorie {
	var $id=0;
	function Categorie($id) {
		$this->id=$id;
	}
	function nom() {
		$id=$this->id;
		#on récupere le nom et l'id_parent:
		$nom="";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom, idparent from categories where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom=$tab['nom'];
		}
		$base->close();		
		return $nom;
	}
	function id_parent() {
		$id=$this->id;
		#on récupere le nom et l'id_parent:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom, idparent from categories where rowid=$id";
		$res = $base->query($sql);
		$id_parent=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$id_parent=$tab['idparent'];
		}
		$base->close();
		return $id_parent;
	}
	function mod_nom($nom, $id_utilisateur=1){
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update categories set nom='$nom' where rowid=".$this->id;
		$base->query($sql);
		$base->close();		
	}
	function mod_parent($idparent, $id_utilisateur=1){
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update categories set idparent=$idparent where rowid=".$this->id;
		$base->query($sql);
		$base->close();		
	}
	public function nbincat()
        {
		return count($this->casquettes());
	}
	public function total()
        {
		$total=Categories::total($this->id);
		return $total;
	}
	public function casquettes()
        {
		$casquettes=array();
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="SELECT t3.rowid, t3.nom FROM casquettes as t3
INNER join ass_casquette_categorie AS t4 ON t3.rowid=t4.id_casquette
WHERE t3.nom!='####' and t4.id_categorie=".$this->id;	
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$rowid=$tab['rowid'];
			$casquettes[]=$rowid;
		}
		$base->close();
		return $casquettes;
	}
	public function enfants()
        {
		$categories=array();
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_enfants="select rowid, nom, idparent from categories where idparent=$id and nom!='####' order by nom COLLATE NOCASE";
		if ($res_enfants = $base->query($sql_enfants)){
			while ($tab_enfants=$res_enfants->fetchArray(SQLITE3_ASSOC)) {
				$categories[$tab_enfants['rowid']]=$tab_enfants['nom'];
			}
		}
		$base->close();
		return $categories;
	}
	public function nb_enfants()
        {
		$id=$this->id;
		$nb=0;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from categories where idparent=$id and nom!='####'";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		$base->close();
		return $nb;
	}
	function suppr($id_utilisateur=1){
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from ass_casquette_categorie where id_categorie=".$this->id;
		$base->query($sql);
		$sql="update categories set nom='####' where rowid=".$this->id;
		$base->query($sql);
		$tab=array();
		foreach($_SESSION['selection']['categories'] as $id_categorie) {
			if ($this->id!=$id_categorie) $tab[]=$id_categorie;
		}
		$_SESSION['selection']['categories']=$tab;
		$base->close();		
	}
}
	
?>
