<?php
	if ($valeur) $valeur=" checked";
	else $valeur="";
	$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><input id=\"$prefixe"."_entree_$nom\" type=\"checkbox\"$valeur name=\"$nom\" class=\"$classes\" />
<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\"></div>";
	$this->entrees[$nom]['js']="
	
	$('#$prefixe"."_entree_$nom').bind('change',function(){
		form$prefixe.entrees['$nom'].value=this.checked;
		if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
		else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
	});
	
	form$prefixe.entrees['$nom'] = {
		value : '',
		test : function () {
			var obj = {ok : 1, message : ''}
			return obj;
		},
		reecriture : function () {
			if (this.value) this.value=1; else this.value=0;
			var obj = {
				html : '',
				value : this.value
			}
			return obj;
		}
	};
	form$prefixe.entrees['$nom'].value=".$this->entrees[$nom]['valeur'].";
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
	";
?>
