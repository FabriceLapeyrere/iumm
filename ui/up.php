<?php

	$t0=microtime(true);
	foreach(Casquettes::toutes() as $id=>$casquette) {
		if (Cache::obsolete('casquette',$id)) Html::casquette($id);
		if (microtime(true)-$t0>20) break;
	}
	foreach(Etablissements::tous() as $id=>$etablissement) {
		if (Cache::obsolete('etablissement',$id)) Html::etablissement($id);
		if (microtime(true)-$t0>20) break;
	}


	$reponse['succes']=1;
	$reponse['js']="setTimeout(up,30000);";
	$reponse['message']="I'm up!";
?>
