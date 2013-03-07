<?php
/**
 *
 * @license	GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author	 Fabrice Lapeyrere <fabrice@surlefil.org>
 */

/**
*  Classe "html", contient tout l'arsenal pour générer le html. 
*/
class html
	{
	var $entrees=array();

	function __construct()
		{
		}

	function contact($id_contact)
		{
		if (Cache::obsolete('contact',$id_contact)) {
			$html="";
			$c=new Contact($id_contact);
			$prenom=$c->prenom();
			$nom=$c->nom();
			if ($prenom=='' && $nom=='' ) $nom_a="(sans nom)";
			else {
				$prenom_a="";
				if ($prenom!='') $prenom_a=$prenom." ";
				$nom_a=$prenom_a.$nom;
			}
			$html.="
	<div class='titre'>
		<span class='titre'>$nom_a</span> 
		".Html::bouton_suppr($id_contact,'bouton supprmain','supprimer','5px')." 
		<span class='barre ui-buttonset'>
			<button class='bouton mod ui-button ui-widget ui-state-default ui-button-icon-only ui-corner-left' role='button' aria-disabled='false' title='renommer'>
				<span class='ui-button-icon-primary ui-icon ui-icon-pencil'></span>
				<span class='ui-button-text'>renommer</span>
			</button>
			<button class='bouton aj ui-button ui-widget ui-state-default ui-button-icon-only ui-corner-right' role='button' aria-disabled='false' title='ajouter une casquette'>
				<span class='ui-button-icon-primary ui-icon ui-icon-plusthick'></span>
				<span class='ui-button-text'>ajouter une casquette</span>
			</button>
		</span>
	</div>";
			$casquettes=$c->casquettes();
			$tabs_head="<ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>";
			$tabs_content="";
			foreach ($casquettes as $id_casquette) {
				$cas=new Casquette($id_casquette);
				$nom_cas=$cas->nom();
				$tabs_head.="<li class='tab ui-state-default ui-corner-top' data-id='$id_casquette' data-idcas='$id_casquette' data-tab='#ed_casquette-$id_casquette'><a style='cursor:pointer;'>$nom_cas</a> ".Html::bouton_suppr($id_casquette,'bouton suppr','supprimer','2px')."</li>\n";
				$tabs_content.="<div id='ed_casquette-$id_casquette' class='casquette ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide' data-id='$id_casquette'>";		
				$tabs_content.=Html::casquette($id_casquette);
				$tabs_content.="</div>\n";			
			}
			$tabs_head.="</ul>\n";
			$html.="$tabs_head\n$tabs_content";
		Cache::put('contact',$id_contact,$html);
		} else $html=Cache::get('contact',$id_contact);
		return $html;
		
	}
	function contacts($binf=0,$motifs='') {
		$html="";
		$retour=array();
		$nb=0;
		$listes=Contacts::liste_rapide($motifs,$binf);
		$liste=$listes['liste'];
		foreach($liste as $id=>$contact) {
			$nb++;
			$html.="<div class='tabs contacts ui-tabs ui-widget ui-widget-content ui-corner-all' id='ed_contact-$id' data-id='$id'>\n";
			$html.=Html::contact($id);
			$html.="</div>";
		}
		if ($nb==0) $html.="Aucun résultat.";
		$retour['html']=$html;
		$retour['pagination']=Html::pagination($binf,$listes['nb']);
		return $retour;		
	}
	function pagination_contacts($binf=0,$motif='')
		{
		$c= new contacts($motif);
		$pagination="";
		#nombre de pages pour les raccourcis
		$p=2;
		#nombre de resultats par page
		$pas=20;
		$b=0;
		$fin=floor($c->nbcontacts/$pas)*$pas;
		$sui=min($binf+$pas,floor($fin/$pas)*$pas);
		$pre=max($binf-$pas,0);
		$pageCourante=floor($binf/$pas);
		$dernierePage=floor($fin/$pas);
		$actif="";
		if ($pageCourante==0) $actif=" on";
		$pagination.="
		<a class='first$actif ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left' data-binf='0' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&lt;&lt;</span>
		</a>
		";
		$pagination.="
		<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='0' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&lt;</span>
		</a>
		";
		if ($pageCourante<$p+1) {
			$b=0;
			while ($b<min($dernierePage*$pas,(2*$p+1)*$pas)){
				$actif="";
				if ($pageCourante==floor($b/$pas)) $actif=" on";
				$pagination.="
				<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$b' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".(floor($b/$pas)+1)."</span>
				</a>
				";
				$b+=$pas;
			}
		}
		else if ($pageCourante>$dernierePage-2*$p-1){
			$b=max(0,$dernierePage-2*$p)*$pas;
			while ($b<=$dernierePage*$pas){
				$actif="";
				if ($pageCourante==floor($b/$pas)) $actif=" on";
				$pagination.="
				<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$b' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".(floor($b/$pas)+1)."</span>
				</a>
				";
				$b+=$pas;
			}
		}
		else {
			$b=max(0,$pageCourante-$p)*$pas;
			while ($b<min($dernierePage,$pageCourante+$p+1)*$pas){
				$actif="";
				if ($pageCourante==floor($b/$pas)) $actif=" on";
				$pagination.="
				<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$b' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".(floor($b/$pas)+1)."</span>
				</a>
				";
				$b+=$pas;
			}
		}
		$pagination.="
		<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$sui' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&gt;</span>
		</a>
		";
		$actif="";
		if ($pageCourante==$dernierePage) $actif=" on";
		$pagination.="
		<a class='last$actif ui-button ui-widget ui-state-default ui-button-text-only ui-corner-right' data-binf='$fin' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&gt;&gt;</span>
		</a>
		";
		return $pagination;
		
	}
	function filtre_contacts($motif="")
		{
		$motif=addslashes($motif);
		$html="
		<input type='text' name='motif' value='$motif'>
		<button class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='rechercher'>
			<span class='ui-button-icon-primary ui-icon ui-icon-search'></span>
			<span class='ui-button-text'>rechercher</span>
		</button>
		";
		return $html;
		
	}
	function filtre_email($motifs="")
		{
		$motif=addslashes($motifs);
		$html="
		<input type='text' name='motif' value='$motifs'>
		<button class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='rechercher'>
			<span class='ui-button-icon-primary ui-icon ui-icon-search'></span>
			<span class='ui-button-text'>rechercher</span>
		</button>
		";
		return $html;
		
	}
	function filtre_news($motifs="")
		{
		$motif=addslashes($motifs);
		$html="
		<input type='text' name='motif' value='$motifs'>
		<button class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='rechercher'>
			<span class='ui-button-icon-primary ui-icon ui-icon-search'></span>
			<span class='ui-button-text'>rechercher</span>
		</button>
		";
		return $html;
		
	}
	function filtre_utilisateur($motifs="")
		{
		$motif=addslashes($motifs);
		$html="
		<input type='text' name='motif' value='$motifs'>
		<button class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='rechercher'>
			<span class='ui-button-icon-primary ui-icon ui-icon-search'></span>
			<span class='ui-button-text'>rechercher</span>
		</button>
		";
		return $html;
		
	}
	function filtre_expediteur($motifs="")
		{
		$motif=addslashes($motifs);
		$html="
		<input type='text' name='motif' value='$motifs'>
		<button class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='rechercher'>
			<span class='ui-button-icon-primary ui-icon ui-icon-search'></span>
			<span class='ui-button-text'>rechercher</span>
		</button>
		";
		return $html;
		
	}
	function pagination_structures($binf=0,$motif='')
		{
		$c= new structures($motif);
		$pagination="";
		#nombre de pages pour les raccourcis
		$p=2;
		#nombre de resultats par page
		$pas=20;
		$b=0;
		$fin=floor($c->nbstructures/$pas)*$pas;
		$sui=min($binf+$pas,floor($fin/$pas)*$pas);
		$pre=max($binf-$pas,0);
		$pageCourante=floor($binf/$pas);
		$dernierePage=floor($fin/$pas);
		$actif="";
		if ($pageCourante==0) $actif=" on";
		$pagination.="
		<a class='first$actif ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left' data-binf='0' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&lt;&lt;</span>
		</a>
		";
		$pagination.="
		<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='0' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&lt;</span>
		</a>
		";
		if ($pageCourante<$p+1) {
			$b=0;
			while ($b<min($dernierePage*$pas,(2*$p+1)*$pas)){
				$actif="";
				if ($pageCourante==floor($b/$pas)) $actif=" on";
				$pagination.="
				<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$b' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".(floor($b/$pas)+1)."</span>
				</a>
				";
				$b+=$pas;
			}
		}
		else if ($pageCourante>$dernierePage-2*$p-1){
			$b=max(0,$dernierePage-2*$p)*$pas;
			while ($b<=$dernierePage*$pas){
				$actif="";
				if ($pageCourante==floor($b/$pas)) $actif=" on";
				$pagination.="
				<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$b' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".(floor($b/$pas)+1)."</span>
				</a>
				";
				$b+=$pas;
			}
		}
		else {
			$b=max(0,$pageCourante-$p)*$pas;
			while ($b<min($dernierePage,$pageCourante+$p+1)*$pas){
				$actif="";
				if ($pageCourante==floor($b/$pas)) $actif=" on";
				$pagination.="
				<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$b' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".(floor($b/$pas)+1)."</span>
				</a>\n
				";
				$b+=$pas;
			}
		}
		$pagination.="
		<a class='int$actif on ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$sui' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&gt;</span>
		</a>
		";
		$actif="";
		if ($pageCourante==$dernierePage) $actif=" on";
		$pagination.="
		<a class='last$actif ui-button ui-widget ui-state-default ui-button-text-only ui-corner-right' data-binf='$fin' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&gt;&gt;</span>
		</a>
		";
		return $pagination;
		
	}
	 	function pagination($binf=0,$nb)
		{
		$pagination="";
		#nombre de pages pour les raccourcis
		$p=2;
		#nombre de resultats par page
		$pas=20;
		$b=0;
		$fin=floor($nb/$pas)*$pas;
		$sui=min($binf+$pas,$fin);
		$bsup=min($binf+$pas,$nb);
		$pre=max($binf-$pas,0);
		$pageCourante=floor($binf/$pas);
		$dernierePage=floor($nb/$pas);
		$binfs=array();
		for ($i=0;$i<$nb;$i+=$pas){
			$binfs[]=$i;
		}
		$actif="";
		if ($pageCourante==0) $actif=" on";
		$pagination.="
		<a class='first$actif ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left' data-binf='0' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&lt;&lt;</span>
		</a>
		";
		if ($pageCourante<=$p) {
			for($i=0;$i<=min(count($binfs)-1,2*$p);$i++){
				$actif="";
				if ($pageCourante==$i) $actif=" on";
				$pagination.="<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='".$binfs[$i]."' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".($binfs[$i]+1)."</span>
				</a>
				";
			}
		}
		else if ($pageCourante>=$dernierePage-$p){
			for($i=max(count($binfs)-1-2*$p,0);$i<=count($binfs)-1;$i++){
				$actif="";
				if ($pageCourante==$i) $actif=" on";
				$pagination.="<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='".$binfs[$i]."' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".($binfs[$i]+1)."</span>
				</a>
				";
			}
		}
		else {
			for($i=$pageCourante-$p;$i<=$pageCourante+$p;$i++){
				$actif="";
				if ($pageCourante==$i) $actif=" on";
				$pagination.="<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='".$binfs[$i]."' role='button' aria-disabled='false'>
					<span class='ui-button-text'>".($binfs[$i]+1)."</span>
				</a>
				";
			}
		}
		$actif="";
		if ($pageCourante==$dernierePage) $actif=" on";
		$pagination.="
		<a class='int$actif ui-button ui-widget ui-state-default ui-button-text-only' data-binf='$fin' role='button' aria-disabled='false'>
			<span class='ui-button-text'>&gt;&gt;</span>
		</a>
		<a class='last ui-button ui-widget ui-state-default ui-button-text-only ui-corner-right' role='button' aria-disabled='false'>
			<span class='ui-button-text'>".($binf+1)." - $bsup / $nb</span>
		</a>
		";
		return $pagination;
		
	}
	function filtre_structures($motif="")
		{
		$motif=addslashes($motif);
		$html="
		<input type='text' name='motif' value='$motif'>
		<button class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='rechercher'>
			<span class='ui-button-icon-primary ui-icon ui-icon-search'></span>
			<span class='ui-button-text'>rechercher</span>
		</button>
		";
		return $html;
		
	}
	function filtre_envoi($motif="")
		{
		$motif=addslashes($motif);
		$html="
		<input type='text' name='motif' value='$motif'>
		<button class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='rechercher'>
			<span class='ui-button-icon-primary ui-icon ui-icon-search'></span>
			<span class='ui-button-text'>rechercher</span>
		</button>
		";
		return $html;
		
	}

	function filtre_support($motif="")
		{
		$motif=addslashes($motif);
		$html="
		<input type='text' name='motif' value='$motif'>
		<button class='ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='rechercher'>
			<span class='ui-button-icon-primary ui-icon ui-icon-search'></span>
			<span class='ui-button-text'>rechercher</span>
		</button>
		";
		return $html;
		
	}

	function structure($id_structure)
		{
		$html="";
		if (Cache::obsolete('structure',$id_structure)) {
			$s=new Structure($id_structure);
			$nom_structure=$s->nom();
			if ($nom_structure=="") $nom_structure="(sans nom)";
			$html.="<div class='titre'><span class='titre'>$nom_structure</span> ".Html::bouton_suppr($id_structure,'bouton supprmain','supprimer','5px')." 
	<span class='barre ui-buttonset'>
	<button class='bouton mod ui-button ui-widget ui-state-default ui-button-icon-only ui-corner-left' role='button' aria-disabled='false' title='renommer'>
		<span class='ui-button-icon-primary ui-icon ui-icon-pencil'></span>
		<span class='ui-button-text'>renommer</span>
	</button>
	<button class='bouton aj ui-button ui-widget ui-state-default ui-button-icon-only ui-corner-right' role='button' aria-disabled='false' title='ajouter un établissement'>
		<span class='ui-button-icon-primary ui-icon ui-icon-plusthick'></span>
		<span class='ui-button-text'>ajouter une casquette</span>
	</button>
	</span>
	</div>\n";
			$tabs_head="<ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>";
			$tabs_content="";
			foreach ($s->etablissements() as $id_etablissement) {
				$e=new Etablissement($id_etablissement);
				$nom_eta=$e->nom();
				$idcas=$e->casquette_propre();
				$tabs_head.="<li class='tab ui-state-default ui-corner-top' data-id='$id_etablissement' data-idcas='$idcas' data-tab='#ed_etablissement-$id_etablissement'><a style='cursor:pointer;'>$nom_eta</a>".Html::bouton_suppr($id_etablissement,'bouton suppr','supprimer','2px')."</li>\n";
				$tabs_content.="<div id='ed_etablissement-$id_etablissement' class='etablissement ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide' data-id='$id_etablissement' data-idcas='$idcas'>";		
				$tabs_content.=Html::etablissement($id_etablissement);			
				$tabs_content.="</div>\n";			
			}
			$tabs_head.="</ul>\n";
			$html.="$tabs_head\n$tabs_content";
			Cache::put('structure',$id_structure,$html);
		} else $html=Cache::get('structure',$id_structure);
		return $html;
		
	}
	function structure_selection($id)
		{
		$html="";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_structure="select rowid, nom, date from structures where rowid=$id";
		if ($res_structure = $base->query($sql_structure)){
			while ($tab_structure=$res_structure->fetchArray(SQLITE3_ASSOC)) {
				$rowid_structure=$tab_structure['rowid'];
				$nom_structure=$tab_structure['nom'];
				$html.="<div class='titre'><span class='titre'>$nom_structure</span></div>\n";
				$sql_eta="select t1.rowid, t1.nom from etablissements as t1 inner join ass_etablissement_structure as t2 on t1.rowid=t2.id_etablissement where t2.id_structure=$rowid_structure";
				$res_eta = $base->query($sql_eta);
				$tabs_head="<ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>";
				$tabs_content="";
				while ($tab_eta=$res_eta->fetchArray(SQLITE3_ASSOC)) {
					$rowid_eta=$tab_eta['rowid'];
					$nom_eta=$tab_eta['nom'];
					$class='';
					if (in_array($rowid_eta,$_SESSION['selection']['etablissements'])) $class='etab-sel';
					$tabs_head.="<li class='tab ui-state-default ui-corner-top $class' data-id='$rowid_eta'><a>$nom_eta</a></li>\n";		
				}
				$tabs_head.="</ul>\n";
				$html.="$tabs_head";
			}
		}
		$base->close();
		return $html;
		
	}
	function structures($binf=0,$motifs='')
		{
		$html="";
		$retour=array();
		$nb=0;
		$listes=Structures::liste_rapide($motifs,$binf);
		$liste=$listes['liste'];
		foreach($liste as $id=>$structure) {
			$nb++;
			$html.="<div class='tabs structures ui-tabs ui-widget ui-widget-content ui-corner-all' id='ed_structure-$id' data-id='$id'>";
			$html.=Html::structure($id);
			$html.="</div>";
		}
		if ($nb==0) $html.="Aucun résultat.";
		$retour['html']=$html;
		$retour['pagination']=Html::pagination($_SESSION['structures']['binf'],$listes['nb']);
		return $retour;		
	}
	function structures_selection($binf=0,$motifs='')
		{
		$html="";
		$listes=Structures::liste_rapide($motifs,$binf);
		$liste=$listes['liste'];
		$nb=0;
		foreach($liste as $id=>$structure) {
				$nb++;
				$html.="<div class='structure ui-tabs ui-widget ui-widget-content ui-corner-all' id='structure-$id' data-id='$id'>";
				$html.=Html::structure_selection($id);
				$html.="</div>";
		}
		if ($nb==0) $html.="Aucun résultat.";
		$retour['html']=$html;
		$retour['pagination']=Html::pagination($_SESSION['sel_structures']['binf'],$listes['nb']);
		return $retour;
		
	}


	function casquettes_selection()
		{
		$html="";
		$liste=Casquettes::liste();
		if(count($liste)==0){
			$html.="Aucun résultat";
		} else {
			foreach($liste as $id=>$casquette){
				$html.="<div id='sel_casquette-$id' class='casquette ui-widget ui-widget-content ui-corner-all' data-id='$id'>".Html::casquette_selection($id)."</div>\n";
			}
		}
		return $html;
	}
	function casquette_sel($id_casquette) {
		return Html::casquette_selection($id_casquette);
	}
	function casquette_sel_propre($id_casquette) {
		$retour=Html::casquette_selection($id_casquette);
		$pattern = "/<input data-id='(\d+)' data-idetab='(\d+)' data-idstr='(\d+)' type='checkbox'\/>/";
		$replacement = '';
		$retour= preg_replace($pattern, $replacement, $retour);
		$pattern = "/<input data-id='(\d+)' data-idcont='(\d+)' type='checkbox'\/>/";
		$replacement = '';
		$retour= preg_replace($pattern, $replacement, $retour);
	
		$pattern = "/<span style='font-size:8px;position:relative;bottom:5px;'><button data-id='(\d+)' class='moins ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' style='border:none;background:none;' role='button' aria-disabled='false' title='supprimer'><span class='ui-button-icon-primary ui-icon ui-icon-close'><\/span><span class='ui-button-text'>supprimer<\/span><\/button><\/span>/";
		$replacement = '';
		$retour= preg_replace($pattern, $replacement, $retour);
		return $retour;
	}
	function casquette_selection($id_casquette)
		{
		$html="";
		if (Cache::obsolete('casquette_sel',$id_casquette)) {
			$c= new Casquette($id_casquette);
			$ctout=$c->tout();
			$nom_casquette=$ctout['nom'];
			$id_etablissement=$ctout['etablissement']['id'];
			$nom_etablissement=$ctout['etablissement']['nom'];
			$id_structure=$ctout['structure']['id'];
			$nom_structure=$ctout['structure']['nom'];
			$id_contact=$ctout['contact']['id'];
			$prenom_contact=$ctout['contact']['prenom'];
			$nom_contact=$ctout['contact']['nom'];
			if ($nom_structure=='' ) $nom_structure="(sans nom)";
			if ($prenom_contact=='' && $nom_contact=='' ) $nom="(sans nom)";
			else {
				$prenom="";
				if ($prenom_contact!='') $prenom=$prenom_contact." ";
				$nom=$prenom.$nom_contact;
			}
			if ($nom_contact!='$$$$')
				$html="<div class='titre'><div class='css-casquette nomstr ui-widget-header ui-corner-all'><input data-id='$id_casquette' data-idcont='$id_contact' type='checkbox'/> $nom, <span class='maj blanc'>$nom_casquette</span></div></div><div class='cas'>".Html::casquette($id_casquette)."</div>";
			else {
				if ($id_etablissement>0) {
				$html="<div class='titre'><div class='css-structure nomstr ui-widget-header ui-corner-all'><input data-id='$id_casquette' data-idetab='$id_etablissement' data-idstr='$id_structure' type='checkbox'/> $nom_structure, <span class='maj blanc'>$nom_etablissement</span></div></div><div class='cas'>".Html::etablissement($id_etablissement,false)."</div>";
				}
			}
			Cache::put('casquette_sel',$id_casquette,$html);
		} else $html=Cache::get('casquette_sel',$id_casquette);
		return $html;
	}
	function casquette($id_casquette)
		{
		$html="";
		if (Cache::obsolete('casquette',$id_casquette)) {
			$c= new Casquette($id_casquette);
			$ctout=$c->tout();
			$tel="";
			$email="";
			$adresse="";
			$fonction="";
			$divers="";
			$donnees=$ctout['donnees'];
			if(is_array($donnees)){
				foreach($donnees as $nom=>$donnee){
					$nom_d=$nom;
					$label_d=$donnee[1];
					$type_d=$donnee[2];
					$date_d=date('d/m/Y',strtotime($donnee[3]));
					switch ($type_d){
						case 'telephone':
							if ($donnee[0]!="") {
								if ($tel==""){
									$tel="<div class='cas-tel-$id_casquette'><ul>";
						
								}
								$valeur_d=$donnee[0];
								$tel.="<li style='margin-top:10px;'  id='ed_donneeCas-$nom_d'><span class='label'>$label_d : <span class='maj'>($date_d)</span></span><br /><span class='valeur'>".str_replace(" ","&nbsp;",$valeur_d)."</span></li>\n";
							}
							break;
						case 'email':
							if ($donnee[0]!="") {
								if ($email==""){
									$email="<div class='cas-email-$id_casquette'><ul>";
						
								}
								$valeur_d=$donnee[0];
								$email.="<li style='margin-top:10px;'  class='ed_donneeCas-$nom_d'><span class='label'>$label_d : <span class='maj'>($date_d)</span></span><br /><span class='valeur'><a href='mailto:$valeur_d'>$valeur_d</a></span></li>\n";
							}
							break;
						case 'adresse':
							$valeur_d="";
							$tab=json_decode($donnee[0]);
							foreach ($tab as $cle=>$valeur){
								if ($valeur_d!="" && $valeur!="") $valeur_d.="<br />";
								if ($valeur!="") $valeur_d.=nl2br($valeur);
							}
							if ($valeur_d!="") {
								$adresse="<div style='margin-top:10px;' class='cas-adresse-$id_casquette'><ul>";
								$adresse.="<li class='donneeCas-$nom_d'><span class='label'>$label_d : <span class='maj'>($date_d)</span></span><br /><span class='valeur'>$valeur_d</span></li>\n";
					
							}
							break;
						default:
							if ($nom=="Fonction" && $donnee[0]!="") {
								if ($fonction==""){
									$fonction="<div class='cas-email-$id_casquette'><ul>";
						
								}
								$fonction.="<li class='donneeCas-$nom_d'><span class='label'>$label_d :</span> <span class='maj'>($date_d)</span><br /><span class='valeur'>".nl2br($donnee[0])."</span></li>";
							}
							elseif ($donnee[0]!="") {
								if ($divers==""){
									$divers="<div class='cas-div-$id_casquette'><h3>Divers</h3><ul>";
					
								}
							$divers.="<li class='donneeCas-$nom_d'><span class='label'>$label_d :</span> <span class='maj'>($date_d)</span><br /><span class='valeur'>".nl2br($donnee[0])."</span></li>";
							}
					}
				}
			}
			if ($fonction!="") $html.="$fonction</ul></div>";
			if ($tel!="") $html.="$tel</ul></div>";
			if ($email!="") $html.="$email</ul></div>";
			if ($adresse!="") $html.="$adresse</ul></div>";
			if ($divers!="") $html.="$divers</ul></div>";
			$rowid_etab=$ctout['etablissement']['id'];
			if ($rowid_etab>0) {
				$structure=$ctout['structure']['nom'];
				$nom_etab=$ctout['etablissement']['nom'];
				$html.="<div class='cas-etablissement-$id_casquette'><h3>Structure</h3><div class='etabContact$rowid_etab'><span class='etabcas' data-id='$rowid_etab'><span class='titre'><b>$structure</b>, $nom_etab</span></span> ".Html::bouton_suppr($id_casquette,'moins','supprimer')."</div><div class='cadre etabContact$rowid_etab'>\n";
				$html_etab=str_replace(Html::bouton_suppr($rowid_etab,'moins','supprimer'),"",Html::etablissement($rowid_etab,false));
				$e=new Etablissement($rowid_etab);
				foreach($e->casquettes() as $id_cas) $html_etab=str_replace(Html::bouton_suppr($id_cas,'moins','supprimer'),"",$html_etab);
				$html.=$html_etab;
				$html.="</div></div>\n";
			}
			$cat=Html::cas_categories($ctout['categories']);
			if ($cat!="") $html.="<div class='cas-categories-$id_casquette'><h3>Listes</h3><div>$cat</div></div>";
			Cache::put('casquette',$id_casquette,$html);
		} else $html=Cache::get('casquette',$id_casquette);
		return $html;
	}
	
	function cas_categories($cats)
		{
		$cat="";
		if (is_array($cats) && isset($cats[0]) && $cats[0]!=""){
			if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." CATEGORIES \n----\n".var_export($cats,true)."\n----\n", 3, "tmp/fab.log");
			foreach($cats as $id_categorie) {
				$cc=new Categorie($id_categorie);
				$nom_cat=$cc->nom();
				$cat.="<span style='margin:2px;' class='dynatree-node' data-id='$id_categorie'><a class='dynatree-title' href='#'>$nom_cat</a></span>";
			}
		}
		return $cat;	
	}
	
	function etablissement($id_etablissement,$contacts=true)
		{
		$html="";
		if (Cache::obsolete('etablissement',$id_etablissement)) {
			$e= new Etablissement($id_etablissement);
			$etout=$e->tout();
			$html="";
			$tel="";
			$email="";
			$adresse="";
			$divers="";
			if(is_array($etout['donnees'])){
				foreach($etout['donnees'] as $nom=>$donnee){
					$nom_d=$nom;
					$label_d=$donnee[1];
					$type_d=$donnee[2];
					$date_d=date('d/m/Y',strtotime($donnee[3]));
					switch ($type_d){
						case 'telephone':
							if ($donnee[0]!="") {
								if ($tel==""){
									$tel="<div style='margin-top:10px;' id='etab-tel-$id_etablissement'><ul>";
						
								}
								$valeur_d=$donnee[0];
								$tel.="<li id='ed_donneeEtab-$nom_d'><span class='label'>$label_d : <span class='maj'>($date_d)</span></span><br /><span class='valeur'>$valeur_d</span></li>\n";
							}
							break;
						case 'email':
							if ($donnee[0]!="") {
								if ($email==""){
									$email="<div style='margin-top:10px;' id='etab-email-$id_etablissement'><ul>";
						
								}
								$valeur_d=$donnee[0];
								$email.="<li id='ed_donneeEtab-$nom_d'><span class='label'>$label_d : <span class='maj'>($date_d)</span></span><br /><span class='valeur'><a href='mailto:$valeur_d'>$valeur_d</a></span></li>\n";
							}
							break;
						case 'adresse':
							if ($donnee[0]!=""){
								$valeur_d="";
								$tab=json_decode($donnee[0]);
								foreach ($tab as $cle=>$valeur){
									if ($valeur_d!="" && $valeur!="") $valeur_d.="<br />";
									if ($valeur!="") $valeur_d.=nl2br($valeur);
								}
								if ($valeur_d!="") {
									$adresse="<div style='margin-top:10px;' id='etab-adresse-$id_etablissement'><ul>";
									$adresse.="<li id='ed_donneeEtab-$nom_d'><span class='label'>$label_d : <span class='maj'>($date_d)</span></span><br /><span class='valeur'>$valeur_d</span></li>\n";
					
								}
							}
							break;
						default:
							if ($divers=="" && $donnee[0]!=""){
								$divers="<div style='margin-top:10px;' id='etab-tel-$id_etablissement'><h3>Divers</h3><ul>";
					
							}
							if ($donnee[0]!="") $divers.="<li id='ed_donneeEtab-$nom_d'><span class='label'>$label_d :</span> <span class='maj'>($date_d)</span><br /><span class='valeur'>".nl2br($donnee[0])."</span></li>";	
					}
				}
			}
			if ($tel!="") $html.="$tel</ul></div>";
			if ($email!="") $html.="$email</ul></div>";
			if ($adresse!="") $html.="$adresse</ul></div>";
			if ($divers!="") $html.="$divers</ul></div>";
			if(count($etout['casquettes'])>0){
				if (is_array($etout['casquettes'])){
					$html.="<div id='ed_cas-etablissement-$id_etablissement'><h3>Contacts</h3><ul class='contactsEtab' id='ed_contactsEtab-$id_etablissement'>";
					foreach($etout['casquettes'] as $id){
						$cas=new Casquette($id);
						$castout=$cas->tout();
						$fonction=$castout['fonction'];
						$pn=$castout['contact']['prenom'];
						$n=$castout['contact']['nom'];
						if ($fonction!="") $fonction=", $fonction";
						if ($pn=='' && $n=='' ) $nom_c="(sans nom)";
						else {
							$prenom="";
							if ($pn!='') $prenom=$pn." ";
							$nom_c=$prenom.$n;
						}
						$html.="<li><a class='etabCas$id' data-id='$id' style='text-decoration:none;'><b>$nom_c</b>$fonction</a>".Html::bouton_suppr($id,'moins','supprimer')."</li>";
					}
					$html.="</ul></div>";
				}
			}
			$id_casquette=$etout['casquette_propre'];
			if ($id_casquette!=0){
				$cas=new Casquette($id_casquette);
				$cat=Html::cas_categories($cas->categories());
				if ($cat!="") $html.="<div class='cas-categories-$id_casquette'><h3>Listes</h3><div>$cat</div></div>";
			}
			Cache::put('etablissement',$id_etablissement,$html);
		} else $html=Cache::get('etablissement',$id_etablissement);
		return $html;
	}
	function categories($idparent)
		{
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_n="select count(*) from categories where idparent=$idparent";
		if ($res_n = $base->query($sql_n)){
			while ($tab_n=$res_n->fetchArray(SQLITE3_ASSOC)) {
				$n=$tab_n['count(*)'];
			}
		}
		$html="";
		if ($n>0){
		$sql_enfants="select rowid, nom, idparent from categories where idparent=$idparent";
		$html.="<ul>";
		if ($res_enfants = $base->query($sql_enfants)){
			while ($tab_enfants=$res_enfants->fetchArray(SQLITE3_ASSOC)) {
				$html.="<li data=\"{icon:false, addClass:'categorie', key :".$tab_enfants['rowid']." }\"><span>".$tab_enfants['nom']." <span class='nbincat'><img src='ui/css/images/loading.gif'/></span></span>";
				$html.=Html::categories($tab_enfants['rowid']);
				$html.="</li>";
			}
		}
		$html.="</ul>";
		}
		return $html;
	}
	function titre_categorie($id)
		{
		$c=new Categorie($id);
		$html="<span>".$c->nom()." <span class='nbincat'>(".$c->nbincat().", ".$c->total().")</span></span>";
		return $html;
	}

	function nbincat($id_categorie)
		{
		$html="";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql_cat="SELECT count(*) FROM ass_casquette_categorie WHERE id_categorie=$id_categorie AND statut=1 AND date IN (select max(date) from ass_casquette_categorie group by id_casquette,id_categorie having id_categorie=$id_categorie)";
		$res_cat = $base->query($sql_cat);
		while ($tab_cat=$res_cat->fetchArray(SQLITE3_ASSOC)) {
			$html.=$tab_cat['count(*)'];
		}
		return $html;
	}
	function bouton_suppr($id,$class,$titre,$bottom='5px',$icon='close')
		{
	$html="<span style='font-size:8px;position:relative;bottom:$bottom;'><button data-id='$id' class='$class ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' style='border:none;background:none;' role='button' aria-disabled='false' title='$titre'><span class='ui-button-icon-primary ui-icon ui-icon-$icon'></span><span class='ui-button-text'>$titre</span></button></span>";
	return $html;
	}

	function bouton_et($titre='et',$op=1)
		{
	$class="";
	if ($op==1) $class=" ui-state-active";
	$html="<button class='et ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only$class' role='button' aria-disabled='false' title='$titre'><span class='ui-button-icon-primary ui-icon ui-et'></span><span class='ui-button-text'>$titre</span></button>";
	return $html;
	}
	function bouton_ou($titre='ou',$op=0)
		{
	$class="";
	if ($op==0) $class=" ui-state-active";
	$html="<button class='ou ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only$class' role='button' aria-disabled='false' title='$titre'><span class='ui-button-icon-primary ui-icon ui-ou'></span><span class='ui-button-text'>$titre</span></button>";
	return $html;
	}
	function bouton_non($titre='non',$N=0)
		{
	$class="";
	if ($N==1) $class=" ui-state-active";
	$html="<button class='non ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only$class' role='button' aria-disabled='false' title='$titre'><span class='ui-button-icon-primary ui-icon ui-non'></span><span class='ui-button-text'>$titre</span></button>";
	return $html;
	}

	function recherche($selection)
	{
		global $departements;
		foreach ($selection as $key => $value) { $$key = $value; }
		#casquettes
		$liste_cass=array();
		$html_cass="";
		if(count($casquettes)>0){
			foreach($casquettes as $id_casquette){
				$c=new Casquette($id_casquette);
				if ($c->prenom_contact()=='' && $c->nom_contact()=='' ) $nom="(sans nom)";
				else {
					$prenom="";
					if ($c->prenom_contact()!='') $prenom=$c->prenom_contact()." ";
					$nom=$prenom.$c->nom_contact().", ".$c->nom();
				}
				if ($c->casquette_etab()==1) $nom=$c->nom_structure().", ".$c->nom_etablissement();
				$liste_cass[]="<b>$nom</b>";
			}
			$html_cass="";
			foreach($liste_cass as $casquette){
				if ($html_cass=="") $html_cass.=$casquette;
				else $html_cass.=", ".$casquette;
			}
			if($cas==1 && $Ncas==0) $html_cass="<b>Est parmi les contacts suivants : </b><br /><span>$html_cass</span><br />";
			else if($cas==1 && $Ncas==1) $html_cass="<b>N'est pas parmi les contacts suivants :</b><br /><span>$html_cass</span><br />";
			else $html_cass="";
		}
		
		#etablissements
		$html_Netabs="Appartient aux structures :";
		if ($Netabs==1) $html_Netabs="N'appartient pas aux structures :";
		
		$liste_etabs=array();
		foreach($etablissements as $id_etablissement){
			$e=new Etablissement($id_etablissement);
			$liste_etabs[]="<b>".$e->nom_structure()."</b> ".$e->nom();
		}
		sort($liste_etabs);
		$html_etabs="";
		foreach($liste_etabs as $etablissement){
			if ($html_etabs=="") $html_etabs.=$etablissement;
			else $html_etabs.=", ".$etablissement;
		}
		if ($html_etabs!="") $html_etabs="<b>$html_Netabs</b><br /><span>$html_etabs</span><br />";
		
		#catégories
		$html_Ncats="Appartient aux listes :";
		if ($Ncats==1) $html_Ncats="N'appartient pas aux listes :";
		
		$liste_cats=array();
		foreach($categories as $id_categorie){
			$c=new Categorie($id_categorie);
			$liste_cats[]=$c->nom();
		}
		sort($liste_cats);
		$html_cats="";
		foreach($liste_cats as $categorie){
			if ($html_cats=="") $html_cats.=$categorie;
			else $html_cats.=", ".$categorie;
		}
		if ($html_cats!="") $html_cats="<b>$html_Ncats</b><br /><span>$html_cats</span><br />";
		
		#motifs
		$html_motifs="";
		if (noaccent($motifs)!="") $html_motifs="<b>Contient les mots :</b><br /> ".noaccent($motifs)." <br />";
	
		#mots
		$html_mots="";
		if (noaccent($mots)!="") $html_mots="<b>Contient les mots entiers :</b><br /> ".noaccent($mots)." <br />";
	
		#départements
		$html_depts="";
		$noms_depts=array();
		if (count($depts)>0 && $depts[0]!='') {
			foreach($depts as $dept){
				$noms_depts[]=$departements[$dept]['nom'];
			}
			$html_depts="<b>Dans les départements suivants :</b><br />".implode($noms_depts,', ')."<br />";
		}
	
		#email
		$html_email="";
		if($email==1 && $Nemail==0) $html_email="Avec e-mail.";
		if($email==1 && $Nemail==1) $html_email="Sans e-mail.";
	
		#adresse
		$html_adresse="";
		if($adresse==1 && $Nadresse==0) $html_adresse="Avec adresse postale.";
		if($adresse==1 && $Nadresse==1) $html_adresse="Sans adresse postale.";

		$html=$html_cass.$html_cats.$html_etabs.$html_motifs.$html_mots.$html_depts.$html_email.$html_adresse;
		return $html;
	}
	function entetes_email()
		{
		$binf=$_SESSION['email']['binf'];
		$motifs=$_SESSION['email']['motifs'];
		$entetes=Emails::liste($binf,$motifs);
		$html="";
		if(count($entetes)==0){
			$html.="Aucun résultat";
		} else {
			foreach($entetes as $id=>$entete){
				$html.="<div class='mail-entete ui-widget-header ui-corner-all' data-id='$id'>".$entete['sujet']." <span class='maj'>(".$entete['date'].")</span></div>";
			}
		}
		return $html;
	}
	function entetes_news()
		{
		$binf=$_SESSION['news']['binf'];
		$motifs=$_SESSION['news']['motifs'];
		$entetes=Newsletters::liste($binf,$motifs);
		$html="";
		if(count($entetes)==0){
			$html.="Aucun résultat";
		} else {
			foreach($entetes as $id=>$entete){
				$html.="<div class='news-entete ui-widget-header ui-corner-all' data-id='$id'>".$entete['sujet']." <span class='maj'>(".$entete['date'].")</span></div>";
			}
		}
		return $html;
	}
	function envois()
		{
		$binf=$_SESSION['emailing']['binf'];
		$motifs=$_SESSION['emailing']['motifs'];
		$envois=Emailing::envois($binf,$motifs);
		$html="";
		if(count($envois)==0){
			$html.="Aucun résultat";
		} else {
			foreach($envois as $id=>$envoi){
				$html.="<div class='emailing-envoi ui-widget-header ui-corner-all' data-id='$id'>".$envoi['sujet']." <span class='maj'>(".$envoi['date'].")</span></div>";
			}
		}
		return $html;
	}
	function envoi($id)
		{
		$html="";
		if ($id==0) {
			$html.="Aucun envoi.";
		} else {
			$envoi=Emailing::envoi($id);
			$nb=Emailing::nb_messages_boite_envoi($id);
			$nb_erreurs=Emailing::nb_erreurs_envoi($id);
			$statut=$envoi['statut'];
			$json='{"nom":"","email":""}';
			if (isset($envoi['expediteur'])) $json=$envoi['expediteur'];
			$expediteur=json_decode($json);
			$html="";
			$html.="<div class='titre' data-id='$id'>".$envoi['sujet']."</div><div class='meta'>Envoi commencé le ".$envoi['date']."<br/>Expéditeur&nbsp;: ".$expediteur->nom." &lt;".$expediteur->email."&gt;<br/>";
			$pjs=Emailing::envoi_pjs($id);
			if (count($pjs)>0){
				$html.="Pièces jointes : ";
				foreach($pjs as $pj){
					$html.="<span style='margin:2px;' class='dynatree-node' data-id='$id_categorie'><a class='dynatree-title pj' target='_blank' href='$pj'>".basename($pj)."</a></span> ";
				}
			}
			$html.="<hr/>";
			if ($nb==0 && $nb_erreurs>0) $html.="<span style='color:green;'>envoi terminé.</span> <span style='color:red;'>Il y a eu des erreurs.</span><br />
		<span style='font-size:10px;'>
				<button class='recommencer ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title=\"Rééssayer pour les messages en erreur.\">
					<span class='ui-button-icon-primary ui-icon ui-icon-refresh'></span>
					<span class='ui-button-text'>Rééssayer pour les messages en erreur.</span>
				</button>
			</span></div> ";
			if ($nb==0 && $nb_erreurs==0) $html.="<span style='color:green;'>envoi terminé.</span></div> ";
			if ($nb>0 && $statut==0) $html.="<span style='color:green;'>envoi en cours...</span><br/>
			<span style='font-size:10px;'>
				<button class='pause ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title=\"Suspendre l'envoi\">
					<span class='ui-button-icon-primary ui-icon ui-icon-pause'></span>
					<span class='ui-button-text'>Suspendre l'envoi</span>
				</button>
			</span></div>";
			if ($nb>0 && $statut==1) $html.="<span style='color:blue;'>envoi en pause.</span><br/>
			<span style='font-size:10px;'>
				 <button class='play ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title=\"Reprendre l'envoi\">
					<span class='ui-button-icon-primary ui-icon ui-icon-play'></span>
					<span class='ui-button-text'>Reprendre l'envoi</span>
				</button>
			</span></div>";
			if ($nb>0 && $statut==2) $html.="<span style='color:blue;'>envoi en pause.</span><br/>
			</div>";
			$html.="<hr />";
			$html.=$envoi['html']."<hr />";
			$html.=Html::messages_boite_envoi($id);
			$html.=Html::log_envoi($id);
		}
		return $html;
	}
	function supports()
		{
		$binf=$_SESSION['publipostage']['binf'];
		$motifs=$_SESSION['publipostage']['motifs'];
		$supports=Publipostage::supports($binf,$motifs);
		$html="";
		if(count($supports)==0){
			$html.="Aucun résultat";
		} else {
			foreach($supports as $id=>$support){
				$html.="<div class='publipostage-support ui-widget-header ui-corner-all' data-id='$id'>".$support['nom']." <span class='maj'>(".$support['datel'].")</span></div>";
			}
		}
		return $html;
	}
	function utilisateurs($binf,$motifs)
		{
		$utilisateurs=Utilisateurs::liste_rapide($motifs,$binf);
		$html="";
		foreach($utilisateurs['liste'] as $id=>$utilisateur){
			$html.="<div class='admin-utilisateur ui-widget-header ui-corner-all' data-id='$id'>".$utilisateur['nom']."</div>";
		}
		$retour=array();
		$retour['html']=$html;
		$retour['pagination']=Html::pagination($binf,$utilisateurs['nb']);
		return $retour;
	}
	function expediteurs($binf,$motifs)
		{
		$expediteurs=Emailing::expediteurs($motifs,$binf);
		$html="";
		foreach($expediteurs['liste'] as $id=>$expediteur){
			$html.="<div class='admin-expediteur ui-widget-header ui-corner-all' data-id='$id'>".$expediteur['nom']."</div>";
		}
		$retour=array();
		$retour['html']=$html;
		$retour['pagination']=Html::pagination($binf,$expediteurs['nb']);
		return $retour;
	}
	function support($id)
		{
		$html="";
		if ($id==0) {
			$html.="Aucun support.";
		} else {
			$support=Publipostage::support($id);
			$ipcase=0;
			$html="";
			$html.="<div class='titre' data-id='$id'>".$support['nom']."</div>";
			$html.="<div><span class='ui-buttonset'><a class='ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all' target='_blank' href='doc.php?t=publipostage&id_support=$id' role='button' aria-disabled='false' title='Télécharger le pdf'>
					<span class='ui-button-text'>Télécharger le pdf</span>
				</a></span></div><div class='plan'>";
			if ($support['nb_lignes']>0 || $support['nb_colonnes']>0) {
				$html.= "<div id=\"haut_de_page\" style=\"background-color:#aaaaaa;position:absolute;top:0px;left:0px;height:".$support['mp_haut']."px;width:".$support['l_page']."px;\"></div>";
				$html.= "<div id=\"gauche_de_page\" style=\"background-color:#aaaaaa;position:absolute;top:0px;left:0px;height:".$support['h_page']."px;width:".$support['mp_gauche']."px;\"></div>";
				$html.= "<div id=\"droite_de_page\" style=\"background-color:#aaaaaa;position:absolute;top:0px;left:".($support['l_page']-$support['mp_droite'])."px;height:".$support['h_page']."px;width:".$support['mp_droite']."px;\"></div>";
				$html.= "<div id=\"bas_de_page\" style=\"background-color:#aaaaaa;position:absolute;top:".($support['h_page']-$support['mp_bas'])."px;left:0px;height:".$support['mp_bas']."px;width:".$support['l_page']."px;\">&nbsp;</div>";
				$h_case=(($support['h_page']-$support['mp_bas']-$support['mp_haut'])/$support['nb_lignes']);
				$l_case=(($support['l_page']-$support['mp_gauche']-$support['mp_droite'])/$support['nb_colonnes']);
	
				for ($i=0;$i<$support['nb_lignes'];$i++) {
					for ($k=0;$k<$support['nb_colonnes'];$k++) {
						$n=$k+$i*$support['nb_colonnes'];
						if ($n>=$ipcase) {
							$html.= "<div id=\"case-$n\" class=\"case_a\" style=\"top:".($support['mp_haut']+$i*$h_case)."px;left:".($support['mp_gauche']+$k*$l_case)."px;height:".($h_case-2)."px;width:".($l_case-2)."px;\">&nbsp;</div>";
						}
						else $html.= "<div id=\"case-$n\" class=\"case_b\" style=\"top:".($support['mp_haut']+$i*$h_case)."px;left:".($support['mp_gauche']+$k*$l_case)."px;height:".($h_case-2)."px;width:".($l_case-2)."px;\">&nbsp;</div>";
					}
				}
			}

			$html.="</div>";
		}
		return $html;
	}
	function messages_boite_envoi($id)
		{
		$binf=$_SESSION['emailing']['binfm'];
		$messages=Emailing::messages_boite_envoi_et_erreur($id,$binf);
		$html="";
		$nb=Emailing::nb_messages_boite_envoi_et_erreur($id);
		if ($nb>0) {
			$html.="<span class='pagination ui-buttonset'>".Html::pagination($binf,$nb)."</span>
			<span class='pagination ui-buttonset'>
				<a aria-disabled='false' role='button' data-binf='0' class='supprtt ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all'>
					<span class='ui-button-text'>Tout supprimer</span>
				</a>
			</span>
	
			<ul>";
			foreach($messages as $id_message=>$message){
				$c=new Casquette($message['id_casquette']);
				if ($c->nom_contact()!="$$$$") $nom=$c->prenom_contact()." ".$c->nom_contact();
				else $nom=$c->nom_structure();
				if($message['erreurs']=="")
					$html.="<li class='message'>à envoyer à <span class='dest'>$nom : ".implode($c->emails(),', ')."</span><button title='supprimer' aria-disabled='false' role='button' style='border: medium none; background: none repeat scroll 0% 0% transparent;' class='suppr ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' data-id='$id_message'><span class='ui-button-icon-primary ui-icon ui-icon-close'></span><span class='ui-button-text'>supprimer</span></button></li>";
				else
					$html.="<li class='message'>ERREUR - ".$message['erreurs']." - <span class='dest'>$nom : ".implode($c->emails(),', ')."</span><button title='supprimer' aria-disabled='false' role='button' style='border: medium none; background: none repeat scroll 0% 0% transparent;' class='suppr ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' data-id='$id_message'><span class='ui-button-icon-primary ui-icon ui-icon-close'></span><span class='ui-button-text'>supprimer</span></button></li>";
			}
			$html.="</ul>";
		}
		return $html;
	}
	function log_envoi($id)
		{
		$log=Emailing::lit_log_envoi($id);
		$html="";
		if ($log!="") {
			$nb_lignes = substr_count($log, "\n");
			$html.="<h3>Envoi de $nb_lignes message(s) effectué (<a target='_blank' href='doc.php?t=log_envoi&id_envoi=$id'>log complet</a>) &nbsp;:</h3>";
			$tab=explode("\n",$log);
			$tab=array_reverse($tab);
			$log_d=implode(array_slice($tab,1,11),"<br />");
			$html.=$log_d;
		}
		return $html;
	}
	function upload_email(){
		$html="
<div class='contacts-upload'>
	<!-- The file upload form used as target for the file upload widget -->
	<form id='mail_fileupload' action='ui/includes/upload/server/php/email.php' method='POST' enctype='multipart/form-data'>
   	<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
	<div class='row fileupload-buttonbar'>
		<div class='span7'>
		<!-- The fileinput-button span is used to style the file input field as button -->
		<span class='btn btn-success fileinput-button'>
			<i class='icon-plus icon-white'></i>
			<span>Ajouter des fichiers</span>
			<input type='file' name='files[]' multiple>
		</span>
		<button type='submit' class='btn btn-primary start'>
			<i class='icon-upload icon-white'></i>
			<span>Démarrer l'upload</span>
		</button>
		<button type='reset' class='btn btn-warning cancel'>
			<i class='icon-ban-circle icon-white'></i>
			<span>Annuler l'upload</span>
		</button>
		<button type='button' class='btn btn-danger delete'>
			<i class='icon-trash icon-white'></i>
			<span>Supprimer</span>
		</button>
		<input type='checkbox' class='toggle'>
		</div>
		<div class='span5'>
		<!-- The global progress bar -->
		<div class='progress progress-success progress-striped active fade'>
			<div class='bar' style='width:0%;'></div>
		</div>
		</div>
	</div>
	<!-- The loading indicator is shown during image processing -->
	<div class='fileupload-loading'></div>
	<br>
	<!-- The table listing the files available for upload/download -->
	<table class='table table-striped'><tbody class='files' data-toggle='modal-gallery' data-target='#modal-gallery'></tbody></table>
	</form>
	<!-- modal-gallery is the modal dialog used for the image gallery -->
	<div id='modal-gallery' class='modal modal-gallery hide fade'>
		<div class='modal-header'>
		<a class='close' data-dismiss='modal'>&times;</a>
		<h3 class='modal-title'></h3>
		</div>
		<div class='modal-body'><div class='modal-image'></div></div>
		<div class='modal-footer'>
		<a class='btn modal-download' target='_blank'>
			<i class='icon-download'></i>
			<span>Download</span>
		</a>
		<a class='btn btn-success modal-play modal-slideshow' data-slideshow='5000'>
			<i class='icon-play icon-white'></i>
			<span>Slideshow</span>
		</a>
		<a class='btn btn-info modal-prev'>
			<i class='icon-arrow-left icon-white'></i>
			<span>Previous</span>
		</a>
		<a class='btn btn-primary modal-next'>
			<span>Next</span>
			<i class='icon-arrow-right icon-white'></i>
		</a>
		</div>
	</div>
</div>";
	return $html;
	}
	function upload_news(){
		$html="
<div class='contacts-upload'>
	<!-- The file upload form used as target for the file upload widget -->
	<form id='news_fileupload' action='ui/includes/upload/server/php/news.php' method='POST' enctype='multipart/form-data'>
   	<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
	<div class='row fileupload-buttonbar'>
		<div class='span7'>
		<!-- The fileinput-button span is used to style the file input field as button -->
		<span class='btn btn-success fileinput-button'>
			<i class='icon-plus icon-white'></i>
			<span>Ajouter des fichiers</span>
			<input type='file' name='files[]' multiple>
		</span>
		<button type='submit' class='btn btn-primary start'>
			<i class='icon-upload icon-white'></i>
			<span>Démarrer l'upload</span>
		</button>
		<button type='reset' class='btn btn-warning cancel'>
			<i class='icon-ban-circle icon-white'></i>
			<span>Annuler l'upload</span>
		</button>
		<button type='button' class='btn btn-danger delete'>
			<i class='icon-trash icon-white'></i>
			<span>Supprimer</span>
		</button>
		<input type='checkbox' class='toggle'>
		</div>
		<div class='span5'>
		<!-- The global progress bar -->
		<div class='progress progress-success progress-striped active fade'>
			<div class='bar' style='width:0%;'></div>
		</div>
		</div>
	</div>
	<!-- The loading indicator is shown during image processing -->
	<div class='fileupload-loading'></div>
	<br>
	<!-- The table listing the files available for upload/download -->
	<table class='table table-striped'><tbody class='files' data-toggle='modal-gallery' data-target='#modal-gallery'></tbody></table>
	</form>
	<!-- modal-gallery is the modal dialog used for the image gallery -->
	<div id='modal-gallery' class='modal modal-gallery hide fade'>
		<div class='modal-header'>
		<a class='close' data-dismiss='modal'>&times;</a>
		<h3 class='modal-title'></h3>
		</div>
		<div class='modal-body'><div class='modal-image'></div></div>
		<div class='modal-footer'>
		<a class='btn modal-download' target='_blank'>
			<i class='icon-download'></i>
			<span>Download</span>
		</a>
		<a class='btn btn-success modal-play modal-slideshow' data-slideshow='5000'>
			<i class='icon-play icon-white'></i>
			<span>Slideshow</span>
		</a>
		<a class='btn btn-info modal-prev'>
			<i class='icon-arrow-left icon-white'></i>
			<span>Previous</span>
		</a>
		<a class='btn btn-primary modal-next'>
			<span>Next</span>
			<i class='icon-arrow-right icon-white'></i>
		</a>
		</div>
	</div>
</div>";
	return $html;
	}
	function modeles_news(){
		$html="";
		$modeles=Newsletters::modeles();
		$html="";
		$html.="<ul id='modeles' class='sf-menu'>";
		$html.="<li>\n";
		$html.="<a href='#'>modèles</a>\n";
		$html.="<ul class='Menu'>\n";		
		foreach ($modeles as $theme=>$blocs) {
			$html.="<li>\n";
			$html.="<a href='#'>$theme</a>\n";
			$html.="<ul class='Menu'>\n";
			foreach ($blocs as $nom=>$id_bloc) {
				$html.="<li data-id='$id_bloc'><a href='#'>$nom</a></li>\n";
			}
			$html.="</ul>\n";
			$html.="</li>\n";
		}
		$html.="</ul>\n";
		$html.="</li>\n";
		$html.="</ul>\n";
		$html.="<button class='ajmain modele ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only' role='button' aria-disabled='false' title='Nouveau modele'>
			<span class='ui-button-icon-primary ui-icon ui-icon-plusthick'></span>
			<span class='ui-button-text'>Nouveau modèle</span>
		</button>";
		$html.="<button class='env-news ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' role='button' aria-disabled='false' title='envoyer à la selection'>
			<span class='ui-button-text'>envoyer à la selection</span>
		</button>";
		return $html;
	}
}
?>
