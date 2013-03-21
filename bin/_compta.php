<?php // 687 > Outils de comptabilité ;
$conn = connecte($base, $host, $login, $passe);
$_SESSION['pdf_base']=$base;
$_SESSION['pdf_$host']=$host;
$_SESSION['pdf_$login']=$login;
$_SESSION['pdf_$passe']=$passe;
$_SESSION['compta_base']=$compta_base;
//$_SESSION['treso_base']=$treso_base="adeli_compta_treso";

if($modul_part=='achat'){
	$defstat=$defstatl;
	$colorstatut=$colorstatutl;
}

if(isset($treso_base)){
if(mysql_query("SHOW COLUMNS FROM $treso_base")  ){
	
}
else{
	if(isset($_GET['mkctb'])){	
		if(mysql_query("CREATE TABLE `$treso_base` (
  `id` bigint(20) NOT NULL auto_increment,
  `ref` bigint(20) NOT NULL default '0',
  `type` int(1) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `montant` decimal(10,2) NOT NULL default '0.00',
  `note` varchar(255) NOT NULL default '',
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)") ){
			$return.=returnn("La table <b>\"Tr&eacute;sorerie\"</b> a &eacute;t&eacute; cr&eacute;&eacute;e correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour red&eacute;marrer <b>\"Compta\"</b></a>","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La table Tr&eacute;sorerie n'a pu être cr&eacute;&eacute;e correctement","990000",$vers,$theme);
		}
	}
	echo"	
	<table cellspacing='0' cellpadding='3' border='0' width='80%'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Compta</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'>

	Votre base de donn&eacute;es n'est pas configur&eacute;e avec une table <b>\"Tr&eacute;sorerie\"</b>...<br>
	Tr&eacute;sorerie sert notamment lorsque vous percevez plusieurs paiement pour une même facture et pour rendre le bilan comptable plus pr&eacute;cis.<br><br>
	<a href='./?option=$option&part=$part&mkctb'>cr&eacute;er le tableau</a><br>
	
	</td></tr></table><br>
	";
}


}





$menupart = array_keys($menu["vente"]);
$sortipart = array_keys($menu["achat"]);
$menupart = array_merge($menupart,$sortipart);

$clients_db='clients';
if($modul_part=="achat" && $part=="achat" && isset($fournisseurs_db)) $clients_db=$fournisseurs_db;

if(isset($compta_base) && mysql_query("SHOW COLUMNS FROM $compta_base")  ){

		$verifupdt = mysql_query("DESC `$compta_base`");
		$allchamps = array();
		while($ro = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$ro->Field);
			if($ro->Field=='mode' && substr($ro->Type,0,7)=='varchar'){
				mysql_query("ALTER TABLE `$compta_base` CHANGE `mode` `mode` TEXT NOT NULL");
			}
		}
		if(!in_array("expedition",$allchamps)){
			mysql_query("ALTER TABLE `$compta_base` ADD `expedition` varchar(255) NOT NULL default ''");
		}
		if(!in_array("devise",$allchamps)){
			mysql_query("ALTER TABLE `$compta_base` ADD `devise` varchar(5) NOT NULL default ''");
		}
		if(!in_array("numero",$allchamps)){
			mysql_query("ALTER TABLE `$compta_base` ADD `numero` INT NOT NULL AFTER `id`");
		}
		if(!in_array("active",$allchamps)){
			mysql_query("ALTER TABLE `$compta_base` ADD `active` INT( 1 ) NOT NULL");
		}
		
		
		////////////////////////// REMPLIR NUMEROS
	$res = mysql_query("SELECT `numero` FROM `$compta_base` WHERE `numero`=0 AND `type`='$part'");
	if($res && mysql_num_rows($res)>0){
		$res = mysql_query("SELECT `id` FROM `$compta_base` WHERE `numero`=0 AND `type`='$part' ORDER BY `id`");
		$n=getnext($compta_base,'numero',"WHERE `type`='$part'");
		while ($ro = mysql_fetch_object($res)) {
			mysql_query("UPDATE `$compta_base` SET `numero`=$n WHERE `id`=".($ro->id)."");
			$n++;
		}
	}
	
eval(get_pref("compta.conf","x"));

if(!isset($taxe)){
	$taxe='HT';
}
if(!isset($message_exp)){
	$message_exp="Votre colis viens d'être exp&eacute;di&eacute; :\n\nr&eacute;f&eacute;rence: <EXP>";
}
$wheredbplus='';



if(!isset($_SESSION['affdesac'])){
	$_SESSION['affdesac']=0;
}
if(isset($_GET['affdesac'])){
	$_SESSION['affdesac']=$_GET['affdesac'];
}
if(!isset($_SESSION['affstat'])){
	$_SESSION['affstat']=-1;
}
if(isset($_GET['affstat'])){
	$_SESSION['affstat']=$_GET['affstat'];
}
if($_SESSION['affstat']>-1){
	$wheredbplus.=" AND `etat`='".$_SESSION['affstat']."'";
}
$affdesac=$_SESSION['affdesac'];
 if($affdesac==0){
			$actouf="<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=1'\">";
		}
		else{
			$actouf="<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=0'\" checked>";
			$wheredbplus.="AND `active`=1";
		}
		

 $wheredb="WHERE `type`='$part'";
 if($part=='bilan' || $part=='statistiques'){
		$wheredb=''; 
 }
 if($part=='bdl'){
 	 $wheredb="WHERE `type`='facture' AND `etat`!=4 ";
 }
 elseif(isset($wheredbplus)){
 	$wheredb.=" $wheredbplus";
 }
 

$cliid='';
$firescom='';
$rescli='';
$res_field = mysql_list_fields($base,'clients');
$columns = mysql_num_fields($res_field);
$res = mysql_query("SELECT `depend` FROM `adeli_groupe`");
if($res && mysql_num_rows($res)>0){
	while($ro = mysql_fetch_array($res)){
	  $rdpend = explode(':',$ro[0]);
	  for ($i = 0; $i < $columns; $i++) {
		  $field_name = mysql_field_name($res_field, $i);
		  $field_act = $field_name;
		  if(isset($r_alias['clients'][$field_name])){
			  $field_act = $r_alias['clients'][$field_name];
		  }
		  if($rdpend[1]!='clients' && $field_act == "$rdpend[1]_$rdpend[2]_$rdpend[3]"){
			  if($u_d!=NULL) $rescli.=" AND `$field_name`='$u_d'";
			  $firescom = $field_name;
			  break 2;
		  }
	  }	
	}
}

if(isset($u_restreint) && $u_restreint[1]!='clients'){
  if($rescli!=''){
		$res = mysql_query("SELECT `id` FROM `clients` WHERE 1 $rescli");
		if($res && mysql_num_rows($res)>0){
			$wheredb.=" AND( `client`='0'";
			$cliid.=" AND( `id`='0'";
			while($ro=mysql_fetch_array($res)){
				$wheredb.=" OR `client`='$ro[0]' ";
				$cliid.=" OR `id`='$ro[0]' ";
			}
			$wheredb.=")";
			$cliid.=")";
		}
  }
}
 
insert('_graphique');
insert('_compta_pdf');
insert('fpdf');
if(is_file('bin/_compta_pdf.php')){
	$openpdf='./?incpath=bin/_compta_pdf.php&1';
}
else{
	$openpdf="$style_url/update.php?file=_compta_pdf.php?1";
	//include("$style_url/update.php?file=$incfich.php");
}

$tabledb = $compta_base;
if(isset($_GET['setvalid'])){
	if($u_droits == ''){
		$setvalid = $_GET['setvalid'];
		if( mysql_query("UPDATE `$tabledb` SET `active`='1' WHERE id='$setvalid'") ){
			$return.=returnn("validation de $unsetvalid@$part effectu&eacute;e avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("la validation de $unsetvalid@$part a &eacute;chou&eacute;e","990000",$vers,$theme);
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
			$return.=returnn("d&eacute;validation de $unsetvalid@$part effectu&eacute;e avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("la d&eacute;validation de $unsetvalid@$part a &eacute;chou&eacute;e","990000",$vers,$theme);
		}
	}
	else{
		$return.=returnn("Vous n'avez pas les droits pour d&eacute;valider ce document","990000",$vers,$theme);
	}
}
if(isset($_GET['multi'])){
	$wereid="id=0 ";
	foreach($_POST as $keyname=>$value) {
		$tid=substr($keyname,3,strlen($keyname));
		$wereid.=" OR `id`='$tid'";
	}
	switch($_GET['multi']){
		case 'active': 
			if(mysql_query("UPDATE `$compta_base` SET `active`=1 WHERE $wereid")){
				$return.=returnn("multi-activation effectu&eacute;e avec succès","009900",$vers,$theme);
			} 
			else{
				$return.=returnn("la multi-activation a &eacute;chou&eacute;e","990000",$vers,$theme);
			}
		break;
		case 'desactive': 
			if(mysql_query("UPDATE `$compta_base` SET `active`=0 WHERE $wereid")){
				$return.=returnn("multi-d&eacute;sactivation effectu&eacute;e avec succès","009900",$vers,$theme);
			} 
			else{
				$return.=returnn("la multi-d&eacute;sactivation a &eacute;chou&eacute;e","990000",$vers,$theme);
			}
		break;
	}
}
/*********************************************************************************************************************

								E D I T I O N

***********************************************************************************************************************/	

if(isset($_GET['edit']) && $part!='livraison'){	
	insert('_compta_edition');
	if(is_file("bin/_compta_edition.php")){
		include("bin/_compta_edition.php");
	}
	else{
		include("$style_url/update.php?file=_compta_edition.php");
	}	
}	
/*********************************************************************************************************************

								L I S T E

***********************************************************************************************************************/	
elseif($modul_part=="vente" || ($modul_part=="achat" && $part=="achat") ){

echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>$part</span></td>
		<td class='buttontd' colspan='2' style='text-align:right'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='3' align='center'>
	<script language='javascript'>
	function sela(k){
		var allche = document.listage.getElementsByTagName(\"input\");
		for (var i=0; i<allche.length; i++) {
			allche[i].checked=k;
		}
	}
	</script>
	<form name='listage' action='./?option=$option&part=$part' method='post'>
	<table width='100%' cellspacing='1' cellpadding='0' border='0'><tr>
	<td align='left'><input type='checkbox' onclick='sela(this.checked)'>
	 -
	<a href='#' onclick=\"document.listage.action+='&edit&freecontent&merge';document.listage.submit()\" class='buttontd'>fusionner</a>
	<a href='#' onclick=\"document.listage.action+='&multi=active';document.listage.submit()\" class='buttontd'>activer</a>
	<a href='#' onclick=\"document.listage.action+='&multi=desactive';document.listage.submit()\" class='buttontd'>d&eacute;sactiver</a>
	&nbsp;&nbsp;
	<a class='buttontd' href='./?option=$option&part=$part&edit&freecontent'>Nouveau</a>
	</td>
	<td align='right'>
	
	Trier :
	<select name='affstat' onchange=\"document.location='./?option=$option&part=$part&affstat='+this.value;\">
	<option value='-1'>tout</option>";
	for($s=0; $s<sizeof($defstat) ; $s++){
		echo"<option value='$s'>$defstat[$s]</option>";
	}
	echo"</select>
	
	Actifs uniquement $actouf
	<script language='javascript'>
	document.listage.affstat.value='".$_SESSION['affstat']."';
	</script>
	</td>
	</tr></table>
	
";
	$tabledb = $compta_base;
	insert("inc_cliste");
	if(is_file("bin/inc_cliste.php")){
		include("bin/inc_cliste.php");
	}
	else{
		include("$style_url/inc_cliste.php");
	}
	echo"</form>";
	//include("$style_url/inc_cliste.php?x_id=$x_id&$query");
}
elseif($modul_part=="achat"){
/*********************************************************************************************************************

								F R A I S

***********************************************************************************************************************/	
	if($part=="livraison"){
		insert('_compta_livraison');
		if(is_file("bin/_compta_livraison.php")){
			include("bin/_compta_livraison.php");
		}
		else{
			include("$style_url/update.php?file=_compta_livraison.php");
		}
	}
	if($part=="frais"){
		echo"<table cellspacing='0' cellpadding='3' border='0' width='600'>
		<tr>
			<td valign='top' class='menuselected' width='150'><span class='gras'>$part</span></td>
			<td class='buttontd' style='text-align:left'>&nbsp;<td>
		</tr>
		<tr><td valign='top' class='cadrebas' colspan='2' align='left'>
		
		En cours de d&eacute;veloppement
		
		
		</td></tr></table>
		
		";
	}
}
elseif($modul_part=="outils"){
	echo"";
/*********************************************************************************************************************

								S T A T S

***********************************************************************************************************************/	
if($part=="statistiques"){
	insert('_compta_stats');
	if(is_file("bin/_compta_stats.php")){
		include("bin/_compta_stats.php");
	}
	else{
		include("$style_url/update.php?file=_compta_stats.php");
	}	
}
/*********************************************************************************************************************

								B I L A N

***********************************************************************************************************************/
elseif($part=="bilan"){
	insert('_compta_bilan');
	if(is_file("bin/_compta_bilan.php")){
		include("bin/_compta_bilan.php");
	}
	else{
		include("$style_url/update.php?file=_compta_bilan.php");
	}
}
/*********************************************************************************************************************

								R E G L A G E S

***********************************************************************************************************************/
	elseif($part=="reglages"){
	
	echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>$part</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'>";
	
	if($u_droits == '' || $u_dgw == 1 ){
		if(isset($_GET["mod"])){
			$nom=stripslashes($_POST["nom"]);
			$url=stripslashes($_POST["url"]);
			$url_cgv=stripslashes($_POST["url_cgv"]);
			$reference=stripslashes($_POST["reference"]);
			$note_devis=stripslashes($_POST["note_devis"]);
			$note_commande=stripslashes($_POST["note_commande"]);
			$note_facture=stripslashes($_POST["note_facture"]);
			$taxe=stripslashes($_POST["taxe"]);
			$bordereau=stripslashes($_POST["bordereau"]);
			$message_exp=stripslashes($_POST["message_exp"]);
			$remise_app=stripslashes($_POST["remise_app"]);
			$pay_adresse = stripslashes($_POST["pay_adresse"]);
			if(set_pref('compta.conf','
$nom="'.str_replace('"','\"',$nom).'";
$url="'.str_replace('"','\"',$url).'";
$url_cgv="'.str_replace('"','\"',$url_cgv).'";
$reference="'.str_replace('"','\"',$reference).'";
$note_devis="'.str_replace('"','\"',$note_devis).'";
$note_commande="'.str_replace('"','\"',$note_commande).'";
$note_facture="'.str_replace('"','\"',$note_facture).'";
$taxe="'.str_replace('"','\"',$taxe).'";
$bordereau="'.str_replace('"','\"',$bordereau).'";
$message_exp="'.str_replace('"','\"',$message_exp).'";
$remise_app="'.str_replace('"','\"',$remise_app).'";
$pay_adresse="'.str_replace('"','\"',$pay_adresse).'";
			',"x")){
				$return.=returnn("enregistrement effectu&eacute; avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("enregistrement &eacute;chou&eacute;","990000",$vers,$theme);
			}
			fclose($fp);
			if($_FILES['file']['name'][0] !=''){
				if(addfile("mconfig", "compta.jpg", $_FILES['file']['tmp_name'][0], $dangerous)){
					$return.=returnn("bandeau charg&eacute; avec succès","009900",$vers,$theme);
				}
				else{
					$return.=returnn("le bandeau n'a pu être charg&eacute; correctement","990000",$vers,$theme);
				}
			}
			if($_FILES['file']['name'][1] !=''){
				if(addfile("mconfig", "compta_t.jpg", $_FILES['file']['tmp_name'][1], $dangerous)){
					$return.=returnn("totem charg&eacute; avec succès","009900",$vers,$theme);
				}
				else{
					$return.=returnn("le totem n'a pu être charg&eacute; correctement","990000",$vers,$theme);
				}
			}
		}
		if(get_pref('compta.conf',"x")==''){
			$nom='';
			$url="http://".getenv("SERVER_NAME");
			$url_cgv='';
			$reference='';	
			$note_devis='';
			$note_commande='';
			$note_facture='';
			$taxe='';
			$bordereau='';	
			$message_exp='';
			$remise_app=0;
			$pay_adresse='';
		}
		else{
			eval(get_pref("compta.conf","x"));
		}	
		$imb="";
		if(is_file("mconfig/compta.jpg")){
			$imb = "<img src='mconfig/compta.jpg' alt='bandeau' width='595'><p align='right'>
			 <a href=\"#\" onclick=\"delfile('mconfig/compta.jpg')\"><img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'> supprimer</a>";
		}
		$imc="";
		if(is_file("mconfig/compta_t.jpg")){
			$imc = "<img src='mconfig/compta_t.jpg' alt='totem'  width='300' height='143'><p align='right'>
			 <a href=\"#\" onclick=\"delfile('mconfig/compta_t.jpg')\"><img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'> supprimer</a>";
		}	
		echo"
		<form action='./?option=$option&part=$part&mod' method='post' enctype='multipart/form-data' name='farme'>
		<table>
		<tr><td>Bandeau</td><td>$imb<br>image (jpg) 21x3,8cm ou 595x108 px
		<input type='file' name='file[]'><hr></td></tr>
		<tr><td>Entête</td><td><textarea name='nom' style='width:300px;height:108px'>$nom</textarea></td></tr>
		<tr><td>Adresse du site</td><td><input type='text' name='url' value='$url' style='width:300px;'></td></tr>
		<tr><td>Adresse des conditions g&eacute;n&eacute;rales de vente</td><td><input type='text' name='url_cgv' value='$url_cgv' style='width:300px;'></td></tr>
		<tr><td>Totem</td><td>$imc<br>image (jpg) 300x143 px
		<input type='file' name='file[]'><hr></td></tr>
		<tr><td>Adresse de paiement : (bordereau)</td><td><textarea name='pay_adresse' style='width:300px;height:30px' onfocus='this.style.height=120' onblur='this.style.height=30'>$pay_adresse</textarea></td></tr>
		<tr><td>R&eacute;f&eacute;rences : (pied de page)</td><td><textarea name='reference' style='width:300px;height:30px' onfocus='this.style.height=120' onblur='this.style.height=30'>$reference</textarea></td></tr>
		
		<tr><td colspan='2' align='center'><hr>Pr&eacute;f&eacute;rences</td></tr>
		
		<tr><td>Document exprim&eacute;</td><td><select name='taxe'><option value='HT'>HT</option><option value='TTC'>TTC</option><option value='NS'>Non soumis à la TVA</option></select>
		</td></tr>
		<tr><td>Format de document</td><td><select name='bordereau'><option value=''>Avec bordereau de paiement</option><option value='sans'>Sans bordereau</option></select>
		</td></tr>
		<tr><td>Appliquer les remises</td><td><select name='remise_app'><option value='0'>Sur le total</option><option value='1'>A la pièce</option></select>
		<script language='javascript'>
			document.farme.taxe.value='$taxe';
			document.farme.bordereau.value='$bordereau';
			document.farme.remise_app.value='$remise_app';
		</script>
		</td></tr>
		
		<tr><td colspan='2' align='center'><hr>Textes pr&eacute;format&eacute;s</td></tr>
		<tr><td>Note pour devis : </td><td><textarea name='note_devis' style='width:300px;height:60px' onfocus='this.style.height=120' onblur='this.style.height=60'>$note_devis</textarea></td></tr>
		<tr><td>Note pour commandes : </td><td><textarea name='note_commande' style='width:300px;height:60px' onfocus='this.style.height=120' onblur='this.style.height=60'>$note_commande</textarea></td></tr>
		<tr><td>Note pour factures : </td><td><textarea name='note_facture' style='width:300px;height:60px' onfocus='this.style.height=120' onblur='this.style.height=60'>$note_facture</textarea></td></tr>
		
		<tr><td>Alerte d'exp&eacute;dition :<br><font size='1'>La chaine \"&lt;EXP&gt;\" sera remplac&eacute; par la valeur du champ d'exp&eacute;dition dans votre message</font></td><td><textarea name='message_exp' style='width:300px;height:60px' onfocus='this.style.height=120' onblur='this.style.height=60'>$message_exp</textarea></td></tr>
		
		<tr><td colspan='2' align='right'><input type='submit' class='buttontd' value='Enregistrer'>";
		}
		else{
			echo"Vous n'avez pas accès a cette fonctionnalité";	
		}
		echo"</td></tr>
		</table>
		</form>
		";
		
	}
	else{
		echo"$modul_part $part";
	}
	
}

else{
	echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Tableau de bord</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadre' colspan='2' align='left'>
	
	<table cellpadding='20'><tr>
	<td valign='top' align='left'>
	<span class='textegrasfonce'>Vente</span><br><br>
	<table>";
	
	foreach($menu["vente"] as $formula){
		if($formula!=''){
			$res= mysql_query("SELECT `id` FROM `$compta_base` WHERE `type`='$formula'");
			$nbres = mysql_num_rows($res);
			echo"<tr><td>- <a href='./?option=$option&$formula'><span class='gras'>".ucfirst($formula)."</span></a></td>
			<td><span class='petittext'>($nbres)</span></td>
			<td><a href='./?option=$option&part=$formula&edit&freecontent'><img src='$style_url/$theme/+.png' alt='+' border='none'></a></td>
			";
		}
 	}
	echo"</table>
	</td>
	
	<td valign='top' align='left'>
	<span class='textegrasfonce'>Achat</span><br><br>
	<table>";
	
	foreach($menu["achat"] as $formula){
		if($formula!=''){
			$res= mysql_query("SELECT `id` FROM `$compta_base` WHERE `type`='$formula'");
			$nbres = mysql_num_rows($res);
			echo"<tr><td>- <a href='./?option=$option&$formula'><span class='gras'>".ucfirst($formula)."</span></a></td>
			<td><span class='petittext'>($nbres)</span></td>
			<td><a href='./?option=$option&part=$formula&edit&freecontent'><img src='$style_url/$theme/+.png' alt='+' border='none'></a></td>
			";
		}
 	}
	echo"</table>
	</td>
	
	
	<td valign='top' align='left'>
	<span class='textegrasfonce'>Gestion</span><br>
	<br> - <a href='./?option=$option&statistiques'><span class='gras'>Statistiques</span></a>
	<br><span class='petittext'>Aperçu du registre</span><br>
	
	<br> - <a href='./?option=$option&bilan'><span class='gras'>Bilan</span></a><br>
	<span class='petittext'>Bilan par p&eacute;riode</span><br>
	
	<br> - <a href='./?option=$option&reglages'><span class='gras'>R&eacute;glages</span></a><br>
	<span class='petittext'>Param&eacute;trez vos documents, entêtes, pieds de page...</span>
	
	
	</td></tr></table>
	";
	$tab="";
	for($i=7 ; $i>0 ; $i--){
		$dat = date('m/Y',strtotime("-$i months"));
		$tab.=",$dat";			
	}
	$tab.=";";
	$topy=0;
	foreach($menu["vente"] as $formula){
		if($formula!=''){
			$tab.="$formula";			
			for($i=7 ; $i>0 ; $i--){
				$dat = date('Ym',strtotime("-$i month"));
				$res= mysql_query("SELECT SUM( `montant` ) FROM `$compta_base` WHERE `type`='$formula' AND EXTRACT(YEAR_MONTH FROM(`date`))='$dat'");
				$ro = mysql_fetch_array($res);
				$tab.=",$ro[0]";
				if($ro[0] > $topy) $topy=$ro[0];
			}
			$tab.=";";
		}
 	}
	if($debit==0) echo"<img src='bin/_graphique.php?type=courbe&top=$topy&tab=$tab' class='cadre'/>";
	else echo"<img src='bin/_graphique.php?type=courbe&top=$topy&tab=$tab&width=240' class='cadre'/>";
}
echo" 


</td></tr></table>";

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
elseif(isset($compta_base)){
	if(isset($_GET['mktb'])){	
		if(mysql_query("CREATE TABLE `$compta_base` (
  `id` bigint(20) NOT NULL auto_increment,
  `code` char(20) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `client` varchar(255) NOT NULL default '',
  `adresse` text NOT NULL,
  `intitule` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `montant` decimal(10,2) NOT NULL default '0.00',
  `acompte` decimal(10,2) NOT NULL default '0.00',
  `acomptele` date NOT NULL default '0000-00-00',
  `etat` int(1) NOT NULL default '0',
  `mode` varchar(255) NOT NULL default '',
  `expedition` varchar(255) NOT NULL default '',
  `clon` bigint(20) NOT NULL default '0',
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)") ){
			$return.=returnn("La table <b>\"Compta\"</b> a &eacute;t&eacute; cr&eacute;&eacute;e correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour red&eacute;marrer <b>\"Compta\"</b></a>","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La table compta n'a pu être cr&eacute;&eacute;e correctement","990000",$vers,$theme);
		}
	}
	echo"	
	<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Compta</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'>

	Votre base de donn&eacute;es n'est pas configur&eacute;e avec une table <b>\"Compta\"</b>...<br><br>
	Voulez vous que <b>Adeli</b> essai de la cr&eacute;er automatiquement ?<br><br>
	<a href='./?option=$option&part=$part&mktb'>cr&eacute;er le tableau</a>
	
	</td></tr></table>
	";
}
else{
	echo"	
	<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Compta</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'>
	<b>\"Compta\"</b> ne peut être install&eacute; sur votre plateforme <b>Adeli</b><br><br>
	
	</td></tr></table>
	";
}
@mysql_close($conn);
?>