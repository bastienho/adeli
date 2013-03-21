<?php // 126 > Bureau ;

//////////////////////////////////////////////////// MOBILE
if($debit==1){
	echo"
	<table cellpadding='5' cellspacing='2' width='250' class='cadre'>
	<tr><td >$phra, $u_nom</td></tr>
	<tr><td>
	";
	for($i = 0 ; $i<sizeof($opt)-1 ; $i++){
		if($opt[$i]!='aide' && $opt[$i]!=''){
		echo"<div style='margin:1px;width:45px;height:45px;float:left;'><a href='./?option=$opt[$i]'><img src='$style_url/img/$opt[$i].png' alt='$opt[$i]' border='none'></a></div>";
		}
	}
	echo"</td></tr>
	<tr><td>";
			if(in_array('site',$opt) && isset($menu_site)){
			if(isset($_GET['wsearch'])){
				set_pref('wsearch',trim($_GET['wsearch']));
			}
			$ws = set_pref('wsearch');
			echo"
			<form action='./' method='get' name='multisearch'>
			<input type='text' name='d' style='font-size:10px;width:170px'><br>
			
			<input type='hidden' name='option' value='site'>			
			<input type='hidden' name='$ws' id='tablsearch'>			
			<input type='hidden' name='q' id='plus'>								
			<select onchange=\"document.getElementById('tablsearch').name=this.value;document.multisearch.option.value=jsoptio[this.value];\" name='wsearch' style='font-size:10px;width:90px;'>
			";
			$site_menupart = array_keys($menu_site);
			$gepa='site';
			$jsoptio='';
			for($i=0; $i<sizeof($menu_site) ; $i++){
	 			
				$spart = $site_menupart[$i];
				$sepa='site';
				if(substr($spart,0,7)=='worknet') $sepa='worknet';
				if(substr($spart,0,7)=='gestion') $sepa='gestion';
				
				$tablo = $menu_site[$spart];
				$cols = sizeof($tablo);			
				$tablk = array_keys($tablo);
				for($m=0; $m<sizeof($tablo) ; $m++){
					$tk = $tablk[$m];
					if(!is_numeric($tk)){
						$humanpart = $tk;
					}
					else{
						$humanpart = $tablo[$tk];
						if($prefixe != ""){
							$humanpart = str_replace($prefixe,"",$humanpart);
						}
						$humanpart = str_replace($spart."_","",$humanpart);
						$humanpart = str_replace("adeli_","",$humanpart);
						$humanpart = str_replace(">$spart","",$humanpart);
						$humanpart = str_replace("-$spart","",$humanpart);
						$humanpart = str_replace(">"," ",$humanpart);	
					}
					$humanpart = ucfirst($humanpart);	
					if(mysql_query("SHOW COLUMNS FROM `$tablo[$tk]`")  ){
						$s='';
						if($ws == $tablo[$tk]){
							$s='selected';
							$gepa=$sepa;
						}
						$jsoptio.="jsoptio['$tablo[$tk]']='$sepa';\n";
						echo"<option value='$tablo[$tk]' $s>$humanpart</option>";
					}				
				}		
			}
			echo"</select><br><input type='submit' value='Recherche' style='font-size:9px;border:none;background:none;padding:0px;'> 
			<a onclick=\"document.getElementById('tablsearch').name=document.multisearch.wsearch.value;document.multisearch.option.value=jsoptio[document.multisearch.wsearch.value];document.getElementById('plus').name='annuaire';document.multisearch.submit();\">Annuaire</a>
			<a onclick=\"document.getElementById('tablsearch').name=document.multisearch.wsearch.value;document.multisearch.option.value=jsoptio[document.multisearch.wsearch.value];document.getElementById('plus').name='edit';document.multisearch.submit();\">Ajouter</a>
			</form>
			<script language='javascript'> 
				jsoptio=new Array();
				$jsoptio
				document.multisearch.option.value='$gepa'; 
			</script>";
		}
	echo"</td></tr>
	";
	if(in_array("mail",$optico)){ 
		echo"<tr><td valign='top' style='text-align:left'><span class='textegrasfonce'>Mail</span><hr>";
	 $res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') ORDER BY `nom`");
		if($res && mysql_num_rows($res)>0){
			while($ro = mysql_fetch_object($res)){
				$b_nom = $ro->nom;
				$b_id = $ro->id;
				$b_serveur = $ro->serveur;
				$b_login = $ro->login;
				$b_pass = $ro->pass;
				$b_dossier = $ro->dossier;
				$b_port = $ro->port;
				if(false !== $mbox = imap_open("\{$b_serveur:$b_port$b_dossier}$mail",$b_login,$b_pass) ){     
						//$num_msg = imap_num_msg ($mbox);	
						$status = imap_status($mbox, "\{$b_serveur:$b_port$b_dossier}INBOX", SA_ALL);
						//if ($status) {
						$num_msg = $status->messages;
						$difmes = $status->unseen;//$num_msg-$fval;
						if($difmes>0){	
							echo utf8_encode("- <a href='bin/_mail_lecteur.php?mail=INBOX&part=lecture&b=$b_id'>$b_nom&nbsp;<b>($difmes)</b></a>&nbsp;|&nbsp;<a href='./?option=mail&b=$b_id&lecture'>dossiers</a><br>");				
						}
						else{
							echo utf8_encode("- <a href='bin/_mail_lecteur.php?mail=INBOX&part=lecture&b=$b_id'>$b_nom</a>&nbsp;|&nbsp;<a href='./?option=mail&b=$b_id&lecture'>dossiers</a><br>");
						}
				}
				else{
					echo"<font color='CCCCCC'>- $b_nom</font><br>";
				}
			}
			mysql_free_result($res);
		}
		echo"</td></tr>";
	}
	if(in_array("agenda",$optico) && isset($agenda_base)){  ///////// AGENDA
			$sqlnow_date = date("Y-m-d");
			$sqlnow_time = date("H:i:s");
	echo"<tr><td valign='top' id='agetable'><a href='./?option=agenda'><span class='textegrasfonce'>Agenda</span></a><hr><a href='./?option=agenda&id='>Nouveau</a>";
			$res = mysql_query("SELECT * FROM `$agenda_base` WHERE `date`<='$sqlnow_date' AND `etat`='0'  ORDER BY `date`,`heure`");
			$totalage = mysql_num_rows($res);
			if($totalage > 0){
				
				while($rowage=mysql_fetch_object($res)){
					$date = $rowage->date;
					$heure = $rowage->heure;
					$hdat = $heure;
					$mdat = date("Ymd",strtotime($date));
					$client = $rowage->client;
					$usr = $rowage->usr;
					$qui = $rowage->qui;
					$note = $rowage->note;
					$type = $rowage->type;
					$etat = $rowage->etat;	
					$only = $rowage->only;				
					$mid = $rowage->id;	
					$priority = $rowage->priority;				
					$couleur = $rowage->couleur;
					$printki=$client;
					if($priority==0){
						$priority=1;
					}
					 if(is_numeric($client) && mysql_query("SHOW COLUMNS FROM `clients`") ){
						$ris = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$client'");
						if($ris && mysql_num_rows($ris)==1){
							$ri = mysql_fetch_object($ris);
							$printki=$ri->nom;
						}
					 }
					 $b_couleur = "#$couleur";
					 if($u_id!=$usr){
						  $b_couleur = "#$couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
					  }
					$prio = "<font style='font-size:".($m_priority*3)."px'>".str_repeat("*",$m_priority)."</font>";		 
						 
					echo"<table cellpadding='1' cellspacing='0' width='90%' style='margin:1px;background:$b_couleur;cursor:default;z-index:150;'><tr><td><a href='./?option=agenda&id=$mid'><font size='2'>$printki</font></a></td><td align='right'>$prio</td></tr></table>";
				}
				
			}
			else{
				echo "aucun événement aujourd'hui>";
			}
		echo"</td></tr>";
	}
	echo"</table>";
	
}
///////////////////////////////////////////////////////////////////////// BUREAU
else{
	echo"<table width='100%'>		
	
	<center>	
	<table cellspacing='0' cellpadding='0' border='0'  width='100%'><tr><td align='center'>	
	
	
	<table cellspacing='0' cellpadding='3' border='0' width='350' height='200' style='margin:5px;float:left;'><tr>
		<td valign='top' class='menuselected' width='150' style='height:30px'><span class='gras'>Vos applications</span></td>
		<td class='buttontd' style='text-align:left' width='200'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center' style='padding:5px'>
	
	<br>
	<style>
	.appli{
		width:100px;
		height:60px;
		display:block;
		position:relative;
		overflow:hidden;
		float:left;
		margin:3px;
		text-align:center;
	}
	.appli img{
	}
	</style>
	";
	for($i = 0 ; $i<sizeof($opt)-1 ; $i++){
		if($opt[$i]!='aide' && $opt[$i]!=''){
		echo"<a class='appli' href='./?option=$opt[$i]'>
		<img src='$style_url/img/$opt[$i].png' alt='$opt[$i]' border='none' /><br/>
		<b>".ucfirst($opt[$i])."</b><br><font class='petittext'> ";
		if(is_file("bin/_$opt[$i].php")){
			$fo = @fopen("bin/_$opt[$i].php","r");
			$ginf = trim(str_replace("//","",str_replace("<?","",str_replace("<?php","",str_replace("<!--","",str_replace("-->","", @fread($fo,100)))))));
			if(ereg(">",$ginf)){
				echo trim(substr($ginf, strpos($ginf,">")+1, strpos($ginf,";")-strpos($ginf,">")-1 )); 
			}
			fclose ($fo);
		}
		echo"</font></a>";
		}
	}
	echo"</td></tr></table>";
	

	echo"
	
	<table cellspacing='0' cellpadding='3' border='0' width='250' height='200' style='margin:5px;float:left;'><tr>
		<td valign='top' class='menuselected' width='150' style='height:30px'><span class='gras'>Nouveautés</span></td>
		<td class='buttontd' style='text-align:left' width='100'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='left' style='padding:5px'>";	
 getrss("http://urbancube.fr/adeli.php",10,250);
echo"</td></tr></table>
	
	
";
$conn = connecte($base, $host, $login, $passe);
if(mysql_query("SHOW COLUMNS FROM `adeli_rss`")  ){
	$res = mysql_query("SELECT * FROM `adeli_rss` WHERE (public='$u_id' OR public=0) AND active=1 AND emplacement=1");
	if($res && mysql_num_rows($res)>0){
		while($ro = mysql_fetch_object($res)){
			$type=$ro->type;
			$url=$ro->url;
			$limite=$ro->limite;
			$nom=ucfirst($ro->nom);
			$rss=$ro->id;
			echo"<table cellspacing='0' cellpadding='2' width='250' height='190' style='margin:5px;float:left;'>
			<tr><td valign='top' class='menuselected' width='150' style='height:30px'><span class='gras'>$nom</span>
			<a href='./?option=reglages&adeli_rss&edit=$rss' class='info'><img src='$style_url/$theme/modif.gif' height='16' alt='modifier' border='none'><span>modifier les paramètres</span></a>";
			if($type==1){
				$urle = split(';',$url);
				echo"&nbsp;<a href='./?option=site&$urle[0]&edit' class='info'><img src='$style_url/$theme/+.png' alt='+' border='none'><span>nouveau</span></a>";
			}
			echo"</td>
	</tr>
	<tr><td valign='top' class='cadrebas' align='left' style='padding:5px'>
			<div style='width:240px;height:160px;position:relative;overflow-y:scroll'>";
			if($type==0){
				getrss($url,$limite,48);
			}
			if($type==1){
				/*if(sizeof($urle)==4){
					$lim="";
					if($limite>0){
						$lim = "LIMIT 0,$limite";
					}
					$sepa='site';
						
					for($m=0; $m<sizeof($menu_site) ; $m++){
						$spart = $site_menupart[$m];
						$tablo = $menu_site[$spart];
						if(in_array($urle[0],$tablo)){							
							if($spart=='worknet') $sepa='worknet';
							if($spart=='gestion') $sepa='gestion';	
							break;
						}
					}
					$rus = mysql_query("SELECT `id`,`$urle[1]`,`$urle[2]` FROM `$urle[0]` WHERE $urle[3] ORDER BY $urle[2] $lim");
					if($rus && mysql_num_rows($rus)>0){
						while($ru = mysql_fetch_object($rus)){
							$rn = $ru->$urle[1];
							$ror = $ru->$urle[2];
							$rid = $ru->id;
							echo"-<a href='./?option=$sepa&$urle[0]&edit=$rid' class='info'>$rn<span style='width:180px;text-decoration:none;'>$ror</span></a><br>";
						}
					}
				}
				*/
				parse_int($urle,$limite);
			}
			echo"</div></td><tr></table>";
		}
	}
}
mysql_close($conn);
echo"<table cellspacing='0' cellpadding='2' width='250' style='margin:10px;float:left;'>
			<tr><td class='buttontd' align='left'>
			<a href='./?option=reglages&adeli_rss&edit&emplacement=1'><span class='textegrasfonce'>ajouter un fil RSS ici</span> </a>
			</td></tr></table>";

echo"
	</td></tr></table>
	<br></center>";
}
?>
