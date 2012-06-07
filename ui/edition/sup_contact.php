<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if ($_SESSION['user']['droits']<3){
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
		$casquettes=$c->casquettes;
		$js="";
		$js.="
		if ($('#ed_contact-$id_contact').length!=0){
			$('#ed_contact-$id_contact').remove();
			ed_scapi.reinitialise();
		}
		if ($('#rncont$id_contact').length!=0) $('#rncont$id_contact').dialog('close');
		";
		foreach($casquettes as $id_casquette=>$casquette){
			$cas=new Casquette($id_casquette);
			$js.="
			if ($('#mcas$id_casquette').length!=0) $('#mcas$id_casquette').dialog('close');
			if ($('#rncas$id_casquette').length!=0) $('#rncas$id_casquette').dialog('close');
			if ($('#ed_etablissement-".$cas->id_etablissement."').length!=0)
			$.post('ajax.php',{action:'edition/etablissement', id_etablissement:".$cas->id_etablissement.",format:'html'},function(data){
				if(data.succes==1){
					$('#ed_etablissement-".$cas->id_etablissement."').html(data.html);
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
			foreach($cas->categories as $id_categorie=>$categorie){
				$cat=new Categorie($id_categorie);
				$js.="
				$.post('ajax.php',{
						action:'edition/nbincat',
						id_categorie:".$cat->id.",
						format:'html'
					},function(data){
						if(data.succes==1){
							$('#ed_dynatree-id-".$cat->id."').find('.nbincat').first().html('('+data.html+')');
							ed_scatapi.reinitialise();
							$('#sel_dynatree-id-".$cat->id."').find('.nbincat').first().html('('+data.html+')');
							sel_scatapi.reinitialise();
						}
					},
					'json'
				);
				";
				while ($cat->id_parent!=0){
					$cat=new Categorie($c->id_parent);
					$js.="
					$.post('ajax.php',{
							action:'edition/nbincat',
							id_categorie:".$cat->id.",
							format:'html'
						},function(data){
							if(data.succes==1){
								$('#ed_dynatree-id-".$cat->id."').find('.nbincat').first().html('('+data.html+')');
								ed_scatapi.reinitialise();
								$('#sel_dynatree-id-".$cat->id."').find('.nbincat').first().html('('+data.html+')');
								sel_scatapi.reinitialise();
							}
						},
						'json'
					);
					";
				}
			}
		}
		$c->suppr($_SESSION['user']['id']);
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
