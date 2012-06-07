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
			title:'Impossible de renommer la liste.',
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
		$id=$_POST['id']['valeur'];
		$nom=$_POST['nom']['valeur'];
	
		$c=new Categorie($id);
		$c->mod_nom($nom, $_SESSION['user']['id']);
		$casquettes=$c->casquettes();
		$js="
			$.post('ajax.php',{action:'edition/categorie', id_categorie:$id},function(data){
				var node=$('#ed_tree').dynatree('getTree').getNodeByKey('$id');
				node.data.title=data.titre;
				node.render();
				$('#ed_tree').dynatree('getTree').getNodeByKey('".$c->id_parent."').sortChildren();
				node=$('#sel_tree').dynatree('getTree').getNodeByKey('$id');
				node.data.title=data.titre;
				node.render();
				$('#sel_tree').dynatree('getTree').getNodeByKey('".$c->id_parent."').sortChildren();
				$('#rncat$id').remove();
			},'json');
			$.post('ajax.php',{
				action:'edition/nbincat',
				id_categorie:$id,
				format:'html'
				},
				function(data){
					if (data.succes==1) {
						$('#ed_dynatree-id-$id').find('.nbincat').first().html('('+data.html+')');
						ed_scatapi.reinitialise();
						$('#sel_dynatree-id-$id').find('.nbincat').first().html('('+data.html+')');
						sel_scatapi.reinitialise();
					}
				},
				'json'
			);
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
			if ($c->id_parent!=0) {
				$js.="
				$.post('ajax.php',{
					action:'edition/nbincat',
					id_categorie:".$c->id_parent.",
					format:'html'
					},
					function(data){
						if (data.succes==1) {
							$('#ed_dynatree-id-".$c->id_parent."').find('.nbincat').first().html('('+data.html+')');
							ed_scatapi.reinitialise();
							$('#sel_dynatree-id-".$c->id_parent."').find('.nbincat').first().html('('+data.html+')');
							sel_scatapi.reinitialise();
						}
					},
					'json'
				);
				";
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
			$cas=new Casquette($id_cas);
			if ($cas->id_etablissement!=0){
				$id_etablissement=$cas->id_etablissement;
				#on rend le cache obsolete
				Cache::set_obsolete('etablissement',$id_etablissement);
				$js.="
				$.post('ajax.php',{
						action:'edition/etablissement',
						id_etablissement:$id_etablissement,
						format:'html'
					},function(data){
						if(data.succes==1) { 
							$('#ed_etablissement-$id_etablissement').html(data.html);
						}
						eval(data.js);
						ed_ssapi.reinitialise();
					},
					'json'
				);
				";
			};
		};
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
