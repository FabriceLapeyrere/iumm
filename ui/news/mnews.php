<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	if (isset($_POST['id_news'])) $id_news=$_POST['id_news'];
	else $id_news=Newsletters::derniere();
	if ($id_news==0) {
		$html="Aucune newsletter.";
	} else {
		$_SESSION['news_rep']="news/$id_news/";
		$html="";
		$html.="<div id='news_content' data-id='$id_news'>\n";
		$n=new Newsletter($id_news);
		$news=$n->news();
		if ($news!=""){
			$blocs=json_decode($n->news());
			$i=0;
			foreach($blocs as $bloc){
				$id_bloc=$bloc->id_bloc;
				$modele=Newsletters::modele($bloc->id_modele);
				$nom_modele=Newsletters::nom_modele($bloc->id_modele);
				$pattern = "/::([^::]*)::/";
				preg_match_all($pattern, $modele, $matches, PREG_OFFSET_CAPTURE, 3);
				foreach($matches[0] as $key=>$value){
					$code=$matches[0][$key][0];
					$tab=explode('&',$matches[1][$key][0]);
					$type=$tab[0];
					$label=$tab[1];
					$nom=filter($label);
					$valeur='';
					if (isset($bloc->params->$nom)) $valeur=$bloc->params->$nom;
					$valeur_mnews=$valeur;
					if(file_exists("ui/news/elements/elt_$type.php")) include "ui/news/elements/elt_$type.php";
					$modele=str_replace($code,$valeur_mnews,$modele);
				}
				$html.="<div class='bloc' id='blocs_$i'><div class='menuBloc'>
			<div>$nom_modele</div>
			<button class='modbloc ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' data-id='$id_bloc' data-idnews='$id_news' role='button' aria-disabled='false' title='modifier le bloc'>
				<span class='ui-button-icon-primary ui-icon ui-icon-pencil'></span>
				<span class='ui-button-text'>modifier le bloc</span>
			</button>
			<button class='supbloc ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' data-id='$id_bloc' data-idnews='$id_news' role='button' aria-disabled='false' title='supprimer le bloc'>
				<span class='ui-button-icon-primary ui-icon ui-icon-minusthick'></span>
				<span class='ui-button-text'>supprimer le bloc</span>
			</button>
				</div>\n";
				$html.="$modele\n";
				$html.="</div>\n";
				$i++;			
			}
		}
		$html.="<div class='bloc last'>\n";
		$html.="<div style='height:100px;'></div>\n";
		$html.="</div>\n";
		$html.="</div>\n";
		$html.=Html::upload_news();
	
		$js="
		$('.bloc').hover(
			function(event){
				if (menuLock==0){
					$('.bloc').each(function(i,e){ $(e).removeClass('actif'); });
					$(this).addClass('actif');
					$(this).children('.menuBloc').show();
				}
			},
			function(event){
				if (menuLock==0){
					$(this).removeClass('actif');
					$(this).children('.menuBloc').hide();
				}
			}
		);
		$('.menuBloc').hover(
			function(event){
				menuLock=1;
			},
			function(event){
				menuLock=0;
			}
		);
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
					news_stop();
					$.post('ajax.php','action=news/ord_blocs&id_news=$id_news&'+$('#news_content').sortable('serialize'),function(data){
							if(data.succes==1){
								eval(data.js);
							}
						},
					'json');
				},
				axis:'Y',
				opacity: 0.6,
				appendTo: 'body',
				helper: 'clone',
				sort:function(e,ui){
					if (e.pageY-$('#news_newsletter').offset().top>$('#news_newsletter').height()*0.75) news_descend(0);
					if (e.pageY-$('#news_newsletter').offset().top<$('#news_newsletter').height()*0.25) news_monte(0);
				}
			});
		".js::upload_news()."
		";
	}
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

