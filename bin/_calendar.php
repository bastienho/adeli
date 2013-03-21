<?php // 10 > Calendrier ;
session_name("adeli");
session_start();

$x_id = $_SESSION['x_id'];
$vers = $_SESSION['vers'];
$theme = $_SESSION['theme'];

$cible = $_GET["cible"];
$type = $_GET["type"];
$date = $_GET["date"];

if($date == "0000-00-00" && $type=='date'){
	$date = date("Y-m-d");
}
elseif($date == "00:00:00" && $type=='time'){
	$date = date("H:i:s");
}
elseif($date == "0000-00-00 00:00:00" || $date==NULL || $date==''){
	$date = date("Y-m-d H:i:s");
}
include("mconfig/adeli.php");
	if(!isset($style_url)) $style_url="http://www.adeli.wac.fr/vers/$vers";
 $nb=0;
 $amettre = substr($date,0,10);
     $Aff=1;
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
	<html>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
		<title>adeli ($parto)</title>
		<link rel='stylesheet' href='$style_url/$theme/style.css' type='text/css'>
		<link rel='icon' href='$style_url/favicon.png' type='image/x-icon'>
		<link rel='shortcut icon' href='$style_url/favicon.png' type='image/x-icon'>
		<script language='javascript' type='text/javascript'>";
			
			if($type=="datetime"){
					echo"ametrre= '$amettre';";	
			}
			else{
					echo"ametrre= '';";	
			}		
			echo"function fejour(koi){
				ametrre = koi;";				
				if($type=="date"){
					echo"parent.document.$cible.value=koi;
					parent.document.getElementById('menu_date').style.visibility='hidden';";	
				}
				if($type=="datetime"){
					echo"parent.document.$cible.value=koi+'T'+document.montre.heure.value+':'+document.montre.minute.value+':'+document.montre.seconde.value;";	
				}									
			echo"}
			
		</script>
	</head>
	<body class=\"bando\">
	<center>
	<img src=\"$style_url/$theme/$type.png\" border='none' alt='$type'>
	<a href='about:blank' onclick=\"javascript:parent.document.getElementById('menu_date').style.visibility='hidden';\">fermer</a>";	 
///////////////////////////////////////// JOUR	 
if(ereg("date",$type)){

	 	$golbaldate = explode("-",substr($date,0,10));
		$Annee = abs($golbaldate[0]);
		$Mois = abs($golbaldate[1]);
		$Jour = abs($golbaldate[2]);


	// controles des variables du script
	if (isset($Aff))    {}else{$Aff=1;}                  // debut semaine lundi par default
	if ($Aff != "0") { $Aff =1; }                        // toute autre valeurs que 0 retourne 1
	
	
	$cejour = $Jour." ".$Mois." ".$Annee;
	$nowjourdeceluila = date("j n Y");
	$cettedate = 
	// definitions complementaires
	$NomDuMois=array("err","Jan","Fev","Mar","Avr","Mai","Juin","Juil","Aou","Sept","Oct","Nov","Dec");
	$Week=array(
		 array("d","l","m","M","j","v","s"), // debut d'affichage =dimanche
		 array("l","m","M","j","v","s","d") //  debut d'affichage =lundi
		 );
	
	$avantan = $Annee-1;
	$aprean = $Annee+1;
	
	$prevtan =  $Annee;
	$prevtmoi = $Mois-1;
	if($prevtmoi < 1){
		$prevtan =  $Annee-1;
		$prevtmoi = 12;
	}
	
	$nextan =  $Annee;
	$nextmoi = $Mois+1;
	if($nextmoi > 12){
		$nextan =  $Annee+1;
		$nextmoi = 1;
	}
	
	$NoJour = -date("w",mktime(0,0,0,$Mois,1,$Annee));     
	if ($Aff == 0 ) {
		$NoJour +=1;
	}
	else{
		$NoJour +=2 ; 
	}          
	if ($NoJour >0 && $Aff ==1) { 
		$NoJour -=7;
	}           
	$JourMax =date("t",mktime(0,0,0,$Mois,1,$Annee));           
	
								
	echo"
	<table cellspacing='1' cellpadding='0' border='0'>
		<tr>
		<td class='fondmediumlignt' colspan='7' valign='top' align='center'>
		<a href='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=$cible&amp;date=$avantan-$Mois-$Jour&amp;type=$type'><<</a>
		 <input type='text' value='$Annee' style='width:40px' onchange=\"document.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=$cible&amp;date='+this.value+'-$Mois-$Jour&amp;type=$type'\">
		 <a href='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=$cible&amp;date=$aprean-$Mois-$Jour&amp;type=$type'>>></a>
			 </td>
		</tr>
		<tr>
		<td class='fondmedium' colspan='7' valign='top' align='center'>
		 <a href='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=$cible&amp;date=$prevtan-$prevtmoi-$Jour&amp;type=$type'><<</a>
		<input type='text' value='$Mois' style='width:30px' onchange=\"document.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=$cible&amp;date=$Annee-'+this.value+'-$Jour&amp;type=$type'\">
		<font color='000000'>$NomDuMois[$Mois] </font>
		 <a href='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=$cible&amp;date=$nextan-$nextmoi-$Jour&amp;type=$type'>>></a>
		</td>
		</tr>
		<tr class='fondmediumlignt'>
		\n";
	for ( $w=0;$w<7;$w++){
		echo "  <td>".strtoupper($Week[$Aff][$w])."</td> \n";
	}
	echo "  </tr>\n";
	
	// creation du calendrier
	for ($semaine=0;$semaine <=5;$semaine++) {   // 6 semaines par mois
		echo "  <tr>\n";
		for ($journee=0;$journee <=6;$journee++) { // 7 jours par semaine
		
		$td= "  <td class='calendarno'>";
		if ("$NoJour $Mois $Annee"==$cejour) {   
			$td= "  <td class='calendarnow'>";
		}
		if ("$NoJour $Mois $Annee"==$nowjourdeceluila) {   
			$td= "  <td class='calendarnowj'>";
		}
		echo"$td";
		if ($NoJour >0 && $NoJour <= $JourMax ){ // si le jour est valide a afficher
		 	$ceNojour = $NoJour;     if(strlen($ceNojour) == 1){ $ceNojour = "0".$ceNojour;}
     		$ceNomois = $Mois;     if(strlen($ceNomois) == 1){ $ceNomois = "0".$ceNomois;}
     		echo "
			<a href='#' onclick=\"fejour('$Annee-$ceNomois-$ceNojour')\"><font style='color:#000000'>$NoJour</font></a>"; 
		}
		else{
			 echo"&nbsp;";    
		}
		$NoJour ++; 
		echo "</td>\n";     
		}
		echo "  </tr>\n"; 
	}
	echo "</table>"; 
}
if(ereg("time",$type)){
		
		
	echo"
	<table cellspacing='1' cellpadding='0' border='0'>
		<tr>
		<td class='fondmediumlignt' valign='top' align='center'> heure</td>
		</tr>
		<tr>
		<td class='fondmedium' valign='top' align='center'>
		<script language='javascript' type='text/javascript'>
			function swatch(){
				heur = parseInt(document.montre.heure.value);
				minu = parseInt(document.montre.minute.value);
				seco = parseInt(document.montre.seconde.value);
				
				if(heur > 23){
					heur=23;
					document.seconde.heure.value=23;
				}
				if(minu > 59){
					minu=59;
					document.montre.minute.value=59;
				}
				if(seco > 59){
					seco=59;
					document.montre.seconde.value=59;
				}
				";				
				if($type=="time"){
					$golbaldate = split(":",$date);
					$Heure = $golbaldate[0];
					$Minute = $golbaldate[1];
					$Seconde = $golbaldate[2];
					echo"parent.document.$cible.value=heur+':'+minu+':'+seco;
					";	
				}
				if($type=="datetime"){
					$golbaldate = split(":",substr($date,11,8));
					$Heure = $golbaldate[0];
					$Minute = $golbaldate[1];
					$Seconde = $golbaldate[2];
					echo"parent.document.$cible.value=ametrre+' '+heur+':'+minu+':'+seco;";	
				}									
			echo"
				parent.document.getElementById('menu_date').style.visibility='hidden';
			}
		</script>
 		<form name='montre' action='about:blank' method='get' onsubmit=\"swatch();return false\">
 
 		<input type='text' name='heure' value='$Heure' style='width:30px' maxlength='2'>h
		<input type='text' name='minute' value='$Minute' style='width:30px' maxlength='2'>m
		<input type='text' name='seconde' value='$Seconde' style='width:30px' maxlength='2'>s
		<p align='right'>
		<input type='submit' class='buttontd' value='ok'>
		</p>
		</form>
		</td>
		</tr>
		</table>	 
	"; 	
}
echo"</center></body>
	</html>";
?>
