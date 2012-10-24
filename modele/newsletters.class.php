<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe contacts permet de lire et d'écrire dans la base les données
 */

class Newsletters {
	function Newsletters() {}
	function nb_news($motifs="") {
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
		$sql="select count(*) from news where sujet!='####'$cond_motifs";
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
		$sql="select * from news where sujet!='####'$cond_motifs order by date desc limit $binf,20";
		#echo $sql;
		$res = $base->query($sql);
		$liste=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[$tab['rowid']]=array('sujet'=>$tab['sujet'], 'date'=>$tab['date']);
		}
		$base->close();
		return $liste;
	}
	function aj_news($sujet) {
		$sujet=SQLite3::escapeString($sujet);
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into news (id_utilisateur,sujet) values (1,'$sujet')";
		$base->query($sql);
		$id_news=$base->lastInsertRowID();
		$base->close();
		mkdir("fichiers/news/$id_news",0777,true);
		mkdir("fichiers/news/$id_news/thumbnails",0777,true);
		return $id_news;
	}
	function derniere() {
		$id=0;
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid from news where sujet!='####' order by date desc limit 0,1";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$id=$tab['rowid'];
		}
		$base->close();
		return $id;
	}
	function modeles(){
		$modeles=array('Divers'=>array());
		$sql="SELECT * FROM news_modele ORDER BY nom";
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			preg_match('/(.*)_(.*)/', stripslashes($tab['nom']), $matches);
			if(isset($matches[1])) $modeles[$matches[1]][$matches[2]]=$tab['rowid'];
			else $modeles['Divers'][stripslashes($tab['nom'])]=$tab['rowid'];
		}
		$base->close();
		function cmpm($a, $b)
		{
			if ($a[1] == $b[1]) {
				return 0;
			}
			return ($a[1] < $b[1]) ? -1 : 1;
		}
		foreach($modeles as $theme=>$ms) {
			$sort=array();
			foreach($ms as $nom=>$id){
				preg_match('/(.*)#(.*)/', $nom, $matches);
				if(isset($matches[1])) $sort[$nom]=array($matches[1],$matches[2]);
				else $sort[$nom]=array($nom,$nom);
			}
			uasort($sort,"cmpm");
			$modeles[$theme]=array();
			foreach($sort as $nom=>$s){
				$modeles[$theme][$s[0]]=$ms[$nom];
			}
		}
		return $modeles;
	}
	function aj_modele($nom,$modele){
		$nom=SQLite3::escapeString($nom);
		$modele=SQLite3::escapeString($modele);
		$sql="insert into news_modele (nom, modele) values ('$nom', '$modele')";
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$base->query($sql);
		$id_modele=$base->lastInsertRowID();
		$base->close();
		return $id_modele;
	}
	function mod_modele($id,$nom,$modele){
		$nom=SQLite3::escapeString($nom);
		$modele=SQLite3::escapeString($modele);
		$sql="update news_modele set nom='$nom', modele='$modele' where rowid=$id";
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$base->query($sql);
		$base->close();
	}
	function modele_utile($id){
		$sql="select count(*) from donnees_news where news like '%\"id_modele\":\"$id\"%'";
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$base->close();
		return $n;
	}
	function sup_modele($id){
		$sql="delete from news_modele where rowid=$id";
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$base->query($sql);
		$base->close();
	}
	function modele($id){
		$sql="SELECT * FROM news_modele where rowid=$id";
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$modele=$tab['modele'];
		}
		$base->close();
		return $modele;
	}
	function nom_modele($id){
		$sql="SELECT * FROM news_modele where rowid=$id";
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$modele=$tab['nom'];
		}
		$tab=explode('#',$modele);
		$base->close();
		return $tab[0];
	}
	function aj_envoi($id_news,$id_expediteur,$liste_casquettes){
		$n=new Newsletter($id_news);	
		$html='';
		$sujet=SQLite3::escapeString($n->sujet);
		$expediteur=Emailing::expediteur($id_expediteur);
		$from=SQLite3::escapeString(json_encode($expediteur));
		$nb_email=count($liste_casquettes);
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into envois (html, sujet, expediteur, nb, log, statut, pid) VALUES ('$html', '$sujet', '$from', $nb_email, '', 1, 0);";
		$base->query($sql);
		$id_envoi=$base->lastInsertRowID();
		$html=SQLite3::escapeString(str_replace("fichiers/news/$id_news","fichiers/envois/$id_envoi",$n->html()));
		$sql="update envois set html='$html' where rowid=$id_envoi";
		$base->query($sql);
		$i=1;
		$sql="BEGIN;";
		foreach ($liste_casquettes as $id=>$casquette) {
			$sql.="insert into boite_envoi (id_casquette, id_envoi, i, erreurs) VALUES ($id, $id_envoi, $i, '');";
			$i++;
		}
		$sql.="COMMIT;";
		$base->query($sql);
		$base->close();
		smartCopy("fichiers/news/$id_news","fichiers/envois/$id_envoi");
		return $id_envoi;
	}
	
}
?>
