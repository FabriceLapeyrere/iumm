<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$id=$_REQUEST['id'];
$retour='';
if (Casquettes::existe($id)) {
	$retour=Html::casquette_sel_propre($id);
}
echo $retour;
?>
