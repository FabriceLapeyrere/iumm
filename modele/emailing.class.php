<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */

class Emailing {
	function Emailing() {}
	function envois($binf,$motifs) {
		$tab_cond_motifs=array();
		if ($motifs!="") {
			$tab_motifs=explode(' ',$motifs);
			foreach($tab_motifs as $motif){
				$motif=SQLite3::escapeString($motif);
				$tab_cond_motifs[]="
				sujet like '%$motif%'
				";
			}
		}
		$cond_motifs="where 1 ";
		if(count($tab_cond_motifs)>0) {
			$cond_motifs.="AND ".implode($tab_cond_motifs,' AND ');
		}
		$envois=array();
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid, sujet, nb, date from envois $cond_motifs order by date desc limit $binf,20";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$envois[$tab['rowid']]=array('sujet'=>$tab['sujet'], 'nb'=>$tab['nb'], 'date'=>$tab['date']);
		}
		$base->close();
		return $envois;
	}
	function envoi($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select sujet, html, nb, datetime(date,'localtime') as date, expediteur, pid, statut from envois where rowid=$id";
		$res = $base->query($sql);
		$envoi=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$envoi=$tab;
		}
		$base->close();
		return $envoi;
	}
	function envoi_pjs($id){
		$i=0;
		$pjs=array();
		$chemin="fichiers/envois/$id/";
		$envoi=Emailing::envoi($id);
		if(file_exists($chemin)){
			if ($handle = opendir($chemin)) {
				while (false !== ($fichier = readdir($handle))) {
					if(!strstr($envoi['html'],$fichier) && $fichier!='.'  && $fichier!='..' ) {
						$pjs[]=$chemin.$fichier;
					}
				}
			}
		}
		return $pjs;
	}
	function messages_boite_envoi($id,$binf) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid, id_casquette, id_envoi, i from boite_envoi where id_envoi=$id and erreurs='' limit $binf,20";
		$res = $base->query($sql);
		$messages=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$messages[$tab['rowid']]=$tab;
		}
		$base->close();
		return $messages;
	}
	function messages_boite_envoi_et_erreur($id,$binf) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid, id_casquette, id_envoi, i, erreurs from boite_envoi where id_envoi=$id limit $binf,20";
		$res = $base->query($sql);
		$messages=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$messages[$tab['rowid']]=$tab;
		}
		$base->close();
		return $messages;
	}
	function sup_message($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from boite_envoi where rowid=$id";
		$res = $base->query($sql);
		$base->close();
	}
	function sup_messages($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from boite_envoi where id_envoi=$id";
		$res = $base->query($sql);
		$base->close();
	}
	function statut_envoi($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select statut from envois where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$statut=$tab['statut'];
		}
		$base->close();
		return $statut;
	}
	function arret_envoi($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="update envois set statut=2 where rowid=$id";
		$res = $base->query($sql);
		$base->close();
	}
	function pause_envoi($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="update envois set statut=1 where rowid=$id";
		$res = $base->query($sql);
		$base->close();
	}
	function play_envoi($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="update envois set statut=0 where rowid=$id";
		$res = $base->query($sql);
		$base->close();
	}
	function nb_envois($motifs="") {
		$tab_cond_motifs=array();
		if ($motifs!="") {
			$tab_motifs=explode(' ',$motifs);
			foreach($tab_motifs as $motif){
				$motif=SQLite3::escapeString($motif);
				$tab_cond_motifs[]="
				sujet like '%$motif%'
				";
			}
		}
		$cond_motifs="where 1 ";
		if(count($tab_cond_motifs)>0) {
			$cond_motifs.="AND ".implode($tab_cond_motifs,' AND ');
		}
			$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from envois $cond_motifs";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		$base->close();
		return $nb;
	}
	function nb_messages_boite_envoi($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from boite_envoi where id_envoi=$id and erreurs=''";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		$base->close();
		return $nb;
	}
	function nb_messages_boite_envoi_et_erreur($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from boite_envoi where id_envoi=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		$base->close();
		return $nb;
	}
	function nb_erreurs_envoi($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from boite_envoi where id_envoi=$id and erreurs!=''";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		$base->close();
		return $nb;
	}
	function efface_erreurs_envoi($id) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="update boite_envoi set erreurs='' where id_envoi=$id";
		$base->query($sql);
		$base->close();
	}
	function envoi_premier_message($id_envoi) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="SELECT * FROM `boite_envoi` where id_envoi=$id_envoi and erreurs='' limit 0,1";
		$res = $base->query($sql);
		$tab=$res->fetchArray(SQLITE3_ASSOC);
		$base->close();
		return $tab;
	}
	function message_erreur($id_message,$erreur) {
		$erreur=SQLite3::escapeString($erreur);
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="UPDATE boite_envoi SET erreurs='$erreur' WHERE rowid=$id_message";
		$res = $base->query($sql);
		$base->close();
	}
	function log_envoi($id_envoi,$log) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$log=SQLite3::escapeString($log);
		$sql="UPDATE envois SET log=log||'$log' WHERE rowid=$id_envoi";
		$res = $base->query($sql);
		$base->close();
	}
	function lit_log_envoi($id_envoi) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select log from envois where rowid=$id_envoi";
		$res = $base->query($sql);
		$log="";
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$log=$tab['log'];
		}
		$base->close();
		return $log;
	}
	function dernier() {
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid from envois order by date desc limit 0,1";
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
