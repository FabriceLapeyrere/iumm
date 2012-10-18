<?php
	require('ctl/includes/tfpdf/tfpdf.php');
	define('FPDF_FONTPATH','ctl/includes/tfpdf/font/');
	class PDF extends TFPDF
	{
		var $widths;
		var $aligns;
		function NbLines($w, $h, $txt, $border=0, $align='J', $fill=false)
		{
			// Output text with automatic or explicit line breaks
			$cw = &$this->CurrentFont['cw'];
			if($w==0)
				$w = $this->w-$this->rMargin-$this->x;
			$wmax = ($w-2*$this->cMargin);
			$s = str_replace("\r",'',$txt);
			if ($this->unifontSubset) {
				$nb=mb_strlen($s, 'utf-8');
				while($nb>0 && mb_substr($s,$nb-1,1,'utf-8')=="\n")	$nb--;
			}
			else {
				$nb = strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
			}
			$b = 0;
			if($border)
			{
				if($border==1)
				{
					$border = 'LTRB';
					$b = 'LRT';
					$b2 = 'LR';
				}
				else
				{
					$b2 = '';
					if(strpos($border,'L')!==false)
						$b2 .= 'L';
					if(strpos($border,'R')!==false)
						$b2 .= 'R';
					$b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
				}
			}
			$sep = -1;
			$i = 0;
			$j = 0;
			$l = 0;
			$ns = 0;
			$nl = 1;
			while($i<$nb)
			{
				// Get next character
				if ($this->unifontSubset) {
					$c = mb_substr($s,$i,1,'UTF-8');
				}
				else {
					$c=$s[$i];
				}
				if($c=="\n")
				{
					// Explicit line break
					if($this->ws>0)
					{
						$this->ws = 0;
						$this->_out('0 Tw');
					}
					$i++;
					$sep = -1;
					$j = $i;
					$l = 0;
					$ns = 0;
					$nl++;
					if($border && $nl==2)
						$b = $b2;
					continue;
				}
				if($c==' ')
				{
					$sep = $i;
					$ls = $l;
					$ns++;
				}

				if ($this->unifontSubset) { $l += $this->GetStringWidth($c); }
				else { $l += $cw[$c]*$this->FontSize/1000; }

				if($l>$wmax)
				{
					// Automatic line break
					if($sep==-1)
					{
						if($i==$j)
							$i++;
						if($this->ws>0)
						{
							$this->ws = 0;
							$this->_out('0 Tw');
						}
					}
					else
					{
						if($align=='J')
						{
							$this->ws = ($ns>1) ? ($wmax-$ls)/($ns-1) : 0;
							$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
						}
						$i = $sep+1;
					}
					$sep = -1;
					$j = $i;
					$l = 0;
					$ns = 0;
					$nl++;
					if($border && $nl==2)
						$b = $b2;
				}
				else
					$i++;
			}
			// Last chunk
			if($this->ws>0)
			{
				$this->ws = 0;
				$this->_out('0 Tw');
			}
			if($border && strpos($border,'B')!==false)
				$b .= 'B';
			$this->x = $this->lMargin;
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
	$police=12;
	if ($support['police']!=0)
		$police=$support['police'];
	$tpl=$support['tpl'];
	function rectangle($pdf,$x,$y,$l,$h,$id_casquette,$mc_gauche,$mc_droite,$mc_haut,$mc_bas,$police,$tpl) {
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
				if (trim($contact['nom'])!="$$$$") $valeur.=trim($contact['prenom']." ".$contact['nom'])." ";
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
			$taille_police=$police;
			#echo "\n".$id_casquette."\n";
			#echo $c->adresse()."\n";
			#echo $taille_police."\n";
			while ($htexte>$hcase) {
				$pdf->SetFont('Arial','',$taille_police);
				$h_ligne=$taille_police*25.4/72;
				$nbl=$pdf->NbLines($l - $mc_gauche - $mc_droite, $h_ligne ,$adresse, 0, 'L');
				$htexte=$nbl*$taille_police*25.4/72;
				if($htexte>$hcase) $taille_police-=0.5;
				#echo $taille_police."\n";
			}
			$nbl=$pdf->NbLines($l - $mc_gauche - $mc_droite, $h_ligne ,$adresse, 0, 'L');
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
				rectangle($pdf, $mp_gauche + $k*($l_page - $mp_gauche - $mp_droite)/$nb_colonnes, $mp_haut + $i * ($h_page - $mp_haut - $mp_bas)/$nb_lignes, ($l_page - $mp_gauche - $mp_droite)/$nb_colonnes, ($h_page - $mp_haut - $mp_bas)/$nb_lignes, $ids[$j],$mc_gauche,$mc_droite,$mc_haut,$mc_bas,$police,$tpl);
				}
				$j++;
			}
		}
		$ipcase=0;
	}
	
	$nom="publipostage_".date('Ymd');
	$pdf->Output($nom.'.pdf','D');
?>
