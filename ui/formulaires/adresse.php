<?php
	if ($valeur!="") {
		$tab=json_decode($valeur);
		$adresse=$tab->adresse;
		$cp=$tab->cp;
		$ville=$tab->ville;
		$pays=$tab->pays;
	} else {
		$adresse="";
		$cp="";
		$ville="";
		$pays="";
	}
	$this->entrees[$nom]['html']="
	<label>$label</label>
	<div class='cadre'>
		<span class='label'>adresse</span> <textarea id=\"$prefixe"."_entree_$nom"."_adresse\" type=\"text\" name=\"adresse\">$adresse</textarea><br/>
		<span class='label'>cp</span> <input id=\"$prefixe"."_entree_$nom"."_cp\" type=\"text\" value=\"$cp\" name=\"cp\"/><br/>
		<span class='label'>ville</span> <input id=\"$prefixe"."_entree_$nom"."_ville\" type=\"text\" value=\"$ville\" name=\"ville\"/><br/>
		<span class='label'>pays</span> <input id=\"$prefixe"."_entree_$nom"."_pays\" type=\"text\" value=\"$pays\" name=\"pays\"/><br/>
	</div>
	<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\"></div>
	";
	$this->entrees[$nom]['js']="
	$('#$prefixe"."_entree_$nom"."_adresse, #$prefixe"."_entree_$nom"."_cp, #$prefixe"."_entree_$nom"."_ville, #$prefixe"."_entree_$nom"."_pays').bind('keyup change focus focusout',function(){
		form$prefixe.entrees['$nom'].value={adresse:$('#$prefixe"."_entree_$nom"."_adresse').val(),cp:$('#$prefixe"."_entree_$nom"."_cp').val(),ville:$('#$prefixe"."_entree_$nom"."_ville').val(),pays:$('#$prefixe"."_entree_$nom"."_pays').val()};
		if (form$prefixe.entrees['$nom'].test().ok) $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
		else $('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].test().message);
	});
	$('#$prefixe"."_entree_$nom"."_cp, #$prefixe"."_entree_$nom"."_ville, #$prefixe"."_entree_$nom"."_pays').keydown(function(e) {
		if (e.keyCode == '13') {
			$('#$prefixe [rel=\"int_".$this->entrees[$nom]['formulaires'][0]."\"]').click();
		}
	});

	form$prefixe.entrees['$nom'] = {
		formulaires : [".implode(',',$this->entrees[$nom]['formulaires'])."],
		type : '$type',
		value : '',
		test : function () {
			var obj = {ok : 1, message : ''}
			return obj;
		},
		reecriture : function () {
			var obj = {
				html : '',
				value : {adresse:$.trim($('#$prefixe"."_entree_$nom"."_adresse').val()),cp:$.trim($('#$prefixe"."_entree_$nom"."_cp').val()),ville:$.trim($('#$prefixe"."_entree_$nom"."_ville').val()),pays:$.trim($('#$prefixe"."_entree_$nom"."_pays').val())}
			
			}
			return obj;
		}
	};
	form$prefixe.entrees['$nom'].value={adresse:$('#$prefixe"."_entree_$nom"."_adresse').html(),cp:$('#$prefixe"."_entree_$nom"."_cp').html(),ville:$('#$prefixe"."_entree_$nom"."_ville').html(),pays:$('#$prefixe"."_entree_$nom"."_adresse').html()};
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
	";
?>
