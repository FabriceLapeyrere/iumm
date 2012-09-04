<?php
	$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><input id=\"$prefixe"."_entree_$nom\" type=\"hidden\" value=\"$valeur\" name=\"$nom\" class=\"$classes\" /><div class='colorSelector'><div style='background-color:$valeur;'></div></div><div class='blocCouleur'>$valeur</div><div style='clear:both;'></div>
<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\"></div>";
	$this->entrees[$nom]['js']="
	$('#$prefixe"."_entree_$nom').next().ColorPicker({
		color: '$valeur',
		onShow: function (colpkr) {
		$(colpkr).fadeIn(500);
		return false;
		},
		onHide: function (colpkr) {
		$(colpkr).fadeOut(500);
		return false;
		},
		onChange: function (hsb, hex, rgb) {
			console.log('#' + hex);
			$('#$prefixe"."_entree_$nom').next().children('div').css('background', '#' + hex);
			$('#$prefixe"."_entree_$nom').next().next().html('#' + hex);
			$('#$prefixe"."_entree_$nom').val('#' + hex);
			form$prefixe.entrees['$nom'].value='#' + hex;
			if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
			else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
		}
	});
	form$prefixe.entrees['$nom'] = {
		formulaires : [".implode(',',$this->entrees[$nom]['formulaires'])."],
		type : 'couleur',
		value : '',
		test : function () {
			var obj = {ok : 1, message : ''}
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
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().ok==1 ? form$prefixe.entrees['$nom'].reecriture().html : form$prefixe.entrees['$nom'].test().message);
	";
?>
