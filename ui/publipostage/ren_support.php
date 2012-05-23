<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id']['valeur'];
	$nom=$_POST['nom']['valeur'];
	
	$s=Publipostage::support($id);
	Publipostage::ren_support($id,$nom);
	$js="
		$.post('ajax.php',{
			action:'publipostage/support',
			id_support:$id,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#publipostage_support .jspPane').html(data.html);
				eval(data.js);
			}
		},
		'json');
		$.post('ajax.php',{action:'publipostage/supports', format:'html'},function(data){
				if(data.succes==1){
					$('#publipostage_supports .jspPane').html(data.html);
					$('#publipostage_supports_head .pagination').html(data.pagination);
					eval(data.js);
					publipostage_ssapi.reinitialise();
				}
			},'json'
		);
		$('#rnsup$id').dialog('close');		
	";
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
