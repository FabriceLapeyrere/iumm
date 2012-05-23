<?php
	foreach(Casquettes::toutes() as $id) {
		$c=new Casquette($id);
		echo $c->nom_contact." ".$c->prenom_contact." ".$c->nom." ";
		$c->index();
		echo "fait\n";
	}
	foreach(Etablissements::tous() as $id) {
		$e=new Etablissement($id);
		echo $e->nom_structure." ".$e->nom." ";
		$e->index();
		echo "fait\n";
	}
	foreach(Contacts::tous() as $id) {
		$c=new Contact($id);
		echo $c->nom." ".$c->prenom." ";
		$c->index();
		echo "fait\n";
	}
	foreach(Structures::toutes() as $id) {
		$s=new Structure($id);
		echo $s->nom." ";
		$s->index();
		echo "fait\n";
	}
?>
