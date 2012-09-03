<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_casquette'];
	$c= new Casquette($id);	
	$h=new html();
	$form=new formulaires;
	$form->prefixe="mcas$id";
	
	$form->ajoute_entree('id', 'hidden', $id, '', array(1));
	$html="";
	$js="";
	$test=1;
	$test2=1;
	$adresse="";
	foreach($c->donnees() as $nom=>$donnee){
		if ($donnee[2]=='adresse') $test=0;
		if ($nom=='Fonction') $test2=0;
	}
	$adresse="<li class='champ' data-type='adresse'>Adresse</li>";
	$a="";
	if ($test==1) $a=$adresse;
	$fonction="<li class='champ' data-type='texte_court'>Fonction</li>";
	$b="";
	if ($test2==1) $b=$fonction;	
	$html.="
<button class='plus'>Ajouter un champ</button>
<ul class='plus ui-widget-content'>
	$a
	<li class='champ' data-type='telephone'>Téléphone fixe</li>
	<li class='champ' data-type='telephone'>Téléphone portable</li>
	<li class='champ' data-type='email'>E-mail</li>
	$b
	<li class='champ' data-type='texte_long'>Note</li>
	<li><span class='generique'>Champ personnalisé</span>
	<div class='perso' style='display:none;'>
		<input type='text' name='label'/>
		<select name='type'>
			<option value='telephone'>Téléphone</option>
			<option value='email'>E-mail</option>
			<option value='texte_court'>Texte court</option>
			<option value='texte_long'>Texte long</option>
		</select>
		<button class='bouton' name='ajouter'>Ajouter</button>
	</div>
	</li>
</ul>
	";
	$js.="
$('#mcas$id .generique').click(function(){ $(this).next().slideToggle();});
$('#mcas$id button.plus').button({ icons: {primary:'ui-icon-plusthick'}, text: false}).click(function(){ $(this).next().slideToggle();});
$('#mcas$id ul.plus').click(function(event){
     event.stopPropagation();
});
$('#mcas$id button.plus').click(function(event){
     event.stopPropagation();
});

$('#mcas$id ul.plus').on('mouseenter','li',function(){ $(this).addClass('ui-state-hover');}).on('mouseleave','li',function(){ $(this).removeClass('ui-state-hover');});

$('#mcas$id ul.plus').on('click','li.champ',function(){
	$('#mcas$id ul.plus').slideToggle();
	var label=$(this).html();
	var type=$(this).dataset('type');
	$.post(
		'ajax.php',
		{action : 'edition/aj_champ_casquette', id:$id, label:label, type:type },
		function(data){
			if (data.succes==1) {
				$('#mcas$id ul.champs').append('<li data-nom=\"'+data.nom+'\" data-type=\"'+type+'\">' + data.html + '</li>');
				eval(data.js);
				infos(data.message);
			}
		},
		'json'
	);
});
$('#mcas$id ul.plus div.perso').on('click','button',function(){
	var label=$(this).prev().prev().val();
	var type=$(this).prev().val();
	if (label!='') {
		$('#mcas$id ul.plus').slideToggle();
		$.post(
			'ajax.php',
			{action : 'edition/aj_champ_casquette', id:$id, label:label, type:type },
			function(data){
				if (data.succes==1) {
					$('#mcas$id ul.champs').append('<li data-nom=\"'+data.nom+'\" data-type=\"'+type+'\">' + data.html + '</li>');
					eval(data.js);
					infos(data.message);
				}
			},
			'json'
		);
	}
});	";
	foreach($c->donnees() as $nom=>$donnee){
		$label_d=$donnee[1];
		$type_d=$donnee[2];
		$valeur_d=$donnee[0];
			
		$form->ajoute_entree($nom, $type_d, $valeur_d, '',array(1),$label_d);
	}
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'edition/mod_casquette');
	$html.="<ul class='champs'>";
	foreach ($form->entrees as $nom => $value) {
		$html.="<li data-nom=\"$nom\" data-type=\"".$value['type']."\">".$value['html']."</li>";
	}
	$html.="</ul>";
	$html.="<br/>";
	$html.=$form->interrupteurs['valider']['html'];
	$js.=$form->initjs();
	foreach ($form->entrees as $key => $value) {
		$js.=$value['js'];
	}
	foreach ($form->interrupteurs as $key => $value) {
		$js.=$value['js'];
	}
	$js.="
$('#mcas$id button').button();
$('#mcas$id ul.champs>li label:nth-child(1)').after('".addslashes($h->bouton_suppr(0,'moins','supprimer','5px'))."');
$('#mcas$id').on('click',' ul.champs>li .moins', function(){
	var nom=$(this).parent().parent().dataset('nom');
	var type=$(this).parent().parent().dataset('type');
	if (type=='adresse') {
		$('#mcas$id ul.plus').prepend(\"$adresse\");
	}
	if (nom=='Fonction') {
		$('#mcas$id ul.plus').prepend(\"$fonction\");
	}
	$.post('ajax.php',{
			action:'edition/sup_champ_casquette',
			id:$id,
			nom:nom
		},function(data){
			if(data.succes==1) $('#ed_casquette-$id').html(data.html)
			eval(data.js);
			ed_scapi.reinitialise();
		},
		'json'
	);
});
";
	$prenom="";
	if($c->prenom_contact()!="") $prenom=$c->prenom_contact()." ";
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']="<b>$prenom".$c->nom_contact()."</b>, ".$c->nom();
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
