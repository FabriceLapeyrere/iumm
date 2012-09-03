<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if (file_exists('db/contacts.sqlite')) unlink('db/contacts.sqlite');
	if (file_exists('db/mailing.sqlite')) unlink('db/mailing.sqlite');
	if (file_exists('db/publipostage.sqlite')) unlink('db/publipostage.sqlite');
	if (file_exists('db/utilisateurs.sqlite')) unlink('db/utilisateurs.sqlite');
	if (file_exists('db/index.sqlite')) unlink('db/index.sqlite');

	function rrmdir($dir) {
		foreach(glob($dir . '/*') as $file) {
		    if(is_dir($file))
		        rrmdir($file);
		    else
		        unlink($file);
		}
		foreach(glob($dir . '/.git*') as $file) {
		    if(is_dir($file))
		        rrmdir($file);
		    else
		        unlink($file);
		}
		rmdir($dir);
	}
    if (file_exists('ui/cache/fichiers')) {
		rrmdir('ui/cache/fichiers');
	}
    if (file_exists('modele/cache/fichiers')) {
		rrmdir('modele/cache/fichiers');
	}
    if (file_exists('modele/corbeille')) {
		rrmdir('modele/corbeille');
	}
    if (file_exists('fichiers/emails')) {
		rrmdir('fichiers/emails');
	}
    if (file_exists('ui/includes/upload/server/php/thumbnails')) {
		rrmdir('ui/includes/upload/server/php/thumbnails');
	}
    if (file_exists('fichiers/envois')) {
		rrmdir('fichiers/envois');
	}
    if (file_exists('fichiers/news')) {
		rrmdir('fichiers/news');
	}
    if (file_exists('tmp')) {
		rrmdir('tmp');
	}

    if (!file_exists('ui/cache/fichiers')) {
		mkdir('ui/cache/fichiers');
		touch('ui/cache/fichiers/.gitkeep');
	}
    if (!file_exists('modele/cache/fichiers')) {
		mkdir('modele/cache/fichiers');
		touch('modele/cache/fichiers/.gitkeep');
	}
    if (!file_exists('modele/corbeille')) {
		mkdir('modele/corbeille');
		touch('modele/corbeille/.gitkeep');
	}
    if (!file_exists('fichiers/emails')) {
		mkdir('fichiers/emails');
		touch('fichiers/emails/.gitkeep');
	}
    if (!file_exists('ui/includes/upload/server/php/thumbnails')) {
		mkdir('ui/includes/upload/server/php/thumbnails');
		touch('ui/includes/upload/server/php/thumbnails/.gitkeep');
	}
    if (!file_exists('fichiers/envois')) {
		mkdir('fichiers/envois');
		touch('fichiers/envois/.gitkeep');
	}
    if (!file_exists('fichiers/news')) {
		mkdir('fichiers/news');
		touch('fichiers/news/.gitkeep');
	}
    if (!file_exists('tmp')) {
		mkdir('tmp');
		touch('tmp/.gitkeep');
	}

?>
