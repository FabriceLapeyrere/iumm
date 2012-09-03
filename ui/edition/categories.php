<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$format="";
	if (isset($_GET['format'])) $format=$_GET['format'];
	$html="";
	$js="";

	function ed_jsoncat($idparent){
		$tab=array();
		$parent=new Categorie($idparent);
		foreach ($parent->enfants() as $id_enfant=>$enfant) {
			$c=new Categorie($id_enfant);
			$children=ed_jsoncat($id_enfant);
			if (count($children)==0)
				if (Cache::obsolete('ed_categorie',$id_enfant)) {
					$tab_cat=array('title'=>"<span>".$c->nom()." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>", 'icon'=>'false', 'addClass'=>'categorie', 'key'=>$c->id."");
					$tab[]=$tab_cat;
					Cache::put('ed_categorie',$id_enfant,json_encode($tab_cat));
				} else {
					$tab[]=json_decode(Cache::get('ed_categorie',$id_enfant));
				}
			else {
				if (Cache::obsolete('ed_categorie',$id_enfant)) {
					$tab_cat=array('title'=>"<span>".$c->nom()." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>",'icon'=>'false', 'addClass'=>'categorie', 'key'=>$c->id."", 'children'=>$children);
					$tab[]=$tab_cat;
					Cache::put('ed_categorie',$id_enfant,json_encode($tab_cat));
				} else {
					$tab[]=json_decode(Cache::get('ed_categorie',$id_enfant));
				}
			}
		}
		return $tab;
	}
	$root=array('title'=>"<span>Listes</span>",'icon'=>'false', 'addClass'=>'categorie', 'key'=>'0', 'children'=>ed_jsoncat(0));
	switch ($format){
		case 'json':
			$reponse=$root;
			break;
	}
?>
