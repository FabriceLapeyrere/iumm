<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if(isset($_POST['binf'])) {
		$binf=$_POST['binf'];
		$_SESSION['admin']['binfx']=$binf;
	}
	else $binf=$_SESSION['admin']['binfx'];
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['admin']['motifsx']!=$motifs){
			$_SESSION['admin']['motifsx']=$motifs;
			$binf=0;
			$_SESSION['admin']['binfx']=$binf;
		}
	}
	else $motifs=$_SESSION['admin']['motifsx'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	switch ($format){
		case 'html':
			$retour=Html::expediteurs($binf,$motifs);
			$js="
			admin_expediteurs();
			admin_ajuste();
			";			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$retour['html'];
			$reponse['pagination']=$retour['pagination'];
			$reponse['js']=$js;
			break;
	}
?>
