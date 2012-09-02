<?php
	$t_all=microtime(true);
	$sql="";
	foreach(Casquettes::toutes() as $id) {
		$t=microtime(true);
		$c= new Casquette($id);
		$c->tout();
		$t1=microtime(true)-$t;
		echo "\r                                                       \rcasquette $id ($t1)";
	}
	echo "\ncasquettes ok \n";
	foreach(Contacts::tous() as $id) {
		$t=microtime(true);
		$c= new contact($id);
		$c->tout();
		$t1=microtime(true)-$t;
		echo "\r                                                       \rcontact $id ($t1)";
	}
	echo "\ncontacts ok \n";
	foreach(Etablissements::tous() as $id) {
		$t=microtime(true);
		$c= new etablissement($id);
		$c->tout()."\n";
		$t1=microtime(true)-$t;
		echo "\r                                                       \retablissement $id ($t1)";
	}
	echo "\netablissements ok \n";
	foreach(Structures::toutes() as $id) {
		$t=microtime(true);
		$c= new structure($id);
		$c->tout()."\n";
		$t1=microtime(true)-$t;
		echo "\r                                                       \rstructure $id ($t1)";
	}
	echo "\nstructures ok \n";
	
?>
