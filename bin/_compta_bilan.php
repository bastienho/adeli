<?php // 72 > Bilan comptable ;
	$type='facture';
	if(isset($_GET['type'])){
		$type = $_GET['type'];
	}
	$client='';
	if(isset($_GET['client'])){
		$client = $_GET['client'];
	}
		echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Bilan</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center' style='padding:10px'>
	<br><br>
	<form name='listage' action='./' method='get'>
	<input type='hidden' name='option' value='$option'>
	<input type='hidden' name='part' value='$part'>
	<input type='hidden' name='affdesac' value='$affdesac'>
	<table><tr>
	<td>";
	echo"
	Vendeur :
	<select></select>
	";
	
	echo"
	Client : 
	<select name='client' style='width:100px'><option value=''>Tous<option>";
		$ros = mysql_query("SELECT `id`,`nom` FROM `$clients_db` WHERE `nom`!='' $cliid ORDER BY `nom`");
		while($rows = mysql_fetch_object($ros)){
			$cid = $rows->id;
			$cno=$rows->nom;		
			$s="";
			if($client==$cid){
				$s="selected";
				$wheredb.=" AND `client`='$cid'";
			}
			echo"<option value='$cid' $s>$cno</option>";
		}
		echo"</select>
	Type : 
	<select name='type'>
			 <option value='devis'>Devis</option>
			 <option value='facture'>Factures</option>
			 <option value='commande'>Commandes</option>
	</select>
	</td>
	<td>Etat : <select name='affstat'>
	<option value='-1'>Tout</option>";
	for($s=0; $s<sizeof($defstat) ; $s++){
		echo"<option value='$s'>$defstat[$s]</option>";
	}
	if($_SESSION['affstat']>0){
	 $wheredb.=" AND `etat`='".$_SESSION['affstat']."'";
	}
	
	if(isset($_GET['du'])){
		$du = $_GET['du'];
	}
	else{
		$du = date("Y-m-").'01';
	}
	if(isset($_GET['au'])){
		$au = $_GET['au'];
	}
	else{
		$au = date("Y-m-d");
	}
	echo"</select></td>
	<td>Du : <input type='date' name='du' value='$du' size='10'></td>
	<td>Au : <input type='date' name='au' value='$au' size='10'></td>
	<td>actifs uniquement <input type='checkbox' name='acti' ";
		if($affdesac==1){
			echo"checked";
			$wheredb.=" AND `active`=1";
		}		
		echo" onclick='if(this.checked==true){document.listage.affdesac.value=1}else{document.listage.affdesac.value=0}'></td>
	<td><input type='submit' class='buttontd' value='ok'></td>
	</form>
	<script language='javascript'>
	document.listage.affstat.value='".$_SESSION['affstat']."';
	document.listage.type.value='$type';
	</script><table cellspacing='0' cellpadding='5' border='0' class='cadre' width='100%'>
	<tr>
		<td class='buttontd'>Type</td>
		<td class='buttontd'>Identifiant</td>
		<td class='buttontd'>Date</td>
		<td class='buttontd'>Client</td>
		<td class='buttontd'>Montant HT</td>
		<td class='buttontd'>Montant TTC</td>
		<td class='buttontd'>Commission</td>
		<td class='buttontd'>Vendeur </td>
	</tr>
	";
	$pdfs='';
	if(!isset($firescom) || $firescom==''){
		$firescom='vendeur';
	}
	$res = mysql_query("SELECT * FROM `$compta_base` WHERE `type`='$type' AND ((`date`>='$du' AND `date`<='$au'))  $wheredb  ORDER BY `date`DESC");
	$tot=0;
	$tht=0;
	$tcom=array();
	$tvat = array();
	$dcom=array();
	if($res && mysql_num_rows($res)>0){
		while($ro=mysql_fetch_object($res)){
			$ddate = strtotime($ro->date);
			$dacom = strtotime($ro->acomptele);
			$id = $ro->id;
			$code = $ro->code;
			$numero = $ro->numero;
			$acompte = $ro->acompte;
			$client = $ro->client;
			$pdfs.="$id,";
			$pluscli=',`id`';
			if($firescom!=''){
				$pluscli=" ,`$firescom`";	
			}
			$clicom='';
			$ros = mysql_query("SELECT `nom`$pluscli FROM `$clients_db` WHERE `id`='$client' AND `id`!='0'");
			if($ros && mysql_num_rows($ros)==1){
				$rows = mysql_fetch_array($ros);
				$client="<a href='./?option=worknet&clients&edit=$client'>".($rows[0])."</a>"; 
				$clicom = $rows[1];
				if($firescom!='' && isset($rdpend)){
					$clicom= get($rdpend[1],$rdpend[3],$clicom,$rdpend[2]);	
					$dcom[$clicom].="<tr><td colspan='5'><b><i>$client : $code$numero, ".date("d/m/Y",$ddate)."</i></b></td></tr>";	
				}
			}
			else{
				$client=$ro->adresse;
			}
			$mont=$ro->montant;
			$red=100;
			if($acompte>0){
				$red = 100-($acompte/$mont*100);
				$mont-=$acompte;				
			}
			$date = date("d/m/Y",$ddate);
			
			$content = split("<!>",$ro->content);
			$i=1;
			$lht = 0;
			$comi=0;
			while($i < sizeof($content) ){
				$lignecontent = split("<>",$content[$i]);
				$label = $lignecontent[0];
				$type = $lignecontent[1];
				$pu = $lignecontent[2];
				$tva = number_format($lignecontent[3],2);
				$quant = $lignecontent[4];
				$libre = $lignecontent[5];
				$ref = $lignecontent[6];
				$remise = $lignecontent[8];
				if($taxe=='HT'){
					$pt = $pu*$quant;
					$pt-= round($pt*$remise/100,2);
					$ptva = round($pt*$tva/100,2);
					$tvat[$tva] += $ptva;					
				}
				else{
					$pt = $pu*$quant;
					$pt-= round($pt*$remise/100,2);
					$ptva = round($pt-($pt/(1+($tva/100))),2);
					$tvat[$tva] += $ptva;
					$pt -= $ptva;
				}
				$com = get('gestion_artstock','com',$ref,'code');
				$comw = get('gestion_artstock','comw',$ref,'code');
				$pa='';
				if($com>0){
					if($comw==1){
						$comid = get('gestion_artstock','id',$ref,'code');
						$pa = get('gestion_artstock','priw_rev',$ref,'code');
						if($pa=='' && $pa==NULL){
							$rys = mysql_query("SELECT `prix`,`remise` FROM `gestion_artfour` WHERE `art`='$comid' ORDER BY `prix` LIMIT 0,1");
							if($rys && mysql_num_rows($rys)==1){
								$ry = mysql_fetch_array($rys);
								$pa = ($ry[0]-($ry[0]*$ry[1]/100));
							}
						}
						if($pa!='' && $pa!=NULL){
							$pa*=$quant;
							$comi += ($pt-$pa)*$com/100;
							if($firescom!=''){
								$arid = get('gestion_artstock','ref',$ref,'code');
								$dcom[$clicom].="<tr><td><a href='./?option=gestion&part=gestion_articles&edit=$arid'>$ref</a>  </td><td>$label </td><td align='left'>".prix($pu)." &euro;</td><td align='center'>$quant</td><td>$com% (bénéfice)</td><td align='right'>".prix(($pt-$pa)*$com/100)." &euro;</td></tr>";	
							}
						}
					}
					else{						
						$comi += $pt*$com/100;
						if($firescom!=''){
							$dcom[$clicom].="<tr><td>$ref</td><td>$label</td><td align='left'>".prix($pu)." &euro;</td><td align='center'>$quant</td><td>$com% (prix de vente)</td><td align='right'>".prix(($pt)*$com/100)." &euro;</td></tr>";	
						}
					}
				}
				//$lht+=($pt*$red/100);	
				
				$i++;
			}	
			if($firescom!=''){
				$tcom[$clicom]+=$comi;	
			}
			echo"<tr>
				<td class='colone'>Solde</td>
				<td class='colone'>$code $numero</td>
				<td class='colone'>$date</td>
				<td class='colone'>$client</td>
				<td class='colone' align='right'>".prix($lht)." </td>
				<td class='colone' align='right'>".prix($mont)."</td>
				<td class='colone' align='right'>";
				if($comi !=NULL ) echo prix($comi);
				echo"</td>
				<td class='colone' align='right'>$clicom</td>
			</tr>";
			$tot+=$mont;
			$tht+=$lht;		
		}
	}
		echo"</table>
		<table>
		<tr><td colspan='2'>R&eacute;capitulatif de la p&eacute;riode</td></tr>
		<tr><td>TTC : </td><td align='right'><b>".prix($tot)." &euro;</b></td></tr>
		";
		$ttva=0;
		foreach($tvat as $t=>$v){
			if($v>0){
				$ttva+=$v;
				echo"<tr><td>TVA $t% : </td><td align='right'><b>".prix($v)." &euro;</b></td></tr>";
			}
		}
		echo"<tr><td>HT : </td><td align='right'><b>".prix($tot-$ttva)." &euro;</b></td></tr>
		<tr><td colspan='2'>
		<a href='#' onclick=\"javascript:pdf=open('$openpdf&mkpdf=$pdfs','pdf','width=400,height=500,scrollbars=1,resizable=1,top='+((screen.height-500)/2)+',left='+((screen.width-400)/2));pdf.focus();\" class='info'><img src='http://www.adeli.wac.fr/icos/pdf.gif' border='none' alt='éditer'> Tout télécharger</a>
		<br><br><hr>Comissions de la période:</td></tr>";
		foreach($tcom as $t=>$v){
			if($v>0){
				if($t=='') $t="Non identifié";
				echo"<tr><td valign='top'><a href='#$t'><b>".$t." : </b></a></td><td align='right'><b>".prix($v)." &euro;</b></td><td>PDF</td></tr>";
			}
		}
		echo"
		</td></tr>
		</table>
		
		<br><br><br>
		
		<hr>Détail des comissions :<br><br>
		";
		foreach($tcom as $t=>$v){
			if($v>0){
				if($t=='') $t="Non identifié";
				echo"<br><a name='$t'></a><b>".$t."</b>";
				if($v>0 && $dcom[$t]!=''){
				echo"<table width='100%'><tr><td>Code</td><td>Désignation</td><td align='left'>PU</td><td align='center'>Quant.</td><td>Commission</td><td align='right'>Total</td></tr>
				".$dcom[$t]."
				</table>";
				}
			}
		}
		echo"
		
		
		
		
		";
?>