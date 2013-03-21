<?php // 3 > Affichage de polies personnalisées dans LaLIE Lettre ;
session_name("adeli"); session_start();
if(isset($_SESSION["u_id"])){	
	
	$str = "Sample de texte";
	$w = 200;
	$h = 100;
	$s = 12;
	
	if(isset($_GET['str']))$str = $_GET['str'];
	if(isset($_GET['w']))$w = $_GET['w'];
	if(isset($_GET['h']))$h = $_GET['h'];
	if(isset($_GET['s']))$s = $_GET['s'];
	$font = $_GET['font'];
	$str = explode("\n",$str."\n");
	$des = imagecreatetruecolor($w, $h);
	
	$bg = imagecolorallocate($des,255,255,255);
	$co = imagecolorallocate($des,0,0,0);
	imagefilledrectangle ( $des, 0, 0, $w, $h, $bg);
	
	if(isset($_GET['font']) && is_file("../lalie/fonts/".$_GET['font'])){
		$font = "../lalie/fonts/".$_GET['font'];
		for($i=0 ; $i<sizeof($str) ; $i++){
			imagettftext($des, $s, 0, 0, ($i+1)*$s, $co, $font,$str[$i]);
		}
 	}
	else{
		for($i=0 ; $i<sizeof($str) ; $i++){
			imagestring($des, $s, 0, $i*$s, $str[$i], $co);	
		}
	}

	
	if(isset($_GET["out"])){
		imagejpeg($des,$out,100);
	}
	else{
		header("Content-Type:  image/jpeg");
		imagejpeg($des,NULL,100);
	}
	imagedestroy($des);
}
?>