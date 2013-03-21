<?php // 14 > LaLIE Envoi programmé ;
if(isset($_GET['i'])  &&  isset($_GET['s'])  &&  isset($_GET['f'])   &&  isset($_GET['d']) ){
	include("../mconfig/adeli.php");	
	include('inc_func.php');
	if(!isset($lalierp)){
		$lalierp='LaLIE_rapports';	
	}	
	$rid=$_GET['i'];
	$s=$_GET['s'];
	$f=$_GET['f'];
	$d=$_GET['d'];
	$conn = mysql_connect($host, $login, $passe);
	mysql_select_db($base);
	$res = mysql_query("SELECT `sujet`,`message`,`dests` FROM `$lalierp` WHERE `id`=$rid AND `secure`='$s'");
	if($res && mysql_num_rows($res)==1){
		$ro = mysql_fetch_array($res);
		$object = $ro[0];
		$de =$d;
		$u_email = $f;
		$listmail= array_unique(explode("\n",$ro[2]));
		$totlml=sizeof($listmail);
		if($object != "" && $de != "" && $u_email != "" && sizeof($listmail)>0 && $ro[1]!='' ){
			$message= $ro[1];
			$prov = getenv("SERVER_NAME");
			$eol="\n";
			$now = time();
			$headers .= "From: $de <$u_email>".$eol;
			$headers .= "Reply-To: $de <$u_email>".$eol;
			$headers .= "Return-Path: $de <$u_email>".$eol;    
			$headers .= "Message-ID: <".$s."@".$prov.">".$eol;
			$headers .= "X-Mailer: PHP v".phpversion().$eol;         
			$mime_boundary="----=_NextPart_".md5(time());
			$headers .= 'MIME-Version: 1.0'.$eol;
			$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"; Content-Transfer-Encoding: quoted-printable; boundary=\"".$mime_boundary."\"".$eol;
			
			$path = substr(getenv('SCRIPT_NAME'),0,strrpos(getenv('SCRIPT_NAME'),"/"));
			$messi = str_replace(" src=\"img/"," src=\"http://$prov$path/img/",$message);
			$messi = str_replace(" src='img/"," src='http://$prov$path/img/",$messi);
			$messi = str_replace("$"."object",urlencode($object),$messi);
			$messi = str_replace("$"."objet",urlencode($object),$messi);			
			$res_field = mysql_list_fields($dbase,$dlaliedb);
			$columns = mysql_num_fields($res_field);		
			$e=0;
			
			for($m=0 ; $m<$totlml ; $m++){
				$rapport="";
				$email = trim(array_shift($listmail));
				if($email != "" && substr_count($email, '@')==1 ){			
					$mess = $messi;
					$rem = mysql_query("SELECT * FROM `$dlaliedb` WHERE `email`='$email' AND `groupe`='desinscrits'");
				   if(!$rem || mysql_num_rows($rem)==0){
						$rem = mysql_query("SELECT * FROM `$dlaliedb` WHERE  `email`='$email' LIMIT 0,1");
					   $rom = mysql_fetch_object($rem);		 	 			 		   
					   for ($i = 0; $i < $columns; $i++) {
							$field_name = mysql_field_name($res_field, $i);
							$field_var = $rom->$field_name;
							$mess = str_replace('$'.$field_name,$field_var ,$mess);
					   }	 
				   
						$e++;
						$msg_txt = trim(stripslashes(strip_tags($mess)))."\n\n\n\n\n\n\n se désincrire: http://www.lalie.wac.fr/?desinsc=$x_id \nsignaler un abus: http://www.lalie.wac.fr/?abus=$x_id";
						
						$msg_html = stripslashes($mess)."<a href='http://www.lalie.wac.fr/?desinsc=$x_id&email=$email' target='_blank'><font color='999999' size='1'>se désincrire</font></a>";
						if(is_file('_lalie_trace.php')){
							$msg_html.= "<img src='http://$prov$path/_lalie_trace.php?r=$rid&m=$email' />";
						}
						$msg="";
						$msg .= $msg_html.$eol.$eol;		
						if(mail($email,$object,$msg,$headers)){
							$rapport.="<div class=lok>$email</div>";
						}
						else{
							$rapport.="<div class=lno>$email</div>";
						}	
					}
					else{
						$rapport.="<div class=lno>$email (Désinscrit)</div>";
					}									
				}
				else{
						$rapport.="<div class=lno>$email (Invalide)</div>";
				}
				if( !mysql_query("UPDATE `$lalierp` SET `rapport`=CONCAT(`rapport`,'".str_replace("'","''",$rapport)."'),`dests`='".implode("\n",$listmail)."' WHERE `id`='$rid'") ){
					echo"la lettre a bien été envoyée à $email, mais le rapport a échoué<br>";
				}							
			}	
			if( mysql_query("UPDATE `$lalierp` SET `active`=1 WHERE `id`='$rid'") ){
				echo"1";
			}
			else{
				echo"la lettre a bien été envoyée, mais le rapport a échoué";
			}
			
		}
		else{
			echo"problème de récupération de données, lettre non envoyée";
		}
		deconnecte($conn);		
	}
	else{
		echo"Envoi non identifié";	
	}
}
else{
	echo"Eléments manquants";	
}
?>