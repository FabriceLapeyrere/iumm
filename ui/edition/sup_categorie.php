<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_categorie'];
	
	$c=new Categorie($id);
	if($c->nb_enfants()==0) {
		$casquettes=$c->casquettes();
		$id_parent=$c->id_parent;
		$c->suppr();
		$js="
			$('#sel_tree').dynatree('getTree').reload();
			$('#ed_tree').dynatree('getTree').getNodeByKey('$id').remove();
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
		$c=new Categorie($id_parent);
		while ($c->id!=0){
			$js.="
			$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id."').data.title='".addslashes(Html::titre_categorie($c->id))."';
			$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id."').render();
			";
			$c=new Categorie($c->id_parent);		
		}
		foreach($casquettes as $id_cas=>$nom_cas){
			
			#on rend le cache obsolete
			Cache::set_obsolete('casquette',$id_cas);
		
			$js.="
			$.post('ajax.php',{
					action:'edition/casquette',
					id_casquette:$id_cas,
					format:'html'
				},function(data){
					if(data.succes==1) $('#ed_casquette-$id_cas').html(data.html)
					eval(data.js);
					ed_scapi.reinitialise();
				},
				'json'
			);
			";
		}
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
