<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$format=$_GET['format'];
	$html="";
	$js="";
	$select_cats=$_SESSION['selection']['categories'];

	function jsoncat($idparent){
		error_log(microtime()."\n",3,'tmp/fab.log');
		global $select_cats;
		$tab=array();
		$parent=new Categorie($idparent);
		foreach ($parent->enfants() as $id_enfant=>$enfant) {
			$c=new Categorie($id_enfant);
			$children=jsoncat($id_enfant);
			$select=false;
			if (in_array($c->id,$select_cats)) $select=true;
			if (count($children)==0)
				$tab[]=array('title'=>"<span>".$c->nom." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>", 'icon'=>false, 'addClass'=>'categorie', 'key'=>$c->id."", 'select'=>$select);
			else
				$tab[]=array('title'=>"<span>".$c->nom." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>",'icon'=>false, 'addClass'=>'categorie', 'key'=>$c->id."", 'select'=>$select, 'children'=>$children);
		}
		return $tab;
	}
	$root=array('title'=>"<span>Listes</span>",'icon'=>false, 'addClass'=>'categorie', 'key'=>'0', 'children'=>jsoncat(0));
	switch ($format){
		case 'json':
			$reponse=$root;
			break;
	}
?>
