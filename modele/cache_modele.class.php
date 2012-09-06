<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe contacts permet de lire et d'écrire dans la base les données
 */

class Cache_modele {
	
	function fichier($int){
		$dix_mille=floor($int/10000);
		$mille=floor(($int%10000)/1000);
		$cent=floor(($int%1000)/100);
		$dix=floor(($int%100)/10);
		$un=floor($int%10);
		return "$dix_mille/$mille/$cent/$dix";
	}
	function dossier($int){
		$dix_mille=floor($int/10000);
		$mille=floor(($int%10000)/1000);
		$cent=floor(($int%1000)/100);
		return "$dix_mille/$mille/$cent";
	}
	function dearchive($id,$objet){
		$tab=array();
		$dossier="modele/cache/fichiers/$objet/".Cache_modele::dossier($id);
		$cache="modele/cache/fichiers/$objet/".Cache_modele::fichier($id);
		if (file_exists($cache)) {
			$t_dearchive=microtime(true);
			while(trim(file_get_contents($cache))=="" && microtime(true)-$t_dearchive<1) {
				sleep(0.01);
			}
			$content=file_get_contents($cache);
			if (trim($content)!="")
				$tab=unserialize($content);
		}
		return $tab;
	}
	function archive($id,$objet,$tab){
		$dossier="modele/cache/fichiers/$objet/".Cache_modele::dossier($id);
		$cache="modele/cache/fichiers/$objet/".Cache_modele::fichier($id);
		if (!file_exists($dossier)) mkdir($dossier,0777,true);
		file_put_contents($cache,serialize($tab));
	}
	function is_wlock($objet,$id){
		$dossier="modele/cache/fichiers/$objet";
		return file_exists("$dossier/$id.lock");
	}
	function wlock($objet,$id){
		$dossier="modele/cache/fichiers/$objet";
		if (!file_exists($dossier)) mkdir($dossier,0777,true);
		touch("$dossier/$id.lock");
	}
	function un_wlock($objet,$id){
		$dossier="modele/cache/fichiers/$objet";
		if (file_exists("$dossier/$id.lock")) unlink("$dossier/$id.lock");
	}
	function is_p_wlock($objet,$id,$cle){
		$dossier="modele/cache/fichiers/$objet";
		return file_exists("$dossier/$id$cle.lock");
	}
	function p_wlock($objet,$id,$cle){
		$dossier="modele/cache/fichiers/$objet";
		if (!file_exists($dossier)) mkdir($dossier,0777,true);
		touch("$dossier/$id$cle.lock");
	}
	function un_p_wlock($objet,$id,$cle){
		$dossier="modele/cache/fichiers/$objet";
		if (file_exists("$dossier/$id$cle.lock")) unlink("$dossier/$id$cle.lock");
	}
	function set($objet,$id,$cle,$valeur) {
		if ($id>0) {
			error_log(date('d/m/Y H:i:s')." - cache-modele SET $objet $id $cle\n", 3, "tmp/cache.log");
			while(Cache_modele::is_wlock($objet,$id)) {
				error_log(date('d/m/Y H:i:s')." - cache-modele $objet $id vérrouillé\n", 3, "tmp/cache.log");
				sleep(0.01);
			}
			Cache_modele::wlock($objet,$id);
			$tab=Cache_modele::dearchive($id,$objet);
			$tab[($id%10).$cle]=$valeur;
			Cache_modele::archive($id,$objet,$tab);
			Cache_modele::un_wlock($objet,$id);
		}
		return $valeur;
	}
	function del($objet,$id,$cle) {
		error_log(date('d/m/Y H:i:s')." - cache-modele DEL $objet $id $cle\n", 3, "tmp/cache.log");
		$cles=explode(',',$cle);
		$tab=Cache_modele::dearchive($id,$objet);
		foreach($cles as $cle){
			$key=($id%10).trim($cle);
			if(array_key_exists($key, $tab)) unset($tab[$key]);
		}
		Cache_modele::archive($id,$objet,$tab);
	}
	function suppr($objet,$id) {
		error_log(date('d/m/Y H:i:s')." - cache-modele SUPPR $objet $id\n", 3, "tmp/cache.log");
		$tab=Cache_modele::dearchive($id,$objet);
		foreach($tab as $nom=>$valeur) {
			if (substr($nom,0,1)==$id%10) unset($tab[$nom]);
		}
		Cache_modele::archive($id,$objet,$tab);
	}
	function get($objet,$id,$cle) {
		$valeur='&&&&';
		if ($id>0) {
			while(Cache_modele::is_p_wlock($objet,$id,$cle)) {
				error_log(date('d/m/Y H:i:s')." - cache-modele GET $objet $id $cle mise à jour en cours\n", 3, "tmp/cache.log");
				sleep(0.1);
			}
			error_log(date('d/m/Y H:i:s')." - cache-modele GET $objet $id $cle \n", 3, "tmp/cache.log");
			$tab=Cache_modele::dearchive($id,$objet);
			$key=($id%10).trim($cle);
			if(array_key_exists($key, $tab)) $valeur=$tab[$key];
		}
		return $valeur;
	}
	function existe($objet,$id,$cle) {
		$tab=Cache_modele::dearchive($id,$objet);
		$key=($id%10).trim($cle);
		$retour=array_key_exists($key, $tab) ? 1 : 0;
		return $retour;
	}
}
