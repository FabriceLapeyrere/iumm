<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$reponse=array();
	$message="";
	$succes=1;
	$id_email=$_POST['id_email'];
	$html=$_POST['html'];
	$pj=$_POST['pj'];
	$e=new Email($id_email);
	$e->aj_donnee($html,$pj);
	$js="
	$('textarea.editor').ckeditorGet().resetDirty();
	$('#mail_email .enr-email').removeClass('on');
	$('#mail_email .enr-email').addClass('off');
	";
	if($succes) {
		$reponse['succes']=1;
		$reponse['message']=$message;
		$reponse['html']="";
		$reponse['js']=$js;
	} else {
		$reponse['succes']=0;
		$reponse['message']="";		
	}
?>
