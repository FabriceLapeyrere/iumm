<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_modele'];
	$modele=Newsletters::modele($id);	
	$nom=Newsletters::nom_modele($id);	
	$form=new formulaires;
	$form->prefixe="mmodele$id";
	
	$form->ajoute_entree('id', 'hidden', $id, '', array(1));
	$form->ajoute_entree('nom', 'texte_court', $nom, '', array(1),'Nom');
	$form->ajoute_entree('modele', 'texte_long', $modele, '', array(1),'Modèle');
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'news/mod_modele');
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
$('mmodele$id .bouton').button();
";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']=$nom;
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
