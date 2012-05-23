<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if(isset($_POST['binf'])) {
		$_SESSION['email']['binf']=$_POST['binf'];
	}
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['email']['motifs']!=$motifs){
			$_SESSION['email']['motifs']=$motifs;
			$_SESSION['email']['binf']=0;
		}
	}
	$format=$_POST['format'];
	$html="";
	$js="";
	
	
	switch ($format){
		case 'html':
			$html=Html::entetes_email();
			$js=Js::entetes_email();
			$pagination=Html::pagination($_SESSION['email']['binf'],Emails::nb_emails($_SESSION['email']['motifs']));		
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['pagination']=$pagination;
			$reponse['js']=$js;
			break;
	}
?>
