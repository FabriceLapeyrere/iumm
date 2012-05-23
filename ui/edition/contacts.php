<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if(isset($_POST['binf'])) {
		$binf=$_POST['binf'];
		$_SESSION['contacts']['binf']=$binf;
	}
	else $binf=$_SESSION['contacts']['binf'];
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['contacts']['motifs']!=$motifs){
			$_SESSION['contacts']['motifs']=$motifs;
			$binf=0;
			$_SESSION['contacts']['binf']=$binf;
		}
	}
	else $motifs=$_SESSION['contacts']['motifs'];
	$format=$_POST['format'];
	$html="";
	$js="";
	
	switch ($format){
		case 'html':
			$html_contact=Html::contacts($_SESSION['contacts']['binf'],$_SESSION['contacts']['motifs']);
			$js='
			ed_contacts();
			ed_ajuste();
			';			
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html_contact['html'];
			$reponse['pagination']=$html_contact['pagination'];
			$reponse['js']=$js;
			break;
	}
?>
