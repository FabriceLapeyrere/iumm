<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$form=new formulaires; 
	$id_support=$_POST['id_support'];
	$form->prefixe="msup$id_support";
	$s=Publipostage::support($id_support);
	$form->ajoute_entree('id', 'hidden', $id_support, '', array(1));
	$form->ajoute_entree('h_page', 'entier', $s['h_page'], '', array(1), 'Hauteur de la page.');
	$form->ajoute_entree('l_page', 'entier', $s['l_page'], '', array(1), 'Largeur de la page.');
	$form->ajoute_entree('nb_lignes', 'entier', $s['nb_lignes'], '', array(1), 'Nombre de lignes.');
	$form->ajoute_entree('nb_colonnes', 'entier', $s['nb_colonnes'], '', array(1), 'Nombre de colonnes.');
	$form->ajoute_entree('mp_gauche', 'entier', $s['mp_gauche'], '', array(1), 'Marge de page (gauche).');
	$form->ajoute_entree('mp_droite', 'entier', $s['mp_droite'], '', array(1), 'Marge de page (droite).');
	$form->ajoute_entree('mp_haut', 'entier', $s['mp_haut'], '', array(1), 'Marge de page (haut).');
	$form->ajoute_entree('mp_bas', 'entier', $s['mp_bas'], '', array(1), 'Marge de page (bas).');
	$form->ajoute_entree('mc_gauche', 'entier', $s['mc_gauche'], '', array(1), 'Marge de case (gauche).');
	$form->ajoute_entree('mc_droite', 'entier', $s['mc_droite'], '', array(1), 'Marge de case (droite).');
	$form->ajoute_entree('mc_haut', 'entier', $s['mc_haut'], '', array(1), 'Marge de case (haut).');
	$form->ajoute_entree('mc_bas', 'entier', $s['mc_bas'], '', array(1), 'Marge de case (bas).');
	$form->ajoute_entree('police', 'entier', $s['police'], '', array(1), 'Taille de la police.');
	$form->ajoute_entree('tpl', 'texte_long', $s['tpl'], '', array(1), 'Modele.');
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'publipostage/mod_support');
	$html.="<ul>";
	foreach ($form->entrees as $nom => $value) {
		$html.='<li>'.$value['html'].'</li>';
	}
	$html.="</ul><br/>";
	$html.=$form->interrupteurs['valider']['html'];
	$js.=$form->initjs();
	foreach ($form->entrees as $key => $value) {
		$js.=$value['js'];
	}
	foreach ($form->interrupteurs as $key => $value) {
		$js.=$value['js'];
	}
	$js.="
$('#msup.bouton').button();
$('#msup input').focus();
";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']=$s['nom'];
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
