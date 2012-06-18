<?php
	if ($_SESSION['user']['droits']<3){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de supprimer le message.',
			modal: true,
			dialogClass: 'css-infos',
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});
		";
	}
	else {
		$id_message=$_POST['id_message'];
		Emailing::sup_message($id_message);
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
	}
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
