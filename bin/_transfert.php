<?php // 26 > Transfert de fichiers ;
if(isset($_GET['i']) && ( isset($_GET['c']) || isset($_GET['f']) )){
	$retu='';
	$prov = getenv("SERVER_NAME");
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
	
	$edit = $i = abs($_GET['i']);
	
	require_once("../mconfig/adeli.php");	
	require_once("inc_func.php");	
	$conn = mysql_connect($host, $login, $passe);
	mysql_select_db($base);
	if( isset($_GET['c']) ){
		$c = abs($_GET['c']);
		$ros = mysql_query("SELECT `id` FROM `$compta_base` WHERE `client`='$i' AND `id`='$c'");
		if($ros && mysql_num_rows($ros)==1){
			 $rew=mysql_fetch_object($ros);
			$fid = $c;
			include('_compta_pdf.php');
		}
		else{
			$retu="Le fichier demandé n'existe pas ou plus.";	
		}
	}
	if( isset($_GET['f']) ){
		$f = explode('/',$_GET['f']);
		$fich = $fichiers['clients'][$f[0]][0];	
		$pat = "../$fich$f[1]";
		if(is_file($pat)){
			$ctype = geti($f[1]);
			$size = filesize($pat);
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=".str_replace(' ','_',$f[1]).";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: $size");
			readfile($pat);
			exit();
		}	
		else{
			$retu="Le fichier demandé n'existe pas ou plus. $pat";	
		}
	}
	if($retu!=''){
		echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html  style=\"padding:0pc;margin:0px\";>
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
	<title>Transfert de fichiers $prov</title>
	<link rel='stylesheet' href='http://www.adeli.wac.fr/style.css' type='text/css'>
	</head>
	
	<body>
	<h1>Transfert de fichiers $prov</h1>
	<span class='content'>$retu</span>
	</body></html>";	
	}

}
?>