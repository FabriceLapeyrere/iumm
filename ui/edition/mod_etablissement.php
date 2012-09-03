<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$message="";
	$succes=1;
	if ($_SESSION['user']['droits']<2){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de modifier l\'établissement.',
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
	
		
		$donnees=array();
		foreach($_POST as $nom=>$entree){
			if ($nom!='action' && $nom!='id') {
				$donnees[$nom][2]=$entree['type'];
				$donnees[$nom][0]=$entree['valeur'];
			}
		}
		$e= new Etablissement($id);
		$etout=$e->tout();
		$tab_cas=$etout['casquettes'];
		#on rend le cache obsolete
		Cache::set_obsolete('etablissement',$id);
		Cache::set_obsolete('structure',$etout['structure']['id']);
		Cache::set_obsolete('casquette',$etout['casquette_propre']);
		Cache::set_obsolete('casquette_sel',$etout['casquette_propre']);
		$old=$etout['donnees'];
		foreach ($donnees as $nom=>$donnee){
			$test=0;
			$message.="$nom =>";
			if ($old[$nom][0] == $donnee[0]) $message.="inchangée.";
			else {
				$message.="à changer.";
				$test=1;
			}
			$message.="<br />";
			if ($test==1) {
				if (is_array($donnee[0])) $valeur=json_encode($donnee[0]);
				else $valeur=$donnee[0];
				$label=$old[$nom][1];
				$type=$donnee[2];
				$e->aj_donnee($nom,$label,$type,$valeur, $_SESSION['user']['id']);
			}
		}
		$js_cas="";
		if (is_array($tab_cas)){
			foreach($tab_cas as $id_cas){
	
				#on rend le cache obsolete
				Cache::set_obsolete('casquette',$id_cas);
				Cache::set_obsolete('casquette_sel',$id_cas);
			
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
			}
		}
		$js="
			$.post('ajax.php',{
					action:'edition/etablissement',
					id_etablissement:$id,
					format:'html'
				},function(data){
					if(data.succes==1) { 
						$('#ed_etablissement-$id').html(data.html);
						$js_cas
					}
					eval(data.js);
					ed_ssapi.reinitialise();
				},
				'json'
			);
			$('#metab$id').dialog('close');
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
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']=$message;
		$reponse['html']="";
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
