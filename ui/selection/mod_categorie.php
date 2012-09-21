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
	$c=new Categorie($id_parent);
	$test=0;
	while($c->id_parent()!=0){
		$c=new Categorie($c->id_parent());
		if ($c->id==$id) $test=1;
	}
	if ($_SESSION['user']['droits']<2){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de modifier la liste.',
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
		$reponse['move']=0;
	}
	elseif ($test==1){
		$js="
		$('<div>Impossible de déplacer.</div>').dialog({
			resizable: false,
			title:'Impossible de déplacer.',
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
		$reponse['move']=0;
	}
	else {
		$reponse['move']=1;
		
		$c=new Categorie($id);
		$id_old=$c->id_parent();
		#on rend le cache obsolete
		Cache::set_obsolete('ed_categorie',$id_old);
		Cache::set_obsolete('sel_categorie',$id_old);
		Cache::set_obsolete('ed_categorie',$id_parent);
		Cache::set_obsolete('sel_categorie',$id_parent);
		Cache::set_obsolete('ed_categorie',$id);
		Cache::set_obsolete('sel_categorie',$id);
		$c->mod_parent($id_parent, $_SESSION['user']['id']);
		$js="
		ed_cat_reload=1;
		";
		while ($c->id!=0){
			
			#on rend le cache obsolete
			Cache::set_obsolete('ed_categorie',$c->id);
			Cache::set_obsolete('sel_categorie',$c->id);
			
			$js.="
			$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id."').data.title='".addslashes(Html::titre_categorie($c->id))."';
			$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id."').render();
			";
			$c=new Categorie($c->id_parent());		
		}
		$c=new Categorie($id_parent);
		while ($c->id!=0){
			
			#on rend le cache obsolete
			Cache::set_obsolete('ed_categorie',$c->id);
			Cache::set_obsolete('sel_categorie',$c->id);
			
			$js.="
			$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id."').data.title='".addslashes(Html::titre_categorie($c->id))."';
			$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id."').render();
			";
			$c=new Categorie($c->id_parent());
		}
		$c=new Categorie($id_old);
		while ($c->id!=0){
			
			#on rend le cache obsolete
			Cache::set_obsolete('ed_categorie',$c->id);
			Cache::set_obsolete('sel_categorie',$c->id);
			
			$js.="
			$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id."').data.title='".addslashes(Html::titre_categorie($c->id))."';
			$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id."').render();
			";
			$c=new Categorie($c->id_parent());
		}
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
