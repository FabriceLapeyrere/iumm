<?php
	$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><select id=\"$prefixe"."_entree_$nom\" name=\"$nom\" class=\"$classes\">";
	$id_news=$params['serveur']['id_news'];
	$chemin="fichiers/news/$id_news/";
	$this->entrees[$nom]['html'].="<option value=\"fichiers/iumm/vide.png\">(vide)</option>";
	if(file_exists($chemin)){
		if ($handle = opendir($chemin)) {
			while (false !== ($fichier = readdir($handle))) {
				if (is_file($chemin.$fichier)){
					if ($valeur!=$chemin.$fichier) {
						$this->entrees[$nom]['html'].="<option value=\"$chemin"."$fichier\">$fichier</option>";
					}
					else {
						$this->entrees[$nom]['html'].="<option selected value=\"$chemin"."$fichier\">$fichier</option>";
					}
				}
			}
		}
	}	
	$this->entrees[$nom]['html'].="</select>
<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\"></div>";
	$this->entrees[$nom]['js']="
	
	$('#$prefixe"."_entree_$nom').change(function(){
		form$prefixe.entrees['$nom'].value=$(this).val();
		if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
		else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
	});
	
	form$prefixe.entrees['$nom'] = {
		type : 'select',
		formulaires : [".implode(',',$this->entrees[$nom]['formulaires'])."],
		value : $('#$prefixe"."_entree_$nom').val(),
		test : function () {
			var obj = {ok : 1, message : ''}
			return obj;
		},
		reecriture : function () {
			var obj = {
				html : '',
				value : this.value
			}
			return obj;
		}
	};
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
	";
?>
