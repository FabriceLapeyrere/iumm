<?php
	if ($valeur=='') {
		$valeur_mnews="(Votre texte<br />sur plusieurs lignes)";
	}
	else {
		$valeur_mnews=nl2br($valeur);
	}
?>
