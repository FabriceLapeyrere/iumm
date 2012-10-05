<?php
	require('ctl/includes/tfpdf/tfpdf.php');
	define('FPDF_FONTPATH','ctl/includes/tfpdf/font/');
	class PDF extends TFPDF
	{
		var $widths;
		var $aligns;
		function NbLines($w, $txt)
		{
			//Computes the number of lines a MultiCell of width w will take
			$cw=&$this->CurrentFont['cw'];
			if($w==0)
				$w=$this->w-$this->rMargin-$this->x;
			$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			$s=str_replace("\r", '', $txt);
			$nb=strlen($s);
			if($nb>0 and $s[$nb-1]=="\n")
				$nb--;
			$sep=-1;
			$i=0;
			$j=0;
			$l=0;
			$nl=1;
			while($i<$nb)
			{
				$c=$s[$i];
				if($c=="\n")
				{
				    $i++;
				    $sep=-1;
				    $j=$i;
				    $l=0;
				    $nl++;
				    continue;
				}
				if($c==' ')
				    $sep=$i;
				$l+=$cw[$c];
				if($l>$wmax)
				{
				    if($sep==-1)
				    {
				        if($i==$j)
				            $i++;
				    }
				    else
				        $i=$sep+1;
				    $sep=-1;
				    $j=$i;
				    $l=0;
				    $nl++;
				}
				else
				    $i++;
			}
			return $nl;
		}
	}
	$id_support=$_GET['id_support'];
	$ipcase=0;
	
	$base = new SQLite3('db/publipostage.sqlite');
	$sql="SELECT * FROM supports WHERE rowid=$id_support";
	$res = $base->query($sql);
	while ($tab=$res->fetchArray(SQLITE3_ASSOC)) {
		$support=$tab;
	}
	$base->close();
	$liste=Casquettes::liste('adresse');
	$nb_enr=count($liste);
	$taille_police=12;
	$nb_lignes=$support['nb_lignes'];
	$nb_colonnes=$support['nb_colonnes'];
	$mc_gauche=$support['mc_gauche'];
	$mc_droite=$support['mc_droite'];
	$mc_haut=$support['mc_haut'];
	$mc_bas=$support['mc_bas'];
	$mp_gauche=$support['mp_gauche'];
	$mp_droite=$support['mp_droite'];
	$mp_haut=$support['mp_haut'];
	$mp_bas=$support['mp_bas'];
	$h_page=$support['h_page'];
	$l_page=$support['l_page'];
	$tpl=$support['tpl'];
	function rectangle($pdf,$x,$y,$l,$h,$id_casquette,$mc_gauche,$mc_droite,$mc_haut,$mc_bas,$taille_police,$tpl) {
		$tab=array();
		$tab['adresse']=Casquettes::adresse_cache($id_casquette);
		$pattern = "/::([^:: \n]*)::/s";
		preg_match_all($pattern, $tpl, $matches);
		foreach($matches[1] as $key=>$cle){
			$valeur="";
			if ($cle=='ADRESSE') {
				$valeur=Casquettes::adresse_cache($id_casquette);
			}
			elseif($cle=='NOM_COMPLET') {
				$c=new Casquette($id_casquette);
				$contact=$c->contact();
				if (trim($contact['prenom'])!="" and trim($contact['nom'])!="$$$$") $valeur.=trim($contact['prenom'])." ";
				if (trim($contact['prenom'])!="" and trim($contact['nom'])!="$$$$") $valeur.=$contact['nom'];
			}
			else {
				$donnees=Cache_modele::get('casquette',$id_casquette,'donnees');
				foreach($donnees as $k=>$v){
					if ($cle==$k) $valeur=$v[0];
				}
			}
			$tpl=str_replace("::".$cle."::",$valeur,$tpl);
		}
		$tpl_tab=explode("\n",$tpl);
		$adresse="";
		foreach($tpl_tab as $ligne)
			if (trim($ligne)!="") $adresse.=trim($ligne)."\n";
		$adresse=trim($adresse);
		if($adresse!="") {
			$htexte=10000;
			$hcase=$h-$mc_haut-$mc_bas;
			$taille_police=12;
			#echo "\n".$id_casquette."\n";
			#echo $c->adresse()."\n";
			#echo $taille_police."\n";
			while ($htexte>$hcase) {
				$pdf->SetFont('Arial','',$taille_police);
				$nbl=$pdf->NbLines($l - $mc_gauche - $mc_droite, $adresse);
				$htexte=$nbl*$taille_police*25.4/72;
				if($htexte>$hcase) $taille_police-=0.5;
				#echo $taille_police."\n";
			}
			$nbl=$pdf->NbLines($l - $mc_gauche - $mc_droite, $adresse);
			$h_ligne=$taille_police*25.4/72;
			$pdf->SetXY($x + $mc_gauche, $y + $h - $mc_bas - $nbl*$h_ligne);
	   		$pdf->MultiCell($l - $mc_gauche - $mc_droite, $h_ligne , $adresse, 0, 'L');
		}
		
	}
	
	if ($l_page>$h_page) {
		$pdf = new PDF('L', 'mm', array($h_page,$l_page));
	}
	else {
		$pdf = new PDF('P', 'mm', array($l_page,$h_page));
	}
	$ids=array();
	foreach($liste as $id_casquette=>$casquette) {
		$ids[]=$id_casquette;
	}
	$j=0;
	#$h=0;
	$pdf->AddFont('Arial', '', 'DejaVuSans.ttf',true);
	while ($j<$nb_enr) {
		$pdf->AddPage();
		$pdf->SetAutoPageBreak(false);
		for ($i=0;$i<$nb_lignes;$i++) {
			for ($k=0;$k<$nb_colonnes;$k++) {
				if (($k+$i*$nb_colonnes)>=$ipcase && $j<$nb_enr) {
				rectangle($pdf, $mp_gauche + $k*($l_page - $mp_gauche - $mp_droite)/$nb_colonnes, $mp_haut + $i * ($h_page - $mp_haut - $mp_bas)/$nb_lignes, ($l_page - $mp_gauche - $mp_droite)/$nb_colonnes, ($h_page - $mp_haut - $mp_bas)/$nb_lignes, $ids[$j],$mc_gauche,$mc_droite,$mc_haut,$mc_bas,$taille_police,$tpl);
				}
				$j++;
			}
		}
		$ipcase=0;
	}
	
	$nom="publipostage_".date('Ymd');
	$pdf->Output($nom.'.pdf','D');
?>
