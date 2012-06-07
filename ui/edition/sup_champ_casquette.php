<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if ($_SESSION['user']['droits']<3){
		$js="
		$('<div>Vos droits sont insuffisants.</div>').dialog({
			resizable: false,
			title:'Impossible de supprimer le champ.',
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
		$id=$_POST['id'];
		$nom=$_POST['nom'];
	
		#on rend le cache obsolete
		Cache::set_obsolete('casquette',$id);

		$c=new Casquette($id);
		$liste=$c->sup_donnee($nom, $_SESSION['user']['id']);
		$js="";
		$js.="
	$.post('ajax.php',{
			action:'edition/casquette',
			id_casquette:$id,
			format:'html'
		},function(data){
			if(data.succes==1) $('#ed_casquette-$id').html(data.html)
			eval(data.js);
			ed_scapi.reinitialise();
		},
		'json'
	);
	$('#mcas$id ul.champs>li[data-nom=\"$nom\"]').remove();
	delete formmcas$id.entrees['$nom'];
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
