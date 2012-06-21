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
			title:'Impossible d\'ajouter une newsletter.',
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
		$sujet=$_POST['sujet']['valeur'];
		$id_news=Newsletters::aj_news($sujet);
		$_SESSION['email']['motifs']="";
		$_SESSION['email']['binf']=0;
		$js="
		$('#nnews').dialog('close');
		$.post('ajax.php',{action:'news/entetes', format:'html'},function(data){
				if(data.succes==1){
					$('#news_entetes .jspPane').html(data.html);
					$('#news_entetes_head .pagination').html(data.pagination);
					eval(data.js);
					news_seapi.reinitialise();
				}
			},'json'
		);
		$.post('ajax.php',{
				action:'news/news',
				id_news:$id_news,
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
