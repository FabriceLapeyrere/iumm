<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$form=new formulaires;
	$form->prefixe="nmodele";
	
	$form->ajoute_entree('nom', 'texte_court', '', '', array(1),'Nom');
	$form->ajoute_entree('modele', 'texte_long', '', '', array(1),'Modèle');
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'news/aj_modele');
	$html.="<ul>";
	foreach ($form->entrees as $nom => $value) {
		$html.="<li data-nom=\"$nom\" data-type=\"".$value['type']."\">".$value['html']."</li>";
	}
	$html.="</ul>";
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
$('#nmodele .bouton').button();
";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']="Nouveau modèle";
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
