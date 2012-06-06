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
	
		#on rend le cache obsolete
		Cache::set_obsolete('casquette',$id_casquette);
		Cache::set_obsolete('etablissement',$id_etablissement);
		$js="";
		if (Casquettes::existe($id_casquette) and Etablissements::existe($id_etablissement)) {
			#on teste si une association existe pour cette casquette
			$c=new Casquette($id_casquette);
			$old_etablissement=$c->id_etablissement;
			Cache::set_obsolete('etablissement',$old_etablissement);
			if ($old_etablissement!=$id_etablissement) {
				$c->ass_etablissement($id_etablissement, $_SESSION['user']['id']);
			}
			$c=new Casquette($id_casquette);
			$c->mod_nom($c->nom_structure, $_SESSION['user']['id']);
			$e=new Etablissement($id_etablissement);
			$js.="$('#edition li[data-tab=\"#ed_casquette-$id_casquette\"] a').html('".json_escape($c->nom_structure)."');";
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
			$e=new Etablissement($old_etablissement);
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
			$('#ed_etablissement-$id_etablissement').html('".json_escape(Html::etablissement($id_etablissement))."');
			";
			$js.=Js::etablissement($id_etablissement);
			$js.="
			ed_ssapi.reinitialise();
			";
			if ($old_etablissement>0 && $old_etablissement!=$id_etablissement) {
				$js.="$('#ed_etablissement-$old_etablissement').html('".json_escape(Html::etablissement($old_etablissement))."');
				";
				$js.=Js::etablissement($old_etablissement);
				$js.="
				ed_ssapi.reinitialise();
				";
			}
			$js.="
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
