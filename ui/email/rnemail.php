<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_email'];
	$e= new Email($id);	
	$form=new formulaires;
	$form->prefixe="rnemail$id";
	
	$form->ajoute_entree('id', 'hidden', $id, '', array(1));
	$form->ajoute_entree('sujet', 'texte_court', $e->sujet, 'Sujet', array(1));
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'email/ren_email');
	foreach ($form->entrees as $nom => $value) {
		$html.=$value['html'];
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
	$js.="
$('rnemail$id .bouton').button();
";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']=$e->sujet;
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
