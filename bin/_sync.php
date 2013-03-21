<?php // 1 > Synchronisation ;

if(is_file("../mconfig/adeli.php") && isset($_GET['base']) && isset($_GET['login']) && isset($_GET['pass'])){

	include("../mconfig/adeli.php");
	function connecte($base, $host, $login, $passe) {
		$conn = @mysql_connect($host, $login, $passe);
		@mysql_select_db($base);
		return $conn;
	}
	function deconnecte($conn) {
		@mysql_close($conn);
	}
	$conn = connecte($base, $host, $login, $passe);

$u_base = $_GET['base'];
$u_login = $_GET['login'];
$u_pass = $_GET['pass'];

$res = mysql_query("SELECT * FROM `adeli_users` WHERE `login`='$u_login' AND `pass`='$u_pass'");
if($res && mysql_num_rows($res) == 1){
	$ro = mysql_fetch_object($res);
			
	$u_id = $ro->id;
		
	if($u_base == 'agenda'){
		$agenda_base="adeli_agenda";
		$output_export="BEGIN:VCALENDAR\r\nVERSION:1.0\r\n\r\n";
		if($sqldate=="now"){
			$sqldate = date("Y-m-d");
		}
		$res = mysql_query("SELECT * FROM `$agenda_base` WHERE `etat`='0' AND (`only`='0' OR `only`='$u_id') ORDER BY `date`,`heure`");
		while($ro = mysql_fetch_object($res)){
			 $m_id = $ro->id;
			 $m_heure = $ro->heure;
			 $m_date = $ro->date;
			 $m_qui = addslashes($ro->qui);
			 $m_type = addslashes($ro->type);
			 $m_client = addslashes($ro->client);
			 $m_note = addslashes($ro->note);
			 $m_etat = $ro->etat;
			 $m_priority = $ro->priority;
			 $m_only = $ro->only;
			 
			 $printki=$m_client;
			 $nots=substr($m_note,0,20);
			 $m_couleur = $ro->couleur;
		
					
			 if(is_numeric($m_client) && mysql_query("SHOW COLUMNS FROM `clients`") ){
				$ris = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$m_client'");
				if($ris && mysql_num_rows($ris)==1){
					$ri = mysql_fetch_object($ris);
					$m_client=$ri->nom;
				}
			 }
			if($m_priority==0) $m_priority=1;
			
			
			$agebody='';
			$bodi=split("\n",strip_tags(trim($m_note)));
			for($e=0 ; $e<sizeof($bodi) ; $e++){
				$agebody.=trim(trim($bodi[$e]))." ";
			}
			$m_note = ereg_replace("[[:punct:]]",' ',urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode(str_replace('"',"`",$agebody))))));
			
		
				
			$sta = array('ACCEPTED','COMPLETED');
			$mt = strtotime("-1 Hours",strtotime("$m_date $m_heure"));
			$vdat = date("Ymd",$mt).'T'.date("His",$mt).'Z';
			//str_replace('-','',$m_date).'T'.str_replace(':','',$m_heure).'Z';
			$output_export.="BEGIN:VEVENT\r\nSUMMARY:$m_client\r\nDTEND:$vdat\r\nDTSTART:$vdat\r\nDESCRIPTION:$m_note\r\nDALARM:$vdat;PT1M;1;$m_client\r\nAALARM;URL:$vdat;PT1M;1;\r\nEND:VEVENT\r\n\r\n";
		
		}
		$output_export.="END:VCALENDAR\r\n";
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Type: application/force-download");
		header("Content-Disposition: inline; filename=agenda.vcs;" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".strlen($output_export));
		echo $output_export; 
		exit();
	}
}
else{
	echo"WRONG USER";
}
}
?>
