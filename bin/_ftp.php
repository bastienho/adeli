<!-- 14  -->

<?php
$conn = connecte($base, $host, $login, $passe);
if(isset($ftp_base) && mysql_query("SHOW COLUMNS FROM $ftp_base")  ){

$part="comptes"; 
$tabledb = $ftp_base;
	if(isset($_GET['setvalid'])){
		if($u_droits == ''){
			$setvalid = $_GET['setvalid'];
			if( mysql_query("UPDATE `$tabledb` SET `active`='1' WHERE id='$setvalid'") ){
				$return.=returnn("validation effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la validation a échouée","990000",$vers,$theme);
			}
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour valider ce document","990000",$vers,$theme);
		}
	}
	if(isset($_GET['unsetvalid'])){
		if($u_droits == ''){
			$unsetvalid = $_GET['unsetvalid'];
			if( mysql_query("UPDATE `$tabledb` SET `active`='0' WHERE id='$unsetvalid'") ){
				$return.=returnn("dévalidation effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la dévalidation a échouée","990000",$vers,$theme);
			}
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour dévalider ce document","990000",$vers,$theme);
		}
	}
	
if(isset($_GET['edit'])){

	$edit = $_GET['edit'];
	if(isset($_GET['new'])){
		$edit='';
	}			
	$action='add';
	if(abs($edit)>0 || isset($_GET['clone'])){
		$action="update";
	}
	
	$ftp_host = "";
	$ftp_login = "";
	$ftp_pass = "";
	$ftp_repertoire = "";
	echo"
	<form action='./?option=$option&part=$part&$action&edit=$edit' method='post' name='fourmis'>
	<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='buttontd' width='150'><a href='./?option=$option&part=$part'><span class='gras'>$part</span></a></td>		
	";
		
		
	if(abs($edit)>0){
		$result = mysql_query("SELECT * FROM $ftp_base WHERE id='$edit'");
		$row = mysql_fetch_object($result);
		$ftp_host = $row->host;
		$ftp_login = $row->login;
		$ftp_pass = $row->pass;
		$ftp_repertoire = $row->repertoire;
		$action="update";
		echo"<td class='buttontd' style='text-align:left'  width='150'><a href='./?option=$option&part=$part&edit'>nouveau</a></td>
		<td class='menuselected' style='text-align:left'  width='150'><a href='./?option=$option&part=$part&edit'>édition</a></td>";
	}
	else{
		echo"<td class='menuselected' style='text-align:left'  width='150'><a href='./?option=$option&part=$part&edit'>nouveau</a></td>
		<td class='buttontd' style='text-align:left' width='5'>&nbsp;</td>";
	}
	
		
		
	echo"	<td class='buttontd' style='text-align:left'>&nbsp;</td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='4' align='center'>
	
	<table>
		<tr><td>Hote</td><td><input type='text' name='host' value='$ftp_host' style='width:300px'></td></tr>
		<tr><td>utilisateur</td><td><input type='text' name='login' value='$ftp_login' style='width:300px'></td></tr>
		<tr><td>mot de passe</td><td><input type='password' name='pass' value='$ftp_pass' style='width:100px'></td></tr>
		<tr><td>répertoire</td><td><input type='text' name='repertoire' value='$ftp_repertoire' style='width:300px'></td></tr>
		<tr><td colspan='2' align='right'>
		<input class=\"buttontd\" type=\"button\" value=\"enregistrer et revenir\" onclick=\"document.fourmis.action='./?option=$option&part=$part&$action=$edit';document.fourmis.submit()\">
		<input class=\"buttontd\" type=\"button\" value=\"enregistrer et ajouter\" onclick=\"document.fourmis.action+='&new';document.fourmis.submit()\">
		<input class=\"buttontd\" type=\"submit\" value=\"enregistrer\">
		</td></tr>
	</table>";
	if(abs($edit)>0){
		echo"<br><br><br><hr>";
		$conn_id = ftp_connect($ftp_host);
		ftp_login($conn_id, $ftp_login, $ftp_pass);
		
		$file_list = ftp_rawlist($conn_id, $ftp_repertoire, true);
		if(is_array($file_list) && sizeof($file_list)>0){
			$return.=returnn("connection établie avec succès","FF9900",$vers,$theme);
			$objs = sizeof($file_list);
			$_SESSION["ftp_host"] = $ftp_host;
			$_SESSION["ftp_login"] = $ftp_login;
			$_SESSION["ftp_pass"] = $ftp_pass;
			$_SESSION["ftp_repertoire"] = $ftp_repertoire;
			echo"
			<b>$objs objets sur le serveur...</b>
			<br>
			<input class=\"buttontd\" type=\"button\" value=\"faire un backup maintenant\" onclick=\"javascript:open('./?incpath=_ftp.php&#63;mkbkp','backupftp','width=100,height=100')\">
			<br><br>";
		}
		else{
			$return.=returnn("la communication avec le serveur a échouée<br>veuillez vérifier les paramètres de connexion","990000",$vers,$theme);
		}
	}

	
	echo"	
	</td></tr></table>
	</form>
	";
	

}
else{
echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>$part</span></td>
		<td class='buttontd' style='text-align:left'  width='150'><a href='./?option=$option&part=$part&edit'>nouveau</a></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='3' align='center'>
";
	$tabledb = $ftp_base;
	include("$style_url/inc_liste.php?x_id=$x_id&$query");
	echo"
	</td></tr></table>
	";
}

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($ftp_base)){
	if(isset($_GET['mktb'])){	
		if(mysql_query("CREATE TABLE `$ftp_base` (
	  `id` bigint(20) NOT NULL auto_increment,
  `host` varchar(255) NOT NULL default '',
  `login` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL default '',
  `repertoire` varchar(255) NOT NULL default '',
  `clon` bigint(20) NOT NULL default '0',
		`active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)") ){
			$return.=returnn("La table <b>\"FTP\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour redémarrer <b>\"FTP\"</b></a>","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La table compta n'a pu être créée correctement","990000",$vers,$theme);
		}
	}
	echo"	
	<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>FTP</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'>

	Votre base de données n'est pas configurée avec une table <b>\"FTP\"</b>...<br><br>
	Voulez vous que <b>Adeli</b> essai de la créer automatiquement ?<br><br>
	<a href='./?option=$option&part=$part&mktb'>créer le tableau</a>
	
	</td></tr></table>
	";
}
else{
	echo"	
	<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>FTP</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'>
	<b>\"FTP\"</b> ne peut être installé sur votre plateforme <b>Adeli</b><br><br>
	
	</td></tr></table>
	";
}
mysql_close($conn);
?>