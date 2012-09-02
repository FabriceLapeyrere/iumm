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
			title:'Impossible de renommer le contact.',
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
		$nom=$_POST['nom']['valeur'];
		$prenom=$_POST['prenom']['valeur'];
		Cache::set_obsolete('contact',$id);
			
		$c=new Contact($id);
		$c->mod_nom($nom, $prenom, $_SESSION['user']['id']);
		if ($prenom!='') $prenom=$prenom." ";

		$casquettes=$c->casquettes();
		$js="
			$('#ed_contact-$id div.titre span.titre').html('".json_escape($prenom.$nom)."');
			$('#rncont$id').remove();
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
		foreach($casquettes as $id_casquette){

			#on rend le cache obsolete
			$cas= new Casquette($id_casquette);
			$id_etablissement=$cas->id_etablissement();
			if($id_etablissement>0) {
				Cache::set_obsolete('casquette',$id_casquette);
				Cache::set_obsolete('casquette_sel',$id_casquette);
				Cache::set_obsolete('etablissement',$id_etablissement);
				Cache::set_obsolete('structure',$cas->id_structure());
	
				$js.="
				$.post('ajax.php',{
						action:'edition/casquette',
						id_casquette:$id_casquette,
						format:'html'
					},function(data){
						if(data.succes==1){
							$('#ed_casquette-$id_casquette').html(data.html);
							eval(data.js);
						}
					},
					'json'
				);	
				";
				$js.="
				$.post('ajax.php',{
						action:'edition/etablissement',
						id_etablissement:$id_etablissement,
						format:'html'
					},function(data){
						if(data.succes==1){
							$('#ed_etablissement-$id_etablissement').html(data.html);
							eval(data.js);
						}
					},
					'json'
				);	
				";
			}
		}
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
		$reponse['message']="";		
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
