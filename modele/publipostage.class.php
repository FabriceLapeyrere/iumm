<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */

class Publipostage {
	function Publipostage() {}
	function supports($binf,$motifs) {
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
		$supports=array();
		$base = new SQLite3('db/publipostage.sqlite');
		$base->busyTimeout (10000);
		$sql="select *, datetime(date,'localtime') as datel from supports $cond_motifs order by date desc limit $binf,20";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$supports[$tab['rowid']]=$tab;
		}
		$base->close();
		return $supports;
	}
	function support($id) {
		$base = new SQLite3('db/publipostage.sqlite');
		$base->busyTimeout (10000);
		$sql="select *, datetime(date,'localtime') as datel from supports where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$support=$tab;
		}
		$base->close();
		return $support;
	}
	function sup_support($id) {
		$base = new SQLite3('db/publipostage.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from supports where rowid=$id";
		$res = $base->query($sql);
		$base->close();
	}
	function aj_support($nom) {
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/publipostage.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into supports (id_utilisateur, nom) values (1,'$nom')";
		$res = $base->query($sql);
		$base->close();
	}
	function ren_support($id,$nom) {
		$nom=SQLite3::escapeString($nom);
		$base = new SQLite3('db/publipostage.sqlite');
		$base->busyTimeout (10000);
		$sql="update supports set nom='$nom' where rowid=$id";
		$res = $base->query($sql);
		$base->close();
	}
	function mod_support($id,$h_page,$l_page,$nb_lignes,$nb_colonnes,$mp_gauche,$mp_droite,$mp_haut,$mp_bas,$mc_gauche,$mc_droite,$mc_haut,$mc_bas) {
		$base = new SQLite3('db/publipostage.sqlite');
		$base->busyTimeout (10000);
		$sql="update supports set h_page=$h_page,l_page=$l_page,nb_lignes=$nb_lignes,nb_colonnes=$nb_colonnes,mp_gauche=$mp_gauche,mp_droite=$mp_droite,mp_haut=$mp_haut,mp_bas=$mp_bas,mc_gauche=$mc_gauche,mc_droite=$mc_droite,mc_haut=$mc_haut,mc_bas=$mc_bas where rowid=$id";
		$res = $base->query($sql);
		$base->close();
	}
	function nb_supports($motifs="") {
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
			$base = new SQLite3('db/publipostage.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from supports $cond_motifs";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		$base->close();
		return $nb;
	}
	function dernier() {
		$base = new SQLite3('db/publipostage.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid from supports order by date desc limit 0,1";
		$res = $base->query($sql);
		$id=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$id=$tab['rowid'];
		}
		$base->close();
		return $id;
	}
}
?>
