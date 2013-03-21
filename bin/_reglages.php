<?php // 49 > Réglages d'Adeli ;
$menuitem = array("login","last","email","droits");
$menutranslate = array(
	"login"=>"utilisateur",
	"last"=>"dernière connexion",
	"email"=>"email",
	"droits"=>"droits"
);
$menuitemg = array("nom","nombre","droits");
$menutranslateg = array(
	"nom"=>"nom du groupe",
	"nombre"=>"nombre d'utilisateurs",
	"droits"=>"droits"
);
$conn = connecte($base, $host, $login, $passe);
if(mysql_query("SHOW COLUMNS FROM `adeli_users`")  ){
///////////////////////////////////////////////////////// UPDATE MODIF
if(isset($_GET['modif']) && isset($_POST['login'])){
	$login = str_replace("'","''",stripslashes($_POST['login']));
	$nom = str_replace("'","''",stripslashes($_POST['nom']));
	$pass0 = stripslashes($_POST['pass0']);
	$pass1 = stripslashes($_POST['pass1']);
	$pass2 = stripslashes($_POST['pass2']);
	$email = stripslashes($_POST['email']);
	if($pass1 == ""){
		$res = mysql_query("SELECT * FROM `adeli_users` WHERE `login`='$login' AND `id`!='$u_id'");
		if(mysql_num_rows($res)==0){
			if(mysql_query("UPDATE `adeli_users` SET `nom`='$nom',`login`='$login',`email`='$email' WHERE `id`='$u_id'")){
				$u_login=stripslashes($_POST['login']);
				$u_nom=stripslashes($_POST['nom']);
				$u_email=stripslashes($_POST['email']);
				$return.=returnn("modification effectuée avec succès","009900");
			}
			else{
				$return.=returnn("modification échouée","990000");
			}	
		}
		else{
			$return.=returnn("Cet utilisateur existe déjà !","FF9900");
		}
	}
	else{	
	
		  if( isset($pass_sql_encode) && in_array('adeli_users',$pass_sql_encode)){
			 $res = mysql_query("SELECT PASSWORD('$pass0')");
			 $ro = mysql_fetch_array($res);
			 $pass0=$ro[0];
		  }
		if($pass0 == $u_pass){
			if($pass1 == $pass2){
				if( isset($pass_sql_encode) && in_array('adeli_users',$pass_sql_encode)){
					$pass1="PASSWORD('$pass1') ";
				}
				else{
					$pass1 = "'$pass1'";	
				}
				if(mysql_query("UPDATE `adeli_users` SET `nom`='$nom',`login`='$login',`email`='$email',`pass`=$pass1 WHERE `id`='$u_id'")){
					$u_nom=stripslashes($_POST['nom']);
					$u_login=stripslashes($_POST['login']);
					$u_email=stripslashes($_POST['email']);
					$return.=returnn("modification effectuée avec succès","009900");
				}
				else{
					$return.=returnn("modification échouée","990000");
				}
			}
			else{
				$return.=returnn("Vous avez entré 2 nouveaux mots de passe différents","990000");
			}
		}
		else{
			$return.=returnn("Votre ancien mot de passe est erroné","990000");
		}
	}
}
///////////////////////////////////////////////////////// SET ALERT
if(isset($_GET['setalert'])){
	$alerte = $_POST['alerte'];
	if(set_pref('r_alerte.conf',$alerte,'x')){
		$return.=returnn("enregistrement effectuée avec succès","009900");
		$_SESSION["r_alerte"]=$alerte;
	}
	else{
		$return.=returnn("enregistrement échoué ($alerte)","990000");
	}
}
///////////////////////////////////////////////////////// SET PRiNT ALERT
if(isset($_GET['alerto'])){
	$alertprintmode = $_GET['alerto'];
	if(set_pref('printalert.conf',$alertprintmode,'x')){
		$return.=returnn("enregistrement effectuée avec succès","009900");
	}
	else{
		$return.=returnn("enregistrement échoué ($alerte)","990000");
	}
}
///////////////////////////////////////////////////////// PERSONNALISATION



	$order="login";
	if(isset($_GET['order'])){
		$order = $_GET['order'];
	}
	
if($part != ""){
	
	if($part == "adeli_rss"){
		insert("_rss");
		if(is_file("bin/_rss.php")){
			$openrss="bin/_rss.php";
		}
		else{
			$openrss="$style_url/update.php?file=_rss.php?1";
		}
		include($openrss);
	}
	if($part == "secure"){
		insert("_secure");
		if(is_file("bin/_secure.php")){
			$opensecure="bin/_secure.php";
		}
		else{
			$opensecure="$style_url/update.php?file=_secure.php?1";
		}
		include($opensecure);
	}
	if($part == "maj"){
		insert("_maj");
		if(is_file("bin/_maj.php")){
			$openmaj="bin/_maj.php";
		}
		else{
			$openmaj="$style_url/update.php?file=_maj.php?1";
		}
		include($openmaj);
	}
	if($part == "logs"){
		insert("_logs");
		if(is_file("bin/_logs.php")){
			$openmaj="bin/_logs.php";
		}
		else{
			$openmaj="$style_url/update.php?file=_logs.php?1";
		}
		include($openmaj);
	}
	
	if($part == "compte"){
		echo"
		<form action='./?option=$option&part=$part&modif' method='post' name='artos'>
			<table cellspacing='0' cellpadding='2' class='cadrebas'>
			<tr><td class='buttontd'>Mon compte</td></tr>		
			<tr><td>		
			<table>
				<tr><td>Nom</td><td><input type='text' name='nom' value='$u_nom' style='width:240px;font-size:10px;'></td></tr>
				<tr><td>Login</td><td><input type='text' name='login' value='$u_login' style='width:240px;font-size:10px;'></td></tr>
				<tr><td>Ancien mot de passe</td><td><input type='password' name='pass0' value='' style='width:140px;font-size:10px;'></td></tr>
				<tr><td>Nouveau mot de passe</td><td><input type='password' name='pass1' value='' style='width:140px;font-size:10px;'></td></tr>
				<tr><td>Confirmer le nouveau de mot de passe</td><td><input type='password' name='pass2' value='' style='width:140px;font-size:10px;'></td></tr>
				<tr><td>email</td><td><input type='text' name='email' value='$u_email' style='width:240px;font-size:10px;'></td></tr>
				<tr><td>Groupe</td><td>$u_gname</td></tr>		
				<tr><td colspan='2' align='right'>				
					<input class=\"buttontd\" type=\"reset\" value=\"rétablir\">
					<input class=\"buttontd\" type=\"submit\" value=\"enregistrer\">
				</td></tr>			
			</table>		
			</td></tr>
			</table>
		</form>
		";
	}
	if($part == "alerte"){
		echo"
		<form action='./?option=$option&part=$part&setalert' method='post' name='artos'>
			<table cellspacing='0' cellpadding='2' class='cadrebas'>
			<tr><td class='buttontd'>Alerte de mise à jour</td></tr>		
			<tr><td>";
			
			if($u_droits==""){
				echo"<table>
					<tr><td>
						Pour les éléments saisis, <br>une alerte doit être envoyée à<br><br>
					<input type='text' name='alerte' value='$r_alerte' style='width:240px;font-size:10px;'></td></tr>
					<tr><td align='right'>				
						<input class=\"buttontd\" type=\"reset\" value=\"rétablir\">
						<input class=\"buttontd\" type=\"submit\" value=\"enregistrer\">
					</td></tr>			
				</table>";
			}
			else{
				echo"<br>Pour les éléments saisis, <br>une alerte doit être envoyée à <b>$r_alerte</b><br>";
			}
			echo"		
			</td></tr>
			</table>
		</form>
		";
	}
	if($part == "personnalisation"){	
		/*<form action='./?option=$option&part=$part' method='get' name='artos' onsubmit='affichload()'>
		<input type='hidden' name='$part'>
		<input type='hidden' name='pers'>
			<table cellspacing='0' cellpadding='2' class='cadrebas' width='400'>
			<tr><td class='buttontd'>Thème</td></tr>		
			<tr><td>
			choisissez parmis les thèmes disponibles<br>
			</td></tr>
			<tr><td align='center'><select name='setheme'>";
				include("http://adeli.wac.fr/vers/$vers/getthemes.php");
				echo"</select>
				<script language='javascript'>
				document.artos.setheme.value='$theme';
				</script></td></tr>
				<tr><td align='right'>
				<br><input class=\"buttontd\" type=\"submit\" value=\"valider\">
				</td></tr>
			
			
					</table>
		</form>
		<br><br>
		
		*/
		
		echo"
		<form action='./?option=$option&part=$part' method='get' name='alertos'>
		<input type='hidden' name='$part'>
			<table cellspacing='0' cellpadding='2' class='cadrebas' width='400'>
			<tr><td class='buttontd'>Afficher les alertes</td></tr>		
			<tr><td>
				<select name='alerto'>
				<option value='1'>Affiher les alertes et confirmations</option>
				<option value='0'>Masquer les alertes et confirmations</option>
			</select>
			</blockquote>
			<script language='javascript'>
			document.alertos.alerto.value='$alertprintmode';
			</script>		
			</td></tr>
			<tr><td align='right'>				
						<input class=\"buttontd\" type=\"submit\" value=\"enregistrer\">
					</td></tr>
		</table>
		</form>
		<br><br>		
		<form action='./?option=$option&part=$part&pers' method='post' name='fourmis' enctype='multipart/form-data' onsubmit='affichload()'>
				<table cellspacing='0' cellpadding='2' class='cadrebas' width='400'>
			<tr><td class='buttontd'>Arrière plan</td></tr>		
			<tr><td>
			<br>
			<b>image d'arrière plan</b><br>";
		$val = trim(get_pref('bg.conf'));
		$bgmod = trim(get_pref('bgmod.conf'));
		echo"<input type='radio' name='bg' value='none'>
			Aucune<br><br>
			
			<input type='radio' name='bg' value='default' checked>
			Arrière plan par défault<br><br>
			
			<input type='radio' name='bg' value='mine'> Mon image (.jpg)
			<blockquote>
			<img src='mconfig/$u_id.bg.jpg' height='50' style='float:right'/><br>
			<input type='file' name='file[]' onclick=\"document.fourmis.bg[2].checked=true\"><br>
			Mode d'affichage :<br>
			<select name='mode'>
				<option value='no-repeat center center'>centrée</option>
				<option value='repeat'>répétée</option>
			</select>
			</blockquote>
			<script language='javascript'>
			// $val
			document.fourmis.bg[";
		if($val == "none") echo 0;
		elseif($val=="mine" && file_exists("mconfig/$u_id.bg.jpg")) echo 2;
		else echo 1;
			echo"].checked=true;
			document.artos2.mode.value='$bgmod';
			</script>";

			echo"<hr><b>couleur d'arrière plan</b><br>";
				
		$val = trim(get_pref('co.conf'));
		if($val==""){
			echo"<input type='radio' name='co' value='default' checked>par défaut<br><br>
			<input type='radio' name='co' value='mine'>personnalisée 		";
		}
		else{
			echo"
			<input type='radio' name='co' value='default'>par défaut
			<br><br>
			<input type='radio' name='co' value='mine' checked>personnalisée		
			";
		}
		echo"<input type=\"color\" name=\"couleur\" value=\"$val\" maxlength='6' size='6' onfocus='document.fourmis.co[1].checked=true' onchange=\"document.getElementById('divcouleur').style.backgroundColor='#'+this.value\">
							<div id=\"divcouleur\" style=\"background-color:#$val;padding:3px;position:relative;height:20px;width:30px;border-color:#000000;border-style:solid;border-width:1px\"></div>
							
							<a href='#acouleur' name='acouleur' onclick=\"choosecolor('','Backcolor','couleur','hexa',event);document.fourmis.co[1].checked=true\">changer la couleur</a>
							
				</td></tr>
				 <tr><td align='right'>				
						<input class=\"buttontd\" type=\"submit\" value=\"enregistrer\">
					</td></tr>
				
		
				</table>
			</form>";
	
	
	}
}
	else{
		echo"<table cellspacing='0' cellpadding='5' border='0' class='cadrebas' width='500'>
   <tr style='height:20px'><td class='buttontd' style='padding:10px'><b>Accueil Réglages</b></td></tr>
   <tr><td class='cadrebas' style='padding:10px'>
  <b>Personnaliser mon Adeli</b>
  <br>
  - <a href='./?option=$option&compte'>Gérer mon compte utilisateur</a>
  <br> 
  - <a href='./?option=$option&personnalisation'>Thèmes et fond d'écran</a>
  <br>
  - <a href='./?option=$option&adeli_rss'>Fils RSS</a>
  <br>
  <br>
  <b>Configurer Adeli</b>
  <br>
  - <a href='./?option=$option&maj'>Mises à jour</a>
  <br>
  - <a href='./?option=$option&logs'>Logs d'activité</a>
  <br>
  - <a href='./?option=$option&alerte'>Paramétrer les alertes</a>
  <br>
  - <a href='./?option=$option&secure'>Paramètres de sécurité</a>
  
  </td></tr></table>";
	}
}
else{
	if(isset($_GET['mktb'])){	
		if(mysql_query("CREATE TABLE `adeli_users` (
  `id` bigint(20) NOT NULL auto_increment,
  `login` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL default '',
  `last` datetime NOT NULL default '0000-00-00 00:00:00',
  `email` varchar(255) NOT NULL default '',
  `g` bigint(20) NOT NULL default '0',
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)") ){
		mysql_query("INSERT INTO `adeli_users` VALUES ($u_id, '".str_replace("'","''",$u_login)."', '".str_replace("'","''",$u_pass)."', '$mysqlnow', '".str_replace("'","''",$u_email)."', $u_g, 1);");
			$return.=returnn("La table <b>\"Utilisateurs\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour redémarrer <b>\"Reglages\"</b></a>","009900");
		}
		else{
			$return.=returnn("La table Utilisateurs n'a pu être créée correctement","990000");
		}
	}
	echo"	
	<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Reglages</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'>

	Votre base de données doit être reconfigurée avec une table <b>\"Utilisateurs\"</b>...<br><br>
	Voulez vous que <b>Adeli</b> essai de la créer automatiquement ?<br><br>
	<a href='./?option=$option&part=$part&mktb'>créer le tableau</a>
	
	</td></tr></table>
	";
}

?>