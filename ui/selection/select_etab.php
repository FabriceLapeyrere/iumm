<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$select_etabs=$_SESSION['selection']['etablissements'];
	$id_etablissement=$_POST['id_etablissement'];
	if (!in_array($id_etablissement,$select_etabs)){
		$select_etabs[]=$id_etablissement;
	}
	$_SESSION['selection']['etablissements']=$select_etabs;
	$_SESSION['sel_binfc']=0;
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
