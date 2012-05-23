<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$id_contact=$_POST['id_contact'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	switch ($format){
		case 'html':
			$html=Html::contact($id_contact);			
			$js=Js::contact($id_contact);			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['js']=$js;
			break;
	}
?>
