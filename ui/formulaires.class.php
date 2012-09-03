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
    function ajoute_entree($nom, $type, $valeur, $classes, $formulaires,$label="",$params=array()) {
        if (file_exists("ui/formulaires/$type.php")) {
			$prefixe=$this->prefixe;
			$this->entrees[$nom]['label']=$label;
			$this->entrees[$nom]['type']=$type;
		    $this->entrees[$nom]['valeur']=$valeur;
		    $this->entrees[$nom]['classes']=$classes;
			$this->entrees[$nom]['formulaires']=$formulaires;
			$this->entrees[$nom]['html']="";
		    $this->entrees[$nom]['js']="";
			include "ui/formulaires/$type.php";
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
