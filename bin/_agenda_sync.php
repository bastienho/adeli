<?php // 1 > Outil de synchronisation de calendrier ;
include("../mconfig/adeli.php");
include("inc_func.php");
$prov = getenv("SERVER_NAME");
if(false !== $conn = @mysql_connect($host, $login, $passe)){
mysql_select_db($base);
if(!isset($agenda_base)) $agenda_base='adeli_agenda';
$sqldate="now";
	$h="none";
	$print=1;
	$maj='agenda_totem';
	$before=0;
	$onlyon=0;
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Type: text/x-vcalendar");
	header("Content-Disposition: inline; filename=adeli_agenda.vcs;" );
	header("Content-Transfer-Encoding: binary");

	echo"BEGIN:VCALENDAR\r\nPRODID:-//Urbancube //Adeli Agenda 1.2.444//FR\r\nVERSION:2.0\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\nX-WR-CALNAME:Adeli Cal $prov \r\nX-WR-TIMEZONE:Europe/Paris\r\n";

	$statous = array('TENTATIVE','CONFIRMED');

	$midi=0;
	$res = mysql_query("SELECT * FROM `$agenda_base` WHERE (`date`>=NOW() OR `etat`=0) ORDER BY `date`,`heure`");
	if($res && mysql_num_rows($res)>0){
			while($ro = mysql_fetch_object($res)){
				 $m_id = $ro->id;
				 $m_usr = $ro->usr;
				 $m_heure = $ro->heure;
				 $m_date = $ro->date;
				 $m_qui = (addslashes($ro->qui));
				 $m_type = (addslashes($ro->type));
				 $m_client = (addslashes($ro->client));
				 $m_note = (addslashes($ro->note));
				 $m_etat = $ro->etat;
				 $m_priority = $ro->priority;
				 $m_only = $ro->only;

				 $printki=$m_client;
				 $nots=substr($m_note,0,20);
				 $m_couleur = $ro->couleur;
					$size=2;
					$marj=1;
					$b_couleur = "-color:#$m_couleur";
					$co=' style="font-weight:bold" ';
						if($m_etat==1){
							$size=1;
							$marj=0;
							$co="color='#$m_couleur'";
							$b_couleur = ':none';
							$nots='';
							$b_couleur = 'none';
						}
						if($m_date==$sqldate && $m_heure>date('h:i:s') && $onlyon==1){
							$size=1;	
							//$b_couleur = ":#$m_couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
						}
						if($u_id!=$m_usr){
							$b_couleur = ":#$m_couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
						}
						
				 if(is_numeric($m_client) && mysql_query("SHOW COLUMNS FROM `clients`") ){
				 	$ris = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$m_client'");
					if($ris && mysql_num_rows($ris)==1){
						$ri = mysql_fetch_object($ris);
						$printki=$ri->nom;
					}
				 }
				if($m_priority==0) $m_priority=1;
				
				
				$agebody='';
				$bodi=split("\n",strip_tags(trim($m_note)));
				for($e=0 ; $e<sizeof($bodi) ; $e++){
					$agebody.=trim(trim($bodi[$e]))." ";
				}
				$m_note = urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode(str_replace('"',"`",$agebody)))));
				$mt = strtotime("-1hour", strtotime("$m_date $m_heure"));
				$me = strtotime("+1hour",$mt);
				$vdat = date("Ymd",$mt).'T'.date("His",$mt).'Z';
				$vend = date("Ymd",$me).'T'.date("His",$me).'Z';
				$m_priority --;
				echo"BEGIN:VEVENT\r\nSUMMARY:$m_client\r\nDTSTART:$vdat\r\nDTEND:$vend\r\nDESCRIPTION:".ereg_replace("[[:punct:]]",' ',urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode($m_note)))))."\r\nSTATUS:$statous[$m_etat]\r\nUID:$m_id@$prov\r\nPRIORITY:$m_priority\r\nLOCATION:$m_qui\r\nCATEGORIES:$m_type\r\nEND:VEVENT\r\n\r\n";
			}
		}
	
	echo"END:VCALENDAR\r\n";

mysql_close($conn);
exit();
}
else{
	echo ("CONNECTION ERROR $base@$host");
}

?>