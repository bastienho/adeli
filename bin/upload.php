<?php // 32 > Chargement de fichiers ;

/*if(ini_get("safe_mode")==1){
	echo"La configuration de votre hébergement ne permet pas de charger des fichiers sur le serveur";
}
else{*/
include("../mconfig/adeli.php");

	
	$ext = strtolower(substr(strrchr(getcwd(),"/"),1));
	if($ext =="bin"){
		@ob_end_clean();
		session_name("adeli"); session_start();
		require("inc_func.php");
		$lien="upload.php?1";
		$pat="../";
	}
	else{
		require("$style_url/inc_func.php");
		$lien="./?incpath=upload.php";
		$pat="";
	}
	$pat = str_replace('//','/',$pat);
	$vers = $_SESSION["vers"];
	$theme = $_SESSION["theme"];
	if(!isset($style_url)) $style_url="http://www.adeli.wac.fr/vers/$vers";
	if(!ereg('http://',$style_url)) $style_url='../'.$style_url;
	echo"-->
	<html>
	<head>
	<title>upload</title>
			<link rel='stylesheet' href='$style_url/$theme/style.css' type='text/css'>
			<link rel='icon' href='$style_url/favicon.png' type='image/x-icon'>
			<link rel='shortcut icon' href='$style_url/favicon.png' type='image/x-icon'>
	</head>
	<body class='buttontd'>
	<div id='loadmask' class=\"popupload\" style=\"visibility:hidden;\">
	 <table style=\"width:100%;height:100%\">
	 <tr><td  align='center' valign='middle'>
	 <img src='$style_url/$theme/loading.gif' alt='chargement en cours' border='none'>
	 </td></tr>
	 </table>
</div>
	<!--";		
	
	$printfiles = array("jpg");
	
	
	
	
	$refreshafter = $_GET['refreshafter'];
	$refreshafter = urlencode($refreshafter);
	
	if(isset($_GET['dir'])){
	$dir = str_replace('//','/',$_GET['dir']);
	
		if(isset($_GET['makedir'])){	
			if(isset($_POST['gloups'])){
				echo $_POST['gloups'];
				$newname = correcname($_POST['newdir']);
				if( mkdir("$pat../$dir/$newname",0777) ){
					if($refreshafter!=""){
					
						echo"--><script language='javascript'>
							parent.opener.location='$pat".urldecode($refreshafter)."';
							self.close();
						</script>";
					}
				}
				else{
					echo"--><br>erreur de création ($dir/$newname)...";
				}
			}
		echo"<!-- --><b>nouveau dossier</b><form action='$lien&makedir&dir=$dir&refreshafter=$refreshafter' method='post' enctype='multipart/form-data' onsubmit=\"document.getElementById('loadmask').style.visibility='visible';\">
			<input type='text' name='newdir' value='nouveau dossier'><input type='hidden' name='gloups' value='chargement en cours'><input type='submit' value='ok'></form>";
		}
		else{
			if(isset($_POST['gloups'])){
				echo $_POST['gloups'];
				$newname = correcname($_FILES['file']['name'][0]);
				if($newname != NULL && copy($_FILES['file']['tmp_name'][0],"$pat../$dir/$newname") ){
					if($refreshafter!=""){
						echo"--><script language='javascript'>
							parent.opener.location='$pat".urldecode($refreshafter)."';
							self.close();
						</script>fichier chargé";
					}
				}
				else{
					echo"--><br>erreur de chargement ($dir/$newname)...";
				}
			}
			
			echo"<!-- --><b>nouveau fichier</b>
			<form action='$lien&dir=$dir&refreshafter=$refreshafter' method='post' enctype='multipart/form-data' name='fo' onsubmit=\"document.getElementById('loadmask').style.visibility='visible';\">
			<input type='file' name='file[]'><input type='hidden' name='gloups' value='chargement en cours'><input type='submit' value='ok'><br><br>(".ini_get('post_max_size')." max)</form>";
		}
	}
	else{
		echo"-->erreur de commande !";
	}
	echo"</body></html>";
//}
?>