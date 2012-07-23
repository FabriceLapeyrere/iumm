<?php
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
?>
