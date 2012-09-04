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
			title:'Impossible de supprimer la casquette.',
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
		$c=new Casquette($id_casquette);
		$id_etablissement=$c->id_etablissement();
		$id_contact=$c->id_contact();
		$cont=new Contact($id_contact);
		$nb=count($cont->casquettes());
		$js="";
		if ($nb==1){
			$js.="
			$('<div>Impossible de supprimer la casquette : un contact doit toujours contenir au moins une casquette.</div>').dialog({
				resizable: false,
				title:'Suppression impossible',
				modal: true,
				close:function(){ 
					$(this).remove();
				},
				buttons: {
					Fermer: function() {
						$(this).dialog('close');
					}
				}
			});
			";
		}
		else {
			$c->suppr($_SESSION['user']['id']);
			#on rend le cache obsolete
			$e=new etablissement($id_etablissement);
			$id_propre=$e-> casquette_propre();
			Cache::set_obsolete('etablissement',$id_etablissement);
			Cache::set_obsolete('casquette',$id_propre);
			Cache::set_obsolete('casquette_sel',$id_propre);
			Cache::set_obsolete('contact',$id_contact);

			$js="";
			$js.="
				$('#ed_contact-$id_contact').html('".json_escape(Html::Contact($id_contact))."');
				".Js::contact($id_contact)."
				ed_scapi.reinitialise();
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
			foreach($c->categories() as $id_categorie){
				$cat=new Categorie($id_categorie);
				$js.="
				$.post('ajax.php',{
						action:'edition/nbincat',
						id_categorie:".$cat->id.",
						format:'html'
					},function(data){
						if(data.succes==1){
							$('#ed_dynatree-id-".$cat->id."').find('.nbincat').first().html('('+data.html+')');
							$('#sel_dynatree-id-".$cat->id."').find('.nbincat').first().html('('+data.html+')');
							ed_scatapi.reinitialise();
							sel_scatapi.reinitialise();
						}
					},
					'json'
				);
				";
				while ($cat->id_parent()!=0){
					$cat=new Categorie($cat->id_parent());
					$js.="
					$.post('ajax.php',{
							action:'edition/nbincat',
							id_categorie:".$cat->id.",
							format:'html'
						},function(data){
							if(data.succes==1){
								$('#ed_dynatree-id-".$cat->id."').find('.nbincat').first().html('('+data.html+')');
								$('#sel_dynatree-id-".$cat->id."').find('.nbincat').first().html('('+data.html+')');
								ed_scatapi.reinitialise();
								sel_scatapi.reinitialise();
							}
						},
						'json'
					);
					";
				}
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
