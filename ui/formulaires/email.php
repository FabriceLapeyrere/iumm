<?php
	$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><input id=\"$prefixe"."_entree_$nom\" type=\"text\" value=\"$valeur\" name=\"$nom\" class=\"$classes\" />
<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\"></div>";
	$this->entrees[$nom]['js']="
	$('#$prefixe"."_entree_$nom').bind('keyup change focus focusout ',function(){
		form$prefixe.entrees['$nom'].value=this.value;
		if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
		else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
	});
	$('#$prefixe"."_entree_$nom').keydown(function(e) {
		var test=0;
		if (e.keyCode == '13' && test==0) {
			$('#$prefixe [rel=\"int_".$this->entrees[$nom]['formulaires'][0]."\"]').click();
		}
	});
	form$prefixe.entrees['$nom'] = {
		formulaires : [".implode(',',$this->entrees[$nom]['formulaires'])."],
		type : '$type',
		value : '',
		test : function () {
			var obj = {ok : 0, message : ''}
			var test=/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			var email=no_accent(this.value.replace(/ at /,'@').replace(/\(at\)/,'@').replace(/\[at\]/,'@').replace(/ /g,''));
			if (this.value=='' || test.test(email)) {
				obj.ok=1;
			}
			else {
				obj.message= '<span style=\"color:red;\">l\'email n\'est pas reconnu...</span>';
			}
			return obj;
		},
		reecriture : function () {
			var email=no_accent(this.value.replace(/ at /,'@').replace(/\(at\)/,'@').replace(/\[at\]/,'@').replace(/ /g,''));
			var obj = {
				html : '<span style=\"color:green;\"> '+email+'</span>',
				value : email
			}
			return obj;
		}
	};
	form$prefixe.entrees['$nom'].value='".addslashes($this->entrees[$nom]['valeur'])."';
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().ok==1 ? form$prefixe.entrees['$nom'].reecriture().html : form$prefixe.entrees['$nom'].test().message);
	";
?>
