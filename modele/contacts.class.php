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
			$tab_motifs=explode(' ',str_replace(',',' ',$motifs));
			foreach($tab_motifs as $motif){
				if (trim($motif)!="") {
					$motif=SQLite3::escapeString($motif);
					$tab_cond_motifs[]="
						select id_contact, nom_contact from indexes where nom_contact!='####' and nom_contact!='$$$$' and text MATCH '$motif*'
					";
				}
			}
		}
		if (trim(implode(' intersect ',$tab_cond_motifs))=='') $sql="select id_contact, nom_contact from indexes where nom_contact!='####' and nom_contact!='$$$$' group by id_contact order by nom_contact";
		else {
			$cond=" select id_contact, nom_contact from ( ".implode(' intersect ',$tab_cond_motifs)." )";
			$sql="$cond group by id_contact order by nom_contact";
		}
		error_log(date('d/m/Y H:i:s')."contacts\n----\n$sql\n----\n", 3, "tmp/fab.log");
		$sql_page="$sql limit $binf,20";
		$base = new SQLite3('db/index.sqlite');
		$base->busyTimeout (10000);
		$liste=array();
		$res = $base->query($sql_page);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[$tab['id_contact']]=$tab['id_contact'];
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
