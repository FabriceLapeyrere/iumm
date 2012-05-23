<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$id_categorie=$_POST['id_categorie'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	switch ($format){
		case 'html':
			$c= new Categorie($id_categorie);
			$html=$c->nbincat().", ".$c->total();			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['js']=$js;
			break;
	}
?>
