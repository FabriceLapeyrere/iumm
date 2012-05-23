<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$select_cass=$_SESSION['selection']['casquettes'];
	$id_casquette=$_POST['id_casquette'];
	$tab=array();
	foreach($select_cass as $id) {
		if ($id!=$id_casquette){
			$tab[]=$id;
		}
	}
	$_SESSION['selection']['casquettes']=$tab;
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
