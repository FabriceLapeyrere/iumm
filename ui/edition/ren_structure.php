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
			title:'Impossible de renommer la structure.',
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
	
		$s=new Structure($id);
		$s->mod_nom($nom, $_SESSION['user']['id']);
		$etablissements=$s->etablissements;
		$js="
			$('#ed_structure-$id span.titre').html('".addslashes($nom)."');
			$('#rnstr$id').remove();
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
		foreach($etablissements as $id_etablissement=>$etablissement){
			$js.="
				$.post('ajax.php',{action:'edition/metablissement', id_etablissement:$id_etablissement},function(data){
					$('.etabContact$id_etablissement span.titre').html(data.titre);
					ed_scapi.reinitialise();
				},'json');
			
			";
			$e=new Etablissement($id);
			$casquettes=$e->casquettes();
			$js_cas="";
			foreach($casquettes as $id_cas=>$nom_cas){
				$cas=new Casquette($id_cas);
				$cas->mod_nom($nom);
				#on rend le cache obsolete
				Cache::set_obsolete('casquette',$id_cas);
		
				$js_cas.="
				$('li[data-tab=\"#ed_casquette-$id\"] a').html('".addslashes($nom)."');
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
			};
			$js.=$js_cas;
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
