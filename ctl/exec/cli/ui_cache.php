<?php
	foreach(Casquettes::toutes() as $id) {
		echo "casquette $id\n";
		Cache::set_obsolete('casquette', $id);
		Html::casquette($id);
	}
	foreach(Etablissements::tous() as $id) {
		echo "etablissement $id\n";
		Cache::set_obsolete('etablissement', $id);
		Html::etablissement($id);
	}
?>
