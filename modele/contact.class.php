<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe casquette permet de lire et d'écrire dans la base les données
 * concernant une casquette
 */

							
class Contact {
	var $id=0;
	var $nom='';
	var $prenom='';
	var $casquettes=array();	
	function Contact($id) {
		$this->id=$id;
	
		#on récupere le nom:
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="select nom, prenom from contacts where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$this->nom=$tab['nom'];
			$this->prenom=$tab['prenom'];
		}
		
		#on récupere les casquettes :
		$sql="select t1.rowid, t1.nom from casquettes as t1 inner join ass_casquette_contact as t2 on t1.rowid=t2.id_casquette where t2.id_contact=$id order by nom";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$rowid=$tab['rowid'];
			$nom=$tab['nom'];
			$this->casquettes[$rowid]=array('nom'=>$nom);
		}
		$base->close();		
	}
	function mod_nom($nom, $prenom, $id_utilisateur=1){
		$nom=SQLite3::escapeString($nom);
		$prenom=SQLite3::escapeString($prenom);
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update contacts set nom='$nom', prenom='$prenom' where rowid=".$this->id;
		$base->query($sql);
		foreach($this->casquettes as $id=>$casquette){
			$tri=SQLite3::escapeString($nom." ".$prenom);
			$sql="update casquettes set tri='$tri' where rowid=$id";
			$base->query($sql);
			$c=new Casquette($id);
			$c->cache();	
		}
		$base->close();		
		$this->cache();
	}
	function aj_casquette($nom, $id_utilisateur=1) {
		$nom=SQLite3::escapeString($nom);
		$tri=SQLite3::escapeString($this->nom." ".$this->prenom);
		$id_contact=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="insert into casquettes (id_utilisateur, nom, tri) values ($id_utilisateur, '$nom', '$tri')";
		$base->query($sql);
		$id_casquette=$base->lastInsertRowID();
		$sql="insert into ass_casquette_contact (id_utilisateur, id_casquette, id_contact) values ($id_utilisateur, $id_casquette,$id_contact)";
		$res = $base->query($sql);
		$base->close();
		$c=new Casquette($id_casquette);
		$c->aj_donnee('Telephone_fixe','Téléphone fixe','telephone','',$id_utilisateur);
		$c->aj_donnee('Telephone_portable','Téléphone portable','telephone','',$id_utilisateur);
		$c->aj_donnee('Email','E-mail','email','',$id_utilisateur);
		$c->aj_donnee('Fonction','Fonction','texte_court','',$id_utilisateur);
		$this->cache();
		return $id_casquette;
	}
	function suppr($id_utilisateur=1){
		foreach($this->casquettes as $id_casquette=>$nom_casquette){
			$c=new Casquette($id_casquette);
			$c->suppr($id_utilisateur);
		}
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="update contacts set nom='####', prenom='####' where rowid=".$this->id;
		$base->query($sql);
		$base->close();
		$this->de_index();
	}
	function cache(){
		async('modele/cache/cache', array('objet'=>'Contact', 'id'=>$this->id));	
	}
	function index(){
		error_log(date('d/m/Y H:i:s')." - Contact ".$this->id.", ".$this->nom." ".$this->prenom."\n", 3, "tmp/cache.log");
		$content="";
		foreach ($this->casquettes as $id=>$casquette){
			$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
			$sql="select content from cache_casquette where rowid=$id";
			$res = $base->query($sql);
			while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
				$content.=$tab['content']." ";
			}
			$base->close();		
		}
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		#on teste si le cache existe
		$sql="select count(*) from cache_contact where rowid=$id";
		$res = $base->query($sql);
		$n=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$content=SQLite3::escapeString(noaccent($content));
		if ($n==0) {
			#si non on crée le cache
			$sql="insert into cache_contact (rowid, content) values ($id, '$content')";
			$res = $base->query($sql);
		} else {
			#si oui on met à jour
			$sql="update cache_contact set content='$content' where rowid=$id";
			$res = $base->query($sql);
		}
		$base->close();		
	}
	function index_existe(){
		$id=$this->id;
		#on teste si le cache existe
		$sql="select count(*) from cache_contact where rowid=$id";
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		$n=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		$base->close();		
		return $n;	
	}
	function de_index(){
		$id=$this->id;
		$base = new SQLite3('db/contacts.sqlite');
		$base->busyTimeout (10000);
		$sql="delete from cache_contact where rowid=$id";
		$res = $base->query($sql);
		$base->close();	
	}	
}
?>
