<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$id_support=$_POST['id_support'];
	$format=$_POST['format'];
	$html=Html::support($id_support);
	$js='';#js::emailing_support();
	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['js']=$js;
			break;
	}
?>

