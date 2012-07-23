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
			$('#d_$prefixe [rel=\"int_".$this->entrees[$nom]['formulaires'][0]."\"]').click();
		}
	});
	
	form$prefixe.entrees['$nom'] = {
		type : 'nombre',
		value : '',
		test : function () {
			var obj = {ok : 0, message : ''}
			if (!this.value.match(/^\d*[.,]\d*$/) && !this.value.match(/^\d*$/)) {
				obj.message= '<span class=\"alert\">Ce n\'est pas un nombre</span>';
			}
			else {
				obj.ok=1;
			}
			return obj;
		},
		reecriture : function () {
			var obj = {
				html : '<i>'+parseFloat(this.value.replace(/,/g,'.'))+'</i>',
				value : parseFloat(this.value.replace(/,/g,'.'))
			}
			return obj;
		}
	};
	form$prefixe.entrees['$nom'].value='".addslashes($this->entrees[$nom]['valeur'])."';
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
	";
?>
