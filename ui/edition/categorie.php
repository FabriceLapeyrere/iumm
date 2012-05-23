<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$id=$_POST['id_categorie'];
	$reponse['succes']=1;
	$reponse['titre']=Html::titre_categorie($id);
	
?>
