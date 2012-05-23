<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id']['valeur'];
	$sujet=$_POST['sujet']['valeur'];
	
	$e=new Email($id);
	$e->mod_sujet($sujet);
	$js="
		$.post('ajax.php',{action:'email/entetes', format:'html'},function(data){
				if(data.succes==1){
					$('#mail_entetes .jspPane').html(data.html);
					$('#mail_entetes_head .pagination').html(data.pagination);
					eval(data.js);
					mail_seapi.reinitialise();
				}
			},'json'
		);
		if ($('#mail_email .enr-email').dataset('id')==$id) $('#mail_email .titre').html('".addslashes($sujet)."');
		$('#rnemail$id').remove();
	";
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
