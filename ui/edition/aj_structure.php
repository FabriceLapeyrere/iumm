<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$nom=$_POST['nom']['valeur'];
	$id_structure=Structures::aj_structure($nom);
	$str= new Structure($id_structure);
	$etablissements=$str->etablissements;
	$id_etablissement=0;
	foreach($etablissements as $id=>$etablissement ){
		$id_etablissement=$id;
	}
	$js="
	$('#nstr').remove();
	$('<div id=\'metab$id_etablissement\'></div>').dialog({
		resizable: false,
		dialogClass: 'css-structure',
		close:function(){ 
			$(this).remove();
			delete window['formmetab$id_etablissement'];
		}
	});	
	$.post('ajax.php',{
		action:'edition/metablissement',
		id_etablissement:$id_etablissement
		},
		function(data){
			if (data.succes==1) {
				$('#metab$id_etablissement').dialog('option',{title:data.titre});
				$('#metab$id_etablissement').html(data.html);
				$('#metab$id_etablissement input[name=\"Email\"]').focus();
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
			delete window['formrnetab$id_etablissement'];
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
				$('#rnetab$id_etablissement input[name=\"Email\"]').focus();
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
			$.post('ajax.php',{action:'edition/structures', format:'html'},function(data){
					if(data.succes==1){
						$('#ed_structures .jspPane').html(data.html);
						$('#ed_structures-head .pagination').html(data.pagination);
						eval(data.js);
					}
				},'json'
			);
	
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
