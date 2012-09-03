<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if ($_SESSION['user']['droits']<2){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de supprimer la liste.',
			modal: true,
			dialogClass: 'css-infos',
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});
		";
	}
	else {
		$id=$_POST['id_categorie'];
		
		#on rend le cache obsolete
		Cache::set_obsolete('ed_categorie',$id);
		Cache::set_obsolete('sel_categorie',$id);
			
		$c=new Categorie($id);
		if($c->nb_enfants()==0) {
			$casquettes=$c->casquettes();
			$id_parent=$c->id_parent();
			$c->suppr($_SESSION['user']['id']);
			$js="
			sel_cat_reload=1;
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
				$c=new Categorie($c->id_parent());		
			}
			foreach($casquettes as $id_cas){
			
				#on rend le cache obsolete
				Cache::set_obsolete('casquette',$id_cas);
				Cache::set_obsolete('casquette_sel',$id_cas);
				
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
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
