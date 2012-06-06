<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe contacts permet de lire et d'écrire dans la base les données
 */

class Contacts {
	function liste_rapide($motifs,$binf=0) {
		$listes=array();
		$tab_cond_motifs=array();
		if ($motifs!="") {
			$tab_motifs=explode(' ',str_replace(',','',$motifs));
			foreach($tab_motifs as $motif){
				$motif=SQLite3::escapeString(noaccent($motif));
				$tab_cond_motifs[]="
				(
					t3.rowid IN (
					select rowid from cache_contact where content MATCH '*$motif*'
					)
				)
				";
			}
		}
		$cond=" AND ( ".implode($tab_cond_motifs,' AND ')." )";
		if (count($tab_cond_motifs)==0) $cond="";
		$sql="select t1.rowid as id, t1.nom as nom_cas, t3.nom as nom_cont, t3.prenom as prenom_cont, t3.rowid as id_contact from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette inner join contacts as t3 on t2.id_contact=t3.rowid where t1.nom!='####' and t3.nom!='$$$$' $cond group by t3.rowid order by nom_cont COLLATE NOCASE  limit $binf,20";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		$liste=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[$tab['id_contact']]=array('nom_cont'=>$tab['nom_cont'], 'prenom_cont'=>$tab['prenom_cont']);
		}
		$listes['liste']=$liste;
		$sql="select count(*) from (select * from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette inner join contacts as t3 on t2.id_contact=t3.rowid where t1.nom!='####' and t3.nom!='$$$$' $cond group by t3.rowid COLLATE NOCASE)";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		$listes['nb']=$nb;
		$base->close();
		return $listes;
	}
	function aj_contact($nom, $prenom, $id_utilisateur=1) {
		$nom=SQLite3::escapeString($nom);
		$prenom=SQLite3::escapeString($prenom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into contacts (id_utilisateur, nom, prenom) values ($id_utilisateur, '$nom', '$prenom')";
		$base->query($sql);
		$id_contact=$base->lastInsertRowID();
		$c=new Contact($id_contact);
		$c->aj_casquette('Perso', $id_utilisateur);
		$base->close();
		return $id_contact;
	}
	function nb_contacts($motifs="") {
		$listes=Contacts::liste_rapide($motifs);
		return count($listes['complete']);
	}
	function tous() {
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid from contacts where nom!='####'";
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
