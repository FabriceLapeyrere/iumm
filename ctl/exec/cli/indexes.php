<?php
	foreach(Casquettes::toutes() as $id) {
		#Index::init($id);
		Index::update($id);
		echo "                      \rcasquette ".$id;
	}
	echo "\n";
?>
