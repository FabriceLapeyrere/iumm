<?php
sleep(3);
if (isset($_POST['objet'])) {
	$objet=$_POST['objet'];
	$id_objet=$_POST['id_objet'];
	Cache::set_obsolete($objet,$id_objet);
	if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - Début de mise en cache-ui $objet($id_objet)\n", 3, "tmp/cache.log");
	$t=microtime(true);
	Html::$objet($id_objet);
	$t1=microtime(true)-$t;
	if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - Fin de mise en cache-ui $objet($id_objet) $t1 \n", 3, "tmp/cache.log");
} else {
	$t_all=microtime(true);
	foreach($_POST as $key=>$params){
		if ($key!='auth_cle' && $key!='action'){
			$objet=$params[0];
			$id_objet=$params[1];
			Cache::set_obsolete($objet,$id_objet);
		}
	}
	foreach($_POST as $key=>$params){
		if ($key!='auth_cle' && $key!='action'){
			$objet=$params[0];
			$id_objet=$params[1];
			if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - Début de mise en cache-ui $objet($id_objet)\n", 3, "tmp/cache.log");
			$t=microtime(true);
			Html::$objet($id_objet);
			$t1=microtime(true)-$t;
			if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - Fin de mise en cache-ui $objet($id_objet) $t1 \n", 3, "tmp/cache.log");
		}
	}
	$t1_all=microtime(true)-$t_all;
	if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - Total mise en cache $t1_all \n", 3, "tmp/cache.log");
	
}
?>
