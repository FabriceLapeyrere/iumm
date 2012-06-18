<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$form=new formulaires;
	$id=$_POST['id_utilisateur'];
	$form->prefixe="mutilisateur$id";
	$u=new Utilisateur($id);
	switch($u->droits()){
		case 1:
			$select='1,*Lecture seule::2,+ écriture::3,+ mailing::4,+ admin';
			break;
		case 2:
			$select='1,Lecture seule::2,*+ écriture::3,+ mailing::4,+ admin';
			break;
		case 3:
			$select='1,Lecture seule::2,+ écriture::3,*+ mailing::4,+ admin';
			break;
		case 4:
			$select='1,Lecture seule::2,+ écriture::3,+ mailing::4,*+ admin';
			break;
	}
	
	$form->ajoute_entree('id_utilisateur', 'hidden', $id, '', array(1), '');
	$form->ajoute_entree('nom', 'texte_court', $u->nom(), '', array(1), 'Nom');
	$form->ajoute_entree('login', 'texte_court', $u->login(), '', array(1), 'Login');
	$form->ajoute_entree('mdp', 'mdp', '#####', '', array(1), 'Mot de passe');
	$form->ajoute_entree('mdp2', 'mdp', '#####', '', array(1), 'Confirmation du mot de passe');
	$form->ajoute_entree('droits', 'select', $select, '', array(1), "Droits");
	$html="";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'admin/mod_utilisateur');
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
$('#nutilisateur .bouton').button();
$('#nutilisateur input').focus();
";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']='Nouvel utilisateur';
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
