<?php // 67 > Page de connexion Adeli ;
session_name("adeli"); session_start();
$serv = $prov = getenv("SERVER_NAME");$query = getenv("QUERY_STRING");$a=0;$ip = getenv('REMOTE_ADDR'); $edit=$_REQUEST['edit'];
	if(isset($_GET['decon'])){session_unset();session_destroy();setcookie('adeli_keep_alive', '', time()-42000, '/');setcookie('adeli_debit', '', time()-42000, '/');header("location:./");}
	$ritour='';

	if(substr($prov,0,4)=='www.'){
		$ritour="<font size='1'>Vous utilisez le site en www. Nous vous conseillons d'utiliser plut�t cette adresse : <a href='http://".substr($prov,4,strlen($prov)).getenv("SCRIPT_NAME")."?$query'>http://".substr($prov,4,strlen($prov)).getenv("SCRIPT_NAME")."?$query</a></font>";
		if(@fopen("http://".substr($prov,4,strlen($prov)).getenv("SCRIPT_NAME"),'rb')){
			header("location: http://".substr($prov,4,strlen($prov)).getenv("SCRIPT_NAME")."?$query");
		}
	}																	  
																		 
																		  
function includ($url){	
	if(ini_get('allow_url_include') || is_file($url)){
		return true;	
	}
	else{
		$fp=fopen($url,'rb');
		$val='';
		while(!feof($fp)){
			$val.=fgets($fp, 4096);	
		}
		$val = trim($val);
		return substr($val,5,strlen($val)-2);
	}
}

	if(isset($_SESSION['u_id']) && $_SESSION['u_id']!=NULL && $_SESSION['u_id']!=''){
		if(isset($_GET['maj']) && isset($_GET['conf'])){
			unlink("mconfig/adeli.php");
			unlink("bin/_inc.php");
			header("location: ./");
		}
		if($_SESSION["keepalive"] && $_SESSION['u_md5']!=NULL){
			setcookie('adeli_keep_alive', $_SESSION['u_md5'], time()+(36000*60*24*60), '/');
			setcookie('adeli_debit', $_SESSION['u_debit'], time()+(36000*60*24*60), '/');
		}
		else{
			setcookie('adeli_keep_alive', '', time()-42000, '/');
			setcookie('adeli_debit', '', time()-42000, '/');
		}
		$x_id=$_SESSION['x_id'];$r_id=$_SESSION['r_id'];$u_id=$_SESSION['u_id'];$vers=$_SESSION['vers'];$option=$_SESSION['option'];
		if(is_file("bin/_inc.php")){
			$incpath = "bin/_inc.php";
			if(!is_file("mconfig/adeli.php") ){
				unlink("bin/_inc.php");
				header("location: ./");
			}
		}
		else{
			$incpath = "http://www.adeli.wac.fr/vers/$vers/inc.php?x_id=$x_id&u_id=$u_id&prov=$prov&option=$option&$query";	
		}	
		if(isset($_GET["incpath"])){		 
			if(ereg("\?",$_GET["incpath"])){
				$incpath = "http://www.adeli.wac.fr/vers/$vers/".$_GET["incpath"]."&x_id=$x_id&u_id=$u_id&prov=$prov&option=$option&$query";
			}
			elseif(ereg("bin/",$_GET["incpath"])){
				$incpath = $_GET["incpath"];
			}
			else{
				$incpath = "http://www.adeli.wac.fr/vers/$vers/".$_GET["incpath"]."?x_id=$x_id&u_id=$u_id&prov=$prov&option=$option&$query";
			}
		}
		echo $ritour;
		if(true!==$incf = includ($incpath)){
			eval ($incf);
		}
		else{
			include($incpath);	
		}
		
	}
	else{
		if(is_file("bin/_inc.php") && is_file("mconfig/adeli.php")){
			if(isset($_COOKIE['adeli_keep_alive']) && $_COOKIE['adeli_keep_alive']!=NULL){
				$md5 = 	$_COOKIE['adeli_keep_alive'];

				include('mconfig/adeli.php');
				$conn = @mysql_connect($host, $login, $passe);
				@mysql_select_db($base);
				$res = mysql_query("SELECT `id` FROM `adeli_users` WHERE `md5`='$md5'");
				if($res && mysql_num_rows($res) == 1){
					$ro = mysql_fetch_array($res);
					$_SESSION['u_id'] = $ro[0];
					$_SESSION["u_debit"] =  $_COOKIE['adeli_debit'];
					$_SESSION["keepalive"] =  true;
					header("location: ./");
				}
			}
			if(isset($_GET['adeli'])){
				if (isset($_COOKIE[session_name()])) {
					setcookie(session_name(), '', time()-42000, '/');
				}
				session_unset();
				session_destroy();
				header("location:./");
			
			}
			//include("http://www.adeli.wac.fr/1.2connection.php");
			?>
            
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html  style="padding:0pc;margin:0px";>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Accueil de Adeli <?php echo $prov; ?></title>
	<link rel='stylesheet' href='http://www.adeli.wac.fr/style.css' type='text/css'>
	<link rel='icon' href='http://www.adeli.wac.fr/favicon.png' type='image/x-icon'>
	<link rel='shortcut icon' href='http://www.adeli.wac.fr/favicon.png' type='image/x-icon'>
	</head>
	
	<body style="padding:0pc;margin:0px";>
    <?php echo $ritour; ?>
	<center>
	<table cellspacing="0" cellpadding="5" border="0" style="width:100%;height:100%;">
		<tr><td bgcolor="#FFFFFF" align="left" style="height:46px">
		<img src="http://www.adeli.wac.fr/data/bando.jpg" alt="Adeli" style='float:left'>
		</td>
		<td align="right" width="80"><font size="7" color="#32b7b6"><b><?php echo $prov; ?></b></font><br>
		<span class='content'>Administration En Ligne</span>
		</td>
		</tr>
		<tr>
		<td colspan='2' style="height:34px;background:url(http://www.adeli.wac.fr/data/bandeau.jpg) repeat-x top center">&nbsp;</td>
		</tr>
	
		<tr><td colspan="2" align="center" valign="middle">

<?php
if(!is_file("mconfig/adeli.php") ){
	echo"erreur, fichier de configuration manquant...";
}
else{
	include("mconfig/adeli.php");
	include("bin/inc_func.php");
	$conn = mysql_connect($host, $login, $passe);
	mysql_select_db($base);

	echo"<div style=\"width:200px;text-align:left\">
		<div class='grostitre'>acc�s</div>	
		<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" style=\"border-width:1px;border-style:solid;border-color:#999999;background:url('http://www.adeli.wac.fr/data/gradgri.jpg') no-repeat top left\"><tr><td align=\"right\">";
	$que = getenv("QUERY_STRING");
	if(mysql_query("SHOW COLUMNS FROM `adeli_users`")  ){
	  if(isset($_POST["login"]) && isset($_POST["pass"])){
	  	$logi = stripslashes($_POST["login"]);
		$login=str_replace("'","''",stripslashes($_POST["login"]));
		$pass=stripslashes($_POST["pass"]);
		$debit = $_POST["debit"];
		$req = "SELECT * FROM `adeli_users` WHERE `login`='$login' AND `pass`='$pass'";
		if( isset($pass_sql_encode) && in_array('adeli_users',$pass_sql_encode)){
			$req = "SELECT * FROM `adeli_users` WHERE `login`='$login' AND `pass`=PASSWORD('$pass')";
		}
		$res = mysql_query($req);
		if($res && mysql_num_rows($res) == 1){
			$ro = mysql_fetch_object($res);
			$_SESSION["u_id"] = $ro->id;
			$_SESSION["u_debit"] = $debit;
			$_SESSION["keepalive"] = isset($_POST["keepmealive"]);
			echo"<!--";
			@ob_end_clean();
			header("location:./?adeli&".$_SESSION["u_id"]."&$que");
			echo"-->
			Connection r�ussie, <a href='./?adeli&".$_SESSION["u_id"]."&$que'>cliquez ici pour acc�der � Adeli</a>
			<script language='javascript'>
			document.location='./?adeli&".$_SESSION["u_id"]."&$que';
			</script>";							 		
		}
		else{
			echo"erreur de login et/ou de mot de passe ($logi)<br><br>";	
		}
	  }
	  if(isset($_POST["email"])){
		$email=str_replace("'","''",$_POST["email"]);
		$res = mysql_query("SELECT `login`,`pass`,`id` FROM `adeli_users` WHERE `email`='$email' ");
		
		if($res && mysql_num_rows($res) > 0){
			$mess="
Vos acc�s sur la console Adeli $prov.";			
			
			while($ro = mysql_fetch_array($res)){
				
				if( isset($pass_sql_encode) && in_array('adeli_users',$pass_sql_encode)){
					$ro[1]='Erreur de chiffrement de mot de passe';
					$pass=pass_create();
					if(mysql_query("UPDATE `adeli_users` SET `pass`=PASSWORD('$pass') WHERE `id`='$ro[2]' ")){
						$ro[1]=' Modifi� en : '.$pass;
					}
				}
			$mess.="
		
utilisateur : $ro[0]
mot de passe : $ro[1]
			";
			}
			$mess.="
� bient�t sur Adeli			
			";		
			if( mail($email,"Vos acc�s sur la console Adeli $prov",$mess,"from: Adeli<noreply@$prov>") ){
				echo"Vos codes vous ont �t� envoy�s sur votre adresse email";
			}	
			else{
				echo"Une erreur est survenue...";
			}				 		
		}
		else{
			echo"Aucun compte n' a �t� cr�� avec cet email<br><br>";	
		}
	  }
	  mysql_close($conn);

	  echo " 
			<form action='./?$que' method='post'>
			<input type='text' name='login' placeholder='identifiant' onfocus='this.select()' style='width:150px'><br>
			<input type='password' name='pass'  onfocus='this.select()' style='width:150px'>
			<br><br>Version :
					<select name='debit'>
						<option value='0'>Classique</option>	
						<option value='1'>Mobile</option>
											
					</select>
					<br>
			<input type='checkbox' name='keepmealive'> Se souvenir de moi<br>
			<font size='1'>(d�cochez si ordinateur partag�)</font><br>
			<input type='image' alt='se connecter' src='http://www.adeli.wac.fr/data/ok_off.gif' onmouseover=\"this.src='http://www.adeli.wac.fr/data/ok_on.gif'\"
			 onmouseout=\"this.src='http://www.adeli.wac.fr/data/ok_off.gif'\">
			</form>
		<p align='left'>		
			<div style=\"position:relative\">
				<a href='#' onclick=\"document.getElementById('perdi').style.visibility='visible'\"><font size=\"1\">Mot de passe oubli�</font></a>
				<div id=\"perdi\" style=\"position:absolute;visibility:hidden;padding:10px;top:-20px;border-width:1px;border-style:solid;border-color:#666666;background:url('http://www.adeli.wac.fr/data/gradgri.jpg') no-repeat top left\">
				<p align='right' style=\"margin:0px;padding:0px\">
				<a href='#' onclick=\"document.getElementById('perdi').style.visibility='hidden'\">Annuler</a>
				<br><br>
				<form action='./?$que' method='post'>
				Veuillez indiquez votre email pour r�cup�rer vos identifiants :<br><br>
				<input type='text' name='email' value='' style='width:200px'><br><input type='image' alt='se connecter' src='http://www.adeli.wac.fr/data/ok_off.gif' onmouseover=\"this.src='http://www.adeli.wac.fr/data/ok_on.gif'\"
			 onmouseout=\"this.src='http://www.adeli.wac.fr/data/ok_off.gif'\">
				</form>
				</p>
				</div>";
	}
	else{
		if(isset($_POST["login"]) && isset($_POST["pass"])){
			$logi = stripslashes($_POST["login"]);
			$login=str_replace("'","''",stripslashes($_POST["login"]));
			$pass=str_replace("'","''",stripslashes($_POST["pass"]));
			
			
			$req1="CREATE TABLE `adeli_groupe` (
  `id` bigint(20) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL default '',
  `droits` text NOT NULL,
  `depend` text NOT NULL,
  `da` tinyint(1) NOT NULL default '0',
  `clon` bigint(20) NOT NULL default '0',
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

			$req2="CREATE TABLE `adeli_rss` (
  `id` bigint(20) NOT NULL auto_increment,
  `public` bigint(20) NOT NULL default '0',
  `type` int(1) NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `limite` int(2) NOT NULL default '0',
  `nom` varchar(255) NOT NULL default '',
  `emplacement` int(1) NOT NULL default '0',
  `clon` int(1) NOT NULL default '0',
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

		$req3="CREATE TABLE `adeli_users` (
  `id` bigint(20) NOT NULL auto_increment,
  `login` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL default '',
  `last` datetime NOT NULL default '0000-00-00 00:00:00',
  `email` varchar(255) NOT NULL default '',
  `g` bigint(20) NOT NULL default '0',
  `d` varchar(255) NOT NULL default '',
  `clon` bigint(20) NOT NULL default '0',
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

			$req4 = "INSERT INTO `adeli_users` (`login`,`pass`,`active`) VALUES ('$login','$pass','1')";
			
			
			
			if(mysql_query($req1) && mysql_query($req2) && mysql_query($req3) && mysql_query($req4)){
				$_SESSION["u_id"] = mysql_insert_id($conn);
				@ob_end_clean();
				@header("location:./?adeli&$que");
				echo"
				Connection r�ussie, <a href='?adeli&$que'>cliquez ici pour acc�der � Adeli</a>
				<script language='javascript'>document.location=document.location+'?adeli&$que';</script>";							 		
			}
			else{
				echo"Une erreur s'est  produite<br><br>";	
			}
		  }
		echo"Premi�re utilisation ?<br><br>
		Choisissez maintenant votre login et mot de passe :
		<form action='./?$que' method='post'>
			<input type='text' name='login' value='login' onfocus='this.select()' style='width:280px'><br>
			<input type='password' name='pass' value='mot de passe'  onfocus='this.select()' style='width:280px'><br>
			
			<input type='image' alt='ok' src='http://www.adeli.wac.fr/data/ok_off.gif' onmouseover=\"this.src='http://www.adeli.wac.fr/data/ok_on.gif'\"
			 onmouseout=\"this.src='http://www.adeli.wac.fr/data/ok_off.gif'\">
			</form>
		";
	}
	
			
	 echo"
			</div>
		</p>
		<br>
		<font size=\"1\"><span class='content'>votre adresse IP</span> <b>$ip</b></font>
		</td></tr></table> </div>";	
	    

}
?>
</td>
		</tr>
	
		<tr><td align="left" valign="bottom">
	  				<a href="http://www.php.net/" target="_blank"><img src="http://www.adeli.wac.fr/data/btn_php.gif" alt="PHP-Logo" title="www.php.net" border="0" height="31" width="88"></a>
                    <a href="http://www.mysql.com/" target="_blank"><img src="http://www.adeli.wac.fr/data/btn_mysql.gif" alt="MySQL-Logo" title="www.mysql.com" border="0" height="31" width="88"></a>
		</td>
		<td align="right" valign="bottom">
			<span class='content'>Compatible :</span>
			<a href="http://www.mozilla-europe.org/fr/products/firefox/" target="_blank"><img style="border:0;width:25px;height:25px" src="http://www.adeli.wac.fr/data/c-firefox.jpg" alt="Mozilla Firefox 3+"></a>
			
					<a href="http://www.microsoft.com/france/windows/ie/downloads/default.mspx" target="_blank"><img style="border:0;width:25px;height:25px" src="http://www.adeli.wac.fr/data/c-ie.jpg" alt="Internet Explorer 8+"></a>
					
					<a href="http://www.apple.com/fr/safari/" target="_blank"><img style="border:0;width:25px;height:25px" src="http://www.adeli.wac.fr/data/c-safari.jpg" alt="Safari 3+"></a>
		</td>
		</tr>
		<tr>
		<td colspan='2' style="height:34px;background:url(http://www.adeli.wac.fr/data/bandeau.jpg) repeat-x top center">&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' align='right' valign="top" style="height:20px">
		<a href='http://www.urbancube.fr' target='_blank'>urbancube</a> 2006 - <?php echo date("Y"); ?>
		</td>
		</tr>
	</table>
	  
	  

	  </center>
	</body>
	</html>
            <?php
		}
		else{
			if(isset($_GET['c'])){
				$login=$_POST['login']; $pass=urlencode($_POST['pass']);
				$incpath="http://www.adeli.wac.fr/index.php?c=1&login=$login&pass=$pass&prov=$prov";				
			}
			else{
				$incpath="http://www.adeli.wac.fr/index.php?prov=$prov&$query";
			}
			if(true!==$incf = includ($incpath)){
				eval ($incf);
			}
			else{
				include($incpath);	
			}
		}
	}
?>
