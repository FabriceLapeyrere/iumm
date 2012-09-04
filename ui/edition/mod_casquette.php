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
			title:'Impossible de modifier la casquette.',
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
		$etab=0;
		$change=0;
		
		$donnees=array();
		foreach($_POST as $nom=>$entree){
			if ($nom!='action' && $nom!='id') {
				$donnees[$nom][2]=$entree['type'];
				$donnees[$nom][0]=$entree['valeur'];
			}
		}
		$c= new Casquette($id);	
		$old=$c->donnees();

		foreach ($donnees as $nom=>$donnee){
			$test=0;
			if (is_array($donnee[0])) $valeur=json_encode($donnee[0]);
			else $valeur=$donnee[0];
			if ($old[$nom][0] != $valeur) {
				$message.="$nom mis(e) à jour.<br />";
				$test=1;
			}
			if ($test==1) {
				$change=1;
				if (is_array($donnee[0])) $valeur=json_encode($donnee[0]);
				else $valeur=$donnee[0];
				$label=$old[$nom][1];
				$type=$donnee[2];
				$c->aj_donnee($nom,$label,$type,$valeur, $_SESSION['user']['id']);
				if ($nom=='Fonction') $etab=1;
			}
		}
		
		$js="
			$('#mcas$id').dialog('close');
		";
		if ($change==1) {	
			#on rend le cache obsolete
			Cache::set_obsolete('casquette',$id);
			Cache::set_obsolete('casquette_sel',$id);
			$contact=$c->contact();
			Cache::set_obsolete('contact',$contact['id']);
			$js.="
				$.post('ajax.php',{
						action:'edition/casquette',
						id_casquette:$id,
						format:'html'
					},function(data){
						if(data.succes==1) $('#ed_casquette-$id').html(data.html)
						eval(data.js);
						ed_scapi.reinitialise();
					},
					'json'
				);
			";
			if ($c->id_etablissement()>0 && $etab==1){
				$id_etablissement=$c->id_etablissement();
				$id_structure=$c->id_structure();
				$e=new etablissement($id_etablissement);
				$id_propre=$e->casquette_propre();
				#on rend le cache obsolete
				Cache::set_obsolete('etablissement',$id_etablissement);
				Cache::set_obsolete('structure',$id_structure);
				Cache::set_obsolete('casquette',$id_propre);
				Cache::set_obsolete('casquette_sel',$id_propre);
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
				foreach ($e->casquettes() as $id_cas) {
					Cache::set_obsolete('casquette',$id_cas);
					Cache::set_obsolete('casquette_sel',$id_cas);
					$js.="
					$.post('ajax.php',{
							action:'edition/casquette',
							id_casquette:$id_cas,
							format:'html'
						},function(data){
							if(data.succes==1) { 
								$('#ed_casquette-$id_cas').html(data.html);
							}
							eval(data.js);
							ed_ssapi.reinitialise();
						},
						'json'
					);
					";
				}
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
		}
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
