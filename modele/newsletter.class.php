<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe casquette permet de lire et d'écrire dans la base les données
 * concernant une casquette
 */

							
class Newsletter {
	var $id=0;
	var $sujet='';
	var $news='';
	function Newsletter($id) {
		$this->id=$id;
	
		#on récupere le nom:
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select sujet from news where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->sujet=$tab['sujet'];
		}
		$base->close();		
	}
	function news(){
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="select news from donnees_news where id_news=".$this->id." order by date desc limit 0,1";
		$res = $base->query($sql);
		$news="";
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$news=$tab['news'];
		}
		return $news;
	}
	function mod_sujet($sujet, $id_utilisateur=1){
		$sujet=SQLite3::escapeString($sujet);
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="update news set sujet='$sujet' where rowid=".$this->id;
		$base->query($sql);
		$base->close();		
	}
	function aj_donnee($news, $id_utilisateur=1) {
		$news=SQLite3::escapeString($news);
		$id_news=$this->id;
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into donnees_news (id_utilisateur,id_news,news) values ($id_utilisateur,$id_news,'$news')";
		$base->query($sql);
		$base->close();
	}
	function aj_bloc($id, $index, $id_utilisateur=1) {
		$news=$this->news();
		$nblocs=array();
		$bc=array('id_modele'=>$id,'params'=>array());
		if ($news!=""){
			$blocs=json_decode($news);
			$i=0;
			foreach($blocs as $bloc){
				if ($i!=$index) $nblocs[]=$bloc;
				else {
					$nblocs[]=$bc;
					$nblocs[]=$bloc;
				}
				$i++;
			}
			if ($i==$index) $nblocs[]=$bc;
		} else {
			$nblocs[]=$bc;
		}
		$news=json_encode($nblocs);
		$this->aj_donnee($news, $id_utilisateur);	
	}
	function ord_blocs($blocs, $id_utilisateur=1) {
		$news=$this->news();
		$nblocs=array();
		$oldblocs=json_decode($news);
		foreach($blocs as $id_bloc){
			$nblocs[]=$oldblocs[$id_bloc];
		}
		$news=json_encode($nblocs);
		$this->aj_donnee($news, $id_utilisateur);	
	}
	function sup_bloc($id_bloc, $id_utilisateur=1) {
		$news=$this->news();
		$nblocs=array();
		$oldblocs=json_decode($news);
		$i=0;
		foreach($oldblocs as $bloc){
			if($i!=$id_bloc) $nblocs[]=$bloc;
			$i++;
		}
		$news=json_encode($nblocs);
		$this->aj_donnee($news, $id_utilisateur);	
	}
	function suppr($id_utilisateur=1){
		$u=new Utilisateur($id_utilisateur);
		$nom="newsletter-".$this->id."-".filter(trim($this->sujet)).".html";
		$html="<!DOCTYPE html><html><head><title>".trim($this->sujet)."</title><meta content='text/html; charset=UTF-8' http-equiv='Content-Type'></head><body><h1>".trim($this->sujet)." (supprimé le ".date('d/m/Y H:i:s')." par ".$u->nom().")</h1><hr />";
		$html.="<pre>".$news."</pre>";
		$html.="</body></html>";
		file_put_contents("modele/corbeille/$nom",$html);
		$base = new SQLite3('db/mailing.sqlite');
		$base->busyTimeout (10000);
		$sql="update news set sujet='####' where rowid=".$this->id;
		$base->query($sql);
		$sql="delete from donnees_news where id_email=".$this->id;
		$base->query($sql);
		$base->close();		
	}	
}
?>
