<?php // 5 > Afficheur d'images ;
if(isset($_GET["file"])){
	$file="../".$_GET["file"];
	$exts = strtolower(substr(strrchr($file,"."),1));
		$exto = array(
			"jpg"=>"image/jpeg",
			"jpeg"=>"image/jpeg",
			"gif"=>"image/gif",
			"png"=>"image/png"		
		);
		if(isset($exto[$exts])){
			if(isset($_GET["jeveuxW"]) || isset($_GET["jeveuxH"])){
			
				  $size = getimagesize($file);
				  if($size[2]==1){ $src = imagecreatefromgif($file); }
				  elseif($size[2]==2){ $src = imagecreatefromjpeg($file); }
				  elseif($size[2]==3){ $src = imagecreatefrompng($file); }
				
				 
					  $neww=$size[0];
					  $newh=$size[1];
					  $ratio = 1;
					  $jeveuxW = $neww;
					  $jeveuxH = $newh;
					  if(isset($_GET["jeveuxW"])){ 
						$jeveuxW = $_GET["jeveuxW"];
						if($neww > $jeveuxW){
							$ratio = $neww/$jeveuxW;
						  }
					  }
					  elseif(isset($_GET["jeveuxH"])){ 
						$jeveuxH = $_GET["jeveuxH"];
						  if($newh > $jeveuxH){
							$ratio = $newh/$jeveuxH;
						  }			  
					  }	
					  $neww /= $ratio;      $newh /= $ratio;
					  $des = imagecreatetruecolor ($neww, $newh) ;
					  $fond = imagecolorallocate($des,  255, 255, 255);
					  $prev=75;
					  if(isset($_GET["prev"])) $prev=$_GET["prev"];
					  
				  if($des && $src){  
					  imagecopyresampled( $des, $src, 0, 0, 0, 0, $neww, $newh, $size[0], $size[1]);
					  header("Content-Type:  image/jpeg");
					  imagejpeg($des,false,50);
					  imagedestroy($des);
				  }
				  else{
					header("Content-Type:  image/$exts");
					 readfile($file);
				  }
			}
			else{
					header("Content-Type:  $exto[$exts]");
					readfile($file);
			}
		}
		else{
			header("location: http://www.adeli.wac.fr/icos/$exts.gif");
		}
}
?>