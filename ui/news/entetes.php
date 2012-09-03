<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if(isset($_POST['binf'])) {
		$_SESSION['news']['binf']=$_POST['binf'];
	}
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['news']['motifs']!=$motifs){
			$_SESSION['news']['motifs']=$motifs;
			$_SESSION['news']['binf']=0;
		}
	}
	$format=$_POST['format'];
	$html="";
	$js="";
	
	
	switch ($format){
		case 'html':
			$html=Html::entetes_news();
			$js="news_entetes();";
			$pagination=Html::pagination($_SESSION['news']['binf'],Newsletters::nb_news($_SESSION['news']['motifs']));		
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['pagination']=$pagination;
			$reponse['js']=$js;
			break;
	}
?>
