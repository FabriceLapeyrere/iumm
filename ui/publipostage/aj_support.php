<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$nom=$_POST['nom']['valeur'];
	Publipostage::aj_support($nom);
	$js="
	$('#nsup').remove();
	$.post('ajax.php',{action:'publipostage/supports', format:'html'},function(data){
			if(data.succes==1){
				$('#publipostage_supports .jspPane').html(data.html);
				$('#publipostage_supports_head .pagination').html(data.pagination);
				eval(data.js);
			}
		},'json'
	);
	";
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['html']="";
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
