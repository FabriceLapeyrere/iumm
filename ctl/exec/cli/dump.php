<?php
	$t=time();
	$nom_zip="sauvegarde_$t.zip";
	class zip extends ZipArchive {
	   
		public function addDirectory($dir) { // adds directory
		    foreach(glob($dir . '/*') as $file) {
		        if(is_dir($file))
		            $this->addDirectory($file);
		        else
		            $this->addFile($file);
		    }
		}
	}
	$zip = new zip;
	$zip->open("tmp/$nom_zip",ZIPARCHIVE::CREATE);
	if (file_exists('db/contacts.sqlite')) {
		echo "Contacts\n";
		$zip->addFile('db/contacts.sqlite');
	}
	if (file_exists('db/mailing.sqlite')) {
		echo "Mailing\n";
		$zip->addFile('db/mailing.sqlite');
	}
	if (file_exists('db/publipostage.sqlite')) {
		echo "Publipostage\n";
		$zip->addFile('db/publipostage.sqlite');
	}
	if (file_exists('db/utilisateurs.sqlite')) {
		echo "Utilisateurs\n";
		$zip->addFile('db/utilisateurs.sqlite');
	}
	if (file_exists('db/index.sqlite')) {
		echo "Index\n";
		$zip->addFile('db/index.sqlite');
	}
	if (file_exists('modele/cache/fichiers')) {
		echo "Cache du modele\n";
		$zip->addDirectory('modele/cache/fichiers');
	}
	if (file_exists('ui/cache/fichiers')) {
		echo "Cache de l'interface graphique\n";
		$zip->addDirectory('ui/cache/fichiers');
	}
	if (file_exists('fichiers')) {
		echo "Fichiers\n";
		$zip->addDirectory('fichiers');
	}
	if (file_exists('modele/corbeille')) {
		echo "Corbeille\n";
		$zip->addDirectory('modele/corbeille');
	}
	foreach(glob('tmp/*.log') as $file) {
		echo "$file\n";
		$zip->addFile($file);
	}
	$zip->close();	
	echo "Terminé, sauvegarde enregistrée dans tmp/$nom_zip\n";
?>
