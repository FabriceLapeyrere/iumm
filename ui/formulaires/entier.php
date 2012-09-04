<?php
	$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><input id=\"$prefixe"."_entree_$nom\" type=\"text\" value=\"$valeur\" name=\"$nom\" class=\"$classes\" />
<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\"></div>";
	$this->entrees[$nom]['js']="
	$('#$prefixe"."_entree_$nom').bind('keyup change focus',function(){
		form$prefixe.entrees['$nom'].value=this.value;
		if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
		else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
	});
	$('#$prefixe"."_entree_$nom').keydown(function(e) {
		if (e.keyCode == '13') {
			$('#$prefixe [rel=\"int_".$this->entrees[$nom]['formulaires'][0]."\"]').click();
		}
	});
	form$prefixe.entrees['$nom'] = {
		formulaires : [".implode(',',$this->entrees[$nom]['formulaires'])."],
		type : 'entier',
		value : '',
		test : function () {
			var obj = {ok : 0, message : ''}
			if (!this.value.match(/^\d*$/)) {
				obj.message= '<span class=\"alert\">Ce n\'est pas un nombre entier.</span>';
			}
			else {
				obj.ok=1;
			}
			return obj;
		},
		reecriture : function () {
			var obj = {
				html : '<i>'+parseInt(this.value)+'</i>',
				value : parseInt($.trim(this.value))
			}
			return obj;
		}
	};
	form$prefixe.entrees['$nom'].value='".addslashes($this->entrees[$nom]['valeur'])."';
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().ok==1 ? form$prefixe.entrees['$nom'].reecriture().html : form$prefixe.entrees['$nom'].test().message);
	";
?>
