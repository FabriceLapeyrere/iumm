<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if (isset($_POST['id_news'])) $id_news=$_POST['id_news'];
	else $id_news=Newsletters::derniere();
	$html="";
	$html.="<div id='news_content' data-id='$id_news'>\n";
	$n=new Newsletter($id_news);
	$news=$n->news();
	if ($news!=""){
		$blocs=json_decode($n->news());
		$i=0;
		foreach($blocs as $bloc){
			$modele=Newsletters::modele($bloc->id_modele);
			$nom_modele=Newsletters::nom_modele($bloc->id_modele);
			$pattern = '#\::([a-zA-Z0-9_]*)\&(.*)::#';
			preg_match_all($pattern, $modele, $matches, PREG_OFFSET_CAPTURE, 3);
			foreach($matches[0] as $key=>$value){
				$valeur='';
				$nom=filter($matches[2][$key][0]);
				if (isset($bloc->params->$nom)) $valeur=$bloc->params->$nom;
				$modele=str_replace("::".$matches[1][$key][0]."&".$matches[2][$key][0]."::",$valeur,$modele);
			}
			$html.="<div class='bloc' id='blocs_$i'><div class='menuBloc'>
		<div>$nom_modele</div>
		<button class='modbloc ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' data-id='$i' data-idnews='$id_news' role='button' aria-disabled='false' title='modifier le bloc'>
			<span class='ui-button-icon-primary ui-icon ui-icon-pencil'></span>
			<span class='ui-button-text'>modifier le bloc</span>
		</button>
		<button class='supbloc ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' data-id='$i' data-idnews='$id_news' role='button' aria-disabled='false' title='supprimer le bloc'>
			<span class='ui-button-icon-primary ui-icon ui-icon-minusthick'></span>
			<span class='ui-button-text'>supprimer le bloc</span>
		</button>
			</div>\n";
			$html.="$modele\n";
			$html.="</div>\n";
			$i++;			
		}
	}
	$html.="<div class='bloc'>\n";
	$html.="<div style='height:100px;'></div>\n";
	$html.="</div>\n";
	$html.="</div>\n";
	$js="
	$('#news_content .bloc').droppable({
			tolerance: 'pointer',
			accept: 'ul.sf-menu>li>ul>li>ul>li',
			hoverClass: 'dessusBloc',
			drop: function( event, ui ) {
				var index = $(this).index();
				$.post('ajax.php',{action:'news/aj_bloc', id_news:$id_news, index:index, id:ui.draggable.dataset('id')},function(data){
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

