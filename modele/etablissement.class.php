<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe etablissement permet de lire et d'écrire dans la base les données
 * concernant un etablissement
 */

							
class Etablissement {
	var $id=0;
	function Etablissement($id) {
		$this->id=$id;
	}
	function tout() {
		$id=$this->id;
		$proprietes=array('nom', 'structure', 'donnees', 'emails', 'adresse', 'cp', 'adr', 'ville', 'pays', 'contacts', 'casquettes', 'casquette_propre');
		$all=array();
		foreach ($proprietes as $prop){
			$all[$prop]=$this->$prop();
		}
		$tout=$all;
		return $all;
	}
	function nom_maj() {
		$id=$this->id;
		$nom="";
		#on récupere le nom:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom from etablissements where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom=$tab['nom'];
		}
		$base->close();	
		return Cache_modele::set('etablissement',$id,'nom',$nom);
	}	
	function nom() {
		$id=$this->id;
		$cache=Cache_modele::get('etablissement',$id,'nom');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->nom_maj();
		}
	}	
	function structure_maj() {
		$id=$this->id;
		$structure=array('nom'=>'', 'id'=>0);
		#on récupere les infos de la structure:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select t1.nom as nom, t1.rowid as id from structures as t1 inner join ass_etablissement_structure as t2 on t1.rowid=t2.id_structure where t2.id_etablissement=$id";
		$res = $base->query($sql);
		$nom_structure="";
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$structure=array('nom'=>$tab['nom'], 'id'=>$tab['id']);
		}
		$base->close();
		return Cache_modele::set('etablissement',$id,'structure',$structure);
	}
	function structure() {
		$id=$this->id;
		$cache=Cache_modele::get('etablissement',$id,'structure');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->structure_maj();
		}
	}
	function nom_structure() {
		$id=$this->id;
		$structure=$this->structure();
		return $structure['nom'];
	}	
	function id_structure() {
		$id=$this->id;
		$structure=$this->structure();
		return $structure['id'];
	}	
	function donnees_maj() {
		$id=$this->id;
		#on récupere les données :
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select t1.rowid as rowid, t1.nom as nom, t1.label as label, t1.type as type, t1.valeur as valeur, t1.date as date, t3.nom as nom_structure
from donnees_etablissement as t1
inner join ass_etablissement_structure as t2 on t1.id_etablissement=t2.id_etablissement
inner join structures as t3 on t3.rowid=t2.id_structure
where t1.id_etablissement=$id and t1.actif=1 order by nom COLLATE NOCASE
		";
		$res = $base->query($sql);
		$donnees=array();
		$emails=array();
		$adresse="";
		$cp="";
		$adr="";
		$ville="";
		$pays="";
		$adresse_complete="";
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom_structure=$tab['nom_structure'];
			$donnees[$tab['nom']]=array($tab['valeur'], $tab['label'], $tab['type'], $tab['date']);
			if ($tab['valeur']!="" && $tab['type']=='email') $emails[]=$tab['valeur'];
			if ($tab['valeur']!="" && $tab['type']=='adresse') {
				$adresse_complete="";
				if (trim($nom_structure)!="") $adresse_complete.=trim($nom_structure);
				$adresse="";
				$t=json_decode($tab['valeur']);
				foreach ($t as $cle=>$val){
					if ($val!="") {
						if ($cle!="cp") {
							$adresse.=$val."\n";
							if($cle=='adresse') $adr=$val;
							if($cle=='ville') $ville=$val;
							if($cle=='pays') $pays=$val;
						}
						else {
							$cp=$val;
							$adresse.=rtrim($val)." ";
						}
					}
				}
				$adresse=rtrim($adresse);				
			}
		}
		$base->close();
		if ($adresse!="" && $adresse_complete=="") $adresse_complete=$adresse;
		elseif ($adresse!="" && $adresse_complete!="") $adresse_complete.="\n".$adresse;
		else $adresse_complete="";
		Cache_modele::set('etablissement',$id,'cp',$cp);
		Cache_modele::set('etablissement',$id,'adr',$adr);
		Cache_modele::set('etablissement',$id,'ville',$ville);
		Cache_modele::set('etablissement',$id,'pays',$pays);
		Cache_modele::set('etablissement',$id,'adresse',$adresse_complete);
		Cache_modele::set('etablissement',$id,'emails',$emails);
		return Cache_modele::set('etablissement',$id,'donnees',$donnees);
	}
	function donnees() {
		$id=$this->id;
		$donnees=array();
		$cache=Cache_modele::get('etablissement',$id,'donnees');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->donnees_maj();
		}
	}
	function emails() {
		$id=$this->id;
		$emails=array();
		$cache=Cache_modele::get('etablissement',$id,'emails');
		if ($cache!='&&&&') {
			if (!is_array($cache))
				$cache=array($cache);
			return $cache;
		} else {
			$this->donnees_maj();
			$cache=Cache_modele::get('etablissement',$id,'emails');
			if (!is_array($cache))
				$cache=array($cache);
			return $cache;
		}
	}
	function adresse() {
		$id=$this->id;
		$adresse=array();
		$cache=Cache_modele::get('etablissement',$id,'adresse');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('etablissement',$id,'adresse');
			return $cache;
		}
	}
	function cp() {
		$id=$this->id;
		$cp="";
		$cache=Cache_modele::get('etablissement',$id,'cp');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('etablissement',$id,'cp');
			return $cache;
		}
	}
	function adr() {
		$id=$this->id;
		$cp="";
		$cache=Cache_modele::get('etablissement',$id,'adr');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('etablissement',$id,'adr');
			return $cache;
		}
	}
	function ville() {
		$id=$this->id;
		$cp="";
		$cache=Cache_modele::get('etablissement',$id,'ville');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('etablissement',$id,'ville');
			return $cache;
		}
	}
	function pays() {
		$id=$this->id;
		$cp="";
		$cache=Cache_modele::get('etablissement',$id,'pays');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('etablissement',$id,'pays');
			return $cache;
		}
	}
	function contacts_maj(){
		$id=$this->id;
		$tab=array();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_c="
select t1.rowid from contacts as t1 
inner join ass_casquette_contact as t2 on t1.rowid=t2.id_contact
inner join casquettes as t3 on t2.id_casquette=t3.rowid
inner join ass_casquette_etablissement as t4 on t3.rowid=t4.id_casquette
where t1.nom!='####' and t1.nom!='$$$$' and t4.id_etablissement=".$this->id;
		$res_c = $base->query($sql_c);
		while ($tab_c=$res_c->fetchArray(SQLITE3_ASSOC)) {
			if (!in_array($tab_c['rowid'],$tab)){
				$tab[]=$tab_c['rowid'];
			}
		}
		$base->close();
		return Cache_modele::set('etablissement',$id,'contacts',$tab);
	}
	function contacts(){
		$id=$this->id;
		$cache=Cache_modele::get('etablissement',$id,'contacts');
		if ($cache!='&&&&') {
			return $cache;	
		} else {
			$contacts=$this->contacts_maj();
			return $contacts;
		}		
	}
	function casquettes_maj(){
		$id=$this->id;
		$tab=array();
		$id_propre=$this->casquette_propre();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_c="
select t3.rowid as id_casquette from contacts as t1 
inner join ass_casquette_contact as t2 on t1.rowid=t2.id_contact
inner join casquettes as t3 on t2.id_casquette=t3.rowid
inner join ass_casquette_etablissement as t4 on t3.rowid=t4.id_casquette
where t1.nom!='####' and t1.nom!='$$$$' and t4.id_etablissement=".$this->id;
		$res_c = $base->query($sql_c);
		while ($tab_c=$res_c->fetchArray(SQLITE3_ASSOC)) {
			if (!in_array($tab_c['id_casquette'],$tab) && $tab_c['id_casquette']!=$id_propre ){
				$tab[]=$tab_c['id_casquette'];
			}
		}
		$base->close();		
		return Cache_modele::set('etablissement',$id,'casquettes',$tab);
	}
	function casquettes(){
		$id=$this->id;
		$cache=Cache_modele::get('etablissement',$id,'casquettes');
		if ($cache!='&&&&') {
			if (!is_array($cache))	
				$cache=array($cache);
			return $cache;
		} else {
			$casquettes=$this->casquettes_maj();
			if (!is_array($casquettes))	
				$casquettes=array($casquettes);
			return $casquettes;
		}
	}
	function casquette_propre_maj(){
		$id=$this->id;
		$tab=array();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_c="
select t3.rowid as id_casquette from contacts as t1 
inner join ass_casquette_contact as t2 on t1.rowid=t2.id_contact
inner join casquettes as t3 on t2.id_casquette=t3.rowid
inner join ass_casquette_etablissement as t4 on t3.rowid=t4.id_casquette
where t1.nom='$$$$' and t4.id_etablissement=".$this->id;
		$res_c = $base->query($sql_c);
		$id_propre=0;
		while ($tab_c=$res_c->fetchArray(SQLITE3_ASSOC)) {
			$id_propre=$tab_c['id_casquette'];
		}
		$base->close();		
		return Cache_modele::set('etablissement',$id,'casquette_propre',$id_propre);
	}
	function casquette_propre(){
		$id=$this->id;
		$cache=Cache_modele::get('etablissement',$id,'casquette_propre');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->casquette_propre_maj();
		}
	}
	function aj_donnee($nom, $label, $type, $valeur, $id_utilisateur=1){
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		$label=SQLite3::escapeString($label);
		$type=SQLite3::escapeString($type);
		$valeur=SQLite3::escapeString($valeur);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="BEGIN;";
		$sql.="update donnees_etablissement set actif=0 where id_etablissement=$id and nom='$nom';";
		$sql.="insert into donnees_etablissement (id_utilisateur, id_etablissement, nom, label, type, valeur,actif) values ($id_utilisateur, $id, '$nom', '$label', '$type', '$valeur',1);";
		$sql.="COMMIT;";
		$base->query($sql);
		$base->close();		
		Cache_modele::del('etablissement',$id,'donnees');
		$id_propre=$this->casquette_propre();
		if ($type=='adresse') {
			Cache_modele::del('casquette',$id_propre,'cp, adresse');
		}
		async('modele/cache/cache',array('objet'=>'Etablissement','id_objet'=>$id,'prop'=>array('donnees')));
		$id_propre=$this->casquette_propre();
		Cache_modele::del('casquette',$id_propre,'donnees_etab');
		Cache_modele::del('casquette',$id_propre,'donnees');
		Cache_modele::del('casquette',$id_propre,'adresse');
		async('modele/cache/cache',array('objet'=>'Casquette','id_objet'=>$id_propre,'prop'=>array('donnees')));
		async('modele/index/index',array('id'=>$id_propre));
		foreach($this->casquettes() as $id_casquette){
			Cache_modele::del('casquette',$id_casquette,'donnees_etab');
			Cache_modele::del('casquette',$id_casquette,'donnees');
			Cache_modele::del('casquette',$id_casquette,'adresse');
			async('modele/cache/cache',array('objet'=>'Casquette','id_objet'=>$id_casquette,'prop'=>array('donnees')));
			async('modele/index/index',array('id'=>$id_casquette));
		}
	}
	function suppr($id_utilisateur=1){
		$id=$this->id;
		#on écrit les données de l'établissement dans un fichier html dans tmp/corbeille/ avant de le supprimer.
		$u=new Utilisateur($id_utilisateur);
		$nom="etablissement-".filter(trim($this->nom_structure()))."-".filter(trim($this->nom()))."-".$this->id.".html";
		$html="<!DOCTYPE html><html><head><title>".trim($this->nom_structure())." - ".trim($this->nom())."</title><meta content='text/html; charset=UTF-8' http-equiv='Content-Type'></head><body><h1>".trim($this->nom_structure())." - ".trim($this->nom())." (supprimé le ".date('d/m/Y H:i:s')." par ".$u->nom().")</h1><hr />";
		$html.=str_replace("<span class='ui-button-text'>supprimer</span>","",Html::etablissement($this->id))."<hr />";
		$html.="</body></html>";
		file_put_contents("modele/corbeille/$nom",$html);
		
		$cp=new Casquette($this->casquette_propre());
		$cpe=new Contact($cp->id_contact());
		$cpe->suppr();
		$casquettes=$this->casquettes();
		$id_structure=$this->id_structure();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from ass_etablissement_structure where id_etablissement=".$this->id;
		$base->query($sql);
		$sql="delete from ass_casquette_etablissement where id_etablissement=".$this->id;
		$base->query($sql);
		$sql="delete from donnees_etablissement where id_etablissement=".$this->id;
		$base->query($sql);
		$sql="delete from etablissements where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		$tab=array();
		foreach($_SESSION['selection']['etablissements'] as $id_etablissement) {
			if ($this->id!=$id_etablissement) $tab[]=$id_etablissement;
		}
		$_SESSION['selection']['etablissements']=$tab;
		Cache_modele::suppr('etablissement',$id);
		Cache_modele::del('structure',$id_structure,'etablissements');
		foreach($casquettes as $id_casquette) {
			Cache_modele::suppr('casquette',$id_casquette);
			async('modele/index/index',array('id'=>$id_casquette));
		}		
	}
	function sup_donnee($nom, $id_utilisateur=1){
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from donnees_etablissement where id_etablissement=".$this->id." and nom='$nom'";
		$base->query($sql);
		$base->close();	
		$id_propre=$this->casquette_propre();	
		Cache_modele::del('etablissement',$id,'donnees');
		Cache_modele::del('casquette',$id_propre,'donnees_etab');
		async('modele/index/index',array('id'=>$id_propre));
	}
	function liste_champs(){
		$tab=array();
		foreach($this->donnees() as $nom=>$donnee){
			$tab[]=$nom;
		}
		return $tab;
	}
	function mod_nom($nom, $id_utilisateur=1){
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update etablissements set nom='$nom' where rowid=$id";
		$base->query($sql);
		$base->close();
		$id_casquette=$this->casquette_propre();
		$tri=SQLite3::escapeString($this->nom_structure());
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update casquettes set tri='$tri' where rowid=$id_casquette";
		$base->query($sql);
		$base->close();
		Cache_modele::del('etablissement',$id,'nom');
		Cache_modele::del('casquette',$this->casquette_propre(),'etablissement');
		foreach ($this->casquettes() as $id_casquette)
			if ($id_casquette>0)
				Cache_modele::del('casquette',$id_casquette,'etablissement');
	}
}
?>
