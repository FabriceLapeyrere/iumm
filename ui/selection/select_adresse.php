<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$adresse=$_POST['adresse'];
	$Nadresse=$_POST['Nadresse'];
	$_SESSION['selection']['adresse']=$adresse;
	$_SESSION['selection']['Nadresse']=$Nadresse;
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
	$reponse['js']=$js;
?>
