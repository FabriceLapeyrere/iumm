<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id'];
	$nom=$_POST['nom'];
	$e=new Etablissement($id);
	$casquettes=$e->casquettes();
	$liste=$e->sup_donnee($nom, $_SESSION['user']['id']);
	$js="";
	$js.="
$.post('ajax.php',{
		action:'edition/etablissement',
		id_etablissement:$id,
		format:'html'
	},function(data){
		if(data.succes==1) $('#ed_etablissement-$id').html(data.html)
		eval(data.js);
		ed_ssapi.reinitialise();
	},
	'json'
);
$('#metab$id ul.champs>li[data-nom=\"$nom\"]').remove();
delete formmetab$id.entrees['$nom'];
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
	if ($type=='adresse') $js.="$('#metab$id ul.plus').prepend('<li class=\"champ\" data-nom=\"adresse\" data-type=\"adresse\">adresse</li>');";
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
