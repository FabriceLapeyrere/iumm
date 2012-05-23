<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$select_etabs=$_SESSION['selection']['etablissements'];
	$id_etablissement=$_POST['id_etablissement'];
	$tab=array();
	foreach($select_etabs as $id) {
		if ($id!=$id_etablissement){
			$tab[]=$id;
		}
	}
	$_SESSION['selection']['etablissements']=$tab;
	$html="";
	$js="
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
	$reponse['succes']=1;
	$reponse['message']="";
	$reponse['js']=$js;
?>
