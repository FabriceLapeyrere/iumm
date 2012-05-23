<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id'];
	$label=$_POST['label'];
	$type=$_POST['type'];
	
	#on nettoie le label pour créer le nom
	$nom=filter($label);
	
	$valeur="";
	$c=new Etablissement($id);
	$j=0;
	$liste=$c->liste_champs();
	$nom_orig=$nom;
	$label_orig=$label;
	while(in_array($nom,$liste)) {
		$j++;
		$nom="$nom_orig"."_$j";
		$label="$label_orig"." $j";
	}
	$c->aj_donnee($nom,$label,$type,"");
	$form=new formulaires;
	$form->prefixe="metab$id";
	$form->ajoute_entree($nom, $type, $valeur, '',array(1), $label);
	$html=$form->entrees[$nom]['html'];
	$js=$form->entrees[$nom]['js'];
	$js.="
$.post('ajax.php',{
		action:'edition/etablissement',
		id_etablissement:$id,
		format:'html'
	},function(data){
		if(data.succes==1) $('#ed_etablissement-$id').html(data.html)
		eval(data.js);
		ed_ssapi.reinitialise();
	},
	'json'
);
$('#metab$id ul.champs>li[data-nom=\"$nom\"] label:nth-child(1)').after('".addslashes(html::bouton_suppr(0,'moins','supprimer','5px'))."');
	";	
	if ($type=='adresse') $js.="$('#metab$id ul.plus [data-type=\"adresse\"]').remove();";
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['html']=$html;
		$reponse['nom']=$nom;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
