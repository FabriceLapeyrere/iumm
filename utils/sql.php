<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */

$base = new SQLite3('../db/mailing.sqlite');
$sql="update boite_envoi set erreurs=''";
$res = $base->query($sql);
while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
	var_dump($tab);
}

?>
