<?php

	if ($_SESSION['user']['droits']<3){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible d\'envoyer la newsletter.',
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
		$id_news=$_POST['id']['valeur'];
		$id_expediteur=$_POST['expediteur']['valeur'];
		$liste=Casquettes::liste('email');
		$id_envoi=Newsletters::aj_envoi($id_news,$id_expediteur,$liste);	
		$js="
		$('#enews$id_news').remove();

		$.post('ajax.php',{
			action:'mailing/envoi',
			id_envoi:$id_envoi,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#emailing_envoi .jspPane').html(data.html);
				window.location.hash ='#emailing'
				eval(data.js);
			}
		},
		'json'
		);
		$.post('ajax.php',{
			action:'mailing/envois',
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#emailing_envois_head').html(data.head);
				$('#emailing_envois .jspPane').html(data.html);
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
