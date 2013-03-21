<?php // 7 > LaLIE Traceur ;
if(isset($_GET['r']) && isset($_GET['m'])){
	$ref = abs($_GET['r']);
	$email = $_GET['m'];	
	$lalierp="LaLIE_rapports";	
	
	include('../mconfig/adeli.php');
	include('inc_func.php');
	
	$conn = mysql_connect($dhost, $dlogin, $dpasse);
	mysql_select_db($dbase);
	
	if(false !== $res=mysql_query("SELECT `rapport` FROM `$lalierp` WHERE `id`='$ref'")){
		$ro  = mysql_fetch_array($res);
		if(strpos($ro[0],"$email</div>")>-1){
		 $date=date("d/m/Y H:i:s");
		 $unik = "ip: ".getenv("REMOTE_ADDR");
		 $nav = $_SERVER['HTTP_USER_AGENT'];
		 
		 $rapp = "<!--$date<>$unik<>$nav-->";
		 $nr = str_replace("'","''",str_replace(">$email</div>",">$email$rapp</div>",$ro[0]));
		 if($nr){
			 mysql_query("UPDATE  `$lalierp` SET `rapport`='$nr' WHERE `id`='$ref'");
		 }
		}
	}
}
$des = imagecreatetruecolor (1,1) ;
$col = imagecolorallocate($des,255,255,255);
imagefill($des,0,0,$col);
header("Content-Type:  image/png");
imagepng($des);
imagedestroy($des);
?>