<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe contacts permet de lire et d'écrire dans la base les données
 */

class Index {
	
	function init($id) {
		$base = new SQLite3('db/index.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into indexes (rowid) values ($id);";
		$base->exec($sql);
		$base->close();
	}
	function suppr($id) {
		$base = new SQLite3('db/index.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from indexes where rowid=$id;";
		$base->exec($sql);
		$base->close();
	}
	function update($id) {
		$c=new Casquette($id);
		$tout=$c->tout();
		#tri
		$tri="";
		if($tout['contact']['nom']=='$$$$')
			$tri=strtolower(noaccent($tout['structure']['nom']));
		else
			if ($tout['contact']['prenom']=="")
				$tri=strtolower(noaccent($tout['contact']['nom']));
			else
				$tri=strtolower(noaccent($tout['contact']['nom']." ".$tout['contact']['prenom']));
		$tri=SQLite3::escapeString($tri);
		
		#texte
		$texte="";
		if($tout['contact']['nom']!='$$$$') {
			$texte.=noaccent($tout['contact']['nom'])." ";
			$texte.=noaccent($tout['contact']['prenom'])." ";
		}
		$texte.=noaccent($tout['structure']['nom'])." ";
		foreach ($tout['donnees'] as $donnee){
			$search=noaccent($donnee[0]);
			$search=str_replace('{"adresse":"',' ',$search);
			$search=str_replace('","cp":"',' ',$search);
			$search=str_replace('","ville":"',' ',$search);
			$search=str_replace('","pays":"',' ',$search);
			$search=str_replace('"}',' ',$search);
			$texte.=$search." ";
		}
		foreach ($tout['donnees_etab'] as $donnee){
			$search=noaccent($donnee[0]);
			$search=str_replace('{"adresse":"',' ',$search);
			$search=str_replace('","cp":"',' ',$search);
			$search=str_replace('","ville":"',' ',$search);
			$search=str_replace('","pays":"',' ',$search);
			$search=str_replace('"}',' ',$search);
			$texte.=$search." ";
		}
		$texte=SQLite3::escapeString($texte);
		
		#dept
		$dept="";
		$dept=floor(intval(str_replace(' ','',$tout['cp']))/1000);
		if ($dept==20)
			if (intval($tout['cp'])%1000>=200)
				$dept='2b';
			else $dept='2a';
		#email
		$email=0;
		if (count($tout['emails'])>0) $email=1;
		
		#adresse
		$adresse=0;
		if (strlen($tout['adresse'])>0) $adresse=1;
		
		#id_contact
		$id_contact=$tout['contact']['id'];
		$nom_contact=SQLite3::escapeString($tout['contact']['nom']);
		
		#id_etablissement
		$id_etablissement=$tout['etablissement']['id'];
		
		#id_structure
		$id_structure=$tout['structure']['id'];
		$nom_structure=SQLite3::escapeString($tout['structure']['nom']);
		
		#categories
		$categories=implode(' ',$tout['categories']);
		
		$base = new SQLite3('db/index.sqlite');
		$base->busyTimeout (10000);
		$sql="update indexes set tri='$tri', text='$texte', dept='$dept', email='$email', adresse='$adresse', id_contact='$id_contact', nom_contact='$nom_contact', id_etablissement='$id_etablissement', id_structure='$id_structure', nom_structure='$nom_structure', categories='$categories' where rowid=$id;";
		$base->exec($sql);
		$base->close();
		if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - index casquette $id ".var_export($tout,true)."\n", 3, "tmp/fab.log");
		if (LDAP==1) ldap_update("rowid=$id");
	}
}
