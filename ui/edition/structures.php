<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if(isset($_POST['binf'])) {
		$binf=$_POST['binf'];
		$_SESSION['structures']['binf']=$binf;
	}
	else $binf=$_SESSION['structures']['binf'];
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['structures']['motifs']!=$motifs){
			$_SESSION['structures']['motifs']=$motifs;
			$binf=0;
			$_SESSION['structures']['binf']=$binf;
		}
	}
	else $motifs=$_SESSION['structures']['motifs'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	$h= new html();
	
	switch ($format){
		case 'html':
			$html_structure=Html::structures($_SESSION['structures']['binf'],$_SESSION['structures']['motifs']);
			$js='
			ed_structures();
			ed_ajuste();
			';			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html_structure['html'];
			$reponse['pagination']=$html_structure['pagination'];
			$reponse['js']=$js;
			break;
	}
?>
