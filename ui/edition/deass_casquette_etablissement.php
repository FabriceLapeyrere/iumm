<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id_casquette=$_POST['id_casquette'];
	
	#on rend le cache obsolete
	Cache::set_obsolete('casquette',$id_casquette);
	Cache::set_obsolete('etablissement',$id_etablissement);
	
	$c=new Casquette($id_casquette);
	$id_etablissement=$c->id_etablissement;
	$c->deass_etablissement();
	$js="
	$.post('ajax.php',{
			action:'edition/casquette',
			id_casquette:$id_casquette,
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#ed_casquette-$id_casquette').html(data.html)
				eval(data.js);
				ed_scapi.reinitialise();
			}
		},
		'json'
	);
	$.post('ajax.php',{
			action:'edition/etablissement',
			id_etablissement:$id_etablissement,
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#ed_etablissement-$id_etablissement').html(data.html);
				eval(data.js);
				ed_ssapi.reinitialise();
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
