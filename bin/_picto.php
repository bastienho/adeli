<?php // 168 > Retouche d'images ;

$pat='../';
if(!function_exists('sql_open')){
	session_name("adeli");
	session_start();
	$vers = $_SESSION['vers'];
	$u_id = $_SESSION['u_id'];
	$theme = $_SESSION['theme'];
	include("../mconfig/adeli.php");
	if(!isset($style_url)) $style_url="http://www.adeli.wac.fr/vers/$vers";
	else $style_url = '../'.$style_url;
	$pat='../../';
	include("inc_func.php");
}
$openpic="_picto_file.php";
$imaforms = array("jpg","jpeg","gif","png","bmp");

////////////////////////////////////////////////////////////////////// EDITEUR
if(isset($_GET['fichier']) ){	
	
	
	if(in_array(strtolower(substr(strrchr($_GET['fichier'],"."),1)),$imaforms)){	
	
	$fichier = stripslashes($_GET['fichier']);
	$fichier_nom = strtolower(substr(strrchr($_GET['fichier'],"/"),1));
	$dims = getimagesize("$pat/$fichier");
	$pds = ponderal(filesize("$pat/$fichier"));	
	
	$plus="";
	
	//////////////////////////////////////////////////////////////////// EFFECT LIST
	$filtres = array("grayscale","negatif","brite","cont","blur","tour");
	
	if(!isset($_SESSION["acts"]) || isset($_GET["init"]) ){
		$_SESSION["acts"]=array();
		for($f=0 ; $f<sizeof($filtres) ; $f++){
			$_SESSION["acts"][$filtres[$f]]=0;
		}
		$_SESSION["acts"]["imagecrop"]=array(0,0,0,0);
	}
	if(!isset($_SESSION["ismorf"])){
		$_SESSION["ismorf"]="checked";
	}
	////////////////////////////////////// setimagesize
	if(isset($_POST["im_larg"])){
		$im_larg = $_POST["im_larg"];
		$im_haut = $_POST["im_haut"];
		$_SESSION["acts"]["imagesize"]=array($im_larg,$im_haut);
		$_SESSION["ismorf"]="";
		if(isset($_POST["morf"])){
			$_SESSION["ismorf"]="checked";
		}
	}
	////////////////////////////////////// cropimage
	if(isset($_POST["im_left"])){
		$im_left = $_POST["im_left"];
		$im_top = $_POST["im_top"];
		$im_right = $_POST["im_right"];
		$im_bottom = $_POST["im_bottom"];
		$_SESSION["acts"]["imagecrop"]=array($im_left,$im_top,$im_right,$im_bottom);
	}
	
	
	if(isset($_SESSION["acts"]["imagesize"])){
			$dims[0] = $_SESSION["acts"]["imagesize"][0];
			$dims[1] = $_SESSION["acts"]["imagesize"][1];
	}
	
	
	
	if(isset($_GET["enreg"])){	$plus="&enreg";}
	
	
	if(isset($_GET["filtre"])){	
		$_SESSION["acts"][$_GET["filtre"]]=$_GET["value"]; 
	}
	
	$next_gray = abs(abs($_SESSION["acts"]["grayscale"])-1);
	$next_nega = abs(abs($_SESSION["acts"]["negatif"])-1);
	$next_90a = $_SESSION["acts"]["tour"]+90;
	$next_90h = $_SESSION["acts"]["tour"]-90;
	$next_180 = $_SESSION["acts"]["tour"]+180;
	}
	//////////////////////////////////////////////////////////////////// PRINT
	echo"
	<html>
		<head>
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
			<title>$fichier_nom ($dims[0] x $dims[1]) $option / $u_login@$denomclient / Adeli-$theme $vers</title>
			<link rel='stylesheet' href='$style_url/$theme/style.css' type='text/css'>
			<link rel='icon' href='$style_url/favicon.png' type='image/x-icon'>
			<link rel='shortcut icon' href='$style_url/favicon.png' type='image/x-icon'>
	</head>
	<body bgcolor='#FFFFFF' onload='window.focus()'>
	<table cellspacing='0' border='0' style=\"width:100%;height:100%\" width='100%' height='100%'>
	<tr style=\"height:20px\"><td class='buttontd'>Edition de <b>$fichier_nom</b></td></tr>
		<tr style=\"height:20px\"><td class='buttontd'>
			<table ><tr>
				<td><a href='_picto.php?upd&fichier=$fichier&enreg'  class='buttontd'>enregistrer les modifications</a></td>
				<td><a href='_picto.php?upd&fichier=$fichier&init' class='buttontd'>réinitialiser</a></td>
			</tr></table>
		</td></tr>
		<tr><td class='cadrebas' align='center' valign='middle'>
		
		<table cellspacing='0' cellpadding='2' border='0' style=\"width:100%;height:100%\">
		<tr><td align='center' valign='middle'><iframe name='canvas' src='about:blank' width='100%' height='100%'></iframe>
		
		<script language='javascript'>
		canvas.document.body.innerHTML=\"<img src='$openpic?file=$fichier$plus&dat=".time()."'>\";
		</script>
		</td>
		<td style='width:150px' bgcolor='#DDDDDD' align='left' valign='top'>
		<b>effets</b><br>
		<input type='checkbox' onclick=\"document.location='_picto.php?upd&fichier=$fichier&filtre=grayscale&value=$next_gray';\" ";
		if($_SESSION["acts"]['grayscale']==1){ echo'checked'; }
		echo">
		<a href='_picto.php?upd&fichier=$fichier&filtre=grayscale&value=$next_gray'>niveau de gris</a><br>
		<input type='checkbox' onclick=\"document.location='_picto.php?upd&fichier=$fichier&filtre=negatif&value=$next_nega';\"";
		if($_SESSION["acts"]['negatif']==1){ echo'checked'; }
		echo">
		<a href='_picto.php?upd&fichier=$fichier&filtre=negatif&value=$next_nega'>négatif</a><br>
					<hr>
					luminosité
					<table cellspacing=\"0\" cellpadding=\"0\"><tr>";
					for($l=-150 ; $l<=150 ; $l+=10){
						$col = '#FFFFFF';
						if( ($l>=$_SESSION["acts"]["brite"] && $l<=0 && $_SESSION["acts"]["brite"]<0) || ($l<=$_SESSION["acts"]["brite"] && $_SESSION["acts"]["brite"]>0 && $l>=0 )){
							$col = '#00CCEE';
						}
						if( $l==-150 || $l==150 || $l==0){
							$col = '#999999';
						}
						echo"<td bgcolor=\"$col\" style=\"width:3px;height:6px;cursor:pointer\" onclick=\"document.location='_picto.php?upd&fichier=$fichier&filtre=brite&value=$l'\"></td>";
					}		
				echo"</tr></table><br>
				contraste <table cellspacing=\"0\" cellpadding=\"0\"><tr>";
					for($l=150 ; $l>=-150 ; $l-=10){
						$col = '#FFFFFF';
						if( ($l>=$_SESSION["acts"]["cont"] && $l<=0 && $_SESSION["acts"]["cont"]<0) || ($l<=$_SESSION["acts"]["cont"] && $_SESSION["acts"]["cont"]>0 && $l>=0 )){
							$col = '#00CCEE';
						}
						if( $l==-150 || $l==150 || $l==0){
							$col = '#999999';
						}
						echo"<td bgcolor=\"$col\" style=\"width:3px;height:6px;cursor:pointer\" onclick=\"document.location='_picto.php?upd&fichier=$fichier&filtre=cont&value=$l'\"></td>";
					}		
				echo"</tr></table><br>
	<hr>
		flou <table cellspacing=\"0\" cellpadding=\"0\"><tr>";
					for($l=0 ; $l<=200 ; $l+=10){
						$col = '#FFFFFF';
						if( ($l>=$_SESSION["acts"]["blur"] && $l<=0 && $_SESSION["acts"]["blur"]<0) || ($l<=$_SESSION["acts"]["blur"] && $_SESSION["acts"]["blur"]>0 && $l>=0 )){
							$col = '#00CCEE';
						}
						echo"<td bgcolor=\"$col\" style=\"width:3px;height:6px;cursor:pointer\" onclick=\"document.location='_picto.php?upd&fichier=$fichier&filtre=blur&value=$l'\"></td>";
					}		
				echo"</tr></table><br>
		<hr>
		<b>rotation</b><br>
		<a href='_picto.php?upd&fichier=$fichier&filtre=tour&value=$next_90h' title='90° Horaire'><img src='$style_url/picto/r90h.png' alt='90° Horaire' border='none'></a>
		<a href='_picto.php?upd&fichier=$fichier&filtre=tour&value=$next_90a' title='90° Anti-Horaire'><img src='$style_url/picto/r90a.png' alt='90° Anti-Horaire' border='none'></a>
		<a href='_picto.php?upd&fichier=$fichier&filtre=tour&value=$next_180' title='180°'><img src='$style_url/picto/r180.png' alt='180°' border='none'></a><br>
		
		<hr><b>recadrage</b> 
		<form action='_picto.php?upd&fichier=$fichier' method='post' name='im_crop'>
				<input type='hidden' name='im_left' value='".$_SESSION["acts"]["imagecrop"][0]."'>
				<input type='hidden' name='im_top' value='".$_SESSION["acts"]["imagecrop"][1]."'>
				<input type='hidden' name='im_right' value='".$_SESSION["acts"]["imagecrop"][2]."'>
				<input type='hidden' name='im_bottom' value='".$_SESSION["acts"]["imagecrop"][3]."'>
		</form>
		<table cellspacing=\"0\" cellpadding=\"0\" style='border-style:dashed;border-color:#000000;border-width:1px'><tr>
		<td width='50'>&nbsp;</td><td width='6'>
				<table cellspacing=\"0\" cellpadding=\"0\">";
					for($l=0 ; $l<=50 ; $l+=2){
						$col = '#FFFFFF';
						if( $l<=$_SESSION["acts"]["imagecrop"][1]){
							$col = '#00CCEE';
						}
						if( $l==$_SESSION["acts"]["imagecrop"][1]){
							$col = '#999999';
						}
						echo"<tr><td bgcolor=\"$col\" style=\"height:2px;width:6px;cursor:pointer\" onclick=\"document.im_crop.im_top.value='$l';document.im_crop.submit();\"></td></tr>";
					}
				echo"</table>
		</td><td width='50'>&nbsp;</td></tr>
		<tr><td colspan='3' align='center'>
				<table cellspacing=\"0\" cellpadding=\"0\"><tr>";
					for($l=0 ; $l<=50 ; $l+=2){
						$col = '#FFFFFF';
						if( $l<=$_SESSION["acts"]["imagecrop"][0]){
							$col = '#00CCEE';
						}
						if( $l==$_SESSION["acts"]["imagecrop"][0]){
							$col = '#999999';
						}
						echo"<td bgcolor=\"$col\" style=\"width:2px;height:6px;cursor:pointer\" onclick=\"document.im_crop.im_left.value='$l';document.im_crop.submit();\"></td>";
					}
					echo"<td bgcolor=\"#CCCCCC\" style=\"width:1px;height:6px;cursor:pointer\"></td>";
					for($l=50 ; $l>=0 ; $l-=2){
						$col = '#FFFFFF';
						if( $l<=$_SESSION["acts"]["imagecrop"][2]){
							$col = '#00CCEE';
						}
						if( $l==$_SESSION["acts"]["imagecrop"][2]){
							$col = '#999999';
						}
						echo"<td bgcolor=\"$col\" style=\"width:2px;height:6px;cursor:pointer\" onclick=\"document.im_crop.im_right.value='$l';document.im_crop.submit();\"></td>";
					}		
				echo"</tr></table>
		</td></tr>
		<tr><td width='50'>&nbsp;</td><td width='6'>
				<table cellspacing=\"0\" cellpadding=\"0\">";
					for($l=50 ; $l>=0 ; $l-=2){
						$col = '#FFFFFF';
						if( $l<=$_SESSION["acts"]["imagecrop"][3]){
							$col = '#00CCEE';
						}
						if( $l==$_SESSION["acts"]["imagecrop"][3]){
							$col = '#999999';
						}
						echo"<tr><td bgcolor=\"$col\" style=\"height:2px;width:6px;cursor:pointer\" onclick=\"document.im_crop.im_bottom.value='$l';document.im_crop.submit();\"></td></tr>";
					}
				echo"</table>
		</td><td width='50'>&nbsp;</td></tr></table>	
				
				
		</td></tr></table>
		</td></tr>
		<tr style=\"height:20px\"><td class='buttontd'>
			<table cellspacing='5'><tr>
				<td valign='top' align='center'>
				<script language='javascript' type='text/javascript'>
		function verifmorf(ki,koi){
						ismorf = '$ismorf';
						koi = parseFloat(koi);
						if(document.im_taille.morf.checked==true){
							if(ki==1){
								lot = parseFloat(document.im_taille.im_haut.value);
								document.im_taille.im_haut.value = koi/$dims[0]*$dims[1];
							}
							if(ki==2){
							 lot = parseFloat(document.im_taille.im_larg.value);
								document.im_taille.im_larg.value = koi/$dims[1]*$dims[0];
							}
						}
					}
				</script>
				<form action='_picto.php?upd&fichier=$fichier' method='post' name='im_taille'>
				taille de l'image<center><span style='font-size:9px'>[--<input type='checkbox' name='morf' ".$_SESSION["ismorf"].">--]</span><br>			
				L:<input type='text' name='im_larg' value='$dims[0]' style='width:50px' onchange='verifmorf(1,this.value)'>
				H:<input type='text' name='im_haut' value='$dims[1]' style='width:50px' onchange='verifmorf(2,this.value)'>
				<input type='submit' value='>'></center>
				</form>
				</td>
				<td valign='top' align='center'>
				fichier d'origine :
				<br><b>$pds</b><br>
				</td>
			</tr></table>
		</td></tr>
		</table>
	</body></html>";
	
}
////////////////////////////////////////////////////////////////////// LISTE
elseif(isset($_GET["listdirr"])){

echo"
<html style='margin:0px;padding:0px'>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
		<title>Picto Adeli</title>
		<link rel='stylesheet' href='$style_url/$theme/style.css' type='text/css'>
		<link rel='icon' href='$style_url/favicon.png' type='image/x-icon'>
		<link rel='shortcut icon' href='$style_url/favicon.png' type='image/x-icon'>
</head>
<body bgcolor='#FFFFFF' style='margin:0px;padding:0px'>
<table cellspacing='0' cellpadding='0' border='0' style=\"width:95%;height:100%\">
	<tr style=\"height:20px\"><td class='buttontd'>sélection de fichier</td></tr>
		<tr><td class='cadrebas'>
	";
	$f=0;
	function listima($directory,$n){
		global $f,$imaforms,$vers,$theme,$pat;
	 $forbidendir=array(".","..","adeli","admin","bin","mconfig","tmp","logs");
		$nb = substr_count($directory,"/");
		if(is_dir("$pat/$directory")){
			$dir = scandir("$pat/$directory");		
			foreach($dir as $entry){
					$file_extension = strtolower(substr(strrchr($entry,"."),1));
					$path = "$pat/$directory/$entry";
					
					if( !in_array($entry,$forbidendir) && filetype($path)=="dir" && $nb < $n){							
							listima("$directory/$entry",$n);
					}
					elseif( in_array($file_extension,$imaforms) && filetype($path)=="file"){
						$f++;
							$statentry = stat($path);
							$size = getimagesize($path);
							$time = date ("d", $statentry[9])." ".$NomDuMois[date ("n", $statentry[9])]." ".date ("Y", $statentry[9]);
							$poids = ponderal($statentry[7]);
							$tail="height";
							if($size[1] < $size[0]){
								$tail="width";
							}	
							$entro = $entry;
							if(strlen($entro)>15){
								$entro = substr($entro,0,12)."...";
							}
						echo"<table ondblclick=\"open('$path','file','width=100,height=100,resizable=1');file.focus();\" style='width:150px;height:100px;float:left;text-align:center;vertical-align:middle'>
						<tr><td valign='top' align='center'>
						<a href='_picto.php?fichier=$directory/$entry' target='picto' title='$entry'>
						<img src='_ima.php?jeveux".strtoupper(substr($tail,0,1))."=50&prev=30&file=..$directory/$entry' alt='icone' $tail='50' class='cadre' border='none' style='float:left'> <b>$entro</b></a><br><font size='1'><b>$poids<br>$time</font>			
						</td></tr></table>";
					}
			}
		}
	}
	if(isset($_POST['search'])){
		$ni = abs($_POST['search']);
		if($ni==0){
			$ni++;
		}
		echo"<b>listage des images sur $ni niveau(x) de répertoires</b><br>
		<a href='_picto.php?listdirr'>retour</a><hr>";
		listima("",$ni);
	}
	else{
		echo"
		<b>scanner les images de mon site
		<form action='_picto.php?listdirr' method='post'>
		sur <input type='text' name='search' value='2'> niveau(x) de répertoires<br>
		<input type='submit' value='chercher'>
		</form>
		";
	}
	echo"</td></tr></table>
</body></html>";
}
////////////////////////////////////////////////////////////////////// INTERFACE
else{
	echo"<table cellspacing='0' border='0' class='cadre' style=\"width:95%;height:500\">
	<tr>
		<td width=\"200\"><iframe src=\"bin/_picto.php?listdirr\" name=\"totem\" height=\"500\" width=\"200\"></iframe></td>	
		<td><iframe src=\"bin/_picto.php?fichier\" name=\"picto\" height=\"500\" width=\"100%\"></iframe></td>
	</tr>
	</table>";
}

?>
