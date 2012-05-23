<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$message="";
	$succes=1;
	$id=$_POST['id'];
	$id_parent=$_POST['id_parent'];
	$c=new Categorie($id);
	$c->mod_parent($id_parent);
	$js="
	$('#sel_tree').dynatree('getTree').reload();
	";
	while ($c->id!=0){
		$js.="
		$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id."').data.title='".addslashes(Html::titre_categorie($c->id))."';
		$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id."').render();
		";
		$c=new Categorie($c->id_parent);		
	}
	$c=new Categorie($id_parent);
	while ($c->id!=0){
		$js.="
		$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id."').data.title='".addslashes(Html::titre_categorie($c->id))."';
		$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id."').render();
		";
		$c=new Categorie($c->id_parent);
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']=$message;
		$reponse['html']="";
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
