<?php
	$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><textarea id=\"$prefixe"."_entree_$nom\" name=\"$nom\" class=\"$classes\">$valeur</textarea>
<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\" style='display:none;'></div>";
	$this->entrees[$nom]['js']="
	
	$('#$prefixe"."_entree_$nom').bind('keyup change focus',function(){
		form$prefixe.entrees['$nom'].value=$('#$prefixe"."_entree_$nom').val();
		if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
		else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
	});

	form$prefixe.entrees['$nom'] = {
		formulaires : [".implode(',',$this->entrees[$nom]['formulaires'])."],
		type : 'texte_long',
		value : '',
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
	form$prefixe.entrees['$nom'].value=$('#$prefixe"."_entree_$nom').html();
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().ok==1 ? form$prefixe.entrees['$nom'].reecriture().html : form$prefixe.entrees['$nom'].test().message);
	";
?>
