<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$message="";
	if (! file_exists("fichiers/emails/")) mkdir("fichiers/emails",755,true);
	if (! file_exists("fichiers/news/")) mkdir("fichiers/news",755,true);
	if (! file_exists("fichiers/envois/")) mkdir("fichiers/envois",755,true);

	if (! is_writable("db/")) $message.="Le dossier \"db/\" doit être accessible en écriture.<br />";
	if (! is_writable("ui/cache/")) $message.="Le dossier \"ui/cache/\" doit être accessible en écriture.<br />";
	if (! is_writable("modele/cache/")) $message.="Le dossier \"ui/cache/\" doit être accessible en écriture.<br />";
	if (! is_writable("fichiers/emails/")) $message.="Le dossier \"fichiers/emails/\" doit être accessible en écriture.<br />";
	if (! is_writable("fichiers/news/")) $message.="Le dossier \"fichiers/news/\" doit être accessible en écriture.<br />";
	if (! is_writable("fichiers/envois/")) $message.="Le dossier \"fichiers/envois/\" doit être accessible en écriture.<br />";
	if ($message!="") {
		echo $message;
		exit;
	}
	if (!file_exists('db/contacts.sqlite')) {
		$base = new SQLite3('db/contacts.sqlite');
		$base->exec(file_get_contents('ctl/init/contacts.sqlite.sql'));
		$base->close();
	}
	if (!file_exists('db/mailing.sqlite')) {
		$base = new SQLite3('db/mailing.sqlite');
		$base->exec(file_get_contents('ctl/init/mailing.sqlite.sql'));
		$base->close();
	}
	if (!file_exists('db/publipostage.sqlite')) {
		$base = new SQLite3('db/publipostage.sqlite');
		$base->exec(file_get_contents('ctl/init/publipostage.sqlite.sql'));
		$base->close();
	}
	if (!file_exists('db/utilisateurs.sqlite')) {
		$base = new SQLite3('db/utilisateurs.sqlite');
		$base->exec(file_get_contents('ctl/init/utilisateurs.sqlite.sql'));
		$base->close();
	}
	if (!file_exists('db/index.sqlite')) {
		$base = new SQLite3('db/index.sqlite');
		$base->exec(file_get_contents('ctl/init/index.sqlite.sql'));
		$base->close();
	}
?>
