<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$message="";
	$succes=1;
	$id=$_POST['id']['valeur'];
	
	#on rend le cache obsolete
	Cache::set_obsolete('etablissement',$id);
	
	$donnees=array();
	foreach($_POST as $nom=>$entree){
		if ($nom!='action' && $nom!='id') {
			$donnees[$nom]['type']=$entree['type'];
			$donnees[$nom]['valeur']=$entree['valeur'];
		}
	}
	$e= new Etablissement($id);
	$tab_cas=$e->casquettes();
	$old=$e->donnees();
	foreach ($donnees as $nom=>$donnee){
		$test=0;
		$message.="$nom =>";
		if ($old[$nom]['valeur'] == $donnee['valeur']) $message.="inchangée.";
		else {
			$message.="à changer.";
			$test=1;
		}
		$message.="<br />";
		if ($test==1) {
			if (is_array($donnee['valeur'])) $valeur=json_encode($donnee['valeur']);
			else $valeur=$donnee['valeur'];
			$label=$old[$nom]['label'];
			$type=$donnee['type'];
			$e->aj_donnee($nom,$label,$type,$valeur);
		}
	}
	$js_cas="";
	foreach($tab_cas as $id_cas=>$nom_cas){
	
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
