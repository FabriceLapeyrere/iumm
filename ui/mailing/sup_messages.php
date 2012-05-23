<?php
	$id_envoi=$_POST['id_envoi'];
	Emailing::sup_messages($id_envoi);
	$js="
	$.post('ajax.php',{
			action:'mailing/envoi',
			id_envoi:$('#emailing_envoi .titre').dataset('id'),
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#emailing_envoi .jspPane').html(data.html);
				eval(data.js);
			}
		},
		'json'
	);
	";
	$succes=1;
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
