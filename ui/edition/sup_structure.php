<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id_structure=$_POST['id_structure'];
	$s=new Structure($id_structure);
	$etablissements=$s->etablissements;
	$js="";
	$js.="
	if ($('#ed_structure-$id_structure').length!=0){
		$('#ed_structure-$id_structure').remove();
		ed_ssapi.reinitialise();
	}
	if ($('#rnstr$id_structure').length!=0) $('#rnstr$id_structure').dialog('close');
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
		$etab=new Etablissement($id_etablissement);
		$js.="
		if ($('#metab$id_etablissement').length!=0) $('#metab$id_etablissement').dialog('close');
		if ($('#rnetab$id_etablissement').length!=0) $('#rnetab$id_etablissement').dialog('close');
		";
		$tab_cas=$etab->casquettes();
		foreach($tab_cas as $id_casquette=>$casquette){
			#on rend le cache obsolete
			Cache::set_obsolete('casquette',$id_casquette);
		
			$js.="
			if ($('#ed_casquette-$id_casquette').length!=0)
			$.post('ajax.php',{action:'edition/casquette', id_casquette:$id_casquette,format:'html'},function(data){
				if(data.succes==1){
					$('#ed_casquette-$id_casquette').html(data.html);
					eval(data.js);
					ed_scapi.reinitialise();
				}
			},'json');
			";
		}
	}
	$s->suppr($_SESSION['user']['id']);
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
