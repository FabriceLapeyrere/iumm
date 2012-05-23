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
$.post('ajax.php',{
	action:'edition/nbincat',
	id_categorie:$id_parent,
	format:'html'
	},
	function(data){
		if (data.succes==1) {
			$('#sel_dynatree-id-$id_parent').find('.nbincat').first().html('('+data.html+')');
		}
	},
	'json'
);
$('#ed_tree').dynatree('getTree').reload();
	";
	while ($c->id_parent!=0){
		$c=new Categorie($c->id_parent);
		$js.="
		$.post('ajax.php',{
				action:'edition/nbincat',
				id_categorie:".$c->id.",
				format:'html'
			},function(data){
				if(data.succes==1){
					$('#sel_dynatree-id-".$c->id."').find('.nbincat').first().html('('+data.html+')');
					sel_scatapi.reinitialise();
				}
			},
			'json'
		);
		";
	}
	$c=new Categorie($id_parent);
	while ($c->id_parent!=0){
		$c=new Categorie($c->id_parent);
		$js.="
		$.post('ajax.php',{
				action:'edition/nbincat',
				id_categorie:".$c->id.",
				format:'html'
			},function(data){
				if(data.succes==1){
					$('#sel_dynatree-id-".$c->id."').find('.nbincat').first().html('('+data.html+')');
					sel_scatapi.reinitialise();
				}
			},
			'json'
		);
		";
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
