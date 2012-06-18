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
			title:'Impossible de désassocier de la liste.',
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
		$id_casquette=$_POST['id_casquette'];
		$id_categorie=$_POST['id_categorie'];
		$c=new Casquette($id_casquette);
		$c->deass_categorie($id_categorie, $_SESSION['user']['id']);
		$id_etablissement=$c->id_etablissement;
		$e=new Etablissement($id_etablissement);
	
		#on rend le cache obsolete
		Cache::set_obsolete('casquette',$id_casquette);
		Cache::set_obsolete('etablissement',$id_etablissement);
		$js="";
		foreach($e->casquettes() as $id_cas=>$cas){
			Cache::set_obsolete('casquette',$id_cas);
			$js.="
			$('#ed_casquette-$id_cas').html('".json_escape(Html::casquette($id_cas))."');
			";
			$js.=Js::casquette($id_cas);
			$js.="
			ed_scapi.reinitialise();
			";	
		}
	
		$js.="
		$('#ed_casquette-$id_casquette').html('".json_escape(Html::casquette($id_casquette))."');
		";
		$js.=Js::casquette($id_casquette);
		$js.="
		ed_scapi.reinitialise();
		$('#ed_etablissement-$id_etablissement').html('".json_escape(Html::etablissement($id_etablissement))."');
		";
		$js.=Js::etablissement($id_etablissement);
		$js.="
		ed_ssapi.reinitialise();
		";
		$c=new Categorie($id_categorie);
		while ($c->id!=0){
			$js.="
			$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id."').data.title='".json_escape(Html::titre_categorie($c->id))."';
			$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id."').render();
			";
			$js.="
			$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id."').data.title='".json_escape(Html::titre_categorie($c->id))."';
			$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id."').render();
			";
			$c=new Categorie($c->id_parent);
		}
		$js.="
		sel_scatapi.reinitialise();
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
	}
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
