<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$blocs=$_POST['blocs'];
	$id_news=$_POST['id_news'];
	if ($_SESSION['user']['droits']<2){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de modifier l\'ordre des blocs.',
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
		$.post('ajax.php',{
				action:'news/mnews',
				id_news:$id_news,
				format:'html'
			},
			function(data){
				if (data.succes==1) {
					$('#news_newsletter .jspPane').html(data.html);
					eval(data.js);
					news_snapi.reinitialise();
				}
			},
			'json'
		);
		";
	}
	else {
		$n=new Newsletter($id_news);
		$n->ord_blocs($blocs);
		$js="
		$.post('ajax.php',{
				action:'news/mnews',
				id_news:$id_news,
				format:'html'
			},
			function(data){
				if (data.succes==1) {
					$('#news_newsletter .jspPane').html(data.html);
					eval(data.js);
					news_snapi.reinitialise();
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
