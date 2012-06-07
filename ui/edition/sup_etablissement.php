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
			title:'Impossible de supprimer l\'établissement.',
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
		$id_etablissement=$_POST['id_etablissement'];
		$e=new etablissement($id_etablissement);
		$id_structure=$e->id_structure;
		$tab_cas=$e->casquettes();
		$str=new Structure($id_structure);
		$nb=count($str->etablissements);
		$js="";
		if ($nb==1){
			$js.="
			$('<div>Impossible de supprimer l'établissement : une structure doit toujours contenir au moins un établissement.</div>').dialog({
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
			$e->suppr($_SESSION['user']['id']);
			$js="";
			$js.="
				if ($('#ed_structure-$id_structure').length!=0)$.post('ajax.php',{action:'edition/structure',id_structure:$id_structure,format:'html'},function(data){
					if(data.succes==1){
						$('#ed_structure-$id_structure').html(data.html)
						eval(data.js);
						ed_scapi.reinitialise();
					}
				},'json');
				if ($('#metab$id_etablissement').length!=0) $('#metab$id_etablissement').dialog('close');
				if ($('#rnetab$id_etablissement').length!=0) $('#rnetab$id_etablissement').dialog('close');
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
			foreach($tab_cas as $id_cas=>$nom_cas){
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
