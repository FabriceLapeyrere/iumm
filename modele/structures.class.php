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
			$tab_motifs=explode(' ',str_replace(',','',$motifs));
			foreach($tab_motifs as $motif){
				$motif=SQLite3::escapeString($motif);
				$tab_cond_motifs[]="
				(
					t3.rowid IN (
					select rowid from cache_structure where content MATCH '*$motif*'
					)
				)
				";
			}
		}
		$cond=" AND ( ".implode($tab_cond_motifs,' AND ')." )";
		if (count($tab_cond_motifs)==0) $cond="";
		$sql="select t1.rowid as id, t1.nom as nom_etab, t3.nom as nom_str, t3.rowid as id_structure from etablissements as t1 inner join ass_etablissement_structure as t2 on t1.rowid=t2.id_etablissement inner join structures as t3 on t2.id_structure=t3.rowid where t1.nom!='####' $cond group by t3.rowid order by nom_str COLLATE NOCASE  limit $binf,20";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$liste=array();
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[$tab['id_structure']]=array('nom_str'=>$tab['nom_str']);
		}
		$listes['liste']=$liste;
		$sql="select count(*) from (select * from etablissements as t1 inner join ass_etablissement_structure as t2 on t1.rowid=t2.id_etablissement inner join structures as t3 on t2.id_structure=t3.rowid where t1.nom!='####' $cond group by t3.rowid COLLATE NOCASE)";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$listes['nb']=$tab['count(*)'];
		}
		$base->close();
		return $listes;
	}
	function aj_structure($nom) {
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into structures (nom) values ('$nom')";
		$base->query($sql);
		$id_structure=$base->lastInsertRowID();
		$s=new Structure($id_structure);
		$s->aj_etablissement('Siège social');
		$base->close();
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
