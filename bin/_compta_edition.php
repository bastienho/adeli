<?php // 109 > Editeur de factures, commandes et devis ;

$remisetype=array('RMP'=>'%','RMF'=>'EUR');
$remiseeten=array('globale','article');
$edit = $_GET['edit'];
/////////////// CREATION
if((isset($_GET['freecontent'])&& !isset($_GET['merge'])) || !isset($_SESSION['pdfact'])){
	$_SESSION['pdfact']=array();
	$_SESSION['uid'] = 0;
	$_SESSION['numero'] = '';
	$_SESSION['client'] = '';
	$_SESSION['adresse'] = '';
	$_SESSION['type'] = $part;
	$_SESSION['code'] = '';
	$_SESSION['intitule']='';
	$_SESSION['date']=date("Y-m-d");
	$_SESSION['acompte']=0;
	$_SESSION['acomptele'] = '';
	$_SESSION['etat'] = 0;
	$_SESSION['mode'] = '';
	$_SESSION['expedition'] = '';
	$_SESSION['devise'] = 'EUR';
}
/////////////// OUVERTURE SIMPLE
if(isset($_GET['edit']) && isset($_GET['getcontent']) ){
	$result = mysql_query("SELECT * FROM $compta_base WHERE id='$edit'");
	$row = mysql_fetch_object($result);
		$_SESSION['pdfact']=array();
		$_SESSION['uid'] = $edit;
		$_SESSION['numero'] = $row->numero;
		$_SESSION['client'] = $row->client;
		$_SESSION['adresse'] = $row->adresse;
		$_SESSION['type'] = $row->type;
		$_SESSION['code'] = $row->code;
		$_SESSION['intitule']=$row->intitule;
		$_SESSION['date']=$row->date;
		$_SESSION['acompte']=$row->acompte;
		$_SESSION['acomptele'] = $row->acomptele;
		$_SESSION['etat'] = $row->etat;
		$_SESSION['mode'] = unquote($row->mode,"'");
		$_SESSION['expedition'] = $row->expedition;
		$_SESSION['devise'] = $row->devise;
		$content = explode("<!>",$row->content);
		if(trim($_SESSION['devise'])=='')$_SESSION['devise']='EUR';
		$i=1;
		while($i < sizeof($content) ){
			$lignecontent = explode("<>",$content[$i]);
			$_SESSION['pdfact'][$i-1]["label"] = $lignecontent[0];
			$_SESSION['pdfact'][$i-1]["type"] = $lignecontent[1];
			$_SESSION['pdfact'][$i-1]["pu"] = $lignecontent[2];
			$_SESSION['pdfact'][$i-1]["tva"] = $lignecontent[3];
			$_SESSION['pdfact'][$i-1]["quant"] = $lignecontent[4];
			$_SESSION['pdfact'][$i-1]["libre"] = $lignecontent[5];
			$_SESSION['pdfact'][$i-1]["coderef"] = $lignecontent[6];
			$_SESSION['pdfact'][$i-1]["hidden"] = $lignecontent[7];
			$_SESSION['pdfact'][$i-1]["remise"] = $lignecontent[8];
			$i++;
		}
		if(isset($_GET['clon'])){
			$edit='';
			if(isset($_GET['type'])){
				$_SESSION['type'] = $_GET['type'];
			}
			$_SESSION['uid'] = 0;
			$_SESSION['numero'] = '';
			$_SESSION['date']=date("Y-m-d");
		}
}
/////////////// CONCATENATION
elseif(isset($_GET['edit']) && isset($_GET['freecontent']) && isset($_GET['merge'])){
	$wereid="id=0 ";
	$listid="0";
	foreach($_POST as $keyname=>$value) {
		$tid=substr($keyname,3,strlen($keyname));
		$wereid.=" OR `id`='$tid'";
		$listid.=",$tid";
	}
	$result = mysql_query("SELECT * FROM $compta_base WHERE $wereid");
	$p=0;
	$_SESSION['pdfact']=array();
	$_SESSION['acompte']=0;
	$_SESSION['acomptele'] = '';
	$_SESSION['etat'] = 0;
	$_SESSION['mode'] = '';
	$_SESSION['listid']=$listid;
	$_SESSION['actionfin']='ig';
	$_SESSION['expedition'] ='';
	$_SESSION['uid'] = 0;
	$_SESSION['numero'] = '';
	while($row = mysql_fetch_object($result)){
		$_SESSION['client'] = $row->client;
		$_SESSION['adresse'] = $row->adresse;
		$_SESSION['type'] = $row->type;
		$_SESSION['code'] = $row->code;
		$_SESSION['intitule']=$row->intitule;
		$_SESSION['devise'] =$row->devise;
		$_SESSION['date']=date("Y-m-d");
		$content = explode("<!>",$row->content);
		if($_SESSION['devise']=='')$_SESSION['devise']='EUR';
		$i=1;
		while($i < sizeof($content) ){
			$lignecontent = explode("<>",$content[$i]);
			$_SESSION['pdfact'][($i+$p)-1]["label"] = $lignecontent[0];
			$_SESSION['pdfact'][($i+$p)-1]["type"] = $lignecontent[1];
			$_SESSION['pdfact'][($i+$p)-1]["pu"] = $lignecontent[2];
			$_SESSION['pdfact'][($i+$p)-1]["tva"] = $lignecontent[3];
			$_SESSION['pdfact'][($i+$p)-1]["quant"] = $lignecontent[4];
			$_SESSION['pdfact'][($i+$p)-1]["libre"] = $lignecontent[5];
			$_SESSION['pdfact'][($i+$p)-1]["coderef"] = $lignecontent[6];
			$_SESSION['pdfact'][($i+$p)-1]["hidden"] = $lignecontent[7];
			$_SESSION['pdfact'][($i+$p)-1]["remise"] = $lignecontent[8];
			$i++;
		}
		$p+=$i;
	}
}


$keyfact=array_keys($_SESSION['pdfact']);
$nbline = sizeof($_SESSION['pdfact']);

if(isset($_GET['a'])){
	$_SESSION['adresse']=stripslashes($_POST['adresse']);
	$_SESSION['intitule']=stripslashes($_POST['intitule']);
	if(isset($_POST['actionfin'])){
		$_SESSION['actionfin']=$_POST['actionfin'];
	}
	$_SESSION['numero']=$_POST['numero'];
	$_SESSION['type']=$_POST['type'];
	$_SESSION['client']=$_POST['client'];
	$_SESSION['code'] = $_POST['code'];
	$_SESSION['date'] = $_POST['date'];
	$_SESSION['acompte']=$_POST['acompte'];
	$_SESSION['acomptele'] = $_POST['acomptele'];
	$_SESSION['etat']=$_POST['etat'];
	$_SESSION['mode'] = $_POST['mode'];
	$_SESSION['expedition'] = $_POST['expedition'];
	$_SESSION['devise'] = $_POST['devise'];
	for($i=0 ; $i<$nbline ; $i++){		
		$idlinefact = $keyfact[$i];		
		$_SESSION['pdfact'][$idlinefact]["label"] = stripslashes($_POST["label$idlinefact"]);
		$_SESSION['pdfact'][$idlinefact]["pu"] = number_format(ereg_replace(",",".",$_POST["pu$idlinefact"]),2, '.', '');
		$_SESSION['pdfact'][$idlinefact]["type"] = $_POST["type$idlinefact"];
		$_SESSION['pdfact'][$idlinefact]["tva"] = $_POST["tva$idlinefact"];
		$_SESSION['pdfact'][$idlinefact]["quant"] = $_POST["quant$idlinefact"];
		$_SESSION['pdfact'][$idlinefact]["libre"] = $_POST["libre$idlinefact"];
		$_SESSION['pdfact'][$idlinefact]["coderef"] = $_POST["coderef$idlinefact"];
		$_SESSION['pdfact'][$idlinefact]["hidden"] = $_POST["hidden$idlinefact"];
		$_SESSION['pdfact'][$idlinefact]["remise"] = $_POST["remise$idlinefact"];
	}
}
$keyfact=array_keys($_SESSION['pdfact']);
$nbline = sizeof($_SESSION['pdfact']);

$part = $_SESSION['type'];

echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr>
		<td class='buttontd' style='text-align:left' width='150'><a href='./?option=$option&part=$part'>Retour &agrave; la liste</a></td>
		<td valign='top' class='menuselected' width='150'><span class='gras'>&Eacute;dition</span></td>
		<td class='buttontd' style='text-align:right'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='3' align='left'>
";


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ENREGISTREMENT
if(isset($_GET['valid'])){
	if($_SESSION['adresse']!='' && $_SESSION['intitule']!='' && $nbline>0){
		echo"enregistrement ";
		
	if(abs($_SESSION['numero'])==0){
		$numero = getnext($compta_base,'numero',"WHERE `type`='$part'");
		$_SESSION['numero']=$numero;
	}	
	if(abs($_SESSION['uid'])==0){		
		if(mysql_query("INSERT INTO `$compta_base` (`numero`,`code`, `type`,`client`, `adresse`, `intitule`, `date`, `acompte`, `acomptele`, `etat`, `mode`,`expedition`,`devise`) VALUES('$numero','$code', '$type','$client', '".str_replace("'","''",$adresse)."', '".str_replace("'","''",$intitule)."', '$date', '$acompte', '$acomptele', '$etat', '$mode','$expedition','$devise')")){
			$_SESSION['uid']=mysql_insert_id();			
		}
		else{
			echo"&eacute;chou&eacute;...";
		}
	}
	
	if(abs($_SESSION['uid'])>0){
		$contentruc = '';
		$tot=0;
		for($i=0 ; $i<$nbline ; $i++){
			$idlinefact = $keyfact[$i];			
			$label = $_SESSION['pdfact'][$idlinefact]["label"];
			$typo = $_SESSION['pdfact'][$idlinefact]["type"];
			$pu = $_SESSION['pdfact'][$idlinefact]["pu"];
			$tva = $_SESSION['pdfact'][$idlinefact]["tva"];
			$quant = $_SESSION['pdfact'][$idlinefact]["quant"];
			$libre = $_SESSION['pdfact'][$idlinefact]["libre"];	
			$coderef = $_SESSION['pdfact'][$idlinefact]["coderef"];	
			$hidden = $_SESSION['pdfact'][$idlinefact]["hidden"];	
			$remise = $_SESSION['pdfact'][$idlinefact]["remise"];			
			$contentruc .= str_replace("'","''","<!>$label<>$typo<>$pu<>$tva<>$quant<>$libre<>$coderef<>$hidden<>$remise");	
			if($taxe=='HT'){
				if($remise_app==1) $pt =  round($pu*(100-$remise)/100,2)*$quant; 
				else $pt = $pu*$quant-($pu*$quant*$remise/100);
				$pt = number_format( $pt ,2, '.', '');
				$ptva = round($pt*$tva/100,2);
				$totva += $ptva;
				$tottc += $pt+$ptva;
				$tot+=$pt;
			}
			else{
				if($remise_app==1) $pt = $pu*$quant-round($pu*$quant*$remise/100,2)  ;
				else $pt = $pu*$quant-($pu*$quant*$remise/100); 
				$pt = number_format( $pt ,2, '.', '');
				$ptva = round($pt-($pt/(1+($tva/100))),2);
				$totva += $ptva;
				$tottc += $pt;
				$tot += $pt-$ptva;
			}
		}
		
		$ttct = $tottc-$acompte;
		echo", ajout des donn&eacute;es ";
		if(mysql_query("UPDATE `$compta_base` SET `numero`='".$_SESSION['numero']."',`code`='".$_SESSION['code']."', `type`='".$_SESSION['type']."', `client`='".$_SESSION['client']."', `adresse`='".str_replace("'","''",$_SESSION['adresse'])."', `intitule`='".str_replace("'","''",$_SESSION['intitule'])."', `content`='$contentruc', `date`='".$_SESSION['date']."', `montant`='$tottc', `acompte`='".$_SESSION['acompte']."', `acomptele`='".$_SESSION['acomptele']."', `etat`='".$_SESSION['etat']."', `mode`='".$_SESSION['mode']."', `active`=1, `expedition`='".str_replace("'","''",$_SESSION['expedition'])."', `devise`='".str_replace("'","''",$_SESSION['devise'])."' WHERE id='".$_SESSION['uid']."'")){
		$retou.="";
		if(isset($_POST['refid'])){
			$retou="Affectation des anciens documents: <br>";
			$listid=$_POST['refid'];
			$actionfin=$_POST['actionfin'];
			$listeid = explode(',',$listid);
			$nbid = sizeof($listeid)-1;
			if($nbid>0){
				$wereid="id=0 ";
				for($n=1 ; $n<$nbid+1 ; $n++){
					$wereid.=" OR `id`='$listeid[$n]'";
				}
				switch($actionfin){
					case 'de':
						if(mysql_query("UPDATE `$compta_base` SET `active`=0 WHERE $wereid"))  $retou.="Les $nbid documents utilis&eacute;s ont bien &eacute;t&eacute; d&eacute;sactiv&eacute;s";
						else $retou.="Les $nbid documents utilis&eacute;s n'ont pu être d&eacute;sactiv&eacute;s";
					break;
					case 'an':
						if(mysql_query("UPDATE `$compta_base` SET `etat`=4 WHERE $wereid"))  $retou.="Les $nbid documents utilis&eacute;s ont bien &eacute;t&eacute; annul&eacute;s";
						else $retou.="Les $nbid documents utilis&eacute;s n'ont pu être annul&eacute;s";
					break;
					case 'su':
						if(mysql_query("DELETE FROM `$compta_base` WHERE $wereid"))  $retou.="Les $nbid documents utilis&eacute;s ont bien &eacute;t&eacute; supprim&eacute;s";
						else $retou.="Les $nbid documents utilis&eacute;s n'ont pu être supprim&eacute;s";
					break;
					default:
						$retou.="Les $nbid documents utilis&eacute;s ont &eacute;t&eacute; conserv&eacute;s";
					break;
				}
				if($retou!="") $retou="<br>$retou<br><br>";
			}
		}
			echo"<hr><br>
		Enregistrement effectu&eacute; avec succ&egrave;s pour votre $part n&deg;".$_SESSION['code'].$_SESSION['numero'].".<br>$retou
		Vous pouvez maintenant:<br><br>
		< <a href='./?option=$option&part=$part&edit=".$_SESSION['uid']."&getcontent'>Modifier le document</a><br>
		+ <a href='#' onclick=\"javascript:open('$openpdf&mkpdf=".$_SESSION['uid']."','pdf','width=400,height=500,scrollbars=1,resizable=1,top='+((screen.height-500)/2)+',left='+((screen.width-400)/2))\"><img src='http://www.adeli.wac.fr/icos/pdf.gif' border='none' alt='PDF'> Cr&eacute;er le pdf </a><br>		
		";
		echo"<br>- <a href='./?option=$option&part=$part'>Revenir &agrave; la liste</a><br><br>";
		if($_SESSION['client']!=0 && $_SESSION['client']!=NULL){
			echo"
			+ <a href='./?option=worknet&part=$clients_db&edit=".$_SESSION['client']."'>Acc&eacute;der au compte</a><br>";
			if(mysql_query("SHOW COLUMNS FROM adeli_messages") && $_SESSION['expedition']!=''){
				echo"
				> <a href='./?option=worknet&adeli_messages&edit&dest=".$_SESSION['client']."&sujet=Exp&eacute;dition de votre commande ".$_SESSION['code']."".$_SESSION['uid']."&message=".urlencode(str_replace('<EXP>',$_SESSION['expedition'],$message_exp))."'><b><img src='$style_url/$theme/colis.png' border='none' alt='colis'> envoyer une alerte exp&eacute;dition</b></a><br><br>";
			}
			$lastyp='';
					$subto=0;
					$subdehors=0;
					$dismoidehors='';
					echo"<br><br> 
					 <table><tr><td class='buttontd'>Autres documents de ce destinataire</td></tr>
						<tr><td class='cadre'>
						<a href=\"./?option=compta&part=$part&edit&forclient=".$_SESSION['client']."\" ><img src='$style_url/$theme/+.png' alt='+' border='none'>Nouveau document</a>
						<table>";
						$rcs='';
						if($modul_part=="achat" && $part=="achat" && isset($fournisseurs_db)) $rcs=" AND `type`='achat'";
					$rus = mysql_query("SELECT DISTINCT(`type`) FROM `$compta_base` WHERE `client`='".$_SESSION['client']."' $rcs ORDER BY `type`DESC");
					while($ruw=mysql_fetch_array($rus)){
						$type = $ruw[0];
						if($type!=$lastyp){
							if($lastyp!=''){
								echo"<tr><td></td><td align='right'>$subto&euro;<td></td></tr>";
								$dismoidehors.="$lastyp: $subdehors&euro; &nbsp; ";
								$subdehors=0;
								$subto=0;
							}
							echo"<tr><td colspan='3'><b>$type</td></tr>";
						}
						$lastyp = $type;
						
						$ros = mysql_query("SELECT * FROM `$compta_base` WHERE `client`='".$_SESSION['client']."' AND `type`='$type' ORDER BY `numero`DESC");
						while($rew=mysql_fetch_object($ros)){							
							$code = $rew->code;
							$numero = $rew->numero;
							$intitule = $rew->intitule;
							$montant = $rew->montant;
							$acompte = $rew->acompte;
							$devise = $rew->devise;
							if($devise=='')$devise='EUR';
							$solde = $montant-$acompte;
							$acc='';
							if($acompte > 0){
								$acc="<br>solde sur $montant$devise";
							}
							$etat = $rew->etat;
							$subto+=$solde;
							if($etat==0){
								$subdehors+=$solde;
							}
							$dat = date("d/m/y",strtotime($rew->date));
							$mid = $rew->id;
							echo"<tr><td>
							<span style='white-space:nowrap'><a href='./?option=compta&$type&edit=$mid&getcontent' class='info'>$code$numero<span><b>$intitule</b><br><font size='1'> $dat$acc</font></span></a>
							<td align='right'><a href='./?option=compta&$type&edit=$mid&getcontent'><font color='#$colorstatut[$etat]'>$solde$devise</font></font></td>
							</td><td><a href='#' onclick=\"javascript:open('$openpdf&mkpdf=$mid','pdf','width=400,height=500,scrollbars=1,resizable=1,top='+((screen.height-500)/2)+',left='+((screen.width-400)/2))\" class='info'><img src='http://www.adeli.wac.fr/icos/pdf.gif' border='none' alt='&eacute;diter'><span>voir le pdf</span></a></span></td>
							<td align='right'><font color='#$colorstatut[$etat]'>$defstat[$etat]</font></td>
							</tr>";
						}
					}
					$dismoidehors.="$lastyp: $subdehors&euro; &nbsp; ";
					
					echo"
					<tr><td></td><td align='right'>$subto&euro;<td></td></tr>
					</table>
					<span class='petittext'>$dismoidehors</span></td></tr>
					</table><hr>";
		}
		
			$_SESSION['pdfact']=array();
			$_SESSION['uid'] = 0;
			$_SESSION['pdfact'] = $_SESSION['pdfact'];
			$_SESSION['client'] = '';
			$_SESSION['adresse'] = '';
			$_SESSION['type'] = '';
			$_SESSION['code'] = '';
			$_SESSION['intitule']='';
			$_SESSION['date']=date("Y-m-d");
			$_SESSION['acompte']=0;
			$_SESSION['acomptele']='';
			$_SESSION['etat']=0;
			$_SESSION['expedition']='';	
			unset($_SESSION['refid'],$_SESSION['actionfin']);
		}
		else{
			echo" &eacute;chou&eacute;... <br><a href='./?option=$option&part=$part&edit=".$_SESSION['uid']."'>revenir &agrave; l'&eacute;diteur</a>";
		}
		}
		else{
			echo"<br> pas d'ID <a href='./?option=$option&part=$part&edit'>revenir &agrave; l'&eacute;diteur</a>";
		}
	}
	else{
		echo"<br>Document incomplet, <a href='./?option=$option&part=$part&edit=".$_SESSION['uid']."'>revenir &agrave; l'&eacute;diteur</a>";
	}

}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// EDITION
else{
	$idlinefact=abs($idlinefact);
		 
	if(isset($_GET['switch'])){
		$swi = $_GET['switch'];
		$swo = $_GET['swotch'];
		$tampon = $_SESSION['pdfact'];
		for($i=0 ; $i<$nbline ; $i++){		
			if($swi<$swo){	
				if($i<$swi) $_SESSION['pdfact'][$i] = $tampon[$i];
				if($i>=$swi && $i<$swo) $_SESSION['pdfact'][$i] = $tampon[$i+1];
				if($i==$swo) $_SESSION['pdfact'][$i] = $tampon[$swi];
				if($i>$swo) $_SESSION['pdfact'][$i] = $tampon[$i];
			}
			if($swi>$swo){
				if($i<$swo) $_SESSION['pdfact'][$i] = $tampon[$i];
				if($i==$swo) $_SESSION['pdfact'][$i] = $tampon[$swi];
				if($i>$swo && $i<=$swi) $_SESSION['pdfact'][$i] = $tampon[$i-1];
				if($i>$swi) $_SESSION['pdfact'][$i] = $tampon[$i];
			}
		}
	}
	
	
	if(isset($_GET['rml'])){
		$rml = $_GET['rml'];
		$idlinefact = $keyfact[$rml];		
		unset($_SESSION['pdfact'][$idlinefact]);
		$keyfact=array_keys($_SESSION['pdfact']);
		$nbline = sizeof($_SESSION['pdfact']);
	}
	
	if(isset($_POST['adelaide1']) && $_POST['adelaide1']!=''){
		if(sizeof($_SESSION['pdfact'])>=1) $idlinefact++;
		$_SESSION['pdfact'][$idlinefact]=array("label"=>$_POST['adelaide1'],"pu"=>0,"quant"=>1,"type"=>'titre',"tva"=>19.6);
		echo"<script language='javascript'>
		document.location='./?option=$option&part=$part&edit=$edit#poslig$nbline';
		</script>";
	}
	if(isset($_POST['adelaide2']) && $_POST['adelaide2']!=''){
		if(sizeof($_SESSION['pdfact'])>=1) $idlinefact++;
		$_SESSION['pdfact'][$idlinefact]=array("label"=>$_POST['adelaide2'],"pu"=>$_POST['adepu'],"quant"=>$_POST['adequant'],"type"=>'chiffre',"tva"=>$_POST['adetva'],"coderef"=>$_POST['adecoderef'],"remise"=>$_POST['aderemise'],"libre"=>$_POST['adlibre']);
		echo"<script language='javascript'>
		document.location='./?option=$option&part=$part&edit=$edit#poslig$nbline';
		</script>";
	}
	if(isset($_POST['adelaide3']) && $_POST['adelaide3']!=''){
		if(sizeof($_SESSION['pdfact'])>=1) $idlinefact++;
		$_SESSION['pdfact'][$idlinefact]=array("label"=>$_POST['adelaide3'],"pu"=>0,"quant"=>1,"type"=>'comment',"tva"=>19.6);
		echo"<script language='javascript'>
		document.location='./?option=$option&part=$part&edit=$edit#poslig$nbline';
		</script>";
	}
	if(isset($_POST['aderemmon']) && is_numeric($_POST['aderemmon']) && $_POST['aderemmon']>0){
		if(sizeof($_SESSION['pdfact'])>=1) $idlinefact++;
		$_SESSION['pdfact'][$idlinefact]=array("label"=>"remise ".$remiseeten[$_POST['aderemetan']]." ".$_POST['aderemmon'].$remisetype[$_POST['aderemtyp']],"pu"=>$_POST['adepu'],"quant"=>$_POST['adequant'],"type"=>'remise',"tva"=>$_POST['adetva'],"coderef"=>$_POST['adecoderef'],"hidden"=>$_POST['aderemmon'].'x'.$_POST['aderemtyp'].$_POST['aderemetan']);
		echo"<script language='javascript'>
		document.location='./?option=$option&part=$part&edit=$edit#poslig$nbline';
		</script>";	
	}
			  
echo"<form name='artos' action='./?option=$option&part=$part&a&edit=$edit' method='post'>";
	
	echo"<div class='buttontd'> <b>Ent&ecirc;te</b></div>
	<table><tr><td valign='top'>
	 <br>
	 date: <input type='date' name='date' value='".$_SESSION['date']."' maxlength=\"10\" style='width:100px'><img src='$style_url/$theme/mysqltype-date.png' alt='date' style='cursor:pointer' onclick=\"javascript:contextdate(event);cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=artos.date&date='+document.artos.date.value+'&type=date'\">
		|
		type:  ";
		if($_SESSION['type']==''){
			echo"
			<select name='type' onchange=\"document.artos.numero.value=''\">
			 <option value='devis' onclick=\"\">devis</option>
			 <option value='facture'>facture</option>
			 <option value='commande'>commande</option>
			 <option value='avoir'>avoir</option>
	     	</select>";
		 }
		 else{
		 	echo"<input type='hidden' name='type' value='".$_SESSION['type']."'> <span class='textegrasfonce' style='font-size: 20px;'>
".strtoupper($_SESSION['type'])."</span>";	 
	 	}
	 echo"</td><td align='right'>";
	 if(isset($_POST['duplic']) && isset($_POST['actionfin']) && isset($_POST['refid']) && $_POST['duplic']!=''){
		$_SESSION['listid']=$_POST['refid'];
		$_SESSION['actionfin']=$_POST['actionfin'];
	}
	if(isset($_SESSION['listid']) && isset($_SESSION['actionfin'])){
		$listid=$_SESSION['listid'];
		$actionfin=$_SESSION['actionfin'];
		$listeid = explode(',',$listid);
		$nbid = sizeof($listeid)-1;
		echo"<input type='hidden' name='refid' value='$listid'>
		Après enregistrement, les <b>$nbid documents fusionn&eacute;s</b> seront
		<select name='actionfin'>
		<option value='ig'>tels quels</option>
		<option value='de'>d&eacute;sactiv&eacute;s</option>
		<option value='an'>annul&eacute;s</option>
		<option value='su'>supprim&eacute;s</option>
		</select>
		<script language='javascript'>
		document.artos.actionfin.value='$actionfin';
		</script>
		";
	}
	elseif(!isset($_GET['clon'])){
		echo"
		<select name='duplic' onchange=\"document.artos.action='./?option=$option&part=$part&edit=$edit&getcontent&clon&type='+this.value\">
		 <option value=''>dupliquer en :</option>
		 <option value='devis'>Devis</option>
		 <option value='facture'>Facture</option>
			<option value='commande'>Fommande</option>
			<option value='avoir'>Avoir</option>
	 </select> et
	 <input type='hidden' name='refid' value='0,$edit'>
		<select name='actionfin'>
		<option value='ig'>conserver</option>
		<option value='de'>d&eacute;sactiver</option>
		<option value='an'>annuler</option>
		<option value='su'>supprimer</option>
		</select>
		l'original
		<input type='submit' value='ok' class='buttontd'>
		";
	}
	else{
		echo"nouvel &eacute;l&eacute;ment";
	}
	
	echo"</td></tr>
	<tr><td>
		
		<table ><tr>
		<td rowspan='2' valign='bottom' width='100'>identifiant : </td>
		<td >Code</span></td><td>Num&eacute;ro</td><td>Devise</td></tr><tr>
		<td ><datalist id='codedeja'><select onchange='document.artos.code.value=this.value;'>";
		$res= mysql_query("SELECT DISTINCT `code` FROM $compta_base");
			while($row=mysql_fetch_row($res)){
				$codex = $row[0];
				echo"<option value='$codex'>$codex</option>";	
			}
		echo"
		</select></datalist><input type='text' name='code' value='' list='codedeja' placeholder='facultatif' style='width:100px;";
		if($_SESSION['code']==''){ echo"border:#F90 2px solid;"; }
		echo"'></td>
		<td><input type='text' name='numero' value='".$_SESSION['numero']."' placeholder='automatique' style='width:100px'></td>
		<td><input type='text' name='devise' value='".$_SESSION['devise']."' size='6' maxlength='5' >	</td>
		</tr>
		</table>
	</td>
	<td></td>
	</tr></table>	
	
		<br>
		<textarea name='mode' style='display:none'></textarea>
		
		<table class='cadre'><tr>";
		
		if(!ereg('<>',$_SESSION['mode'])){
			$tmode='';
			for($s=0; $s<$_SESSION['etat'] ; $s++){
				$tmode.='<>';		
			}
			if($_SESSION['mode']=='') $_SESSION['mode']='#';
			$tmode.=$_SESSION['mode'];
			for($s=$_SESSION['etat']; $s<sizeof($defstat) ; $s++){
				$tmode.='<>';		
			}
			$_SESSION['mode'] = $tmode;
		}		
		$cmode = explode('<>',$_SESSION['mode']);
		$barre='';
		for($s=0; $s<sizeof($defstat) ; $s++){
				echo"<td >$defstat[$s]<br>
				<input type='text' value=\"$cmode[$s]\" name='etat_$s' onkeyup='expi()'>
				</td>";
				$barre.="<td id='colorstat$s' style='padding:0px;font-size:3px;'><input type='radio' value='$s' name='etat' onclick='expi()'></td>";
		}
		$islivre="<input type='hidden' name='expedition' value=''>";
		if($_SESSION['etat']==3){
			$islivre="<br>informations d'expedition :<input type='text' name='expedition' value='".$_SESSION['expedition']."'>";
		}
		
		
		echo"	 </tr><tr>$barre</tr></table>
		<script language='javascript'>
		var staco=new Array('".implode("','",$colorstatut)."');
		document.artos.type.value='".$_SESSION['type']."';
		document.artos.etat[".$_SESSION['etat']."].checked=true;
		isexpi=false;
		function expi(){
			if(isexpi==true){
				document.getElementById('confirmmodifstock').style.display='inline';
			}
			isexpi==true;
			ki=0;
			cesta='';
			for(i=0; i<".sizeof($cmode)." ; i++){
				if(eval('document.artos.etat_'+i)){
					va = eval('document.artos.etat_'+i).value;				
					cesta+=va+'<>';
					if(document.artos.etat[i].checked==true){
						ki=i;
					}
				}
			}
			for(i=0 ; i<=".sizeof($cmode)." ; i++){
				if(document.getElementById('colorstat'+i)){
					if(i<=ki){
						document.getElementById('colorstat'+i).style.background='#'+staco[i];
					}
					else{
						document.getElementById('colorstat'+i).style.background='#FFFFFF';
					}
				}
			}
			document.artos.etat[ki].checked=true;
			document.artos.mode.value=cesta;
		}
		expi();
		</script> 
		<span id='islivre'>
		<br>informations d'expedition :<input type='text' name='expedition' value='".$_SESSION['expedition']."'>
		</span>
		<span id='confirmmodifstock' style='display:none'>
		Voulez vous affecter le stock ?
		Magsasin <select></select> type d'affectation <select><option>en positif</option><option en négatif</option></select>
		</span>
		
		<br><br>
		<p align='right'>
		  <input type='submit' class=\"buttontd\" value='Recalculer'>";
		  if($nbline>0){
		  	$enregistre=" <input type='button' class=\"grosbouton\" onclick=\"document.artos.action+='&valid';document.artos.submit();\" value='Enregistrer'>";
		  }
		  else{
		  	$enregistre=" <input type='button' class=\"buttontd\" value='Document incomplet'>";
		  }
		  echo"$enregistre
		</p>
		<hr>
	<table width=\"100%\">
	<tr class='buttontd'>
			  <td><b>Document</b></td>
			</tr>
	</table>";
	$styint="";
	if($_SESSION['intitule']==""){
		$styint="border-color:#FF0000;border-width:2px;border-style:solid";
	}
	$styadr="";
	if($_SESSION['adresse']==""){
		$styadr="border-color:#FF0000;border-width:2px;border-style:solid";
	}
	echo"
	 <br>
		intitul&eacute; : <input type='text' name='intitule' value='' style='width:300px;$styint'>
		
		<p align='right'>
		Destinataire :<br>
		<a style='cursor:pointer' onclick=\"document.location='./?option=worknet&part=$clients_db&edit='+document.artos.client.value;\"><font size='1'><u>Acc&eacute;der au compte</u></font></a>
		<select name='client' onchange=\"document.artos.adresse.value=jsclient[this.value];\" style='width:180px'><option value=''>ins&eacute;rer client</option>";
		$conn = mysql_connect($host, $login, $passe);
		$ros = mysql_query("SELECT * FROM `$clients_db` WHERE `nom`!='' $cliid ORDER BY `nom`");
		$jsclien=" ";
		$perma_remise=0;
		while($rows = mysql_fetch_object($ros)){
			$cid = $rows->id;
			$cno=$rows->nom;
			$adressef = $rows->adresse;
			if(isset($rows->adresse2))$adressef.= ' '.$rows->adresse2;
			if(isset($rows->adresse_2))$adressef.= ' '.$rows->adresse_2;
			if(isset($rows->code))$adressef.= "\n".$rows->code;
			if(isset($rows->zip))$adressef.= "\n".$rows->zip;
			if(isset($rows->codepostal))$adressef.= "\n".$rows->codepostal;
			if(isset($rows->code_postal))$adressef.= "\n".$rows->code_postal;
			if(isset($rows->zipcode))$adressef.= "\n".$rows->zipcode;
			if(isset($rows->ville))$adressef.= " ".$rows->ville;
			if(isset($rows->pays) && !is_numeric($rows->pays))$adressef.= "\n".$rows->pays;
			
			$adres=explode("\n",strip_tags($adressef));
			$adressa="";
			for($e=0 ; $e<sizeof($adres) ; $e++){
				$adressa.=trim($adres[$e])."\\n";
			}
			$adressa = str_replace('"','\"',$adressa);
			$s="";
			if($_SESSION['client']==$cid){
				$s="selected";
				if(isset($rows->remise) && is_numeric($rows->remise))$perma_remise=$rows->remise;
			}
			$jsclien.="jsclient[$cid]=\"".str_replace('"','',$cno)."\\n".str_replace('"','',$adressa)."\";\n";
			echo"<option value='$cid' $s>$cno</option>";
		}
		@mysql_close($conn);
		echo"</select>
		
		<br>
		Adresse du client<br>
	<textarea name='adresse' style='width:180;$styadr'>".$_SESSION['adresse']."</textarea>
	
	<script language='javascript'>
		jsclient=new Array();
		$jsclien
		";
		if(isset($_GET['forclient']) && is_numeric($_GET['forclient'])){
			echo"
			document.artos.client.value='".$_GET['forclient']."';
			document.artos.adresse.value=jsclient['".$_GET['forclient']."'];
			";
		}
		echo"
		</script>
	</p>
	 <br>
	<table>
	<tr class='buttontd'>
			  <td align=\"center\" valign='top'>n&deg;</td>
			  <td align=\"center\" valign='top'><b>R&eacute;f.</b></td>
			  <td align=\"center\" valign='top'><b>D&eacute;tail</b></td>
			  <td align=\"center\"><b>PU $taxe</b></td>
			  <td align=\"center\"><b>Quant</b></td>
			  <td align=\"center\"><b>Remise</b>(%)</td>
			  <td align=\"center\"><b>tva</b>(%)</td>
			  <td align=\"center\"><b>Total $taxe</b></td>
			</tr>
	";
	$jscalc='';
	$tot=0;
	$tottc=0;
	$totva=0;
	for($i=0 ; $i<$nbline ; $i++){
		$idlinefact = $keyfact[$i];
	
		$label = $_SESSION['pdfact'][$idlinefact]["label"];
		$typo = $_SESSION['pdfact'][$idlinefact]["type"];
		$pu = $_SESSION['pdfact'][$idlinefact]["pu"];
		$quant = $_SESSION['pdfact'][$idlinefact]["quant"];
		$tva = $_SESSION['pdfact'][$idlinefact]["tva"];
		$coderef = $_SESSION['pdfact'][$idlinefact]["coderef"];
		$libre = $_SESSION['pdfact'][$idlinefact]["libre"];
		$hidden = $_SESSION['pdfact'][$idlinefact]["hidden"];
		$remise = abs($_SESSION['pdfact'][$idlinefact]["remise"]);
		$labelbr = substr_count($label,"\n");
		$labelbr+=1;
		
		echo"<tr>
			<td valign='top'><a name='poslig$i'></a>
			<input type='hidden' name='hidden$idlinefact' value='$hidden'>
			<select onchange=\"document.artos.action+='&switch=$i&swotch='+this.value+'#poslig'+this.value;document.artos.submit();\">";
			for($o=0 ; $o<$nbline ; $o++){
				$s = "";
				if($o==$i)$s="selected";
				echo"<option value='$o' $s>".($o+1)."</option>";
			}
			echo"</select>
			</td>";
		if($typo==="titre"){
					echo"<td colspan='8' valign='top' align='left'><input name=\"label$idlinefact\" type=\"text\" style=\"width:480px\" class='textegrasfonce' value=''>
							<script language='javascript'>
								document.artos.label$idlinefact.value=\"".addslashes($label)."\";
							</script>
							</td>
					</tr><tr>
					<td colspan='8' align='right'>";					
		}
		elseif($typo==="comment"){
					echo"<td colspan='8' valign='top' align='left'><textarea name=\"label$idlinefact\" cols=\"40\" rows=\"$labelbr\" style=\"width:280px\">$label</textarea>
							</td>
					</tr><tr>
					<td colspan='8' align='right' valign='bottom'>";					
		}
		else{
			if($hidden!='' && $typo==="remise"){
				$hidden = explode('x',$hidden);
				if(substr($hidden[1],0,3)=='RMF'){
					$pu = $hidden[0];
					$quant = -1;
					$tva = 19.6;
				}
				if(substr($hidden[1],0,3)=='RMP'){
					if(substr($hidden[1],3,1)=='0'){
						$tva = 19.6;
						if($taxe=='HT'){
							$pu = round($tot*$hidden[0]/100,2);
						}
						else{
							$pu =  round($tottc*$hidden[0]/100,2);
						}
						$quant = -1;
					}
					if(substr($hidden[1],3,1)=='1'){
						$tva = $last_tva;
						$pu = round($last_pu*$hidden[0]/100,2);
						$quant = -$last_quant;
					}					
				}
				$typo='remise';
			}
			if($taxe=='HT'){
				$pt = number_format($pu*$quant,2, '.', '');
				$ptva = round($pt*$tva/100,2);
				$totva += $ptva;
				$tottc += $pt+$ptva;
				$tot+=$pt;
				$jscalc.="
				pu = pfloat(document.artos.pu$idlinefact.value);
				qu = pfloat(document.artos.quant$idlinefact.value);
				rm = pfloat(document.artos.remise$idlinefact.value);
				tv = pfloat(document.artos.tva$idlinefact.value);";
				if($remise_app == 1) $jscalc.="ph = around(pu*(100-rm))/100*qu;";
				else $jscalc.="ph = (pu*qu)*(100-rm)/100;";
				$jscalc.="
				//ph = (pu*qu)*(100-rm)/100;
				pt = around(ph*(1+(tv/100)));
				document.getElementById('pt$idlinefact').innerHTML=around(ph);					
				to+=pt;
				ht+=ph;
				ttva+=pt-ph;
				";
			}
			else{
				$pt = number_format($pu*$quant,2, '.', '');
				$ptva = round($pt-($pt/(1+($tva/100))),2);
				$totva += $ptva;
				$tottc += $pt;
				$tot+=$pt-$ptva;
				
				$jscalc.="
				pu = pfloat(document.artos.pu$idlinefact.value);
				qu = pfloat(document.artos.quant$idlinefact.value);
				rm = pfloat(document.artos.remise$idlinefact.value);
				tv = pfloat(document.artos.tva$idlinefact.value);
				pt = around(pu*qu);
				";
				if($remise_app == 1) $jscalc.="pt = around(pu*(100-rm))/100*qu;";
				else $jscalc.="pt = (pu*qu)*(100-rm)/100;";
				$jscalc.="
				//ph = (pu*qu)*(100-rm)/100;
				ph = around(pt/(1+(tv/100)));
				document.getElementById('pt$idlinefact').innerHTML=around(pt);
				to+=pt;
				ht+=ph;
				ttva+=pt-ph;
				";
			}
			if($hidden==''){
				$last_pu = $pu;
				$last_quant = $quant;
				$last_tva = $tva;
				$typo='chiffre';
			}
			
			
			
					
					echo"<td align=\"center\" valign='top'><input name=\"coderef$idlinefact\" type=\"text\" size=\"4\" value='$coderef'><input name=\"libre$idlinefact\" type=\"hidden\" value=\"$libre\"></td>
					<td><textarea name=\"label$idlinefact\" cols=\"40\" rows=\"$labelbr\" style=\"width:280px\" onkeyup='calcu();this.style.height=this.scrollHeight;' onfocus='this.style.height=this.scrollHeight;'>$label</textarea></td>
							<td align=\"center\" valign='top'><input name=\"pu$idlinefact\" type=\"text\" size=\"8\" value='$pu' onkeyup='calcu()'></td>
							<td align=\"center\" valign='top'><input name=\"quant$idlinefact\" type=\"text\" style='text-align:center' size=\"4\" value='$quant' onkeyup='calcu()'></td>
							<td align=\"center\" valign='top'><input name=\"remise$idlinefact\" type=\"text\" size=\"2\" value='$remise' onkeyup='calcu()'></td>
							<td align=\"center\" valign='top'><input name=\"tva$idlinefact\" type=\"text\" size=\"4\" value='$tva' onkeyup='calcu()'></td>
							<td align=\"right\" valign='top'><span id='pt$idlinefact'>$pt</span></td>
					</tr><tr>
						<td colspan='8'>";
				
		}				
		echo"<p align='right'>
							<select name='type$idlinefact' onchange=\"document.artos.action+='#poslig$i';document.artos.submit();\">
							<option value='titre'>titre</option>
							<option value='chiffre'>ligne de compte</option>
							<option value='comment'>commentaire</option>
							<option value='remise'>remise</option>
						</select>						
				<script language='javascript'>
		document.artos.type$idlinefact.value='$typo';
		</script>";				
			if($i > 0){
				echo"<a href='#' onclick=\"document.artos.action+='&switch=$i&swotch=".($i-1)."#poslig$i';document.artos.submit();\"><img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></a>";
			}
			if($i < $nbline-1){
				echo"<a href='#poslig$i' onclick=\"document.artos.action+='&switch=$i&swotch=".($i+1)."#poslig$i';document.artos.submit();\"><img src='$style_url/$theme/class_down_off.jpg' alt='v' border='none'></a>";
			}
			echo"<a href='#poslig$i' onclick=\"document.artos.action+='&rml=$i#poslig$i';document.artos.submit();\"><img src='$style_url/$theme/del.jpg' alt='x' border='none'></a></p></td>
		</tr>
		<tr bgcolor=\"#9AA9D2\">
				<td height=\"1\" align=\"center\" colspan='8'></td>
		</tr>";
	}
	if($nbline == 0){
		echo"
		  <tr>
		  <td align=\"left\" colspan='8'>
		  Aucune ligne
		  </td>
		</tr>
		<tr bgcolor=\"#9AA9D2\">
		  <td height=\"1\" align=\"center\" colspan='8'></td>
		</tr>";
	}	
	$ttct = number_format ($tottc-$_SESSION['acompte'],2, '.', '');	
	$tot = number_format ($tot,2, '.', '');
	$totva = number_format ($totva,2, '.', '');
	$tottc = number_format ($tottc,2, '.', '');
	if($tva==''){
		$tva='19.60';
	}
	echo"
			<tr>
		  <td align=\"left\" colspan='8'><b>Ajouter une ligne</b> :</td></tr>
		
		  <tr>
		  	<td align=\"left\" colspan='2'>titre :</td>
			<td colspan='5'>
			  <input type='text' name='adelaide1' size='60'>
		 	 </td>
		  </tr>
		  <tr>
		  	<td>Compte :</td>
			<td><input name=\"adecoderef\" type=\"text\" size=\"4\" value=' '></td>
		  	<td><textarea name='adelaide2' style='width:300px;height:20px;'></textarea></td>
		  	<td><input name=\"adepu\" type=\"text\" size=\"8\" value='0.00' onkeyup='calcu()'></td>
		  	<td><input name=\"adequant\" type=\"text\" size=\"8\" value='1' onkeyup='calcu()'style='text-align:center' size=\"4\"></td>
		  	<td align='center'><input name=\"aderemise\" type=\"text\" size=\"2\" value='$perma_remise' onkeyup='calcu()'></td>
			<td><input name=\"adetva\" type=\"text\" size=\"4\" value='$tva' onkeyup='calcu()'>
			<input name=\"adlibre\" type=\"hidden\" value=''></td>
		  	<td align='right'> <span id='adept'></span> </td>
		 </tr>
		 <tr>
		 	<td align=\"left\" colspan='2'>commentaire :</td>
			<td colspan='5'>
		 		<input type='text' name='adelaide3' size='60'>
		 	</td>
		  </tr>
		  
		  <tr>
		  	<td colspan='2'>Remise :</td>
			<td colspan='5'>
				<input type='text' name='aderemmon' value='0.00' size='3'/>
			  <select name='aderemtyp'>
			    <option value='RMP'>%</option>
				<option value='RMF'>".$_SESSION['devise']."</option>				
			  </select>
			  <select name='aderemetan'>
				<option value='1'>Sur la derni&egrave;re ligne</option>
				<option value='0'>Sur toutes les derni&egrave;res lignes</option>
			  </select>
		  </td></tr>
			";
	// 
		if(isset($rayons_db) && is_file("bin/inc_ajax.php")){
			$chp = array('desc','plus1','plus2','ean','libre','taille','couleur');
			$csp = fread(get_pref("compta_search_print.conf"));
			
			echo"			
			<tr><td align=\"left\" colspan='8'><b>Boutique</b></td></tr>
			<tr><td colspan='2'>R&eacute;f&eacute;rence, EAN :
			</td><td colspan='5'>
		  <input type='text' name='adelaide4' size='60'><input type='button' onclick='gesword()' class='buttontd' value='Rechercher'/>
		  <div id='chpd'>";
		  foreach($chp as $ch){
			echo"<input type='checkbox' name='chp_$ch'";
			if(strpos($csp,"<$ch>")>-1) echo" checked";
			echo"  onclick='gesword()' />&nbsp;".ucfirst($ch)."&nbsp; ";
		  }
		  echo"</div>
		  <div style='position:relative'>
				<div style='position:absolute;left:-5;top:0;z-index:450;width:150px;'>
					  <span id='gesluto'></span>
				</div> 
		   </div>
		   
		  <script language='javascript'> 
				function glos(){
					document.getElementById('gesluto').innerHTML='';
				}
				function gesword(){
					clef = document.artos.adelaide4.value.toLowerCase();
					if(clef != ''){
						var chp = document.getElementById('chpd').getElementsByTagName('input');
						var quoip = '';
						for(i=0 ; i<chp.length ; i++){
							if(chp[i].checked==true){
								quoip+='&'+chp[i].name+'=1';
							}
						}
						envoyer('bin/inc_ajax.php?scan=gestion_article','q',clef+'&client=".$_SESSION['client']."&part=$part'+quoip,'gesluto');
					}
					else{
						document.getElementById('gesluto').innerHTML='';
					}
				}
			</script>
		  </td></tr>
			";
		}
			
			
			
			
		  
		 	/*echo"<tr><td align=\"left\" colspan='2'><b>Fonctions</b></td></tr>
			<tr><td>&Eacute;ch&eacute;ancier :
			</td><td>
				sur <input type='text' name='adeechean' value='0' size='2'/> Mois &agrave; partir du
		   </td></tr>";
			
			*/
			
			
			
			if($taxe=='HT'){
				$jscalc.="
				pu = pfloat(document.artos.adepu.value);
				qu = pfloat(document.artos.adequant.value);
				rm = pfloat(document.artos.aderemise.value);
				tv = pfloat(document.artos.adetva.value);
				";
				if($remise_app == 1) $jscalc.="ph = Math.round(pu*(100-rm)*100)/100/100*qu;";
				else $jscalc.="ph = (pu*qu)*(100-rm)/100;";
				$jscalc.="
				
				pt = around(ph*(1+(tv/100)));
				document.getElementById('adept').innerHTML=around(ph);					
				to+=pt;
				ht+=ph;
				ttva+=pt-ph;
				";
			}
			else{
				$jscalc.="
				pu = pfloat(document.artos.adepu.value);
				qu = pfloat(document.artos.adequant.value);
				rm = pfloat(document.artos.aderemise.value);
				tv = pfloat(document.artos.adetva.value);
				";
				if($remise_app == 1) $jscalc.="pt = Math.round(pu*(100-rm))/100*qu;";
				else $jscalc.="pt = (pu*qu)*(100-rm)/100;";
				$jscalc.="				
				ph = around(pt/(1+(tv/100)));
				document.getElementById('adept').innerHTML=around(pt);
				to+=pt;
				ht+=ph;
				ttva+=pt-ph;
				";
			}
			echo"
			
			<tr><td colspan='8' align='right'>
			<input type='submit' class='buttontd' value='ajouter'>	  			
			</td></tr>
			
			
		<tr bgcolor=\"#9AA9D2\">
		  <td height=\"1\" align=\"center\" colspan='8'></td>
		</tr>
		<tr>
		  <td align=\"right\" colspan='8'>
		  <b><span id='ht'>$tot</span> ".$_SESSION['devise']." HT</b><br>
		  TVA: <span id='ftva'>$totva</span> ".$_SESSION['devise']."<br>
		  <span id='ttc'>$tottc</span> ".$_SESSION['devise']." TTC	  
		  </td>
		</tr>
		<tr>
		  <td align=\"right\" colspan='8'>
		  Acompte vers&eacute; le: <input type='text' name='acomptele' value='".$_SESSION['acomptele']."'>,  <input type='text' name='acompte' value='".$_SESSION['acompte']."' style='width:50px' onkeyup='calcu()'> ".$_SESSION['devise']."
		  </td>
		</tr>
		<tr>
		  <td align=\"right\" colspan='8'>
			total &agrave; payer
		  <b><span id='total'>$ttct</span> ".$_SESSION['devise']." TTC</b>	
		<script language='javascript'>
		function around(n){
			return Math.round(n*100)/100;
		}
		function pfloat(n){
			if(isNaN(n)){
				return 0;
			}			
			return parseFloat(n);
		}
		function ajoutlign(s,p,q,t,r,l,re){			
			document.artos.adelaide2.value=s;
			document.artos.adepu.value=p;
			document.artos.adequant.value=q;
			document.artos.adetva.value=t;
			document.artos.adecoderef.value=r;
			document.artos.adlibre.value=l;
			document.artos.aderemise.value=re;
			glos();
			parent.document.artos.submit();	
		}
		function calcu(){
			var to=0;
			var ht=0;
			var ttva=0;
			$jscalc
			
			document.getElementById('ttc').innerHTML=around(to);
			document.getElementById('ftva').innerHTML=around(ttva);
			document.getElementById('ht').innerHTML=around(ht);
			acompte = pfloat(document.artos.acompte.value);
			to-=acompte;
			document.getElementById('total').innerHTML=around(to);	
		}	
		calcu();
	</script>  
		  </td>
		</tr>
		<tr bgcolor=\"#9AA9D2\">
		  <td height=\"1\" align=\"center\" colspan='8'></td>
		</tr>
		</table>
		<p align='right'>
		 <input type='submit' class=\"buttontd\" value='Recalculer'>
		  $enregistre
		  </p>
		
	</form>
	<script language='javascript'>
		document.artos.intitule.value=\"".addslashes($_SESSION['intitule'])."\";
		document.artos.code.value=\"".addslashes($_SESSION['code'])."\";
		document.artos.mode.value=\"".addslashes($_SESSION['mode'])."\";
		document.artos.adelaide4.focus();
	</script>";
}


?>