<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if(isset($_POST['binf'])) {
		$binf=$_POST['binf'];
		$_SESSION['utilisateurs']['binf']=$binf;
	}
	else $binf=$_SESSION['utilisateurs']['binf'];
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['utilisateurs']['motifs']!=$motifs){
			$_SESSION['utilisateurs']['motifs']=$motifs;
			$binf=0;
			$_SESSION['utilisateurs']['binf']=$binf;
		}
	}
	else $motifs=$_SESSION['utilisateurs']['motifs'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	switch ($format){
		case 'html':
			$retour=Html::utilisateurs($binf,$motifs);
			$js='
			admin_utilisateurs();
			admin_ajuste();
			';			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$retour['html'];
			$reponse['pagination']=$retour['pagination'];
			$reponse['js']=$js;
			break;
	}
?>
