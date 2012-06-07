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
				$sql[]=" t1.rowid IN ( ".implode(',',$casquettes)." ) ";
			if ($cas==1 and $Ncas==1) 
				$sql[]=" t1.rowid NOT IN ( ".implode(',',$casquettes)." ) ";
		}
		#etablissements
		
		$cond_etabs="";
		foreach($etablissements as $id_etablissement){
			if ($cond_etabs=="") $cond_etabs.="t4.id_etablissement=$id_etablissement ";
			else $cond_etabs.="or t4.id_etablissement=$id_etablissement ";
		}
		if ($cond_etabs!="" and $Netabs==0) {
			$sql[]="

			(
				t1.rowid IN (
					select
					t1.rowid as id

					from 
					casquettes as t1
						inner join 
						ass_casquette_contact as t2
							on t1.rowid=t2.id_casquette inner join
						contacts as t3
							on t2.id_contact=t3.rowid  inner join 
						ass_casquette_etablissement as t4
							on t1.rowid=t4.id_casquette inner join
						etablissements as t5
							on t4.id_etablissement=t5.rowid
						

					where 
						t5.nom!='####'
						and
						( $cond_etabs )
						and
						t4.id_casquette||','||t4.date IN (
							SELECT id_casquette||','||max ( date )
							FROM 'ass_casquette_etablissement'
							GROUP BY id_casquette
						)
				)
			)
			";
		}
		if ($cond_etabs!="" and $Netabs==1) {
			$sql[]="

			(
				t1.rowid NOT IN (
					select
					t1.rowid as id

					from 
					casquettes as t1
						inner join 
						ass_casquette_contact as t2
							on t1.rowid=t2.id_casquette inner join
						contacts as t3
							on t2.id_contact=t3.rowid  inner join 
						ass_casquette_etablissement as t4
							on t1.rowid=t4.id_casquette inner join
						etablissements as t5
							on t4.id_etablissement=t5.rowid
						

					where 
						t5.nom!='####'
						and
						( $cond_etabs )
						and
						t4.id_casquette||','||t4.date IN (
							SELECT id_casquette||','||max ( date )
							FROM 'ass_casquette_etablissement'
							GROUP BY id_casquette
						)
				) or t1.rowid NOT IN (
					select
					t1.rowid as id

					from 
					casquettes as t1
						inner join 
						ass_casquette_contact as t2
							on t1.rowid=t2.id_casquette inner join
						contacts as t3
							on t2.id_contact=t3.rowid  inner join 
						ass_casquette_etablissement as t4
							on t1.rowid=t4.id_casquette inner join
						etablissements as t5
							on t4.id_etablissement=t5.rowid
					where 
						t5.nom!='####'
						and
						t4.id_casquette||','||t4.date IN (
							SELECT id_casquette||','||max ( date )
							FROM 'ass_casquette_etablissement'
							GROUP BY id_casquette
						)
				)
			)
			";
		}
		
		#catégories
		$liste_cats=array();
		$cond_cats="";
		foreach($categories as $id_categorie){
			if ($cond_cats=="") $cond_cats.="id_categorie=$id_categorie ";
			else $cond_cats.="or id_categorie=$id_categorie ";
		}
		if ($cond_cats!="" && $Ncats==0) {
			$sql[]="
			(
				t1.rowid IN (
					select
					id_casquette from ass_casquette_categorie where 
						statut=1
						and
						( $cond_cats )
						and id_casquette||','||id_categorie||','||date IN (
								SELECT
								id_casquette||','||id_categorie||','||max ( date )
								FROM 'ass_casquette_categorie'
								GROUP BY id_categorie, id_casquette
						)
				)
			)";
		}
		if ($cond_cats!="" && $Ncats==1) {
			$sql[]="
			(
				t1.rowid NOT IN (
					select
					id_casquette from ass_casquette_categorie where 
						id_casquette||','||id_categorie||','||date IN (
								SELECT
								id_casquette||','||id_categorie||','||max ( date )
								FROM 'ass_casquette_categorie'
								GROUP BY id_categorie, id_casquette
							)
						and
						statut=1
						and
						( $cond_cats )

				) OR t1.rowid NOT IN (
					select
					id_casquette from ass_casquette_categorie where 
						id_casquette||','||id_categorie||','||date IN (
								SELECT
								id_casquette||','||id_categorie||','||max ( date )
								FROM 'ass_casquette_categorie'
								GROUP BY id_categorie, id_casquette
							)
						and
						statut=1

				)
			)";
		}
		
		#motifs
		$tab_cond_motifs=array();
		if ($motifs!="") {
			$tab_motifs=explode(' ',str_replace(',','',$motifs));
			foreach($tab_motifs as $motif){
				$motif=SQLite3::escapeString(noaccent($motif));
				$tab_cond_motifs[]="
				(
					t1.rowid IN (
					select rowid from cache_casquette where content MATCH '$motif*'
					)
				)
				";
			}
		}
		$cond_motifs="( ".implode($tab_cond_motifs,' AND ')." )";
		if (count($tab_cond_motifs)>0) $sql[]=$cond_motifs;
		#mots
		$tab_cond_mots=array();
		if ($mots!="") {
			$tab_mots=explode(' ',str_replace(',','',$mots));
			foreach($tab_mots as $mot){
				if(trim($mot)!="") {
					$mot=SQLite3::escapeString(noaccent($mot));
					$tab_cond_mots[]="
					(
						t1.rowid IN (
						select rowid from cache_casquette where content MATCH '$mot'
						)
					)
					";
				}
			}
		}
		$cond_mots="( ".implode($tab_cond_mots,' AND ')." )";
		if (count($tab_cond_mots)>0) $sql[]=$cond_mots;
		
		#départements
		$html_depts="";
		$tab_cond_depts=array();
		if (count($depts)>0 && $depts[0]!='') {
			foreach($depts as $dept){
				if(trim($dept)!="") {
					$tab_cond_depts[]="
					(
						t1.rowid IN (
						select rowid from cache_casquette where cp MATCH '$dept*'
						)
					)
					";
				}
			}
				
		}
		$cond_depts="( ".implode($tab_cond_depts,' OR ')." )";
		if (count($tab_cond_depts)>0) $sql[]=$cond_depts;
		
		#email
		$html_email="";
		if($email==1 && $Nemail==0)
			$sql[]="
			(
				t1.rowid IN (
				select rowid from cache_casquette where email!=''
				)
			)
			";
		if($email==1 && $Nemail==1)
			$sql[]="
			(
				t1.rowid IN (
				select rowid from cache_casquette where email=''
				)
			)
			";
		#adresse
		$html_adresse="";
		if($adresse==1 && $Nadresse==0)
			$sql[]="
			(
				t1.rowid IN (
				select rowid from cache_casquette where adresse!=''
				)
			)
			";
		if($adresse==1 && $Nadresse==1)
			$sql[]="
			(
				t1.rowid IN (
				select rowid from cache_casquette where adresse=''
				)
			)
			";
		return implode($sql,' AND ');
	}
	function sql_combinaison($c){
		#cas simple: une selection
		if (!key_exists('c1',$c)){
			$N="";
			if ($c['N']==1) $N=" NOT";
			$sql=Casquettes::sql_selection($c['selection']);
		}
		else {	
			if ($c['op']==1) $op=" AND";
			else $op=" OR";
			$N="";
			if ($c['N']==1) $N=" NOT";
	
			$sql=" ( $N ".Casquettes::sql_combinaison($c['c1'])." $op ".Casquettes::sql_combinaison($c['c2'])." ) ";
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
		if ($_SESSION['N']==1) $N="NOT";
		$op="";
		if ($_SESSION['op']==1) $op="AND";
		else $op="OR";
		$cond="";
		
		if ($cond_selection=="") $cond_selection='1';
		
		if ($cond_combinaison=="") $cond=" AND $N $cond_selection";
		else $cond=" AND $N ( $cond_combinaison $op $cond_selection )";
		
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$base->createFunction('mot', 'mot', 2);
		$base->createFunction('cp', 'cp', 1);
		$base->createFunction('motif', 'motif', 2);
		$binf=$_SESSION['sel_binfc'];
		switch ($type) {
			case "":
				$sql="select t1.rowid as id, t1.tri as tri, t1.nom as nom_cas, t3.nom as nom_cont, t3.prenom as prenom_cont from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette inner join contacts as t3 on t2.id_contact=t3.rowid where t1.nom!='####' $cond order by tri COLLATE NOCASE limit $binf,20 ";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['id']]=array('nom_cas'=>$tab['nom_cas'], 'nom_cont'=>$tab['nom_cont'], 'prenom_cont'=>$tab['prenom_cont']);
				}
				break;
			case "toutes":
				$sql="select t1.rowid as id, t1.tri as tri, t1.nom as nom_cas, t3.nom as nom_cont, t3.prenom as prenom_cont from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette inner join contacts as t3 on t2.id_contact=t3.rowid where t1.nom!='####' order by tri COLLATE NOCASE";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['id']]=array('nom_cas'=>$tab['nom_cas'], 'nom_cont'=>$tab['nom_cont'], 'prenom_cont'=>$tab['prenom_cont']);
				}
				break;
			case "nb":
				$sql="select count(*) from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette inner join contacts as t3 on t2.id_contact=t3.rowid where t1.nom!='####' $cond order by tri COLLATE NOCASE";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste=$tab['count(*)'];
				}
				break;
			case "complete":
				$sql="select t1.rowid as id, t1.tri as tri, t1.nom as nom_cas, t3.nom as nom_cont, t3.prenom as prenom_cont from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette inner join contacts as t3 on t2.id_contact=t3.rowid where t1.nom!='####' $cond order by tri COLLATE NOCASE";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['id']]=array('nom_cas'=>$tab['nom_cas'], 'nom_cont'=>$tab['nom_cont'], 'prenom_cont'=>$tab['prenom_cont']);
				}
				break;
			case "email":
				$sql="select t1.rowid as id, t1.tri as tri, t1.nom as nom_cas, t3.nom as nom_cont, t3.prenom as prenom_cont from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette inner join contacts as t3 on t2.id_contact=t3.rowid where t1.nom!='####' $cond AND
		(
			t1.rowid IN (
				select id_casquette from donnees_casquette where type='email' and valeur!='' and id_casquette||','||nom||','||date IN (
					select id_casquette||','||nom||','||MAX(date) from donnees_casquette group by id_casquette, nom
				)
			)
			OR
			t1.rowid IN (
				select t1.id_casquette from ass_casquette_etablissement as t1 inner join donnees_etablissement as t2 on t1.id_etablissement=t2.id_etablissement where t2.id_etablissement||','||t2.nom||','||t2.date IN (
					select id_etablissement||','||nom||','||MAX(date) from donnees_etablissement group by id_etablissement, nom
				) and t1.id_casquette||','||t1.date IN (
					select id_casquette||','||MAX(date) from ass_casquette_etablissement group by id_casquette
				) and t2.type='email' and t2.valeur!=''
			)
		) order by tri COLLATE NOCASE";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['id']]=array('nom_cas'=>$tab['nom_cas'], 'nom_cont'=>$tab['nom_cont'], 'prenom_cont'=>$tab['prenom_cont']);
				}
				break;
			case "adresse":
				$sql="select t1.rowid as id, t1.tri as tri, t1.nom as nom_cas, t3.nom as nom_cont, t3.prenom as prenom_cont from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette inner join contacts as t3 on t2.id_contact=t3.rowid where t1.nom!='####' $cond AND
			(
				t1.rowid IN (
					select id_casquette from donnees_casquette where type='adresse' and valeur!='' and valeur!='{\"adresse\":\"\",\"cp\":\"\",\"ville\":\"\",\"pays\":\"\"}' and id_casquette||','||nom||','||date IN (
						select id_casquette||','||nom||','||MAX(date) from donnees_casquette group by id_casquette, nom
					)
				) OR t1.rowid IN (
					select id_casquette from ass_casquette_etablissement as t1 inner join donnees_etablissement as t2 on t1.id_etablissement=t2.id_etablissement where t2.type='adresse' and t2.valeur!='' and t2.valeur!='{\"adresse\":\"\",\"cp\":\"\",\"ville\":\"\",\"pays\":\"\"}' and t2.id_etablissement||','||t2.nom||','||t2.date IN (
						select id_etablissement||','||nom||','||MAX(date) from donnees_etablissement group by id_etablissement, nom
					) and t1.date||','||t1.id_casquette IN (select max(date)||','||id_casquette from ass_casquette_etablissement group by id_casquette) group by id_casquette	
				)
			) order by tri COLLATE NOCASE";
				$res = $base->query($sql);
				while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
					$liste[$tab['id']]=array('nom_cas'=>$tab['nom_cas'], 'nom_cont'=>$tab['nom_cont'], 'prenom_cont'=>$tab['prenom_cont']);
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
		#on teste si le cache existe
		$sql="select adresse from cache_casquette where rowid=$id";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$adresse=$tab['adresse'];
		}
		$base->close();		
		return $adresse;
	}	
}
?>
