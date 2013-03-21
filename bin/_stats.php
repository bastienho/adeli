<?php // 131 > Statistiques de visites ;

$lim = 1000000;
echo" <table width='90%' cellspacing='0' cellpadding='5' border='0' class='cadrebas'>
		   <tr><td class='buttontd'><span class='textegras'>Statistiques</span> </td></tr>
		   <tr><td>";
		   
insert('_graphique');
insert('_stats_log');
$chem = "..";	
if(isset($stats_path)){
$chem .= "/".$stats_path;
}
else{
for($i=0 ; $i<5 ; $i++){	   
	if(is_dir("$chem/stats")){
		break;
	}
	elseif(is_dir("$chem/www/stats")){
		$chem.="/www";
		break;
	}
	else{
		$chem.="/..";
	}		
}   
}
if(!is_dir("$chem/stats")){
	echo"Stats n'est pas installé sur votre site
	<!-- $chem/stats -->";
}
else{
	$logs = "$chem/stats";	   
	 $date=date("Y-m-d");
	if(isset($_GET['act'])){	$act = $_GET['act'];}
	else{	$act = '00';}
	
	if(isset($_GET['annee'])){	$annee = $_GET['annee'];}
	else{	$annee = date("Y");}
	
	if(isset($_GET['keldate'])){  $keldate = $_GET['keldate']; }
	else{  $keldate='0000'; }
	
	if(isset($_GET['keljour'])){  $keljour = $_GET['keljour']; }
	else{  $keljour='00'; }
	

///////////////////////////////////////////////:class total
$dir = scandir("$logs/");
$toutotal=0;
 foreach ($dir as $entry){
	//while (false !== ($entry = $dir->read())) {
	  if((ereg('total',$entry))){
		 $fp_total = fopen("$logs/$entry","r");
		 $val_total = fread($fp_total,filesize("$logs/$entry"));
		 $toutotal+=$val_total;
	 }
 }
$depuisle=date("Ym");
foreach ($dir as $entry){
	  if((ereg('dat_',$entry))){
		 $entry = str_replace('dat_','',$entry);
		 $entry = str_replace('.log','',$entry);
		 $entry = str_replace('_','',$entry);
		 	if($entry < $depuisle && $entry != "000000"){
		 		$depuisle = $entry;	
		 	}	
	 }
 }  
 $depuisle = $NomDuMois[abs(substr($depuisle,4,2))]." ".substr($depuisle,0,4);
 echo"\n<b>nombre total de visites depuis $depuisle</b>: <font class=\textefonce'>".number_format($toutotal,0,'',' ')."</font>
 <br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>
 <table cellpadding='2' cellspacing='0'><tr>";


///////////////////////////////////////////////:class unik
foreach ($dir as $entry){
	  if((ereg('unik',$entry))){
		 $cetotal = str_replace('unik','',$entry);
		 $cetotal = str_replace('.log','',$cetotal);
		 $cetotal = str_replace('_','',$cetotal);
		 if($cetotal == $annee){
		 	echo"<td bgcolor='CCCCCC'><b>$cetotal</b></td>";
		 }
		 else{
		 	echo"<td><a href='./?option=$option&annee=$cetotal'>$cetotal</a></td>";
		 }
		 
	 }
 }  
 echo"</tr></table><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>";
 		 
		 $fp_total = fopen("$logs/total_$annee.log","r");
		 $val_total = fread($fp_total,filesize("$logs/total_$annee.log"));
		 $fp_unik = fopen("$logs/unik_$annee.log","r");
		 $val_unik = fread($fp_unik,filesize("$logs/unik_$annee.log"));
		 $tab_unik = split('<>',$val_unik);
		 $nb_ip = sizeof($tab_unik)-1;
		 $rapportvisite = round( $val_total/$nb_ip ,2);
		 
		 $moyenmoi=0;
		 $dir = scandir("$logs/");
		  foreach ($dir as $entry){
		  
			  if(ereg("dat_$annee",$entry)){
				$moyenmoi++;
			 }
		 }  
		  $moyenneparmoi = round($val_total/$moyenmoi,2);
  			$poidsparmoi = round(($totalpoids/3)*$moyenneparmoi,2);
			
				if($poidsparmoi >1000000000){
					$poidsparmoi/=1000000000;
					$poidsparmoi=round($poidsparmoi,2);
					$poidsparmoi.=" Go";
				}
				elseif($poidsparmoi >1000000){
					$poidsparmoi/=1000000;
					$poidsparmoi=round($poidsparmoi,2);
					$poidsparmoi.=" Mo";
				}
				else{
					$poidsparmoi/=1000;
					$poidsparmoi=round($poidsparmoi,2);
					$poidsparmoi.=" Ko";
				}
				
		//$quato  = round($moyenneparmoi/$autorised*100,2);
		echo"
		<b>visites en $annee</b>: <font class=\textefonce'>".number_format($val_total,0,'',' ')."</font>
		<br><b>visiteurs uniques en $annee</b>: <font class=\textefonce'>".number_format($nb_ip,0,'',' ')."</font>
		<font size='1' color='999999'>
		
		<br><b>$rapportvisite</b> visites par visiteur en moyenne
		<br><b>$moyenneparmoi</b> visites mensuelles en moyenne (<b>$quato %</b>)
		
		
		</font>
		<br><br>";
		
        
///////////////////////////////////////////////:class par date
if($act == 'date'){
	echo"
	<br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>
	[-]-<a href='./?option=$option&annee=$annee'><b>classement par date</b></a>:
	<br><font size='1'><table><tr><td valign='top' width='340'><font size='1'>";
	$alldatlog=array("");
	$dir = scandir("$logs/");
	
	foreach ($dir as $entry){
	  if((ereg('dat_',$entry))&&(ereg($annee,$entry))){
		array_push($alldatlog,$entry);
	  }
	 }
	 $tlm = array();$tlja = array();
	 sort($alldatlog);
	 $nbar=sizeof($alldatlog);
	 for($d=1 ; $d<$nbar ; $d++){
		 
	   $entry = $alldatlog[$d];
	   $titre_moid = substr($entry,9,2);
	   $titre_an = substr($entry,4,4);
	   $titre_moi =$NomDuMois[abs($titre_moid)];
	   $cettedate = $titre_an."_".$titre_moi;
	  
	  
	   
	   
	   $fp_dat = fopen("$logs/$entry","r");
	   $val_dat = trim(fread($fp_dat,filesize("$logs/$entry")));
	   fclose($fp_dat);
	   $nb_dat = strlen($val_dat)/20; 
	   
	   if($keldate == $cettedate){ 
	   
	    $tab_dat = array();
	   for($t=0 ; $t<$nb_dat ; $t++){
	   	array_push($tab_dat,substr($val_dat,($t*20)+1,19));
	   }
	  $tlj = '';
	   
	   
	  
	   $tlh = array();
	   $cemoiiii = $titre_moi;
	   echo"
	   <font color='999999'>&nbsp;&nbsp;[-]--</font><a href='./?option=$option&annee=$annee&act=date'><font class=\textefonce'><b>$titre_moi</b></font></a> ($nb_dat)<br>";
		 for($i=1 ; $i<=$nb_dat-1 ; $i++){
		    //echo substr($tab_dat[$i],5,5);
			$jour = substr($tab_dat[$i],8,2);
			if(!ereg($jour,$tlj)){
				$marquer=substr($tab_dat[$i],0,10);
				$nbcejour=substr_count($val_dat,$marquer);
				$tlja[$jour] = $nbcejour;
				 $tlj.='!'.$jour;
				  if($jour != $keljour){					   
					   if($nbcejour!=0) $nbcejour = "($nbcejour)";
					   else $nbcejour='';
					   echo"
					   <font color='AAAAAA'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[+]-----</font>
					   <a href='./?option=$option&annee=$annee&act=date&keldate=$cettedate&keljour=$jour'><font class='textefonce'>$jour</font></a> $nbcejour<br>";
				  }
				  if($jour == $keljour){ 			
					    echo"
					   <font color='AAAAAA'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[- ]-----</font>
					   <a href='./?option=$option&annee=$annee&act=date&keldate=$cettedate'><b><font class='textefonce'>$jour</font></b></a> ($nbcejour)<br>";
						$nbhpj = $nbcejour;
						for($h=0 ; $h<23 ; $h++){
							$he=$h;
							if($he<10) $he="0$he";
							$tlh[$h]=substr_count($val_dat,$marquer.'-'.$he);							
						}
				  }	
			}
		 }
	   }
	   else{
		   echo"
		   <font color='999999'>&nbsp;&nbsp;[+]--</font><a href='./?option=$option&annee=$annee&act=date&keldate=$cettedate'><font class=\textefonce'>$titre_moi ($nb_dat)</font></a><br>";
	   }
	   $tlm["$titre_moi"] = $nb_dat;
	  }
	// }
	
	  echo"</td><td valign='top'><font style='font-size:9px'>";
	  /////////////////////////////////////////// GRAPHIQUES
		 if(isset($_GET['keljour'])){
			$testop = $tlh; 
			 sort($testop);
			 reset($testop);
			 $top = $testop[sizeof($testop)-1];			
			 $moihpj = round($top/2);
				$dn = $NomDuJour[date("w",strtotime("$annee-$titre_moid-$keljour"))];
				
			 echo"<b>$nbhpj</b> visites le $dn $keljour $cemoiiii<br><table cellspacing='0' style='border-width:2px; border-style:solid; border-color:DDDDDD DDDDDD DDDDDD DDDDDD;'>
			 <tr><td valign='top' align='right' bgcolor='#DDDDDD' style='height:20px'><font color='000000' style='font-size:9px'><b>$top</b>_<br></td>";
				  for($h=0 ; $h<=23 ; $h++){
					$g = $h;
					if($g<10){
						$g = "0".$g;
					}
					$nubh = abs($tlh[$h]);
					$color='CCCCCC';
					if($nubh>0) $color='666666';
					
					$nbres=round(($nubh*100/$top));
					$empty = 100-$nbres;
					echo"<td rowspan='3' valign='bottom' align='center'><a class='info'><span>$nubh</span>
					<br>
					<img src='$style_url/$theme/gray.gif' width='20' height='$empty'><br><img src='$style_url/$theme/red.gif' width='20' height='$nbres'><br>
					<font class='textefonce' style='font-size:9px'>$g</font></a></td>";
				  }
				 echo"</tr><tr><td valign='middle' align='right'  bgcolor='#DDDDDD'><font color='000000' style='font-size:9px'><b>$moihpj</b>_</td></tr>
			   <tr><td valign='bottom' align='right' style='height:20px' bgcolor='#DDDDDD'><font color='000000' style='font-size:9px'><b>0</b>_<br>
			   <font color='999999' style='font-size:9px'>heure</font>
			   </td></tr>
			   </table>"; 			  
		  }
		  elseif(isset($_GET['keldate'])){
		  		$tljj = $tlja;
			 $nbhpm = 0;
			 $top = 0;
			     while( list($g,$nubh) = each($tljj) ){										
					$nbhpm+=$nubh;
					if($nubh > $top){
						$top=$nubh;
					}
				  }
			 $moihpa = round($top/2);
				$cuntd=array();
			 echo"<b>$nbhpm</b> visites en $cemoiiii<br><table cellspacing='0' style='border-width:2px; border-style:solid; border-color:DDDDDD DDDDDD DDDDDD DDDDDD;'>
			 <tr><td valign='top' align='right' bgcolor='DDDDDD'><font color='000000' style='font-size:9px'><b>$top</b>_</td>";
			 	while( list($g,$nubh) = each($tlja) ){										
					$nbres=round(($nubh*100/$top),2);
					$empty = 100-$nbres;
					$dn = date("w",strtotime("$annee-$titre_moid-$g"));
					$d = strtoupper(substr($NomDuJour[$dn],0,1));
					$cuntd[$dn]+=$nubh;
					echo"<td rowspan='3' valign='bottom' align='center'><a class='info'><span>$nubh</span>
					<br>
					<img src='$style_url/$theme/gray.gif' width='20' height='$empty'><br><img src='$style_url/$theme/red.gif' width='20' height='$nbres'><br>
					<font class='textefonce' style='font-size:9px'>$g<br>$d</font></a></td>";
				  }
				 echo"</tr><tr><td valign='middle' align='right'  bgcolor='DDDDDD'><font color='000000' style='font-size:9px'><b>$moihpa</b>_</td></tr>
			   <tr><td valign='bottom' align='right' bgcolor='DDDDDD'><font color='000000' style='font-size:9px'><b>0</b>_<br>
			   <font color='999999' style='font-size:9px'>jours</font>
			   </td></tr>
			   </table><hr>						
						";	
						$nbg=0;
						$vrb='';
				
					 			  
						for($df=0 ; $df<7 ; $df++){
							$dg = $NomDuJour[$df];
							$dc = $cuntd[$df];
							$nbg++;
							$vrb.="&c$nbg=$dg&p$nbg=".(($dc/$nbhpm*100)*360/100);
							echo"<span class='textegrasfonce'>$dg</span> : $dc visites<br>";
						}
			if(is_file('bin/_graphique.php')){
				echo"<img src='bin/_graphique.php?nb=$nbg$vrb'>";
			}	
						echo"";
		  }
		  else{
		  		$tlmm = $tlm;
			 $nbhpa = 0;
			 $top = 0;
			     while( list($g,$nubh) = each($tlmm) ){										
					$nbhpa+=$nubh;
					if($nubh > $top){
						$top=$nubh;
					}
				  }
			 $moihpa = round($top/2);
			 echo"<b>$nbhpa</b> visites en $annee<br><table cellspacing='0' style='border-width:2px; border-style:solid; border-color:DDDDDD DDDDDD DDDDDD DDDDDD;'>
			 <tr><td valign='top' align='right' bgcolor='DDDDDD'><font color='000000' style='font-size:9px'><b>$top</b>_</td>";
			 	while( list($g,$nubh) = each($tlm) ){										
					$nbres=round(($nubh*100/$top),2);
					$empty = 100-$nbres;
					$g = substr($g,0,3);
					echo"<td rowspan='3' valign='bottom' align='center'>
					<font color='CCCCCC' style='font-size:9px'>$nubh</font><br>
					<img src='$style_url/$theme/gray.gif' width='20' height='$empty'><br><img src='$style_url/$theme/red.gif' width='20' height='$nbres'><br>
					<font class='textefonce' style='font-size:9px'>$g</font></td>";
				  }
				 echo"</tr><tr><td valign='middle' align='right'  bgcolor='DDDDDD'><font color='000000' style='font-size:9px'><b>$moihpa</b>_</td></tr>
			   <tr><td valign='bottom' align='right' bgcolor='DDDDDD'><font color='000000' style='font-size:9px'><b>0</b>_<br>
			   <font color='999999' style='font-size:9px'>mois</font>
			   </td></tr>
			   </table>"; 			  
		  }
		  
  
   echo"</td></tr></table>";
		 
		 
}
else{
 echo"<br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>[+]-<a href='./?option=$option&annee=$annee&act=date'><b>classement par date</b></a>:";
}
echo"<font size='2'>";
///////////////////////////////////////////////////:prov
if($act == 'prov'){
		echo"<br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>[-]-<a href='./?option=$option&annee=$annee'><b>provenance</b></a><font size='1'>";
	
	 $fp_nav = fopen("$logs/prov_$annee.log","r");
	 $val_nav = fread($fp_nav,filesize("$logs/prov_$annee.log"));
	 $results='';
	 $nbnavtot=0;	
	 
	if(isset($_GET['dev'])){
		echo"(<a href='./?option=$option&annee=$annee&act=prov&keldate=$cetotal'>liens généralisés</b></a>)";
	}
	else{
		echo"(<a href='./?option=$option&annee=$annee&act=prov&keldate=$cetotal&dev='>liens complets</b></a>)";
	}
	if(!isset($_GET['cla'])){
		echo"(<a href='./?option=$option&annee=$annee&act=prov&keldate=$cetotal&dev=&cla='>tous par ordre alphabétique</b></a>)";
	}
	else{
		echo"(<a href='./?option=$option&annee=$annee&act=prov&keldate=$cetotal&dev='>ordre de fréquentation</b></a>)";
	}
	echo"
	 </font><table><tr bgcolor='CCCCCC'><td><font size='1'>chemin</td><td colspan='2'><font size='1'>taux</td></tr>";  
	 $val_nav = split("<>",$val_nav);
	 
	 if(!isset($_GET['dev'])){
		$newval=array();
		$nbar=sizeof($val_nav);
		for($e=0 ; $e<$nbar ; $e++){
			$tval = $val_nav[$e];
			if(strpos($val_nav[$e],'.')!==false){
				$val_nav[$e]=str_replace('co.uk','couk',$val_nav[$e]);
				$val_nav[$e]=str_replace('co.jp','cojp',$val_nav[$e]);
				$val_nav[$e]=str_replace('com.pe','compe',$val_nav[$e]);
				$tval = substr(strrchr(  substr($val_nav[$e],0,strpos($val_nav[$e],strrchr($val_nav[$e],".")))  ,"."),1);
			}
			if(is_numeric($tval)){
				$tval = "adresse ip";
			}
			array_push($newval,"$tval");
		}
		$val_nav = $newval;
	}	 
	 $allresults = sizeof($val_nav);
	 $nbprov = array_count_values($val_nav); 
	 if(!isset($_GET['cla'])){
		$nbprov = 	array_flip ($nbprov);
	 }
	 $artosprov = array_keys($nbprov); 
	 sort($artosprov);
	 if(!isset($_GET['cla'])){
		$artosprov = 	array_reverse ($artosprov);
	 }
	 $nbar=sizeof($nbprov);
	for($e=0 ; $e<$nbar ; $e++){
		 if(!isset($_GET['cla'])){
			$artosnumb = $artosprov[$e];
			$artosid = $nbprov[$artosnumb];		
		 }
		 else{
			$artosid = $artosprov[$e];
			$artosnumb = $nbprov[$artosid];
		}
		$nbres=round(($artosnumb*100/$allresults),2);
		$print = "$artosid";
		if(isset($_GET['dev'])){
			$print = "<a href='http://$artosid' target='_blank'>$artosid</a>";
		}
		$empty = 100-$nbres;
		 echo"<tr><td><font size='1'>$print</font></td><td><font size='1'>$nbres%</td><td><img src='$style_url/$theme/red.gif' height='5' width='$nbres'><img src='$style_url/$theme/gray.gif' height='5' width='$empty'></td></tr>";
	}
	 echo"</table>";
}
else{
 echo"<br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>[+]-<a href='./?option=$option&annee=$annee&act=prov'><b>provenance</b></a>:";
}
///////////////////////////////////////////////////:mots clefs
if($act == 'key'){
		echo"<br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>[-]-<a href='./?option=$option&annee=$annee'><b>mots clefs</b></a><font size='1'>";
		if(!isset($_GET['cla'])){
			echo"(<a href='./?option=$option&annee=$annee&act=key&keldate=$cetotal&dev=&cla='>tous par ordre alphabétique</b></a>)";
		}
		else{
			echo"(<a href='./?option=$option&annee=$annee&act=key&keldate=$cetotal&dev='>ordre de fréquentation</b></a>)";
		}
		echo"
		</font><table><tr bgcolor='CCCCCC'><td><font size='1'>chemin</td><td colspan='2'><font size='1'>taux</td></tr>";
	
	 $fp_nav = fopen("$logs/find_$annee.log","r");
	 $siz= filesize("$logs/find_$annee.log");
	  if( $siz>$lim){
		fseek($fp_nav,$siz-$lim);
		$val_nav = fread($fp_nav, $lim);
		echo"<br><br>Le fichier de log étant très volumineux, seul les derniers enregistrement sont utilisés.";
		$siz=$lim;
	  } 
	  else{
	   $val_nav = fread($fp_nav,$siz);
	  }
	 $results='';
	 $nbnavtot=0;	
	 $val_nav = explode("<>",$val_nav);
	 
	 
	
	 
	 $allresults = sizeof($val_nav);
	 $nbprov = array_count_values($val_nav); 
	 if(!isset($_GET['cla'])){
		$nbprov = 	array_flip ($nbprov);
	 }
	 $artosprov = array_keys($nbprov); 
	 sort($artosprov);
	 if(!isset($_GET['cla'])){
		$artosprov = 	array_reverse ($artosprov);
	 }
	 $nbar=sizeof($nbprov);
	for($e=0 ; $e<$nbar ; $e++){
			
		 if(!isset($_GET['cla'])){
			$artosnumb = $artosprov[$e];
			$artosid = $nbprov[$artosnumb];		
		 }
		 else{
			$artosid = $artosprov[$e];
			$artosnumb = $nbprov[$artosid];
		}
		$nbres=round(($artosnumb*100/$allresults),2);
		if(ereg("Ã",$artosid)){
			$artosid = utf8_decode($artosid);
		}
		$print = "$artosid";

		$empty = 100-$nbres;
		 echo"<tr><td><font size='1'>$print</font></td><td><font size='1'>$nbres%</td><td><img src='$style_url/$theme/red.gif' height='5' width='$nbres'><img src='$style_url/$theme/gray.gif' height='5' width='$empty'></td></tr>";
	}
	 echo"</table>";
}
else{
 echo"<br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>[+]-<a href='./?option=$option&annee=$annee&act=key'><b>mots clefs</b></a>:";
}



 ///////////////////////////////////////////////////:nav

if($act == 'nav'){
	echo"<br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>[-]-<a href='./?option=$option&annee=$annee'><b>navigateurs</b></a>";

 $fp_nav = fopen("$logs/nav_$annee.log","r");
 $siz = filesize("$logs/nav_$annee.log");
 $val_nav = fread($fp_nav, $siz);
 
 
 
 
 $results='';
 $nbnavtot=0;
 

 //////////////////////////////////////::old
 if(ereg("MSIE",$val_nav)){
		 $val_nav = split("<>",$val_nav);
		 $write="";
		 $nbar=sizeof($val_nav);
		 for($e = 1 ; $e<$nbar ; $e++){
			 $nav=$val_nav[$e];
		if(!ereg("bot",$nav)){
			if(ereg("MSIE",$nav)){      $browser='internet explorer';}
			 if(eregi("Netscape",$nav)){  $browser='netscape';}
			 if(ereg("Safari",$nav)){      $browser='safari';}
			 if(ereg("Opera",$nav)){      $browser='opera';}
			 if(eregi("Firefox",$nav)){      $browser='mozilla firefox';}
			 if(eregi("Maxthon",$nav)){      $browser='maxthon';}
			 // version
			 $version='';
			 if(ereg("MSIE 5.",$nav)){   $version='4x';}
			 if(ereg("MSIE 4.",$nav)){   $version='5x';}
			 if(ereg("MSIE 6.",$nav)){   $version='6x';}
			 if(ereg("1.0",$nav)){   $version='1';}
			 if(ereg("7.02",$nav)){   $version='7';}
			 // plateform
			 $plateform='inconnue';
			 if(ereg("Mac OS X",$nav)){   $plateform='mac osx';}
			 if(ereg("Mac_PowerPC",$nav)){   $plateform='mac os 9x';}
			 if(ereg("Windows 98",$nav)){   $plateform='windows 98';}
			 if(ereg("Windows NT 5.",$nav)){   $plateform='windows xp';}
			 if(ereg("Windows NT 4.0",$nav)){   $plateform='windows 2000';} 
			  $write.= "<>".$browser." ".$version." sur ".$plateform;
			  }
			 }
			 copy("dat_0000_00.log","$logs/$entry");
			 $fp_nav = fopen("$logs/$entry","r");
			  fseek($fp_nav,0);
			  fwrite($fp_nav,$write);
			  fclose($fp_nav);
			  $val_nav=$write;
	 }
	 
	
	elseif( $siz>$lim){
		fseek($fp_nav,$siz-$lim);
		$val_nav = fread($fp_nav, $lim);
		echo"<br><br>Le fichier de log étant très volumineux, seul les derniers enregistrement sont utilisés.";
		$siz=$lim;
	} 
	echo"\n
	<br><table><tr bgcolor='CCCCCC'><td colspan='2'><font size='1'>navigateurs</td><td colspan='2'><font size='1'>taux</td></tr>
	<tr><td colspan='4'><b>Récents les plus populaires</b> (500 dernières visites)</td></tr>"; 
	
	// $val_nav = str_replace('internet','',$val_nav);
	// $val_nav = str_replace('mozilla','',$val_nav);
	
	

	 if( $siz<=$lim){
	 $val_nav = explode("<>",$val_nav);
	 $allresults = sizeof($val_nav);
	 $nbprov = array_count_values($val_nav); 
	// $artosprov = array_keys($nbprov);
	 
	 $topval =   array_count_values(array_slice($val_nav,-500,500,true));
	 arsort($topval);
	 $topval  = array_slice($topval,0,5,true);
	 foreach($topval as $artosid=>$artosnumb){
		    $nbres=$artosnumb/5;//round(($artosnumb*100/100),2);
			$empty=100-$nbres;
			$navdet = explode(' sur ',$artosid);
		  echo"<tr><td><font size='2'>$navdet[0]</font></td><td><font size='2'>$navdet[1]</font></td><td><font size='2'>$nbres%</td><td><img src='$style_url/$theme/red.gif' height='5' width='$nbres'><img src='$style_url/$theme/gray.gif' height='5' width='$empty'></td></tr>";
	 }
	 
	 echo"<tr><td colspan='4'><b>Tous</b></td></tr><tr bgcolor='CCCCCC'><td colspan='2'><font size='1'>navigateurs</td><td colspan='2'><font size='1'>taux</td></tr>";
	 
	 ksort($nbprov);
	// $nbara=sizeof($nbprov);
	//for($e=0 ; $e<$nbara ; $e++){
	foreach($nbprov as $artosid=>$artosnumb){
		$nbres=round(($artosnumb*100/$allresults),2);
		$empty=100-$nbres;
		 echo"<tr><td colspan='2'><font size='1'>$artosid</font></td><td><font size='1'>$nbres%</td><td><img src='$style_url/$theme/red.gif' height='5' width='$nbres'><img src='$style_url/$theme/gray.gif' height='5' width='$empty'></td></tr>";
	}
	 echo"</table>";
	// */
	}
	else{
		echo"Fichier de log trop volumineux ($siz octets)";	
	}

}
else{
 echo"<br><img src='$style_url/$theme/gray.gif' width='400' height='1'><br>[+]-<a href='./?option=$option&annee=$annee&act=nav'><b>navigateurs</b></a>:";
}

	
}	
		   
echo"</td></tr></table>";

?>
