<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id_news=$_POST['id_news'];
	$id_bloc=$_POST['id_bloc'];
	$html="";
	$js="";
	$n=new Newsletter($id_news);
	$news=$n->news();
	$blocs=json_decode($news);
	$i=0;
	foreach($blocs as $b){
		if ($b->id_bloc==$id_bloc) $index=$i;
		$i++;
	}
	$bloc=$blocs[$index];
	$modele=Newsletters::modele($bloc->id_modele);
	$nom_modele=Newsletters::nom_modele($bloc->id_modele);
	$form=new formulaires;
	$form->prefixe="mbloc$id_news"."_$id_bloc";
	$form->ajoute_entree('id_news', 'hidden', $id_news, '', array(1,2));
	$form->ajoute_entree('id_bloc', 'hidden', $id_bloc, '', array(1,2));
	$pattern = "/::([^::]*)::/";
	preg_match_all($pattern, $modele, $matches, PREG_OFFSET_CAPTURE, 3);
	foreach($matches[0] as $key=>$value){
		$code=$matches[0][$key][0];
		$tab=explode('&',$matches[1][$key][0]);
		$type=$tab[0];
		$label=$tab[1];
		$nom=filter($label);
		$params_modele=array();
		for($i=2;$i<count($tab);$i++){
			$params_modele[]=$tab[$i];
		}
		$params_serveur=array();
		$params_serveur['id_news']=$id_news;
		$valeur='';
		if (isset($bloc->params->$nom)) $valeur=$bloc->params->$nom;
		$valeur_mbloc=$valeur;
		if(file_exists("ui/news/elements/elt_$type.php")) include "ui/news/elements/elt_$type.php";
		$form->ajoute_entree($nom, $type, $valeur_mbloc, '', array(1,2),$label,array('modele'=>$params_modele,'serveur'=>$params_serveur));
	}
	$form->ajoute_interrupteur('update', 'bouton', 'Mettre à jour', 'bouton', 1, 'news/update_bloc');
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 2, 'news/mod_bloc');
	$html.="<ul>";
	foreach ($form->entrees as $nom => $value) {
		$html.="<li data-nom=\"$nom\" data-type=\"".$value['type']."\">".$value['html']."</li>";
	}
	$html.=$form->interrupteurs['update']['html'];
	$html.=$form->interrupteurs['valider']['html'];
	$js.=$form->initjs();
	foreach ($form->entrees as $key => $value) {
		$js.=$value['js'];
	}
	foreach ($form->interrupteurs as $key => $value) {
		$js.=$value['js'];
	}
	$html.="</ul>";
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']=$n->sujet." - ".$nom_modele;
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>

