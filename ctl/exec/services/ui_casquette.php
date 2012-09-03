<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$id=$_REQUEST['id'];
$retour='';
if (Casquettes::existe($id)) {
	$retour=Html::casquette_sel($id);
	$pattern = "/<input data-id='(\d+)' data-idetab='(\d+)' data-idstr='(\d+)' type='checkbox'\/>/";
	$replacement = '';
	$retour= preg_replace($pattern, $replacement, $retour);
	$pattern = "/<input data-id='(\d+)' data-idcont='(\d+)' type='checkbox'\/>/";
	$replacement = '';
	$retour= preg_replace($pattern, $replacement, $retour);
	
	$pattern = "/<span style='font-size:8px;position:relative;bottom:5px;'><button data-id='(\d+)' class='moins ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' style='border:none;background:none;' role='button' aria-disabled='false' title='supprimer'><span class='ui-button-icon-primary ui-icon ui-icon-close'><\/span><span class='ui-button-text'>supprimer<\/span><\/button><\/span>/";
	$replacement = '';
	$retour= preg_replace($pattern, $replacement, $retour);
}
echo $retour;
?>
