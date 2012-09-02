<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$succes=1;
	$id=$_POST['id_etablissement'];
	$e= new Etablissement($id);	
	$h=new html();
	$form=new formulaires;
	$form->prefixe="metab$id";
	
	$form->ajoute_entree('id', 'hidden', $id, '', array(1));
	$html="";
	$js="";
	$test=1;
	$adresse="";
	foreach($e->donnees() as $nom=>$donnee){
		if ($donnee[2]=='adresse') $test=0;
	}
	$adresse="<li class='champ' data-type='adresse'>Adresse</li>";
	$a="";
	if ($test==1) $a=$adresse;

	$html.="
<button class='plus'>Ajouter un champ</button>
<ul class='plus ui-widget-content'>
	$a
	<li class='champ' data-type='telephone'>Téléphone fixe</li>
	<li class='champ' data-type='telephone'>Téléphone portable</li>
	<li class='champ' data-type='email'>E-mail</li>
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
$('#metab$id .generique').click(function(){ $(this).next().slideToggle();});
$('#metab$id button.plus').button({ icons: {primary:'ui-icon-plusthick'}, text: false}).click(function(){ $(this).next().slideToggle();});
$('#metab$id ul.plus').click(function(event){
     event.stopPropagation();
});
$('#metab$id button.plus').click(function(event){
     event.stopPropagation();
});

$('#metab$id ul.plus').on('mouseenter','li',function(){ $(this).addClass('ui-state-hover');}).on('mouseleave','li',function(){ $(this).removeClass('ui-state-hover');});

$('#metab$id ul.plus').on('click','li.champ',function(){
	$('#metab$id ul.plus').slideToggle();
	var label=$(this).html();
	var type=$(this).dataset('type');
	$.post(
		'ajax.php',
		{action : 'edition/aj_champ_etablissement', id:$id, label:label, type:type },
		function(data){
			if (data.succes==1) {
				$('#metab$id ul.champs').append('<li data-nom=\"'+data.nom+'\" data-type=\"'+type+'\">' + data.html + '</li>');
				eval(data.js);
				infos(data.message);
			}
		},
		'json'
	);
});
$('#metab$id ul.plus div.perso').on('click','button',function(){
	var label=$(this).prev().prev().val();
	var type=$(this).prev().val();
	if (label!='') {
		$('#metab$id ul.plus').slideToggle();
		$.post(
			'ajax.php',
			{action : 'edition/aj_champ_etablissement', id:$id, label:label, type:type },
			function(data){
				if (data.succes==1) {
					$('#metab$id ul.champs').append('<li data-nom=\"'+data.nom+'\" data-type=\"'+type+'\">' + data.html + '</li>');
					eval(data.js);
					infos(data.message);
				}
			},
			'json'
		);
	}
});
	";
	foreach($e->donnees() as $nom=>$donnee){
		$label_d=$donnee[1];
		$type_d=$donnee[2];
		$valeur_d=$donnee[0];
			
		$form->ajoute_entree($nom, $type_d, $valeur_d, '',array(1),$label_d);
	}
	$form->ajoute_interrupteur('valider', 'bouton', 'Enregistrer', 'bouton', 1, 'edition/mod_etablissement');
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
$('#metab$id .bouton').button();
$('#metab$id ul.champs>li label:nth-child(1)').after('".addslashes($h->bouton_suppr(0,'moins','supprimer','5px'))."');
$('#metab$id ul.champs>li').on('click','.moins', function(){
	var nom=$(this).parent().parent().dataset('nom');
	var type=$(this).parent().parent().dataset('type');
	if (type=='adresse') {
		console.log('cool');
		$('#metab$id ul.plus').prepend(\"$adresse\");
	}
	$.post('ajax.php',{
			action:'edition/sup_champ_etablissement',
			id:$id,
			nom:nom
		},function(data){
			if(data.succes==1) $('#ed_etablissement-$id').html(data.html)
			eval(data.js);
			ed_ssapi.reinitialise();
		},
		'json'
	);
});
";
	
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']="";
		$reponse['titre']="<b>".$e->nom_structure()."</b>, ".$e->nom();
		$reponse['html']=$html;
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
