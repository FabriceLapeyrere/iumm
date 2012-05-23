<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$format=$_POST['format'];
	if(isset($_POST['binf'])) {
		$_SESSION['publipostage']['binf']=$_POST['binf'];
	}
	if(isset($_POST['motifs'])) {
		$motifs=$_POST['motifs'];
		if($_SESSION['publipostage']['motifs']!=$motifs){
			$_SESSION['publipostage']['motifs']=$motifs;
			$_SESSION['publipostage']['binf']=0;
		}
	}
	$html=Html::supports();
	$head="<div class='filtre'>".Html::filtre_support()."</div>
		<span class='pagination ui-buttonset'>".Html::pagination($_SESSION['publipostage']['binf'],Publipostage::nb_supports($_SESSION['publipostage']['motifs']))."</span>
	";
	$pagination="<span class='pagination ui-buttonset'>".Html::pagination($_SESSION['publipostage']['binf'],Publipostage::nb_supports($_SESSION['publipostage']['motifs']))."</span>
	";
	$js=js::publipostage_supports();
	
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

