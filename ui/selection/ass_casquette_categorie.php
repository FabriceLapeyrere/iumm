<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id_casquette=$_POST['id_casquette'];
	$id_categorie=$_POST['id_categorie'];
	
	
	$c=new Casquette($id_casquette);
	$c->ass_categorie($id_categorie);
	$id_etablissement=$c->id_etablissement;
	$e=new Etablissement($id_etablissement);
	
	#on rend le cache obsolete
	Cache::set_obsolete('casquette',$id_casquette);
	Cache::set_obsolete('etablissement',$id_etablissement);
	foreach($e->casquettes() as $id_cas=>$cas){
		Cache::set_obsolete('casquette',$id_cas);	
	}
	
	$js="
	$.post('ajax.php',{
			action:'edition/casquette',
			id_casquette:$id_casquette,
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#ed_casquette-$id_casquette').html(data.html)
				eval(data.js);
			}
			ed_scapi.reinitialise();
		},
		'json'
	);
	$.post('ajax.php',{
			action:'edition/etablissement',
			id_etablissement:$id_etablissement,
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#ed_etablissement-$id_etablissement').html(data.html)
				eval(data.js);
			}
			ed_ssapi.reinitialise();
		},
		'json'
	);
	$.post('ajax.php',{
			action:'selection/casquette',
			id_casquette:$id_casquette,
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#sel_casquette-$id_casquette').html(data.html)
				eval(data.js);
			}
			sel_scasapi.reinitialise();
		},
		'json'
	);
	$.post('ajax.php',{
			action:'edition/nbincat',
			id_categorie:$id_categorie,
			format:'html'
		},function(data){
			if(data.succes==1){
				$('#sel_dynatree-id-$id_categorie').find('.nbincat').first().html('('+data.html+')');
				$('#ed_dynatree-id-$id_categorie').find('.nbincat').first().html('('+data.html+')');
			}
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
	$c=new Categorie($id_categorie);
	while ($c->id_parent!=0){
		$c=new Categorie($c->id_parent);
		$js.="
		$.post('ajax.php',{
				action:'edition/nbincat',
				id_categorie:".$c->id.",
				format:'html'
			},function(data){
				if(data.succes==1){
					$('#sel_dynatree-id-".$c->id."').find('.nbincat').first().html('('+data.html+')');
					sel_scatapi.reinitialise();
				}
			},
			'json'
		);
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
