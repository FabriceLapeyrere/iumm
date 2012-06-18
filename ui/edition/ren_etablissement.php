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
			title:'Impossible de renommer l\'établissement.',
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
	
		#on rend le cache obsolete
		Cache::set_obsolete('etablissement',$id);
	
		$e=new Etablissement($id);
		$e->mod_nom($nom, $_SESSION['user']['id']);
		$casquettes=$e->casquettes();
		$js="";
		$js.="
			$('li[data-tab=\"#ed_etablissement-$id\"] a').html('".addslashes($nom)."');
			$.post('ajax.php',{action:'edition/metablissement', id_etablissement:$id},function(data){
				if ($('#metab$id').length!=0) $('#metab$id').dialog('option',{title:data.titre});
				$('.titre.etabContact".$e->id."').html(data.titre);
			},'json');
			$('#rnetab$id').remove();
			$.post('ajax.php',{action:'edition/metablissement', id_etablissement:$id},function(data){
				$('.etabContact$id span.titre').html(data.titre);
				ed_scapi.reinitialise();
			},'json');
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
		foreach($casquettes as $id_cas=>$nom_cas){
	
			#on rend le cache obsolete
			Cache::set_obsolete('casquette',$id_cas);
		
			$js_cas.="
			$.post('ajax.php',{
					action:'edition/casquette',
					id_casquette:$id_cas,
					format:'html'
				},function(data){
					if(data.succes==1) $('#ed_casquette-$id_cas').html(data.html)
					eval(data.js);
					ed_scapi.reinitialise();
				},
				'json'
			);
			";
		};
	}
	if($succes) {
		$reponse['succes']=1;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
