<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id_news=$_POST['id_news'];
	$id_bloc=$_POST['id_bloc'];
	$html="";
	$js="";
	$n=new Newsletter($id_news);
	$news=$n->news();
	$blocs=json_decode($news);
	$bloc=$blocs[$id_bloc];
	$modele=Newsletters::modele($bloc->id_modele);
	$form=new formulaires;
	$form->prefixe="mbloc$id_news"."_$id_bloc";
	$form->ajoute_entree('id_news', 'hidden', $id_news, '', array(1,2));
	$form->ajoute_entree('id_bloc', 'hidden', $id_bloc, '', array(1,2));
	$pattern = '#\::([a-zA-Z0-9_]*)\&(.*)::#';
	preg_match_all($pattern, $modele, $matches, PREG_OFFSET_CAPTURE, 3);
	foreach($matches[0] as $key=>$value){
		$type=$matches[1][$key][0];
		$label=$matches[2][$key][0];
		$nom=filter($matches[2][$key][0]);
		$valeur='';
		if (isset($bloc->params->$nom)) $valeur=$bloc->params->$nom;
		$form->ajoute_entree($nom, 'texte_court', $valeur, '', array(1,2),$label);
		$html.='type : '.$matches[1][$key][0].' '.'nom : '.$matches[2][$key][0].'<br />';
	}
	$form->ajoute_interrupteur('update', 'bouton', 'Mettre à jour', 'bouton', 1, 'news/update_bloc');
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 2, 'news/mod_bloc');
	$html.="<ul>";
	foreach ($form->entrees as $nom => $value) {
		$html.="<li data-nom=\"$nom\" data-type=\"".$value['type']."\">".$value['html']."</li>";
	}
	$html.=$form->interrupteurs['update']['html'];
	$html.=$form->interrupteurs['valider']['html'];
	$js.=$form->initjs();
	foreach ($form->entrees as $key => $value) {
		$js.=$value['js'];
	}
	foreach ($form->interrupteurs as $key => $value) {
		$js.=$value['js'];
	}
	$html.="</ul>";
	$js.="
	$('#news_content .bloc').droppable({
			accept: 'ul.sf-menu>li>ul>li>ul>li',
			hoverClass: 'dessusBloc',
			drop: function( event, ui ) {
				var index = $(this).index();
				$.post('ajax.php',{action:'news/aj_bloc', id_news:$id_news, index:index, nom:ui.draggable.html()},function(data){
				if(data.succes==1){
					eval(data.js);
				}
			},'json'
		);
			}
		});
		$('#news_content').sortable({
			update: function(event, ui) {
				$.post('ajax.php','action=news/ord_blocs&id_news=$id_news&'+$('#news_content').sortable('serialize'),function(data){
						if(data.succes==1){
							eval(data.js);
						}
					},
				'json');
			}
		});
	";
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>

