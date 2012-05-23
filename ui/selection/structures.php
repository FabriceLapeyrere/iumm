<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if(isset($_POST['binf'])) {
		$binf=$_POST['binf'];
		$_SESSION['sel_structures']['binf']=$binf;
	}
	else $binf=$_SESSION['sel_structures']['binf'];
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['sel_structures']['motifs']!=$motifs){
			$_SESSION['sel_structures']['motifs']=$motifs;
			$binf=0;
			$_SESSION['sel_structures']['binf']=$binf;
		}
	}
	else $motifs=$_SESSION['sel_structures']['motifs'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	$h= new html();
	
	switch ($format){
		case 'html':
			$html_structure_sel=Html::structures_selection($_SESSION['sel_structures']['binf'],$_SESSION['sel_structures']['motifs']);
			$js='
			sel_ajuste();
			';			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html_structure_sel['html'];
			$reponse['pagination']=$html_structure_sel['pagination'];
			$reponse['js']=$js;
			break;
	}
?>
