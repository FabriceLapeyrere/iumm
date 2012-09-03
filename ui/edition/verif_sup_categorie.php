<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_categorie'];
	
	$c=new Categorie($id);
	$nom=$c->nom();
	if($c->nb_enfants()==0) {
		$js="
		$('<div>Suppression de <b>".addslashes($nom)."</b> ?</div>').dialog({
			resizable: false,
			title:'Etes vous sûr de vouloir supprimer ?',
			modal: true,
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				Supprimer: function() {
					$(this).dialog('close');
					$.post('ajax.php',{
						action:'edition/sup_categorie',
						id_categorie:id
						},
						function(data){
							if (data.succes==1) {
								eval(data.js);
							}
						},
						'json'
					);
				},
				Annuler: function() {
					$(this).dialog('close');
				}
			}
		});
		";
	} else {
		$js="
		$('<div>La catégorie contient d\'autres catégories, elle ne peut pas être supprimée.</div>').dialog({
			resizable: false,
			title:'Impossible de supprimer',
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
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
