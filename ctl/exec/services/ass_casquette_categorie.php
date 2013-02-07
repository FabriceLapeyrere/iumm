<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$id_casquette=$_REQUEST['id_casquette'];
$id_categorie=$_REQUEST['id_categorie'];

$c=new Casquette($id_casquette);
$c->ass_categorie($id_categorie);
$cat=new Categorie($id_categorie);
#on rend le cache obsolete
Cache::set_obsolete('ed_categorie',$cat);
Cache::set_obsolete('sel_categorie',$cat);
Cache::set_obsolete('casquette_sel',$id_casquette);
Cache::set_obsolete('casquette',$id_casquette);
$contact=$c->contact();
if ($contact['nom']!="$$$$") {
	Cache::set_obsolete('contact',$contact['id']);
} else {
	$id_etablissement=$c->id_etablissement();
	$id_structure=$c->id_structure();
	Cache::set_obsolete('etablissement',$id_etablissement);
	Cache::set_obsolete('structure',$id_structure);
	$e=new etablissement($id_etablissement);
	foreach($e->casquettes() as $id_cas){
		if($id_cas>0) {
			$c=new casquette($id_cas);		
			Cache::set_obsolete('contact',$c->id_contact());
			Cache::set_obsolete('casquette',$id_cas);
			Cache::set_obsolete('casquette_sel',$id_cas);
		}
	}
}
while ($cat->id!=0){
	Cache::set_obsolete('ed_categorie',$cat->id);
	Cache::set_obsolete('sel_categorie',$cat->id);
	$cat=new Categorie($cat->id_parent());
}

echo json_encode(array('ajout'=>'ok'));
?>
