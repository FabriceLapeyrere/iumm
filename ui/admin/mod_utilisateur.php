<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_utilisateur']['valeur'];
	$nom=$_POST['nom']['valeur'];
	$login=$_POST['login']['valeur'];
	$mdp=$_POST['mdp']['valeur'];
	$mdp2=$_POST['mdp2']['valeur'];
	$droits=$_POST['droits']['valeur'];
	if ($mdp!=$mdp2) {
		$js="
		$('<div>Les mots de passe ne sont pas identiques</div>').dialog({
			resizable: false,
			title:'Impossible d\'ajouter l\'utilisateur',
			modal: true,
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
		$u=new Utilisateur($id);
		$u->mod_nom($nom);
		$u->mod_login($login);
		if ($mdp!="#####") {
			$mdp=crypt($mdp,"keller");
			$u->mod_mdp($mdp);
		}
		$u->mod_droits($droits);
		$retour=Html::utilisateurs($_SESSION['admin']['binf'],$_SESSION['admin']['motifs']);
		$html=json_escape($retour['html']);
		$js="
		$('#mutilisateur$id').remove();
		$('#admin_utilisateurs .jspPane').html('$html');
		admin_utilisateurs();
		";
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['html']="";
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
