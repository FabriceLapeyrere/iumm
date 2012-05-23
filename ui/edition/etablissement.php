<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$id_etablissement=$_POST['id_etablissement'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	switch ($format){
		case 'html':
			$html=Html::etablissement($id_etablissement);			
			$js=Js::etablissement($id_etablissement);			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['js']=$js;
			break;
	}
?>
