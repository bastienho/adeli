<?php // 60 > graphique camembert ;
$type='camembert';
if(isset($_GET['type'])) $type=$_GET['type'];
$colorz = array(
	array(255,0,0),
	array(255,180,0),
	array(150,200,0),
	array(0,255,0),
	array(0,255,170),
	array(0,150,255),
	array(0,0,255),
	array(180,0,255),
	array(255,0,180),
	array(150,150,150),
	array(0,0,150),
	array(0,150,0),
	array(150,0,0),
	array(255,255,0),
	array(0,255,255),
	array(255,0,255),
	array(150,100,50),
	array(0,200,50),
	array(150,150,50),
	array(50,0,250),
	);
	
if($type=='camembert'){ /////////////////////////////////////////////////////////////////////// CAMEMBERT
if(isset($_GET['nb']) && is_numeric($_GET['nb']) && isset($_GET['p1']) && isset($_GET['c1'])){
	$nb = $_GET['nb'];
	$h = 100+($nb*10);
	$image = imagecreatetruecolor(100,$h );
	$fond = imagecolorallocatealpha($des,  255, 255, 255, 100);
	imagefill($image, 0, 0, $fond);
	imagealphablending($image, true);
	$bg  = imagecolorallocate($image, 255, 255, 255);
	$sh  = imagecolorallocate($image, 200, 200, 200);
	imagefilledrectangle($image, 0, 0, 100, $h, $bg);
	
	imagefilledarc($image, 50, 75, 100, 40, 0, 360, $sh, IMG_ARC_PIE);
	if(phpversion() >= 5){
		imagefilter( $image, IMG_FILTER_GAUSSIAN_BLUR);
	}
	$nbc = sizeof($colorz);
	$colorzi = array_rand($colorz,$nbc);
	
	$ci=0;
	// Allocation de quelques couleurs
	for($i=1 ; $i<=$nb ; $i++){
		if(!isset($colorzi[$ci])) $ci=0;
		$co = $colorz[$colorzi[$ci]];
		$ci++;
		$r = $co[0];
		$v = $co[1];
		$b = $co[2];
		$br = $r-50;
		$bv = $v-50;
		$bb = $b-50;
		if($br<0)$br=0;
		if($bv<0)$bv=0;
		if($bb<0)$bb=0;
		
		${"color$i"}  = imagecolorallocate($image, $r, $v, $b);
		${"dd$i"}  = imagecolorallocate($image, $br, $bv, $bb);
	}
	for ($y = 60; $y > 50; $y--) {
		$s=0;
		for($i=1 ; $i<=$nb ; $i++){	
		   imagefilledarc($image, 50, $y, 100, 50, $s, $s+$_GET["p$i"], ${"dd$i"}, IMG_ARC_PIE);
		   $s+=$_GET["p$i"];
		}
		
	}
	
	//imagefilledrectangle($image, 0, 0, 100, 50, $bg);
	$s=0;
	for($i=1 ; $i<=$nb ; $i++){
		imagefilledarc($image, 50, 50, 100, 50, $s, $s+$_GET["p$i"], ${"color$i"}, IMG_ARC_PIE);
		imagestring($image, 1, 1, 80+($i*10),$_GET["c$i"].' '.round($_GET["p$i"]/360*100,2).'%', ${"color$i"});
		$s+=$_GET["p$i"];
	}

	// Affichage de l'image
	header('Content-type: image/png');
	imagepng($image);

}
else{
	$image = imagecreatetruecolor(100,100);
	$bg  = imagecolorallocate($image, 255, 255, 255);
	$sh  = imagecolorallocate($image, 200, 200, 200);
	imagefilledrectangle($image, 0, 0, 100, 100, $bg);
	
	imagefilledarc($image, 50, 75, 100, 40, 0, 360, $sh, IMG_ARC_PIE);
	if(phpversion() >= 5){
		imagefilter( $image, IMG_FILTER_GAUSSIAN_BLUR);
	}
	imagefilledarc($image, 50, 60, 100, 50, 0, 360, $bg, IMG_ARC_PIE);
	imagearc ($image, 50, 60, 100, 50, 0,360, $sh);
	// Affichage de l'image
	header('Content-type: image/png');
	imagepng($image);
}
}
elseif($type=='courbe'){ /////////////////////////////////////////////////////////////////////// COURBE
	if(isset($_GET['tab'])){
		$width=400;
		if(isset($_GET['width'])) $width=$_GET['width'];
		$topy=abs($_GET['top']);
		$tab = explode(';',$_GET['tab']);
		if(sizeof($tab)>2){
			$cols = explode(',',$tab[0]);
			if(sizeof($cols)>2){
				$image = imagecreatetruecolor($width,170);
				$bg  = imagecolorallocate($image, 255, 255, 255);
				$gr  = imagecolorallocate($image, 200, 200, 200);
				$bl  = imagecolorallocate($image, 0, 0, 0);
				imagefilledrectangle($image, 0, 0, $width, 170, $bg);
				
				$nbc = sizeof($cols);
				$colw = $width/($nbc-1);
				$colorzi = array_rand($colorz,$nbc);
				
				foreach($cols as $l=>$col){
					imagedashedline ($image, 1+$l*$colw,10,1+$l*$colw, 165, $gr);
				}
				$ci=0;
				
				imagesetthickness ($image,3);
				$py=0;
				foreach($tab as $p=>$par){
					if($p>0){
						if(!isset($colorzi[$ci])) $ci=0;
						$co = $colorz[$colorzi[$ci]];
						$ci++;
						$r = $co[0];
						$v = $co[1];
						$b = $co[2];
						
						$color  = imagecolorallocate($image, $r, $v, $b);
						
						$lx=0;
						$ly=10;
						$cols = explode(',',$par);
						foreach($cols as $l=>$col){
							if($l==0){
								imagestring($image, 2, 3, 11+($py*10) ,$col, $gr);
								imagestring($image, 2, 2, 10+$py*10 ,$col, $color);
								$py++;
							}
							else{
								$nx=($l-1)*$colw;
								$ny=10+round(140-($col/$topy*140));
								if($l>1){
									 imageline($image, $lx, $ly, $nx, $ny, $color);
									 if($col==$topy){
										 imagestring($image, 2, $nx+2, 0 ,number_format($col,2,',',' '), $color);
									 }
								}
								$lx=$nx;
								$ly=$ny;
							}
						}
					}
				}
				$cols = explode(',',$tab[0]);
				foreach($cols as $l=>$col){
					imagestring($image, 2, 2+($colw*($l-1)), 155 ,$col, $bl);
				}
			}
			header('Content-type: image/png');
			imagepng($image);
		}
	}
}

?>