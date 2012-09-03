<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	foreach(Casquettes::toutes() as $id) {
		#Index::init($id);
		Index::update($id);
		echo "                      \rcasquette ".$id;
	}
	echo "\n";
?>
