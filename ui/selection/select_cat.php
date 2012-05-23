<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$tab=array();
	if (isset($_POST['selection'])){ $selection=$_POST['selection'];
		foreach($selection as $categorie) {
			$tab[]=$categorie['value'];
		}
	}
	$_SESSION['selection']['categories']=$tab;
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
