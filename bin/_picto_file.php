<?php // 19 > Affichage dans l'éditeur d'image Picto ;
session_name("adeli"); 
session_cache_expire('nocache');
session_start();
if(isset($_GET["file"])){
	$file="../../".$_GET["file"];
	if(is_file($file)){
	$exts = strtolower(substr(strrchr($file,"."),1));

		$exto = array(
			"jpg"=>"image/jpeg",
			"jpeg"=>"image/jpeg",
			"gif"=>"image/gif",
			"png"=>"image/png"
		);
	header("Content-Type:  $exto[$exts]");
	$size = getimagesize($file);
	if($size[2]==1){ $src = imagecreatefromgif($file); }
	elseif($size[2]==2){ $src = imagecreatefromjpeg($file); }
	elseif($size[2]==3){ $src = imagecreatefrompng($file); }
	
	$left=$_SESSION["acts"]["imagecrop"][0]*$size[0]/100;
	$top=$_SESSION["acts"]["imagecrop"][1]*$size[1]/100;
	$right=$_SESSION["acts"]["imagecrop"][2]*$size[0]/100;
	$bottom=$_SESSION["acts"]["imagecrop"][3]*$size[1]/100;
	
	$size[0]-=($left+$right);
	$size[1]-=($top+$bottom);
	
	$neww = $size[0];
	$newh = $size[1];
	
	$quality = 100;
	

	
	if(isset($_SESSION["acts"]["imagesize"])){
		$neww = $_SESSION["acts"]["imagesize"][0]-($left+$right);
		$newh = $_SESSION["acts"]["imagesize"][1]-($top+$bottom);
		$dleft=$_SESSION["acts"]["imagecrop"][0]*$neww/100;
		$dtop=$_SESSION["acts"]["imagecrop"][1]*$newh/100;
		$dright=$_SESSION["acts"]["imagecrop"][2]*$neww/100;
		$dbottom=$_SESSION["acts"]["imagecrop"][3]*$newh/100;
		$neww-=($dleft+$dright);
		$newh-=($dtop+$dbottom);
	}

	
		
	$des = imagecreatetruecolor($neww, $newh);

	

	imagecopyresampled( $des, $src, 0, 0, $left, $top, $neww, $newh, $size[0]-$right, $size[1]-$bottom);
	$filtes = array_keys($_SESSION["acts"]);
	
	imagedestroy($src);
	/////////////////////////////////////////FONCTIONS		
	for($i=0 ; $i<sizeof($_SESSION["acts"]) ; $i++){
		$filtre = $filtes[$i];
		
		if( $_SESSION["acts"][$filtre]!=0 ){
			if($filtre == "negatif" ){ 
				imagefilter( $des, IMG_FILTER_NEGATE);
			}
			if($filtre == "grayscale"){
				imagefilter( $des, IMG_FILTER_GRAYSCALE);
			}
			if($filtre == "brite"){
				imagefilter( $des, IMG_FILTER_BRIGHTNESS, $_SESSION["acts"][$filtre]);
			}
			if($filtre == "cont"){
				imagefilter( $des, IMG_FILTER_CONTRAST, $_SESSION["acts"][$filtre]);
			}
			if($filtre == "blur"){
				imagefilter( $des, IMG_FILTER_GAUSSIAN_BLUR, $_SESSION["acts"][$filtre]);
			}
			if($filtre == "tour"){
				$des = imagerotate( $des, $_SESSION["acts"][$filtre], 0);
			}
		}
		
	}
	
	/////////////////////////////////////////SORTIE
	if(isset($_GET["enreg"])){
		if($size[2]==1){ imagegif($des,$file); }
		elseif($size[2]==2){ imagejpeg($des,$file,$quality); }
		elseif($size[2]==3){ imagepng($des,$file); }
		$_SESSION["acts"]=array();
		readfile($file);
	}
	else{
		if($size[2]==1){  header("Content-Type:  image/gif"); imagegif($des); }
		elseif($size[2]==2){ header("Content-Type:  image/jpeg");  imagejpeg($des); }
		elseif($size[2]==3){  header("Content-Type:  image/png"); imagepng($des); }
		//imagedestroy($des);
	}
	}
	else{
		$des = imagecreatetruecolor(300, 300);
		$col = imagecolorallocate($des,255,255,255);
		$txc = imagecolorallocate($des, 100, 100, 100);
		imagefilledrectangle ( $des, 0, 0, 300, 300, $col);
		imagestring($des, 3, 100,140,"image Picto",$txc);
		imagejpeg($des);
	}
	imagedestroy($des);
}
?>