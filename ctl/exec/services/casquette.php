<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$email=$_REQUEST['email'];

$base = new SQLite3('db/index.sqlite');
$base->busyTimeout (10000);
$email=SQLite3::escapeString($email);		
$sql="select rowid from indexes where text match '$email'";
$res = $base->query($sql);
$retour=array();
$id=0;
while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
	$id=$tab['rowid'];
}
$base->close();
if ($id>0) {
	$c=new Casquette($id);
	$retour=$c->tout();
	$retour['id']=$c->id;
}
echo json_encode($retour);
?>
