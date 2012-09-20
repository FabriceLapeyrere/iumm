<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$format="";
	if (isset($_GET['format'])) $format=$_GET['format'];
	$html="";
	$js="";
		function sel_jsoncat($idparent){
		$tab=array();
		$parent=new Categorie($idparent);
		foreach ($parent->enfants() as $id_enfant=>$enfant) {
			$c=new Categorie($id_enfant);
			$children=sel_jsoncat($id_enfant);
			if (count($children)==0)
				if (Cache::obsolete('sel_categorie',$id_enfant)) {
					$tab_cat=array('title'=>"<span>".$c->nom()." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>", 'icon'=>false, 'addClass'=>'categorie', 'key'=>$c->id."");
					Cache::put('sel_categorie',$id_enfant,json_encode($tab_cat));
					$tab[]=$tab_cat;
				} else {
					$tab_cat=json_decode(Cache::get('sel_categorie',$id_enfant));
					$tab[]=$tab_cat;
				}
			else {
				if (Cache::obsolete('sel_categorie',$id_enfant)) {
					$tab_cat=array('title'=>"<span>".$c->nom()." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>",'icon'=>false, 'addClass'=>'categorie', 'key'=>$c->id."", 'children'=>$children);
					Cache::put('sel_categorie',$id_enfant,json_encode($tab_cat));
					$tab[]=$tab_cat;
				} else {
					$tab_cat=json_decode(Cache::get('sel_categorie',$id_enfant));
					$tab[]=$tab_cat;
				}
			}
		}
		return $tab;
	}
	$root=array('title'=>"<span>Listes</span>",'icon'=>false, 'addClass'=>'categorie', 'key'=>'0', 'children'=>sel_jsoncat(0));
	switch ($format){
		case 'json':
			$reponse=$root;
			break;
	}
?>
