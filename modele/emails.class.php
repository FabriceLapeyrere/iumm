<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe contacts permet de lire et d'écrire dans la base les données
 */

class Emails {
	function Emails() {}
	function nb_emails($motifs="") {
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
		$cond_motifs="";
		if(count($tab_cond_motifs)>0) {
			$cond_motifs=" AND ( ".implode($tab_cond_motifs,' AND ')." )";
		}
		#on récupere le nombre d'emails:
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from emails where sujet!='####'$cond_motifs";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$nb=$tab['count(*)'];
		}
		return $nb;
	}
	function liste($binf,$motifs) {
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
		$cond_motifs="";
		if(count($tab_cond_motifs)>0) {
			$cond_motifs=" AND ( ".implode($tab_cond_motifs,' AND ')." )";
		}
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select * from emails where sujet!='####'$cond_motifs order by date desc limit $binf,20";
		#echo $sql;
		$res = $base->query($sql);
		$liste=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[$tab['rowid']]=array('sujet'=>$tab['sujet'], 'date'=>$tab['date']);
		}
		$base->close();
		return $liste;
	}
	function aj_email($sujet) {
		$sujet=SQLite3::escapeString($sujet);
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into emails (id_utilisateur,sujet) values (1,'$sujet')";
		$base->query($sql);
		$id_email=$base->lastInsertRowID();
		$base->close();
		mkdir("fichiers/emails/$id_email");
		mkdir("fichiers/emails/$id_email/thumbnails");
		return $id_email;
	}
	function dernier() {
		$id=0;
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid from emails where sujet!='####' order by date desc limit 0,1";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$id=$tab['rowid'];
		}
		$base->close();
		return $id;
	}
	function aj_envoi($id_email,$id_expediteur,$liste_casquettes){
		$e=new Email($id_email);	
		$html='';
		$sujet=SQLite3::escapeString($e->sujet);
		$expediteur=Emailing::expediteur($id_expediteur);
		$from=SQLite3::escapeString(json_encode($expediteur));
		$nb_email=count($liste_casquettes);
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into envois (html, sujet, expediteur, nb, log, statut, pid) VALUES ('$html', '$sujet', '$from', $nb_email, '', 1, 0);";
		$base->query($sql);
		$id_envoi=$base->lastInsertRowID();
		$html=SQLite3::escapeString(str_replace("fichiers/emails/$id_email","fichiers/envois/$id_envoi",$e->html));
		$sql="update envois set html='$html' where rowid=$id_envoi";
		$base->query($sql);
		$i=1;
		foreach ($liste_casquettes as $id=>$casquette) {
			$sql="insert into boite_envoi (id_casquette, id_envoi, i, erreurs) VALUES ($id, $id_envoi, $i, '')";
			$base->query($sql);
			$i++;
		}
		$base->close();
		smartCopy("fichiers/emails/$id_email","fichiers/envois/$id_envoi");
		return $id_envoi;
	}
		
}
?>
