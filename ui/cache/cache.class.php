<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
/*
 * la classe cache gère le cache des vues html de l'interface utilisateur
 */
class cache {
	function obsolete($table, $id){
		$o=1;
		$sql="select obsolete from $table where rowid=$id";
		$base = new SQLite3('ui/cache/db/cache.sqlite');
		$base->busyTimeout (10000);
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$o=$tab['obsolete'];
		}
		$base->close();
		return $o;
	}
	function set_obsolete($table, $id){
		$base = new SQLite3('ui/cache/db/cache.sqlite');
		$base->busyTimeout (10000);
		#on teste si le cache existe
		$sql="select count(*) from $table where rowid=$id";
		$res = $base->query($sql);
		$n=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		
		if ($n==0) {
			#si non on crée le cache
			$sql="insert into $table (rowid, html, obsolete) values ($id, '', 1)";
			$res = $base->query($sql);
		} else {
			#si oui on met à jour
			$sql="update $table set obsolete=1 where rowid=$id";
			$res = $base->query($sql);
		}
		$base->close();
	}	
	function put($table, $id, $html){
		$html=SQLite3::escapeString($html);
		$base = new SQLite3('ui/cache/db/cache.sqlite');
		$base->busyTimeout (10000);
		#on teste si le cache existe
		$sql="select count(*) from $table where rowid=$id";
		$res = $base->query($sql);
		$n=0;
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$n=$tab['count(*)'];
		}
		
		if ($n==0) {
			#si non on crée le cache
			$sql="insert into $table (rowid, html, obsolete) values ($id, '$html', 0)";
			$res = $base->query($sql);
		} else {
			#si oui on met à jour
			$sql="update $table set html='$html', obsolete=0 where rowid=$id";
			$res = $base->query($sql);
		}
		$base->close();
	}	
	function get($table, $id){
		$html="";
		$base = new SQLite3('ui/cache/db/cache.sqlite');
		$base->busyTimeout (10000);
		$sql="select html from $table where rowid=$id";
		$res = $base->query($sql);
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$html=$tab['html'];
		}
		$base->close();
		return $html;
	}	
}
?>
