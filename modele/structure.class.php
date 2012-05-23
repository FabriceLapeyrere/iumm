<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe casquette permet de lire et d'écrire dans la base les données
 * concernant une casquette
 */

							
class Structure {
	var $id=0;
	var $nom='';
	var $etablissements=array();	
	function Structure($id) {
		$this->id=$id;
	
		#on récupere le nom:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom from structures where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->nom=$tab['nom'];
		}
		
		#on récupere les etablissements :
		$sql="select t1.rowid, t1.nom from etablissements as t1 inner join ass_etablissement_structure as t2 on t1.rowid=t2.id_etablissement where t2.id_structure=$id order by nom";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$rowid=$tab['rowid'];
			$nom=$tab['nom'];
			$this->etablissements[$rowid]=array('nom'=>$nom);
		}
		$base->close();		
	}
	function mod_nom($nom){
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update structures set nom='$nom' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		foreach($this->etablissements as $id=>$etablissement){
			$e=new Etablissement($id);
			$id_casquette=$e->casquette_propre();
			$tri=SQLite3::escapeString($nom);
			$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
			$sql="update casquettes set tri='$tri' where rowid=$id_casquette";
			$base->query($sql);
			$base->close();
			$e->cache();
		}	
	}
	function aj_etablissement($nom) {
		$nom=SQLite3::escapeString($nom);
		$id_structure=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into etablissements (nom) values ('$nom')";
		$base->query($sql);
		$id_etablissement=$base->lastInsertRowID();
		$sql="insert into ass_etablissement_structure (id_etablissement, id_structure) values ($id_etablissement,$id_structure)";
		$res = $base->query($sql);
		$base->close();
		#on met à jour les etablissements :
		$this->etablissements[$id_etablissement]=array('nom'=>$nom);
		$e=new Etablissement($id_etablissement);
		#on ajoute la casquette propre à l'établissement
		$id_contact=Contacts::aj_contact('$$$$','');
		$c=new Contact($id_contact);
		$casquettes=$c->casquettes;
		foreach($c->casquettes as $id=>$casquette) $id_casquette=$id;
		$cas=new Casquette($id_casquette);
		$cas->ass_etablissement($id_etablissement);
		$e->aj_donnee('Telephone_fixe','Téléphone fixe','telephone','');
		$e->aj_donnee('Email','E-mail','email','');
		$e->aj_donnee('Adresse','Adresse','adresse','');
		return $id_etablissement;
	}
	function suppr(){
		foreach($this->etablissements as $id_etablissement=>$etablissement){
			$e=new Etablissement($id_etablissement);
			$e->suppr();
		}
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update structures set nom='####' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		$this->de_index();
	}
	function cache(){
		async('modele/cache/cache', array('objet'=>'Structure', 'id'=>$this->id));	
	}
	function index(){
		error_log(date('d/m/Y H:i:s')." - Structure ".$this->id.", ".$this->nom."\n", 3, "tmp/cache.log");
		$content="";
		foreach ($this->etablissements as $id=>$etablissement){
			$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
			$sql="select content from cache_etablissement where rowid=$id";
			$res = $base->query($sql);
			while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
				$content.=$tab['content']." ";
			}
			$base->close();		
		}
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		#on teste si le cache existe
		$sql="select count(*) from cache_structure where rowid=$id";
		$res = $base->query($sql);
		$n=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$content=SQLite3::escapeString(noaccent($content));
		if ($n==0) {
			#si non on crée le cache
			$sql="insert into cache_structure (rowid, content) values ($id, '$content')";
			$res = $base->query($sql);
		} else {
			#si oui on met à jour
			$sql="update cache_structure set content='$content' where rowid=$id";
			$res = $base->query($sql);
		}
		$base->close();		
	}
	function index_existe(){
		$id=$this->id;
		#on teste si le cache existe
		$sql="select count(*) from cache_structure where rowid=$id";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		$n=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$base->close();		
		return $n;	
	}
	function de_index(){
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from cache_structure where rowid=$id";
		$res = $base->query($sql);
		$base->close();	
	}	
}
?>
