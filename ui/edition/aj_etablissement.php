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
			title:'Impossible d\'ajouter un etablissement.',
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
		$id_structure=$_POST['id_structure'];
		$s=new Structure($id_structure);
		$id_etablissement=$s->aj_etablissement('Siège Social', $_SESSION['user']['id']);
		Cache::set_obsolete('structure',$id_structure);
		$js="
		$.post('ajax.php',{
				action:'edition/structure',
				id_structure:$id_structure,
				format:'html'
			},function(data){
				if(data.succes==1){
					$('#ed_structure-$id_structure').html(data.html)
					eval(data.js);
				}
				ed_scapi.reinitialise();
			},
			'json'
		);
		$('<div id=\'metab$id_etablissement\'></div>').dialog({
			resizable: false,
			dialogClass: 'css-structure',
			close:function(){ 
				$(this).remove();
				delete window['formmcas$id_etablissement'];
			}
		});	
		$.post('ajax.php',{
			action:'edition/metablissement',
			dialogClass: 'css-structure',
			id_etablissement:$id_etablissement
			},
			function(data){
				if (data.succes==1) {
					$('#metab$id_etablissement').dialog('option',{title:data.titre});
					$('#metab$id_etablissement').html(data.html);
					eval(data.js);
				}
			},
			'json'
		);
		$('<div id=\'rnetab$id_etablissement\'></div>').dialog({
			resizable: false,
			dialogClass: 'css-structure',
			close:function(){ 
				$(this).remove();
				delete window['formrncas$id_etablissement'];
			}
		});	
		$.post('ajax.php',{
			action:'edition/rnetablissement',
			id_etablissement:$id_etablissement
			},
			function(data){
				if (data.succes==1) {
					$('#rnetab$id_etablissement').dialog('option',{title:data.titre});
					$('#rnetab$id_etablissement').html(data.html);
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
