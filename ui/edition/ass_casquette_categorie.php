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
			title:'Impossible d\'associer à la liste.',
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
		$c->ass_categorie($id_categorie, $_SESSION['user']['id']);
		
		#on rend le cache obsolete
		Cache::set_obsolete('casquette_sel',$id_casquette);
		Cache::set_obsolete('casquette',$id_casquette);
		$cat=new Categorie($id_categorie);
		$nom_cat=$cat->nom();
		$js="";
		
		$contact=$c->contact();
		if ($contact['nom']!="$$$$") {
			Cache::set_obsolete('contact',$contact['id']);
			$js.="
			$('#ed_casquette-$id_casquette').html('".json_escape(Html::casquette($id_casquette))."');
			";
			$js.=Js::casquette($id_casquette);
			$js.="
			ed_scapi.reinitialise();
			";
		} else {
			$id_etablissement=$c->id_etablissement();
			$id_structure=$c->id_structure();
			Cache::set_obsolete('etablissement',$id_etablissement);
			Cache::set_obsolete('structure',$id_structure);
			$js.="
			$('#ed_etablissement-$id_etablissement').html('".json_escape(Html::etablissement($id_etablissement))."');
			";
			$js.=Js::etablissement($id_etablissement);
			$js.="
			ed_ssapi.reinitialise();
			";
			$e=new etablissement($id_etablissement);
			foreach($e->casquettes() as $id_cas){
				if($id_cas>0) {
					$c=new casquette($id_cas);		
					Cache::set_obsolete('contact',$c->id_contact());
					Cache::set_obsolete('casquette',$id_cas);
					Cache::set_obsolete('casquette_sel',$id_cas);
					$js.="
					$('#ed_casquette-$id_cas').html('".json_escape(Html::casquette($id_cas))."');
					";
					$js.=Js::casquette($id_cas);
					$js.="
					ed_scapi.reinitialise();
					";
				}
			}
		}
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
