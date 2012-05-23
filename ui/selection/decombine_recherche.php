<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if (key_exists('c1',$_SESSION['combinaison'])) {
		$_SESSION['selection']=$_SESSION['combinaison']['c2']['selection'];
		$_SESSION['N']=$_SESSION['combinaison']['N'];
		$_SESSION['combinaison']=$_SESSION['combinaison']['c1'];
	}
	else {
		$_SESSION['selection']=$_SESSION['combinaison']['selection'];
		$_SESSION['N']=$_SESSION['combinaison']['N'];
		$_SESSION['combinaison']=array();
	}
	$js="
	$.post('ajax.php',{
			action:'selection/selection_humains',
			format:'html',
			reload_cat:1
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
	$reponse['html']="";
	$reponse['js']=$js;;
?>
