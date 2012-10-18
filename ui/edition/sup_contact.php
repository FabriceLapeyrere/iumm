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
			title:'Impossible de supprimer le contact.',
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
		$id_contact=$_POST['id_contact'];
		$c=new Contact($id_contact);
		$casquettes=$c->casquettes();
		$js="";
		$js.="
		if ($('#ed_contact-$id_contact').length!=0){
			$('#ed_contact-$id_contact').remove();
			ed_scapi.reinitialise();
		}
		if ($('#rncont$id_contact').length!=0) $('#rncont$id_contact').dialog('close');
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
		$cats=array();
		foreach($casquettes as $id_casquette){
			$cas=new Casquette($id_casquette);
			$id_etablissement=$cas->id_etablissement();
			Cache::set_obsolete('etablissement',$id_etablissement);
			$js.="
			if ($('#mcas$id_casquette').length!=0) $('#mcas$id_casquette').dialog('close');
			if ($('#rncas$id_casquette').length!=0) $('#rncas$id_casquette').dialog('close');
			if ($('#ed_etablissement-$id_etablissement').length!=0)
			$.post('ajax.php',{action:'edition/etablissement', id_etablissement:$id_etablissement,format:'html'},function(data){
				if(data.succes==1){
					$('#ed_etablissement-$id_etablissement').html(data.html);
					eval(data.js);
					ed_ssapi.reinitialise();
				}
			},'json');
			";
			if ($id_etablissement!=0) {
				$e=new Etablissement($id_etablissement);
				foreach($e->casquettes() as $id_cas){
					Cache::set_obsolete('casquette',$id_cas);
					$js.="
						$.post('ajax.php',{action:'edition/casquette', id_casquette:$id_cas,format:'html'},function(data){
							if(data.succes==1){
								$('#ed_casquette-$id_cas').html(data.html);
								eval(data.js);
								ed_ssapi.reinitialise();
							}
						},'json');
			
					";
				}
			}
			$cats=array_merge($cats,$cas->categories());
		}
		$c->suppr($_SESSION['user']['id']);
		foreach($cats as $id_categorie){
			$cat=new Categorie($id_categorie);	
			while ($cat->id!=0){
				Cache::set_obsolete('ed_categorie',$cat->id);
				Cache::set_obsolete('sel_categorie',$cat->id);
				$js.="
				if ($('#ed_tree').dynatree('getTree').getNodeByKey('".$cat->id."')) {
				$('#ed_tree').dynatree('getTree').getNodeByKey('".$cat->id."').data.title='".json_escape(Html::titre_categorie($cat->id))."';
				$('#ed_tree').dynatree('getTree').getNodeByKey('".$cat->id."').render();
				}
				";
				$js.="
				if ($('#sel_tree').dynatree('getTree').getNodeByKey('".$cat->id."')) {
				$('#sel_tree').dynatree('getTree').getNodeByKey('".$cat->id."').data.title='".json_escape(Html::titre_categorie($cat->id))."';
				$('#sel_tree').dynatree('getTree').getNodeByKey('".$cat->id."').render();
				}
				";
				$cat=new Categorie($cat->id_parent());
			}
		}
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
