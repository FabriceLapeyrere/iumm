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
		type : 'telephone',
		value : '',
		test : function () {
			var obj = {ok : 0, message : ''}
			var epp=/^\+[0-9\-]{1,4}[\.| ][0-9]{4,14}(?:x.+)?$/;
			var france=/^0[1-9]\d{8}$/;
			var telf=this.value.replace(/[\- \.]/g,'').replace(/\([0-9]\)/,'');
			var telepp=this.value.replace(/^00/,'+').replace(/[\. ]/,'#').replace(/\([0-9]\)/,'').replace(/[\- \.\(\)]/g,'').replace('#','.');
			if (this.value=='' || epp.test(telepp) || france.test(telf)) {
				obj.ok=1;
			}
			else {
				obj.message= '<span style=\"color:red;\">le numéro n\'est pas reconnu...</span>';
			}
			return obj;
		},
		reecriture : function () {
			var tel='';
			var epp=/^\+[0-9\-]{1,4}[\.| ][0-9]{4,14}(?:x.+)?$/;
			var france=/^0[1-9]\d{8}$/;
			var telf=this.value.replace(/[\- \.]/g,'').replace(/\([0-9]\)/,'');
			var telepp=this.value.replace(/^00/,'+').replace(/[\. ]/,'#').replace(/\([0-9]?\)/,'').replace(/[\- \.\(\)]/g,'').replace('#','.');
			console.log(telepp);
			if (this.value=='') tel='';
			else if(epp.test(telepp)){
				var r=/\.[0-9]*$/;
				var s=r.exec(telepp)[0].replace('.','');
				for(var i=s.length; i>=1; i=i-2)
				{
					var a,b;
					if (i-2<0) {a=0;b=1;}
					else {a=i-2;b=2;}
					tel=(tel=='') ? s.substr(a,b) : s.substr(a,b)+' '+tel;
				}
				var r=/^\+[0-9\-]{1,4}\./;
				var tel=r.exec(telepp)[0].replace('.','') + ' ' + tel;
				
			}
			else if(france.test(telf)){
				for(var i=telf.length; i>=1; i=i-2)
				{
					var a,b;
					if (i-2<0) {a=0;b=1;}
					else {a=i-2;b=2;}
					tel= (tel=='') ? telf.substr(a,b) : telf.substr(a,b)+' '+tel;
				}
			}
			
			var obj = {
				html : '<span style=\"color:green;\"> '+tel+'</span>',
				value : tel
			}
			return obj;
		}
	};
	form$prefixe.entrees['$nom'].value='".addslashes($this->entrees[$nom]['valeur'])."';
	$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
	";
?>
