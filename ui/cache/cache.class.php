<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe cache gère le cache des vues html de l'interface utilisateur
 */
class cache {
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
		$dossier="ui/cache/fichiers/$objet/".Cache::dossier($id);
		$cache="ui/cache/fichiers/$objet/".Cache::fichier($id);
		if (file_exists($cache))
			$tab=unserialize(file_get_contents($cache));
		return $tab;
	}
	function archive($id,$objet,$tab){
		$dossier="ui/cache/fichiers/$objet/".Cache::dossier($id);
		$cache="ui/cache/fichiers/$objet/".Cache::fichier($id);
		if (!file_exists($dossier)) mkdir($dossier,0777,true);
		file_put_contents($cache,serialize($tab));
	}
	function obsolete($objet, $id){
		$tab=Cache::dearchive($id,$objet);
		$key=($id%10);
		$retour=array_key_exists($key, $tab) ? 0 : 1;
		return $retour;
	}
	function set_obsolete($objet, $id){
		error_log(date('d/m/Y H:i:s')." - ui/cache set_obsolete $objet($id)\n", 3, "tmp/cache.log");
		$tab=Cache::dearchive($id,$objet);
		$key=($id%10);
		if(array_key_exists($key, $tab)) unset($tab[$key]);
		Cache::archive($id,$objet,$tab);
	}	
	function put($objet, $id, $html){
		$tab=Cache::dearchive($id,$objet);
		$key=($id%10);
		$tab[$key]=$html;
		Cache::archive($id,$objet,$tab);	
	}	
	function get($objet, $id){
		$html="";
		$tab=Cache::dearchive($id,$objet);
		$key=($id%10);
		if(array_key_exists($key, $tab)) $html=$tab[$key];
		return $html;
	}	
}
?>
