<?php
$email=$_REQUEST['email'];

$base = new SQLite3('db/index.sqlite');
$base->busyTimeout (10000);
$email=SQLite3::escapeString($email);		
$sql="select rowid from indexes where text match '$email'";
$res = $base->query($sql);
$retour='';
$id=0;
while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
	$id=$tab['rowid'];
}
$base->close();
if ($id>0) {
	$c=new Casquette($id);
	$retour=json_encode($c->tout());
}
echo $retour;
?>
