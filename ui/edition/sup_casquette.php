<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id_casquette=$_POST['id_casquette'];
	$c=new Casquette($id_casquette);
	$id_etablissement=$c->id_etablissement;
	$id_contact=$c->id_contact;
	$cont=new Contact($id_contact);
	$nb=count($cont->casquettes);
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
		$liste=$c->suppr();
	
		#on rend le cache obsolete
		Cache::set_obsolete('etablissement',$id_etablissement);

		$js="";
		$js.="
			if ($('#ed_contact-$id_contact').length!=0)$.post('ajax.php',{action:'edition/contact',id_contact:$id_contact,format:'html'},function(data){
				if(data.succes==1){
					$('#ed_contact-$id_contact').html(data.html)
					eval(data.js);
					ed_scapi.reinitialise();
				}
			},'json');
			if ($('#mcas$id_casquette').length!=0) $('#mcas$id_casquette').dialog('close');
			if ($('#rncas$id_casquette').length!=0) $('#rncas$id_casquette').dialog('close');
			if ($('#ed_etablissement-".$c->id_etablissement."').length!=0)
			$.post('ajax.php',{action:'edition/etablissement', id_etablissement:".$c->id_etablissement.",format:'html'},function(data){
				if(data.succes==1){
					$('#ed_etablissement-".$c->id_etablissement."').html(data.html);
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
		foreach($c->categories as $id_categorie=>$categorie){
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
			while ($cat->id_parent!=0){
				$cat=new Categorie($cat->id_parent);
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
