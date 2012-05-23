<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$sujet=$_POST['sujet']['valeur'];
	$id_email=Emails::aj_email($sujet);
	$email= new Email($id_email);
	$_SESSION['email']['motifs']="";
	$_SESSION['email']['binf']=0;
	$js="
	$('#nemail').remove();
	$.post('ajax.php',{action:'email/entetes', format:'html'},function(data){
			if(data.succes==1){
				$('#mail_entetes .jspPane').html(data.html);
				$('#mail_entetes_head .pagination').html(data.pagination);
				eval(data.js);
				mail_seapi.reinitialise();
			}
		},'json'
	);
	$.post('ajax.php',{
			action:'email/memail',
			id_email:$id_email,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#mail_email .jspPane').html(data.html);
				eval(data.js);
			}
		},
		'json'
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
