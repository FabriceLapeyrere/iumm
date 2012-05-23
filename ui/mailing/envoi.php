<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$id_envoi=$_POST['id_envoi'];
	if(isset($_POST['binf'])) {
		$_SESSION['emailing']['binfm']=$_POST['binf'];
	}
	$format=$_POST['format'];
	$html=Html::envoi($id_envoi);
	$nb=Emailing::nb_messages_boite_envoi($id_envoi);
	$js=js::emailing_envoi();
	if ($nb>0)
		$js.="setTimeout('$.post(\"ajax.php\",{action:\"mailing/envoi\",id_envoi:$(\"#emailing_envoi .titre\").dataset(\"id\"),format:\"html\"},function(data){if (data.succes==1) { $(\"#emailing_envoi .jspPane\").html(data.html); eval(data.js); } }, \"json\" );',5000);";
	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['js']=$js;
			break;
	}
?>

