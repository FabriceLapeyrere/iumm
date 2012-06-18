<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if ($_SESSION['user']['droits']<2){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de modifier le support.',
			modal: true,
			dialogClass: 'css-infos',
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});
		";
	}
	else {
		$id=$_POST['id']['valeur'];
		$h_page=$_POST['h_page']['valeur'];
		$l_page=$_POST['l_page']['valeur'];
		$nb_lignes=$_POST['nb_lignes']['valeur'];
		$nb_colonnes=$_POST['nb_colonnes']['valeur'];
		$mp_gauche=$_POST['mp_gauche']['valeur'];
		$mp_droite=$_POST['mp_droite']['valeur'];
		$mp_haut=$_POST['mp_haut']['valeur'];
		$mp_bas=$_POST['mp_bas']['valeur'];
		$mc_gauche=$_POST['mc_gauche']['valeur'];
		$mc_droite=$_POST['mc_droite']['valeur'];
		$mc_haut=$_POST['mc_haut']['valeur'];
		$mc_bas=$_POST['mc_bas']['valeur'];
	
		$s=Publipostage::support($id);
		Publipostage::mod_support($id,$h_page,$l_page,$nb_lignes,$nb_colonnes,$mp_gauche,$mp_droite,$mp_haut,$mp_bas,$mc_gauche,$mc_droite,$mc_haut,$mc_bas);
		$js="
			$.post('ajax.php',{
				action:'publipostage/support',
				id_support:$id,
				format:'html'
			},
			function(data){
				if (data.succes==1) {
					$('#publipostage_support .jspPane').html(data.html);
					eval(data.js);
				}
			},
			'json');
			$.post('ajax.php',{action:'publipostage/supports', format:'html'},function(data){
					if(data.succes==1){
						$('#publipostage_supports .jspPane').html(data.html);
						$('#publipostage_supports_head .pagination').html(data.pagination);
						eval(data.js);
						publipostage_ssapi.reinitialise();
					}
				},'json'
			);
		";
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
