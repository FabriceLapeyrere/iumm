<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_utilisateur'];
	$u=new Utilisateur($id);
	$u->suppr();
	$retour=Html::utilisateurs($_SESSION['admin']['binf'],$_SESSION['admin']['motifs']);
	$html=json_escape($retour['html']);
	$js="
		$('#mutilisateur$id').remove();
		$('#admin_utilisateurs .jspPane').html('$html');
		admin_utilisateurs();
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
