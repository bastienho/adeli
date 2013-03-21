<?php // 689 > Lecteur de courriels ;
session_name("adeli");
session_start();




if($_SESSION['u_id']!=0){
	$u_id = $_SESSION['u_id'];
	$mail_base =  $_SESSION["mail_base"];
	if(isset($_GET['b'])){
		$bid = $_GET['b'];	
		
		$conn = mysql_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_pass"]);
		mysql_select_db($_SESSION["db_base"]);
		echo"";
		$res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') AND `id`='$bid'");
		$ro = mysql_fetch_object($res);
		$b_serveur = $ro->serveur;
		$b_port = $ro->port;
		$b_dossier = $ro->dossier;
		$b_login = $ro->login;
		$b_pass = $ro->pass;
		mysql_close($conn);
		
		$_SESSION["ma_nom"]=$b_nom;
		$_SESSION["ma_id"]=$bid;
		$_SESSION["ma_serveur"]=$b_serveur;
		$_SESSION["ma_port"]=$b_port;
		$_SESSION["ma_dossier"]=$b_dossier;
		$_SESSION["ma_login"]=$b_login;
		$_SESSION["ma_pass"]=$b_pass;
	}
					
	$extsis = array("PJPEG"=>"jpg","X-ZIP-COMPRESSED"=>"zip","MSWORD"=>"doc","MSEXCELL"=>"xls");
	$b_nom = $_SESSION["ma_nom"];
	$bid = $b_id = $_SESSION["ma_id"];
	$b_serveur = $_SESSION["ma_serveur"];
	$b_port = $_SESSION["ma_port"];
	$b_dossier = $_SESSION["ma_dossier"];
	$b_login = $_SESSION["ma_login"];
	$b_pass = $_SESSION["ma_pass"];
	$vers = $_SESSION["vers"];
	$theme = $_SESSION["theme"];
	$mail = $_GET["mail"];
	$x_id = $_SESSION['x_id'];
	$u_nom = $_SESSION['u_nom'];
	$signa = $_SESSION['signature'];
	$debit = $_SESSION['u_debit'];
	$menu_site = $_SESSION['menu_site'];
	include("../mconfig/adeli.php");
	if(!isset($style_url)) $style_url="http://www.adeli.wac.fr/vers/$vers";
	
	if(is_file('inc_func.php')){
		include('inc_func.php');
	}
	else{
		include("$style_url/update.php?file=inc_func.php");
	}
	
	if(is_file('_mail_lecteur.php')){
		$openmel='_mail_lecteur.php?1';
	}
	else{
		$openmel="$style_url/update.php?file=_mail_lecteur.php";
	}
	$NomDuMois=array("err","Jan","Fév","Mar","Avr","Mai","Juin","Juil","Aoû","Sept","Oct","Nov","Déc");
	$NomDuJour=array("dim","lun","mar","mer","jeu","ven","sam","err");
	$sear="1";
///////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if(isset($_GET['piece'])){
		$piece = $_GET['piece'];
		$mbox = imap_open("\{$b_serveur:143/imap/notls}$b_dossier","$b_login","$b_pass");
						
		if($mbox==NULL){
			echo"connexion impossible  au serveur (".$_SESSION['s_id'].") <b>$b_serveur</b> 
			avec le compte <b>$b_login</b><br>" . imap_last_error();
		}
		else{
		  
		  $coding = array("7BIT","8BIT","BINARY","BASE64","QUOTED-PRINTABLE","OTHER"); 
		  
		  if(isset($_GET['nom']) && $_GET['nom']!='' && isset($_GET['encoding']) && $_GET['encoding']!='' ){
		  	$value=$_GET['nom'];
		  	$encoding=$_GET['encoding'];
			$contenu = imap_fetchbody($mbox, $mail, $piece);

			if($encoding == "0"){
			   $contenu= quoted_printable_decode($contenu);
			}	
			elseif($encoding == "1"){
				$contenu= imap_8bit($contenu);
			}	
			elseif($encoding == "2"){
				$contenu= imap_base64 (imap_binary ($contenu));
			}					   
			elseif($encoding == "3"){
				$contenu= imap_base64 ($contenu);
			}	
			elseif($encoding == "4"){
			  $contenu= quoted_printable_decode($contenu);
			}
			else{
			  $contenu= imap_8bit(quoted_printable_decode(imap_base64 (imap_binary ($contenu))));
			}
		  }
		  else{
			  $struct = imap_fetchstructure($mbox, $mail);
			  $structpart = $struct->parts;
			  $numpart=0;
			  //$yopiyop = $structpart[$numpart];
			  foreach($structpart as $yopiyop) {
				  $numpart++;
				  $id = $yopiyop->id;
				  if($numpart == $piece || $piece == $id || $id == "<$piece>" || ereg('\.',$piece)){
				  $disposition = $yopiyop->disposition;
				  $subtype = $yopiyop->subtype;
				  $bytes = ($yopiyop->bytes)/1000;
				  $subtype = $yopiyop->subtype;
				  $parameters = $yopiyop->parameters;
				  $encoding = $yopiyop->encoding;
				  if(isset($yopiyop->description)){
					$value=$yopiyop->description;
				  }
				  elseif(is_array($parameters)){
					$value = $parameters[0]->value;
				  }
				  elseif(isset($yopiyop->dparameters)){
					  $dparameters=$yopiyop->dparameters;
					  $value = $dparameters[0]->value;
				  }
				  
				  else{
					$value = 'inconnue';
				  }
				  
				  
				   $value = trim(stripslashes((quoted_printable_decode ($value))));
					$value=ereg_replace("\?","",$value) ;
					$value=ereg_replace("=","",$value) ;
					$value=ereg_replace("ISO-8859-1Q","",$value);
					$value=ereg_replace("iso-8859-1Q","",$value);
			
			
			
					  if($encoding == "3"){
						  $contenu = imap_base64(imap_fetchbody($mbox, $mail, $numpart));
						  break;
					  }	
					  elseif($encoding == "4"){
							$contenu = quoted_printable_decode(imap_fetchbody($mbox, $mail, $numpart));
							break;
					  }
					  else{
							$contenu = imap_fetchbody($mbox, $mail, $numpart);
							break;
					  } 
				 }
			   }
		   
					if($subtype == "APPLEDOUBLE"){
						$value = $yopiyop->parts[0]->dparameters[0]->value;
					}
					if($value==""){
						$value = $yopiyop->dparameters[0]->value;
					}
		   
		  }
			$file_extension = strtolower(substr(strrchr($value,"."),1));
			if($file_extension == NULL){
				if(isset($extsis[$subtype])){
					$file_extension = $extsis[$subtype];
				}
				else{
					$file_extension = strtolower($subtype);
				}
				$value = "$value.$file_extension";
			}
			switch( $file_extension ){
			  case "pdf": $ctype="application/pdf"; break;
			  case "exe": $ctype="application/octet-stream"; break;
			  case "zip": $ctype="application/zip"; break;
			  case "rar": $ctype="application/zip"; break;
			  case "doc": $ctype="application/msword"; break;
			  case "xls": $ctype="application/vnd.ms-excel"; break;
			  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			  case "gif": $ctype="image/gif"; break;
			  case "png": $ctype="image/png"; break;
			  case "jpeg":$ctype="image/jpg"; break;
			  case "jpg": $ctype="image/jpg"; break;
			  default: $ctype="application/force-download";
			}
			
			$fname=trim($value);
			$fname = ereg_replace(" ","_",$fname);
			$fname = ereg_replace("-","_",$fname);
			
			
			$size = strlen($contenu);
			if(!isset($_GET['print'])){
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: public");
				header("Content-Disposition: attachment; filename=$fname;" );
				header("Content-Transfer-Encoding: binary");
			}
			header("Content-Type: $ctype");
			header("Content-Length: $size");
			echo $contenu;
			exit();	
			}
	}
/******************************************************************************************************************************/
/******************************************************************************************************************************/
/******************************************************************************************************************************/
/******************************************************************************************************************************/
/******************************************************************************************************************************/
/******************************************************************************************************************************/
/******************************************************************************************************************************/
	else{
		echo"<html>
		<head>
		<title>Mail Adeli $vers</title>
				<link rel='stylesheet' href='$style_url/$theme/style.css' type='text/css'>
				<link rel='icon' href='$style_url/favicon.png' type='image/x-icon'>
				<link rel='shortcut icon' href='$style_url/favicon.png' type='image/x-icon'>
				<script language=\"JavaScript\" src=\"$style_url/func.js\"></script>
				<style>
				.ltd{
					padding:1px;
					font-size:10px;
				}
				.ltd span{
					display:block;
					padding:0px;
					overflow:hidden
				}
				tr.listhree td,tr.listhree td,tr.listhree td{
					overflow:hidden;
					height:12px;
					white-space:nowrap;
				}
				tr.listhree{
					background-color:#FFFFFF;
					cursor:default;
					color:#000000;
				}
				</style>
				<script language='javascript'>
		 
		  
		  function colortab(id,is,classe){
			if(is == true){
				document.getElementById(id).className='listhree';
			}
			if(is == false){
				document.getElementById(id).className=classe;
			}
			ulogin = '$u_nom';
		  }
		parent.affichload();
		</script>
		<script language='javascript'>
				function resizz(){				
					var Hu;
					Hu=document.body.scrollHeight;
					
					Hu+=20;
					if(isNaN(Hu) || Hu<50) Hu=50;
					nam = this.name;
					parent.document.getElementById(nam).style.height=Hu+'px';
				}
				var nbf=0;
				function addfile(){
					con = document.getElementById('fils');
					var oDiv=document.createElement('DIV');
					con.appendChild(oDiv);
					oDiv.id='fil'+nbf;
					oDiv.innerHTML=\"<input type='file' name='file[]'><a style='cursor:pointer' onclick='delfil(\"+nbf+\")'>Supprimer</a>\";
					nbf++;
					document.ecrou.nbf.value=nbf;					
				}
				function delfil(n){
					document.getElementById('fil'+n).innerHTML='';					
				}
				
			</script>
		</head>
		<body style='padding:0px;margin:0px'>
		
		";
					if($debit==0){
						echo"<div id='corps'>";
					}
					else{
						echo"
						<select onchange=document.location=this.value><option value='../?option=mail'></option>";
						$conn = mysql_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_pass"]);
						mysql_select_db($_SESSION["db_base"]);
						$res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') ORDER BY `nom`");
						while($ro = mysql_fetch_object($res)){
							$b_nom = $ro->nom;
							$b_id = $ro->id;
							if($b_id == $bid){
								echo"<option value='_mail_lecteur.php?mail=INBOX&part=lecture&b=$b_id' selected>$b_nom</option>";
							}
							else{
								echo"<option value='_mail_lecteur.php?mail=INBOX&part=lecture&b=$b_id'>$b_nom</option>";
							}
						}
						mysql_close($conn);
						echo"</select> <a href='../?option' target='_parent'>Retour à l'accueil</a>";
					}
					
/*************************************************************************************************************************
		
							 ECRITURE MESSAGE
		
		*************************************************************************************************************************/
		if(isset($_GET['ecrire'])){
		
		
				$mbox = imap_open("\{$b_serveur:143/imap/notls}INBOX.sent-mail","$b_login","$b_pass");
						
			if($mbox==NULL){
				echo"connexion impossible  au serveur (".$_SESSION['s_id'].") <b>$b_serveur</b> 
				avec le compte <b>$b_login</b><br>" . imap_last_error();
			}
			else{
			
			$to='';
			$sujet='';
			$message='';
			$nouv=true;
			if(isset($_GET['to'])) $to = $_GET['to'];
			if(isset($_GET['sujet'])) $sujet = $_GET['sujet'];
			if(isset($_GET['message'])) $message = $_GET['message'];
			
			if(isset($_POST['to'])){ 
				$de = stripslashes($_POST['de']);
				$to = stripslashes($_POST['to']);
				$cc = stripslashes($_POST['cc']);
				$sujet = stripslashes($_POST['sujet']);
				$message = stripslashes($_POST['message']);
				if($debit!=0){
					$message.="\n\n$signa";
				}
				
				
				
				try {
					$oEmail = new SimpleMail ();
					$oEmail->From = $de;
					$oEmail->To = split("[,;]",$to);
					$oEmail->Bcc = split("[,;]",$cc);
					
					$oEmail->Subject = $sujet;
					
					//$oEmail->addBody (strip_tags($message));
					$oEmail->addBody ($message,'text/plain','ISO-8859-1');
				$rec='';
					//$oEmail->addAttachment ('/var/www/myDoc.pdf', MimeType::get ('pdf')); // Basic file !
					if($_POST['nbf']>0){
						//$part0["type"] = TYPEMULTIPART;
						//$part0["subtype"] = "mixed";
						//$body[1] = $part0;
						//$body[2] = $part1;
						//$p=3;
						for($i=0 ; $i<$_POST['nbf'] ; $i++){
							if($_FILES['file']['name'][$i]!=NULL){
								$nom = $_FILES['file']['name'][$i];
								$rec.="- ".($i+1)."  $nom ";
								$id = "../tmp/mail_$u_id"."_$i";
								if(copy($_FILES['file']['tmp_name'][$i],$id)){
									$file_extension = strtolower(substr(strrchr($nom,"."),1));
									$oEmail->addAttachment ($id, geti ($file_extension),$nom);	
									$rec.=" ajouté !";
									unlink($id);				
								}
								$rec.="<br>";
							}
						}
					}
				
					$oEmail->send ();
				
					echo"<br>
					<div class='cadre'>
					Votre message a été envoyé avec succès !
					<br><br>
					<a href='$openmel&mail=INBOX&c=$now' target='lister' class='buttontd'>Retour à la boite de réception</a>
					&nbsp;
					<a href='$openmel&ecrire&part=nouveau' class='buttontd'>&Eacute;crire un nouveau message</a>
					<br>$rec
					<br><br>
					";

					$mail = "Subject:$sujet\n".$oEmail->getMessage ();
					$nouv=false;
					$to='';
					$cc='';
					$sujet='';
					$message='';
					//var_dump ($oEmail);
					
					//$mail = str_replace("\r","",$oEmail->getMessage ());
					if(imap_append($mbox,"\{$b_serveur:143/imap/notls}INBOX.sent-mail",$mail,"\\Seen")){
						echo"Le message a été enregistré dans les éléments envoyés !";
					}
					else{
						echo"Le message n'a pas pu être sauvegardé !";
					}
					echo "<!-- $mail --><br><br><br></div>";
				}
				catch (Exception $oE) {
					var_dump ($oE);
					echo "une erreur s'est produite !<br>>".$oE->getMessage ();
				}
				/*
				$eol="\n";
				
				
$now = time();
$envelope["from"]= $de;
$envelope["to"]  = $to;
$envelope["cc"]  = $cc;
$envelope["subject"]  = $sujet;
$envelope["date"]  = date('r');
$envelope["Message-ID"]  = "<Adeli$b_login".$now."@".$_SERVER['SERVER_NAME'].">";
$envelope["X-Mailer"]  = "Adeli v1.2 - PHP v".phpversion();

$part1["type"] = TYPETEXT;
$part1["subtype"] = "plain";
$part1["contents.data"] = "$message\n\n\n\t";



if($_POST['nbf']>0){
	$part0["type"] = TYPEMULTIPART;
	$part0["subtype"] = "mixed";
	$body[1] = $part0;
	$body[2] = $part1;
	$p=3;
	for($i=0 ; $i<$_POST['nbf'] ; $i++){
		if($_FILES['file']['name'][$i]!=NULL){
			$id = "tmp/mail_$u_id"."_$i";
			$nom = $_FILES['file']['name'][$i];
			if(copy($_FILES['file']['tmp_name'][$i],$id)){
				$file_extension = strtolower(substr(strrchr($nom,"."),1));
				switch( $file_extension ){
				  case "pdf": $part["type"] = TYPEAPPLICATION; $ctype="pdf"; break;
				  case "zip": $part["type"] = TYPEAPPLICATION; $ctype="zip"; break;
				  case "doc": $part["type"] = TYPEAPPLICATION; $ctype="msword"; break;
				  case "xls": $part["type"] = TYPEAPPLICATION; $ctype="vnd.ms-excel"; break;
				  case "ppt": $part["type"] = TYPEAPPLICATION; $ctype="vnd.ms-powerpoint"; break;
				  case "gif": $part["type"] = TYPEIMAGE; $ctype="gif"; break;
				  case "png": $part["type"] = TYPEIMAGE; $ctype="png"; break;
				  case "jpeg":$part["type"] = TYPEIMAGE; $ctype="jpeg"; break;
				  case "jpg": $part["type"] = TYPEIMAGE; $ctype="jpeg"; break;
				  default: $part["type"] = TYPEAPPLICATION; $ctype="octet-stream";
				}
				
				$part["encoding"] = ENCBINARY;
				$part["subtype"] = $ctype;
				$part["disposition"] = 'attachement';
				$part["description"] = basename($nom);
				$part["contents.data"] = file_get_contents($id);
				$body[$p] = $part;
				$p++;
				unlink($id);				
			}
		}
	}
}
else{
	$body[1] = $part1;
}

				$mail = str_replace("\r","",imap_mail_compose($envelope, $body));
				if(imap_mail($to,$sujet,'',$mail)){
				//mail("$to,$b_login",$sujet,$message,$headers)
					echo"<br>
					<div class='cadre'>
					Votre message a été envoyé avec succès !
					<br><br>
					<a href='$openmel&mail=INBOX&c=$now' target='lister' class='buttontd'>Retour à la boite de réception</a>
					&nbsp;
					<a href='$openmel&ecrire&part=nouveau' class='buttontd'>&Eacute;crire un nouveau message</a>
					</div>
					
					";
					$nouv=false;
					$to='';
					$cc='';
					$sujet='';
					$message='';
					if(imap_append($mbox,"\{$b_serveur:143/imap/notls}INBOX.sent-mail",$mail,"\\Seen")){
						echo"Le message a été enregistré dans les éléments envoyés !";
					}
					else{
						echo"Le message n'a pas pu être sauvegardé !";
					}
				}
				else{
					echo"une erreur s'est produite !<br>";
				}	*/

			}
			if($nouv){
			echo"
			
			<form action='$openmel&ecrire' method='post' name='ecrou' enctype='multipart/form-data'>
			<table style='width:100%;' cellspacing='0' cellpadding='3'>
			<tr><td colspan='2' class='buttontd' style='text-align:left'><input type='submit' class='buttontd' value='Envoyer'></td></tr>
			<tr><td width='50'><b>De :</b></td><td><input type='text' name='de' value=\"$u_nom<$b_login>\" style='width:80%;'></td></tr>
			<tr><td width='50'><b>&Agrave; :</b></td><td><input type='text' name='to' value=\"$to\" style='width:80%;'></td></tr>
			<tr><td width='50'><b>Cc :</b></td><td><input type='text' name='cc' value=\"$cc\" style='width:80%;'></td></tr>
			<tr><td width='50' valign='top'><b>Sujet :</b></td><td><input type='text' name='sujet' value=\"$sujet\" style='width:80%;'><div id='fils'></div>
			<b>+ <a style='cursor:pointer' onclick='addfile()'>Attacher un fichier</a></b>
			</td></tr>
			<tr><td colspan='2'>
			<input type='hidden' name='nbf' value='0'>
				<textarea id='messa' name='message' style='width:90%;height:200px' onkeyup=\"this.style.height=this.scrollHeight+5;resizz();\" onfocus=\"this.style.height=this.scrollHeight+5;resizz();this.scrolling='false';\">";
			if($debit==0){
echo"
$signa

----------
$message";
	}

		echo"</textarea></td></tr>
			<tr><td colspan='2' class='buttontd' style='text-align:left'><input type='submit' class='buttontd' value='Envoyer'></td></tr>			
			</table>			
			</form>
			<script language='javascript'>
				function Cursor_SetPos( where_, pos_){
					var Obj = document.getElementById( where_);
					if( Obj){
						Obj.focus();
						if( typeof Obj.selectionStart != \"undefined\"){
							Obj.setSelectionRange( pos_, pos_);
						}
						else{ // IE and consort
							var Chaine = Obj.createTextRange();
							Chaine.moveStart('character', pos_);
							//-- Deplace le curseur
							Chaine.collapse();
							Chaine.select();
						}
					}
				} ";
				if($message!=''){
				echo"Cursor_SetPos( 'messa', 0);";
				}
				echo"
			</script>
			";
			}
		}
	}	/*************************************************************************************************************************
		
							 LISTE MESSAGE
		
		*************************************************************************************************************************/
		elseif(isset($_GET["mail"]) && !is_numeric($mail)){
			$_SESSION["ma_dossier"] = $mail;
			$mbox = imap_open("\{$b_serveur:143/imap/notls}$mail","$b_login","$b_pass");
					
					
						
			if($mbox==NULL){
				echo"connexion impossible  au serveur (".$_SESSION['s_id'].") <b>$b_serveur</b> 
				avec le compte <b>$b_login</b><br>" . imap_last_error();
			}
			else{
		
		
		
					
					
				if(isset($_POST["effect"])){
					$liste_mail="";
					$nbsup=0;
					foreach($_POST as $v){
						if(is_numeric($v)){
							if($liste_mail!=""){
								$liste_mail.=",";
							}
							$liste_mail.="$v";
							$nbsup++;
						}		
					}
					if( isset($_GET["deplacedans"]) && $_GET["deplacedans"]!=""){
						imap_mail_move ( $mbox, "$liste_mail", $_GET["deplacedans"], CP_UID );
									
					}
					if( isset($_GET["marqueas"]) && $_GET["marqueas"]!=""){
						if($_GET["marqueas"]=='SEEN'){
							imap_setflag_full($mbox,"$liste_mail",'\\SEEN',ST_UID);
						}
						if($_GET["marqueas"]=='UNSEEN'){
							imap_clearflag_full($mbox,"$liste_mail",'\\SEEN',ST_UID);
						}
						if($_GET["marqueas"]=='FLAGGED'){
							imap_setflag_full($mbox,"$liste_mail",'\\FLAGGED',ST_UID);
						}
						if($_GET["marqueas"]=='UNFLAGGED'){
							imap_clearflag_full($mbox,"$liste_mail",'\\FLAGGED',ST_UID);
						}
									
					}
					if( isset($_GET["del"])){
						imap_mail_move ( $mbox, "$liste_mail", 'INBOX.trash', CP_UID );
						//imap_setflag_full($mbox,"$liste_mail",'\\DELETED',ST_UID);
								
					}
					echo"
					$nbsup messages ont été affectés
					<script language='javascript'>
						document.location=document.location+'&dat=$now';
					</script>";
				}
				if(isset($_GET["effect"])){
					if( isset($_GET["del"])){
						imap_mail_move ( $mbox, $_GET["del"], 'INBOX.trash', CP_UID );
									
					}
					if( isset($_GET["empty"]) && $mail=='INBOX.trash'){
						$nbm = imap_num_msg ($mbox);
						$liste_mail='';
						for($i=1 ; $i<=$nbm ; $i++){
							$liste_mail.="$i";
							if($i<$nbm) $liste_mail.=",";
						}
						imap_setflag_full($mbox,"$liste_mail",'\\DELETED');
						
					}
				}
				imap_expunge($mbox);
					$status = imap_status($mbox, "{".$b_serveur.":$b_port$b_dossier}".$val, SA_ALL);
					$nbmes = $status->messages;
					$nbnl = $status->unseen;
		
					//////////////////////////// classement
				 if(isset($_GET['q'])){
					$q = $_GET['q'];
					$sear = "q=$q";
					$mbrange = imap_search ($mbox, "TEXT \"$q\"");
					$num_msg = sizeof($mbrange);
				  }
				  elseif(isset($_GET['from'])){
					$from = $_GET['from'];
					$mbrange = imap_search ($mbox, "FROM \"$from\"");
					$sear = "from=$from";
					$num_msg = sizeof($mbrange);  
				  }	
				  else{
					$mbrange = imap_sort ( $mbox, SORTARRIVAL , 0, SE_NOPREFETCH);
					$sear='2';
					$num_msg = imap_num_msg ($mbox);	
					if($mail=='INBOX'){						
						set_pref("mails.$b_id.mail",$num_msg);
						//$sear='1';
					}
				  }	  
				  $l_fin=$num_msg;
				  
				if(ereg("\.",$mail)){
					$dossier = strtolower(substr(strrchr(trim($mail),"."),1));
				}
				else{
					$dossier = strtolower($mail);
				}
					
				echo"
				<script language='javascript'>
						lo = document.location;
						function read(ki){
							/*parent.reader.location='$openmel&part=lecture&mail='+ki+'&tot=$num_msg';
							parent.document.getElementById('lister').style.height='140px';							
							var lis = document.getElementById('tab'+ki).getElementsByTagName('div');
							for(var i=0 ; i<lis.length ; i++){
								lis[i].style.fontWeight='normal';
							}
							document.location=lo+'#p'+ki;*/
							document.location='$openmel&part=lecture&mail='+ki+'&tot=$num_msg&dossier=$mail';
						}
					</script>
					";
					if($debit==0){
						echo"<form name='farmermail' action='$openmel&mail=$mail' method='post'>
					<input type='hidden' name='effect'>";
					}
					else{
						echo"<form name='farmermail' action='_mail_lecteur.php?mail=INBOX&part=lecture&mail=$mail&del' method='post'>
					<input type='hidden' name='effect' value='effect'><input type='submit' value='Supprimer'>";
					}
					
					
					echo"
					<style>
					.islu, .isnotlu{
						height:26px;
						overflow:hidden;
						font-style:inherit;
						white-space:nowrap;
					}
					.isnotlu{
						font-weight:bold;
					}
					</style>
					<table cellpadding='3'";
					if($debit == 0) echo"width='100%'";
					echo"><tr><td>$num_msg message(s) dans <b>$dossier</b>";
					if($nbnl>0) echo" dont $nbnl non lus";
					
					if($mail=='INBOX.trash') echo" <a href='$openmel&mail=$mail&effect&empty' class='buttontd'>Vider la corbeille</a>";
					echo"</td><td align='right'>";
					
					/*
					$l_deb = $num_msg;
					$l_fin = 0;
					$parpage = 100;
					if($debit!=0){
						$parpage = 30;
					}
						$numberofpages = round($num_msg/$parpage);
						if($numberofpages*$parpage<$num_msg){
							$numberofpages++;
						}
						$page=1;
						if(isset($_GET['page'])){
							$page=$_GET['page'];
						}
						$l_deb = $parpage*($page-1);
						$l_fin = $parpage*$page;
						
						$l_deb = $num_msg-$l_deb;
						$l_fin = $num_msg-$l_fin;
						if($l_fin < 1){
							$l_fin = 0;
						}
						//$printlimit = $l_fin+1;
						$laspages="Pages : ";
						
					   for($p=1 ; $p<=$numberofpages ; $p++){
							if($p == $page){echo"<b><u>";}
							$laspages.="<a href='$openmel&mail=$mail&page=$p'>$p</a>";
							if($p == $page){echo"</u></b>";}
							$laspages.=" ";
					   }
					   if($debit==0){
						   echo $laspages;
					   }
										*/
					echo"</td></tr></table>
					<table cellpadding='1' cellspacing='1' border='0'";
					if($debit == 0) echo"width='100%'";
					echo">	";
						
					$bgtd = '1';
					$refre='';
					
					$lastdate='00000000';
					$sauj = date('Ymd');					
					$shie = date('Ymd',strtotime('-1 day'));
					$sse1 = date('Ymd',strtotime('-7 days'));
					$sse2 = date('Ymd',strtotime('-14 days'));					
					$smo1 = date('Ymd',mktime ( 0, 0, 0, date('n'),1,date('Y')) );					
					$smo2 = date('Ymd',strtotime('-1 month',mktime ( 0, 0, 0, date('n'),1,date('Y')) ));
					$san1 = date('Ymd',mktime ( 0, 0, 0, 1,1,date('Y')) );	
					$san2 = date('Ymd',mktime ( 0, 0, 0, 1,1,date('Y')-1) );	
					$dejaplustot=false;
					
					
					$depl = explode('<>',get_pref('mails.depli').'<>');
					$depa=array();
					$depa[0] = abs($depl[0]);
					$depa[1] = abs($depl[1]);
					$depa[2] = abs($depl[2]);
					$depa[3] = abs($depl[3]);
					$depa[4] = abs($depl[4]);
					$depa[5] = abs($depl[5]);
					$depa[6] = abs($depl[6]);
					$depa[7] = abs($depl[7]);
					if(isset($_GET['depli'])){
						$depli = $_GET['depli'];
						$depval = abs($_GET['depval']);
						$depa[$depli] = $depval;
						
						set_pref("mails.depli",implode('<>',$depa));
					}
					
					
					$affichemail=true;
				for($i=$l_fin ; $i>=0 ; $i--){
						$ucid = $i;
						if($sear!="1"){
							$ucid = $mbrange[$i];
						}
					$overview = imap_fetch_overview($mbox,$ucid);
					//$headers = imap_fetch_overview($mbox,$ucid);
					
					if(is_array($overview)) {
						reset($overview);
						while(list($key,$val) = each($overview)) {
							
							$mnum = $val->msgno;  
						   $mdate = $val->date;
						   $msujet =  quoted_printable_decode(imap_utf8($val->subject));
						   if($mail=='INBOX.sent-mail'){
							  	 $mfrom = quoted_printable_decode(imap_utf8($val->to)) ;
						   		$mto = quoted_printable_decode(imap_utf8($val->from)) ;
						   }
						   else{
						   		$mfrom = quoted_printable_decode(imap_utf8($val->from)) ;
						   		$mto = quoted_printable_decode(imap_utf8($val->to)) ;
						   }
						   $mstatut = $val->seen;
						   $mpoubelle = $val->deleted ;
						   $mflag = $val->flagged;
						   $mrepondu = $val->answered;
						   $munik = htmlspecialchars($val->message_id);
						   $msize = $val->size;
						   $uid = $val->uid;
						   $mid = $ucid;
						   
						  
						   
						   $mailfrom = $mfrom;
							if($munik == ""){
								$munik = "$mdate$mfrom";
							}
						   if(ereg("[Ã,Â]",$mfrom)){
							$mfrom = utf8_decode($mfrom);
						   }
						   if(ereg("[Ã,Â]",$mto)){
							$mto = utf8_decode($mto);
						   }
						   if(ereg("@",$mfrom)){
							$mfrom = substr($mfrom,0,strpos($mfrom,"@"));
						   }
						   $mfrom = strip_tags($mfrom);
						   if(strlen($mfrom)>23){
							$mfrom = substr($mfrom,0,23);
						   }
					 		
						  
						   
						   $mto = substr($mto,0,strpos(ereg_replace("\"","","$mto@"),"@"));
						   
							if(ereg("[Ã,Â]",$msujet)){
							$msujet = utf8_decode($msujet);
						   }
						   if($msujet == "" && $mail!='INBOX.sent-mail'){$msujet="sans sujet";}
						   $mpsujet = $msujet;
					 
						   $msujet = addslashes($msujet);
						   
							$midate = strtotime($mdate);
							$mdate = get_date($midate);//$NomDuJour[date("w",$midate)].date(" d/m/Y H:i",$midate);
							$bddate = date("d/m/y H:i",$midate);
									$msize=ponderal($msize);
							$sdate = date('Ymd',$midate);	
					
						   $staturead="lu";	
							$styletext="islu";
							$bt='';
							$pt='';
							if($mstatut == "0"){
								$styletext ="isnotlu";
								$staturead="non lu";
								$bt='<b>';
								$pt='</b>';
							} 
							 $colorgenre = "FFFFFF";
						
							$ititre = str_replace('"','~',"($mid) $msujet - $mto");
							$cll = 'listone';	
						   if($bgtd == '1'){
							$bgtd='2';
							$cll="listone";
						   }
						   else{
							$bgtd='1';
							$cll="listtwo";
						   } 
						   
						   if(substr($munik,0,strlen(htmlspecialchars("<Adeli$b_login")))==htmlspecialchars("<Adeli$b_login") && $mail!='INBOX.sent-mail'){
						   	if($refre!=""){
								$refre.=",";
							}
						   	$refre.="$uid";
						   }
						   else{
							  if($lastdate=='00000000' && $sdate==$sauj){
								   echo"<tr><td colspan='5'>";
								   if($depa[0]==1){
									   $affichemail=true;
									   echo"<a href='$openmel&part=lecture&mail=$mail&depli=0&depval=0#lm0' name='lm0'>[-]";
								   }
								   else{
									$affichemail=false;
									echo"<a href='$openmel&part=lecture&mail=$mail&depli=0&depval=1#lm0' name='lm0'>[+]";
								   }
								   echo"&nbsp;<b>Aujourd'hui</b></a></td></tr>";
								   $lastdate = $sdate;	
								   if($depa[0]==0 && $depa[1]==0 && $depa[2]==0 && $depa[3]==0 && $depa[4]==0 && $depa[5]==0 && $depa[6]==0 && $depa[7]==0){
										echo"
										<tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=1&depval=1#lm1' name='lm1'>[+]&nbsp;<b>Hier</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=2&depval=1#lm2' name='lm2'>[+]&nbsp;<b>Il y a une semaine</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=3&depval=1#lm3' name='lm3'>[+]&nbsp;<b>Plus tôt dans le mois</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=4&depval=1#lm4' name='lm4'>[+]&nbsp;<b>Le mois dernier</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=5&depval=1#lm5' name='lm5'>[+]&nbsp;<b>Plus tôt dans l'année</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=6&depval=1#lm6' name='lm6'>[+]&nbsp;<b>L'année dernière</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=1#lm7' name='lm7'>[+]&nbsp;<b>Plus tôt</b><br>...
										</td></tr>										
										";
										break 2;   
								   }
							   }
							   elseif($sdate!='19700101' &&  $sdate==$shie && ($lastdate==$sauj || $lastdate=='00000000')){
								   echo"<tr><td colspan='5'>";
								   if($depa[1]==1){
									   $affichemail=true;
									   echo"<a href='$openmel&part=lecture&mail=$mail&depli=1&depval=0#lm1' name='lm1'>[-]";
								   }
								   else{
									$affichemail=false;
									echo"<a href='$openmel&part=lecture&mail=$mail&depli=1&depval=1#lm1' name='lm1'>[+]";
								   }
								   echo"&nbsp;<b>Hier</b></td></tr>";
								   $lastdate = $sdate;
								   if($depa[1]==0 && $depa[2]==0 && $depa[3]==0 && $depa[4]==0 && $depa[5]==0 && $depa[6]==0 && $depa[7]==0){
										echo"
										<tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=2&depval=1#lm2' name='lm2'>[+]&nbsp;<b>Il y a une semaine</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=3&depval=1#lm3' name='lm3'>[+]&nbsp;<b>Plus tôt dans le mois</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=4&depval=1#lm4' name='lm4'>[+]&nbsp;<b>Le mois dernier</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=5&depval=1#lm5' name='lm5'>[+]&nbsp;<b>Plus tôt dans l'année</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=6&depval=1#lm6' name='lm6'>[+]&nbsp;<b>L'année dernière</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=1#lm7' name='lm7'>[+]&nbsp;<b>Plus tôt</b><br>...
										</td></tr>										
										";
										break 2;   
								   }
							   }
							   elseif($sdate!='19700101' && $sdate<$shie && $sdate>=$sse1 && ($lastdate==$shie || $lastdate==$sauj || $lastdate=='00000000')){
								   echo"<tr><td colspan='5'>";
								   if($depa[2]==1){
									   $affichemail=true;
									   echo"<a href='$openmel&part=lecture&mail=$mail&depli=2&depval=0#lm2' name='lm2'>[-]";
								   }
								   else{
									$affichemail=false;
									echo"<a href='$openmel&part=lecture&mail=$mail&depli=2&depval=1#lm2' name='lm2'>[+]";
								   }
								   echo"&nbsp;<b>Il y a une semaine</b></td></tr>";
								   $lastdate = $sse1;
								    if($depa[2]==0 && $depa[3]==0 && $depa[4]==0 && $depa[5]==0 && $depa[6]==0 && $depa[7]==0){
										echo"
										<tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=3&depval=1#lm3' name='lm3'>[+]&nbsp;<b>Plus tôt dans le mois</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=4&depval=1#lm4' name='lm4'>[+]&nbsp;<b>Le mois dernier</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=5&depval=1#lm5' name='lm5'>[+]&nbsp;<b>Plus tôt dans l'année</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=6&depval=1#lm6' name='lm6'>[+]&nbsp;<b>L'année dernière</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=1#lm7' name='lm7'>[+]&nbsp;<b>Plus tôt</b><br>...
										</td></tr>										
										";
										break 2;   
								   }
							   }
							   elseif($sdate!='19700101' && $sdate<$sse1 && $sdate>=$smo1 && ($lastdate==$sse1 || $lastdate==$shie || $lastdate==$sauj || $lastdate=='00000000')){
								   echo"<tr><td colspan='5'>";
								   if($depa[3]==1){
									   $affichemail=true;
									   echo"<a href='$openmel&part=lecture&mail=$mail&depli=3&depval=0#lm3' name='lm3'>[-]";
								   }
								   else{
									$affichemail=false;
									echo"<a href='$openmel&part=lecture&mail=$mail&depli=3&depval=1#lm3' name='lm3'>[+]";
								   }
								   echo"&nbsp;<b>Plus tôt dans le mois</b></td></tr>";
								   $lastdate = $smo1;
								    if($depa[3]==0 && $depa[4]==0 && $depa[5]==0 && $depa[6]==0 && $depa[7]==0){
										echo"
										<tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=4&depval=1#lm4' name='lm4'>[+]&nbsp;<b>Le mois dernier</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=5&depval=1#lm5' name='lm5'>[+]&nbsp;<b>Plus tôt dans l'année</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=6&depval=1#lm6' name='lm6'>[+]&nbsp;<b>L'année dernière</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=1#lm7' name='lm7'>[+]&nbsp;<b>Plus tôt</b><br>...
										</td></tr>										
										";
										break 2;   
								   }
							   }
							   elseif($sdate!='19700101' && $sdate<$smo1 && $sdate>=$smo2 && ($lastdate==$smo1 || $lastdate==$sse1 || $lastdate==$shie || $lastdate==$sauj || $lastdate=='00000000')){
								   echo"<tr><td colspan='5'>";
								   if($depa[4]==1){
									   $affichemail=true;
									   echo"<a href='$openmel&part=lecture&mail=$mail&depli=4&depval=0#lm4' name='lm4'>[-]";
								   }
								   else{
									$affichemail=false;
									echo"<a href='$openmel&part=lecture&mail=$mail&depli=4&depval=1#lm4' name='lm4'>[+]";
								   }
								   echo"&nbsp;<b>Le mois dernier</b></td></tr>";
								   $lastdate = $smo2;
								    if($depa[4]==0 && $depa[5]==0 && $depa[6]==0 && $depa[7]==0){
										echo"
										<tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=5&depval=1#lm5' name='lm5'>[+]&nbsp;<b>Plus tôt dans l'année</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=6&depval=1#lm6' name='lm6'>[+]&nbsp;<b>L'année dernière</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=1#lm7' name='lm7'>[+]&nbsp;<b>Plus tôt</b><br>...
										</td></tr>										
										";
										break 2;   
								   }
							   }							   
							   elseif($sdate!='19700101' && $sdate<$smo2 && $sdate>=$san1 && ($lastdate==$smo2 || $lastdate==$smo1 || $lastdate==$sse1 || $lastdate==$shie || $lastdate==$sauj || $lastdate=='00000000')){
								   echo"<tr><td colspan='5'>";
								   if($depa[5]==1){
									   $affichemail=true;
									   echo"<a href='$openmel&part=lecture&mail=$mail&depli=5&depval=0#lm5' name='lm5'>[-]";
								   }
								   else{
									$affichemail=false;
									echo"<a href='$openmel&part=lecture&mail=$mail&depli=5&depval=1#lm5' name='lm5'>[+]";
								   }
								   echo"&nbsp;<b>Plus tôt dans l'année</b></td></tr>";
								   $lastdate = $san1;
								   if($depa[5]==0 && $depa[6]==0 && $depa[7]==0){
										echo"
										<tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=6&depval=1#lm6' name='lm6'>[+]&nbsp;<b>L'année dernière</b>
										</td></tr><tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=1#lm7' name='lm7'>[+]&nbsp;<b>Plus tôt</b><br>...
										</td></tr>										
										";
										break 2;   
								   }
							   }
							   elseif($sdate!='19700101' && $sdate<$san1 && $sdate>=$san2 && ($lastdate==$san1 || $lastdate==$smo2 || $lastdate==$smo1 || $lastdate==$sse1 ||$lastdate==$shie || $lastdate==$sauj || $lastdate=='00000000')){
								   echo"<tr><td colspan='5'>";
								   if($depa[6]==1){
									   $affichemail=true;
									   echo"<a href='$openmel&part=lecture&mail=$mail&depli=6&depval=0#lm6' name='lm6'>[-]";
								   }
								   else{
									$affichemail=false;
									echo"<a href='$openmel&part=lecture&mail=$mail&depli=6&depval=1#lm6' name='lm6'>[+]";
								   }
								   echo"&nbsp;<b>L'année dernière</b></td></tr>";
								   $lastdate = $san2;
								    if($depa[6]==0 && $depa[7]==0){
										echo"
										<tr><td colspan='5'>
										<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=1#lm7' name='lm7'>[+]&nbsp;<b>Plus tôt</b><br>...
										</td></tr>										
										";
										break 2;   
								   }
							   }
							   elseif($sdate!='19700101' && $sdate<$san2 && $dejaplustot==false){
								   echo"<tr><td colspan='5'>";
								   if($depa[7]==1){
									   $affichemail=true;
									   echo"<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=0#lm7' name='lm7'>[-]";
								   }
								   else{
									$affichemail=false;
									echo"<a href='$openmel&part=lecture&mail=$mail&depli=7&depval=1#lm7' name='lm7'>[+]";
								   }
								   echo"&nbsp;<b>Plus tôt</b></td></tr>";
								   $dejaplustot = true;
							   }
							   
							   
							 if($affichemail==true || $mstatut == "0" || $sear!='2'){ 
						   	if($debit == 0){
							   echo "<tr id=\"tab$mid\" style='cursor:pointer;' class='$cll'>
							   <td class='ltd' width='25'><a name='p$mid'></a><input type='checkbox' name='is$mid' value='$uid' onclick=\"colortab('tab$mid',this.checked,'$cll')\">";
							   if($mflag==1) echo'*';
							   $mdate = str_replace(',','<br>',$mdate);
							   echo"</td>
							   <td onclick=\"read($mid)\" class='ltd'><div class='$styletext' style='width:340px;'>$mfrom<br><i>$mpsujet</i></div></td>
							   <td onclick=\"read($mid)\" class='ltd'><div style='width:100px;' class='$styletext'>$mdate</div></td>
							   <td onclick=\"read($mid)\" class='ltd' align='right'><div style='width:70px;' class='$styletext'>$msize $ispieces</div></td>
								</tr>
								";	
								// <td onclick=\"read($mid)\" class='ltd' style='width:100px;$styletext'><div style='width:90px;$styletext'>$mto</div></td>
								}
								else{
									echo "<tr>
							<td><input type='checkbox' name='is$mid' value='$uid' style='width:20px;height:20px'>";
							   if($mflag==1) echo'*';
							   echo"</td>
							   <td><a href='$openmel&part=lecture&mail=$mid&tot=$num_msg' target='_parent'>$bt".substr($mfrom,0,15)."<br><i>".substr($mpsujet,0,20)."</i>$pt</a></td>
							   <td align='right'><a href='$openmel&part=lecture&mail=$mid&tot=$num_msg' target='_parent'>$bt$mdate$pt</a></td>
								</tr>
								";	
								}	
							 }
							}
						}
					}
					
				}
				if($refre!='' && $mail!='INBOX.sent-mail'){
					imap_mail_move ( $mbox, "$refre", "INBOX.sent-mail", CP_UID );							
					imap_expunge($mbox);
					echo"<script language='javascript'>
					//alert('$refre');
					document.location='$openmel&mail=$mail';
					</script>";
				}
				echo"</table>";
				
				if($debit == 0){
				/*echo"
				<table id='top_col' style='position:absolute;top:0px;left:0px' cellpadding='1' cellspacing='0' border='0' width='100%'>
					<tr>
			   <td width='50' class='buttontd'><div style='width:40px;'>&nbsp;</div></td>
			   <td width='100' class='buttontd'><div style='width:90px;'>De</div></td>
			   <td width='100' class='buttontd'><div style='width:90px;'>&Agrave;</div></td>
			   <td width='150' class='buttontd'><div style='width:140px;'>Sujet</div></td>
			   <td width='150' class='buttontd'><div style='width:140px;'>Date</div></td>
			   <td width='50' class='buttontd'><div style='width:40px;'>Taille</div></td></tr>
			   <tr>
			   </table>
			   <script language='javascript'>
				function topcol(){
					document.getElementById('top_col').style.top=document.body.scrollTop-2;				
					setTimeout('topcol()',1);					
				}
				topcol();
				
				</script>
				";*/
				
				}
				else{
					echo"<input type='submit' value='Supprimer'>";
				}
				//$laspages
				echo"
				
				</form>";
			}
		}
		/*************************************************************************************************************************
		
							 LECTURE MESSAGE
		
		*************************************************************************************************************************/
		elseif(isset($_GET["mail"]) && is_numeric($mail) && isset($_GET['del'])){
			$mbox = imap_open("\{$b_serveur:143/imap/notls}".$b_dossier,"$b_login","$b_pass");
						
			if($mbox==NULL){
				echo"connexion impossible  au serveur (".$_SESSION['s_id'].") <b>$b_serveur</b> 
				avec le compte <b>$b_login</b><br>" . imap_last_error();
			}
			elseif($mail!=0 ){
				$overview = imap_fetch_overview($mbox, $mail);
				if(is_array($overview)) {					 
					reset($overview);
					while(list($key,$val) = each($overview)) {
					   $mnum = $val->msgno;
					   $mdate = $val->date;
					   $msujet =  quoted_printable_decode(imap_utf8($val->subject));
					   $mid = $val->uid;
					}
					echo"
					êtes vous sûr de vouloir supprimer le courriel <b>$msujet</b> ?<br>
					<a href='$openmel&mail=$mail' class='buttontd'>Non</a>
					<a href='$openmel&mail=".$_SESSION["ma_dossier"]."&effect&del=$mid' class='buttontd'>Oui</a>
					";
				}
			}
		
		}
		elseif(isset($_GET["mail"]) && is_numeric($mail)){
		
			$mbox = imap_open("\{$b_serveur:143/imap/notls}".$b_dossier,"$b_login","$b_pass");
			$dossier = $_GET['dossier'];
			if($dossier=='') $dossier='INBOX';
			if($mbox==NULL){
				echo"connexion impossible  au serveur (".$_SESSION['s_id'].") <b>$b_serveur</b> 
				avec le compte <b>$b_login</b><br>" . imap_last_error();
			}
			elseif($mail!=0 ){
				//imap_setflag_full($mbox,imap_uid($mbox,$mail),'\\SEEN',ST_UID);
	  $status = imap_setflag_full($mbox, imap_uid($mbox,$mail),"\\Seen \\$x_id",ST_UID );

				//echo gettype($status) . "\n";
				//echo $status . "\n";

				//imap_expunge($mbox);
				
				$overview = imap_fetch_overview($mbox, $mail);
				$infos = imap_headerinfo($mbox, $mail);
				if(is_array($overview)) {
					 	$encod=4;
					 
							reset($overview);
							while(list($key,$val) = each($overview)) {
							   $mnum = $val->msgno;
							   $mdate = $val->date;
							   $msujet =  quoted_printable_decode(imap_utf8($val->subject));
							   $mfrom = quoted_printable_decode(imap_utf8($val->from)) ;
							   $mto = quoted_printable_decode(imap_utf8($val->to)) ; 
							   $mbcc = htmlentities(quoted_printable_decode(imap_utf8(($infos->bccaddress))));
							   $mcc = htmlentities(quoted_printable_decode(imap_utf8(($infos->ccaddress))));
							   $mtoto = htmlentities(quoted_printable_decode(imap_utf8(($infos->toaddress))));
						   	   $mstatut = $val->seen;
							   $munik = htmlspecialchars($val->message_id);
							   $msize = $val->size;
							   $mto = imap_utf8($val->to);
							   $mid = $val->uid;
							   if($munik == ""){
									$munik = "$mdate$mfrom";
								}
							}
					
							
							$mfrom = utf8_decode($mfrom);
							$mailfrom = str_replace('"','',$mfrom);
							if(ereg("@",$mfrom)){
								$mfrom = substr(strip_tags($mfrom),0,strpos($mfrom,"@"));
							}
							$header=imap_headerinfo($mbox, $mail);
								  $from=$header->from;								  
								  $mailfrom = $from[0]->mailbox."@".$from[0]->host;
						   
							$msujet = utf8_decode($msujet);
					
							if($msujet == ""){
								$msujet="sans sujet";
							}
							
					
							$mystat = date("U", strtotime($mdate));
							$datenvoi = date("d/m/Y à H:i", strtotime($mdate));
							$nystat = date("U", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y") ));
					
							
								$dif = round(($nystat-$mystat)/60);
								if($dif <= 5){
										$dif = "$dif min";
								}
								elseif($dif <= 60){
										$dif = "$dif min";
								}
								elseif($dif <= 60*12){
										$dif = round(($dif/60),2);
										$min = substr($dif,2,2);
										$dif = substr($dif,0,1);
										$min = round($min/100*60);
										if($min < 9){
											$min = "0".$min;
										}
										$dif = $dif."h".$min;
								}
								elseif($dif <= 60*24*6){
									$dif = round(($dif/(60*24)))." jour(s)";
								}
								elseif($dif <= 60*24*30){
									$dif = round(($dif/(60*24*7)))." semaines(s)";
								}
								elseif($dif <= 60*24*30*12){
									$dif = round(($dif/(60*24*30)))." mois(s)";
								}
								else{
									$dif = round(($dif/(60*24*30*12)))." an(s)";
								}
								
					  $mnum=$mid;
					  echo "
					  <table  cellspacing='0' cellpadding='5'";
					if($debit == 0) echo"width='100%'";
					echo">
			  <tr><td class='buttontd' style='text-align:left'>";
			  
			  if($debit == 0){
			  	echo"
				<form name='farmermail' action='$openmel&mail=$dossier' method='post'>
					<input type='hidden' name='effect'>
				<input type='hidden' name='is$mail' value='$mid' checked>
				</form>
					  <div id='entete' style='display:block;width:100%;height:70px;overflow:hidden;'>
					  <table  cellspacing='0' cellpadding='3' width='100%' style='height:200px'>
					  <tr><td valign='top' align='left'>
					  <br>";
			  }
			  else{
			  echo"
					  <div>
					  <table  cellspacing='0' cellpadding='3'>
					  <tr><td valign='top' align='left'>
					  <br>";
			  }
					  echo"
						<b>$mfrom</b> <span class='petittext'>";
				if($debit == 0){
					
					$conn = mysql_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_pass']);
					mysql_select_db($_SESSION['db_base']);
					
					$folow='';

					$site_menupart = array_keys($menu_site);
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
							$db = $tablo[$tk];
							if(mysql_query("SHOW COLUMNS FROM `$db`")  ){	
								$command='';
								$res_field = mysql_list_fields($_SESSION['db_base'],$db);
								$columns = mysql_num_fields($res_field);
								for ($c=0 ; $c < $columns; $c++) {
									$field_type = mysql_field_type($res_field, $c); 
									if($field_type=='string' || $field_type=='blob'){
										$field_name = mysql_field_name($res_field, $c);
										$command.="`$field_name`LIKE'%$mailfrom%' OR";
									}
								}
								$command = substr($command,0,strlen($command)-3);
								$result1 = mysql_query("SELECT `id` FROM `$db` WHERE $command");
								
								if($result1 && mysql_num_rows($result1)>0){									
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
									$folow.="<optgroup label='$humanpart'>$humanpart";
									while($ru = mysql_fetch_array($result1)){
										$folow.=" <option value='../?option=$sepa&part=$db&edit=$ru[0]'>#$ru[0]</option> ";
									}
									$folow.="</optgroup>";
								}
							}				
						}		
					}	
					if($folow!=''){
						echo"\n<select onchange='parent.document.location=this.value'>
						<option value='#'>liens</option> 
						$folow
						</select>";	
					}
				}	
				
					echo"</span><br><b>$msujet</b>
						<br>";
					if($debit == 0) echo"Reçu le : ";
					echo"$datenvoi<br>";
				if($debit == 0){
					echo"
						<br><b>&Agrave;:</b> $mtoto
						<br><b>Cc:</b> $mcc
						<br><b>Bcc:</b> $mbcc
						<br>
						<br>
						<br><b>Contenu:</b> $mbcc
						<br>
					
						"; 
				}
					 $pieces ="<table>";
					 $piecesh='';
						
					   $replytxt="";
					   $plain="";
					   $notext=0;
					   $html='';
					   
						$struct = imap_fetchstructure($mbox, $mail);
					   $structpart = $struct->parts;
					   $numpart=0;
					   
					   $encode = $structpart[0]->encoding;
					   
					 
					   $funcdecode = array("7BIT","8BIT","BINARY","BASE64","QUOTED-PRINTABLE","OTHER");
					   $is_sub=false;
				   function lit($structpart,$nivo=0){
				   		global $mbox,$mail,$pieces,$plain,$html,$extsis,$numpart,$vers,$funcdecode,$openmel,$is_sub,$piecesh, $debit;
				   		$locpart=0;
						foreach($structpart as $yopiyop) {
						  
						  $disposition = $yopiyop->disposition;
						  $boundary = $yopiyop->boundary;
						  $subtype = $yopiyop->subtype;
						  $bytes = ($yopiyop->bytes)/1000;
						  $subtype = $yopiyop->subtype;
						  $encoredesparts = $yopiyop->parts;
						  $encoding = $yopiyop->encoding;
						  $parameters = $yopiyop->parameters;
						  if(isset($yopiyop->description)){
						  	$value=$yopiyop->description;
						  }
						  elseif(is_array($parameters)){
							  $value = $parameters[0]->value;
						  }
						  elseif(isset($yopiyop->dparameters)){
						  	  $dparameters=$yopiyop->dparameters;
							  $value = $dparameters[0]->value;
						  }						  
						  else{
						  	$value='object non identifié';
						  }
						  $typedevar = gettype($encoredesparts);
						  if($debit == 0){ 
						  	echo"<br>";
							if($nivo>0) echo"&nbsp;&nbsp;";
							echo"$subtype  ($funcdecode[$encoding])";
						  }
						  $oktodebug=1;
						  $value = utf8_decode(quoted_printable_decode($value));
						
										  
						  
							if($numpart==1){
								$encoding_one=$encoding;
							}
							$dedans=$numpart;
							
							if($nivo>0){
								$locpart++;								
								$dedans=($nivo).'.'.($locpart);
									
							}
							else{
								$numpart++;
								$dedans = $numpart;
							}
							if($dedans==0) $dedans=1;					
							if(isset($yopiyop->parts) || $subtype=='RELATED' || $subtype=='MIXED' || $subtype=='APPLEDOUBLE'){
								if($debit == 0) echo" $dedans:(<blockquote> ";
								$is_sub=true;
								if($nivo!=0){
									lit($yopiyop->parts,$numpart.'.'.$locpart);//$nivo+1										
								}
								else{
									lit($yopiyop->parts,$nivo+1);//
								}
								if($debit == 0) echo'</blockquote> ) ';
							}	
							elseif( ($subtype == "PLAIN" || $subtype == "HTML"  ) && $disposition!="ATTACHMENT"){
									if($debit == 0) echo" message $dedans ";
									$varde = strtolower($subtype);
										if($is_sub==false){
											$dedans++;
										}
										if(abs($nivo)>0){
										  $texte = imap_fetchbody($mbox, $mail,$dedans);
										  echo"#";
										  }
										  else{
											$texte = imap_fetchbody($mbox, $mail,$numpart);
										  }
									  //if($texte=='') $texte = imap_fetchbody($mbox, $mail,$nivo);
									  //echo $texte;
									  if($encoding == "0"){
										 // $text = imap_utf7_decode($text);
										 ${$varde} .= quoted_printable_decode($texte);
									  }	
									  elseif($encoding == "1"){
										  ${$varde} .= quoted_printable_decode(imap_8bit($texte));
									  }	
									  elseif($encoding == "2"){
										  ${$varde} .= imap_base64 (imap_binary ($texte));
									  }					   
									  elseif($encoding == "3"){
										  ${$varde} .= imap_base64 ($texte);
									  }	
									  elseif($encoding == "4"){
										${$varde} .= quoted_printable_decode($texte);
									  }
									  else{
										if($debit == 0) echo"default encodage";
											${$varde} .= imap_8bit(quoted_printable_decode(imap_base64 (imap_binary ($texte))));
									  }
									  ${$varde} .= '  ';
									}
							else{//if(strtolower($disposition)!='inline'){			
								$file_extension = strtolower(substr(strrchr(trim($value),"."),1));
								  echo" $dedans";
								  if($file_extension == NULL){
									if(isset($extsis[$subtype])){
										$file_extension = $extsis[$subtype];
									}
									else{
										$file_extension = strtolower($subtype);
									}
									$value = "$value.$file_extension";
								 }	
								$pieces.="<tr><td> <table width='100' style='border-width:1px; border-style:solid; border-color:FFFFFF 999999 999999 FFFFFF;cursor:pointer' bgcolor='EEEEEE' onclick=\"document.location='$openmel&mail=$mail&piece=$dedans&nom=$value&encoding=$encoding'\"><tr><td align='center'><img src='http://www.adeli.wac.fr/icos/$file_extension.gif' border='none' align='absbottom'> </td></tr></table></td>
								<td style='color:#000000;'>$value</td><td style='color:#000000;'>($bytes Ko)</td><td><a href='$openmel&mail=$mail&piece=$numpart'><img src='$style_url/images/down.gif' border='none' alt='télécharger'></a></td></tr>";
								$piecesh.="<a href=\"$openmel&mail=$mail&piece=$dedans&nom=$value&encoding=$encoding\" class='info'><font size='1'>$value</font><span>($bytes Ko)</span><a> ";
							}
							
							if($debit == 0) echo"/";
						} 
				   }
				   
				   lit($structpart,0);
					
				  
					$pieces.="</table>";
					echo"<br>";
					if($html!='' ){//&& ($debit==0 || ($debit != 0 && $plain==''))){
						 $text=$html;
						 if($debit == 0) echo"HTML";
					}
					elseif($plain!=''){
						 $text=$plain;
						if($debit == 0)  echo"PLAIN";	
					}				
					else{
						$text = imap_fetchbody($mbox, $mail, 1);
						if($encoding=='') $encoding=$encode;
						$encoding = $encoding_one;
									if($encoding == "0" || ereg('=E9',$text)){
										 // $text = imap_utf7_decode($text);
										 $text = quoted_printable_decode($text);
									  }	
									  elseif($encoding == "1"){
										  $text = imap_8bit($text);
									  }	
									  elseif($encoding == "2"){
										  $text = imap_base64 (imap_binary ($text));
									  }					   
									  elseif($encoding == "3" || !ereg(' ',$text)){
										  $text = imap_base64 ($text);
									  }	
									  elseif($encoding == "4"){
											$text = quoted_printable_decode($text);
									  }
									  else{
										//$text = quoted_printable_decode($text);
									  }
									  
						if($debit == 0) echo"default content > $encoding ";
					}
					if(!ereg("</",	$text) && !ereg("<br>",$text)){
						if($debit == 0) echo"HTML rendu ";
						$taxt = split("[ \n]",$text);
						$toxt="";
						for($i=0 ; $i<sizeof($taxt) ; $i++){
						  $tuxt = wordwrap($taxt[$i],40,"-<br>",1);
						  if(ereg("://",$taxt[$i])){
						   $toxt.= "<a href='".trim($taxt[$i])."' target='_blank'><u>$tuxt</u></a> ";
						  }
						  elseif(ereg('www.',$taxt[$i])){
						   $toxt.= "<a href='http://".trim($taxt[$i])."' target='_blank'><u>$tuxt</u></a> ";
						  }
						  elseif(ereg('@',$taxt[$i])){
						   $toxt.= "<a href='mailto:".trim($taxt[$i])."'><u>$tuxt</u></a> ";
						  }
						  else{
						   $toxt.= $taxt[$i]." ";
						  }
						}
						$text = nl2br($toxt);
					}
					
					$body=str_replace("cid:","$openmel&mail=$mail&print&piece=",$text);
				 
				  if($piecesh!=''){
				  	$piecesh="<hr><font size='1'>Pièces jointes :</font> ".$piecesh;
				  }

				   echo"	   
			  </td>
			  <td align='right' valign='top'>$comp<br> il y a $dif &nbsp; ";
			 if(isset($_GET['tot']) && is_numeric($_GET['tot'])){
				$tot = $_GET['tot'];
				if($mail > 1) echo"<a href='$openmel&part=lecture&mail=".($mail-1)."&tot=$tot'><img src='$style_url/$theme/fl_g.png' alt='<<' border='none'></a>";
				echo" <span class='petittext'><b>$mail</b>/$tot</span> ";
				if($mail < $tot) echo"<a href='$openmel&part=lecture&mail=".($mail+1)."&tot=$tot'><img src='$style_url/$theme/fl_r.png' alt='>>' border='none'></a>";
			}			  
			  echo"<br><br> ";
			 if($debit == 0){
				 echo" <a href='#' onclick='self.print()' class='buttontd'>Imprimer</a>&nbsp;";
			  if(in_array('agenda',$_SESSION['opt'])){
			  	$agebody='';
				$bodi=split("\n",strip_tags(trim($body)));
				for($e=0 ; $e<sizeof($bodi) ; $e++){
					$agebody.=trim(trim($bodi[$e]))." ";
				}
				$agebody = urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode($agebody))));
			  	
				echo"
				<a href='#' onclick=\"parent.contextage('".date("Y-m-d")."','".date("H:i:s")."',event,'Ajouter une date','add','99CCCC',0);parent.document.agendaform.qui.value='$u_nom';parent.document.agendaform.client.value='".join(" ",split("\n",ereg_replace("\n","",strip_tags(trim($mailfrom)))))."';parent.document.agendaform.note.value='".str_replace("'","\'",str_replace('"','',$agebody)).";'\" class='buttontd'>Rappeler</a>";
				
			  }
			  echo"&nbsp;<a href='#respons' onclick='document.ecrou.message.focus()' class='buttontd'>Répondre</a>&nbsp;";
			
					echo"<a href='#' onclick='sizpa(\"entete\",parseInt(document.getElementById(\"entete\").scrollHeight));mysi();' class='buttontd'>Détails</a>";
			}
			else{
				echo"<a href='$openmel&mail=$mail&del' class='buttontd'>Supprimer</a>&nbsp;<a href='#respons' onclick='document.ecrou.message.focus()' class='buttontd'>Répondre</a>&nbsp;<a href='$openmel&mail=INBOX&c=$now' target='lister' class='buttontd'>Retour</a>";
			}
			  echo"</td>
			  </tr> ";
			  if($debit == 0){
			  echo"
				  <tr><td valign='bottom' colspan='2' align='center'>				   
				   <a href='#' onclick='sizpa(\"entete\",70);mysi();' class='buttontd'>Masquer les détails</a>
				   </td></tr>";
				   
				 }
				 else{
					 $body = html_my_text(trim(strip_tags($body)));
				 }
				 $body = str_replace("_____________________________________________","_____________________________________________ ",$body);
				 $body = str_replace("<pre>","<span>",$body);
				 $body = str_replace("</pre>","</span>",$body);
				 $body = str_replace('<BASE','<br',$body);
				 $body = str_replace('<base','<br',$body);
				 $body = str_replace('<META','<br',$body);
				 $body = str_replace('<meta','<br',$body);
				 if(strpos($body,'charset=utf-8')>-1){
					$body=utf8_decode($body); 
				 }
  				$plein = ereg_replace("<STYLE[*?]</STYLE>",'',$body);
				$plein = strip_tags($plein);
				 	 
				 echo"
			  </table>
			  </div>			  
			  $piecesh
			  </td></tr>
			  <tr><td style='color:#000000;' align='left' bgcolor='#FFFFFF'>
			  $body<br>
			  <link rel='stylesheet' href='$style_url/$theme/style.css' type='text/css'>
				<link rel='stylesheet' href='$style_url/style.css' type='text/css'>
		
			  ";
			  if($debit == 0){
				  if($pieces != "<table></table>"){
					  echo"<br><hr>
					  <b>pièces jointes</b><br>$pieces";
				  } 
				} 
			 
					  echo"
					  
					  <br><br>
					  <form action='$openmel&ecrire' method='post' name='ecrou' enctype='multipart/form-data'><a name='respons'></a>
			<table style='width:100%;' cellspacing='0' cellpadding='3'>
			<tr><td colspan='2' class='buttontd' style='text-align:left'><input type='submit' class='buttontd' value='Envoyer'></td></tr>
			<tr><td width='50'><b>De :</b></td><td><input type='text' name='de' value=\"$u_nom<$b_login>\" style='width:80%;'></td></tr>
			<tr><td width='50'><b>&Agrave; :</b></td><td><input type='text' name='to' value=\"$mailfrom\" style='width:80%;'></td></tr>
			<tr><td width='50'><b>Cc :</b></td><td><input type='text' name='cc' value=\"$mcc\" style='width:80%;'></td></tr>
			<tr><td width='50' valign='top'><b>Sujet :</b></td><td><input type='text' name='sujet' value=\"Re: $msujet\" style='width:80%;'><div id='fils'></div>
			<b>+ <a style='cursor:pointer' onclick='addfile()'>Attacher un fichier</a></b>
			</td></tr>
			<tr><td colspan='2'>
			<input type='hidden' name='nbf' value='0'>
				<textarea id='messa' name='message' style='width:";
					if($debit == 0) echo"90%";
					else echo"200px";
					echo";height:200px'>";
			if($debit==0){
echo"
$signa

----------

en réponse à : \"$msujet\",
envoyé le: $datenvoi,
par : $mailfrom
à : $mto

".$plein;
	}

		echo"</textarea></td></tr>
			<tr><td colspan='2' class='buttontd' style='text-align:left'><input type='submit' class='buttontd' value='Envoyer'></td></tr>			
			</table>			
			</form>
					  
					  
					  </td></tr></table>
					  ";
				}
		
			}
			else{
				echo"veuillez sélectionner un email dans la liste ci-dessus";
			}
			echo"
		
		";
			
		}
		if( 
		(isset($_GET['mail']) && isset($_GET['part']) && $_GET['part']=='lecture')
		||
		(isset($_GET['ecrire']) && isset($_GET['part']) && $_GET['part']=='nouveau')
		 ){
		echo"<span id='deb'></span>
		<script language='javascript'>
		function mysi(){
			nam = this.name;
			parent.document.getElementById(nam).style.width='100%';
			
			var Hu;
			Hu=document.getElementById('corps').scrollHeight;
			Wi=document.getElementById('corps').scrollWidth;
			if(isNaN(Hu)) Hu = parseInt(Hu);
			if(isNaN(Wi)) Wi = parseInt(Wi);
			Hu+=20;
			
			parent.document.getElementById(nam).style.height='200px';
			if(isNaN(Hu) || Hu<200) Hu=200;	 	
			if(isNaN(Wi) || Wi<400) Wi=400;	 	
			parent.document.getElementById(nam).style.height=Hu+'px';
			parent.document.getElementById(nam).style.width=Wi+'px';
			
			
			
			
				
		}
		mysi();
		</script>
		
		";
		}
		echo"<script language='javascript'>
		parent.unaffichload();
		parent.scanlogges('bin/inc_ajax.php?scan=mail','ajax_mail',0,false);
		</script></corps></body></html>";	
	}
	
}

?>