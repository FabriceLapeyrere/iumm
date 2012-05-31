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
			$html_contact=Html::utilisateurs($_SESSION['utilisateurs']['binf'],$_SESSION['utilisateurs']['motifs']);
			$js='
			ad_utilisateurs();
			ad_ajuste();
			';			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html_contact['html'];
			$reponse['pagination']=$html_contact['pagination'];
			$reponse['js']=$js;
			break;
	}
?>
