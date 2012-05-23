<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */

	$id_envoi=$_GET['id_envoi'];	
	header ("Content-type: text/plain");
	header ("Content-Disposition: attachment; filename=\"log_envoi_$id_envoi.txt\"");
	echo Emailing::lit_log_envoi($id_envoi);
?>	
