<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$req='';
if (isset($_REQUEST['req'])) $req=$_REQUEST['req'];
$binf=0;
if (isset($_REQUEST['binf'])) $binf=$_REQUEST['binf'];
$nr=5;
if (isset($_REQUEST['nr'])) $nr=$_REQUEST['nr'];

$base = new SQLite3('db/index.sqlite');
$base->busyTimeout (10000);
$req=SQLite3::escapeString($req);
$req=implode('* ',explode(' ',str_replace(',',' ',$req)));				
$sql="select count(*) from indexes where text match '$req*'";
$res = $base->query($sql);
$nb=0;
while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
	$nb=$tab['count(*)'];
}
$sql="select rowid from indexes where text match '$req*' order by tri COLLATE NOCASE limit $binf,$nr";
$res = $base->query($sql);
$casquettes=array();
while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
	$casquettes[$tab['rowid']]=array();
}
$base->close();
foreach($casquettes as $id=>$tab) {
	$c=new Casquette($id);
	$casquettes[$id]=$c->tout();
	$casquettes[$id]['html']=Html::casquette_sel_propre($id);
	$casquettes[$id]['etab']=$c->casquette_etab();
}

$retour=array(
	'nb'=>$nb,
	'binf'=>$binf,
	'nr'=>$nr,
	'casquettes'=>$casquettes,
	);
echo json_encode($retour);
?>
