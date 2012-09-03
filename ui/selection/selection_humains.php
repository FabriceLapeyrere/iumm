<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$format=$_POST['format'];
	function affiche_combinaison($c){
		#cas simple: une selection
		if (!key_exists('c1',$c)){
			$N="";
			if ($c['N']==1) $N="Selection inversée.<br />";
			$html="$N<div class='recherche cadre'>".html::recherche($c['selection'])."</div>";
		}
		else {	
			if ($c['op']==1) $op="<img src='ui/css/images/et.png'><br />";
			else $op="<img src='ui/css/images/ou.png'><br />";
			$N="";
			if ($c['N']==1) $N="Selection inversée.<br />";
	
			$html="$N<div class='cadre'>".affiche_combinaison($c['c1'])."<div class='op'>$op</div>".affiche_combinaison($c['c2'])."</div>";
		}
		return $html;
	}
	if ( ! isset($_POST['no_reset_binf'])) $_SESSION['binfc']=0;
	$html="";
	$bouton_decombiner="
	<button class='decombiner ui-button ui-widget ui-state-default ui-button-icon-only ui-corner-all' role='button' aria-disabled='false' title='supprimer'>
		<span class='ui-button-icon-primary ui-icon ui-icon-minusthick'></span>
		<span class='ui-button-text'>combiner</span>
	</button>";
	$bouton_scombinaison="";
	if (count($_SESSION['combinaison'])>0){
		$html=affiche_combinaison($_SESSION['combinaison'])."<div class='op'>".html::bouton_et('intersection',$_SESSION['op'])."<br />".html::bouton_ou('réunion',$_SESSION['op'])."</div>";
		
		$bouton_scombinaison="<input id='sel_scombinaison' type='checkbox' />Uniquement ce filtre.";
	}
	$recherche=html::recherche($_SESSION['selection']);
	if ($recherche!="") $html.="
	<div class='recherche'><div class='cadre cadre1'>$recherche</div><div style='clear:both;'></div>$bouton_scombinaison</div>
	<div class='op'>
		<button class='combiner ui-button ui-widget ui-state-default ui-button-icon-only ui-corner-all' role='button' aria-disabled='false' title='combiner'>
			<span class='ui-button-icon-primary ui-icon ui-icon-plusthick'></span>
			<span class='ui-button-text'>combiner</span>
		</button><br />$bouton_decombiner
	</div>";
	elseif ($recherche=="" && count($_SESSION['combinaison'])==0) {
		$html.="<div class='recherche'><div class='cadre cadre1'>Aucun filtre.</div><div style='clear:both;'></div></div>";
	}
	else {
		$html.="<div class='recherche'><div class='cadre cadre1'>Aucun filtre.</div><div style='clear:both;'></div>$bouton_scombinaison</div><div class='op'>
	$bouton_decombiner</div>";
	}
	$js="";
	$js.="
	$('#sel_Ncats').attr('checked',".($_SESSION['selection']['Ncats']==1 ? 'true' : 'false').");
	$('#sel_Netabs').attr('checked',".($_SESSION['selection']['Netabs']==1 ? 'true' : 'false').");
	$('#sel_N').attr('checked',".($_SESSION['N']==1 ? 'true' : 'false').");
	$('#sel_scombinaison').attr('checked',".($_SESSION['scombinaison']==1 ? 'true' : 'false').");
	$('#sel_mots').val('".$_SESSION['selection']['mots']."');
	$('#sel_motifs').val('".$_SESSION['selection']['motifs']."');
	$('#sel_depts').val('".implode($_SESSION['selection']['depts'],', ')."');
	";
	if (isset($_POST['reload_cat'])) {
		$js.="
			var node=$('#sel_tree').dynatree('getTree').getNodeByKey('0');
			selectRec(node,false);
		";
		foreach($_SESSION['selection']['categories'] as $key){
		$js.="
			node=$('#sel_tree').dynatree('getTree').getNodeByKey($key);
			node.selectNoFire(true);
			while(node.data.key!='0'){
				node=node.parent;
				if (!node.isSelected()) $('#sel_dynatree-id-'+node.data.key+'>span').addClass('dynatree-partsel');
			}
		";
		}
	}
	$js.="
	$('#sel_structures .tab').removeClass('etab-sel');
	";
	foreach($_SESSION['selection']['etablissements'] as $key){
		$js.="
		$('#sel_structures .tab[data-id=$key]').addClass('etab-sel');
		";
	}

	if($_SESSION['selection']['email']==1 && $_SESSION['selection']['Nemail']==0) $js.="$('#sel_email').attr('checked',true);\n";
	else $js.="$('#sel_email').attr('checked',false);\n";
	if($_SESSION['selection']['email']==1 && $_SESSION['selection']['Nemail']==1) $js.="$('#sel_semail').attr('checked',true);\n";
	else $js.="$('#sel_semail').attr('checked',false);\n";
	if($_SESSION['selection']['adresse']==1 && $_SESSION['selection']['Nadresse']==0) $js.="$('#sel_adresse').attr('checked',true);\n";
	else $js.="$('#sel_adresse').attr('checked',false);\n";
	if($_SESSION['selection']['adresse']==1 && $_SESSION['selection']['Nadresse']==1) $js.="$('#sel_sadresse').attr('checked',true);\n";
	else $js.="$('#sel_sadresse').attr('checked',false);\n";
	if($_SESSION['selection']['cas']==1 && $_SESSION['selection']['Ncas']==0) $js.="$('#sel_cas').attr('checked',true);\n";
	else $js.="$('#sel_cas').attr('checked',false);\n";
	if($_SESSION['selection']['cas']==1 && $_SESSION['selection']['Ncas']==1) $js.="$('#sel_scas').attr('checked',true);\n";
	else $js.="$('#sel_scas').attr('checked',false);\n";

	$js.="
	$('#sel_casquettes .jspPane').html('".json_escape(Html::casquettes_selection())."');
	sel_casquettes();
	sel_ajuste();
	";
	$js.="
	$('#sel_filtres .pagination').html('".json_escape(Html::pagination($_SESSION['sel_binfc'],Casquettes::liste('nb')))."');
	$('#sel_casquettes .casquette .titre input').attr('checked',false);
	";
	foreach($_SESSION['selection']['casquettes'] as $key){
		$js.="
		$('#sel_casquettes input[data-id=$key]').attr('checked',true);
		";
	}
	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html."<div style='clear:both;'>";
			$reponse['js']=$js;
			break;
	}
?>
