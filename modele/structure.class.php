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
	function Structure($id) {
		$this->id=$id;
	}	
	function tout() {
		$id=$this->id;
		$proprietes=array('nom', 'etablissements');
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
		$sql="select nom from structures where rowid=$id";
		error_log(date('d/m/Y H:i:s')." - $sql, $id \n", 3, "tmp/fab.log");
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom=$tab['nom'];
		}
		$base->close();	
		return Cache_modele::set('structure',$id,'nom',$nom);
	}
	function nom() {
		$id=$this->id;
		$cache=Cache_modele::get('structure',$id,'nom');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->nom_maj();
		}
	}
	function etablissements_maj() {
		$id=$this->id;
		$etablissements=array();
		#on récupere les etablissements :
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select t1.rowid from etablissements as t1 inner join ass_etablissement_structure as t2 on t1.rowid=t2.id_etablissement where t2.id_structure=$id order by nom COLLATE NOCASE";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$etablissements[]=$tab['rowid'];
		}
		$base->close();
		return Cache_modele::set('structure',$id,'etablissements',$etablissements);	
	}
	function etablissements() {
		$id=$this->id;
		$cache=Cache_modele::get('structure',$id,'etablissements');
		if ($cache!='&&&&') {
			return $cache;
		} else {
			return $this->etablissements_maj();
		}
	}
	function mod_nom($nom, $id_utilisateur=1){
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update structures set nom='$nom' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		foreach($this->etablissements() as $id_etablissement){
			$e=new Etablissement($id_etablissement);
			$id_casquette=$e->casquette_propre();
			$tri=SQLite3::escapeString($nom);
			$base = new SQLite3('db/contacts.sqlite');
			$base->busyTimeout (10000);
			$sql="update casquettes set tri='$tri' where rowid=$id_casquette";
			$base->query($sql);
			$base->close();
			Cache_modele::del('etablissement',$id_etablissement,'structure');		
		}	
		Cache_modele::del('structure',$id,'nom');		
		foreach ($this->etablissements() as $id_etablissement) {
			Cache_modele::del('etablissement',$id_etablissement,'structure');
			Cache_modele::del('etablissement',$id_etablissement,'donnees');
			Cache_modele::del('etablissement',$id_etablissement,'adresse');
			$e=new Etablissement($id_etablissement);
			$id_propre=$e->casquette_propre();
			Cache_modele::del('casquette',$id_propre,'structure');
			Cache_modele::del('casquette',$id_propre,'donnees');
			Cache_modele::del('casquette',$id_propre,'adresse');
			async('modele/cache/cache',array('objet'=>'Casquette','id_objet'=>$id_propre,'prop'=>array('structure')));
			async('modele/index/index',array('id'=>$id_propre));
			$c=new Casquette($id_propre);
			$c->nom_structure();
			foreach ($e->casquettes() as $id_casquette) {
				Cache_modele::del('casquette',$id_casquette,'structure');
				async('modele/cache/cache',array('objet'=>'Casquette','id_objet'=>$id_casquette,'prop'=>array('structure')));
				async('modele/index/index',array('id'=>$id_casquette));
			}
		}
	}
	function aj_etablissement($nom, $id_utilisateur=1) {
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		$id_structure=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into etablissements (id_utilisateur, nom) values ($id_utilisateur, '$nom')";
		$base->query($sql);
		$id_etablissement=$base->lastInsertRowID();
		$sql="insert into ass_etablissement_structure (id_utilisateur, id_etablissement, id_structure) values ($id_utilisateur, $id_etablissement, $id_structure)";
		$res = $base->query($sql);
		$base->close();
		Cache_modele::del('structure',$id,'etablissements');		
		async('modele/cache/cache',array('objet'=>'Structure','id_objet'=>$id_structure,'prop'=>array('etablissements')));
		async('modele/cache/cache',array('objet'=>'Etablissement','id_objet'=>$id_etablissement,'prop'=>array('nom','structure')));
		$e=new Etablissement($id_etablissement);
		#on ajoute la casquette propre à l'établissement
		$id_contact=Contacts::aj_contact('$$$$','');
		$c=new Contact($id_contact);
		$casquettes=$c->casquettes();
		foreach($casquettes as $id_cas) $id_casquette=$id_cas;
		$cas=new Casquette($id_casquette);
		$cas->ass_etablissement($id_etablissement);
		async('modele/index/index',array('id'=>$id_casquette));
		return $id_etablissement;
	}
	function suppr($id_utilisateur=1){
		$id=$this->id;
		foreach($this->etablissements() as $id_etablissement){
			$e=new Etablissement($id_etablissement);
			$e->suppr($id_utilisateur);
		}
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update structures set nom='####' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		Cache_modele::suppr('structure',$id);		
	}
}
?>
