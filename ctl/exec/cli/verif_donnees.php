<?php
	foreach(Casquettes::toutes() as $id) {
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select * from donnees_casquette where id_casquette=$id";
		$res = $base->query($sql);
		$donnees=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$donnees[]=$tab;
		}
		$base->close();
		foreach($donnees as $donnee){
			if($donnee['type']=='adresse' && $donnee['valeur']!=""){
				$t=json_decode($donnee['valeur']);
				if (!is_object($t)){
					echo "\nVérif casquette $id :\nprobleme d'adresse\n";
					echo "erreur : ".$donnee['valeur']."\n";
					var_dump(json_decode($donnee['valeur']));
					echo "\ncorrection : ".str_replace("\n"," ",$donnee['valeur'])."\n";
					var_dump(json_decode(str_replace("\n"," ",$donnee['valeur'])));
					$valeur=SQLite3::escapeString(str_replace("\n"," ",$donnee['valeur']));
					$rowid=$donnee['rowid'];
					$base = new SQLite3('db/contacts.sqlite');
					$base->busyTimeout (10000);
					$sql="update donnees_casquette set valeur='$valeur' where rowid=$rowid";
					$base->query($sql);
					$base->close();
				} else {
					echo "ok\n";
				}
			}
		}
	}
	foreach(Etablissements::tous() as $id) {
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select * from donnees_etablissement where id_etablissement=$id";
		$res = $base->query($sql);
		$donnees=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$donnees[]=$tab;
		}
		$base->close();
		foreach($donnees as $donnee){
			if($donnee['type']=='adresse' && $donnee['valeur']!=""){
				$t=json_decode($donnee['valeur']);
				if (!is_object($t)){
					echo "\nVérif etablissement $id :\nprobleme d'adresse\n";
					echo "erreur : ".$donnee['valeur']."\n";
					var_dump(json_decode($donnee['valeur']));
					echo "\ncorrection : ".str_replace("\n"," ",$donnee['valeur'])."\n";
					var_dump(json_decode(str_replace("\n"," ",$donnee['valeur'])));
					$valeur=SQLite3::escapeString(str_replace("\n"," ",$donnee['valeur']));
					$rowid=$donnee['rowid'];
					$base = new SQLite3('db/contacts.sqlite');
					$base->busyTimeout (10000);
					$sql="update donnees_etablissement set valeur='$valeur' where rowid=$rowid";
					$base->query($sql);
					$base->close();
				} else {
					echo "ok\n";
				}
			}
		}
	}
?>
