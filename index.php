<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */

include "ctl/dbs.php";
include "ui/session.php";

define('CLASS_MODELE_DIR', 'modele/');
define('CLASS_CACHE_DIR', 'ui/cache/');
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_MODELE_DIR.PATH_SEPARATOR.CLASS_CACHE_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

include "utils/toujours.php";
include "ui/html.class.php";
include "ui/js.class.php";

$html_Ncats="";
if ($sel_Ncats==1) $html_Ncats="checked";
$html_Netabs="";
if ($sel_Netabs==1) $html_Netabs="checked";
$html_depts=implode($sel_depts,', ');
$html_email="";
if($sel_email==1 && $sel_Nemail==0) $html_email="checked";
$html_semail="";
if($sel_email==1 && $sel_Nemail==1) $html_semail="checked";
$html_adresse="";
if($sel_adresse==1 && $sel_Nadresse==0) $html_adresse="checked";
$html_sadresse="";
if($sel_adresse==1 && $sel_Nadresse==1) $html_sadresse="checked";
$html_cas="";
if($sel_cas==1 && $sel_Ncas==0) $html_cas="checked";
$html_scas="";
if($sel_cas==1 && $sel_Ncas==1) $html_scas="checked";
$html_N="";
if($N==1) $html_N="checked";

$html_contacts=Html::contacts($binfc,$motifc);
$html_structures=Html::structures($binfs,$motifs);
$html_structures_sel=Html::structures_selection($sel_binfs,$sel_motifs);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>contacts</title>
<meta http-equiv="Content-Type" Content="text/html; charset=UTF-8">
<link media="all" type="text/css" href="ui/css/jquery-ui.css" rel="stylesheet">
<link media="all" type="text/css" href="ui/css/ui.dynatree.css" rel="stylesheet">
<link media="all" type="text/css" href="ui/css/jquery.jscrollpane.css" rel="stylesheet">
<link media="all" type="text/css" href="ui/css/jquery.contextMenu.css" rel="stylesheet">
<style>
body {font-size:10px;margin:0px;}
label{margin-right:10px;}
#infos {background:#C1E2DB;display:none;position:absolute;left:0;bottom:0;padding:5px;z-index:9999;}
div.menu {position:relative;height:30px;}
ul {list-style:none;margin:0;padding:0;}
ul.plus {display:none;position:absolute;margin-left:50%;top:0;left:-75px;width:152px;padding:2px;z-index:1;border-bottom-right-radius:4px;
	border-bottom-left-radius:4px;border:none;
	-webkit-box-shadow: 0 6px 6px -6px black;
	-moz-box-shadow: 0 6px 6px -6px black;
	box-shadow: 0 6px 12px -6px black;
}
ul.plus li {cursor:pointer;border:1px solid #aaa;padding:5px 0 5px 0;border-radius:4px;width:150px;margin-bottom:2px;text-align:center;font-weight:bold;position:relative;}
ul.champs li {text-align:right; margin-bottom:10px}
ul.champs li .tooltip {margin-right:40px;}
.tooltip {display:none;}
.ui-dialog .ui-dialog-content {overflow:visible;}
.ui-dialog {
	overflow:visible;
}
.titre{font-size:14px;padding:5px;}
.tabs{float:left;padding:2px;margin:0 0 5px 5px;}
.ui-state-default.actif {border-color:#aaa;}
.ui-draggable-dragging {background:#FFF;opacity:0.5;border-radius:4px;border:1px solid #aaa;}
.cadre{border:1px solid #aaa;padding:2px 15px 10px 15px;border-radius: 4px;}
a.dynatree-title{border:1px solid #aaa; border-radius:4px;text-decoration:none;}
.barre {font-size:10px;position:absolute;right:0px;top:3px;}
.pagination a.first {border-right:none;}
.pagination a.int {border-right:none;border-left:none;}
.pagination a.last {border-left:none;}
.pagination a.on span {text-decoration:underline;}
.casquette h3, .etablissement h3 {font-size:14px;color:#888;margin-bottom:5px;}
.maj {font-size:10px;color:#aaa;}
.label {font-size:12px;color:#888;}
.valeur {font-size:14px;color:#000;}
.pagination .ui-button-text-only .ui-button-text {
    padding: 3px;
}
#edition {position:absolute;background:#fff;z-index:0;}

#ed_contacts{position:absolute;}
#ed_contacts-head{position:absolute;}
#ed_contacts-head .titre {position:absolute;width:199px;height:22px;color:#333; font-size:22px;font-family:sans-serif;}
#ed_structures{position:absolute;}
#ed_structures-head{position:absolute;}
#ed_structures-head .titre {position:absolute;width:199px;height:22px;color:#333; font-size:22px;font-family:sans-serif;}
#ed_categories{position:absolute;}
#ed_categories-head{position:absolute;}
#ed_categories-head .titre {position:absolute;width:199px;height:22px;color:#333; font-size:22px;font-family:sans-serif;}
#ed_structures .contactsEtab li a {cursor:pointer;}
#edition .contextMenu, .ui-dialog{
	border:none;
	-webkit-box-shadow: 2px 4px 10px -4px black;
	-moz-box-shadow: 2px 4px 10px -4px black;
	box-shadow: 2px 4px 10px -4px black;
}
#edition .pagination {position:absolute;width:280px;}
#edition .filtre {position:absolute;width:280px;}
#edition .filtre input {
	width:199px;
	height:22px;
	border:1px solid #ccc;
	border-radius:4px;
	position:relative;
	top:2px;
	margin-right:5px;
	padding-left:5px;
}
#edition button.ajmain {
	position:absolute;
}
#edition .etabcas {cursor:pointer;}
#edition div.perso input {width:140px;}

#selection {position:absolute;background:#fff;z-index:1;}

#sel_casquettes{position:absolute;}
#sel_casquettes .casquette {width:200px;height:250px;float:left;margin:0 5px 5px 0;overflow:hidden;}
#sel_casquettes .casquette div.nomstr {padding:5px;}
#sel_casquettes .casquette div.nometab {margin:5px;}
#sel_casquettes .etabcas{margin-bottom:5px;}
#sel_casquettes .cas {padding:5px;}
#sel_categories{position:absolute;}
#sel_structures{position:absolute;}
#sel_filtres{position:absolute;}
#sel_filtres>div {float:left;height:30px;}
#sel_filtres input[type='text'] {width:150px;}

#structures .filtre {width:280px;margin-bottom:4px;}
#selection .filtre input {
	width:199px;
	height:22px;
	border:1px solid #ccc;
	border-radius:4px;
	position:relative;
	top:2px;
	margin-right:5px;
	padding-left:5px;
}
#selection .maj {font-size:10px;color:#aaa;}
#selection .blanc {color:#fff;}
#selection .etab-sel {background:#aaa;border: 1px solid #bbb;}
#selection .etab-sel a { color:#fff;}
#sel_humains .cadre {float:left;padding:2px;}
#sel_humains .cadre1 {float:left;padding:2px;width:196px;}
#sel_humains .recherche {float:left;width:200px;padding:2px;}
#sel_humains .op {float:left;margin:2px;}
#sel_humains {position:absolute;top:40px;}
#sel_structures .filtre {margin-bottom:4px;}
#menu {position:absolute;z-index:10;height:25px;width:100%;
	-webkit-box-shadow: 2px 4px 10px -4px black;
	-moz-box-shadow: 2px 4px 10px -4px black;
	box-shadow: 2px 4px 10px -4px black;
}
#menu .boite-menu {padding-top:5px;}
#menu a {text-decoration:none; color:#333; font-size:14px;margin-left:20px; font-family:sans-serif;}

#email {position:absolute;background:#fff;z-index:0;}
#mail_entetes {position:absolute;}
#mail_entetes .mail-entete {padding:5px; cursor:pointer; margin-bottom:5px;background:none;color:#000; }
#mail_entetes_head {position:absolute;}
#mail_email {position:absolute;}
#mail_email .enr-email.off {opacity:0.5;}
#email .titre{color:#333; font-size:22px;font-family:sans-serif;}
#mail_email .titre {font-size:22px;}
#mail_editeur {margin:10px 0 10px 0;}

#newsletter {position:absolute;background:#fff;z-index:0;}
#emailing {position:absolute;background:#fff;z-index:0;}
#emailing_envois {position:absolute;}
#emailing_envois_head {position:absolute;}
#emailing_envois .emailing-envoi {padding:5px; cursor:pointer; margin-bottom:5px;background:none;color:#000; }
#emailing_envoi {position:absolute;}
#emailing .titre{color:#333; font-size:22px;font-family:sans-serif;}
#emailing_envoi .meta {margin-top:10px;font-family:sans-serif;font-size:14px;}
#emailing_envoi .titre {font-size:22px;}

#publipostage {position:absolute;background:#fff;z-index:0;}
#publipostage_support {position:absolute;}
#publipostage_supports {position:absolute;}
#publipostage_supports .publipostage-support {padding:5px; cursor:pointer; margin-bottom:5px;background:none;color:#000; }
#publipostage_supports_head {position:absolute;}
#publipostage .titre{color:#333; font-size:22px;font-family:sans-serif;}
#publipostage_support .titre {font-size:22px;}
.case_a {background:#eee;position:absolute;border:1px solid #ddd;}
.plan {position:relative;}

#mask {position:absolute;z-index:8;width:100%; height:100%; background:#FFF;}
#mask .loader{position:absolute;width:66px; height:66px; background:url('ui/css/images/ajax-loader.gif');left:50%;top:50%;margin-left:-33px;margin-top:-33px;}
select {width:270px;}
</style>

<!-- fonctionnalité d'upload -->

<!-- Bootstrap CSS Toolkit styles -->
<link rel="stylesheet" href="ui/includes/upload/css/bootstrap.min.css">
<!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
<link rel="stylesheet" href="ui/includes/upload/css/bootstrap-responsive.min.css">
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
<!-- Bootstrap Image Gallery styles -->
<link rel="stylesheet" href="ui/includes/upload/css/bootstrap-image-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="ui/includes/upload/css/jquery.fileupload-ui.css">
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="ui/includes/upload/js/html5.js"></script><![endif]-->
<!-- The template to display files available for upload -->
</head>
<body>
<div id="mask">
<div class='loader'></div>
</div>
<div id="infos"></div>
<div id='edition'>
	<ul id="ed_menu_casquette" class="contextMenu">
	    <li class="edit">
		<a href="#edit">Modifier</a>
	    </li>
	    <li class="edit">
		<a href="#rename">Renommer</a>
	    </li>
	</ul>
	<ul id="ed_menu_etablissement" class="contextMenu">
	    <li class="edit">
		<a href="#edit">Modifier</a>
	    </li>
	    <li class="edit">
		<a href="#rename">Renommer</a>
	    </li>
	</ul>
	<ul id="ed_menu_categorie" class="contextMenu">
	    <li class="edit">
		<a href="#rename">Renommer</a>
	    </li>
	    <li class="delete">
		<a href="#delete">Supprimer</a>
	    </li>
	</ul>
	<ul id="ed_menu_categorie_contact" class="contextMenu">
	    <li class="delete">
		<a href="#delete">Supprimer</a>
	    </li>
	</ul>
	
	<div id="ed_contacts-head">
		<div class='titre'>Contacts</div>
		<div class="filtre"><?=Html::filtre_contacts($motifc)?></div>
		<button class="ajmain ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" aria-disabled="false" title="ajouter un contact">
			<span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>
			<span class="ui-button-text">ajouter un contact</span>
		</button>
		<div class="pagination ui-buttonset"><?=$html_contacts['pagination']?></div>
	</div>
	<div id="ed_structures-head">
		<div class='titre'>Structures</div>
		<div class="filtre"><?=Html::filtre_structures($motifs)?></div>
		<button class="ajmain ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" aria-disabled="false" title="ajouter une structure">
			<span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>
			<span class="ui-button-text">ajouter une structure</span>
		</button>
		<div class="pagination ui-buttonset"><?=$html_structures['pagination']?></div>
	</div>
	<div id="ed_categories-head">
		<div class='titre'>Listes</div>
		<button class="ajmain ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" aria-disabled="false" title="ajouter une liste">
			<span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>
			<span class="ui-button-text">ajouter une liste</span>
		</button>
	</div>

	<div id="ed_contacts"><?=$html_contacts['html']?>
	</div>
	<div id="ed_structures"><?=$html_structures['html']?>
	</div>
	<div id="ed_categories"><div id="ed_tree"></div></div>
</div>













<div id="selection">
	<ul id="sel_menu_casquette" class="contextMenu">
	    <li class="edit">
		<a href="#edit">Modifier</a>
	    </li>
	    <li class="edit">
		<a href="#renameco">Contact</a>
	    </li>
	    <li class="edit">
		<a href="#renamecas">Casquette</a>
	    </li>
	</ul>
	<ul id="sel_menu_etablissement" class="contextMenu">
	    <li class="edit">
		<a href="#edit">Modifier</a>
	    </li>
	    <li class="edit">
		<a href="#renamest">Structure</a>
	    </li>
	    <li class="edit">
		<a href="#renameet">Etablissement</a>
	    </li>
	</ul>
	<ul id="sel_menu_categorie_contact" class="contextMenu">
	    <li class="delete">
		<a href="#delete">Supprimer</a>
	    </li>
	</ul>

	<div id="sel_humains"></div>
	<div id="sel_filtres">
	<div>
	<input type="text" name="motifs" id="sel_motifs" value="<?=$sel_motifs?>">
	<button class="motifs ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" title="rechercher">
		<span class="ui-button-text">rechercher</span>
	</button>
	</div>
	<div>
	<input type="text" name="mots" id="sel_mots" value="<?=$sel_mots?>">
	<button class="mots ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" title="rechercher">
		<span class="ui-button-text">rechercher des mots entiers</span>
	</button></div>
	<div>
	<input type="text" name="depts" id="sel_depts" value="<?=$html_depts?>">
	<button class="depts ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" title="rechercher">
		<span class="ui-button-text">départements</span>
	</button>
	</div>
	<div style='clear:both;'></div>
			<div><input type="checkbox" id="sel_email" <?=$html_email?>/> Avec e-mail. </div>
			<div><input type="checkbox" id="sel_semail" <?=$html_semail?>/> Sans e-mail. </div>
			<div><input type="checkbox" id="sel_adresse" <?=$html_adresse?>/> Avec adresse postale.</div>
			<div><input type="checkbox" id="sel_sadresse" <?=$html_sadresse?>/> Sans adresse postale.</div>
			<div><input type="checkbox" id="sel_Ncats" <?=$html_Ncats?>/> Pas dans les listes. </div>
			<div><input type="checkbox" id="sel_Netabs" <?=$html_Netabs?>/> Pas dans les structures. </div>
			<div><input type="checkbox" id="sel_cas" <?=$html_cas?>/> Parmi les contacts sélectionnés. </div>
			<div><input type="checkbox" id="sel_scas" <?=$html_scas?>/> Exclure les contacts sélectionnés. </div>
			<div><input type="checkbox" id="sel_N" <?=$html_N?>/> Inverser la selection.</div>
	<div style='clear:both;'></div>
	<div>
	<span class="ui-buttonset">
		<a id="sel_decoche" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" title="Tout décocher">
			<span class="ui-button-text">Tout décocher</span>
		</a>
	</span>
	<span class="pagination ui-buttonset"><?=Html::pagination($_SESSION['sel_binfc'],Casquettes::liste('nb'))?></span>
	</div>
	</div>

	<div id="sel_casquettes"><?=Html::casquettes_selection()?></div>
	<div id="sel_categories"><div id="sel_tree"></div></div>
	<div id="sel_structures">
		<div class='head'>
			<div class="filtre"><?=Html::filtre_structures($motifs)?></div>
			<span class="pagination ui-buttonset"><?=$html_structures_sel['pagination']?></span>
		</div>
		<div class='liste'>
		<?=$html_structures_sel['html']?>
		</div>
	</div>
</div>
<div id="email">
<ul id="mail_menu_email" class="contextMenu">
    <li class="edit">
	<a href="#rename">Renommer</a>
    </li>
    <li class="delete">
	<a href="#delete">Supprimer</a>
    </li>
</ul>
<div id="mail_email">
</div>
<div id="mail_entetes_head">
	<div class='titre'>Emails</div>
	<button class="ajmain ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" aria-disabled="false" title="Nouvel e-mail">
		<span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>
		<span class="ui-button-text">Nouvel e-mail</span>
	</button>
	<div class="filtre"><?=Html::filtre_email()?></div>
	<span class="pagination ui-buttonset"><?=Html::pagination($_SESSION['email']['binf'],Emails::nb_emails())?></span>
</div>
<div id="mail_entetes">
<?=Html::entetes_email()?>
</div>
</div>
<div id="newsletter">
</div>
<div id="emailing">
	<div id="emailing_envois_head">
		<div class='titre'>Envois</div>
		<div class="filtre"><?=Html::filtre_envoi()?></div>
		<span class="pagination ui-buttonset"><?=Html::pagination($_SESSION['emailing']['binf'],Emailing::nb_envois())?></span>
	</div>
	<div id="emailing_envois">
	<?=Html::envois()?>
	</div>
	<div id="emailing_envoi">
	<?=Html::envoi(Emailing::dernier())?>
	</div>
</div>

<div id="publipostage">
	<ul id="publipostage_menu_support" class="contextMenu">
	    <li class="edit">
		<a href="#rename">Renommer</a>
	    </li>
	    <li class="edit">
		<a href="#edit">Modifier</a>
	    </li>
	    <li class="delete">
		<a href="#delete">Supprimer</a>
	    </li>
	</ul>
	<div id="publipostage_supports_head">
		<div class='titre'>Supports</div>
		<button class="ajmain ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" aria-disabled="false" title="Nouvel e-mail">
			<span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span>
			<span class="ui-button-text">Nouvel e-mail</span>
		</button>
		<div class="filtre"><?=Html::filtre_support()?></div>
		<span class="pagination ui-buttonset"><?=Html::pagination($_SESSION['emailing']['binf'],Publipostage::nb_supports())?></span>
	</div>
	<div id="publipostage_supports">
	<?=Html::supports()?>
	</div>
	<div id="publipostage_support">
	<?=Html::support(Publipostage::dernier())?>
	</div>
</div>
<div id="menu"><div class='boite-menu'><a href="#edition">edition</a> <a href="#selection">selection</a> <a href="#email">email</a> <a href="#emailing">e-mailing</a>  <a href="#publipostage">publipostage</a> <a href="doc.php?t=export_csv" target="_blank">csv</a></div></div>

<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>Démarrer l'upload</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>Annuler</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>Supprimer</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>
    </tr>
{% } %}
</script>

<!-- fin fonctionnalité d'upload -->

<script src="ui/js/jquery.min.js"></script>
<script src="ui/js/jquery-ui.min.js"></script>
<script src="ui/js/jquery.dataset.js" type="text/javascript"></script>
<script src="ui/js/jquery.mousewheel.js" type="text/javascript"></script>
<script src="ui/js/jquery.jscrollpane.js" type="text/javascript"></script>
<script src="ui/js/jquery.dynatree.js" type="text/javascript"></script>
<script src="ui/js/jquery.contextMenu.js" type="text/javascript"></script>
<script src="ui/js/jquery.ba-hashchange.js" type="text/javascript"></script>
<script type="text/javascript" src="ui/includes/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="ui/includes/ckeditor/adapters/jquery.js"></script>
<script src="ui/js/edition.js" type="text/javascript"></script>
<script src="ui/js/selection.js" type="text/javascript"></script>
<script src="ui/js/email.js" type="text/javascript"></script>
<script src="ui/js/emailing.js" type="text/javascript"></script>
<script src="ui/js/publipostage.js" type="text/javascript"></script>
<script src="ui/js/iumm.js" type="text/javascript"></script>

<!-- fonctionnalité d'upload -->

<!-- The Templates plugin is included to render the upload/download listings -->
<script src="ui/includes/upload/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="ui/includes/upload/js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="ui/includes/upload/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
<script src="ui/includes/upload/js/bootstrap.min.js"></script>
<script src="ui/includes/upload/js/bootstrap-image-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="ui/includes/upload/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="ui/includes/upload/js/jquery.fileupload.js"></script>
<!-- The File Upload image processing plugin -->
<script src="ui/includes/upload/js/jquery.fileupload-ip.js"></script>
<!-- The File Upload user interface plugin -->
<script src="ui/includes/upload/js/jquery.fileupload-ui.js"></script>
<!-- The localization script -->
<script src="ui/includes/upload/js/locale.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="ui/includes/upload/js/cors/jquery.xdr-transport.js"></script><![endif]-->

<!-- fin fonctionnalité d'upload -->



</body>
</html>
