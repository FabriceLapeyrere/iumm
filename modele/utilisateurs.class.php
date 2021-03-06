<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe utilisateurs permet de lire et d'écrire dans la base les données
 * concernant les utilisateurs
 */

							
class Utilisateurs {
	function tous() {
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select * from utilisateurs";
		$res = $base->query($sql);
		$utilisateurs=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$utilisateurs[$tab['rowid']]=$tab;
		}
		$base->close();
		return $utilisateurs;
	}
	function existe($login) {
		$login=SQLite3::escapeString($login);
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from utilisateurs where login='$login'";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$base->close();
		return $n>0;
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
					nom like '%$motif%'
				)
				";
			}
		}
		$cond=" WHERE ( ".implode($tab_cond_motifs,' AND ')." )";
		if (count($tab_cond_motifs)==0) $cond="";
		$sql="select * from utilisateurs $cond limit $binf,20";
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$liste=array();
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[$tab['rowid']]=$tab;
		}
		$listes['liste']=$liste;
		$sql="select count(*) from utilisateurs $cond";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$listes['nb']=$tab['count(*)'];
		}
		$base->close();
		return $listes;
	}
	function ok($login,$mdp) {
		$login=SQLite3::escapeString($login);
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select * from utilisateurs where login='$login' and mdp='$mdp'";
		if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - $sql \n", 3, "tmp/auth.log");
		$res = $base->query($sql);
		$utilisateurs=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$utilisateurs[]=$tab;
		}
		$base->close();
		if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - tentative pour $login : ".count($utilisateurs)."\n", 3, "tmp/auth.log");
		return count($utilisateurs)==1 ? $utilisateurs[0]['rowid'] : 0;
	}
	function nb_utilisateurs($motifs="") {
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
		$cond_motifs="where 1 ";
		if(count($tab_cond_motifs)>0) {
			$cond_motifs.="AND ".implode($tab_cond_motifs,' AND ');
		}
			$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from utilisateurs $cond_motifs";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		$base->close();
		return $nb;
	}
	function aj_utilisateur($nom, $login, $mdp, $droits) {
		$nom=SQLite3::escapeString($nom);
		$login=SQLite3::escapeString($login);
		$mdp=SQLite3::escapeString($mdp);
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into utilisateurs (nom, login, mdp, droits) values ('$nom','$login','$mdp','$droits')";
		$base->query($sql);
		$id_utilisateur=$base->lastInsertRowID();
		$base->close();
		return $id_utilisateur;
	}

}
?>
