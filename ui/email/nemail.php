<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$form=new formulaires;
	$form->prefixe="nemail";
	
	$form->ajoute_entree('sujet', 'texte_court', '', '', array(1), 'Sujet');
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'email/aj_email');
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
$('#nemail .bouton').button();
$('#nemail input').focus();
";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']='Nouvel email';
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
