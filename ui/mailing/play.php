<?php
	if ($_SESSION['user']['droits']<3){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de lancer l'envoi.',
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
		include "ctl/exec/envoi.class.php";
		$id_envoi=$_POST['id_envoi'];
		$e= new Envoi($id_envoi);
		$e->start();
		$js="
		setTimeout('$.post(\"ajax.php\",{action:\"mailing/envoi\",id_envoi:$(\"#emailing_envoi .titre\").dataset(\"id\"),format:\"html\"},function(data){if (data.succes==1) { $(\"#emailing_envoi .jspPane\").html(data.html); eval(data.js); } }, \"json\" );',1000);
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
