<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$message="";
	$succes=1;
	$id=$_POST['id_categorie'];
	$id_parent=$_POST['id_parent'];
	$c=new Categorie($id);
	$js="
		$('#ed_tree').dynatree('getTree').getNodeByKey('$id').activate();
	";
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
