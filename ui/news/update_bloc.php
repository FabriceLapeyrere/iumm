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
			title:'Impossible de supprimer un bloc.',
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
		$id_news=$_POST['id_news']['valeur'];
		$id_bloc=$_POST['id_bloc']['valeur'];
		$n=new Newsletter($id_news);
		$news=$n->news();
		$blocs=json_decode($news);
		$i=0;
		foreach($blocs as $b){
			if ($b->id_bloc==$id_bloc) $index=$i;
			$i++;
		}
		$bloc=$blocs[$index];
		$id_modele=$bloc->id_modele;
		$modele=Newsletters::modele($id_modele);
		$pattern = "/::([^::]*)::/";
		preg_match_all($pattern, $modele, $matches, PREG_OFFSET_CAPTURE, 3);
		foreach($matches[0] as $key=>$value){
				$code=$matches[0][$key][0];
				$tab=explode('&',$matches[1][$key][0]);
				$type=$tab[0];
				$label=$tab[1];
				$nom=filter($label);
				if(isset($_POST[$nom])){
				if (is_array($bloc->params)) $bloc->params=json_decode('{}');
				$bloc->params->$nom=$_POST[$nom]['valeur'];
			}
		}
		$blocs[$index]=$bloc;
		$news=json_encode($blocs);
		$n->aj_donnee($news);
		$js="
		$.post('ajax.php',{
				action:'news/mnews',
				id_news:$id_news,
				format:'html'
			},
			function(data){
				if (data.succes==1) {
					$('#news_newsletter .jspPane').html(data.html);
					eval(data.js);
					news_snapi.reinitialise();
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
