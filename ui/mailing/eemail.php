<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_email'];
	$e=new Email($id);	
	$form=new formulaires;
	$form->prefixe="eemail$id";
	
	$expediteurs=Emails::expediteurs();
	$liste=Casquettes::liste('email');
	$nb_email=count($liste);
	$options=array();
	foreach($expediteurs as $id_expediteur=>$expediteur){
		$options[]="$id_expediteur,".$expediteur['nom']." &lt;".$expediteur['email']."&gt;"; 
	}
	
	$form->ajoute_entree('id', 'hidden', $id, '', array(1));
	$form->ajoute_entree('expediteur', 'select', implode('::',$options), 'Expediteur', array(1));
	$html="Choix de l'expéditeur :<br />";
	$js="";
	$form->ajoute_interrupteur('valider', 'bouton', 'Confirmer l\'envoi', 'bouton', 1, 'mailing/env_email');
	foreach ($form->entrees as $nom => $value) {
		$html.=$value['html'];
	}
	$html.="<p>L'email va être envoyé à $nb_email contacts.</p>";
	$html.=$form->interrupteurs['valider']['html'];
	$js.=$form->initjs();
	foreach ($form->entrees as $key => $value) {
		$js.=$value['js'];
	}
	foreach ($form->interrupteurs as $key => $value) {
		$js.=$value['js'];
	}
	$js.="
$('eemail$id .bouton').button();
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
