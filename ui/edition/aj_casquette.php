<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if ($_SESSION['user']['droits']<3){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible d\'ajouter une casquette.',
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
	
	$id_contact=$_POST['id_contact'];
	$c=new Contact($id_contact);
	$id_casquette=$c->aj_casquette('Perso', $_SESSION['user']['id']);
	$js="
	$.post('ajax.php',{
			action:'edition/contact',
			id_contact:$id_contact,
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#ed_contact-$id_contact').html(data.html)
				eval(data.js);
			}
			ed_scapi.reinitialise();
		},
		'json'
	);
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
