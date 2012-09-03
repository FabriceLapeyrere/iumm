<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_contact'];
	$c= new Contact($id);	
	$form=new formulaires;
	$form->prefixe="rncont$id";
	
	$form->ajoute_entree('id', 'hidden', $id, '', array(1));
	$form->ajoute_entree('nom', 'texte_court', $c->nom(), '', array(1), 'Nom');
	$form->ajoute_entree('prenom', 'texte_court', $c->prenom(), '', array(1), 'Prénom');
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'edition/ren_contact');
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
	$js.="
$('#rncont$id .bouton').button();
";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']=$c->prenom()." ".$c->nom();
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
