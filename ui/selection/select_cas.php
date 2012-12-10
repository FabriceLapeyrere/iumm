<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$select_cass=$_SESSION['selection']['casquettes'];
	$id_casquette=$_POST['id_casquette'];
	if (!in_array($id_casquette,$select_cass)){
		$select_cass[]=$id_casquette;
	}
	$_SESSION['selection']['casquettes']=$select_cass;
	$html="";
	$js="
	$.post('ajax.php',{
			action:'selection/selection_humains',
			no_reset_binf:1,
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
