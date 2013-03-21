<?php // 3 > code barre EAN ;
class debora{
	var $arryGroup = array('A' => array(
				0 => "0001101", 1 => "0011001",
				2 => "0010011",	3 => "0111101",
				4 => "0100011",	5 => "0110001",
				6 => "0101111",	7 => "0111011",
				8 => "0110111",	9 => "0001011"
				),
				'B' => array(
				0 => "0100111",	1 => "0110011",
				2 => "0011011",	3 => "0100001",
				4 => "0011101",	5 => "0111001",
				6 => "0000101",	7 => "0010001",
				8 => "0001001",	9 => "0010111"
				),
				'C' => array(
				0 => "1110010",	1 => "1100110",
				2 => "1101100",	3 => "1000010",
				4 => "1011100",	5 => "1001110",
				6 => "1010000",	7 => "1000100",
				8 => "1001000",	9 => "1110100"
				)
				);
	
	var $arryFamily = array(
					0 => array('A','A','A','A','A','A'),
					1 => array('A','A','B','A','B','B'),
					2 => array('A','A','B','B','A','B'),
					3 => array('A','A','B','B','B','A'),
					4 => array('A','B','A','A','B','B'),
					5 => array('A','B','B','A','A','B'),
					6 => array('A','B','B','B','A','A'),
					7 => array('A','B','A','B','A','B'),
					8 => array('A','B','A','B','B','A'),
					9 => array('A','B','B','A','B','A')
					);
	
	function debora($EAN13){
		settype($EAN13,'string');
		for($i=0;$i<13;$i++)
		{
			$this->EAN13[$i] = substr($EAN13,$i,1);
		}
		
		$this->strCode = $this->makeCode();
	}
	
	function makeCode(){
		$arryLeftClass = $this->arryFamily[$this->EAN13[0]];
		$strCode = '101';
		for ($i=1; $i<7; $i++){
			$strCode .= $this->arryGroup[$arryLeftClass[$i-1]][$this->EAN13[$i]];
		}
		$strCode .= '01010';
		for ($i=7; $i<13; $i++)
		{
			$strCode .= $this->arryGroup['C'][$this->EAN13[$i]];
		}
		$strCode .= '101';		
		return $strCode;
	}
	
	
	function makeImage($imageType="png"){
		$img=imagecreate(120, 70);
		
		$color[0] = ImageColorAllocate($img, 255,255,255);
		$color[1] = ImageColorAllocate($img, 0,0,0);
		
		$coords[0] = 15;
		$coords[1] = 10;
		$coords[2] = 1;
		$coords[3] = 40;
		
		imagefilledrectangle($img, 0, 0, 95, 80, $color[0]);
		
		for($i=0;$i<strlen($this->strCode);$i++)
		{
			$posX = $coords[0];
			$posY = $coords[1];
			$intL = $coords[2];
			$intH = $coords[3];
			
			$fill_color = substr($this->strCode,$i,1);
			
			if ($i < 3 || ($i >= 46 && $i < 49) || $i >= 92) {
				$intH = $intH + 8;
			}
			
			imagefilledrectangle($img, $posX, $posY, $posX, ($posY+$intH), $color[$fill_color]);
			$coords[0] = $coords[0] + $coords[2];
		}
		
		imagestring($img, 3, 7, 50, $this->EAN13[0], $color[1]);
		imagestring($img, 3, 19, 50, implode('', array_slice($this->EAN13,1, 6)), $color[1]);
		imagestring($img, 3, 65, 50, implode('', array_slice($this->EAN13,7)), $color[1]);
		
		Header( "Content-type: image/".$imageType); 
		
		$func_name = 'image'.$imageType;
		
		$func_name($img); 
		imagedestroy($img); 
	}	
}
if(isset($_GET['ean'])){
	$ean = $_GET['ean'];
	if($ean != ''){
		$objCode = new debora($_GET['ean']);
		$objCode->makeImage();
	}
	else{
		$img=imagecreate(120, 70);
		$color = ImageColorAllocate($img, 200,200,200);
		imagefilledrectangle($img, 0, 0, 120, 70, $color);
		imagejpeg($img); 
		imagedestroy($img); 			
	}
}
?>