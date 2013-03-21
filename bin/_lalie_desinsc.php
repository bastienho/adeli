<?php // 2 > LaLIE désinscription ;
$serv = $prov = getenv("SERVER_NAME");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html  style="padding:0pc;margin:0px";>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>désinscription <?php echo $prov; ?></title>
	<link rel='stylesheet' href='http://www.adeli.wac.fr/style.css' type='text/css'>
	</head>
	
	<body style="padding:0pc;margin:0px";>
    <center>
	<h1><?php echo $prov; ?></h1>
	<p><?php 
if(isset($_GET['r']) && isset($_GET['m'])){
	$ref = abs($_GET['r']);
	$email = $_GET['m'];	
	$laliedb="LaLIE";	
	
	include('../mconfig/adeli.php');
	include('inc_func.php');
	
	$conn = mysql_connect($dhost, $dlogin, $dpasse);
	mysql_select_db($dbase);
	
	if(false !== $res=mysql_query("SELECT `id` FROM `$laliedb` WHERE `id`='".str_replace("'","''",$ref)."' AND `email`='".str_replace("'","''",$email)."'") || false===$res=mysql_query("SELECT `id` FROM `$laliedb` WHERE `email`='".str_replace("'","''",$email)."'") ){
		if(false === $res=mysql_query("SELECT `id` FROM `$laliedb` WHERE `groupe`='desinscrits' AND `email`='".str_replace("'","''",$email)."'") ){
			if(mysql_query("INSERT INTO `$laliedb` (`groupe`,`email`) VALUES('desinscrits','".str_replace("'","''",$email)."')")){
				echo"$email a bien été supprimé de notre liste de destinataires";
			}
			else{
				echo"Une erreur s'est produite, votre email n'a pu être identifié.";
			}
		}
		else{
			echo"Votre adresse est déjà inscrite sur notre liste rouge.";
		}
	}
}
else{
	echo"Une erreur s'est produite, paramètre manquant.";
}
?>
</p></center></body></html>