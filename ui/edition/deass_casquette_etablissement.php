<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id_casquette=$_POST['id_casquette'];
	$c=new Casquette($id_casquette);
	$id_etablissement=$c->id_etablissement();
	$e=new Etablissement($id_etablissement);
	$casquettes=$e->casquettes();
	$c->deass_etablissement($_SESSION['user']['id']);
	
	#on rend le cache obsolete
	Cache::set_obsolete('etablissement',$id_etablissement);
	$id_propre=$e->casquette_propre();
	Cache::set_obsolete('casquette',$id_propre);
	Cache::set_obsolete('casquette_sel',$id_propre);
	
	$c->mod_nom("Perso");
	$js="";
	$js.="$('#edition li[data-tab=\"#ed_casquette-$id_casquette\"] a').html('Perso');";
	#on met à jour contact dans 5s
	$contact=$c->contact();
	async('ui/cache/cache',array('objet'=>'contact','id_objet'=>$contact['id']));
						
	foreach($casquettes as $id_cas){
		Cache::set_obsolete('casquette',$id_cas);	
		Cache::set_obsolete('casquette_sel',$id_cas);	
		$js.="
		$('#ed_casquette-$id_cas').html('".json_escape(Html::casquette($id_cas))."');
		";
		$js.=Js::casquette($id_cas);
		$js.="
		ed_scapi.reinitialise();
		";
	}
	$js.="
	$('#ed_etablissement-$id_etablissement').html('".json_escape(Html::etablissement($id_etablissement))."');
	";
	$js.=Js::etablissement($id_etablissement);
	$js.="
	ed_ssapi.reinitialise();
	";
	$js.="
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
