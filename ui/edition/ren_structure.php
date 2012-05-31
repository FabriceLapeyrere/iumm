<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
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
		foreach($casquettes as $id_cas=>$nom_cas){
	
			#on rend le cache obsolete
			Cache::set_obsolete('casquette',$id_cas);
		
			$js_cas.="
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
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
