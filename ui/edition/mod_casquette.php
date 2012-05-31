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
	Cache::set_obsolete('casquette',$id);
	
	$donnees=array();
	foreach($_POST as $nom=>$entree){
		if ($nom!='action' && $nom!='id') {
			$donnees[$nom]['type']=$entree['type'];
			$donnees[$nom]['valeur']=$entree['valeur'];
		}
	}
	$c= new Casquette($id);	
	$old=$c->donnees();
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
			$c->aj_donnee($nom,$label,$type,$valeur, $_SESSION['user']['id']);
		}
	}
	$js="
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
		$.post('ajax.php',{
				action:'selection/casquette',
				id_casquette:$id,
				format:'html'
			},function(data){
				if(data.succes==1) $('#sel_casquette-$id').html(data.html)
				eval(data.js);
			},
			'json'
		);
		$('#mcas$id').dialog('close');
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
