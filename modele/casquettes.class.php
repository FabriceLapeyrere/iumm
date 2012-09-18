<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe contacts permet de lire et d'écrire dans la base les données
 */

class Casquettes {
	
	function existe($id) {
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select count(*) from casquettes where rowid=$id and nom!='####'";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$base->close();
		return $n>0;
	}
	function sql_selection($selection) {
		foreach ($selection as $key => $value) { $$key = $value; }
		$sql=array();
	
		#casquettes
		if (count($casquettes)>0) {
			if ($cas==1 and $Ncas==0) 
				$sql[]=" select rowid,tri from indexes where rowid IN ( ".implode(',',$casquettes)." ) ";
			if ($cas==1 and $Ncas==1) 
				$sql[]=" select rowid,tri from indexes where rowid NOT IN ( ".implode(',',$casquettes)." ) ";
		}
		#etablissements
		
		$cond_etabs="";
		foreach($etablissements as $id_etablissement){
			if ($cond_etabs=="") $cond_etabs.="select rowid,tri from indexes where id_etablissement match '$id_etablissement' ";
			else $cond_etabs.=" union select rowid,tri from indexes where  id_etablissement match '$id_etablissement' ";
		}
		if ($cond_etabs!="" and $Netabs==0) {
			$sql[]="

			select rowid,tri from ( $cond_etabs )
			";
		}
		if ($cond_etabs!="" and $Netabs==1) {
			$sql[]="

			select rowid,tri from indexes except select rowid,tri from ( $cond_etabs )
			
			";
		}
		
		#catégories
		$liste_cats=array();
		$cond_cats="";
		foreach($categories as $id_categorie){
			if ($cond_cats=="") $cond_cats.="select rowid,tri from indexes where categories match '$id_categorie' ";
			else $cond_cats.="union select rowid,tri from indexes where categories match '$id_categorie' ";
		}
		if ($cond_cats!="" && $Ncats==0) {
			$sql[]="
			select rowid,tri from ( $cond_cats )
			";
		}
		if ($cond_cats!="" && $Ncats==1) {
			$sql[]="
			select rowid,tri from indexes except select rowid,tri from ( $cond_cats )
			";
		}
		
		#motifs
		$tab_cond_motifs=array();
		if ($motifs!="") {
			$tab_motifs=explode(' ',str_replace(',','',$motifs));
			foreach($tab_motifs as $motif){
				if (trim($motif)!='') {
					$motif=SQLite3::escapeString(noaccent($motif));
					$tab_cond_motifs[]="
					select rowid,tri from indexes where text match '$motif*'
					";
				}
			}
		}
		$cond_motifs=implode($tab_cond_motifs,' intersect ');
		if (count($tab_cond_motifs)>0) $sql[]=$cond_motifs;
		#mots
		$tab_cond_mots=array();
		if ($mots!="") {
			$tab_mots=explode(' ',str_replace(',','',$mots));
			foreach($tab_mots as $mot){
				if(trim($mot)!="") {
					$mot=SQLite3::escapeString(noaccent($mot));
					$tab_cond_mots[]="
					select rowid,tri from indexes where text match '$mot'
					";
				}
			}
		}
		$cond_mots=implode($tab_cond_mots,' intersect ');
		if (count($tab_cond_mots)>0) $sql[]=$cond_mots;
		
		#départements
		$html_depts="";
		$tab_cond_depts=array();
		if (count($depts)>0 && $depts[0]!='') {
			foreach($depts as $dept){
				if(trim($dept)!="") {
					$tab_cond_depts[]="
					select rowid,tri from indexes where dept match '$dept'
					";
				}
			}
				
		}
		$cond_depts="select rowid,tri from ( ".implode($tab_cond_depts,' union ')." )";
		if (count($tab_cond_depts)>0) $sql[]=$cond_depts;
		
		#email
		$html_email="";
		if($email==1 && $Nemail==0)
			$sql[]="
			select rowid,tri from indexes where email match '1'
			";
		if($email==1 && $Nemail==1)
			$sql[]="
			select rowid,tri from indexes where email match '0'
			";
		#adresse
		$html_adresse="";
		if($adresse==1 && $Nadresse==0)
			$sql[]="
			select rowid,tri from indexes where adresse match '1'
			";
		if($adresse==1 && $Nadresse==1)
			$sql[]="
			select rowid,tri from indexes where adresse match '0'
			";
		return count($sql)>0 ? " ".implode(' intersect ',$sql)." " : "";
	}
	function sql_combinaison($c){
		#cas simple: une selection
		if (!key_exists('c1',$c)){
			$N="";
			if ($c['N']==1) $N=" select rowid,tri from indexes except ";
			$sql=" $N ".Casquettes::sql_selection($c['selection']);
		}
		else {	
			if ($c['op']==1) $op=" intersect ";
			else $op=" union ";
			$N="";
			if ($c['N']==1) $N=" select rowid,tri from indexes except ";
			$sql="select rowid,tri from ( $N ".Casquettes::sql_combinaison($c['c1'])." $op ".Casquettes::sql_combinaison($c['c2'])." ) ";
		}
		return $sql;
	}

	function liste($type="") {
		#$t=microtime(true);
		$liste=array();
		$cond_combinaison="";
		if (count($_SESSION['combinaison'])>0 && $_SESSION['scombinaison']==0) $cond_combinaison=Casquettes::sql_combinaison($_SESSION['combinaison']);
		$cond_selection=Casquettes::sql_selection($_SESSION['selection']);
		$N="";
		if ($_SESSION['N']==1) $N="select rowid,tri from indexes except ";
		$op="";
		if ($_SESSION['op']==1) $op=" intersect ";
		else $op=" union ";
		$cond="select rowid,tri from indexes";
		
		if ($cond_selection=="") $cond_selection='select rowid,tri from indexes';
		
		if ($cond_combinaison=="") $cond=" $N $cond_selection";
		else $cond="select rowid,tri from  $N ( select rowid,tri from ($cond_combinaison ) $op select rowid,tri from ($cond_selection) )";
		
		
		$base = new SQLite3('db/index.sqlite');
		$base->busyTimeout (10000);
		$binf=$_SESSION['sel_binfc'];
		
		switch ($type) {
			case "":
				$sql="$cond order by tri COLLATE NOCASE limit $binf,20 ";
				if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')."\n----\n$sql\n----\n", 3, "tmp/fab.log");
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['rowid']]=$tab['rowid'];
				}
				break;
			case "toutes":
				$sql="$cond order by tri COLLATE NOCASE";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['rowid']]=$tab['rowid'];
				}
				break;
			case "nb":
				$sql="select count(*) from ( $cond )";
				if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')."\n----\n$sql\n----\n", 3, "tmp/fab.log");
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste=$tab['count(*)'];
				}
				break;
			case "email":
				$sql="$cond intersect select rowid, tri from indexes where email match '1' order by tri COLLATE NOCASE";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['rowid']]=$tab['rowid'];
				}
				break;
			case "adresse":
				$sql="$cond intersect select rowid, tri from indexes where adresse match '1' order by tri COLLATE NOCASE";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['rowid']]=$tab['rowid'];
				}
				break;
				
		}
		$base->close();
		#echo (microtime(true)-$t)."\n";
		return $liste;
		
	}
	function toutes() {
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid from casquettes where nom!='####'";
		$liste=array();
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[]=$tab['rowid'];
		}
		$base->close();
		return $liste;
	}
	function adresse_cache($id) {
		return Cache_modele::get('casquette',$id,'adresse');
	}	
}
?>
