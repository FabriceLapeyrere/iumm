<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe casquette permet de lire et d'écrire dans la base les données
 * concernant une casquette
 */

							
class Casquette {
	var $id=0;
	function Casquette($id) {
		$this->id=$id;
	}
	function tout() {
		$id=$this->id;
		$proprietes=array('nom', 'contact', 'etablissement', 'structure', 'categories', 'donnees', 'emails', 'adresse', 'cp', 'adr', 'ville', 'pays', 'fonction', 'donnees_etab');
		$all=array();
		foreach ($proprietes as $prop){
			$all[$prop]=$this->$prop();
		}
		$tout=$all;
		return $all;
	}
	function nom_maj() {
		$id=$this->id;
		Cache_modele::p_wlock('casquette',$id,'nom');
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom from casquettes where rowid=$id";
		$res = $base->query($sql);
		$nom="";
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom=$tab['nom'];
		}
		$base->close();
		$retour=Cache_modele::set('casquette',$id,'nom',$nom);
		Cache_modele::un_p_wlock('casquette',$id,'nom');
		return $retour;
	}
	function nom() {
		$id=$this->id;
		#on récupere le nom:
		$cache=Cache_modele::get('casquette',$id,'nom');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->nom_maj();
		}
	}
	function contact_maj() {
		$id=$this->id;
		Cache_modele::p_wlock('casquette',$id,'contact');
		#on récupere les infos du contact:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select t1.nom as nom, t1.prenom as prenom, t1.rowid as id from contacts as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_contact where t2.id_casquette=$id";
		$res = $base->query($sql);
		$contact=array('nom'=>"",'prenom'=>"",'id'=>0);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$contact=array('nom'=>$tab['nom'],'prenom'=>$tab['prenom'],'id'=>$tab['id']);
		}
		$base->close();
		$retour=Cache_modele::set('casquette',$id,'contact',$contact);
		Cache_modele::un_p_wlock('casquette',$id,'contact');
		return $retour;
	}
	function contact() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'contact');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->contact_maj();
		}
	}
	function nom_contact() {
		$id=$this->id;
		$contact=$this->contact();
		return $contact['nom'];
	}
	function prenom_contact() {
		$id=$this->id;
		$contact=$this->contact();
		return $contact['prenom'];
	}
	function id_contact() {
		$id=$this->id;
		$contact=$this->contact();
		return $contact['id'];
	}
	function etablissement_maj() {
		$id=$this->id;
		Cache_modele::p_wlock('casquette',$id,'etablissement');
		#on récupere l'etablissement :
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select t1.nom as nom, t1.rowid as id from etablissements as t1 inner join ass_casquette_etablissement as t2 on t1.rowid=t2.id_etablissement inner join ass_etablissement_structure as a on t1.rowid=a.id_etablissement inner join structures as t3 on a.id_structure=t3.rowid where t2.id_casquette=$id";
		$res = $base->query($sql);
		$etablissement=array('nom'=>"",'id'=>0);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$etablissement=array('nom'=>$tab['nom'],'id'=>$tab['id']);
		}
		$base->close();
		$retour=Cache_modele::set('casquette',$id,'etablissement',$etablissement);
		Cache_modele::un_p_wlock('casquette',$id,'etablissement');
		return $retour;
	}	
	function etablissement() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'etablissement');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->etablissement_maj();
		}
	}	
	function id_etablissement() {
		$id=$this->id;
		$etablissement=$this->etablissement();
		return $etablissement['id'];
	}	
	function nom_etablissement() {
		$id=$this->id;
		$etablissement=$this->etablissement();
		return $etablissement['nom'];
	}	
	function structure_maj() {
		$id=$this->id;
		Cache_modele::p_wlock('casquette',$id,'structure');
		#on récupere l'etablissement :
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select t3.rowid as id, t3.nom as nom from etablissements as t1 inner join ass_casquette_etablissement as t2 on t1.rowid=t2.id_etablissement inner join ass_etablissement_structure as a on t1.rowid=a.id_etablissement inner join structures as t3 on a.id_structure=t3.rowid where t2.id_casquette=$id";
		$res = $base->query($sql);
		$structure=array('nom'=>"", 'id'=>0);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$structure=array('nom'=>$tab['nom'], 'id'=>$tab['id']);
		}
		$base->close();
		$retour=Cache_modele::set('casquette',$id,'structure',$structure);
		Cache_modele::un_p_wlock('casquette',$id,'structure');
		return $retour;
	}	
	function structure() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'structure');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->structure_maj();
		}
	}	
	function id_structure() {
		$id=$this->id;
		$structure=$this->structure();
		return $structure['id'];
	}	
	function nom_structure() {
		$id=$this->id;
		$structure=$this->structure();
		return $structure['nom'];
	}	
	function categories_maj() {
		$id=$this->id;
		Cache_modele::p_wlock('casquette',$id,'categories');
		#on récupere les categories :
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_cat="SELECT id_categorie as rowid FROM ass_casquette_categorie WHERE id_casquette=$id";
		$res_cat = $base->query($sql_cat);
		$categories=array();	
		while ($tab_cat=$res_cat->fetchArray(SQLITE3_ASSOC)) {
			if ($tab_cat['rowid']>0)
				$categories[]=$tab_cat['rowid']+0;
		}
		$base->close();
		$retour=Cache_modele::set('casquette',$id,'categories',$categories);
		Cache_modele::un_p_wlock('casquette',$id,'categories');
		return $retour;
	}
	function categories() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'categories');
		if ($cache!='&&&&') {
			return $cache;
		} else {
			return $this->categories_maj();
		}
	}
	function casquette_etab() {
		$casquette_etab=0;
		if ($this->nom_contact()=='$$$$') $casquette_etab=1;
		return $casquette_etab;
	}
	function donnees_maj() {
		$id=$this->id;
		Cache_modele::p_wlock('casquette',$id,'donnees');
		#on récupere les données :
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select t1.nom as nom, t1.label as label, t1.type as type, t1.valeur as valeur, t1.date as date, t3.nom as nom_contact, t3.prenom as prenom_contact, t4.id_etablissement as id_etablissement
from donnees_casquette as t1
inner join ass_casquette_contact as t2 on t1.id_casquette=t2.id_casquette
inner join contacts as t3 on t3.rowid=t2.id_contact
left join ass_casquette_etablissement as t4 on t4.id_casquette=t1.id_casquette
where t1.id_casquette=$id and t1.actif=1 order by nom COLLATE NOCASE
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
		$fonction="";
		$id_etablissement=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nom_contact=$tab['nom_contact'];
			$prenom_contact=$tab['prenom_contact'];
			if ($tab['id_etablissement']>0) $id_etablissement=$tab['id_etablissement'];
			$donnees[$tab['nom']]=array($tab['valeur'], $tab['label'], $tab['type'], $tab['date']);
			if ($tab['valeur']!="" && $tab['type']=='email') $emails[]=$tab['valeur'];
			if ($tab['valeur']!="" && $tab['type']=='adresse') {
				$adresse_complete="";
				if (trim($prenom_contact)!="" and trim($nom_contact)!="$$$$") $adresse_complete.=trim($prenom_contact)." ";
				if (trim($prenom_contact)!="" and trim($nom_contact)=="") $adresse_complete.=" \n";
				if (trim($nom_contact)!="" and trim($nom_contact)!="$$$$") $adresse_complete.=$nom_contact;
				$adresse="";
				if (trim($adresse)!="") $adresse.="\n";
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
			if ($tab['valeur']!="" && $tab['nom']=='Fonction') $fonction=rtrim($tab['valeur']);
		}
		$base->close();
		$donnees_etab=array();
		$etablissement=$this->etablissement();
		if ($etablissement['id']>0){
			$e= new Etablissement($etablissement['id']);
			$etout=$e->tout();
			#on met les emails de la structure si vide
			if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - tout tout tout  ".var_export($etout,true)."	 \n", 3, "tmp/debug.log");
			if (count($emails)==0) {
				$emails=$etout['emails'];
			}
			#on met l'adresse de la structure si elle existe
			$adresse_etab=$etout['adresse'];
			if (trim($adresse_etab)!="") {
				$adresse="";
				$contact=$this->contact();
				$prenom_contact=$contact['prenom'];
				$nom_contact=$contact['nom'];
				$adresse.=$adresse_etab;
				$cp=$etout['cp'];
				if(trim($etout['structure']['nom'])!="")
					$adr=$etout['structure']['nom']."\n".$etout['adr'];
				else
					$adr=$etout['adr'];
				$ville=$etout['ville'];
				$pays=$etout['pays'];
			}
			$donnees_etab=$etout['donnees'];	
		}
		if ($adresse!="" && $adresse_complete=="") $adresse_complete=$adresse;
		elseif ($adresse!="" && $adresse_complete!="") $adresse_complete.="\n".$adresse;
		else $adresse_complete="";
		Cache_modele::set('casquette',$id,'cp',$cp);
		Cache_modele::set('casquette',$id,'adr',$adr);
		Cache_modele::set('casquette',$id,'ville',$ville);
		Cache_modele::set('casquette',$id,'pays',$pays);
		Cache_modele::set('casquette',$id,'adresse',$adresse_complete);
		Cache_modele::set('casquette',$id,'emails',$emails);
		Cache_modele::set('casquette',$id,'fonction',$fonction);
		Cache_modele::set('casquette',$id,'donnees_etab',$donnees_etab);
		$retour=Cache_modele::set('casquette',$id,'donnees',$donnees);
		Cache_modele::un_p_wlock('casquette',$id,'donnees');
		return $retour;
		
	}
	function donnees() {
		$id=$this->id;
		$donnees=array();
		$cache=Cache_modele::get('casquette',$id,'donnees');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->donnees_maj();
		}
	}
	function emails() {
		$id=$this->id;
		$emails=array();
		$cache=Cache_modele::get('casquette',$id,'emails');
		if ($cache!='&&&&') {
			if (!is_array($cache))
				$cache=array($cache);
			return $cache;
		} else {
			$this->donnees_maj();
			$cache=Cache_modele::get('casquette',$id,'emails');
			return $cache;
		}
	}
	function adresse() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'adresse');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('casquette',$id,'adresse');
			return $cache;
		}
	}
	function donnees_etab_maj() {
		$id=$this->id;
		$etablissement=$this->etablissement();
		if ($etablissement['id']>0){
			Cache_modele::p_wlock('casquette',$id,'donnees_etab');
			$e= new Etablissement($etablissement['id']);
			$retour=Cache_modele::set('casquette',$id,'donnees_etab',$e->donnees());
			Cache_modele::un_p_wlock('casquette',$id,'donnees_etab');
			return $retour;
		} else
			return array();
	}
	function donnees_etab() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'donnees_etab');
		if ($cache!='&&&&')
			return $cache;
		else {
			return $this->donnees_etab_maj();
		}
	}
	function cp() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'cp');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('casquette',$id,'cp');
			return $cache;
		}
	}
	function adr() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'adr');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('casquette',$id,'adr');
			return $cache;
		}
	}
	function ville() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'ville');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('casquette',$id,'ville');
			return $cache;
		}
	}
	function pays() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'pays');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('casquette',$id,'pays');
			return $cache;
		}
	}
	function fonction() {
		$id=$this->id;
		$cache=Cache_modele::get('casquette',$id,'fonction');
		if ($cache!='&&&&')
			return $cache;
		else {
			$this->donnees_maj();
			$cache=Cache_modele::get('casquette',$id,'fonction');
			if (!is_array($cache))
				$cache=array($cache);
			return $cache;
		}
	}
	function ass_categorie($id_categorie, $id_utilisateur=1){
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$id_casquette=$this->id;
		$sql="delete from ass_casquette_categorie where id_casquette=$id_casquette and id_categorie=$id_categorie;
insert into ass_casquette_categorie (id_utilisateur, id_casquette, id_categorie) values ($id_utilisateur, $id_casquette,$id_categorie);";
		$base->query($sql);
		$base->close();
		Cache_modele::del('casquette',$id,'categories');
		async('modele/index/index',array('id'=>$id));
	}
	function deass_categorie($id_categorie, $id_utilisateur=1){
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$id_casquette=$this->id;
		$sql="delete from ass_casquette_categorie where id_casquette=$id_casquette and id_categorie=$id_categorie";
		$base->query($sql);
		$base->close();
		Cache_modele::del('casquette',$id,'categories');
		async('modele/index/index',array('id'=>$id));
	}
	function ass_etablissement($id_etablissement, $id_utilisateur=1){
		$id=$this->id;
		$old_etablissement=$this->id_etablissement();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from ass_casquette_etablissement where id_casquette=$id;";
		$sql.="insert into ass_casquette_etablissement (id_utilisateur, id_etablissement, id_casquette) values ($id_utilisateur, $id_etablissement,$id);";
		$base->query($sql);
		if ($this->casquette_etab()==1){
			$e=new Etablissement($id_etablissement);
			$tri=SQLite3::escapeString($e->nom_structure());
			$sql="update casquettes set tri='$tri' where rowid=".$this->id;
			$base->query($sql);
		}
		$base->close();
		Cache_modele::del('casquette',$id,'structure, etablissement, donnees_etab, adresse, adr, cp, ville, pays');
		Cache_modele::del('etablissement',$id_etablissement,'casquettes, contacts');
		if ($old_etablissement>0) Cache_modele::del('etablissement',$old_etablissement,'casquettes, contacts');
		async('modele/index/index',array('id'=>$id));
	}
	function deass_etablissement($id_utilisateur=1){
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$id_casquette=$this->id;
		$id_etablissement=$this->id_etablissement();
		$sql="delete from ass_casquette_etablissement where id_casquette=$id_casquette";
		$base->query($sql);
		$base->close();
		Cache_modele::del('casquette',$id,'structure, etablissement, donnees_etab, adresse, adr, cp, ville, pays');
		async('modele/cache/cache',array('objet'=>'Casquette','id_objet'=>$id,'prop'=>array('structure', 'etablissement')));
		Cache_modele::del('etablissement',$id_etablissement,'casquettes, contacts');
		async('modele/cache/cache',array('objet'=>'Etablissement','id_objet'=>$id_etablissement,'prop'=>array('casquettes', 'contacts')));
		async('modele/index/index',array('id'=>$id));
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
		$sql.="update donnees_casquette set actif=0 where id_casquette=$id and nom='$nom' and actif=1;";
		$sql.="insert into donnees_casquette (id_utilisateur, id_casquette, nom, label, type, valeur,actif) values ($id_utilisateur, $id, '$nom', '$label', '$type', '$valeur',1);";
		$sql.="COMMIT;";
		$base->query($sql);
		$base->close();
		Cache_modele::del('casquette',$id,'donnees');
		if ($type=='adresse') {
			Cache_modele::del('casquette',$id,'cp, adresse, emails');
			async('modele/cache/cache',array('objet'=>'Casquette','id_objet'=>$id,'prop'=>array('donnees')));
		}
		if ($nom=='Fonction') {
			Cache_modele::del('casquette',$id,'fonction');
			async('modele/cache/cache',array('objet'=>'Casquette','id_objet'=>$id,'prop'=>array('donnees')));
		}
		async('modele/index/index',array('id'=>$id));
	}
	function sup_donnee($nom, $id_utilisateur=1){
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update donnees_casquette set actif=0 where id_casquette=$id and nom='$nom';";
		$base->query($sql);
		$base->close();		
		Cache_modele::del('casquette',$id,'cp, adresse, donnees, emails');
		async('modele/index/index',array('id'=>$id));
	}
	function suppr($id_utilisateur=1){
		$id=$this->id;
		$tout=$this->tout();
		if ($tout['contact']['nom']!='$$$$'){
			$u=new Utilisateur($id_utilisateur);
			#on écrit les données de la casquette dans un fichier html dans corbeille/ avant de la supprimer.
			$nom="casquette-".filter(trim($tout['contact']['nom']).trim($tout['contact']['prenom']))."-".filter(trim($tout['nom']))."-".$this->id.".html";
			$html="<!DOCTYPE html><html><head><title>".trim($tout['contact']['prenom'])." ".trim($tout['contact']['nom'])." - ".trim($tout['nom'])."</title><meta content='text/html; charset=UTF-8' http-equiv='Content-Type'></head><body><h1>".trim($tout['contact']['prenom'])." ".trim($tout['contact']['nom'])." - ".trim($tout['nom'])." (supprimé le ".date('d/m/Y H:i:s')." par ".$u->nom().")</h1><hr />";
			$html.=str_replace("<span class='ui-button-text'>supprimer</span>","",Html::casquette($this->id))."<hr />";
			$html.="</body></html>";
			file_put_contents("modele/corbeille/$nom",$html);
		}
		
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="BEGIN;";
		$sql.="delete from ass_casquette_contact where id_casquette=".$this->id.";";
		$sql.="delete from ass_casquette_etablissement where id_casquette=".$this->id.";";
		$sql.="delete from ass_casquette_categorie where id_casquette=".$this->id.";";
		$sql.="delete from donnees_casquette where id_casquette=".$this->id.";";
		$sql.="update casquettes set nom='####' where rowid=".$this->id.";";
		$sql.="COMMIT;";
		$base->query($sql);
		$base->close();
		Cache_modele::suppr('casquette',$id);
		Cache_modele::del('contact',$tout['contact']['id'],'casquettes');
		Cache_modele::del('etablissement',$tout['etablissement']['id'],'casquettes');
		Index::suppr($id);
	}
	function mod_nom($nom, $id_utilisateur=1){
		$id=$this->id;
		$nom=SQLite3::escapeString($nom);
		if ($this->casquette_etab()==1){
			$e=new Etablissement($this->id_etablissement());
			$tri=SQLite3::escapeString($e->nom_structure());
			$base = new SQLite3('db/contacts.sqlite');
			$base->busyTimeout (10000);
			$sql="update casquettes set tri='$tri' where rowid=".$this->id;
			$base->query($sql);
			$base->close();	
		} else {
			$tri=SQLite3::escapeString($this->nom_contact()." ".$this->prenom_contact());
		}
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update casquettes set nom='$nom', tri='$tri' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		Cache_modele::del('casquette',$id,'nom');
		async('modele/index/index',array('id'=>$id));
	}
	function liste_champs(){
		$tab=array();
		foreach($this->donnees() as $nom=>$donnee){
			$tab[]=$nom;
		}
		return $tab;
	}
}
?>
