<?php // 15 > Statistiques comptables ;


echo"
	<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr><td valign='top' class='buttontd' width='150'><span class='gras'>$part</span></td>";
	$modtri = array("etat","code","client","date");
	$tri=$modtri[0];
	if(isset($_GET["tri"])){
		$tri=$_GET["tri"];
	}
	for($t=0 ; $t<sizeof($modtri) ; $t++){
		if($tri==$modtri[$t]){
			echo"<td valign='top' class='menuselected' width='100'><a href='./?option=$option&part=$part&tri=$modtri[$t]'>$modtri[$t]</a></td>";
		}
		else{
			echo"<td valign='top' class='buttontd' width='100'><a href='./?option=$option&part=$part&tri=$modtri[$t]'>$modtri[$t]</a></td>";
		}
	}
	$colstre = 2+sizeof($modtri);
	
	echo"<td class='buttontd' style='text-align:right'>
	n'afficher que les actifs ";
		if($affdesac==0){
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&tri=$tri&affdesac=1&tri=$tri'\">";
		}
		else{
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&tri=$tri&affdesac=0&tri=$tri'\" checked>";
			 $wheredb.="AND `active`=1";
		}		
		echo"
	</td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='$colstre' align='center'>
	<form name='listage'>
	Afficher : <select name='affstat' onchange=\"document.location='./?option=$option&part=$part&tri=$tri&affstat='+this.value;\">
	<option value='-1'>tout</option>";
	for($s=0; $s<sizeof($defstat) ; $s++){
		echo"<option value='$s'>$defstat[$s]</option>";
	}
	echo"</select></form>
	<script language='javascript'>
	document.listage.affstat.value='".$_SESSION['affstat']."';
	</script>
	";
	
	$expr="`$tri`";
	if($tri=="date"){
		$class_d = "an";
		if(isset($_GET["class_d"])){
			$class_d=$_GET["class_d"];
		}
		echo"filtrer par :";
		if($class_d=="an"){
			$expr="EXTRACT(YEAR FROM `$tri`)";
			echo"<u>Année</b> | <a href='./?option=$option&part=$part&tri=$tri&class_d=mois'>Mois</a>";
		}
		if($class_d=="mois"){
			$expr="EXTRACT(YEAR_MONTH FROM `$tri`)";
			echo"<a href='./?option=$option&part=$part&tri=$tri&class_d=an'>Année</a> | <u>Mois</u>";
		}
	}
	$cods=array();
	$tauto=0;
	$res= mysql_query("SELECT `id` FROM $compta_base");
	$nbrestot = mysql_num_rows($res);
	$nbstat=0;
	
	$mk = array_keys($menu);
	echo"<table>";
	//print_r($menu);
		for($m=0 ; $m<sizeof($menu[$mk[0]]) ; $m++){
			$spart = $menupart[$m];
			$formula = $menu[$mk[0]][$spart];
			//echo" $spart $formula -- ";

			if($formula!=''){
			$res= mysql_query("SELECT SUM(`montant`),COUNT(`id`) FROM `$compta_base` WHERE `type`='$formula'  $wheredb");
			$row=mysql_fetch_array($res);
			$totres = $row[0];
			$nbres = $row[1];		
			
			if($nbres > 0){
			$nbstat++;
			$res= mysql_query("SELECT DISTINCT $expr,SUM(`montant`),COUNT($expr) FROM `$compta_base` WHERE `type`='$formula'  $wheredb GROUP BY $expr ORDER BY $expr");
			
			echo"<tr><td colspan='6' class='buttontd'><a href='./?option=$option&$formula'><span class='gras'>$spart</span></a>
			<br>
			<span class='petittext'>$nbres enregistrement(s) / ".prix($totres)."&euro;</span></td></tr>";	
					
			
			$vrb='';
			$panelcode=array();			
			$thisgroupe=array();
			while($row=mysql_fetch_array($res)){
				$code = $row[0];
				$taut = $row[1];
				$nbo = $row[2];
				$exp="AND `$tri`='$code'";
				if($tri=="date"){
					if($class_d=="an"){
						$code = substr($code,0,4);
					}
					if($class_d=="mois"){
						$code = substr($code,0,4)."-".substr($code,4,2);						
					}
					$exp="AND `$tri`LIKE'$code%'";
				}
				$prc = round($taut/$totres*100);
				if($code==""){
					$afcode = $code="$tri vide";
				}
				elseif($tri=="etat"){
					$afcode = "<a href='./?option=$option&$formula&affstat=$code'>$defstat[$code]</a>";
					$code = $defstat[$code];
				}
				elseif($tri=="client" && is_numeric($code)){
					$ros = mysql_query("SELECT `nom` FROM `$clients_db` WHERE `id`='$code'");
					$rows = mysql_fetch_object($ros);					
					$afcode = "<a href='./?option=worknet&clients&edit=$code'>".($rows->nom)."</a>";
					$code=$rows->nom;
				}
				elseif($tri=="date"){
					if($class_d=="an"){
						$afcode = $code = " ".substr($code,0,4);
					}
					if($class_d=="mois"){
						$afcode = $code = $NomDuMois[abs(substr($code,5,2))]." ".substr($code,0,4);
					}
				}
				else{
					$afcode = $code;
				}
				if($prc > 2){
					$cods[$code]["num"]+=$nbo;
					$cods[$code]["tot"]+=$taut;
					
					$thisgroupe[$afcode]["num"]+=$nbo;
					$thisgroupe[$afcode]["tot"]+=$taut;					
					$thisgroupe[$afcode]["prc"]+=$prc;
					
					$nbg++;								
				}
				else{
					$cods["autres"]["num"]+=$nbo;
					$cods["autres"]["tot"]+=$taut;
					$panelcode["num"]+=$nbo;
					$panelcode["tot"]+=$taut;
					$panelcode["prc"]+=$prc;
					
					$thisgroupe["autres"]["num"]+=$nbo;
					$thisgroupe["autres"]["tot"]+=$taut;										
					$thisgroupe["autres"]["prc"]+=$prc;	
				}
				$tauto+=$taut;
					
				
			}
			$u=0;
			foreach ($thisgroupe as $key => $row) {
				$numa[$u]  = $row["num"];
				$tota[$u] = $row["tot"];
				$prca[$u] = $row["prc"];
				$u++;
			}
			
			if(array_multisort($tota, SORT_DESC, $prca, SORT_DESC, $numa, SORT_DESC, $thisgroupe)){
				//echo"$formula : pas dans l'ordre ";
			
			
				$coda = array_keys($thisgroupe);
				$siz = 	sizeof($thisgroupe);
				$nbg=0;
				for($s=0 ; $s<$siz ; $s++){
					$code = $coda[$s];
					$nbg++;
					$nbo = $thisgroupe[$code]["num"];
					$tto = $thisgroupe[$code]["tot"];					
					$prc = $thisgroupe[$code]["prc"];
					$vrb.="&c$nbg=".urlencode(strip_tags($code))."&p$nbg=".($prc*360/100);
					$empty = 100-$prc;
					echo"<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>$code</b</td>
					<td class='petittext'>$nbo</td>
					<td class='petittext'><img src='$style_url/$theme/red.gif' height='5' width='$prc'><img src='$style_url/$theme/gray.gif' height='5' width='$empty'>
					<td class='petittext' >$prc %</td>
					<td align='right'>".prix($tto)."&euro;</td>
					</tr>";	
				} 
				$thisgroupe=array();
				if(is_file('bin/_graphique.php')){				
					echo"<tr><td colspan='6' align='center'><img src='bin/_graphique.php?nb=$nbg$vrb'></td></tr>";
				}
			}	
			else{
					echo"<tr><td colspan='6' align='center'>Erreur lors de l'analyse...</td></tr>";
			}
			
			}
			}
		}
	
	if($nbstat>1){
	foreach ($cods as $key => $row) {
		$num[$key]  = $row["num"];
		$tot[$key] = $row["tot"];
	}
	
	array_multisort($tot, SORT_DESC, $num, SORT_DESC, $cods);
	$coda = array_keys($cods); 	
	echo"<tr><td colspan='6' class='buttontd'><b>total :</b><br>
	<span class='petittext'>$nbrestot enregistrement(s) / ".prix($tauto)."&euro;</span></td></tr>";
	
	$nbg=0;
	$vrb="";
	$siz = 	sizeof($cods);
	for($m=0 ; $m<$siz ; $m++){
		$code = $coda[$m];
		$nbo = $cods[$code]["num"];
		$tto = $cods[$code]["tot"];
		$prc = round($nbo/$nbrestot*100);
		$prct = round($tto/$tauto*100);
		$empty = 100-$prc;
		$emptt = 100-$prct;
		$nbg++;
		$vrb.="&c$nbg=".urlencode(strip_tags($code))."&p$nbg=".($prct*360/100);
		echo"<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>$code</b</td>
		<td class='petittext'>$nbo</td>
		<td class='petittext'><img src='$style_url/$theme/red.gif' height='5' width='$prc'><img src='$style_url/$theme/gray.gif' height='5' width='$empty'>
		$prc%</td>
		<td class='petittext' align='right'><b>".prix($tto)."&euro;</b></td>
		<td class='petittext'><img src='$style_url/$theme/red.gif' height='5' width='$prct'><img src='$style_url/$theme/gray.gif' height='5' width='$emptt'>
		$prct%</td>
		</tr>";
	}
	if(is_file('bin/_graphique.php')){
		echo"<tr><td colspan='6' align='center'><img src='bin/_graphique.php?nb=$nbg$vrb'></td></tr>";
	}	
	
	}
	echo"</table>";
	echo"</div>";
?>