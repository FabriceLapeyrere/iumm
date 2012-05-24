<?php
class Envoi{
	var $id=0;
	function Envoi($id){
		$this->id=$id;
	}
	function start(){
		$command = "nohup /usr/bin/php exec.php envoi_mails ".$this->id." > /dev/null 2>&1 &";
		exec($command);
	}	
	function stop(){
		Emailing::arret_envoi($this->id);
	}
}
?>
