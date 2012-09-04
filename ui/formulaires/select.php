<?php
	$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><select id=\"$prefixe"."_entree_$nom\" name=\"$nom\" class=\"$classes\">";
	$options=explode('::',$valeur);
	foreach($options as $option) {
		$tab=explode(',',$option);
		$val=$tab[0];
		$lib=$tab[1];
		if (strpos($lib,'*')===false) {
			$this->entrees[$nom]['html'].="<option value=\"$val\">$lib</option>";
		}
		else {
			$this->entrees[$nom]['html'].="<option selected value=\"$val\">".str_replace('*','',$lib)."</option>";
			$valeur_select=$val;
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
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().ok==1 ? form$prefixe.entrees['$nom'].reecriture().html : form$prefixe.entrees['$nom'].test().message);
	";
?>
