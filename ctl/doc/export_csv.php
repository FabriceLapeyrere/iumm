<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */

	$fp = fopen('tmp/export.csv', 'w');
	$csv=array();
	$csv[0]=array();
	foreach (Casquettes::liste('complete') as $id=>$casquette) {
		$tab=array();
		$c=new Casquette($id);
		if ($c->casquette_etab==0) {
			$tab['1_nom']=$c->nom_contact;
			$tab['2_prenom']=$c->prenom_contact;
		} else {
			$tab['1_nom']="";
			$tab['2_prenom']="";
		}
		$donnees=$c->donnees();
		foreach($donnees as $nom=>$donnee){
			if ($donnee['valeur']!=""){
				if ($donnee['type']=='adresse') {
					$adresse=json_decode($donnee['valeur']);
					$tab["3_contact_adresse_adresse"]=$adresse->adresse;
					$tab["3_contact_adresse_cp"]=$adresse->cp;
					$tab["3_contact_adresse_ville"]=$adresse->ville;
					$tab["3_contact_adresse_pays"]=$adresse->pays;
				}
				else $tab["3_contact_".$nom]=$donnee['valeur'];
			}
		}
		$tab['4_structure']=$c->nom_structure;
		$donnees=$c->donnees_etab();
		foreach($donnees as $nom=>$donnee){
			if ($donnee['valeur']!=""){
				if ($donnee['type']=='adresse') {
					$adresse=json_decode($donnee['valeur']);
					$tab["5_struture_adresse_adresse"]=$adresse->adresse;
					$tab["5_struture_adresse_cp"]=$adresse->cp;
					$tab["5_struture_adresse_ville"]=$adresse->ville;
					$tab["5_struture_adresse_pays"]=$adresse->pays;
				}
				else $tab["5_struture_".$nom]=$donnee['valeur'];
			}
		}
		$tab['6_listes']="";
		$categories=$c->categories;
		$listes=array();
		foreach($categories as $categorie){
			$listes[]=$categorie['nom'];
		}
		$tab['6_listes']=implode(', ',$listes);
		$csv[]=$tab;
	}
	$keys=array();
	foreach ($csv as $tab){
		foreach($tab as $cle=>$valeur){
			if (!in_array($cle,$keys)) $keys[]=$cle;
		}
	}
	sort($keys);
	$final=array();
	$final[0]=array();
	foreach($keys as $cle){
		$final[0][]=substr($cle,2);
	}
	foreach ($csv as $index=>$tab){
		$tmp=array();
		foreach($keys as $cle){
			if (isset($tab[$cle])) $tmp[]=$tab[$cle];
			else $tmp[]="";
		}
		$final[]=$tmp;
	}
	foreach ($final as $line) {
	    fputcsv($fp, $line);
	}
	fclose($fp);
	header("Location: tmp/export.csv");
?>	
