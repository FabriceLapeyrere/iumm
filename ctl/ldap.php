<?php
include 'conf/ldap.php';	
/**
 * function ldap_escape
 * @author Chris Wright
 * @version 2.0
 * @param string $subject The subject string
 * @param bool $dn Treat subject as a DN if TRUE
 * @param string|array $ignore Set of characters to leave untouched
 * @return string The escaped string
 */
function ldap_escape ($subject, $dn = FALSE, $ignore = NULL) {

    // The base array of characters to escape
    // Flip to keys for easy use of unset()
    $search = array_flip($dn ? array('\\', ',', '=', '+', '<', '>', ';', '"', '#') : array('\\', '*', '(', ')', "\x00"));

    // Process characters to ignore
    if (is_array($ignore)) {
        $ignore = array_values($ignore);
    }
    for ($char = 0; isset($ignore[$char]); $char++) {
        unset($search[$ignore[$char]]);
    }

    // Flip $search back to values and build $replace array
    $search = array_keys($search); 
    $replace = array();
    foreach ($search as $char) {
        $replace[] = sprintf("\\%02x", ord($char));
    }

    // Do the replacement and return the result
    return str_replace($search, $replace, $subject);

}
function ldap_update($condition="1",$v=0) {
	// connect to ldap server
	include 'conf/ldap.php';	
	$ldapconn = ldap_connect($ldap_srv)
		or die("Could not connect to LDAP server.");
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

	if ($ldapconn) {

		// binding to ldap server
		$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
		$base = new SQLite3('db/index.sqlite');
		$base->busyTimeout (10000);
		$sql="select rowid,tri from indexes where $condition";
		$res = $base->query($sql);
		$liste=array();
		while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
			$liste[]=$tab['rowid'];
		}
		# on efface :
		if ($v==1) echo "On efface :\n";
		foreach($liste as $id){
			$contact="uid=$id,$ldapbase";
			if ($v==1) echo "Suppression de la casquette $id            \r";
			@ldap_delete($ldapconn,$contact);
		}
				
		# on ecrit :
		if ($v==1) echo "\nOn met à jour :\n";
		foreach($liste as $id){
			if ($v==1) echo "Mise à jour de la casquette $id            \r";
			$c= new Casquette($id);
			$ctout=$c->tout();
			$entry=Array();
			$entry['uid']=$id;
			$entry['cn']="";
			if ($ctout['contact']['nom']!='$$$$'){

				if (trim($ctout['contact']['prenom'])!="") $entry['cn'].=$ctout['contact']['prenom'];
				if (trim($entry['cn'])!="" && trim($ctout['contact']['prenom'])!="") $entry['cn'].=" ";
				if (trim($ctout['contact']['nom'])!="") $entry['cn'].=$ctout['contact']['nom'];
				if ($entry['cn']=="") $entry['cn']="(sans nom)";
				if (trim($ctout['contact']['nom'])!="") $entry['sn']=$ctout['contact']['nom'];
				else $entry['sn']="(sans nom)";
				$entry['gn']=$ctout['contact']['prenom'];
				$entry['o']=$ctout['structure']['nom'];
				$entry['mail']=isset($ctout['emails'][0]) ? $ctout['emails'][0] : "";
				$entry['telephoneNumber']= isset($ctout['donnees']['Telephone_fixe'][0]) ? $ctout['donnees']['Telephone_fixe'][0] : "";
				$entry['mobile']=isset($ctout['donnees']['Telephone_portable'][0]) ? $ctout['donnees']['Telephone_portable'][0] : "";
				$cats=$ctout['categories'];
				$categories="";
				foreach ($cats as $id_cat) {
					$cat=new Categorie($id_cat);
					if ($categories=="") $categories.=$cat->nom();
					else $categories.="; ".$cat->nom();
				}
				$entry['description']=$categories;
				$entry['description'].=isset($ctout['donnees']['Note'][0]) ? "\n".$ctout['donnees']['Note'][0] : "";
				$entry['postalAddress']=$ctout['adr'];
				$entry['postalCode']=$ctout['cp'];
				$entry['l']=$ctout['ville'];
				$entry["objectclass"][0]="top";
				$entry["objectclass"][1]="inetOrgPerson";
				$entry["objectclass"][2]="person";
				$entry["objectclass"][3]="organizationalPerson";
				
			} else {

				$entry['cn'].=$ctout['structure']['nom'];
				if ($entry['cn']=="") $entry['cn']="(sans nom)";
				$entry['sn']=$entry['cn'];
				$entry['mail']=isset($ctout['emails'][0]) ? $ctout['emails'][0] : "";
				$entry['telephoneNumber']=isset($ctout['donnees_etab']['Telephone_fixe'][0]) ? $ctout['donnees_etab']['Telephone_fixe'][0] : "";
				$entry['mobile']=isset($ctout['donnees_etab']['Telephone_portable'][0]) ? $ctout['donnees_etab']['Telephone_fixe'][0] : "";
				$cats=$ctout['categories'];
				$categories="";
				foreach ($cats as $id_cat) {
					$cat=new Categorie($id_cat);
					if ($categories=="") $categories.=$cat->nom();
					else $categories.="; ".$cat->nom();
				}
				$entry['description']=$categories;
				$entry['description'].=isset($ctout['donnees_etab']['Note'][0]) ? "\n".$ctout['donnees_etab']['Note'][0] : "";
				$entry['postalAddress']=$ctout['adr'];
				$entry['postalCode']=$ctout['cp'];
				$entry['l']=$ctout['ville'];
				$entry["objectclass"][0]="top";
				$entry["objectclass"][1]="inetOrgPerson";
				$entry["objectclass"][2]="person";
				$entry["objectclass"][3]="organizationalPerson";

			}

			#on supprime les champs vide, sinon erreur ldap
			$entry_new=Array();
			foreach ($entry as $key => $value){
					if ($value != ""){
							$entry_new[$key] = $value;
					}
			}
			// Ajout des données dans l'annuaire
			$contact="uid=$id,$ldapbase";
			$r=ldap_add($ldapconn, $contact, $entry_new);
		}
		if ($v==1) echo "\n";
			
		ldap_close($ldapconn);	
	}
}	

?>
