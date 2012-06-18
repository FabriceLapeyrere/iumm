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
			title:'Impossible d\'ajouter une casquette.',
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
		$id_etablissement=$_POST['id_etablissement'];
		$id_contact=$_POST['id_contact'];
		$e=new Etablissement($id_etablissement);
		$c=new Contact($id_contact);
		$id_casquette=$c->aj_casquette($e->nom_structure, $_SESSION['user']['id']);
		$cas= new Casquette($id_casquette);
		$cas->ass_etablissement($id_etablissement, $_SESSION['user']['id']);
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
