<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id_support=$_POST['id_support'];
	Publipostage::sup_support($id_support);
	$id_dernier=Publipostage::dernier();
	$js="
	if ($('#publipostage_support .titre').dataset('id')==$id_support)
		$.post('ajax.php',{action:'publipostage/support', id_support:$id_dernier, format:'html'},function(data){
				if(data.succes==1){
					$('#publipostage_support .jspPane').html(data.html);
					eval(data.js);
					publipostage_sapi.reinitialise();
				}
			},'json'
		);
	$.post('ajax.php',{action:'publipostage/supports', format:'html'},function(data){
			if(data.succes==1){
				$('#publipostage_supports .jspPane').html(data.html);
				$('#publipostage_supports_head .pagination').html(data.pagination);
				eval(data.js);
				publipostage_ssapi.reinitialise();
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
