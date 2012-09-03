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
			title:'Impossible de renommer la newsletter.',
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
		$id=$_POST['id']['valeur'];
		$sujet=$_POST['sujet']['valeur'];
	
		$n=new Newsletter($id);
		$n->mod_sujet($sujet);
		$js="
			$.post('ajax.php',{action:'news/entetes', format:'html'},function(data){
					if(data.succes==1){
						$('#news_entetes .jspPane').html(data.html);
						$('#news_entetes_head .pagination').html(data.pagination);
						eval(data.js);
						news_seapi.reinitialise();
					}
				},'json'
			);
			$('#rnnews$id').remove();
			$.post('ajax.php',{
					action:'news/mnews',
					id_news:$id,
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
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
