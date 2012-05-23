<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$op=$_SESSION['op'];
	$N=$_SESSION['N'];
	if (count($_SESSION['combinaison'])==0) $_SESSION['combinaison']=array('selection'=>$_SESSION['selection'],'N'=>$N);
	else $_SESSION['combinaison']=array('c1'=>$_SESSION['combinaison'], 'c2'=>array('selection'=>$_SESSION['selection'],'N'=>0), 'op'=>$op, 'N'=>$N);
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

	$_SESSION['selection']['categories']=array();
	$_SESSION['selection']['etablissements']=array();
	$_SESSION['selection']['Ncats']=0;
	$_SESSION['selection']['Netabs']=0;
	$_SESSION['selection']['motifs']="";
	$_SESSION['selection']['mots']="";
	$_SESSION['selection']['depts']=array();
	$_SESSION['selection']['email']=0;
	$_SESSION['selection']['Nemail']=0;
	$_SESSION['selection']['adresse']=0;
	$_SESSION['selection']['Nadresse']=0;
	$_SESSION['selection']['casquettes']=array();
	$_SESSION['selection']['cas']=0;
	$_SESSION['selection']['Ncas']=0;
	
	$_SESSION['scombinaison']=0;

	$_SESSION['op']=0;
	$_SESSION['N']=0;

	$reponse['succes']=1;
	$reponse['message']="";
	$reponse['html']="";
	$reponse['js']=$js;
?>
