<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$id_envoi=$argv[2];
if(Emailing::statut_envoi($id_envoi)==1) {
	Emailing::play_envoi($id_envoi);
	if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - Envoi numéro $id_envoi commencé.\n", 3, "tmp/envoi.log");
	require("ctl/includes/phpmailer/class.phpmailer.php");
	require("ctl/includes/phpmailer/class.smtp.php");
	require("conf/mailing.php");

	$envoi=Emailing::envoi($id_envoi);
	$html=$envoi['html'];
	$sujet=$envoi['sujet'];
	$nb=$envoi['nb'];
	$expediteur=json_decode($envoi['expediteur']);
	$pjs=Emailing::envoi_pjs($id_envoi);
	$pas=0;		

	$mail = new PHPMailer();
	$mail->SetLanguage("fr","ctl/includes/phpmailer/language/");
	$mail->IsSMTP();
	$mail->Host = $smtp_host;
	$mail->Port = $smtp_port;
	$mail->SMTPAuth = $smtp_auth;
	$mail->Username = $smtp_username;
	$mail->Password = $smtp_password;
	$mail->CharSet = "UTF-8";
	$mail->Subject = $sujet;
	foreach ($pjs as $key=>$pj) {
		echo "$pj\n";
		$mail->AddAttachment($pj);
	}
		
	$mail->MsgHTML($html);
	$mail->From = $expediteur->email;
	$mail->FromName = $expediteur->nom;
	while (Emailing::nb_messages_boite_envoi($id_envoi)>0) {
		if ($pas==$mailing_nbmail) {
			$pas=1;
			if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - On attend\n", 3, "tmp/envoi.log");
			for($j=1;$j<$mailing_t_pause;$j++) {
				sleep(1);
				if(Emailing::statut_envoi($id_envoi)==2) {
					if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - statut : ".Emailing::statut_envoi($id_envoi)." -> arret demandé\n", 3, "tmp/envoi.log");
					Emailing::pause_envoi($id_envoi);
					if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - statut : ".Emailing::statut_envoi($id_envoi)."\n", 3, "tmp/envoi.log");
					if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - Envoi numéro $id_envoi arrété.\n", 3, "tmp/envoi.log");
					exit(0);
				}
			}
		} else {
			$pas++;
		}
		$tab=Emailing::envoi_premier_message($id_envoi);
		$i=$tab['i'];
		$id_casquette=$tab['id_casquette'];
	
		$c=new Casquette($tab['id_casquette']);		
		foreach($c->emails() as $id=>$email){
			$mail->AddAddress($email,$c->prenom_contact()." ".$c->nom_contact());
		}
	
		if (!$mail->Send())
		{
			$log=date('d/m/Y H:i:s')." - ERREUR - ".$mail->ErrorInfo." - $i/$nb ".$c->prenom_contact()." ".$c->nom_contact()." : ".implode($c->emails(),', ')." \n";
			Emailing::message_erreur($tab['rowid'],$mail->ErrorInfo);
			if(DEBUG_LOG) error_log($log, 3, "tmp/envoi.log");
		}
		else
		{
			$log=date('d/m/Y H:i:s')." - $i/$nb ".$c->prenom_contact()." ".$c->nom_contact()." : ".implode($c->emails(),', ')." \n";
			Emailing::log_envoi($id_envoi, $log);
			Emailing::sup_message($tab['rowid']);
			if(DEBUG_LOG) error_log($log, 3, "tmp/envoi.log");
		}
		$mail->ClearAddresses();
		#sleep(2);
	}
}
Emailing::pause_envoi($id_envoi);
if(DEBUG_LOG) error_log("statut : ".Emailing::statut_envoi($id_envoi)."\n", 3, "tmp/envoi.log");
if(DEBUG_LOG) error_log("Envoi numéro $id_envoi arrété.\n", 3, "tmp/envoi.log");
exit(0);
?>
