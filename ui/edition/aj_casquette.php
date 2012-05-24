<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=0;
	$id_contact=$_POST['id_contact'];
	$c=new Contact($id_contact);
	$id_casquette=$c->aj_casquette('Perso');
	$succes=0;
	if ($id_casquette>0) $succes=1;
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
