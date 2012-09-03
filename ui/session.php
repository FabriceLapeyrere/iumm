<?php
session_start();

#variables de session edition

$binfc=0;
$binfs=0;
$motifc="";
$motifs="";
if (! isset($_SESSION['contacts'])) $_SESSION['contacts']= array();
if (isset($_SESSION['contacts']['binf'])) $binfc=$_SESSION['contacts']['binf'];
else $_SESSION['contacts']['binf'] = $binfc;
if (isset($_SESSION['contacts']['motifs'])) $motifc=$_SESSION['contacts']['motifs'];
else $_SESSION['contacts']['motifs'] = $motifc;
if (! isset($_SESSION['structures'])) $_SESSION['structures']= array();
if (isset($_SESSION['structures']['binf'])) $binfs=$_SESSION['structures']['binf'];
else $_SESSION['structures']['binf'] = $binfs;
if (isset($_SESSION['structures']['motifs'])) $motifs=$_SESSION['structures']['motifs'];
else $_SESSION['structures']['motifs'] = $motifs;

#variables de session selection
$sel_binfc=0;
$sel_binfs=0;
$sel_motifc="";
$sel_motifs="";
$sel_cats=array();
$sel_etabs=array();
$sel_Ncats=0;
$sel_Netabs=0;
$sel_motifs="";
$sel_mots="";
$sel_depts=array();
$sel_email=0;
$sel_Nemail=0;
$sel_adresse=0;
$sel_Nadresse=0;
$sel_cass=array();
$sel_cas=0;
$sel_Ncas=0;
$combinaison=array();
$scombinaison=0;
$op=1;
$N=0;
if (! isset($_SESSION['sel_structures'])) $_SESSION['sel_structures']= array();
if (isset($_SESSION['sel_structures']['binf'])) $sel_binfs=$_SESSION['sel_structures']['binf'];
else $_SESSION['sel_structures']['binf'] = $sel_binfs;
if (isset($_SESSION['sel_structures']['motifs'])) $sel_motifs=$_SESSION['sel_structures']['motifs'];
else $_SESSION['sel_structures']['motifs'] = $sel_motifs;

if (isset($_SESSION['sel_binfc'])) $sel_binfc=$_SESSION['sel_binfc'];
else $_SESSION['sel_binfc'] = $sel_binfc;
if (! isset($_SESSION['selection'])) $_SESSION['selection']= array();
if (isset($_SESSION['selection']['categories'])) $sel_cats=$_SESSION['selection']['categories'];
else $_SESSION['selection']['categories'] = $sel_cats;
if (isset($_SESSION['selection']['etablissements'])) $sel_etabs=$_SESSION['selection']['etablissements'];
else $_SESSION['selection']['etablissements'] = $sel_etabs;
if (isset($_SESSION['selection']['Ncats'])) $sel_Ncats=$_SESSION['selection']['Ncats'];
else $_SESSION['selection']['Ncats'] = $sel_Ncats;
if (isset($_SESSION['selection']['Netabs'])) $sel_Netabs=$_SESSION['selection']['Netabs'];
else $_SESSION['selection']['Netabs'] = $sel_Netabs;
if (isset($_SESSION['selection']['motifs'])) $sel_motifs=$_SESSION['selection']['motifs'];
else $_SESSION['selection']['motifs'] = $sel_motifs;
if (isset($_SESSION['selection']['mots'])) $sel_mots=$_SESSION['selection']['mots'];
else $_SESSION['selection']['mots'] = $sel_mots;
if (isset($_SESSION['selection']['depts'])) $sel_depts=$_SESSION['selection']['depts'];
else $_SESSION['selection']['depts'] = $sel_depts;
if (isset($_SESSION['selection']['email'])) $sel_email=$_SESSION['selection']['email'];
else $_SESSION['selection']['email'] = $sel_email;
if (isset($_SESSION['selection']['Nemail'])) $sel_Nemail=$_SESSION['selection']['Nemail'];
else $_SESSION['selection']['Nemail'] = $sel_Nemail;
if (isset($_SESSION['selection']['adresse'])) $sel_adresse=$_SESSION['selection']['adresse'];
else $_SESSION['selection']['adresse'] = $sel_adresse;
if (isset($_SESSION['selection']['Nadresse'])) $sel_Nadresse=$_SESSION['selection']['Nadresse'];
else $_SESSION['selection']['Nadresse'] = $sel_Nadresse;
if (isset($_SESSION['selection']['casquettes'])) $sel_cas=$_SESSION['selection']['casquettes'];
else $_SESSION['selection']['casquettes'] = $sel_cass;
if (isset($_SESSION['selection']['cas'])) $sel_cas=$_SESSION['selection']['cas'];
else $_SESSION['selection']['cas'] = $sel_cas;
if (isset($_SESSION['selection']['Ncas'])) $sel_Ncas=$_SESSION['selection']['Ncas'];
else $_SESSION['selection']['Ncas'] = $sel_Ncas;
if (isset($_SESSION['combinaison'])) $combinaison=$_SESSION['combinaison'];
else $_SESSION['combinaison'] = $combinaison;
if (isset($_SESSION['op'])) $op=$_SESSION['op'];
else $_SESSION['op'] = $op;
if (isset($_SESSION['N'])) $N=$_SESSION['N'];
else $_SESSION['N'] = $N;
if (isset($_SESSION['scombinaison'])) $scombinaison=$_SESSION['scombinaison'];
else $_SESSION['scombinaison'] = $scombinaison;

#variables de session email

$binfe=0;
$motifse='';

if (! isset($_SESSION['email'])) $_SESSION['email']= array();
if (isset($_SESSION['email']['binf'])) $binfe=$_SESSION['email']['binf'];
else $_SESSION['email']['binf'] = $binfe;
if (isset($_SESSION['email']['motifs'])) $motifse=$_SESSION['email']['motifs'];
else $_SESSION['email']['motifs'] = $motifse;

#variables de session newsletter

$binfn=0;
$motifsn='';

if (! isset($_SESSION['news'])) $_SESSION['email']= array();
if (isset($_SESSION['news']['binf'])) $binfn=$_SESSION['email']['binf'];
else $_SESSION['news']['binf'] = $binfn;
if (isset($_SESSION['news']['motifs'])) $motifsn=$_SESSION['email']['motifs'];
else $_SESSION['news']['motifs'] = $motifsn;

#variables de session emailing

$binfemailing=0;
$binfemailingm=0;
$motifsemailing='';

if (! isset($_SESSION['emailing'])) $_SESSION['emailing']= array();
if (isset($_SESSION['emailing']['binfm'])) $binfemailingm=$_SESSION['emailing']['binfm'];
else $_SESSION['emailing']['binfm'] = $binfemailingm;
if (isset($_SESSION['emailing']['binf'])) $binfemailing=$_SESSION['emailing']['binf'];
else $_SESSION['emailing']['binf'] = $binfemailing;
if (isset($_SESSION['emailing']['motifs'])) $motifsemailing=$_SESSION['emailing']['motifs'];
else $_SESSION['emailing']['motifs'] = $motifsemailing;

#variables de session publipostage

$binfpublipostage=0;
$motifspublipostage='';

if (! isset($_SESSION['publipostage'])) $_SESSION['publipostage']= array();
if (isset($_SESSION['publipostage']['binf'])) $binfpublipostage=$_SESSION['publipostage']['binf'];
else $_SESSION['publipostage']['binf'] = $binfpublipostage;
if (isset($_SESSION['publipostage']['motifs'])) $motifspublipostage=$_SESSION['publipostage']['motifs'];
else $_SESSION['publipostage']['motifs'] = $motifspublipostage;

#variables de session admin

$binfadmin=0;
$motifsadmin='';

if (! isset($_SESSION['admin'])) $_SESSION['admin']= array();
if (isset($_SESSION['admin']['binf'])) $binfadmin=$_SESSION['admin']['binf'];
else $_SESSION['admin']['binf'] = $binfadmin;
if (isset($_SESSION['admin']['motifs'])) $motifsadmin=$_SESSION['admin']['motifs'];
else $_SESSION['admin']['motifs'] = $motifsadmin;


?>
