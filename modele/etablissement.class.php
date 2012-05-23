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
	var $nom='';
	var $id_structure=0;	
	var $nom_structure="";	
	function Etablissement($id) {
		$this->id=$id;
	
		#on récupere le nom:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom from etablissements where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->nom=$tab['nom'];
		}
		
		#on récupere les infos de la structure:
		$sql="select t1.rowid, t1.nom from structures as t1 inner join ass_etablissement_structure as t2 on t1.rowid=t2.id_structure where t2.id_etablissement=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->nom_structure=$tab['nom'];
			$this->id_structure=$tab['rowid'];
		}
		$base->close();	
	}
	function donnees() {
		$id=$this->id;
		$donnees=array();
		#on récupere les données :
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid, nom, label, type, valeur, date from donnees_etablissement where id_etablissement=$id and valeur!='####' and id_etablissement||','||nom||','||date IN (select id_etablissement||','||nom||','||max(date) from donnees_etablissement group by id_etablissement, nom) order by nom";
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
	function aj_donnee($nom, $label, $type, $valeur){
		$nom=SQLite3::escapeString($nom);
		$label=SQLite3::escapeString($label);
		$type=SQLite3::escapeString($type);
		$valeur=SQLite3::escapeString($valeur);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into donnees_etablissement (id_utilisateur, id_etablissement, nom, label, type, valeur) values (1, ".$this->id.", '$nom', '$label', '$type', '$valeur')";
		$base->query($sql);
		$base->close();
		$this->cache();	
	}
	function suppr(){
		#on écrit les données de l'établissement dans un fichier html dans tmp/corbeille/ avant de le supprimer.
		$nom="etablissement-".filter(trim($this->nom_structure))."-".filter(trim($this->nom))."-".$this->id.".html";
		$html="<!DOCTYPE html><html><head><title>".trim($this->nom_structure)." - ".trim($this->nom)."</title><meta content='text/html; charset=UTF-8' http-equiv='Content-Type'></head><body><h1>".trim($this->nom_structure)." - ".trim($this->nom)." (date de suppression : ".date('d/m/Y H:i:s').")</h1><hr />";
		$html.=str_replace("<span class='ui-button-text'>supprimer</span>","",Html::etablissement($this->id))."<hr />";
		$html.="</body></html>";
		file_put_contents("modele/corbeille/$nom",$html);
		
		$cp=new Casquette($this->casquette_propre());
		$cpe=new Contact($cp->id_contact);
		$cpe->suppr();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from ass_etablissement_structure where id_etablissement=".$this->id;
		$base->query($sql);
		foreach($this->casquettes() as $id_casquette=>$nom_casquette){
			$sql="insert into ass_casquette_etablissement (id_casquette,id_etablissement) values ($id_casquette,0)";
			$base->query($sql);
		}	
		$sql="delete from ass_casquette_etablissement where id_etablissement=".$this->id;
		$base->query($sql);
		$sql="delete from donnees_etablissement where id_etablissement=".$this->id;
		$base->query($sql);
		$sql="delete from etablissements where rowid=".$this->id;
		$base->query($sql);
		$tab=array();
		foreach($_SESSION['selection']['etablissements'] as $id_etablissement) {
			if ($this->id!=$id_etablissement) $tab[]=$id_etablissement;
		}
		$_SESSION['selection']['etablissements']=$tab;
		
		$base->close();
		$this->de_index();		
	}
	function sup_donnee($nom){
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into donnees_etablissement (id_utilisateur, id_etablissement, nom, label, type, valeur) values (1, ".$this->id.", '$nom', '', '', '####')";
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
	function contacts(){
		$tab=array();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_c="
select t1.rowid, t1.nom from contacts as t1 
inner join ass_casquette_contact as t2 on t1.rowid=t2.id_contact
inner join casquettes as t3 on t2.id_casquette=t3.rowid
inner join ass_casquette_etablissement as t4 on t3.rowid=t4.id_casquette
where t4.id_etablissement=".$this->id." AND t4.id_casquette||','||t4.date IN (
SELECT id_casquette||','||max ( date )
FROM 'ass_casquette_etablissement'
GROUP BY id_casquette ) ";
		$res_c = $base->query($sql_c);
		while ($tab_c=$res_c->fetchArray(SQLITE3_ASSOC)) {
			if (!array_key_exists($tab_c['rowid'],$tab)){
				$tab[$tab_c['rowid']]=$tab_c['nom'];
			}
		}
		return $tab;
	}
	function casquettes(){
		$tab=array();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_c="
SELECT t3.rowid, t3.nom FROM casquettes as t3
INNER join ass_casquette_etablissement AS t4 ON t3.rowid=t4.id_casquette
WHERE t4.id_etablissement=".$this->id." AND t4.id_casquette||','||t4.date IN (
SELECT id_casquette||','||max ( date )
FROM 'ass_casquette_etablissement'
GROUP BY id_casquette ) ";
		$res_c = $base->query($sql_c);
		while ($tab_c=$res_c->fetchArray(SQLITE3_ASSOC)) {
			if (!array_key_exists($tab_c['rowid'],$tab) && $tab_c['rowid']!=$this->casquette_propre() ){
				$tab[$tab_c['rowid']]=$tab_c['nom'];
			}
		}
		return $tab;
	}
	function casquette_propre(){
		$tab=array();
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_c="
select t1.rowid as id_contact, t1.nom as nom_contact, t3.rowid as id_casquette from contacts as t1 
inner join ass_casquette_contact as t2 on t1.rowid=t2.id_contact
inner join casquettes as t3 on t2.id_casquette=t3.rowid
inner join ass_casquette_etablissement as t4 on t3.rowid=t4.id_casquette
where t4.id_etablissement=".$this->id." AND t4.id_casquette||','||t4.date IN (
SELECT id_casquette||','||max ( date )
FROM 'ass_casquette_etablissement'
GROUP BY id_casquette ) ";
		$res_c = $base->query($sql_c);
		$id=0;
		while ($tab_c=$res_c->fetchArray(SQLITE3_ASSOC)) {
			if ($tab_c['nom_contact']=='$$$$') $id=$tab_c['id_casquette'];
		}
		return $id;
	}
	function mod_nom($nom){
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update etablissements set nom='$nom' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		$id_casquette=$this->casquette_propre();
		$tri=SQLite3::escapeString($this->nom_structure);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update casquettes set tri='$tri' where rowid=$id_casquette";
		$base->query($sql);
		$base->close();
		$this->cache();	
	}
	function cache(){
		async('modele/cache/cache', array('objet'=>'Etablissement', 'id'=>$this->id));	
	}
	function index(){
		error_log(date('d/m/Y H:i:s')." - Etablissement ".$this->id.", ".$this->nom_structure.", ".$this->nom."\n", 3, "tmp/cache.log");
		$content="";
		$content.=$this->nom." ";
		$content.=$this->nom_structure." ";
		foreach ($this->donnees() as $nom=>$donnee){
			$content.=$donnee['valeur']." ";
		}
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		#on teste si le cache existe
		$sql="select count(*) from cache_etablissement where rowid=$id";
		$res = $base->query($sql);
		$n=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$content=SQLite3::escapeString(noaccent($content));
		if ($n==0) {
			#si non on crée le cache
			$sql="insert into cache_etablissement (rowid, content) values ($id, '$content')";
			$res = $base->query($sql);
		} else {
			#si oui on met à jour
			$sql="update cache_etablissement set content='$content' where rowid=$id";
			$res = $base->query($sql);
		}
		$base->close();
		foreach ($this->casquettes() as $id=>$casquette){
			$c=new Casquette($id);
			$c->index();
		}
		$c=new Casquette($this->casquette_propre());
		$c->index();
		$s=new Structure($this->id_structure);
		$s->index();
	}
	function index_existe(){
		$id=$this->id;
		#on teste si le cache existe
		$sql="select count(*) from cache_etablissement where rowid=$id";
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
		$sql="delete from cache_etablissement where rowid=$id";
		$res = $base->query($sql);
		$base->close();	
	}
}
?>
