<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe casquette permet de lire et d'écrire dans la base les données
 * concernant une casquette
 */

							
class Contact {
	var $id=0;
	function Contact($id) {
		$this->id=$id;
	}
	function tout() {
		$id=$this->id;
		$proprietes=array('nom', 'prenom', 'casquettes');
		$all=array();
		foreach ($proprietes as $prop){
			$all[$prop]=$this->$prop();
		}
		$tout=$all;
		return $all;
	}
	function nom_maj() {
		$id=$this->id;
		#on récupere le nom:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom, prenom from contacts where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom=$tab['nom'];
		}
		$base->close();
		return Cache_modele::set('contact',$id,'nom',$nom);
	}
	function nom() {
		$id=$this->id;
		$cache=Cache_modele::get('contact',$id,'nom');
		if ($cache!='&&&&') {
			return $cache;
		} else {
			return $this->nom_maj();	
		}
	}
	function prenom_maj() {
		$id=$this->id;
		#on récupere le nom:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select prenom from contacts where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$prenom=$tab['prenom'];
		}
		$base->close();
		Cache_modele::set('contact',$id,'prenom',$prenom);
		return $prenom;
	}
	function prenom() {
		$id=$this->id;
		$cache=Cache_modele::get('contact',$id,'prenom');
		if ($cache!='&&&&') {
			return $cache;
		} else {
			return $this->prenom_maj();	
		}
	}
	function casquettes_maj() {
		$id=$this->id;
		
			$casquettes=array();
			#on récupere les casquettes :
			$base = new SQLite3('db/contacts.sqlite');
			$base->busyTimeout (10000);
			$sql="select t1.rowid from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette where t1.nom!='####' and t2.id_contact=$id order by nom";
			$res = $base->query($sql);
			while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
				$casquettes[]=$tab['rowid'];
			}
			$base->close();
			return 	Cache_modele::set('contact',$id,'casquettes',$casquettes);
	}
	function casquettes() {
		$id=$this->id;
		$cache=Cache_modele::get('contact',$id,'casquettes');
		if ($cache!='&&&&') {
			if (!is_array($cache))	
				$cache=array($cache);
			return $cache;
		} else {
			return $this->casquettes_maj();	
		}
	}
	function mod_nom($nom, $prenom, $id_utilisateur=1){
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		$prenom=SQLite3::escapeString($prenom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update contacts set nom='$nom', prenom='$prenom' where rowid=".$this->id;
		$base->query($sql);
		foreach($this->casquettes() as $id_casquette){
			$tri=SQLite3::escapeString($nom." ".$prenom);
			$sql="update casquettes set tri='$tri' where rowid=$id_casquette";
			$base->query($sql);
		}
		$base->close();		
		Cache_modele::del('contact',$id,'nom, prenom');
		foreach ($this->casquettes() as $id_casquette) {
			Cache_modele::del('casquette',$id_casquette,'contact');
			async('modele/cache/cache',array('objet'=>'Casquette','id_objet'=>$id_casquette,'prop'=>array('contact')));
			async('modele/index/index',array('id'=>$id_casquette));
		}
	}
	function aj_casquette($nom, $id_utilisateur=1) {
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		$tri=SQLite3::escapeString($this->nom()." ".$this->prenom());
		$id_contact=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into casquettes (id_utilisateur, nom, tri) values ($id_utilisateur, '$nom', '$tri')";
		$base->query($sql);
		$id_casquette=$base->lastInsertRowID();
		$sql="insert into ass_casquette_contact (id_utilisateur, id_casquette, id_contact) values ($id_utilisateur, $id_casquette,$id_contact)";
		$res = $base->query($sql);
		$base->close();
		$c=new Casquette($id_casquette);
		Cache_modele::del('contact',$id,'casquettes');
		Index::init($id_casquette);
		return $id_casquette;
	}
	function suppr($id_utilisateur=1){
		$id=$this->id;
		foreach($this->casquettes() as $id_casquette){
			$c=new Casquette($id_casquette);
			$c->suppr($id_utilisateur);
		}
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update contacts set nom='####', prenom='####' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		Cache_modele::suppr('contact',$id);
	}
}
?>
