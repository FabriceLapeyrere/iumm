<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe categorie permet de lire et d'écrire dans la base les données
 */

class Categories {
	var $nbcategories=0;
	function Categories() {
		#on récupere le nombre de contacts:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from categories$cond";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->nbcontacts=$tab['count(*)'];
		}
	}
	function aj_categorie($nom, $id_utilisateur=1) {
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into categories (id_utilisateur,nom,idparent) values ($id_utilisateur, '$nom', 0)";
		$base->query($sql);
		$sql="select max(rowid) from categories";
		$res = $base->query($sql);
		$rowid;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$rowid=$tab['max(rowid)'];
		}
		$base->close();
		return $rowid;
	}
	
	public function casquettes_arbre($id)
        {
		$cat= new Categorie($id);
		$c=$cat->casquettes();
		$casquettes=array();
		foreach($c as $idc=>$casquette) $casquettes[]=$idc;
		error_log(date('d/m/Y H:i:s')." - total catégorie $id Parent : ".implode(', ',$casquettes)."\n", 3, "tmp/debug.log");
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_enfants="select rowid, nom, idparent from categories where idparent=$id and nom!='####' order by nom";
		$enfants=array();
		if ($res_enfants = $base->query($sql_enfants)){
			while ($tab_enfants=$res_enfants->fetchArray(SQLITE3_ASSOC)) {
				$enfants[]=$tab_enfants['rowid'];
			}
		}
		$base->close();
		foreach($enfants as $id_enfant) {
			$cas_enfant=Categories::casquettes_arbre($id_enfant);
			error_log(date('d/m/Y H:i:s')." - total catégorie $id Enfant $id_enfant : ".implode(', ',$cas_enfant)."\n", 3, "tmp/debug.log");
			$casquettes=array_merge($casquettes,$cas_enfant);
		}
		$casquettes=array_unique($casquettes);
		error_log(date('d/m/Y H:i:s')." - total catégorie $id : ".implode(', ',$casquettes)."\n", 3, "tmp/debug.log");
		return $casquettes;	
	}	
	public function total($id)
        {
		return count(Categories::casquettes_arbre($id));
	}
}
?>
