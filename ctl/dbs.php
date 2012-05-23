<?php
	$message="";
	if (! is_writable("db/")) $message.="Le dossier \"db/\" doit être accessible en écriture.<br />";
	if (! is_writable("ui/cache/db/")) $message.="Le dossier \"ui/cache/db/\" doit être accessible en écriture.<br />";
	if (! is_writable("fichiers/emails/")) $message.="Le dossier \"fichiers/emails/\" doit être accessible en écriture.<br />";
	if (! is_writable("fichiers/envois/")) $message.="Le dossier \"fichiers/envois/\" doit être accessible en écriture.<br />";
	if ($message!="") {
		echo $message;
		exit;
	}
	if (!file_exists('db/contacts.sqlite')) {
		copy('ctl/init/contacts.sqlite.init','db/contacts.sqlite');
	}
	if (!file_exists('db/mailing.sqlite')) {
		copy('ctl/init/mailing.sqlite.init','db/mailing.sqlite');
	}
	if (!file_exists('db/publipostage.sqlite')) {
		copy('ctl/init/publipostage.sqlite.init','db/publipostage.sqlite');
	}
	if (!file_exists('ui/cache/db/cache.sqlite')) {
		copy('ctl/init/cache.sqlite.init','ui/cache/db/cache.sqlite');
	}
?>
