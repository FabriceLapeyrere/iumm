<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_expediteur']['valeur'];
	$nom=$_POST['nom']['valeur'];
	$email=$_POST['email']['valeur'];
	Emailing::mod_expediteur($id,$nom,$email);
	$retour=Html::expediteurs($_SESSION['admin']['binfx'],$_SESSION['admin']['motifsx']);
	$html=json_escape($retour['html']);
	$js="
	$('#mexpediteur$id').remove();
	$('#admin_expediteurs .jspPane').html('$html');
	admin_expediteurs();
	";
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
