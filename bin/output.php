<?php // 8 > Aperçu d'exportation ;
$fp=fopen("tmp/outpumim","rb");
		fseek($fp,0);
		$outpumim = fread($fp,filesize("tmp/outpumim"));
		$fp=fopen("tmp/outputxt","rb");
		fseek($fp,0);
		$outputxt = fread($fp,filesize("tmp/outputxt"));
		$fp=fopen("tmp/outpufi","rb");
		fseek($fp,0);
		$outpufi = fread($fp,filesize("tmp/outpufi"));
		fclose($fp);
		
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Type: $outpumim");
		header("Content-Disposition: inline; filename=$outpufi;" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".strlen($outputxt));
	if(isset($_GET['print'])){
		echo"<input type='button' onclick=\"self.print()\" value='imprimer'><hr>";	
	}

	echo $outputxt; 

	if(isset($_GET['print'])){
		echo"
		<script language='javascript'>
		self.print();		
		</script>
		
		<hr>";	
	}
	exit();
?>