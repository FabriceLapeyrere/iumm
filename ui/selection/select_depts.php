<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$depts=$_POST['depts'];
	$tab_depts=explode(',',$depts);
	$_SESSION['selection']['depts']=array();
	$_SESSION['sel_binfc']=0;
	foreach($tab_depts as $dept){
		if (array_key_exists(trim(strtoupper($dept)),$departements)) $_SESSION['selection']['depts'][]=trim(strtoupper($dept));
	}
	$_SESSION['selection']['depts']=array_unique($_SESSION['selection']['depts']);
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
