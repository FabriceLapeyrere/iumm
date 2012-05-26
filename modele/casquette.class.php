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
	var $casquette_etab=0;
	var $nom='';
	var $categories=array();	
	var $id_etablissement=0;	
	var $id_structure=0;	
	var $nom_etablissement="";	
	var $nom_structure="";	
	var $nom_contact="";	
	var $prenom_contact="";	
	var $id_contact=0;	
	function Casquette($id) {
		$this->id=$id;
	
		#on récupere le nom:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom from casquettes where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->nom=$tab['nom'];
		}
		
		#on récupere les infos du contact:
		$sql="select t1.rowid, t1.nom, t1.prenom from contacts as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_contact where t2.id_casquette=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->nom_contact=$tab['nom'];
			$this->prenom_contact=$tab['prenom'];
			$this->id_contact=$tab['rowid'];
		}
		
		#on récupere l'etablissement :
		$sql="select t1.rowid, t1.nom, t3.rowid as id_structure, t3.nom as structure from etablissements as t1 inner join ass_casquette_etablissement as t2 on t1.rowid=t2.id_etablissement inner join ass_etablissement_structure as a on t1.rowid=a.id_etablissement inner join structures as t3 on a.id_structure=t3.rowid where t2.id_casquette=$id and t2.date IN (select max(date) from ass_casquette_etablissement group by id_casquette having id_casquette=$id)";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$rowid=$tab['rowid'];
			$this->id_etablissement=$rowid;
			$this->nom_etablissement=$tab['nom'];
			$this->id_structure=$tab['id_structure'];
			$this->nom_structure=$tab['structure'];
		}
		
		#on récupere les categories :
		$sql_cat="SELECT t1.rowid as rowid, t1.nom as nom FROM categories as t1  inner join ass_casquette_categorie as t2 on t1.rowid=t2.id_categorie WHERE t2.id_casquette=$id AND t2.id_casquette||','||t2.id_categorie||','||1||','||t2.date IN (
SELECT id_casquette||','||id_categorie||','||statut||','||max ( date )
FROM 'ass_casquette_categorie'
GROUP BY id_casquette,id_categorie)";
		$res_cat = $base->query($sql_cat);
		while ($tab_cat=$res_cat->fetchArray(SQLITE3_ASSOC)) {
			$this->categories[$tab_cat['rowid']]=array('nom'=>$tab_cat['nom']);
		}
		$base->close();
		if ($this->nom_contact=='$$$$') $this->casquette_etab=1;
	}
	function donnees() {
		$id=$this->id;
		$donnees=array();
		#on récupere les données :
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid, nom, label, type, valeur, date from donnees_casquette where id_casquette=$id and valeur!='####' and id_casquette||','||nom||','||date IN (select id_casquette||','||nom||','||max(date) from donnees_casquette group by id_casquette, nom) order by nom";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$rowid=$tab['rowid'];
			$nom=$tab['nom'];
			$label=$tab['label'];
			$type=$tab['type'];
			$valeur=$tab['valeur'];
			$date=$tab['date'];
			$donnees[$nom]=array('rowid'=>$rowid, 'label'=>$label, 'type'=>$type, 'valeur'=>$valeur, 'date'=>$date);
		}
		$base->close();
		return $donnees;
	}
	function donnees_etab() {
		$e=new etablissement($this->id_etablissement);
		return $e->donnees();
	}
	function emails() {
		$emails=array();
		$donnees=$this->donnees();
		foreach ($donnees as $nom=>$donnee) {
			if ($donnee['valeur']!="" && $donnee['type']=='email') $emails[]=$donnee['valeur'];
		}
		$email_str=0;
		if(count($emails)==0) $email_str=1;
		$donnees=$this->donnees_etab();
		foreach ($donnees as $nom=>$donnee) {
			if ($donnee['valeur']!="" && $donnee['type']=='email' && $email_str==1) $emails[]=$donnee['valeur'];
		}
		return $emails;
	}
	function adresse() {
		$adresse="";
		$adresse_complete="";
		$donnees=$this->donnees();
		if (trim($this->prenom_contact)!="" and trim($this->nom_contact)!="$$$$") $adresse_complete.=trim($this->prenom_contact)." ";
		if (trim($this->nom_contact)!="" and trim($this->nom_contact)!="$$$$") $adresse_complete.=$this->nom_contact;
		foreach ($donnees as $nom=>$donnee) {
			if ($donnee['valeur']!="" && $donnee['type']=='adresse') {
				$adresse="";
				if (trim($adresse)!="") $adresse.="\n";
				$t=json_decode($donnee['valeur']);
				foreach ($t as $cle=>$val){
					if ($val!="") {
						if ($cle!="cp") $adresse.=$val."\n";
						else $adresse.=rtrim($val)." ";
					}
				}
				$adresse=rtrim($adresse);				
			}
		}
		$donnees=$this->donnees_etab();
		foreach ($donnees as $nom=>$donnee) {
			if ($donnee['valeur']!="" && $donnee['type']=='adresse') {
				$adresse="";
				if (trim($this->nom_structure)!="") $adresse.=trim($this->nom_structure);
		   		if (trim($adresse)!="") $adresse.="\n";
				$t=json_decode($donnee['valeur']);
				foreach ($t as $cle=>$val){
					if ($val!="") {
						if ($cle!="cp") $adresse.=$val."\n";
						else $adresse.=$val." ";
					}
				}
				$adresse=rtrim($adresse);				
			}
		}
		$adresse_complete.="\n".$adresse;
		return $adresse_complete;
	}
	function ass_categorie($id_categorie){
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$id_casquette=$this->id;
		$sql="insert into ass_casquette_categorie (id_casquette, id_categorie, statut) values ($id_casquette,$id_categorie, 1)";
		$base->query($sql);
		$base->close();
		$this->cache();
	}
	function deass_categorie($id_categorie){
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$id_casquette=$this->id;
		$sql="insert into ass_casquette_categorie (id_casquette, id_categorie, statut) values ($id_casquette,$id_categorie, 0)";
		$base->query($sql);
		$base->close();
		$this->cache();
	}
	function ass_etablissement($id_etablissement){
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$id_casquette=$this->id;
		$sql="insert into ass_casquette_etablissement (id_etablissement, id_casquette) values ($id_etablissement,$id_casquette)";
		$base->query($sql);
		if ($this->casquette_etab==1){
			$e=new Etablissement($id_etablissement);
			$tri=SQLite3::escapeString($e->nom_structure);
			$sql="update casquettes set tri='$tri' where rowid=".$this->id;
			$base->query($sql);
		}
		$base->close();
		$this->cache();
	}
	function deass_etablissement(){
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$id_casquette=$this->id;
		$id_etablissement=$this->id_etablissement;
		$sql="insert into ass_casquette_etablissement (id_casquette,id_etablissement) values ($id_casquette,0)";
		$base->query($sql);
		$base->close();
		$this->cache();
	}
	function aj_donnee($nom, $label, $type, $valeur){
		$nom=SQLite3::escapeString($nom);
		$label=SQLite3::escapeString($label);
		$type=SQLite3::escapeString($type);
		$valeur=SQLite3::escapeString($valeur);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into donnees_casquette (id_utilisateur, id_casquette, nom, label, type, valeur) values (1, ".$this->id.", '$nom', '$label', '$type', '$valeur')";
		$base->query($sql);
		$base->close();		
		$this->cache();
	}
	function sup_donnee($nom){
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into donnees_casquette (id_utilisateur, id_casquette, nom, label, type, valeur) values (1, ".$this->id.", '$nom', '', '', '####')";
		$base->query($sql);
		$base->close();		
		$this->cache();
	}
	function suppr(){
		if ($this->nom_contact!='$$$$'){
			#on écrit les données de la casquette dans un fichier html dans corbeille/ avant de la supprimer.
			$nom="casquette-".filter(trim($this->nom_contact).trim($this->prenom_contact))."-".filter(trim($this->nom))."-".$this->id.".html";
			$html="<!DOCTYPE html><html><head><title>".trim($this->prenom_contact)." ".trim($this->nom_contact)." - ".trim($this->nom)."</title><meta content='text/html; charset=UTF-8' http-equiv='Content-Type'></head><body><h1>".trim($this->prenom_contact)." ".trim($this->nom_contact)." - ".trim($this->nom)." (date de suppression : ".date('d/m/Y H:i:s').")</h1><hr />";
			$html.=str_replace("<span class='ui-button-text'>supprimer</span>","",Html::casquette($this->id))."<hr />";
			$html.="</body></html>";
			file_put_contents("modele/corbeille/$nom",$html);
		}
		
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from ass_casquette_contact where id_casquette=".$this->id;
		$base->query($sql);
		$sql="delete from ass_casquette_etablissement where id_casquette=".$this->id;
		$base->query($sql);
		$sql="delete from ass_casquette_categorie where id_casquette=".$this->id;
		$base->query($sql);
		$sql="delete from donnees_casquette where id_casquette=".$this->id;
		$base->query($sql);
		$sql="update casquettes set nom='####' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		$this->de_index();		
	}
	function mod_nom($nom){
		$this->nom=$nom;
		$nom=SQLite3::escapeString($nom);
		if ($this->casquette_etab==1){
			$e=new Etablissement($this->id_etablissement);
			$tri=SQLite3::escapeString($e->nom_structure);
			$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
			$sql="update casquettes set tri='$tri' where rowid=".$this->id;
			$base->query($sql);
			$base->close();	
		} else {
			$tri=SQLite3::escapeString($this->nom_contact." ".$this->prenom_contact);
		}
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update casquettes set nom='$nom', tri='$tri' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		$this->cache();
	}
	function liste_champs(){
		$tab=array();
		foreach($this->donnees() as $nom=>$donnee){
			$tab[]=$nom;
		}
		return $tab;
	}
	function cache(){
		async('modele/cache/cache', array('objet'=>'Casquette', 'id'=>$this->id));	
	}
	function index(){
		error_log(date('d/m/Y H:i:s')." - Casquette ".$this->id.", ".$this->nom_contact." ".$this->prenom_contact.", ".$this->nom."\n", 3, "tmp/cache.log");
		$content="";
		$cp="";
		$adresse=$this->adresse();
		$email=implode(' ', $this->emails());
		if ($this->nom_contact!='$$$$') $content.=$this->nom." ";
		if ($this->nom_contact!='$$$$') $content.=$this->nom_contact." ";
		if ($this->nom_contact!='$$$$') $content.=$this->prenom_contact." ";
		$content.=$this->nom_etablissement." ";
		$content.=$this->nom_structure." ";
		foreach ($this->donnees() as $nom=>$donnee){
			if ($donnee['valeur']!="" && $donnee['type']=='adresse') {
				$t=json_decode($donnee['valeur']);
				$cp=cp($t->cp);
				$content.=$t->adresse." ".$t->cp." ".$t->ville." ".$t->pays." ";
			} else {
				$content.=$donnee['valeur']." ";
			}
		}
		foreach ($this->donnees_etab() as $nom=>$donnee){
			if ($donnee['valeur']!="" && $donnee['type']=='adresse') {
				$t=json_decode($donnee['valeur']);
				$cp=cp($t->cp);		
				$content.=$t->adresse." ".$t->cp." ".$t->ville." ".$t->pays." ";
			} else {
				$content.=$donnee['valeur']." ";
			}
		}
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		#on teste si le cache existe
		$sql="select count(*) from cache_casquette where rowid=$id";
		$res = $base->query($sql);
		$n=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$content=SQLite3::escapeString(noaccent($content));
		$cp=SQLite3::escapeString($cp);
		$adresse=SQLite3::escapeString($adresse);
		$email=SQLite3::escapeString($email);
		if ($n==0) {
			#si non on crée le cache
			$sql="insert into cache_casquette (rowid, content, cp, adresse, email) values ($id, '$content', '$cp', '$adresse', '$email')";
			$res = $base->query($sql);
		} else {
			#si oui on met à jour
			$sql="update cache_casquette set content='$content', cp='$cp', adresse='$adresse', email='$email' where rowid=$id";
			$res = $base->query($sql);
		}
		$base->close();		
		$c=new Contact($this->id_contact);
		$c->index();
	}
	function index_existe(){
		$id=$this->id;
		#on teste si le cache existe
		$sql="select count(*) from cache_casquette where rowid=$id";
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
		$sql="delete from cache_casquette where rowid=$id";
		$res = $base->query($sql);
		$base->close();	
	}
	function adresse_cache($id) {
		#on teste si le cache existe
		$sql="select adresse from cache_casquette where rowid=$id";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$adresse=$tab['adresse'];
		}
		$base->close();		
		return $adresse;
	}	
}
?>
