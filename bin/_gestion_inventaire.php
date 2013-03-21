<?php // 1 > Inventaire des stocks ;

if(is_file('bin/fpdf.php') && filesize('bin/fpdf.php')>0 ){
	require('bin/fpdf.php');
}
else{
	require("$style_url/update.php?file=fpdf.php");
}
echo"<h2>Inventaire</h2>";
if(isset($fournisseurs_db) && ($u_droits == '' || $u_dgw == 1) &&  mysql_query("SHOW COLUMNS FROM $fournisseurs_db") && mysql_query("SHOW COLUMNS FROM `gestion_artfour`")){
	if( mysql_query("SHOW COLUMNS FROM gestion_multistock") && isset($shop_db) && is_array($shop_db) && sizeof($shop_db)==3 ){
			$magnom='';
			$res = mysql_query("SELECT `$shop_db[1]`,`$shop_db[2]` FROM `$shop_db[0]` ORDER BY `$shop_db[2]`");
			if($res && mysql_num_rows($res)>0){
				while($roy=mysql_fetch_array($res)){
					 $shop_list[$roy[0]]=$roy[1];
					echo"				<a href='./?option=$option&part=$part&from=$roy[0]'>$roy[1]</a> &nbsp;";
					if(isset($_GET['from']) && $roy[0]==$_GET['from']){
						$magnom = $roy[1];	
					}
				}
			}
	}
	if(isset($_GET['from']) || !mysql_query("SHOW COLUMNS FROM gestion_multistock") ){
		echo"<hr>
		<h3>$magnom</h3>
		<a href='tmp/inventaire.pdf' target='_blank'><b>Télécharger le PDF</b></a
		<table>
		<tr>
				<td>Réf.</td>
				<td>Désignation</td>
				<td>P. Achat</td>
				<td>Stock </td>
				<td align='right'>Sous total</td>
				</tr>
		";	
			
		if(isset($_GET['from']) && mysql_query("SHOW COLUMNS FROM gestion_multistock") && isset($shop_db) && is_array($shop_db) && sizeof($shop_db)==3 ){
						$res = mysql_query("SELECT 
						  		 
						  		 `gestion_artfour`.`art`,
								 `gestion_artfour`.`prix`,
								 `gestion_artfour`.`remise`,
								 `gestion_artstock`.`code` ,
								 `gestion_artfour`.`tva`,
								 
								 `gestion_artrad`.`nom`,
								 `gestion_artstock`.`taille`,
								 `gestion_artstock`.`couleur`,
								 `gestion_artstock`.`ref`,
								 
								 `gestion_multistock`.`stock`,
								 
								 `$fournisseurs_db` .`nom`,
								 `gestion_artstock`.`id`
						  FROM 
						  		`gestion_artfour` , `gestion_artrad` , `gestion_artstock`,`$fournisseurs_db` ,`gestion_multistock`,`gestion_articles`
						  WHERE 
						  		`gestion_artfour`.`art`=`gestion_artstock`.`id` 
								AND 
								`gestion_artrad`.`ref`=`gestion_artstock`.`ref` 
								AND 
								`gestion_articles`.`id`=`gestion_artstock`.`ref` 
								AND 
								`gestion_articles`.`active`=1 
								AND 
								`gestion_artstock`.`active`=1 
								AND 
								`gestion_artfour`.`prix`>0 
								AND 
								`gestion_multistock`.`magasin`='".$_GET['from']."'
								AND 
								`gestion_multistock`.`article`=`gestion_artstock`.`id` 
							GROUP BY 
								`gestion_artstock`.`id`
							ORDER BY 
								`gestion_artrad`.`nom`
						 ");
		}
		else{
						$res = mysql_query("SELECT  
						  		 `gestion_artfour`.`art`,
								 `gestion_artfour`.`prix`,
								 `gestion_artfour`.`remise`,
								 `gestion_artstock`.`code` ,
								 `gestion_artfour`.`tva`,
								 
								 `gestion_artrad`.`nom`,
								 `gestion_artstock`.`taille`,
								 `gestion_artstock`.`couleur`,
								 `gestion_artstock`.`ref`,
								 
								 `gestion_artstock`.`stock`,
								 
								 `$fournisseurs_db` .`nom`,
								 `gestion_artstock`.`id`
						  FROM 
						  		`gestion_artfour` , `gestion_artrad` , `gestion_artstock`,`$fournisseurs_db` 
						  WHERE 
						  		`gestion_artfour`.`art`=`gestion_artstock`.`id` 
								AND 
								`gestion_artrad`.`ref`=`gestion_artstock`.`ref` 
								AND 
								`gestion_artfour`.`prix`>0
							GROUP BY 
								`gestion_artfour`.`art`
							ORDER BY 
								`gestion_artrad`.`nom`
						 ");
		}
		$date = date("d/m/Y");
				$pdf = new FPDF( 'P', 'mm', 'A4' );
				$pdf->Open();
				$pdf->SetAutoPageBreak(0);
				$pdf->AddPage();
				$pdf->SetFillColor(255,255,255);
				$pdf->SetDrawColor(50,50,50);
				$pdf->SetFont('Arial','B',9);
				$pdf->SetTextColor(0, 0, 0);
				$lineh = 6;
				$linew = 200;
				$page=1;			
				$y=1;				
				$pdf->SetXY(5,$y*$lineh);
				$pdf->Cell(150,$lineh, "INVENTAIRE $magnom", 0, 2, "L", 1); 
				$pdf->SetXY(155,$y*$lineh);
				$pdf->Cell(45,$lineh, "$date PAGE $page", 0, 2, "R", 1); 
				$y++;				
				$pdf->SetXY(5,$y*$lineh);
				$pdf->Cell(35,$lineh, "REFERENCE", 1, 2, "L", 1); 
				$pdf->SetXY(40,$y*$lineh);
				$pdf->Cell(90,$lineh, "DESIGNATION", 1, 2, "L", 1); 
				$pdf->SetXY(130,$y*$lineh);
				$pdf->Cell(30,$lineh, "PRIX ACHAT", 1, 0, "R", 1); 			
				$pdf->SetXY(160,$y*$lineh);
				$pdf->Cell(20,$lineh, "STOCK", 1, 0, "C", 1); 		
				$pdf->SetXY(180,$y*$lineh);
				$pdf->Cell(20,$lineh, "TOTAL", 1, 0, "C", 1); 
				$y++;
				
		$tot=0;		
		while($ro=mysql_fetch_array($res)){
			$pht = $ro[1]-($ro[1]*$ro[2]/100);
			$ttc = $pht + ($pht*$ro[4]/100);
			
			if(isset($_GET['from']) && mysql_query("SHOW COLUMNS FROM gestion_multistock") && isset($shop_db) && is_array($shop_db) && sizeof($shop_db)==3 ){
				$ress = mysql_query("SELECT `stock` FROM `gestion_multistock` WHERE `magasin`='".$_GET['from']."'AND `article`='$ro[11]'");
				$ros = mysql_fetch_array($ress);
				$ro[8]= $ros[0];
			}
			if($ro[3]==''){
				$ro[3]=get('gestion_articles','code',$ro[11]);
			}
				echo"<tr>
				<td><a href='./?option=$option&part=gestion_articles&edit=$ro[8]'>$ro[3]</a></td>
				<td>$ro[5] $ro[6] $ro[7]</td>
				<td align='right'> <a class='info'><b>".number_format($ttc,2,',',' ')."</b><span>$ro[1] (-$ro[2]%) (TVA $ro[4]%)</span></a></td>
				<td align='center'>$ro[8] </td>
				<td align='right'>".number_format($ttc*$ro[8],2,',',' ')."</td>
				</tr>";
				$tot+=$ttc*$ro[8];
				$pdf->SetXY(5,$y*$lineh);
				$pdf->Cell(35,$lineh, "$ro[3]", 1, 2, "L", 1); 
				$pdf->SetXY(40,$y*$lineh);
				$pdf->Cell(90,$lineh, "$ro[5] $ro[6] $ro[7]", 1, 2, "L", 1); 
				$pdf->SetXY(130,$y*$lineh);
				$pdf->Cell(30,$lineh, number_format($ttc,2,',',' '), 1, 0, "R", 1); 
				$pdf->SetXY(160,$y*$lineh);
				$pdf->Cell(20,$lineh, "$ro[8]", 1, 0, "C", 1);
				$pdf->SetXY(180,$y*$lineh);
				$pdf->Cell(20,$lineh, number_format($ttc*$ro[8],2,',',' '), 1, 0, "R", 1); 
				$y++;
				if($y>45){
					$pdf->AddPage();
					$page++;
					$y=1;				
					$pdf->SetXY(5,$y*$lineh);
					$pdf->Cell(150,$lineh, "INVENTAIRE $magnom", 0, 2, "L", 1); 
					$pdf->SetXY(155,$y*$lineh);
					$pdf->Cell(45,$lineh, "$date PAGE $page", 0, 2, "R", 1); 
					$y++;				
					$pdf->SetXY(5,$y*$lineh);
					$pdf->Cell(35,$lineh, "REFERENCE", 1, 2, "L", 1); 
					$pdf->SetXY(40,$y*$lineh);
					$pdf->Cell(90,$lineh, "DESIGNATION", 1, 2, "L", 1); 
					$pdf->SetXY(130,$y*$lineh);
					$pdf->Cell(30,$lineh, "PRIX ACHAT", 1, 0, "R", 1); 			
					$pdf->SetXY(160,$y*$lineh);
					$pdf->Cell(20,$lineh, "STOCK", 1, 0, "C", 1); 		
					$pdf->SetXY(180,$y*$lineh);
					$pdf->Cell(20,$lineh, "TOTAL", 1, 0, "C", 1); 
					$y++;
				}
		}
		$pdf->Output("tmp/inventaire.pdf");
		echo"<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td align='right'>$tot</td>
		</tr>
		</table>
		<a href='tmp/inventaire.pdf' target='_blank'><b>Télécharger le PDF</b></a>";
	}
echo"</td></tr></table>";
}
else{
	echo"Droits ou configuration insufisants";
}
?>