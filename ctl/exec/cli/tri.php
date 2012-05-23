<?php
foreach(Casquettes::toutes() as $id) {
		$c=new Casquette($id);
		echo $c->nom_contact." ".$c->prenom_contact." ".$c->nom." ";
		if ($c->casquette_etab==1){
			echo "STRUCTURE ";
			$tri=SQLite3::escapeString($c->nom_structure);
			$base = new SQLite3('db/contacts.sqlite');
			$base->busyTimeout (10000);
			$sql="update casquettes set tri='$tri' where rowid=".$c->id;
			$base->query($sql);
			$base->close();	
		} else {
			$tri=SQLite3::escapeString($c->nom_contact." ".$c->prenom_contact);
			$base = new SQLite3('db/contacts.sqlite');
			$base->busyTimeout (10000);
			$sql="update casquettes set tri='$tri' where rowid=".$c->id;
			$base->query($sql);
			$base->close();
		}
		echo "fait : $tri \n";
	}
?>	
