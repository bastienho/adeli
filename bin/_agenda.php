<?php // 549 > Agenda ;
$conn = connecte($base, $host, $login, $passe);
if(mysql_query("SHOW COLUMNS FROM `$agenda_base`")  ){

		$verifupdt = mysql_query("DESC `$agenda_base`");
		$allchamps = array();
		while($ro = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$ro->Field);
		}
		if(!in_array("only",$allchamps)){
			mysql_query("ALTER TABLE `$agenda_base` ADD `only` BIGINT NOT NULL default '0'");
		}
		if(!in_array("lien",$allchamps)){
			mysql_query("ALTER TABLE `$agenda_base` ADD `lien` VARCHAR(255) NOT NULL default ''");
		}
insert("_agenda_sync");
mysql_close($conn);
function func_csv($str){
	$str =  ereg_replace("[,;]"," - ",$str);
	$taxt = split("[ \n]",$str);
	$str="";
	$nbt = sizeof($taxt);
	for($i=0 ; $i<$nbt ; $i++){
	  $str.= trim($taxt[$i])." ";
	}
	return trim($str);
}
function scandate($x_id,$sqldate="now",$h="none",$print=1,$add=1){
	$requ="";
	if($sqldate=="now"){
		$sqldate = date("Y-m-d");
	}
	$ch='10';
	if(is_numeric($h )){
		$next = $h+1;
		$requ =  "AND `heure` >= '$h:00:00'	AND `heure` < '$next:00:00'";
		$ch=$h;
	}
	global $agenda_base, $part, $u_id, $debit;
	if($add==1) echo"<div id='agenda_$sqldate$h' class='aam' ondblClick=\"contextage('$sqldate','$ch:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\">";
	/* if(!is_numeric($h)){
		echo "<div class='aam' id='agenda_$sqldate$h"."am'>";
	 }
	 else{
		echo "<div class='hou' id='agenda_$sqldate$h"."ho' ondblClick=\"contextage('$sqldate','$h:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."$h','$sqldate&h=$h&print=1')\"><div style='height:100%; min-height:30px; padding:5px; padding-bottom:10px;'>";
	 }*/
	$midi=0;
	$res = mysql_query("SELECT * FROM `$agenda_base` WHERE `date`='$sqldate' AND (`only`='0' OR `only`='$u_id') $requ ORDER BY `date`,`heure`");
	if($res && mysql_num_rows($res)>0){
	if($debit==1 && $print==0) return mysql_num_rows($res);
			while($ro = mysql_fetch_object($res)){
				 $m_id = $ro->id;
				 $m_heure = $ro->heure;
				 $m_usr = $ro->usr;
				 $m_date = $ro->date;
				 $m_qui = addslashes($ro->qui);
				 $m_type = addslashes($ro->type);
				 $m_client = addslashes($ro->client);
				 $m_note = addslashes($ro->note);
				 $m_etat = $ro->etat;
				 $m_priority = $ro->priority;
				 $m_only = $ro->only;
				 $m_lien = $ro->lien;
				 
				 
				 $printki=$m_client;
				 $nots=substr($m_note,0,20);
				 $m_couleur = $ro->couleur;
					$size=2;
					$marj=1;
					$b_couleur = "#$m_couleur";
					$co=' style="font-weight:bold" ';
						if($m_etat==1){
							$size=1;
							$marj=0;
							$co="color='#$m_couleur'";
							$b_couleur = 'none';
							$nots='';
						}
						if($m_date==$sqldate && $m_heure>date('h:i:s') && $onlyon==1){
							$size=1;	
							//$b_couleur = ":#$m_couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
						}
						if($u_id!=$m_usr){
							$b_couleur = "#$m_couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
						}

				 if(is_numeric($m_client) && mysql_query("SHOW COLUMNS FROM `clients`") ){
				 	$ris = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$m_client'");
					if($ris && mysql_num_rows($ris)==1){
						$ri = mysql_fetch_object($ris);
						$printki="<a href='./?option=site&clients&edit=$m_client'><font size='$size' $co>".($ri->nom)."&nbsp;</font></a>";
					}
				 }
				if($m_priority==0) $m_priority=1;
				
				
				$agebody='';
				$bodi=split("\n",strip_tags(trim($m_note)));
				for($e=0 ; $e<sizeof($bodi) ; $e++){
					$agebody.=trim(trim($bodi[$e]))." ";
				}
				$m_note = urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode(str_replace('"',"`",$agebody)))));
				
				if($debit==0){		
					if($print==1){
						$prio = "<font style='font-size:".($m_priority*3)."px'>".str_repeat("*",$m_priority)."</font>";		 
						$printo="<table cellpadding='1' cellspacing='0' id='$sqldate$h"."_$m_id' width='90%' style='margin:$marj;background:$b_couleur;cursor:default;z-index:150;";
						if(!is_numeric($h) && $midi==0 && str_replace(':','',$m_heure)>120000){
							 $midi=1;
							 $printo.="margin-top:10px;";
						 }
						$printo.="' onClick=\"fillage('$m_qui','$m_type','$m_client','$m_priority','$m_etat','$m_note','$m_only','$m_usr','$m_lien'); contextage('$m_date','$m_heure',event,'Modifier','update=$m_id&$part','$m_couleur',$m_id,'agenda_$sqldate$h','$sqldate&h=$h&print=$print');\"><tr><td><font size='$size' $co>$printki</font></td><td align='right'>$prio</td></tr></table>";
					}
					else{
						$prio = "<font style='font-size:".($m_priority*3)."px'>*</font>";
						$printo="<a onClick=\"fillage('$m_qui','$m_type','$m_client','$m_priority','$m_etat','$m_note','$m_only','$m_usr','$m_lien');			contextage('$m_date','$m_heure',event,'Modifier','update=$m_id&$part','$m_couleur',$m_id,'agenda_$sqldate$h','$sqldate&h=$h&print=$print');\"><font color='#$m_couleur'>$prio</font></a>";
					}
				}
				else{
					if($print==1){
						$prio = "<font style='font-size:".($m_priority*3)."px'>".str_repeat("*",$m_priority)."</font>";		 
						$printo="<table cellpadding='1' cellspacing='0' width='90%' style='margin:$marj;background:$b_couleur;cursor:default;z-index:150;'><tr><td><a href='./?option=$option&part=$part&id=$m_id'><font size='$size' $co>$printki</font></a></td><td align='right'>$prio</td></tr></table>";
					}
					else{
						$prio = "<font style='font-size:".($m_priority*3)."px'>*</font>";
						$printo="<a href='./?option=$option&part=$part&id=$m_id'><font color='#$m_couleur'>$prio</font></a> ";
					}
				}
				echo $printo;
				if(isset($_POST["export"]) && $m_etat<=$_POST["pref"]){
					global $output_export;
					global $export;
					if($export=='csv'){
						$sta = array('en cours','terminé');
						$vdat = date("d/m/Y H:i",strtotime("$m_date $m_heure"));
						$output_export.=func_csv($m_qui).";".func_csv($m_type).";".func_csv($m_client).";$vdat;".(4-$m_priority).";$sta[$m_etat];".func_csv($m_note)."\n";
					}
					if($export=='vcs'){
						$sta = array('ACCEPTED','COMPLETED');
						$mt = strtotime($_POST['dec_sens'].$_POST['nb_sens']." Hours",strtotime("$m_date $m_heure"));
						$vdat = date("Ymd",$mt).'T'.date("His",$mt).'Z';
						//str_replace('-','',$m_date).'T'.str_replace(':','',$m_heure).'Z';
						$output_export.="BEGIN:VEVENT\r\nSUMMARY:$m_client\r\nDTEND:$vdat\r\nDTSTART:$vdat\r\nDESCRIPTION:".ereg_replace("[[:punct:]]",' ',urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode($m_note)))))."\r\nDALARM:$vdat;PT1M;1;$m_client\r\nAALARM;URL:$vdat;PT1M;1;\r\nEND:VEVENT\r\n\r\n";
/*PRIORITY:".(4-$m_priority)."
STATUS:$sta[$m_etat]

*/
					}
				}
			}
			/*if(!is_numeric($h) && $midi==0 ){
			  
			   echo"</div><div class='cadre' style='height:50%; border-width:0px;' ondblClick=\"contextage('$sqldate','14:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\">&nbsp;";
		   }*/
	}
		if($add==1) echo "</div>";
}

if($part == ""){
	$part = get_pref('agenda.mode.conf');
}


if($part != ""){


	$senss  =array("-","+");
	
	$decs = $senss[abs(get_pref('agenda.decs.conf'))];
	$nbs = abs(get_pref('agenda.nbs.conf'));


if($part=='jour' || $part=='semaine' || $part=='mois' || $part=='annee'){
	set_pref('agenda.mode.conf',$part);
}

if(isset($_POST["export"])){
	if($_POST['dec_sens']!='' && $_POST['nb_sens']!=''){
		set_pref('agenda.decs.conf',$_POST['dec_sens']);
		set_pref('agenda.nbs.conf',$_POST['nb_sens']);
	}
	insert("output");
	$output_export="BEGIN:VCALENDAR\r\nVERSION:1.0\r\n\r\n";
	$export = $_POST["export"];
	if(is_file('bin/output.php')){
		$openexpo='./?incpath=bin/';
	}
	else{
		$openexpo='$style_url/update.php?file=';
	}
}
	$conn = connecte($base, $host, $login, $passe);
	$verifupdt = mysql_query("DESC `$agenda_base`");
		$allchamps = array();
		while($ro = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$ro->Field);
		}
		if(!in_array("priority",$allchamps)){
			mysql_query("ALTER TABLE `$agenda_base` ADD `priority` INT(1) NOT NULL");
		}
	mysql_close($conn);	


$d = date("Ymd");
if(isset($_GET['d']) && strlen($_GET['d']) == 8 && is_numeric($_GET['d']) ){
	$d = $_GET['d'];
}

$lannee = substr($d,0,4);
$lemois = substr($d,4,2);
$lejour = substr($d,6,2);

$sqldate = $lannee."-".$lemois."-".$lejour;
$JourMax =date("t",mktime(0,0,0,$lemois,1,$lannee));
$Joursemin =date("w",mktime(0,0,0,$lemois,1,$lannee));
$julienlast = gregoriantojd ( $lemois,$lejour,$lannee );
$lemoislettre = $NomDuMois[abs($lemois)];
$lejourlettre = $NomDuJour[jddayofweek($julienlast,0)];
$selectedday = mktime(0,0,0,$lemois,$lejour,$lannee);
$lenumdesemaine = date("W", $selectedday);


$pMois = $lemois-1;
$pAnnee = $lannee;
if($pMois < 1){
	$pMois = 12;
	$pAnnee--;
}
$pJourMax =date("t",mktime(0,0,0,$pMois,1,$pAnnee));

if($part=='jour'){
	$interval = "days";
	$printcurrent="$lejourlettre $lejour $lemoislettre $lannee";
}
elseif($part=='semaine'){
	$interval = "weeks";
	$printcurrent="$lannee, semaine $lenumdesemaine";
}
elseif($part=='mois'){
	$interval = "months";
	$printcurrent="$lemoislettre $lannee";
}
elseif($part=='annee'){
	$interval = "years";
	$printcurrent="Année $lannee";
}
else{
	$interval = "days";
	$printcurrent="Agenda";
}

$nextjour = date("d",strtotime("+1 $interval",$selectedday));
$nextmois = date("m",strtotime("+1 $interval",$selectedday));
$nextan = date("Y",strtotime("+1 $interval",$selectedday));

$prevjour = date("d",strtotime("-1 $interval",$selectedday));
$prevmois = date("m",strtotime("-1 $interval",$selectedday));
$prevan = date("Y",strtotime("-1 $interval",$selectedday));


if(strlen ($nextjour)==1){ $nextjour="0$nextjour";}
if(strlen ($nextmois)==1){ $nextmois="0$nextmois";}
if(strlen ($prevjour)==1){ $prevjour="0$prevjour";}
if(strlen ($prevmois)==1){ $prevmois="0$prevmois";}
$nextdate = "$nextan$nextmois$nextjour";
$prevdate="$prevan$prevmois$prevjour";


echo"

<script language=\"JavaScript\" type=\"text/javascript\">

Position.includeScrollOffsets = true;

function loadrag(){
	var t1 = document.getElementById(\"agetable\");
	var trs = t1.getElementsByTagName(\"table\");
	for (var i = 0; i < trs.length; i++){
		var drag_text = trs[i].innerHTML;
		new Draggable(trs[i], {revert:true});		
	}
	var divs = t1.getElementsByTagName(\"div\");
	for (var j = 0; j < divs.length; j++){
		if(divs[j].className=='aam' || divs[j].className=='apm' || divs[j].className=='hou'){
			Droppables.add(divs[j], { hoverclass:'hoverclass123', onDrop:function(element, dropon, event){ dropdate(element.id,dropon.id)}});
		}
	}
}
window.onload = function(){
	loadrag();	
};

function dropdate(ki,kan){
 //kan = kan.substr(0,kan.length-2);
 de = 'agenda_'+ki.substr(0,ki.indexOf('_'));
 ki = ki.substr(ki.indexOf('_')+1,ki.length);
 changedate(ki,de,kan);
 loadrag();	
}



</script>
<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr>
		<td valign='top' width='20' class='buttontd'  style='text-align:right'><a href='./?option=$option&part=$part&d=$prevdate'><img src='$style_url/$theme/fl_g.png' alt='<<' border='none'></a></td>
		<td valign='top' class='menuselected' width='120'><span class='gras'>$printcurrent</span></td>
		<td class='buttontd' style='text-align:left'><a href='./?option=$option&part=$part&d=$nextdate'><img src='$style_url/$theme/fl_r.png' alt='>>' border='none'></a><div style='position:relative;float:right'>";
		if($debit==0){
			echo"
			
			<a style='cursor:pointer' onclick=\"document.getElementById('expocal').style.visibility='visible'\">Exporter cette page</a>
			<div style='position:absolute;top:0px;right:0px;width:400px;visibility:hidden;z-index:250;' id='expocal'>
	<table width='400' cellpadding='5' cellspacing='0' border='0' class='cadre'>
	<tr><td class='buttontd'>Exporter l'agenda</td></tr>
	<tr><td  class='cadre' style='border-width:0px;padding:5px'>
		<form action='./?option=$option&part=$part&d=$d' method='post'>
		Prendre en compte :<br>
		<input type='radio' value='0' name='pref' checked> Uniquement les date en cours<br>
		<input type='radio' value='1' name='pref'> Tous<br><br>
		Format de fichier :<br>
		<input type='radio' value='vcs' name='export' checked> <b>vcs</b> (outlook / appareils mobiles)<br>
		<input type='radio' value='csv' name='export'> <b>csv</b> (excell)<br><br>
		Décallage horaire :<br>
		<select name='dec_sens'>";
		for($i=0 ; $i<2 ; $i++){
			$s='';
			if($senss[$i]==$decs) $s='selected';
			echo"<option>$senss[$i]</option>";
		}
		echo"</select>
		<select name='nb_sens'>";
		for($i=0 ; $i<24 ; $i++){
			$s='';
			if($i==$nbs) $s='selected';
			echo"<option $s>$i</option>";
		}
		echo"</select>h<br><br>
		<input type='button' class='buttontd' onclick=\"document.getElementById('expocal').style.visibility='hidden'\" value='Annuler'>
		<input type='submit' class='buttontd' value='Exporter'>
	
		</form>
	</td></tr></table>
	</div>
			";
		}
		if(is_file('bin/_agenda_sync.php')){
			$path = substr(getenv('SCRIPT_NAME'),0,strrpos(getenv('SCRIPT_NAME'),"/"));
			echo" <a href='http://$prov/$path/bin/_agenda_sync.php' target='_blank'>Vcs</a>";	
		}
		 echo"</div><td>
	</tr>
	<tr><td valign='top' class='cadre' colspan='3'>
";
///////////////////////////////////// EDIT
if(isset($_GET['id']) || $part == "nouveau"){
	$id = $_GET['id'];
	$conn = connecte($base, $host, $login, $passe);
	
	$res = mysql_query("SELECT * FROM `$agenda_base` WHERE `id`='$id' AND (`only`='0' OR `only`='$u_id')");
	if($res && mysql_num_rows($res)>0){
	$ro = mysql_fetch_object($res);
	 $m_id = $ro->id;
	 $m_heure = $ro->heure;
	 $m_usr = $ro->usr;
	 $m_date = $ro->date;
	 $m_qui = addslashes($ro->qui);
	 $m_type = addslashes($ro->type);
	 $m_client = addslashes($ro->client);
	 $m_note = addslashes($ro->note);
	 $m_etat = $ro->etat;
	 $m_priority = $ro->priority;
	 $m_only = $ro->only;
	 $m_couleur = $ro->couleur;
	 $action="update&edit=$id";
	}
	else{
		$date = date('Y-m-d');
		if(isset($_GET['date'])){
			$date= $_GET['date'];
		}
		$heure = date('H');
		if(isset($_GET['heure'])){
			$heure= $_GET['heure'];
		}
		 $m_heure = $heure.':00:00';
		 $m_date = $date;
		 $m_qui = $u_nom;
		 $m_type = '';
		 $m_client = '';
		 $m_note = '';
		 $m_etat = 0;
		 $m_priority = 1;
		 $m_only = 0;
		 $m_couleur = '99CCCC';		 
	 	$action="add";
	}
	echo"	<table width='200' cellpadding='1' cellspacing='0' border='0' style='width:200px;height:300px'>
	<tr><td colspan='2'>
	<form action='./?option=$option&part=$part&$action' method='post' name='agendaform2'>
	<table cellspacing='0' cellpadding='0'  border='0'>
			<tr><td colspan='2'><select name='usr'>";
	$rescontacts = mysql_query("SELECT * FROM `adeli_users` ORDER BY `nom`");
	while($row = mysql_fetch_object($rescontacts)){
		$c_id = $row->id;
		$c_nom = $row->nom;	
		$s='';
		if(($m_usr!=NULL  && $c_id==$m_usr) || ($m_usr==NULL && $c_id==$u_id)) $s='selected';
		echo"<option value='$c_id' $s>$c_nom</option>";
	}
	echo"</select>
		</td></tr>";
		if($debit==0){
	echo"
	<tr><td>
		Date :</td><td><img src='$style_url/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"contextdate(event);cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=agendaform.date&amp;date='+document.agendaform.date.value+'&amp;type=date'\"><input type='date' name=\"date\" value=\"$m_date\" maxlength=\"10\" style=\"width:80px\">
		</td></tr>
		<tr><td>
		Heure : </td><td><img src='$style_url/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"contextdate(event);cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=agendaform.heure&amp;date='+document.agendaform.heure.value+'&amp;type=time'\"><input type='text' name=\"time\" value=\"$m_heure\" maxlength=\"10\" style=\"width:60px\">
		</td></tr>
";
		}
		else{
	echo"
	<tr><td>
		Date :</td><td><input type='date' name=\"date\" value=\"$m_date\"/>
		</td></tr>
		<tr><td>
		Heure : </td><td><input type='time' step='900' name=\"heure\" value=\"$m_heure\"/>
		</td></tr>
";
		}
echo"
		<tr><td>
		Avec :</td><td><input type='text' name=\"client\" value=\"$m_client\" style=\"width:150px\">
		</td></tr>
		<tr><td valign='top'>
		Type : <br><br></td><td valign='top'><input id='typo_txt' type='text' name='type' value='$m_type'></td></tr>
		<tr><td colspan='2'>
		<textarea name=\"note\" style=\"width:190px;height:120px;font-size:12px\">$m_note</textarea>
		</td></tr>
		<tr><td>
		Lieu :</td><td> <input type='text' name=\"qui\" value=\"$m_qui\" style=\"width:150px\">
		</td></tr>
		<tr><td>
		Couleur :</td><td><div style='position:relative'>";
		if($debit==0){
		echo"
		<input type=\"hidden\" name=\"couleur\" value=\"$m_couleur\" style='position:absolute;'>
							<div id=\"divcol\" style=\"background-color:#$m_couleur;padding:3px;position:absolute;height:20px;width:30px;border-color:#000000;border-width:1px;border-style:solid;\"></div></div><br>
							<table><tr><td>";
							$ret = mysql_query("SELECT DISTINCT `couleur` FROM `$agenda_base` WHERE `couleur`!='' ORDER BY`couleur`");
							while($rot=mysql_fetch_object($ret)){
								echo"<div style='background-color:#".($rot->couleur).";float:left;border-color:#000000;border-width:1px;border-style:solid;' onclick=\"document.agendaform.couleur.value='".($rot->couleur)."';document.getElementById('divcol').style.backgroundColor='".($rot->couleur)."'\">&nbsp;&nbsp;</div>";
							}
							mysql_close($conn);
							echo "</td></tr></table>
							<div style='position:relative'></div>
							<a style='cursor:pointer' onclick=\"choosecolor('','','','age',event)\">nouvelle couleur</a>";
		}
		else{
							echo"<table><tr><td>";
							$ret = mysql_query("SELECT DISTINCT `couleur` FROM `$agenda_base` WHERE `couleur`!='' ORDER BY`couleur`");
							while($rot=mysql_fetch_object($ret)){
								$s='';
								if($rot->couleur == $m_couleur) $s='checked';
								echo"<div style='background-color:#".($rot->couleur).";float:left;border-color:#000000;border-width:1px;border-style:solid;'><input type='radio' name='couleur' $s value='".($rot->couleur)."'></div>";
							}
							mysql_close($conn);
							echo "</td></tr></table>";
		}
		
		
		echo"							
		</td></tr>
		<tr><td>
		&Eacute;tat : </td><td><select name=\"etat\">
									<option value=\"0\">$statg[0]</option>
									<option value=\"1\">$statg[1]</option>
									</select>
		</td></tr>
		<tr><td>
		Priorité : </td><td><select name=\"priority\">
									<option>1</option>
									<option>2</option>
									<option>3</option>
									</select>
		</td></tr>
		<tr><td>
		Visible : </td><td><select name=\"only\">
									<option value='0'>Par tous les utilisateurs</option>
									<option value='$u_id'>Seulement moi</option>
									</select>
		</td></tr>	
		<tr><td colspan='2'  align='right'><input type='submit' class='grosbouton' value='Enregistrer'>
		</td></tr>
		</table>
	
		</form>
	</td></tr></table>
	<script language=\"JavaScript\">
	document.agendaform2.only.value='$m_only';
	document.agendaform2.couleur.value='$m_couleur';
	document.agendaform2.priority.value='$m_priority';
	document.agendaform2.etat.value='$m_etat';
	</script>
	";

}
/////////////////////////////////// JOUR
elseif($part == "jour"){
	echo"<table width='100%' class='bando' id='agetable' cellpadding='2' cellspacing='1' border='0'>
	<tr><td colspan='2' class='cadre' style='border-width:0px;padding:5px' >";
	for($i=1 ; $i<7 ; $i++){ 		
		$conn = connecte($base, $host, $login, $passe);
		scandate($x_id,$sqldate,$i,1,0);
		mysql_close($conn);
	}
	echo"</td></tr>";
	for($i=8 ; $i<19 ; $i++){ 		
		echo"<tr><td align='center' width='30' class='fondmediumlignt' valign='top'><span class='gras'>$i h</span></td>";
		if($debit==0){
		echo"<td class='cadre' style='border-width:0px;padding:5px' >";
		// ondblClick=\"contextage('$sqldate','$i:00:00',event,'Ajouter une date','add&$part','99CCCC',0)\"
		}
		else{
			echo"<td class='cadre' style='border-width:0px;padding:5px' ><a href='./?option=$option&part=$part&id&date=$sqldate&heure=$i'>+</a>";
		}
		$conn = connecte($base, $host, $login, $passe);
		scandate($x_id,$sqldate,$i);
		mysql_close($conn);
		echo"&nbsp;</td></tr>";
	}
	echo"<tr><td colspan='2' class='cadre' style='border-width:0px;padding:5px' >";
	for($i=20 ; $i<24 ; $i++){ 		
		$conn = connecte($base, $host, $login, $passe);
		scandate($x_id,$sqldate,$i,1,0);
		mysql_close($conn);
	}
	echo"</td></tr></table>";
}
/////////////////////////////////// SEMAINE
elseif($part == "semaine"){
	echo"<table width='100%' class='bando' id='agetable' cellpadding='2' cellspacing='1' border='0'>
	<tr><td><a style='cursor:pointer' onclick='repli()' id='repltxt'></a></td>";
	$td = date('w',$selectedday);
	if($td==0) $td=7;
	$fd = strtotime('-'.( $td-1 ).' days',$selectedday);
	for($i=1 ; $i<8 ; $i++){ 
		$numofsem = $lenumdesemaine-1;
		/*$e = $i;
		$numberofday = date("d",strtotime("+$numofsem weeks $e days",mktime(0,0,0,1,1,$lannee)));
		$numberofmonth = date("m",strtotime("+$numofsem weeks $e days",mktime(0,0,0,1,1,$lannee)));
		$numberofyear = date("Y",strtotime("+$numofsem weeks $e days",mktime(0,0,0,1,1,$lannee)));*/
		
		$numberofday = date("d",$fd);
		$numberofmonth = date("m",$fd);
		$numberofyear = date("Y",$fd);
		
		$fd+=60*60*24;
			
		$sqldate = $numberofyear."-".$numberofmonth."-".$numberofday;
		
		$numberofday = abs($numberofday);
		if( date("Ymd",mktime(0,0,0,abs($numberofmonth),$numberofday,$numberofyear)) == date("Ymd")){
			echo"<td align='justify' width='20%' class='cadre' style='padding:2px' valign='top'>";
		}
		else{
			echo"<td align='justify' width='20%' class='fondmedium' valign='top'>";
		}			
		echo"<span class='gras'>".substr($NomDuJoursemaine[$i],0,3)."&nbsp;$numberofday/$numberofmonth</span><br></td>";		
	}
	echo"</tr>";
	
	for($h=1 ; $h<24 ; $h++){	
		echo"<tr><td style='padding:2px' valign='top'><div id='heur$h' style='position:relative;height:20px;overflow:inherit;padding:3px;'>$h&nbsp;h</div></td>";
		$fd = strtotime('-'.( $td-1 ).' days',$selectedday);
		for($i=1 ; $i<8 ; $i++){ 
			$numberofday = date("d",$fd);
			$numberofmonth = date("m",$fd);
			$numberofyear = date("Y",$fd);
			
			$fd+=60*60*24;
			
			$sqldate = $numberofyear."-".$numberofmonth."-".$numberofday;
			if($debit==0){
				if( $sqldate.$h == date("Y-m-dG")){	
					echo"<td class='cadre' style='padding:0px; border:#F90 3px outset;'  valign='top'>";
					//ondblClick=\"contextage('$sqldate','$h:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate$h','$sqldate&h=$h&print=1')\"
				}
				elseif( $sqldate == date("Y-m-d")){	
					echo"<td class='cadre' style='padding:0px; border:#F90 1px outset;'  valign='top'>";
					//ondblClick=\"contextage('$sqldate','$h:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate$h','$sqldate&h=$h&print=1')\"
				}
				else{	
					echo"<td class='cadre' style='padding:0px;border-width:0px'  valign='top'>";
					//ondblClick=\"contextage('$sqldate','$h:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate$h','$sqldate&h=$h&print=1')\" 
				} 
					
			}
			else{
				if( $sqldate.$h == date("Y-m-dG")){	
					echo"<td class='cadre' style='padding:5px; border:#F90 3px outset;'  valign='top'>";
				}
				elseif( $sqldate == date("Y-m-d")){	
					echo"<td class='bando' style='padding:5px; border:#F90 1px outset;'  valign='top'>";
				}
				else{	
					echo"<td class='cadre' style='padding:5px;border-width:0px' valign='top'>";			
				}
				echo"<a href='./?option=$option&part=$part&id&date=$sqldate&heure=$h'>+</a>";	
			}
			echo"<div id='cont$i.$h' style='position:relative;overflow:inherit;padding:5px;'>";	
			$conn = connecte($base, $host, $login, $passe);
			scandate($x_id,$sqldate,$h);
			mysql_close($conn);
			echo"&nbsp;</div></td>";
		}
		echo"</tr>";		
	}
	echo"</table>
	<script language='javascript'>
	rep=false;
	function repli(){
		if(rep==true){
			wi = '20px';
			ov = 'inherit';
			pa = '3px';
			fs= '10px';
			document.getElementById('repltxt').innerHTML='Réduite la journée';
			rep=false;
		}
		else{
			wi = '0px';
			ov = 'hidden';
			pa = '0px';
			fs= '1px';
			document.getElementById('repltxt').innerHTML='Tout voir';
			rep=true;
		}
		for(i=1 ; i<24 ; i++){
			if(i<8 || i>18 || i==12 || i==13){
				document.getElementById('heur'+i).style.height=wi;
				document.getElementById('heur'+i).style.overflow=ov;
				document.getElementById('heur'+i).style.padding=pa;
				document.getElementById('heur'+i).style.fontSize=fs;
				for(j=1 ; j<8 ; j++){
					document.getElementById('cont'+j+'.'+i).style.height=wi;
					document.getElementById('cont'+j+'.'+i).style.position='relative';
					document.getElementById('cont'+j+'.'+i).style.overflow=ov;
					document.getElementById('cont'+j+'.'+i).style.padding=pa;
					document.getElementById('cont'+j+'.'+i).style.fontSize=fs;
				}
			}
		}		
	}
	repli();
	</script>
	
	";
}
/////////////////////////////////// MOIS
elseif($part == "mois"){
	echo"<table width='100%' class='bando' id='agetable' cellpadding='2' cellspacing='1' border='0'>
	<tr><td><font size='1'>s.</font></td>";
	for($d=1 ; $d<=7 ; $d++){
		if($debit==0) echo"<td style='padding:5px' width='20%'><span class='textegrasfonce'>$NomDuJoursemaine[$d]</span></td>";
		else echo"<td style='padding:2px'>".strtoupper(substr($NomDuJoursemaine[$d],0,1))."</td>";
	}
	echo"</tr>";

$nbj = 0;
$NoJour = -date("w",mktime(0,0,0,$lemois,1,$lannee));     
$NoJour +=2 ;          
if ($NoJour >0) { 
	$NoJour -=7;
}           

for ($semaine=0;$semaine <=5;$semaine++) {   
	$semo = date("W",mktime (0,0,0,$lemois,$NoJour,$lannee) );
	if($debit==0) echo"<tr><td align='justify' width='40' class='fondmediumlignt' valign='top'><span class='gras'>$semo</span></td>";
	else echo"<tr><td valign='top'>$semo</td>";
	for($d=1 ; $d<=7 ; $d++){
		$decal = $NoJour-1;
		if($decal >= 0){
			$decal = "+ $decal";
		}
		$CeNoJour = date("j",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
		
		$cenojour = date("d",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
		$cenomois = date("m",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
		$cenoan = date("Y",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
		
		$sqldate = "$cenoan-$cenomois-$cenojour";
		$ldate = "$cenoan$cenomois$cenojour";
		if($debit==0){
			if( $sqldate == $_SESSION["date"]){
				echo"<td class='cadre' style='padding:5px; border:#F90 3px outset;' valign='top' ><a onclick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\">$CeNoJour</a> ";
				// ondblClick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\"
			}
			elseif ($NoJour >0 && $NoJour <= $JourMax ){		
				echo"<td valign='top'  class='cadre' style='border-width:0px' ><a onclick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\">$CeNoJour</a> ";
				//ondblClick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\"
			}
			else{
				echo"<td class='fondmedium' valign='top' ><a onclick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\">$CeNoJour</a> ";
				// ondblClick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\"
			}
		}
		else{
			if( $sqldate == $_SESSION["date"]){
				echo"<td class='bando' style='padding:5px; border:#F90 3px outset;' valign='top'><a href='./?option=$option&part=jour&d=$ldate'>$CeNoJour</a> ";
			}
			elseif ($NoJour >0 && $NoJour <= $JourMax ){		
				echo"<td valign='top'  class='cadre' style='border-width:0px'><a href='./?option=$option&part=jour&d=$ldate'>$CeNoJour</a> ";
			}
			else{
				echo"<td class='fondmedium' valign='top'><a href='./?option=$option&part=jour&d=$ldate'>$CeNoJour</a> ";
			}

		}
		echo"<br>";
		
		
		$conn = connecte($base, $host, $login, $passe);
		if($debit==0){
			 scandate($x_id,$sqldate,"none",1);
		}
		else{
			 if(scandate($x_id,$sqldate,"none",0)>0){
				echo"<a href='./?option=$option&part=jour&d=$ldate'>*</a>"; 
			 }
		}
		mysql_close($conn);
		echo"</td>";
		$NoJour++;
	}
}	
	echo"</table>";
}
////////////////////////////////// ANNEE
elseif($part == "annee"){
	echo"<table width='100%' class='bando' id='agetable' cellpadding='2' cellspacing='1' border='0'>
	<tr><td>";
	for($lemois=1 ; $lemois<=12 ; $lemois++){
		$lm=$lemois;
		if($lemois<10){
			$lm='0'.$lemois;
		}
	echo"<div style='margin:2px;position:relative;float:left;width:200px;height:200px;'>
	<table cellpadding='0' cellspacing='0' border='0'>
	
	<tr><td colspan='7'><a href='./?mois&d=$lannee$lm"."01'>".$NomDuMois[$lemois]."</a></td></tr>
	<tr>";
	for($d=1 ; $d<=7 ; $d++){
		echo"<td><span class='textegrasfonce'>".strtoupper(substr($NomDuJoursemaine[$d],0,1))."</span></td>";
	}
	echo"</tr>";
	


$nbj = 0;
$JourMax =date("t",mktime(0,0,0,$lemois,1,$lannee));
$NoJour = -date("w",mktime(0,0,0,$lemois,1,$lannee));     
$NoJour +=2 ;          
if ($NoJour >0) { 
	$NoJour -=7;
}           

for ($semaine=0;$semaine <=5;$semaine++) {   
	$semo = date("W",mktime (0,0,0,$lemois,$NoJour,$lannee) );
	//echo"<tr><td align='justify' width='70' class='fondmediumlignt' valign='top'><span class='gras'>$semo</span></td>";
	echo"<tr>";
	for($d=1 ; $d<=7 ; $d++){
		$decal = $NoJour-1;
		if($decal >= 0){
			$decal = "+ $decal";
		}
		$CeNoJour = date("j",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
		
		$cenojour = date("d",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
		$cenomois = date("m",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
		$cenoan = date("Y",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
		
		$sqldate = "$cenoan-$cenomois-$cenojour";
		
		if ($NoJour >0 && $NoJour <= $JourMax ){	
			if( date('Ymd',mktime(0,0,0,$lemois,$NoJour,$lannee)) == date('Ymd')){
			echo"<td class='cadre' style='padding:5px' valign='top'  ondblClick=\"contextage('$sqldate','10:00:00',event,'Modifier','add&$part','99CCCC',0)\"><font size='1'>$CeNoJour</font><br>";
		}
		else{	
			echo"<td class='cadre' style='border-width:0px;padding:5px' valign='top'  ><font size='1'>$CeNoJour</font><br>";
			//ondblClick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0)\"
			}
					$conn = connecte($base, $host, $login, $passe);
					scandate($x_id,$sqldate,"none",0);
					mysql_close($conn);
		}
		else{
			echo"<td class='fondmedium'>&nbsp;<br>";
		}
	
		echo"</td>";
		$NoJour++;
	}
}	


	echo"</table></div>";
	}
	echo"</td></tr></table>";
}
///////////////////////////////// LISTE

elseif($part == "liste"){
	if(!isset($_SESSION["printall"])){
		$_SESSION["printall"]=0;
	}
	if(isset($_GET["printall"])){
		$_SESSION["printall"]=$_GET["printall"];
	}
	if($_SESSION["printall"]==0){
		$sqlnow_date = date("Y-m-d");
		$sqlnow_time = date("H:i:s");
		echo"<b>N'afficher que les dates à venir</b> | <a href='./?option=$option&part=$part&d=$d&printall=1'>afficher tout</a>";
		$plusql = " AND (`date`>'$sqlnow_date' OR (`date`='$sqlnow_date' AND `heure`>'$sqlnow_time'))";
	}
	else{
		echo"<a href='./?option=$option&part=$part&d=$d&printall=0'>N'afficher que les dates à venir</a> | <b>afficher tout</b>";
		$plusql = "";
	}
	$conn = connecte($base, $host, $login, $passe);
	
	echo"<hr>
	<table cellspacing='0' cellpadding='1' border='0' class='cadre'>
	<tr class=\"buttontd\">
		<td></td>
		<td>date</td>
		<td>qui</td>
		<td>notes</td>
		<td>état</td></tr>
	<tr><td colspan=\"5\">
	<a  name=\"ho\" href=\"#ho\" ondblClick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0)\">
		<b>Ajouter une date</b></a><hr>
	</td></tr>
	";
	$bgtd = '1';
	$res = mysql_query("SELECT * FROM `$agenda_base` WHERE 1 $plusql ORDER BY `date`DESC,`heure`DESC ");
	while($ro = mysql_fetch_object($res)){
		 $m_id = $ro->id;
		 $m_heure = $ro->heure;
		 $m_date = $ro->date;
		 $m_qui = addslashes($ro->qui);
		 $m_type = addslashes($ro->type);
		 $m_client = addslashes($ro->client);
		 $m_note = addslashes($ro->note);
		 $m_etat = $ro->etat;
		 $m_hda = date("d/m/Y, H:i",strtotime("$m_date $m_heure"));
		 $m_couleur = $ro->couleur;
	   if($bgtd == '1'){
		$bgtd='2';
		echo"<tr class='listone' ";
	   }
	   else{
		$bgtd='1';
		echo"<tr class='listtwo' ";
	   }
		echo"
		ondblClick=\"contextage('$m_date','$m_heure',event,'Modifier','update=$m_id&$part','$m_couleur',$m_id);
		document.agendaform.qui.value='$m_qui';
		document.agendaform.type.value='$m_type';
		document.agendaform.client.value='$m_client';
		document.agendaform.note.value='$m_note';\">
		<td>
		<div style='background-color:#$m_couleur;cursor:default;display:block;padding:3px;position:relative;z-index:150;width:10px;height:10px' ><span</span> </div>
		</td>
		<td>$m_hda</td>
		<td><b>$m_qui <u>$m_client</u></b></td>
		<td>$m_note</td>
		<td>$statg[$m_etat]</td></tr>";

	}
	mysql_close($conn);
	echo"<tr><td colspan=\"5\"><hr>
	<a name=\"ba\" href=\"#ba\"ondblClick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0)\">
		<b>Ajouter une date</b></a>
	</td></tr>
	</table>";
}

echo"</td></tr></table>";
if(isset($_POST["export"])){
	if($export=='vcs'){
		$output_export.="END:VCALENDAR\r\n";
	}
	$fp=fopen("tmp/outpumim","w+");
	fwrite($fp,'text/x-vcalendar');
	$fp=fopen("tmp/outputxt","w+");
	fwrite($fp,$output_export);
	$fp=fopen("tmp/outpufi","w+");
	fwrite($fp,'agenda.'.$export);
	fclose($fp);
	if(!is_dir("sync")){
		mkdir("sync");
	}
	if(is_dir("sync")){
		$fp=fopen("sync/agenda.vcs","w+");
		fwrite($fp,$output_export);
		fclose($fp);
	}
	echo"<script language='javascript'>
	open('$openexpo"."outdown.php','calendar','width=100,height=100');
	</script>";
}

}
	
else{ ///////////////////////////////// accueil
	echo"
	<center>
	<table width='100%' class='bando' id='agetable' cellpadding='0' cellspacing='1' border='0'>
	<tr><td class='buttontd'>afficher :
	<a href='./?option=agenda&jour' class='buttontd'><span>aujourd'hui</span></a>
	<a href='./?option=agenda&semaine' class='buttontd'><span>semaine</span></a>
	<a href='./?option=agenda&mois' class='buttontd'><span>mois</span></a>
	<a href='./?option=agenda&annee' class='buttontd'><span>année</span></a></td></tr>
	<tr><td class='cadre' style='border-width:0px;padding:5px' ondblClick=\"contextage('$sqldate','$i:00:00',event,'Ajouter une date','add&$part','99CCCC',0)\">";
	$conn = connecte($base, $host, $login, $passe);
		scandate($x_id);
	mysql_close($conn);
	echo"</td></tr></table>
	</center>
	<br>
	";
}
}
else{
mysql_close($conn);
	if(isset($_GET['mktb'])){	
		$conn = connecte($base, $host, $login, $passe);
		if(mysql_query("CREATE TABLE `$agenda_base` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `usr` bigint(20) NOT NULL default '0',
		  `date` date NOT NULL default '0000-00-00',
		  `heure` time NOT NULL default '00:00:00',
		  `qui` varchar(255) NOT NULL default '',
		  `type` varchar(255) NOT NULL default '',
		  `client` varchar(255) NOT NULL default '',
		  `couleur` varchar(6) NOT NULL default '99CCCC',
		  `note` text NOT NULL,
		  `etat` int(1) NOT NULL default '0',
		  `only` bigint(20) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
			)
		") ){
			$return.=returnn("La table <b>\"Agenda\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour redémarrer <b>\"Agenda\"</b></a>","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La table Agenda n'a pu être créée correctement (".mysql_error().")","990000",$vers,$theme);
		}
		mysql_close($conn);
	}
	echo"	
	<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Mail</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadre' colspan='2' align='center'>

	Votre base de données n'est pas configurée avec une table <b>\"Agenda\"</b>...<br><br>
	Voulez vous que <b>Adeli</b> essai de la créer automatiquement ?<br><br>
	<a href='./?option=$option&part=$part&mktb'>créer le tableau</a>
	
	</td></tr></table>
	";
}
?>
