<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$id_email=$_POST['id_email'];
	$format=$_POST['format'];
	$_SESSION['email_rep']="emails/$id_email/";
	$e=new Email($id_email);
	$sujet=$e->sujet;
	$pjs="";
	if (count($e->pjs())>0)	$pjs="<div id='mail_pjs'>".implode(', ',$e->pjs())."</div>";
	$html="
	<button data-id='$id_email' class='mod-email ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' role='button' aria-disabled='false' title='modifier'>
		<span class='ui-button-text'>modifier</span>
	</button>
	<button data-id='$id_email' class='mod-email ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' role='button' aria-disabled='false' title='envoyer à la selection'>
		<span class='ui-button-text'>envoyer à la selection</span>
	</button>
	<div id='mail_sujet'>$sujet</div>
	$pjs
	".$e->html."
	";
	$js="
	$( 'textarea.editor' ).ckeditor();
	".js::upload()."
	mail_ajuste();
	";
	
	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['js']=$js;
			break;
	}
?>

