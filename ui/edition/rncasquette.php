<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_casquette'];
	$c= new Casquette($id);	
	$form=new formulaires;
	$form->prefixe="rncas$id";
	
	$form->ajoute_entree('id', 'hidden', $id, '', array(1));
	$form->ajoute_entree('nom', 'texte_court', $c->nom, '', array(1), "Nom de la casquette");
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'edition/ren_casquette');
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
$('#rncas$id .bouton').button();
$('#rncas$id input').focus();
$('#rncas$id input').select();

";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']=$c->prenom_contact." ".$c->nom_contact.", ".$c->nom;
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
