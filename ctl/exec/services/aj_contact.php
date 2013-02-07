<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$nom=$_REQUEST['nom'];
$prenom=$_REQUEST['prenom'];
$email=$_REQUEST['email'];
$categories=$_REQUEST['categories'];

$id=Contacts::aj_contact($nom, $prenom);
$c=new Contact($id);
$casquettes=$c->casquettes();
$cas=new Casquette($casquettes[0]);
$cas->aj_donnee('Email', 'E-mail', 'email', $email);
foreach($categories as $id_cat){
	$cas->ass_categorie($id_cat);
	$cat=new Categorie($id_cat);
	while ($cat->id!=0){
		Cache::set_obsolete('ed_categorie',$cat->id);
		Cache::set_obsolete('sel_categorie',$cat->id);
		$cat=new Categorie($cat->id_parent());
	}
}
echo json_encode(array('ajout'=>'ok'));
?>
