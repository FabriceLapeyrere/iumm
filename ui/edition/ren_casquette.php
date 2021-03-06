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
			title:'Impossible de renommer la casquette.',
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

		#on rend le cache obsolete
		Cache::set_obsolete('casquette',$id);
		Cache::set_obsolete('casquette_sel',$id);
		
		$c=new Casquette($id);
		$c->mod_nom($nom, $_SESSION['user']['id']);
		$js="
			$('li[data-tab=\"#ed_casquette-$id\"] a').html('".addslashes($nom)."');
			if ($('#mcas$id').length>0)
			$.post('ajax.php',{action:'edition/mcasquette', id_casquette:$id},function(data){
				$('#mcas$id').dialog('option',{title:data.titre});
			},'json');
			$('#rncas$id').dialog('close');
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
		$id_etablissement=$c->id_etablissement();
		if ($id_etablissement!=0){
			Cache::set_obsolete('etablissement',$id_etablissement);
			$js.="
			$('#ed_etablissement-$id_etablissement').html('".json_escape(Html::etablissement($id_etablissement))."');
			";
			$js.=Js::etablissement($id_etablissement);
			$js.="
			ed_ssapi.reinitialise();
			";
			$e=new Etablissement($id_etablissement);
			foreach($e->casquettes() as $id_cas){
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
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
