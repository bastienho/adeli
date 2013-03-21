<?php // 1448 > Gestion de comptes clients ;

insert('_transfert');
insert('_site');
	
$r_alias["adeli_messages"]["dest"]="clients_id_nom";
$r_alias["adeli_messages"]["prov"]="clients_id_nom";
$r_alias["adeli_messages"]["etat"]="bool";



$alias["adeli_messages"]["dest"]="destinataire";
$alias["adeli_messages"]["prov"]="expéditeur";
$alias["adeli_messages"]["message_dest_email"]="Email";
$alias["adeli_messages"]["etat"]="Lu";
$alias["adeli_messages"]["text"]="Message";
$alias["clients"]["adres"]="adresse";
$alias["clients"]["last"]="dernière connexion";
$alias["clients"]["text"]="message";

$types["adeli_messages"]="txt";

$stat_message = array("non lu","lu","répondu");
if($part=="" && isset($_GET["part"]) && $_GET["part"]!=""){
	$part = $_GET["part"];
}

$tabledb = $part;	
$comportement = split(",",$types[$part]);

if(in_array("compta",$opt) && isset($compta_base)){
	$_SESSION['pdf_base']=$base;
	$_SESSION['pdf_$host']=$host;
	$_SESSION['pdf_$login']=$login;
	$_SESSION['pdf_$passe']=$passe;
	$_SESSION['compta_base']=$compta_base;
	
	echo"<div id='compta_valid_form' style='width:300px;height:200px;visibility:hidden;position:absolute;z-index:150'>
		<form action='./' method='get' name='validexp'>
		<input type='hidden' name='option' value='site'>
		<input type='hidden' name='adeli_messages' value='1'>
		<input type='hidden' name='dest' value='$edit'>
		<input type='hidden' name='sujet' value='Expédition de votre commande'>
		<input type='hidden' name='message' value=''>
		<input type='submit' value='enregistrer'>
		</form>			  
	</div>";
	insert('_compta_pdf');
	if(is_file('bin/_compta_pdf.php')){
		$openpdf='./?incpath=bin/_compta_pdf.php&1';
	}
	else{
		$openpdf='$style_url/update.php?file=_compta_pdf.php?1';
		//include("$style_url/update.php?file=$incfich.php");
	}
}


if($part=='adeli_messages' && $edit==''){
	
	
	$verifupdt = mysql_query("DESC `adeli_messages`");
	$allchamps = array();
	while($ro = mysql_fetch_object($verifupdt)){
		array_push($allchamps,$ro->Field);
	}
	if(!in_array("message_dest_email",$allchamps)){
		mysql_query("ALTER TABLE `adeli_messages` ADD `message_dest_email` VARCHAR( 255 ) NOT NULL AFTER `dest`");
	}
	
	
	
	$signa=get_pref('mails.signture');
	
	if(isset($_GET["message"])){
		$fields_values['text'] = stripslashes($_GET["message"]).$signa;
	} 
	elseif(isset($_GET['rec'])){
		$rec = $_GET['rec'];
		$rem = mysql_query("SELECT `texte` FROM `adeli_message_template` WHERE `id`='$rec'");
		if($rem && mysql_num_rows($rem)>0){
			$rom = mysql_fetch_array($rem);
			$fields_values['text'] = str_replace('$variable','$variable'.stripslashes($_POST["adeli_message_var"]),$rom[0]);
			if(mysql_query("SHOW COLUMNS FROM `clients`")){
				if(isset($_GET['dest'])){
					$dest = $_GET['dest'];
				}
				elseif(isset($_POST['dest'])){
					$dest = $_POST['dest'];
				}
				$res_cl = mysql_list_fields($dbase,'clients');
				$colcli = mysql_num_fields($res_cl);
				$rem = mysql_query("SELECT * FROM `clients` WHERE `id`='$dest'");
				$rom = mysql_fetch_object($rem);		 	 			 		   
				for ($c = 0; $c < $colcli; $c++) {
					$fdn = mysql_field_name($res_cl, $c);
					$field_var = $rom->$fdn;
					$fields_values['text'] = str_replace('$'.$fdn,$field_var,$fields_values['text']);
				}
			 }
		}
	}
	else{
		$fields_values['text'] = $signa;
	}
	if( is_file('bin/_transfert.php') && in_array("adeli_messages",$menu["worknet"]) && isset($_POST["adeli_message_var"]) && (isset($_POST['dest']) || isset($_GET['dest']))){
		if(isset($_GET['dest'])){
			$dest = $_GET['dest'];
		}
		elseif(isset($_POST['dest'])){
			$dest = $_POST['dest'];
		}
		
		$linkfich=$serv;
		if(isset($link_domain) && !empty($link_domain)) $linkfich=$link_domain;
		
		$plval='';
		$scna = str_replace('index.php','',getenv("SCRIPT_NAME"));
		$serv = getenv("SERVER_NAME");
		foreach($_POST as $k=>$v){
			if(substr($k,0,5)=='join_'){
				$k = substr($k,5,strlen($k));
				if(substr($k,0,7)=='compta_'){
					$mid = substr($k,7,strlen($k));
					$plval .= "<li><a href='http://$linkfich$scna"."bin/_transfert.php?i=$dest&c=$mid'><b>$v</b></a></li>";	
				}
				if(substr($k,0,5)=='file_'){
					$nam = basename($v);
					$plval .= "<li><a href='http://$linkfich$scna"."bin/_transfert.php?i=$dest&f=$v'><b>$nam</b></a></li>";	
				}										
			}
		}
		if($plval!=''){
			$plval="<div style='border:#CCC 1px solid; padding:10px'><b>Fichiers joints : </b><ul>$plval</ul></div>";	
		}
		if(strpos($fields_values['text'],'$variable')>-1){
			$fields_values['text']=str_replace('$variable',$plval,$fields_values['text']);	
		}
		elseif($plval!=''){
			$fields_values['text'] = "<font face='arial'>".$plval."<br><br></font>".html_my_text($fields_values['text']);
		}
	}
	else{
		$fields_values['text'] = "<font face='arial'>".html_my_text($fields_values['text'])."</font>";
	}	


	//message_dest_email
	if(isset($_GET["dest"])){
		$fields_values['message_dest_email']=get('clients','email',$_GET['dest']);
	}
}

if($part=="adeli_messages" && isset($_GET["add"])){
	$clid = $_POST["dest"];
	$sujet = stripslashes($_POST["sujet"]);
	$texte = stripslashes($_POST["text"]);
	
	$ros = mysql_query("SELECT `nom`,`email` FROM `clients` WHERE `id`='$clid'");
	$rows = mysql_fetch_object($ros);
	$nom = $rows->nom;
	if($rows->email!=""){
		$easysend=false;
		$res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') AND `adresse`='$u_email'");
		if($res && mysql_num_rows($res)>0){
			$ro = mysql_fetch_object($res);
			$b_serveur = $ro->serveur;
			$b_port = $ro->port;
			$b_dossier = $ro->dossier;
			$b_login = $ro->login;
			$b_pass = $ro->pass;
			$mbox = imap_open("\{$b_serveur:143/imap/notls}INBOX.sent-mail","$b_login","$b_pass");						
			if($mbox==NULL){
				$easysend=true;
			}
			else{
				$oEmail = new SimpleMail ();
				$oEmail->From = "$u_nom<$u_email>";
				$oEmail->To = split("[,;]",stripslashes($_POST['message_dest_email']));
				$oEmail->Bcc = array();
				
				$oEmail->Subject = $sujet;
				
				$oEmail->addBody ("<html><head></head><body>".$texte."</body></html>",'text/html','ISO-8859-1');
				
				$oEmail->send ();
			
				$return.=returnn("message envoyé à $nom ".$rows->email,"009900");

				$mail = "Subject:$sujet\n".$oEmail->getMessage ();
				
				if(imap_append($mbox,"\{$b_serveur:143/imap/notls}INBOX.sent-mail",$mail,"\\Seen")){
					$return.=returnn("Le message a été enregistré dans les éléments envoyés !","009900");
				}
				else{
					$return.=returnn("Le message n'a pas pu être sauvegardé !","009900");
				}
			}			
		}
		else{
			$easysend=true;
		}
		if($easysend==true){
			if(ereg("</",$texte)){
				$head=  "Content-Type: text/html;charset=ISO-8859-1\nFrom: $u_nom<$u_email>";
			}
			else{
				$head = "from: $u_nom<$u_email>";
			}
			if(mail(stripslashes($_POST['message_dest_email']),$sujet,$texte,$head)){
				$return.=returnn("message envoyé à $nom ".$rows->email,"009900");
			}
		}
		
	}
	
}
if($part=='adeli_messages' && $edit!='' && !isset($_GET['modif'])){
	$editmode_forced=1;
}

insert('_site');
include('bin/_site.php');
?>
