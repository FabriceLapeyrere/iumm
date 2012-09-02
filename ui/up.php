<?php

	$t0=microtime(true);
#	foreach(Cache::obsoletes('casquette') as $id) {
#		Html::casquette($id);
#		if (microtime(true)-$t0>10) break;
#	}
#	foreach(Cache::obsoletes('etablissement') as $id) {
#		Html::etablissement($id);
#		if (microtime(true)-$t0>10) break;
#	}


	$reponse['succes']=1;
	$reponse['js']="setTimeout(up,30000);";
	$reponse['message']="I'm up!";
?>
