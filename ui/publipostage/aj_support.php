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
			title:'Impossible d\'ajouter un support.',
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
		$nom=$_POST['nom']['valeur'];
		$id=Publipostage::aj_support($nom);
		Publipostage::mod_support($id,297,210,1,1,0,0,0,0,0,0,0,0,12,"::NOM_COMPLET::\n::ADRESSE::");
		$js="
		$('#nsup').remove();
		$.post('ajax.php',{action:'publipostage/supports', format:'html'},function(data){
				if(data.succes==1){
					$('#publipostage_supports .jspPane').html(data.html);
					$('#publipostage_supports_head .pagination').html(data.pagination);
					eval(data.js);
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
