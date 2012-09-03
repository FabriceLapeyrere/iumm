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
		$structure=$e->structure();
		$id_casquette=$c->aj_casquette($structure['nom'], $_SESSION['user']['id']);
		$cas= new Casquette($id_casquette);
		$cas->ass_etablissement($id_etablissement, $_SESSION['user']['id']);
		Cache::set_obsolete('etablissement',$id_etablissement);
		Cache::set_obsolete('contact',$id_contact);
		async('ui/cache/cache',array('objet'=>'casquette','id_objet'=>$e->casquette_propre()));
		async('ui/cache/cache',array('objet'=>'casquette_sel','id_objet'=>$e->casquette_propre()));
		async('ui/cache/cache',array('objet'=>'structure','id_objet'=>$structure['id']));
		$js="
		$('#ed_contact-$id_contact').html('".json_escape(Html::contact($id_contact))."');
		".Js::contact($id_contact)."
		$('#ed_etablissement-$id_etablissement').html('".json_escape(Html::etablissement($id_etablissement))."');
		".Js::etablissement($id_etablissement)."
		var action=function(){
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
		}
		setTimeout(action,5000);
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
