<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe casquette permet de lire et d'écrire dans la base les données
 * concernant une casquette
 */

							
class Email {
	var $id=0;
	var $sujet='';
	var $html='';
	var $pj=array();	
	function Email($id) {
		$this->id=$id;
	
		#on récupere le nom:
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select t1.sujet as sujet, t2.html as html, t2.pj as pj from emails as t1 inner join donnees_email as t2 on t1.rowid=t2.id_email where t1.rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->sujet=$tab['sujet'];
			$this->html=$tab['html'];
			$this->pj=explode(',',$tab['pj']);
		}
		$sql="select sujet from emails where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->sujet=$tab['sujet'];
		}
		$base->close();		
	}
	function mod_sujet($sujet, $id_utilisateur=1){
		$sujet=SQLite3::escapeString($sujet);
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="update emails set sujet='$sujet' where rowid=".$this->id;
		$base->query($sql);
		$base->close();		
	}
	function aj_donnee($html, $pj, $id_utilisateur=1) {
		$html=SQLite3::escapeString($html);
		$pj=SQLite3::escapeString($pj);
		$id_email=$this->id;
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into donnees_email (id_utilisateur,id_email,html,pj) values ($id_utilisateur,$id_email,'$html','$pj')";
		$base->query($sql);
		$base->close();
	}
	function suppr($id_utilisateur=1){
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="update emails set sujet='####' where rowid=".$this->id;
		$base->query($sql);
		$sql="delete from donnnees_email where id_email=".$this->id;
		$base->query($sql);
		$base->close();		
	}
	function images(){
		$i=0;
		$images=array();
		$chemin="fichiers/emails/".$this->id."/";
		if(file_exists($chemin)){
			if ($handle = opendir($chemin)) {
				while (false !== ($fichier = readdir($handle))) {
					if(preg_match("/(\.|\/)(gif|jpe?g|png)$/i", $fichier)) {
						$images[]=$chemin.$fichier;
					}
				}
			}
		}
		return $images;
	}
	function pjs(){
		$i=0;
		$pjs=array();
		$chemin="fichiers/emails/".$this->id."/";
		if(file_exists($chemin)){
			if ($handle = opendir($chemin)) {
				while (false !== ($fichier = readdir($handle))) {
					if(!strstr($this->html,$fichier) and $fichier!='.'  and $fichier!='..' ) {
						$pjs[]=$chemin.$fichier;
					}
				}
			}
		}
		return $pjs;
	}
	
}
?>
