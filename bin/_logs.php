<?php // 9 > Surveillance d'activité sur Adeli ;
echo"<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Configuration des Logs</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'><br><br>
	<form action='./?logs' method='post'>
	Conserver les logs d'activité pendant <br>
	<input type='text' size='2' name='setlogs' value='$life_log'> semaines<br>
	<input type='submit' value='ok'>
	<br><br>
</form>
</td></tr></table><br><br>

<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Logs d'activité</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='left'><br><br>";
	if(is_dir("mconfig/logs")){
		if(isset($_GET['view']) && is_file("mconfig/logs/".$_GET['view'].".log")){
			echo"<a href='./?option=$option&part=$part'>retour</a><hr>";
			$fp = fopen("mconfig/logs/".$_GET['view'].".log","r");
			while(!feof($fp)){
				echo str_replace('<script','<!--',str_replace('</script>','-->',fgets($fp, 4096)));				
			}
			fclose($fp);
		}
		else{
			$sem = date("YW");
			$limilfe = date("YW",strtotime("-$life_log Weeks"));
			$dir=dir("mconfig/logs");
			while(false !== $entry = $dir->read()){
				if(is_file("mconfig/logs/$entry")){
					echo "<a href='./?option=$option&part=$part&view=".str_replace('.','',substr($entry,0,6))."'>".substr($entry,0,4).", semaine ".substr($entry,4,2)."</a><br>";
				}
			}
		}
	}
	
	echo"</td></tr></table>";
?>