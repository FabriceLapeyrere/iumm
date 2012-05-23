<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$format=$_POST['format'];
	if(isset($_POST['binf'])) {
		$_SESSION['emailing']['binf']=$_POST['binf'];
	}
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['emailing']['motifs']!=$motifs){
			$_SESSION['emailing']['motifs']=$motifs;
			$_SESSION['emailing']['binf']=0;
		}
	}
	$html=Html::envois();
	$head="<div class='filtre'>".Html::filtre_envoi()."</div>
		<span class='pagination ui-buttonset'>".Html::pagination($_SESSION['emailing']['binf'],Emailing::nb_envois($_SESSION['emailing']['motifs']))."</span>
	";
	$pagination="<span class='pagination ui-buttonset'>".Html::pagination($_SESSION['emailing']['binf'],Emailing::nb_envois($_SESSION['emailing']['motifs']))."</span>
	";
	$js=js::emailing_envois();
	
	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['head']=$head;
			$reponse['pagination']=$pagination;
			$reponse['js']=$js;
			break;
	}
?>

