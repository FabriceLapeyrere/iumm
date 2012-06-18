<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if ($_SESSION['user']['droits']<2){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de supprimer l\'e-mail.',
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
		$id_email=$_POST['id_email'];
		$e=new Email($id_email);
		$e->suppr();
		$id_dernier=0;
		$id_dernier=Emails::dernier();
		$js="
		if ($('#mail_email .enr-email').dataset('id')==$id_email)
			$.post('ajax.php',{action:'email/memail', id_mail:$id_dernier, format:'html'},function(data){
					if(data.succes==1){
						$('#mail_email .jspPane').html(data.html);
						eval(data.js);
						mail_smapi.reinitialise();
					}
				},'json'
			);
		$.post('ajax.php',{action:'email/entetes', format:'html'},function(data){
				if(data.succes==1){
					$('#mail_entetes .jspPane').html(data.html);
					$('#mail_entetes_head .pagination').html(data.pagination);
					eval(data.js);
					mail_seapi.reinitialise();
				}
			},'json'
		);
		";	
	}
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
