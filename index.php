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
<link rel="stylesheet" media="all" type="text/css" href="ui/includes/min/?f=ui/css/jquery-ui.css,ui/css/ui.dynatree.css,ui/css/jquery.jscrollpane.css,ui/css/jquery.contextMenu.css,ui/includes/upload/css/bootstrap.min.css,ui/includes/upload/css/bootstrap-responsive.min.css,ui/includes/upload/css/bootstrap-image-gallery.min.css,ui/includes/upload/css/jquery.fileupload-ui.css,ui/css/iumm.css">
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="ui/includes/upload/js/html5.js"></script><![endif]-->
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
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
<script src="ui/includes/min/?f=ui/js/jquery.min.js,ui/js/jquery-ui.min.js,ui/js/jquery.dataset.js,ui/js/jquery.mousewheel.js,ui/js/jquery.jscrollpane.js,ui/js/jquery.dynatree.js,ui/js/jquery.contextMenu.js,ui/js/jquery.ba-hashchange.js,ui/includes/ckeditor/ckeditor.js,ui/includes/ckeditor/adapters/jquery.js,ui/js/edition.js,ui/js/selection.js,ui/js/email.js,ui/js/emailing.js,ui/js/publipostage.js,ui/js/iumm.js,ui/includes/upload/js/tmpl.min.js,ui/includes/upload/js/load-image.min.js,ui/includes/upload/js/canvas-to-blob.min.js,ui/includes/upload/js/bootstrap.min.js,ui/includes/upload/js/bootstrap-image-gallery.min.js,ui/includes/upload/js/jquery.iframe-transport.js,ui/includes/upload/js/jquery.fileupload.js,ui/includes/upload/js/jquery.fileupload-ip.js,ui/includes/upload/js/jquery.fileupload-ui.js,ui/includes/upload/js/locale.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="ui/includes/upload/js/cors/jquery.xdr-transport.js"></script><![endif]-->

<!-- fin fonctionnalité d'upload -->
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



</body>
</html>
