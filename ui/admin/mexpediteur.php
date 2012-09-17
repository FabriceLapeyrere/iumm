<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$form=new formulaires;
	$id=$_POST['id_expediteur'];
	$form->prefixe="mexpediteur$id";
	$e=Emailing::expediteur($id);
	$form->ajoute_entree('id_expediteur', 'hidden', $id, '', array(1), '');
	$form->ajoute_entree('nom', 'texte_court', $e['nom'], '', array(1), 'Nom');
	$form->ajoute_entree('email', 'email', $e['email'], '', array(1), 'Email');
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'admin/mod_expediteur');
	foreach ($form->entrees as $nom => $value) {
		$html.=$value['html']."<br />";
	}
	$html.="<br/>";
	$html.=$form->interrupteurs['valider']['html'];
	$js.=$form->initjs();
	foreach ($form->entrees as $key => $value) {
		$js.=$value['js'];
	}
	foreach ($form->interrupteurs as $key => $value) {
		$js.=$value['js'];
	}	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']='Modifier '.$e['nom'];
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
