<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id']['valeur'];
	$nom=$_POST['nom']['valeur'];

	#on rend le cache obsolete
	Cache::set_obsolete('casquette',$id);
		
	$c=new Casquette($id);
	$c->mod_nom($nom);
	$js="
		$('li[data-tab=\"#ed_casquette-$id\"] a').html('".addslashes($nom)."');
		if ($('#mcas$id').length>0)
		$.post('ajax.php',{action:'edition/mcasquette', id_casquette:$id},function(data){
			$('#mcas$id').dialog('option',{title:data.titre});
		},'json');
		$('#rncas$id').dialog('close');
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
	if ($c->id_etablissement>0){
		#on rend le cache obsolete
		Cache::set_obsolete('etablissement',$c->id_etablissement);
		$js.="
			if ($('#ed_contactsEtab-".$c->id_etablissement."').length!=0)
			$.post('ajax.php',{action:'edition/mcasquette', id_casquette:$id},function(data){
				$('#ed_contactsEtab-".$c->id_etablissement." a[data-id=$id]').html(data.titre);
			},'json');
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
