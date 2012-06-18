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
			title:'Impossible d\'ajouter un contact.',
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
		$prenom=$_POST['prenom']['valeur'];
		$c= new Contacts();
		$id_contact=$c->aj_contact($nom,$prenom, $_SESSION['user']['id']);
		$cont= new Contact($id_contact);
		$casquettes=$cont->casquettes;
		$id_casquette=0;
		foreach($casquettes as $id=>$casquette ){
			$id_casquette=$id;
		}
		$js="
		$('#ncont').remove();
		$('<div id=\'mcas$id_casquette\'></div>').dialog({
			resizable: false,
			close:function(){ 
				$(this).remove();
				delete window['formmcas$id_casquette'];
			}
		});	
		$.post('ajax.php',{
			action:'edition/mcasquette',
			id_casquette:$id_casquette
			},
			function(data){
				if (data.succes==1) {
					$('#mcas$id_casquette').dialog('option',{title:data.titre});
					$('#mcas$id_casquette').html(data.html);
					$('#mcas$id_casquette input[name=\"Email\"]').focus();
					eval(data.js);
				}
			},
			'json'
		);
		$('<div id=\'rncas$id_casquette\'></div>').dialog({
			resizable: false,
			close:function(){ 
				$(this).remove();
				delete window['formrncas$id_casquette'];
			}
		});	
		$.post('ajax.php',{
			action:'edition/rncasquette',
			id_casquette:$id_casquette
			},
			function(data){
				if (data.succes==1) {
					$('#rncas$id_casquette').dialog('option',{title:data.titre});
					$('#rncas$id_casquette').html(data.html);
					$('#rncas$id_casquette input[name=\"Email\"]').focus();
					eval(data.js);
				}
			},
			'json'
		);
		$.post('ajax.php',{
				action:'selection/selection_humains',
				format:'html'
			},function(data){
				if(data.succes==1){
					$('#sel_humains').html(data.html);
					eval(data.js);
					$.post('ajax.php',{action:'edition/contacts', format:'html'},function(data){
							if(data.succes==1){
								$('#ed_contacts .jspPane').html(data.html);
								$('#ed_contacts-head .pagination').html(data.pagination);
								eval(data.js);
							}
						},'json'
					);
	
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
