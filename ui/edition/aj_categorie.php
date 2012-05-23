<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$nom=$_POST['nom']['valeur'];
	$c= new Categories();
	$rowid=$c->aj_categorie($nom);
	$js="
	$('#ncat').remove();
	$('#sel_tree').dynatree('getTree').reload();
	var node=$('#ed_tree').dynatree('getTree').getNodeByKey('0');
	node.addChild({title:'<span>".addslashes($nom)." <span class=\'nbincat \'>(0, 0)</span></span>',icon:false, addClass:'categorie', key:'$rowid'});
	node.sortChildren();
	ed_scatapi.reinitialise();
		$.post('ajax.php',{
			action:'selection/selection_humains',
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#sel_humains').html(data.html);
				eval(data.js);
			}
		},
		'json'
	);
	";
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['html']="";
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
