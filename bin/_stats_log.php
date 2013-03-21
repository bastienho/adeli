<?php // 7 > Enregistreur de visites ;
echo"<!-- STATS outil de statistique - websolutions  - www.adeli.wac.fr -->
";
/*********************************************************************************************

                                : GET INFOS :

*********************************************************************************************/
   //////////////////////////////////////////////////:provenance
 $prov = getenv("HTTP_REFERER");
 $dossier="http://".$_SERVER['HTTP_HOST']."/";
	
if(!is_dir("stats")){
 echo"<!-- first visit -->";
	@mkdir("stats",0777);	
	
}
if(is_dir("stats")){
//chmod("stats",0777);

if(-1 >= $pos = strpos($prov,$dossier)){
	echo"<!-- + 1 -->";
 //////////////////////////////////////////////////:date
 $date="/".date("Y")."-".date("m")."-".date("d")."-".date("H")."-".date("i")."-".date("s");
 //////////////////////////////////////////////////:ip de l'utilisateur
 $unik = getenv("REMOTE_ADDR");
   //////////////////////////////////////////////////:mot clefs
 $prov = getenv("HTTP_REFERER");
 $find="";
 $findresu = array("q","find","search","p","kw");
 for($f=0 ; $f< sizeof($findresu) ; $f++){
	 if(ereg($findresu[$f]."=",$prov)){
	  $rech = substr($prov,strpos($prov,$findresu[$f]."=")+strlen($findresu[$f])+1,strlen($prov));
	  	if(ereg("&",$rech)){
			$find .= "<>".urldecode(substr($rech,0,strpos($rech,"&")));
		}
		else{
			$find .= "<>".urldecode(substr($rech,0,strlen($rech) ));
		}
	 }
 }
 //////////////////////////////////////////////////:provenance
 $prov = str_replace('http://','',$prov);
 $prov =   substr($prov,0,strpos($prov,'/',0));
 if($prov == ''){
  $prov = 'acces direct';
 }
 //////////////////////////////////////////////////:navigateur
 $nav = $_SERVER['HTTP_USER_AGENT'];
 $nav = str_replace('/','',$nav);
 $nav = str_replace('\(','',$nav);
 $nav = str_replace('\)','',$nav);
 $nav = str_replace('\;','',$nav);
?><!-- W+ --><?php
/*********************************************************************************************

                                : WRITE LOGS :

*********************************************************************************************/
///////////////////////////////////////////////////:total
 $log_total="stats/total_".date("Y").".log";
  if(false !== $fp_total = @fopen("$log_total","a+")){
	@chmod("$log_total",0777);
	 $val_total = @fread($fp_total,filesize("$log_total"));
	@fclose($fp_total);
	 $val_total = abs(trim($val_total));
	 $val_total++;
	if(false!== $fp_total = @fopen("$log_total","w+")){
	 if(!fwrite($fp_total,$val_total)){
			echo"<!-- ! -->";
		}
		else{
			echo"<!-- $val_total -->";
		}
		 fclose($fp_total);
	}
  }
///////////////////////////////////////////////////:unik
 $log_unik="stats/unik_".date("Y_m").".log";
 if(false !== $fp_unik = @fopen("$log_unik","a+")){
	//chmod("$log_unik",0777);
	$valid_unik = 0;
	 if(false !== $val_unik = @fread($fp_unik,filesize("$log_unik"))){
		 if(strpos($val_unik,$unik)>-1){
		   $valid_unik = 0;
		 }
	 }
	 if($valid_unik == 1){
	  $unik = '<>'.$unik;
	  @fseek($fp_unik,strlen($val_unik));
	  @fwrite($fp_unik,$unik);
	  @fclose($fp_unik);
	 }
 }
///////////////////////////////////////////////////:prov
 $log_prov="stats/prov_".date("Y_m").".log";
 if(false !== $fp_prov = @fopen("$log_prov","a+")){
	@chmod("$log_prov",0777);
 $val_prov = @fread($fp_prov,filesize("$log_prov"));
  fseek($fp_prov,strlen($val_prov));
  fwrite($fp_prov,"<>".$prov);
  fclose($fp_prov);
 }
///////////////////////////////////////////////////:mots clefs
if($find != ""){
 $log_find="stats/find_".date("Y_m").".log";
 if(false !== $fp_find = @fopen("$log_find","a+")){
	@chmod("$log_find",0777);
 $val_find = fread($fp_find,filesize("$log_find"));
  @fseek($fp_find,strlen($val_find));
  @fwrite($fp_find,$find);
  @fclose($fp_find);
}
}
///////////////////////////////////////////////////:nav
if(!ereg("bot",$nav)){
 $browser='navigateur non détecté';
if(ereg("MSIE",$nav)){      $browser='explorer';}
 if(eregi("Netscape",$nav)){  $browser='netscape';}
 if(ereg("Safari",$nav)){      $browser='safari';}
 if(ereg("Opera",$nav)){      $browser='opera';}
 if(ereg("Firefox",$nav)){      $browser='firefox';}
 if(ereg("Chrome",$nav)){      $browser='chrome';}
 if(ereg("Maxthon",$nav)){      $browser='maxthon';}
 if(ereg("Obigo",$nav)){      $browser='obigo internet phone';}
 // version
 $version='';
 if(ereg("MSIE 5.",$nav)){   $version='4x';}
 if(ereg("MSIE 4.",$nav)){   $version='5x';}
 if(ereg("MSIE 6.",$nav)){   $version='6x';}
 if(ereg("MSIE 7.",$nav)){   $version='7x';}
 if(ereg("MSIE 8.",$nav)){   $version='8x';}
 if(ereg("Firefox/2",$nav)){   $version='2x';}
 if(ereg("Firefox/3",$nav)){   $version='3x';}
 if(ereg("Firefox3.",$nav)){   $version='3.x';}
 if(ereg("Firefox3.5",$nav)){   $version='3.5';}
 if(ereg("Chrome1.",$nav)){   $version='x1';}
 if(ereg("Chrome2.",$nav)){   $version='2x';}
 if(ereg("Chrome3.",$nav)){   $version='3x';}
 if(ereg("Safari/12",$nav)){   $version='1x';}
 if(ereg("Safari531",$nav)){   $version='4x';}
 if(ereg("Opera9",$nav)){   $version='9x';}
 if(ereg("Opera9.8",$nav)){   $version='10x';}
 
 // plateform
 $plateform='inconnue';
 if(ereg("Mac OS X",$nav)){   $plateform='mac osx';}
 if(ereg("Mac_PowerPC",$nav)){   $plateform='mac os 9x';}
 if(ereg("Windows 98",$nav)){   $plateform='windows 98';}
 if(ereg("Windows NT 6.",$nav)){   $plateform='windows Vista';}
 if(ereg("Windows NT 6.1",$nav)){   $plateform='windows 7';}
 elseif(ereg("Windows NT 5.",$nav)){   $plateform='windows XP';}
 elseif(ereg("Windows NT 4.0",$nav)){   $plateform='windows 2000';}
 elseif(ereg("MIDP",$nav)){   $plateform='mobile';}
 if(ereg("mobil",$nav)){   $plateform='mobile';}

 $nav = $browser." ".$version." sur ".$plateform;
 
 $log_nav="stats/nav_".date("Y_m").".log";
 if(false !== $fp_nav = @fopen("$log_nav","a+")){
	@chmod("$log_nav",0777);
 $val_nav = @fread($fp_nav,filesize("$log_nav"));
  @fseek($fp_nav,strlen($val_nav));
  @fwrite($fp_nav,"<>".$nav);
  @fclose($fp_nav);
} 
}
 ///////////////////////////////////////////////////:dat
$log_dat="stats/dat_".date("Y_m").".log";
 if(false !== $fp_dat = @fopen($log_dat,"a+")){
	@chmod("$log_dat",0777);
 $val_dat = @fread($fp_dat,filesize($log_dat));
 @fseek($fp_dat,strlen($val_dat));
 @fwrite($fp_dat,$date);
 @fclose($fp_dat);
}
 }

}
?>