<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe contacts permet de lire et d'écrire dans la base les données
 */

class Structures {
	var $nbstructures=0;
	function Structures($motifs="") {
		#on récupere le nombre de contacts:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$tab_cond_motifs=array();
		if ($motifs!="") {
			$tab_motifs=explode(' ',$motifs);
			foreach($tab_motifs as $motif){
				$motif=SQLite3::escapeString($motif);
				$tab_cond_motifs[]="
				nom like '%$motif%'
				";
			}
		}
		$cond_motifs="";
		if(count($tab_cond_motifs)>0) {
			$cond_motifs=" AND ( ".implode($tab_cond_motifs,' AND ')." )";
		}
		$sql="select count(*) from structures where nom!='####'$cond_motifs";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->nbcontacts=$tab['count(*)'];
		}
	}
	function liste_rapide($motifs,$binf=0) {
		$listes=array();
		$tab_cond_motifs=array();
		if ($motifs!="") {
			$tab_motifs=explode(' ',str_replace(',',' ',$motifs));
			foreach($tab_motifs as $motif){
				if (trim($motif)!="") {
					$motif=SQLite3::escapeString($motif);
					$avant=$motif;
					$motif=str_replace('!','',$motif);
					if ($avant!=$motif) $motif="tri:$motif";
					$tab_cond_motifs[]="
						select id_structure, nom_structure from indexes where nom_structure!='####' and and nom_contact='$$$$' text MATCH '$motif*'
					";
				}
			}
		}
		if (trim(implode(' intersect ',$tab_cond_motifs))=='') $sql="select id_structure, nom_structure from indexes where nom_structure!='####' and id_structure!='0'  group by id_structure order by nom_structure COLLATE NOCASE";
		else {
			$cond=" select id_structure, nom_structure from ( ".implode(' intersect ',$tab_cond_motifs)." )";
			$sql="$cond where id_structure!='0' group by id_structure order by nom_structure COLLATE NOCASE";
		}
		if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')."structures\n----\n$sql\n----\n", 3, "tmp/fab.log");
		$sql_page="$sql limit $binf,20";
		
		$base = new SQLite3('db/index.sqlite');
		$base->busyTimeout (10000);
		$liste=array();
		$res = $base->query($sql_page);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[$tab['id_structure']]=$tab['id_structure'];
		}
		$listes['liste']=$liste;
		$sql="select count(*) from ( $sql 	)";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$listes['nb']=$tab['count(*)'];
		}
		$base->close();
		return $listes;
	}
	function aj_structure($nom, $id_utilisateur=1) {
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into structures (id_utilisateur, nom) values ($id_utilisateur, '$nom')";
		$base->query($sql);
		$id_structure=$base->lastInsertRowID();
		$s=new Structure($id_structure);
		$s->aj_etablissement('Siège social');
		$base->close();
		async('modele/cache/cache',array('objet'=>'Structure','id_objet'=>$id_structure,'prop'=>array('nom')));
		return $id_structure;
	}
	function nb_structures($motifs="") {
		$listes=Structures::liste_rapide($motifs);
		return count($listes['complete']);
	}
	function toutes() {
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid from structures where nom!='####'";
		$liste=array();
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[]=$tab['rowid'];
		}
		$base->close();
		return $liste;
	}
}
?>
