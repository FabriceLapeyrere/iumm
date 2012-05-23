<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id']['valeur'];
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
		if (isset($old[$nom])) {
			$message.="déjà là, ";
			if ($old[$nom]['valeur'] == $donnee['valeur']) $message.="inchangée.";
			else $message.="à changer.";
		}
		else $message.="à créer. ";
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
