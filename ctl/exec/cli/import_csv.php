<?php
define('CLASS_DIR', 'modele/');
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();

$fichier=$argv[2];
$cats=$argv[3];

$categories=explode(',',$cats);

function VerifierAdresseMail($adresse)  
{  
   $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';  
   if(preg_match($Syntaxe,$adresse))  
      return true;  
   else  
     return false;  
}
$f=explode("\n",file_get_contents($fichier));
$emails=array();
foreach($f as $l){
	if(VerifierAdresseMail(trim($l)))
		$emails[]=trim($l);
}
foreach($emails as $email){
	$id=Contacts::aj_contact('','');	
	$c=new Contact($id);
	foreach($c->casquettes() as $id_casquette){
		$cas=new Casquette($id_casquette);
		$cas->aj_donnee('Email', 'E-mail', 'email', $email);
		foreach($categories as $cat){
			$cas->ass_categorie($cat);
		}
		$cas->tout();
		Cache::set_obsolete('casquette', $id_casquette);
		Cache::set_obsolete('casquette_sel', $id_casquette);
		Html::casquette($id_casquette);
		Html::casquette_selection($id_casquette);
		Index::update($id_casquette);
	}
	echo "Email $email inséré !\n";		
}

foreach($categories as $cat){
	Cache::set_obsolete('ed_categorie', $cat);
	Cache::set_obsolete('sel_categorie', $cat);
}
echo "terminé\n";
?>
