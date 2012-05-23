<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$format=$_GET['format'];
	$html="";
	$js="";

	function jsoncat($idparent){
		$tab=array();
		$parent=new Categorie($idparent);
		foreach ($parent->enfants() as $id_enfant=>$enfant) {
			$c=new Categorie($id_enfant);
			$children=jsoncat($id_enfant);
			if (count($children)==0)
				$tab[]=array('title'=>"<span>".$c->nom." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>", 'icon'=>'false', 'addClass'=>'categorie', 'key'=>$c->id."");
			else
				$tab[]=array('title'=>"<span>".$c->nom." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>",'icon'=>'false', 'addClass'=>'categorie', 'key'=>$c->id."", 'children'=>$children);
		}
		return $tab;
	}
	$root=array('title'=>"<span>Listes</span>",'icon'=>'false', 'addClass'=>'categorie', 'key'=>'0', 'children'=>jsoncat(0));
	switch ($format){
		case 'json':
			$reponse=$root;
			break;
	}
?>
