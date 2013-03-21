<?php // 397 > Lecteur de courriel ;
$conn = connecte($base, $host, $login, $passe);
if(mysql_query("SHOW COLUMNS FROM $mail_base")  ){
insert('_mail_lecteur');
if(is_file('bin/_mail_lecteur.php')){
	$openmel='bin/_mail_lecteur.php';
}
else{
	$openmel='$style_url/update.php?file=_mail_lecteur.php?1';
}


		$verifupdt = mysql_query("DESC `$mail_base`");
		$allchamps = array();
		while($ro = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$ro->Field);
		}
		if(!in_array("only",$allchamps)){
			mysql_query("ALTER TABLE `$mail_base` ADD `only` BIGINT NOT NULL default '0'");
		}

	


$signa=get_pref('mails.signture');
$_SESSION['signature']=$signa;

	if($modul_part == NULL){
		
		echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
		<tr><td class='buttontd' style='text-align:left'>&nbsp;<td>
		</tr>
		<tr><td valign='top' class='cadrebas' align='left' style='padding:20px'>	
		
		";
		$res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') ORDER BY `nom`");
		if($res && mysql_num_rows($res)){
			echo"<table cellpadding='10'>";
			while($ro = mysql_fetch_object($res)){
				$b_nom = $ro->nom;
				$b_id = $ro->id;
				$b_serveur = $ro->serveur;
				$b_login = $ro->login;
				$b_pass = $ro->pass;
				$b_dossier = $ro->dossier;
				$b_port = $ro->port;
				$difmes =='';
				if(false !== $mbox = imap_open("\{$b_serveur:$b_port$b_dossier}$mail",$b_login,$b_pass) ){     
						$status = imap_status($mbox, "\{$b_serveur:$b_port$b_dossier}INBOX", SA_ALL);
						$num_msg = $status->messages;
						$difmes = '('.($status->unseen).')';
				}
				echo"<tr>
				<td><a href='./?lecture&b=$b_id'><b>$b_nom</b> $difmes</a></td>
				<td> <a class='buttontd'  href='./?lecture&b=$b_id'>Lire</a> </td>
				<td> <a class='buttontd'  href='./?nouveau&b=$b_id'>&Eacute;crire</a> </td>
				<td> <a class='buttontd'  href='./?comptes&b=$b_id'>Modifier</a> </td>
				</tr>";
			}
			echo"</table>";
		}
		else{
			echo"Aucune boite mail n'a été paramétrée<br><br><a href='./?option=mail&part=comptes&b' class='buttontd'>Créer un compte</a>";	
		}
		echo"
		
		</td></tr></table>";
	
	}

	elseif($modul_part == "messages"){
		echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
		<tr><td class='buttontd' style='text-align:left'>
		Boite aux lettre : 
		<select onchange=document.location=this.value><option value='./?option=$option'></option>";
		$res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') ORDER BY `nom`");
		$nbtruc = abs(mysql_num_rows($res));
		$nbtruc += 1;
		if(!isset($_SESSION['s_id'])){
			$_SESSION['s_id'] = 0;
		}
		if(isset($_GET['b'])){
			$_SESSION['s_id'] = $_GET['b'];
		}
			while($ro = mysql_fetch_object($res)){
				$b_nom = $ro->nom;
				$b_id = $ro->id;
				if($b_id == $_SESSION['s_id']){
					echo"<option value='./?option=$option&part=$part&b=$b_id' selected>$b_nom</option>";
					$b_serveur = $ro->serveur;
					$b_port = $ro->port;
					$b_dossier = $ro->dossier;
					$b_login = $ro->login;
					$b_pass = $ro->pass;
					
					$_SESSION["ma_nom"]=$b_nom;
					$_SESSION["ma_id"]=$b_id;
					$_SESSION["ma_serveur"]=$b_serveur;
					$_SESSION["ma_port"]=$b_port;
					$_SESSION["ma_dossier"]=$b_dossier;
					$_SESSION["ma_login"]=$b_login;
					$_SESSION["ma_pass"]=$b_pass;
				}
				else{
					echo"<option value='./?option=$option&part=$part&b=$b_id'>$b_nom</option>";
				}
			}
		echo"</select><td>
		</tr>
		<tr><td valign='top' class='cadrebas' align='left'>
		<table cellspacing='0' cellpadding='3' border='0' width='100%'><tr>
		<td valign='top' width='200'>
		
		";
		//if($part=="lecture"){
				echo"<br><span id='foldrs'>";
			$mbox = imap_open("\{$b_serveur:$b_port$b_dossier}$mail","$b_login","$b_pass");
			
		if(	isset($_GET["newdos"])){
			$newdos = ereg_replace("\.","_",$_GET["newdos"]);
			if(!imap_createmailbox($mbox, imap_utf7_encode("\{$b_serveur:$b_port$b_dossier}INBOX.$newdos"))){
				echo"le dossier <b>$newdos</b> n'a pu être créé<br>";
			}
		}

					$num_msg = imap_num_msg ($mbox);	
					set_pref("mails.".$_SESSION["ma_id"].".mail",$num_msg);
					
					
			echo"";
			$sousb = '';
			$folders = imap_listmailbox($mbox, "{".$b_serveur.":$b_port$b_dossier}", "*");
			if ($folders == false) {
			  // echo "Aucun sous dossier<br />\n";
			} else {
				//$folders= array_reverse($folders);
				sort($folders);
				$lastdos="";
				$optsels="";
				
				if(!in_array("INBOX.sent-mail",$folders)){
					imap_createmailbox($mbox, imap_utf7_encode("\{$b_serveur:$b_port$b_dossier}INBOX.sent-mail"));
				}
				if(!in_array("INBOX.spam",$folders)){
					imap_createmailbox($mbox, imap_utf7_encode("\{$b_serveur:$b_port$b_dossier}INBOX.spam"));
				}
				if(!in_array("INBOX.trash",$folders)){
					imap_createmailbox($mbox, imap_utf7_encode("\{$b_serveur:$b_port$b_dossier}INBOX.trash"));
				}
				if(!in_array("INBOX.drafts",$folders)){
					imap_createmailbox($mbox, imap_utf7_encode("\{$b_serveur:$b_port$b_dossier}INBOX.drafts"));
				}
			   foreach ($folders as $val) {
			   	   $val = substr($val,strlen("{".$b_serveur.":$b_port$b_dossier}"));
				   $pval= $val;
				   if(ereg($lastdos,$val)){
				   		//echo"-";
						$pval = substr($val,strlen($lastdos)+1);
				   }
				   else{
				   		$lastdos=$val;
				   }
				   //if($pval=='INBOX'){
				   	//$pval='BOITE DE R&Eacute;C&Egrave;PTION';
				  // }
				   
				   //	$pval='';
				   $sval = str_replace('-','',$pval);
				   
				   $status = imap_status($mbox, "{".$b_serveur.":$b_port$b_dossier}".$val, SA_ALL);
					$nbmes = $status->messages;
					$nbnl = $status->unseen;
					
					$nlst="";
					if($nbnl>0){
						$nlst="font-weight:bolder;";
					}
				   $optsels.="<option value='$val'>$pval</option>";
				   if($pval!='sent-mail' && $pval!='spam' && $pval!='trash' && $pval!='drafts' && $pval!='INBOX'){ 
				   //
				   	$sousb.= "<a href='$openmel&#63;mail=".$val ."&part=lecture' target='lister' class='info' style='display:block;$nlst'><img src='http://adeli.wac.fr/vers/$vers/images/mail_dos.png' alt='[]' border='none' align='absmiddle'> $pval";
					if($debit==0)$sousb.="<span style='top:0px;width:50px;left:0px'>$nbmes messages</span>";
					$sousb.="</a> ";
				   }
				   else{
				   	${"envoyer$sval"} = $nbmes;
				   }		
			   }
			}
			// 
		if($part=="lecture"){
			$lh = 440;
			$rh = 1;
		}
		if($part=="nouveau"){
			$lh = 1;
			$rh = 340;
		}
			echo"
			<a href='$openmel&#63;ecrire&part=nouveau' onclick=\"document.getElementById('lister').style.height='1px';\" target='lister'>
			<img src='http://adeli.wac.fr/vers/$vers/images/mail_new.png' alt='+' border='none' align='absmiddle'>Nouveau message</a>
			<br><br>
			<a href='$openmel&#63;mail=INBOX&part=lecture' $nlst target='lister'>
			<img src='http://adeli.wac.fr/vers/$vers/images/mail_in.png' alt='<' border='none' align='absmiddle'>Réception</a> <span class='petittext'>($envoyerINBOX)</span><br>
			<a href='$openmel&#63;mail=INBOX.sent-mail&part=lecture' $nlst target='lister'>
			<img src='http://adeli.wac.fr/vers/$vers/images/mail_out.png' alt='>' border='none' align='absmiddle'>Envoyés</a> <span class='petittext'>($envoyersentmail)</span><br>
			<span class='petittext'>
			$sousb
			</span></span>
			<a href='$openmel&#63;mail=INBOX.spam&part=lecture' $nlst target='lister'>
			<img src='http://adeli.wac.fr/vers/$vers/images/mail_spam.png' alt='X' border='none' align='absmiddle'>SPAMS</a> <span class='petittext'>($envoyerspam)</span><br>
			<a href='$openmel&#63;mail=INBOX.trash&part=lecture' $nlst target='lister'>
			<img src='http://adeli.wac.fr/vers/$vers/images/mail_trash.png' alt='C' border='none' align='absmiddle'>Corbeille</a><br>
		<br><br>
		<script language='javascript' type='text/javascript'>
		function newdos(){
			glok = prompt(\"Veuillez saisir le nom du dossier\",\"nouveau dossier\");	
			if(glok){
				document.location='./?option=$option&part=$part&newdos='+glok;
			}	
		}
		</script>	
		<a href='#' onclick='newdos()' class='buttontd'><font size='1'>
		<img src='http://adeli.wac.fr/vers/$vers/images/mail_don.png' alt='[+]' border='none' align='absmiddle'>Nouveau dossier</font></a>
		</td>";
				if($debit==0){
				echo"<td valign='top'>
		
				
				<table cellpadding='1' cellspacing='0' border='0' width='100%'>
				<tr><td align='left'><a style='cursor:pointer' onclick=\"ver=confirm('êtes vous sûr de vouloir supprimer les messages sélectionnés ?'); if(ver){lister.document.farmermail.action+='&del=';lister.document.farmermail.submit();}\" class='buttontd'>Supprimer</a>
				<a style='cursor:pointer' onclick=\"lister.document.farmermail.action+='&deplacedans=INBOX.spam';lister.document.farmermail.submit();\" class='buttontd'>Signaler comme spam</a>
				
				&nbsp;
				
				<select onchange=\"lister.document.farmermail.action+='&marqueas='+this.value;lister.document.farmermail.submit();;this.value=0\">
					<option value='0'>Marquer comme</option>
					<option value='SEEN'>Lu</option>
					<option value='UNSEEN'>Non lu</option>
					<option value='FLAGGED'>Suivi</option>
					<option value='UNFLAGGED'>Non suivi</option>
				 </select>	
				<select onchange=\"lister.document.farmermail.action+='&deplacedans='+this.value;lister.document.farmermail.submit();this.value=0\">
					<option value='0'>Déplacer vers</option>
					$optsels
				 </select>				
				</td><td align='right'>
				recherche <input type='text' onchange=\"lister.location=lister.location+'&q='+this.value\">
				</td></tr>
				<tr>
		   		<td colspan='2'>			   
				<iframe src='$openmel&#63;mail=INBOX&part=$part' width='100%' height='$lh' name='lister' id='lister' frameborder='0'></iframe>
				</td></tr>
				
				</table>
				
		</td>";
				}
				echo"</tr>
		</table>	
		</td></tr>
		</table>";
		
	}

	elseif($modul_part == "configuration"){
		echo"<table cellspacing='0' cellpadding='3' border='0' width='600'>
		<tr>
			<td class='buttontd'  style='text-align:left'><span class='gras'>$part</span></td>
		</tr>
		<tr><td valign='top' class='cadrebas' align='left'>";
	if($part=="signature"){
		if(isset($_POST['signature'])){
			$signat = stripslashes($_POST['signature']);
			if(set_pref('mails.signture',$signat)){
				$signa=$signat;
				$return.=returnn("votre signature a bien été enregistrée","009900",$vers,$theme);
			}
			else{
				$return.=returnn("erreur lors de l'enregistrement de votre signature","990000",$vers,$theme);
			}
			fclose($fp);
		}	
			echo"
			<hr>
			<b>modifier ma signature</b><br>
			<form action='./?option=$option&part=$part' method='post'>
			<textarea name='signature' cols='80' rows='10'>$signa</textarea><br>
			<input type='submit' value='enregistrer' class='buttontd'>
			</form>
			";
			 		
		
	}
	if($part=="comptes"){
				echo"<table cellspacing='0' cellpadding='3' border='0' width='580'><tr>
				<td valign='top'>";
			$res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') ORDER BY `nom`");
			while($ro = mysql_fetch_object($res)){
				$b_nom = $ro->nom;
				$b_id = $ro->id;
				echo"<a href='./?option=$option&part=$part&b=$b_id'>- $b_nom</a><br>";
				
			}
			echo"
			<br><a href='./?option=mail&part=comptes&b'>- Nouveau</a><br>
			</td><td valign='top'>";
			if(isset($_GET['b'])){
				$b = $_GET['b'];
				if(is_numeric($b)){				
					$res = mysql_query("SELECT * FROM `$mail_base` WHERE `id`='$b'");
					$ro = mysql_fetch_object($res);
					$serveur = $ro->serveur;
					$port = $ro->port;
					$dossier = $ro->dossier;
					$adresse = $ro->adresse;
					$login = $ro->login;
					$nom = $ro->nom;
					$pass = $ro->pass;
					$only = $ro->only;
					$action="update&edit=$b";
					$vali="<a href='./?option=$option&part=$part&del=$b' class='buttontd'>Supprimer</a>
					<input type='submit' value='Enregistrer' class='buttontd'>
					";
				}
				else{
					$dossier='/pop3';
					$nom='nouveau compte';
					$adresse='email@serveur.ext';
					$serveur='serveur.ext';
					$port='110';
					$login='login';
					$pass='******';
					$action='add';
					$only = 0;
					$vali="<input type='submit' value='Ajouter' class='buttontd'>";
				}
				echo"
				<form action='./?option=$option&part=$part&$action' method='post' name='fourme'>
				<input type='hidden' name='active' value='1'>
				<select name='dossier' onchange=\"if(this.value=='/pop3'){document.fourme.port.value='110';}if(this.value=='/pop3/ssl/novalidate-cert'){document.fourme.port.value='995';}if(this.value=='/imap/notls'){document.fourme.port.value='143';}\">
				<option value='/pop3'>POP3</option>
				<option value='/pop3/ssl/novalidate-cert'>POP3 + SSL</option>
				<option value='/imap/notls'>IMAP</option>
				</select><br>
				Nom du compte :  <input type='text' name='nom' value='$nom'><br>
				Adresse de messagerie :  <input type='text' name='adresse' value='$adresse' onchange=\"if(document.fourme.login.value=='login' || document.fourme.login.value==''){document.fourme.login.value=this.value;}\"><br>
				Serveur de réception :  <input type='text' name='serveur' value='$serveur'><br>
				Port de réception :  <input type='text' name='port' value='$port' size='3'><br>
				Nom de connexion :  <input type='text' name='login' value='$login'><br>
				Mot de passe :  <input type='password' autocomplete='off' name='pass' value='$pass'><br>
				Cette boite sera visible pour
				<select name='only'>
				<option value='0'>Tous les utilisateurs</option>
				<option value='$u_id'>Seulement moi</option>
				</select><br>
				$vali
				</form>
				<script language='javascript'>
				document.fourme.dossier.value='$dossier';
				document.fourme.only.value='$only';
				</script>";
			}
			echo"
			</td></tr></table>
			";
			
		}
		
		
		
		echo"</td></tr>
		</table>";
	}
}

else{
	if(isset($_GET['mktb'])){	
		if(mysql_query("CREATE TABLE `$mail_base` (
			`id` bigint(20) NOT NULL auto_increment,
			`nom` varchar(255) NOT NULL default '',
			`adresse` varchar(255) NOT NULL default '',
			`serveur` varchar(255) NOT NULL default '',
			`port` int(3) NOT NULL default '0',
			`dossier` varchar(255) NOT NULL default '',
			`login` varchar(255) NOT NULL default '',
			`pass` varchar(255) NOT NULL default '',
			`only` bigint(20) NOT NULL default '0',
			`active` int(1) NOT NULL default '0',
  			PRIMARY KEY  (`id`)
)") ){
			$return.=returnn("La table <b>\"Mail\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour redémarrer <b>\"Mail\"</b></a>","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La table Mail n'a pu être créée correctement","990000",$vers,$theme);
		}
	}
	echo"	
	<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Mail</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'>

	Votre base de données n'est pas configurée avec une table <b>\"Mail\"</b>...<br><br>
	Voulez vous que <b>Adeli</b> essai de la créer automatiquement ?<br><br>
	<a href='./?option=$option&part=$part&mktb'>créer le tableau</a>
	
	</td></tr></table>
	";
}
?>