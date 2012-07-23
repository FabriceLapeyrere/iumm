<?php
	$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><input id=\"$prefixe"."_entree_$nom\" type=\"password\" value=\"$valeur\" name=\"$nom\" class=\"$classes\" />
<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\"></div>";
	$this->entrees[$nom]['js']="
	$('#$prefixe"."_entree_$nom').bind('keyup change focus focusout',function(){
		form$prefixe.entrees['$nom'].value=this.value;
		if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
		else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
	});
	$('#$prefixe"."_entree_$nom').keydown(function(e) {
		var test=0;
		$('ul.ui-autocomplete').each(function(i,e){
			if ($(this).css('display')!='none') test++;
		})
		if (e.keyCode == '13' && test==0) {
			$('#$prefixe [rel=\"int_".$this->entrees[$nom]['formulaires'][0]."\"]').click();
		}
	});
	/*$('#$prefixe"."_entree_$nom').autocomplete({
		source: 'ajax.php?action=input_complete&nom=$nom',
		minLength: 2,
		select: function(event, ui) {
			form$prefixe.entrees['$nom'].value=ui.item.value;
			if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
			else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
		}
	});*/
	
	form$prefixe.entrees['$nom'] = {
		formulaires : [".implode(',',$this->entrees[$nom]['formulaires'])."],
		type : 'mdp',
		value : '',
		test : function () {
			var obj = {ok : 0, message : ''}
			if (this.value.length<4) {
				obj.message= '<span style=\"color:red;\">c\'est trop court !</span>';
			}
			else {
				obj.ok=1;
			}
			return obj;
		},
		reecriture : function () {
			var obj = {
				html : '',
				value : $.trim(this.value)
			}
			return obj;
		}
	};
	form$prefixe.entrees['$nom'].value='".addslashes($this->entrees[$nom]['valeur'])."';
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
	";
?>
