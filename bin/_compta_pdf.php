<?php // 106 > visionneuse de documents Compta ;
if(isset($_GET['mkpdf']) || isset($_GET['mkbdl']) || isset($conn)){ 

if(!isset($conn)){
	function connecte($base, $host, $login, $passe) {
		$conn = mysql_connect($host, $login, $passe);
		mysql_select_db($base);
		return $conn;
	}
	
	$conn = connecte($_SESSION['pdf_base'], $_SESSION['pdf_$host'], $_SESSION['pdf_$login'], $_SESSION['pdf_$passe']);
	$compta_base = $_SESSION['compta_base'];

	if(is_file('bin/fpdf.php') && filesize('bin/fpdf.php')>0 ){
		require('bin/fpdf.php');
	}
	else{
		require("$style_url/update.php?file=fpdf.php");
	}
}
else{
	if(is_file('fpdf.php') && filesize('fpdf.php')>0 ){
		require('fpdf.php');
	}
	else{
		require("$style_url/update.php?file=fpdf.php");
	}
}
require_once('mconfig/adeli.php');
if(is_file('inc_func.php')){
	require_once('inc_func.php');
}
elseif(is_file('bin/inc_func.php')){
	require_once('bin/inc_func.php');
}
define('FPDF_FONTPATH','http://www.adeli.wac.fr/libs/fpdf/font/');
$pat='.';
$na = strtolower(substr(strrchr(getenv("SCRIPT_NAME"),'/'),1));
if($na=='_compta_pdf.php' || $na=='_transfert.php'){
	$pat='..';
}	
if(isset($_GET['mkpdf']) || isset($fid) ){
	
	$pdf = new FACTURE( 'P', 'mm', 'A4' );
	$pdf->Open();
	$pdf->SetTitle("$type"."_$code$edit");
	$pdf->SetAutoPageBreak(0);
	eval(get_pref("compta.conf","x"));
	fclose($fp);
	$entete_img="";
	if(is_file("$pat/mconfig/compta.jpg")){
		$entete_img = "$pat/mconfig/compta.jpg";
	}
	if(!isset($taxe) || $taxe==''){
		$taxe='HT';
	}
	$edits = explode(',',$_GET['mkpdf'].',');
	if(isset($fid)) $edits = explode(',',$fid.',');
	$ispages=false;
	if(sizeof(edits)==2)  $ispages=true;
	foreach($edits as $edit){
		if($edit!=''){
			
			$page=1;
			$result = mysql_query("SELECT * FROM $compta_base WHERE id='$edit'");
			$row = mysql_fetch_object($result);
			$pdfact=array();
			$uid = $edit;
			$numero = $row->numero;
			$adresse = $row->adresse;
			$clientfacture = $row->client;
			$type = $row->type;
			$code = $row->code;
			$intitule=$row->intitule;
			$date=$row->date;
			$acompte=$row->acompte;
			$acomptele = $row->acomptele;
			$etat = $row->etat;
			$mode = $row->mode;
			$devise = $row->devise;
			$content = explode("<!>",$row->content);
			
			if($type=='achat'){
				$defstat=$defstatl;
				$colorstatut=$colorstatutl;
			}
			
			if($devise=='')$devise='EUR';
			$i=1;
			if(ereg("Livraison",$adresse)){
				$adresse = substr($adresse,0,strpos($adresse,"Livraison"));
			}
			if(ereg("livraison",$adresse)){
				$adresse = substr($adresse,0,strpos($adresse,"livraison"));
			}
			while($i < sizeof($content) ){
				$lignecontent = explode("<>",$content[$i]);
				$pdfact[$i-1]["label"] = $lignecontent[0];
				$pdfact[$i-1]["type"] = $lignecontent[1];
				$pdfact[$i-1]["pu"] = $lignecontent[2];
				$pdfact[$i-1]["tva"] = $lignecontent[3];
				$pdfact[$i-1]["quant"] = $lignecontent[4];
				$pdfact[$i-1]["libre"] = $lignecontent[5];
				$pdfact[$i-1]["coderef"] = $lignecontent[6];
				$pdfact[$i-1]["hidden"] = $lignecontent[7];
				$pdfact[$i-1]["remise"] = abs($lignecontent[8]);
				$i++;
			}
			
			$keyfact=array_keys($pdfact);
			$pdf->ajout($nom,$url,$url_cgv,$reference,$clientfacture,$date,$type,$code.' '.$numero,$adresse,$intitule,$entete_img,$taxe,false,$page,$ispages,$devise);
			$_SESSION['y']=97;
			$tot=0;
			$tst=0;
			$ttc=0;
			$ttva=array();
			$total=0;
			for($i=0 ; $i<sizeof($pdfact) ; $i++){
				$idlinefact = $keyfact[$i];		
				$label = $pdfact[$idlinefact]["label"];
				$typo = $pdfact[$idlinefact]["type"];
				$tva = number_format($pdfact[$idlinefact]["tva"],2, '.', '');
				$pu = number_format($pdfact[$idlinefact]["pu"],2, '.', '');
				$quant = $pdfact[$idlinefact]["quant"];		
				$libre = $pdfact[$idlinefact]["libre"];		
				$coderef = $pdfact[$idlinefact]["coderef"];	
				$remise = $pdfact[$idlinefact]["remise"];		
				$pt=0;
				if($typo==="titre"){
					$pdf->titre($_SESSION['y'],$label);
				}
				elseif($typo==="comment"){
					$pdf->comment($_SESSION['y'],$label);
				}
				else{
					$pdf->ligne($_SESSION['y'],$label,$pu,$quant,$tva,$libre,$coderef,$remise);
					//$pt = number_format(($pu*$quant)*(100-$remise)/100,2, '.', '');
					if($remise_app==1) $pt =  round($pu*(100-$remise)/100,2)*$quant; //$pu*$quant-round($pu*$quant*$remise/100,2);
					else $pt = $pu*$quant-($pu*$quant*$remise/100);
					$pt = number_format( $pt ,2, '.', '');
					if($tva>0){
						if($taxe=='HT'){
							$ptva = round($pt*$tva/100,2);
							$ttva[$tva]+=$ptva;
							$ttc += $pt+$ptva;
							$tot+=$pt;
							$total+=$pt+$ptva;					
						}
						elseif($taxe=='TTC'){
							$ptva = round($pt-($pt/(1+($tva/100))),2);
							$ttva[$tva]+=$ptva;
							$ttc += $pt;
							$tot+=$pt-$ptva;					
							$total+=$pt;
						}
						else{
							$ptva = round($pt-($pt/(1+($tva/100))),2);
							$ttva['erreur de paramétrage TVA']=$ptva;
							$ttc += $pt;
							$tot+=$pt-$ptva;					
							$total+=$pt;
						}
					}
					else{
						$tst+=$pt;
						$total+=$pt;
					}
				}		
				if($_SESSION['y']>=240){
					$page++;
					$pdf->comment($_SESSION['y'],"Suite page suivante >>");
					$pdf->ajout2($nom,$url,$url_cgv,$reference,$clientfacture,$date,$type,$code.' '.$numero,$adresse,$intitule,$entete_img,$taxe,false,$page,$ispages,$devise);
					$_SESSION['y']=37;
				}
			}
			$insert = "\n\n".${"note_$type"};
			//$hauteur = substr_count($insert,"\n")*8;
			if($_SESSION['y']+$hauteur>=205){
				$page++;
				$pdf->comment($_SESSION['y'],"Suite page suivante >>");
				$pdf->ajout2($nom,$url,$url_cgv,$reference,$clientfacture,$date,$type,$code.' '.$numero,$adresse,$intitule,$entete_img,$taxe,false,$page,$ispages,$devise);
				$_SESSION['y']=37;
			}
			
			$pdf->comment($_SESSION['y'],$insert);
			
			/*$pdf->SetFont('Arial','',11);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetDrawColor(255,255,255);
			$pdf->Cell(102,35,'', 1,0, "L", 1);
			$pdf->SetXY(8,160);
			$pdf->MultiCell(100,4,$insert, 0, "L", 0); */
			
			$y=200;
			 if($bordereau=='sans'){
			  	$y=250;
		  	 }
			
			
	/////////////////////// TOTAUX
			$pdf->SetXY(118,$y-3);
			$pdf->SetFillColor(255,255,255);
			$pdf->Cell(92,30, '', 0, 2, 'L', 1); 
			
			
		
			$ttvap='';
			foreach($ttva as $tk=>$tv){
				$ttvap.="$tk% : ".number_format($tv,2, ',', '')."\n";
			}
			$pdf->SetDrawColor(230,230,230);
			
			
			$pdf->SetXY(120,$y);
			$pdf->Cell(90,1,'', 'B',0, "L", 0);
			$pdf->SetFont('Arial','B',9);
			$pdf->SetX(150);
			if($taxe=='HT'){
				$pdf->Cell(30,8,'Hors Taxes', 0,0, "L", 0);
				$pdf->SetX(180);
				$pdf->Cell(20,8,number_format($tot,2, ',', ''), 0,0, "R", 0);
				
				$pdf->SetXY(120,$y+6);
				$pdf->Cell(90,1,'', 'B',0, "L", 0);
				$pdf->SetFont('Arial','B',9);
				$pdf->SetX(150);
				$pdf->Cell(30,8,'TVA', 0,0, "L", 0);
			}
			else{
				$pdf->Cell(30,8,'TTC', 0,0, "L", 0);
				$pdf->SetX(180);
				$pdf->Cell(20,8,number_format($ttc,2, ',', ''), 0,0, "R", 0);
				
				$pdf->SetXY(120,$y+6);
				$pdf->Cell(90,1,'', 'B',0, "L", 0);
				$pdf->SetFont('Arial','B',9);
				$pdf->SetX(150);
				$pdf->Cell(30,8,'dont TVA', 0,0, "L", 0);
			}
			$pdf->SetFont('Arial','B',8);
			$pdf->SetXY(150,$y+8);
			$pdf->MultiCell(50,3,$ttvap, 0, "R", 0);
			$pdf->SetFont('Arial','B',9);
			if($tst>0){
				$pdf->SetXY(120,$y+12);
				$pdf->Cell(90,1,'', 'B',0, "L", 0);
				$pdf->SetFont('Arial','B',9);
				$pdf->SetX(150);
				$pdf->Cell(30,8,'Non assujetti', 0,0, "L", 0);
				$pdf->SetX(180);
				$pdf->Cell(20,8,number_format($tst,2, ',', ''), 0,0, "R", 0);
			}
			$pdf->SetXY(120,$y+18);
			$pdf->Cell(90,1,'', 'B',0, "L", 0);
			$pdf->SetFont('Arial','B',9);
			$pdf->SetX(150);
			$pdf->Cell(30,8,'TOTAL', 0,0, "L", 0);
			$pdf->SetX(180);
			$pdf->Cell(20,8,number_format($total,2, ',', ''), 0,0, "R", 0);
			
			$pdf->SetFont('Arial','B',9);
			if($acompte>0){
				
				$pdf->SetXY(120,$y+22);	
				$pdf->Cell(30,8,'Acompte versé : '.$acomptele, 0,0, "L", 0);
				$pdf->SetX(180);
				$pdf->Cell(20,8,'-'.number_format($acompte,2, ',', ''), 0,0, "R", 0);
				$total -= $acompte;
			}
			$pdf->SetXY(120,$y+28);
			$pdf->Cell(90,1,'', 'B',0, "L", 0);
			$pdf->SetFont('Arial','B',9);
			$pdf->SetX(150);
			$pdf->Cell(30,8,'TOTAL', 0,0, "L", 0);
			$pdf->SetX(170);
			$pdf->SetFont('Arial','B',13);
			$pdf->Cell(30,8,number_format($total,2, ',', '').' '.$devise, 0,0, "R", 0);
			
			$pdf->SetTextColor(100, 100, 100);
			$pdf->Rotate(45,120,$y+15);
			$cmode = explode('<>',$mode);			
			$pdf->SetXY(120,$y+15);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(30,8,strtoupper($defstat[$etat]), 0,0, "L", 0);
			$pdf->Rotate(0);
			$pdf->SetTextColor(1, 1, 1);
			
			
		 
		 if($bordereau==''){
		  //////////////////// BORDEREAU
			$pdf->SetDrawColor(200,200,200);
			$pdf->SetFillColor(255,255,255);
			$pdf->Rect(0,200,110,77,'F'); 
			$pdf->Line(0,200,209,200);	
			
			$pdf->SetXY(5,205);
			$pdf->SetTextColor(150, 150, 150);
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(150,6,'TALON DE PAIEMENT', 0,0, "L", 0);
			
			if(is_file("$pat/mconfig/compta_t.jpg")){
				$pdf->Image("$pat/mconfig/compta_t.jpg",5,235,80);
			}
			
			$pdf->SetFont('Arial','',11);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetDrawColor(255,255,255);
			//$pdf->Cell(102,35,'', 1,0, "L", 1);
			$pdf->SetXY(8,215);
			$pdf->MultiCell(100,4,"$type : $code $numero\ndate : ".date("d/m/Y",strtotime($date))."\nmontant : ".number_format($total,2, ',', '').' '.$devise, 0, "L", 0); 
			
			$pdf->SetXY(105,235);
			$pdf->SetFont('Arial','',13);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetDrawColor(200,200,200);
			$pdf->Cell(105,35,'', 1,0, "L", 1);
			$pdf->SetXY(112,240);
			$pdf->MultiCell(100,5,$pay_adresse, 0, "L", 0); 
		  }			
			
			$pdf->AliasNbPages();
			
			if(sizeof($edits)>2){
				$page = $pdf->PageNo();
				if($page%2!=0){
					$pdf->AddPage();
				}
			}
		}
	}
	//@ob_end_clean();
	//$pdf->Output("$type"."_$code$edit.pdf",true);
	$fich = "$pat/tmp/compta.pdf";
	//echo $fich;
	$pdf->Output($fich);//
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Type: application/pdf");
	header('Content-Disposition: attachment; filename="'.$type.'_'.$code.$numero.'.pdf"' );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($fich));
	readfile($fich);
	unlink($fich);
	exit();
}
///////////////////////////////////////////////////////////////////////////////////////// BON DE LIVRAISON
elseif(isset($_GET['mkbdl'])){
	$edit=$_GET['mkbdl'];
	$ispages=true;
	$result = mysql_query("SELECT * FROM $compta_base WHERE id='$edit'");
	$row = mysql_fetch_object($result);
	$pdfact=array();
	$uid = $edit;
	$numero = $row->numero;
	$adresse = $row->adresse;
	$clientfacture = $row->client;
	$type = $row->type;
	$code = $row->code;
	$intitule=$row->intitule;
	$date=$row->date;
	$acompte=$row->acompte;
	$acomptele = $row->acomptele;
	$etat = $row->etat;
	$mode = $row->mode;
	$content = explode("<!>",$row->content);
	$i=1;
	if(ereg("Livraison",$adresse)){
		$adresse = trim(substr($adresse,strpos($adresse,"Livraison")+11,strlen($adresse)));
	}
	if(ereg("livraison",$adresse)){
		$adresse = trim(substr($adresse,strpos($adresse,"livraison")+11,strlen($adresse)));
	}
	while($i < sizeof($content) ){
		$lignecontent = explode("<>",$content[$i]);
		$pdfact[$i-1]["label"] = $lignecontent[0];
		$pdfact[$i-1]["type"] = $lignecontent[1];
		$pdfact[$i-1]["pu"] = $lignecontent[2];
		$pdfact[$i-1]["tva"] = $lignecontent[3];
		$pdfact[$i-1]["quant"] = $lignecontent[4];
		$pdfact[$i-1]["libre"] = $lignecontent[5];
		$pdfact[$i-1]["coderef"] = $lignecontent[6];
		$i++;
	}
	eval(get_pref("compta.conf","x"));
	$entete_img="";
	if(is_file("$pat/mconfig/compta.jpg")){
		$entete_img = "$pat/mconfig/compta.jpg";
	}
	if(!isset($taxe)){
		$taxe='HT';
	}
	$keyfact=array_keys($pdfact);
	$pdf = new FACTURE( 'P', 'mm', 'A4' );
	$pdf->Open();
	$page=1;
	$pdf->SetTitle("$type"."_$code$edit");
	$pdf->SetAutoPageBreak(0);
	$pdf->ajout($nom,$url,$url_cgv,$reference,$clientfacture,$date,"BON DE LIVRAISON",$code.' '.$numero,$adresse,$intitule,$entete_img,$taxe,true,$page,$ispages);
	$_SESSION['y']=97;
	$tot=0;
	$ttc=0;
	for($i=0 ; $i<sizeof($pdfact) ; $i++){
		$idlinefact = $keyfact[$i];		
		$label = $pdfact[$idlinefact]["label"];
		$typo = $pdfact[$idlinefact]["type"];
		$tva = number_format($pdfact[$idlinefact]["tva"],2, '.', '');
		$pu = number_format($pdfact[$idlinefact]["pu"],2, '.', '');
		$quant = $pdfact[$idlinefact]["quant"];		
		$libre = $pdfact[$idlinefact]["libre"];		
		$coderef = $pdfact[$idlinefact]["coderef"];		
		$pt=0;
		if($typo==="titre"){
			$pdf->titre($_SESSION['y'],$label);
		}
		else{
			//$pdf->comment($_SESSION['y'],$label."\n\nx".$quant);
			$pdf->lignel($_SESSION['y'],$label,$quant,$libre,$coderef);
		}

		if($_SESSION['y']>=245){
			$page++;
			$pdf->ajout($nom,$url,$url_cgv,$reference,$clientfacture,$date,"BON DE LIVRAISON",$code.' '.$numero,$adresse,$intitule,$entete_img,$taxe,true,$page,$ispages);
			$_SESSION['y']=97;
		}
	}
	$pdf->Line(120,250,210,250);
	$pdf->SetFont('Arial','B',9);
	$pdf->SetXY(120,250);
	$pdf->Cell(50,8,'Cachet / signature du destinataire :', 0,0, "L", 0);

	

	
	$pdf->AliasNbPages();
	//@ob_end_clean();
	//$pdf->Output("$type"."_$code$edit.pdf",true);
	$fich = "$pat/tmp/compta.pdf";
	//echo $fich;
	$pdf->Output($fich);//
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Type: application/pdf");
	header("Content-Disposition: inline; filename=$type"."_$code$edit.pdf;" );
	//header("Content-Disposition: inline;" );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($fich));
	readfile($fich);
	unlink($fich);
	exit();
}
elseif(isset($_GET['mkfh'])){
	$edit=$_GET['mkfh'];	
	$result = mysql_query("SELECT * FROM $compta_base WHERE id='$edit'");
	$row = mysql_fetch_object($result);
	$pdfact=array();
	$uid = $edit;
	$adresse = $row->adresse;
	$numero = $row->numero;
	$clientfacture = $row->client;
	$type = $row->type;
	$code = $row->code;
	$intitule=$row->intitule;
	$date=$row->date;
	$acompte=$row->acompte;
	$acomptele = $row->acomptele;
	$etat = $row->etat;
	$mode = $row->mode;
	$content = split("<!>",$row->content);
	$i=1;
	while($i < sizeof($content) ){
		$lignecontent = split("<>",$content[$i]);
		$pdfact[$i-1]["label"] = $lignecontent[0];
		$pdfact[$i-1]["type"] = $lignecontent[1];
		$pdfact[$i-1]["pu"] = $lignecontent[2];
		$pdfact[$i-1]["tva"] = $lignecontent[3];
		$pdfact[$i-1]["quant"] = $lignecontent[4];
		$i++;
	}
	eval(get_pref("compta.conf","x"));
	$entete_img="";
	if(is_file("$pat/mconfig/compta.jpg")){
		$entete_img = "$pat/mconfig/compta.jpg";
	}
	if(!isset($taxe)){
		$taxe='HT';
	}
	$keyfact=array_keys($pdfact);
	
	return("facture HTML<br>en cours de développement");
}
}
else{
	echo"paramètre manquant";
}
?>