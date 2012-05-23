<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=0;
	$id_etablissement=$_POST['id_etablissement'];
	$id_contact=$_POST['id_contact'];
	$e=new Etablissement($id_etablissement);
	$c=new Contact($id_contact);
	$id_casquette=$c->aj_casquette($e->nom_structure);
	$cas= new Casquette($id_casquette);
	$cas->ass_etablissement($id_etablissement);
	$succes=0;
	if ($id_casquette>0) $succes=1;
	$js="
	$.post('ajax.php',{
			action:'edition/etablissement',
			id_etablissement:$id_etablissement,
			format:'html'
		},function(data){
			if(data.succes==1) { 
				$('#ed_etablissement-$id_etablissement').html(data.html);
				eval(data.js);
			}
			ed_ssapi.reinitialise();
		},
		'json'
	);
	$.post('ajax.php',{
			action:'edition/contact',
			id_contact:$id_contact,
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#ed_contact-$id_contact').html(data.html)
				eval(data.js);
			}
			ed_scapi.reinitialise();
		},
		'json'
	);
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
