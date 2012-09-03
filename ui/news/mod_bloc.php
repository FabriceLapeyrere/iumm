<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
include "ui/news/update_bloc.php";

$reponse['js'].="
	$('#mbloc$id_news"."_$id_bloc').dialog('close');
";
?>
