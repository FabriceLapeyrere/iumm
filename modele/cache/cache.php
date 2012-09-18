<?php
$objet=$_POST['objet'];
$id_objet=$_POST['id_objet'];
$prop=$_POST['prop'];
$o=new $objet($id_objet);
foreach($prop as $p){
	$p=$p."_maj";
	$t=microtime();
	$o->$p();
	$t1=microtime()-$t;
	if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - cache-modele $objet($id_objet)::$p(); $t1 \n", 3, "tmp/fab.log");
}

?>
