<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$id_casquette=$_POST['id_casquette'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=Html::casquette($id_casquette);
			$reponse['js']=Js::casquette($id_casquette);
			break;
	}
?>
