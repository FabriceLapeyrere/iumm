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
			title:'Impossible d\'ajouter un bloc.',
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
		$id_orig=$_POST['id_n_orig'];
		$id_bloc=$_POST['id_bloc'];
		$id_news=$_POST['id_news'];
		$n=new Newsletter($id_orig);
		$news_orig=$n->news();
		$blocs_orig=json_decode($news_orig);
		$i=0;
		foreach($blocs_orig as $b){
			if ($b->id_bloc==$id_bloc) $bloc=$b;
		}
		$bloc->id_bloc=time();
		$nn=new Newsletter($id_news);
		$news=$nn->news();
		$blocs=json_decode($news);
		$blocs[]=$bloc;
		$news=json_encode($blocs);
		$nn->aj_donnee($news);
		$chemin="fichiers/news/$id_orig/";
		if(file_exists($chemin)){
			if ($handle = opendir($chemin)) {
				while (false !== ($fichier = readdir($handle))) {
					if (is_file($chemin.$fichier)){
						foreach($b->params as $cle=>$valeur){
							if($valeur==$chemin.$fichier){
								copy($chemin.$fichier,"fichiers/news/$id_news/$fichier");
								$b->params->$cle="fichiers/news/$id_news/$fichier";
							}
						}
					}
				}
			}
		}	
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
