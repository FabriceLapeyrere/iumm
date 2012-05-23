<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$format=$_POST['format'];
	$html="";
	$js="
	sel_casquettes();
	sel_ajuste();
	";
	$js.="
	$('#sel_filtres .pagination').html('".json_escape(Html::pagination($_SESSION['sel_binfc'],Casquettes::liste('nb')))."');
	$('#sel_casquettes .casquette .titre input').attr('checked',false);
	";
	foreach($_SESSION['selection']['casquettes'] as $key){
		$js.="
		$('#sel_casquettes input[data-id=$key]').attr('checked',true);
		";
	}

	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=Html::casquettes_selection();
			$reponse['js']=$js;
			break;
	}
?>
