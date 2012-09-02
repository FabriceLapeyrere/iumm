<?php
	$t_all=microtime(true);
	foreach(Casquettes::toutes() as $id) {
		$t=microtime(true);
		Cache::set_obsolete('casquette', $id);
		Cache::set_obsolete('casquette_sel', $id);
		Html::casquette($id);
		Html::casquette_selection($id);
		$t1=microtime(true)-$t;
		echo "casquette $id ($t1)\r";
	}
	echo "casquettes ok \n";
	foreach(Contacts::tous() as $id) {
		$t=microtime(true);
		Cache::set_obsolete('contact', $id);
		Html::contact($id);
		$t1=microtime(true)-$t;
		echo "contact $id ($t1)\r";
	}
	echo "contacts ok \n";
	foreach(Etablissements::tous() as $id) {
		$t=microtime(true);
		Cache::set_obsolete('etablissement', $id);
		Html::etablissement($id);
		$t1=microtime(true)-$t;
		echo "etablissement $id ($t1)\r";
	}
	echo "etablissements ok \n";
	foreach(Structures::toutes() as $id) {
		$t=microtime(true);
		Cache::set_obsolete('structure', $id);
		Html::structure($id);
		$t1=microtime(true)-$t;
		echo "structure $id ($t1)\r";
	}
	echo "structures ok \n";
	echo "categories pour edition...\n";
	foreach(Categories::toutes() as $id) {
		Cache::set_obsolete('ed_categorie', $id);
	}
	include "ui/edition/categories.php";
	echo "categories pour selection...\n";
	foreach(Categories::toutes() as $id) {
		Cache::set_obsolete('sel_categorie', $id);
	}
	include "ui/selection/categories.php";
	$t1_all=microtime(true)-$t_all;
	echo "le cache est à jour !! ($t1_all)\n";
	
?>
