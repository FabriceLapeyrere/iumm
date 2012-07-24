<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if ($_SESSION['user']['droits']<4){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de supprimer un modèle.',
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
		$id_modele=$_POST['id_modele'];
		if(Newsletters::modele_utile($id_modele)>0) {
			$js="
			$('<div>Le modèle est utilisé.</div>').dialog({
				resizable: false,
				title:'Impossible de supprimer le modèle.',
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
			Newsletters::sup_modele($id_modele);
			$js="
			$('#news_modeles').html('".json_escape(Html::modeles_news())."');
			contextmenu_modeles();
			$.post('ajax.php',{
					action:'news/mnews',
					id_news:$('#news_content').dataset('id'),
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
