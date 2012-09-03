<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$a=var_export($_SESSION,true);
	if (key_exists('c1',$_SESSION['combinaison'])) {
		$_SESSION['selection']=$_SESSION['combinaison']['c2']['selection'];
		$_SESSION['N']=$_SESSION['combinaison']['N'];
		$_SESSION['combinaison']=$_SESSION['combinaison']['c1'];
	}
	elseif (count($_SESSION['combinaison'])>0) {
		$_SESSION['selection']=$_SESSION['combinaison']['selection'];
		$_SESSION['N']=$_SESSION['combinaison']['N'];
		$_SESSION['combinaison']=array();
	}
	else {
		#reset complet
		$sel_binfc=0;
		$sel_binfs=0;
		$sel_motifc="";
		$sel_motifs="";
		$sel_cats=array();
		$sel_etabs=array();
		$sel_Ncats=0;
		$sel_Netabs=0;
		$sel_motifs="";
		$sel_mots="";
		$sel_depts=array();
		$sel_email=0;
		$sel_Nemail=0;
		$sel_adresse=0;
		$sel_Nadresse=0;
		$sel_cass=array();
		$sel_cas=0;
		$sel_Ncas=0;
		$combinaison=array();
		$scombinaison=0;
		$op=1;
		$N=0;
		$_SESSION['sel_structures']= array();
		$_SESSION['sel_structures']['binf'] = $sel_binfs;
		$_SESSION['sel_structures']['motifs'] = $sel_motifs;
		$_SESSION['sel_binfc'] = $sel_binfc;
		$_SESSION['selection']= array();
		$_SESSION['selection']['categories'] = $sel_cats;
		$_SESSION['selection']['etablissements'] = $sel_etabs;
		$_SESSION['selection']['Ncats'] = $sel_Ncats;
		$_SESSION['selection']['Netabs'] = $sel_Netabs;
		$_SESSION['selection']['motifs'] = $sel_motifs;
		$_SESSION['selection']['mots'] = $sel_mots;
		$_SESSION['selection']['depts'] = $sel_depts;
		$_SESSION['selection']['email'] = $sel_email;
		$_SESSION['selection']['Nemail'] = $sel_Nemail;
		$_SESSION['selection']['adresse'] = $sel_adresse;
		$_SESSION['selection']['Nadresse'] = $sel_Nadresse;
		$_SESSION['selection']['casquettes'] = $sel_cass;
		$_SESSION['selection']['cas'] = $sel_cas;
		$_SESSION['selection']['Ncas'] = $sel_Ncas;
		$_SESSION['combinaison'] = $combinaison;
		$_SESSION['op'] = $op;
		$_SESSION['N'] = $N;
		$_SESSION['scombinaison'] = $scombinaison;
	}
	$b=var_export($_SESSION,true);
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
	$reponse['message']="$a
----
$b";
	$reponse['html']="";
	$reponse['js']=$js;;
?>
