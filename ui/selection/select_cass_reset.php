<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$_SESSION['selection']['casquettes']=array();
	$_SESSION['sel_binfc']=0;
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
