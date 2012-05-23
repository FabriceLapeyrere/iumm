<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */


/*
 * la classe formulaires contient tout ce dont nous avons besoin pour
 * gerer les entrées utilisateur, tant coté client (js) que serveur (php)
 * 
 */

							
class formulaires {
	var $entrees=array();
	var $interrupteurs=array();
	var $prefixe="";
	function initjs() {
		$js= "form".$this->prefixe."={entrees:{}, interrupteurs:{}};";
		return $js;
	}
	function prefixe ($prefixe="") {
		$this->prefixe="id".time().$prefixe;	
	} 
	
	/* 08/10/2010 Fabrice Lapeyrere
	 * name: ajoute_entree
	 * 
	 * génère une entrée de formulaire avec le code js de vérification et de
	 * réécriture de l'entrée utilisateur 
	 *  
	 * @param $nom string nom de l'entrée
	 * @param $type string type de l'entrée 'texte_court', 'texte_long', 'email',
	 * 'telephone', etc.
	 * @param $valeur string la valeur par défaut du champ
	 * @param $classes string les classes css à associer
	 * @param $formulaires array les id des formulaires parents
	 * @return bool true si l'ajout c'est bien passé, false sinon.
	 */
    function ajoute_entree($nom, $type, $valeur, $classes, $formulaires,$label="") {
	$prefixe=$this->prefixe;
	$this->entrees[$nom]['label']=$label;
	$this->entrees[$nom]['type']=$type;
        $this->entrees[$nom]['valeur']=$valeur;
        $this->entrees[$nom]['classes']=$classes;
	$this->entrees[$nom]['formulaires']=$formulaires;
	$this->entrees[$nom]['html']="";
        $this->entrees[$nom]['js']="";
        switch ($type) {
			case 'hidden':
				$this->entrees[$nom]['html']="<input id=\"$prefixe"."_entree_$nom\" type=\"hidden\" value=\"$valeur\" name=\"$nom\" class=\"$classes\" />";
				$this->entrees[$nom]['js']="
				
				form$prefixe.entrees['$nom'] = {
					formulaires : [".implode(',',$this->entrees[$nom]['formulaires'])."],
					type : 'hidden',
					value : '$valeur',
					test : function () {
						var obj = {ok : 1, message : ''}
						return obj;
					},
					reecriture : function () {
						var obj = {
							html : this.value,
							value : this.value
						}
						return obj;
					}
				};
				";
				break;

			case 'entier':
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
				$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
				";
				break;
				
			case 'nombre':
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
				break;
				
			case 'texte_court':
				$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><input id=\"$prefixe"."_entree_$nom\" type=\"text\" value=\"$valeur\" name=\"$nom\" class=\"$classes\" />
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
					type : 'texte_court',
					value : '',
					test : function () {
						var obj = {ok : 0, message : ''}
						if (this.value.length>50) {
							obj.message= '<span style=\"color:red;\">c\'est trop long !</span>';
						}
						else {
							obj.ok=1;
						}
						return obj;
					},
					reecriture : function () {
						var obj = {
							html : '<span style=\"color:green;\"> '+this.value+'</span>',
							value : $.trim(this.value)
						}
						return obj;
					}
				};
				form$prefixe.entrees['$nom'].value='".addslashes($this->entrees[$nom]['valeur'])."';
				$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
				";
				break;

			case 'email':
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
						var email=no_accent(this.value.replace(/ at /,'@').replace(/\(at\)/,'@').replace(/ /g,''));
						if (this.value=='' || test.test(email)) {
							obj.ok=1;
						}
						else {
							obj.message= '<span style=\"color:red;\">l\'email n\'est pas reconnu...</span>';
						}
						return obj;
					},
					reecriture : function () {
						var email=no_accent(this.value.replace(/ at /,'@').replace(/\(at\)/,'@').replace(/ /g,''));
						var obj = {
							html : '<span style=\"color:green;\"> '+email+'</span>',
							value : email
						}
						return obj;
					}
				};
				form$prefixe.entrees['$nom'].value='".addslashes($this->entrees[$nom]['valeur'])."';
				$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
				";
				break;

			case 'telephone':
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
				break;

			case 'adresse':
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
				break;

			case 'texte_long':
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
				$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
				";
				break;

			case 'select':
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
				$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
				";
				break;

			case 'date':
				$this->entrees[$nom]['html']="<label for=\"$prefixe"."_entree_$nom\">$label</label><br /><input id=\"$prefixe"."_entree_$nom\" type=\"text\" value=\"$valeur\" name=\"$nom\" class=\"$classes\" />
<div id=\"$prefixe"."_entree_$nom-infos\" class=\"tooltip\"></div>";
				$this->entrees[$nom]['js']="
				
				$( '#$prefixe"."_entree_$nom' ).datepicker({
							altField: '#$prefixe"."_entree_$nom-infos',
							altFormat: 'dd/mm/yy',
							dateFormat: 'dd/mm/yy',
							showOn: 'button',
							buttonImage: 'ui/css/custom-theme/images/calendar.gif',
							buttonImageOnly: true
				});
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
					type : 'date',
					value : '',
					test : function () {
						var obj = {ok : 0, message : ''}
						if (!this.value.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
							obj.message= '<span class=\"alert\">Ce n\'est pas une date valide.</span>';
						}
						else {
							obj.ok=1;
						}
						return obj;
					},
					reecriture : function () {
						var obj = {
							html : '<i>'+this.value+'</i>',
							value : this.value
						}
						return obj;
					}
				};
				form$prefixe.entrees['$nom'].value='".addslashes($this->entrees[$nom]['valeur'])."';
				$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.entrees['$nom'].reecriture().html);
				";
				break;

			case 'case':
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
				break;
		}
    }
/* 08/10/2010 Fabrice Lapeyrere
 * name: ajoute_interrupteur
 * 
 * génère un interrupteur pour poster les infos d'un formulaire, avec
 * le code js pour afficher le resultat du traitement.
 *  
 * @param $nom string nom de l'interrupteur
 * @param $type string type de l'interrupteur 'bouton', 'image', 'texte'
 * @param $valeur string le texte à afficher
 * @param $classes string les classes css à associer
 * @param $formulaire int l'id du formulaire
 * @param $action string le code js à executer
 * @return bool true si l'ajout c'est bien passé
 */
    function ajoute_interrupteur($nom, $type, $valeur, $classes, $formulaire, $action, $callback="function (){}") {
	$prefixe=$this->prefixe;
	$this->interrupteurs[$nom]['type']=$type;
        $this->interrupteurs[$nom]['valeur']=$valeur;
        $this->interrupteurs[$nom]['classes']=$classes;
	$this->interrupteurs[$nom]['formulaire']=$formulaire;
        $this->interrupteurs[$nom]['action']=$action;
        $this->interrupteurs[$nom]['prefixe']=$prefixe;
        switch ($type) {
			case 'bouton':
				$test="";
				$valeurs="";
				$this->interrupteurs[$nom]['html']="<button id=\"$prefixe"."_interrupteur_$nom\" rel=\"int_".$this->interrupteurs[$nom]['formulaire']."\" name=\"$nom\" class=\"$classes\">$valeur</button>
<div id=\"$prefixe"."_interrupteur_$nom-infos\" class=\"tooltip\"></div>";
				$this->interrupteurs[$nom]['js']="
				
				$('#$prefixe"."_interrupteur_$nom').click(function(){
					var test=form$prefixe.interrupteurs['$nom'].test();
					if (test.ok) {
						$('#$prefixe"."_interrupteur_$nom-infos').html('');
						$('#$prefixe"."_entree_$nom-infos').html(form$prefixe.interrupteurs['$nom'].action(test.valeurs));
					}
					else $('#$prefixe"."_interrupteur_$nom-infos').html(test.message);
				});
				
				form$prefixe.interrupteurs['$nom'] = {
					formulaire:".$this->interrupteurs[$nom]['formulaire'].",
					test : function () {
						var estdedans=0;
						var test=1;
						var valeurs={};
						for (cle in form$prefixe.entrees) {
							var e=form$prefixe.entrees[cle];
							for(var i=0; i < e.formulaires.length; i++)
							{
								if(e.formulaires[i]==this.formulaire) estdedans=1;
							}
							if (estdedans==1) {
								if (e.test().ok==0) test=0;
								var v={};
								v[cle]={valeur:e.reecriture().value, type:e.type};
								$.extend(true, valeurs, v);
							}
						}
						console.log(valeurs);
						obj = {ok : 0, message : '', valeurs : valeurs}
						if (test) {
							obj.ok=1;
						}
						else {
							obj.message= 'verifiez s\'il vous plait';
							$('#$prefixe .tooltip').show();
						}
						return obj;
					},
					action : function (valeurs) {
						$.post(
							'ajax.php',
							$.extend({action : '$action'}, valeurs),
							function(data){
								if (data.succes==1) {
									eval(data.js);
									infos(data.message);
								}
							},
							'json');
					}
				}
				;";
				break;
		}
    }
	
}
?>
