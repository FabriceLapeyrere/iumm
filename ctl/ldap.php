<?php
include 'conf/ldap.php';	
/**
* Escapes an LDAP AttributeValue
*/
function ldap_escape($string)
{
    return stripslashes($string);
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
	
		# on ecrit :
		foreach($liste as $id){
			$c= new Casquette($id);
			$ctout=$c->tout();
			if ($v==1) echo "Mise à jour de la casquette $id            \r";
			$entry=Array();
			$entry['uid']=$id;
			$entry['cn']="";
			if ($ctout['contact']['nom']!='$$$$'){

				if (trim($ctout['contact']['prenom'])!="") $entry['cn'].=ldap_escape($ctout['contact']['prenom']);
				if (trim($entry['cn'])!="" && trim($ctout['contact']['prenom'])!="") $entry['cn'].=" ";
				if (trim($ctout['contact']['nom'])!="") $entry['cn'].=ldap_escape($ctout['contact']['nom']);
				if ($entry['cn']=="") $entry['cn']=ldap_escape("(sans nom)");
				if (trim($ctout['contact']['nom'])!="") $entry['sn']=ldap_escape($ctout['contact']['nom']);
				else $entry['sn']=ldap_escape("(sans nom)");
				$entry['gn']=ldap_escape($ctout['contact']['prenom']);
				$entry['o']=ldap_escape($ctout['structure']['nom']);
				$entry['mail']=isset($ctout['emails'][0]) ? ldap_escape($ctout['emails'][0]) : "";
				$entry['telephoneNumber']= isset($ctout['donnees']['Telephone_fixe'][0]) ? ldap_escape($ctout['donnees']['Telephone_fixe'][0]) : "";
				$entry['mobile']=isset($ctout['donnees']['Telephone_portable'][0]) ? ldap_escape($ctout['donnees']['Telephone_portable'][0]) : "";
				$cats=$ctout['categories'];
				$categories="";
				foreach ($cats as $id_cat) {
					$cat=new Categorie($id_cat);
					if ($categories=="") $categories.=ldap_escape($cat->nom());
					else $categories.="; ".ldap_escape($cat->nom());
				}
				$entry['description']=$categories;
				$entry['description'].=isset($ctout['donnees']['Note'][0]) ? "\n".ldap_escape($ctout['donnees']['Note'][0]) : "";
				$entry['postalAddress']=ldap_escape($ctout['adr']);
				$entry['postalCode']=ldap_escape($ctout['cp']);
				$entry['l']=ldap_escape($ctout['ville']);
				$entry["objectclass"][0]="top";
				$entry["objectclass"][1]="inetOrgPerson";
				$entry["objectclass"][2]="person";
				$entry["objectclass"][3]="organizationalPerson";
				
			} else {

				$entry['cn'].=ldap_escape($ctout['structure']['nom']);
				if ($entry['cn']=="") $entry['cn']=ldap_escape("(sans nom)");
				$entry['sn']=ldap_escape($entry['cn']);
				$entry['mail']=isset($ctout['emails'][0]) ? ldap_escape($ctout['emails'][0]) : "";
				$entry['telephoneNumber']=isset($ctout['donnees_etab']['Telephone_fixe'][0]) ? ldap_escape($ctout['donnees_etab']['Telephone_fixe'][0]) : "";
				$entry['mobile']=isset($ctout['donnees_etab']['Telephone_portable'][0]) ? ldap_escape($ctout['donnees_etab']['Telephone_portable'][0]) : "";
				$cats=$ctout['categories'];
				$categories="";
				foreach ($cats as $id_cat) {
					$cat=new Categorie($id_cat);
					if ($categories=="") $categories.=ldap_escape($cat->nom());
					else $categories.="; ".ldap_escape($cat->nom());
				}
				$entry['description']=ldap_escape($categories);
				$entry['description'].=isset($ctout['donnees_etab']['Note'][0]) ? "\n".ldap_escape($ctout['donnees_etab']['Note'][0]) : "";
				$entry['postalAddress']=ldap_escape($ctout['adr']);
				$entry['postalCode']=ldap_escape($ctout['cp']);
				$entry['l']=ldap_escape($ctout['ville']);
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
			@ldap_delete($ldapconn,$contact);
			$r=ldap_add($ldapconn, $contact, $entry_new);
		}
		if ($v==1) echo "\n";
			
		ldap_close($ldapconn);	
	}
}	

?>
