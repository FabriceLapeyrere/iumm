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
			title:'Impossible d\'associer à l\'établissement.',
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
		$id_etablissement=$_POST['id_etablissement'];
	
		$js="";
		if (Casquettes::existe($id_casquette) and Etablissements::existe($id_etablissement)) {
			#on teste si une association existe pour cette casquette
			$c=new Casquette($id_casquette);
			$old_etablissement=$c->id_etablissement();
			if ($old_etablissement!=$id_etablissement) {
				$c->ass_etablissement($id_etablissement, $_SESSION['user']['id']);
				#On supprime le cache du nouvel etablissement
				Cache::set_obsolete('etablissement',$id_etablissement);
				$e=new etablissement($id_etablissement);
				$id_propre=$e->casquette_propre();
				Cache::set_obsolete('casquette',$id_propre);
				Cache::set_obsolete('casquette_sel',$id_propre);
	
				#On met à jour le nom de la casquette
				$e=new Etablissement($id_etablissement);
				$etout=$e->tout();
				$nom_structure=$etout['structure']['nom'];
				$c->mod_nom($nom_structure, $_SESSION['user']['id']);
				$js.="$('#edition li[data-tab=\"#ed_casquette-$id_casquette\"] a').html('".json_escape($nom_structure)."');";
				#On met à jour contact et casquette_sel dans 5s
				async('ui/cache/cache',array('objet'=>'casquette_sel','id_objet'=>$id_casquette));
				$contact=$c->contact();
				async('ui/cache/cache',array('objet'=>'contact','id_objet'=>$contact['id']));
				#On met à jour la casquette
				Cache::set_obsolete('casquette',$id_casquette);
				$js.="
				$('#ed_casquette-$id_casquette').html('".json_escape(Html::casquette($id_casquette))."');
				".Js::casquette($id_casquette)."
				ed_scapi.reinitialise();
				";
				#on met à jour le cache des autres casquettes de l'etablissement dans 5s
				$js_delai="";
				foreach($etout['casquettes'] as $id_cas){
					if ($id_cas!=$id_casquette && $id_cas>0) {
						async('ui/cache/cache',array('objet'=>'casquette','id_objet'=>$id_cas));
						async('ui/cache/cache',array('objet'=>'casquette_sel','id_objet'=>$id_cas));
						$js_delai.="
						if ($('#ed_casquette-$id_cas').length>0) {
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
						}
						";
					}
				}
				#on met à jour le cache des autres casquettes de l'ancien etablissement dans 5s				
				if ($old_etablissement>0){
					#on met à jour le cache de l'ancien établissement (etablissement + casquette propre) dans 5s
					async('ui/cache/cache',array('objet'=>'etablissement','id_objet'=>$old_etablissement));
					$old=new Etablissement($old_etablissement);
					async('ui/cache/cache',array('objet'=>'casquette_sel','id_objet'=>$old->casquette_propre()));
					$e=new Etablissement($old_etablissement);
					foreach($e->casquettes() as $id_cas){
						if ($id_cas>0) {
							async('ui/cache/cache',array('objet'=>'casquette','id_objet'=>$id_cas));
							async('ui/cache/cache',array('objet'=>'casquette_sel','id_objet'=>$id_cas));
							$js_delai.="
							if ($('#ed_casquette-$id_cas').length>0) {
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
							}
							";
						}
					}
				}
				$js.="
				$('#ed_etablissement-$id_etablissement').html('".json_escape(Html::etablissement($id_etablissement))."');
				".Js::etablissement($id_etablissement)."
				ed_ssapi.reinitialise();
				";
				if ($old_etablissement>0 && $old_etablissement!=$id_etablissement) {
					$js_delai.="
					$.post('ajax.php',{
							action:'edition/etablissement',
							id_etablissement:$old_etablissement,
							format:'html'
						},function(data){
							if(data.succes==1) $('#ed_etablissement-$old_etablissement').html(data.html)
							eval(data.js);
							ed_ssapi.reinitialise();
						},
						'json'
					);
					";
				}
				$js_delai.="
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
				$js.="
					var action=function(){
						$js_delai
					}
					setTimeout(action,3000);
				";
			}			
		} else $succes=0;
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
