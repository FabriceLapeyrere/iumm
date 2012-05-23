<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$sel_binfc=$_POST['binfc'];
	$_SESSION['sel_binfc']=$sel_binfc;
	$js="
	$.post('ajax.php',{action:'selection/casquettes', format:'html'},function(data){
			if(data.succes==1){
				$('#sel_casquettes .jspPane').html(data.html);
				eval(data.js);
			}
		},'json');
	";						
	$reponse['succes']=1;
	$reponse['js']=$js;
?>
