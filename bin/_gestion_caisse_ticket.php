<?php  // 24 > Ticket de caisse ;
session_name("adeli");
session_start();

if($_SESSION['u_id']!=0){
	//////////////////////////////////////////CAISSE
	
	
	$modechoix = array(
	"es"=>"espèces",
	"ch"=>"chèque",
	"cb"=>"carte bancaire",
	"pp"=>"Paypal",
	"ba"=>"bon d'achat"
	);
	
	
	$caisid = $_SESSION['caisid'];
	$totnumven = $_SESSION['totnumven'];
	$articles_db = 'gestion_articles';
	
	
	
	
	if(isset($_GET['mode'])){
		array_push($_SESSION["osier$caisid"]["mode"],array($_GET['mode'],"",0));
	}
	if(isset($_GET['mmode'])){
		$_SESSION["osier$caisid"]["mode"][$_GET['mmodeid']][$_GET['mmodeki']]=$_GET['mmodedif'];
	}
	if(isset($_GET['dmode'])){
		$_SESSION["osier$caisid"]["mode"][$_GET['dmode']]=null;
	}
	if(isset($_GET['rendu'])){
		$_SESSION["osier$caisid"]["rendu"]=$_GET['rendu'];
	}
	if(isset($_GET['delbox'])){//DEL
		$del = $_GET['delbox'];
		$maskbox = array($del=>$_SESSION["osier$caisid"]["content"][$del]);
		$newbox = array_diff_assoc($_SESSION["osier$caisid"]["content"], $maskbox);
		$_SESSION["osier$caisid"]["content"]=$newbox;
	}
	if(isset($_GET['delrem'])){//DEL
		$del = $_GET['delrem'];
		if($del=="pt" || $del=="pft"){
			$totremis = $_SESSION["osier$caisid"]["remise_type"]="";
			$_SESSION["osier$caisid"]["remise_num"]=0;
		}
		else{
			$_SESSION["osier$caisid"]["content"][$del][0] = "a";
		}
	}
	
	if(isset($_GET['red'])){// REM
		$red=$_GET['red'];
		$rem=$_GET['rem'];
		if($red=="pa"){
			$art = $_GET['art'];
			$_SESSION["osier$caisid"]["content"][$art][2] = $rem;
			$_SESSION["osier$caisid"]["content"][$art][0] = "t";				
		}
		if($red=="pfa"){
			$art = $_GET['art'];
			$_SESSION["osier$caisid"]["content"][$art][2] = $rem;
			$_SESSION["osier$caisid"]["content"][$art][0] = "f";
		}
		if($red=="pt"){
			$_SESSION["osier$caisid"]["remise_type"]="pt";
			$_SESSION["osier$caisid"]["remise_num"] = $rem;	
		}
		if($red=="pft"){
			$_SESSION["osier$caisid"]["remise_type"]="pft";
			$_SESSION["osier$caisid"]["remise_num"] = $rem;
		}
	}
	
	$keyvente = array_keys($_SESSION["ventes"]);
	
	$nbbox = sizeof($_SESSION["osier$caisid"]["content"]);
	$keybox = array_keys($_SESSION["osier$caisid"]["content"]);
	
	$stamp = date("d/m/Y - H:i:s");
	
	$type_remise = $_SESSION["osier$caisid"]["remise_type"];
	$num_remise = $_SESSION["osier$caisid"]["remise_num"];
	$caisid = $_SESSION['caisid'];
	if(isset($_GET['setclientpourcettevente'])){
		$_SESSION['clientidc'][$caisid] = $_GET['setclientpourcettevente'];
	}
	if(!isset($_SESSION['clientidc'][$caisid])){
		$_SESSION['clientidc'][$caisid]=0;
	}
	$clientname="client de passage";
	if($_SESSION['clientidc'][$caisid]!=0){
		$clientnameid=$_SESSION['clientidc'][$caisid];
		$result = mysql_query("SELECT * from clients WHERE id='$clientnameid'");
		$row = mysql_fetch_object($result);   
		$clientname = $row->prenom;
		$clientname .= ' '.$row->nom;
	}
	echo"
	<html>
	<head>
	<title>Ticket de caisse</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
	<link rel='stylesheet' href='style.css' type='text/css'>
	</head>
	<body style='background:none;'>
	$stamp<br>
	<b>$clientname</b>
	<script language='JavaScript'>
			fuck=0;
		</script>
		<form name='farme'>
	<select name='client' onchange=\"document.location='_gestion_caisse_ticket.php?setclientpourcettevente='+this.value;\">
	<option value='0'>changer</option>
	<option value='0'>client de passage</option>";
	$result = mysql_query("SELECT `id`,`nom` FROM clients ORDER BY `nom`");
		while($row = mysql_fetch_object($result)){   
			$clientname = $row->nom;
			$clientname .= ' '.$row->prenom;
			$clientnameid = $row->id;
			echo"<option value='$clientnameid'>$clientname</option>";
		}
	echo"
	</select>
	</farme><br>
	<font color='00FF00'>";
	if(isset($_GET['addwithid'])){
		$addid=$_GET['addwithid'];
		$ret = $_GET['t'];
		if($ret==1){
				$ret="r";
				$ar="r";
		}
		elseif($ret==""){
				$ret="a";
				$ar="";
		}
					if(array_key_exists("$ar$addid",$_SESSION["osier$caisid"]["content"])){
						$_SESSION["osier$caisid"]["content"]["$ar$addid"][1]++;
					}
					else{
						$_SESSION["osier$caisid"]["content"]["$ar$addid"][1] = 1;
						$_SESSION["osier$caisid"]["content"]["$ar$addid"][0] = $ret;
						$_SESSION["osier$caisid"]["content"]["$ar$addid"][2] = 0;					
					}
	}
	
	if(isset($_GET['annulvente'])){
		unset($_SESSION["osier$caisid"]);
		$maskventes = array($caisid=>$_SESSION['ventes'][$caisid]);
		$newventes = array_diff_assoc($_SESSION['ventes'], $maskventes);
		$_SESSION["ventes"]=$newventes;
		$_SESSION["caisid"] = $keyvente[sizeof($_SESSION["ventes"])-1];
		if(sizeof($_SESSION['ventes']) > 0){
		echo"vente annulée ... prochaine : $caisid<br>
		<script language='JavaScript'>
			parent.document.location='./?option=gestion&part=gestion_caisse';
		</script>";
		}
		else{
		echo"vente annulée<br>
		<script language='JavaScript'>
			parent.document.location='./?option=gestion&part=gestion_caisse&newvente';
		</script>";
		}
	}
	if(isset($_GET['bondachat'])){//BON D'ACHAT
		$addtobox=$_GET['bondachat'];
		$alpha = substr($addtobox,0,3);
		$num = substr($addtobox,3,14);
		$chires = mysql_query("SELECT * FROM bons WHERE alpha='$alpha' AND num='$num'");
		 if(mysql_num_rows($chires)==1){
				$chirow = mysql_fetch_object($chires);
				if($chirow->valid==1){
						$montant = $chirow->mont;
						array_push($_SESSION["osier$caisid"]["mode"],array("ba","$alpha$num",$montant));
				}
				else{
					echo"Bon d'achat déja utilisé !<br>";
				}
			}
			else{
				echo"Bon d'achat introuvable...<br>$alpha $num<br>";
			}
	}
	if(isset($_GET['ref'])){//ADD
		$addtobox=$_GET['ref'];
		//$ = mysql_query("SELECT * FROM artstock WHERE code='$addtobox' OR ean='$addtobox'");
		$req = urluntranslate(str_replace("'","''",$addtobox));
		$chires = mysql_query("SELECT 
		DISTINCT 
			`prix`,
			`tva`,
			`promo`,
			`rayon`,
			`nom`,
			`desc`,
			`plus1`,
			`plus2`,
			`gestion_artstock`.`code`,
			`gestion_artstock`.`ean`,
			`gestion_artstock`.`libre`,
			`gestion_artstock`.`taille`,
			`gestion_artstock`.`couleur`,
			`gestion_artstock`.`stock`
			`gestion_artstock`.`id`
		FROM 
			`$articles_db`,`gestion_artstock` ,`gestion_artrad` 
		WHERE 
			 `$articles_db`.`id`=`gestion_artrad`.`ref` 
			AND `$articles_db`.`active`=1 
			AND	(`nom`REGEXP'$req' OR `desc`REGEXP'$req' OR `plus1`REGEXP'$req' OR `plus2`REGEXP'$req' OR `gestion_artstock`.`code`REGEXP'$req' OR `gestion_artstock`.`code`LIKE'%$q%' OR `gestion_artstock`.`libre`REGEXP'$req' OR `gestion_artstock`.`ean`LIKE'%$q%') 
			AND	 `$articles_db`.`active`=1
			AND	 `nom`!=''
			AND `gestion_artstock`.`active`=1 
			AND `gestion_artstock`.`ref`=`$articles_db`.`id`
			AND `gestion_artstock`.`prix`>0");
		if(mysql_num_rows($chires) == 1){
			$chirow = mysql_fetch_array($chires);
			$addid = $chirow[14];
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["label"] = $_GET['ade'];
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["type"] = 'chiffre';
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["pu"] = $_GET['pu'];
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["quant"] = $_GET['numbo'];
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["tva"] = $_GET['tva'];
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["coderef"] = $_GET['ref'];
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["libre"]='';
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["hidden"]=$addid;
					$_SESSION["osier$caisid"]["content"]["$ar$addid"]["remise"]=0;
						echo"ajout d'un article<br>
						<script language='JavaScript'>
								parent.addcb();
							document.location='_gestion_caisse_ticket.php';
						</script>
						";
		}
		elseif(mysql_num_rows($chires) > 1){
			echo"<script language='JavaScript'>
								alert('Plusieurs choix pour cet article !');
				</script>
				<b>Plusieurs choix pour cet article:</b><ul>";
				
			while($ro = mysql_fetch_array($chires)){
					$addid = $chirow->id;
					$addcol = $chirow->couleur;
					$addtai = $chirow->taille;
					echo"<li><a href='_gestion_caisse_ticket.php?numbo=".$_GET['numbo']."&last=&last_art=&ade=$ro[5]&pu=$ro[0]+$ro[11]+$ro[12]&tva=$ro[1]&ref=$ro[8]'>$ro[0]+$ro[11]+$ro[12]</a></li>";
			}
			echo"</ul>";
		}
		else{
			echo"Cet article n'est pas dans la base de données !<br>";
		}
	}
	elseif(isset($_GET['numbo'])){
			$newquant = abs($_GET['numbo']);	
			if(isset($_GET['last_art'])){
				$forlui = $_GET['last_art'];
			}
			else{
				$forlui = $keybox[$nbbox-1];
			}
			//$forlui = $_SESSION["osier$caisid"]["content"][$forlui];
			$_SESSION["osier$caisid"]["content"][$forlui][1] = $newquant;
			echo"modification d'une quantité<br>
			<script language='JavaScript'>
			parent.effectnumber();
			</script>";
		}
		
		$nbbox = sizeof($_SESSION["osier$caisid"]["content"]);
		$keybox = array_keys($_SESSION["osier$caisid"]["content"]);
		
		echo"</font>
		<style>
		#tablo td{
			vertical-align:top;	
		}
		#tablo #title td{
			color:#CCC;
		}
		</style>
		<table cellspacing='0' cellpadding='3' id='tablo'>
			<tr id='title'>
				<td></td>
				<td>désignation</td>
				<td>PU</td>
				<td>quant</td>
				<td>tva</td>
				<td>TOTAL</td>
			</tr>
		";
		$bontruc=1;
		$prixtotal;
		$whatsyournumber=0;
		$content="";
		for($i=$nbbox-1 ; $i>=0 ; $i--){
			$itemid = $keybox[$i];
			
			$itemisremise=$_SESSION["osier$caisid"]["content"][$itemid][0];
			$itemquant=$_SESSION["osier$caisid"]["content"][$itemid][1];
			$rem=$_SESSION["osier$caisid"]["content"][$itemid][2];
			
			$idtosearch = $itemid;
			if(!is_numeric($itemid)){			
				$idtosearch = substr($itemid,1,strlen($itemid));						
			}
			
			
			$whatsyournumber++;
		
			$chires = mysql_query("SELECT * FROM artstock WHERE id='$idtosearch'");
			$chirow = mysql_fetch_object($chires);
					$couleur = $chirow->couleur;
					$taille = $chirow->taille;
					$prix = $chirow->prix;
					$promo = $chirow->promo;
					$stock = $chirow->stock;
					$reference = $chirow->reference;
					$ref = $chirow->ref;
					$stockid = $chirow->id;
					$tvai = $chirow->tva;
					if($tvai==0.00) $tvai=19.6;
					$prix = round($prix-($prix*$promo/100),2);
					
			$chires = mysql_query("SELECT * FROM artrad WHERE ref='$ref' AND lang='fr'");
			$chirow = mysql_fetch_object($chires);
					$nom = $chirow->nom;
			
					if($itemisremise=="a"){
						$prix = $prix;
						$nom = $nom;
					}
					elseif($itemisremise=="t"){
						$nom = "$nom remise</b> ($prix - $rem%)";
						$prix = round($prix-($prix*$rem/100),2);
					}
					elseif($itemisremise=="f"){
						$nom = "$nom remise</b> ($prix prix forcé)";
						$prix = $rem;
					}
					elseif($itemisremise=="r"){
						$prix = -$prix;
						$nom = "retour $nom";
					}
					else{
						$prix = $prix;
						$nom = $nom;
					}
					
			$limage = "<img src='image.php?jeveuxW=50&file=data/default.jpg' border='none'>";
				if(file_exists("art/$ref/default.jpg")){
					$limage = "<img src='image.php?jeveuxW=50&file=art/$ref/default.jpg' border='none'>";
				}
				if(file_exists("art/$ref/$couleur.jpg")){
					$limage = "<img src='image.php?jeveuxW=50&file=art/$ref/$couleur.jpg' border='none'>";
				}
				$tot = $prix*$itemquant;
				$prixtotal+=$tot;
				//$tva = round($prix-($prix/1.196),2);
				$tva = round($prix*$tvai/100,2);
				echo"<tr><td colspan='7'></td></tr>";
	
			if($stock < $itemquant && $itemisremise=="a"){
				echo"<tr bgcolor='FF0000'  onclick=\"document.location='_gestion_caisse_ticket.php?last_art=$itemid'\"><td><b>$whatsyournumber</b> $stock max!</td>";
				$bontruc=0;
			}
			elseif((isset($_GET['last_art']) && $_GET['last_art']==$itemid ) || (!isset($_GET['last_art']) && $whatsyournumber==1)){
				echo"<tr bgcolor='EEFFEE'><td>><b>$whatsyournumber</b></td>";
				$last_art = $itemid;
			}
			else{
				echo"<tr bgcolor='FFFFFF' onclick=\"document.location='_gestion_caisse_ticket.php?last_art=$itemid'\"><td><b>$whatsyournumber</b></td>";
				//$last_art = $itemid;
			}
			
			echo"<td title='($itemisremise)'><b>$nom</b><br>$reference <br><font size='1'>$taille $couleur</font>$itemid </td>
				<td align='right'>$prix </td>
				<td align='center'>$itemquant</td>
				<td align='center'><font size='1'>$tvai%</font></td>			
				<td align='right'>$tot</td><td>";
					if($itemisremise!="a" && $itemisremise!="r"){
						echo"<a href='_gestion_caisse_ticket.php?delrem=$itemid' title='supprimer remise'><b>-</b></a>";
					}
					
						echo"<a href='_gestion_caisse_ticket.php?delbox=$itemid'>x</a></td></tr>";
						
				
			$content.="$reference,$nom $taille $couleur,$itemquant,$prix,$tot,$tva,$stockid,$itemisremise,$rem;";  
		}
	
	if(isset($_GET['last_art'])){
		$last_art = $_GET['last_art'];
	}
	$whatsyournumber++;
	if(isset($_SESSION["osier$caisid"]["remise_type"]) && $_SESSION["osier$caisid"]["remise_type"]!=""){
		$totremis = $_SESSION["osier$caisid"]["remise_type"];
		$totrem = $_SESSION["osier$caisid"]["remise_num"];
		$oldprixtotal = $prixtotal;
		if($totremis == "pt"){
			$prixtotal=round($prixtotal-($prixtotal*$totrem/100),2);
			$content.="0,remise,,1,,,0,pt,$totrem;";
			//$content.="$reference,$nom $taille $couleur,$itemquant,$prix,$tot,$tva,$stockid,$itemisremise,$rem;";
			echo"<tr bgcolor='EEFFEE'><td></td>
			<td title='($itemisremise)'><font size='1' color='6666CC'><b>$whatsyournumber</b></font><b>remise</b><br>pourcentage total</td>
				<td>$oldprixtotal</td>
				<td align='center'>- $totrem%</td>			
				<td>$prixtotal</td><td>
				<a href='_gestion_caisse_ticket.php?delrem=pt'>x</a></td></tr>";
		}
		if($totremis == "pft"){
			$prixtotal = $totrem;
			$content.="0,remise,,1,,,0,pft,$totrem;";
			echo"<tr bgcolor='EEFFEE'><td></td>
			<td title='($itemisremise)'><font size='1' color='6666CC'><b>$whatsyournumber</b></font><b>remise</b><br>prix forcé total</td>
				<td>$oldprixtotal</td>
				<td align='center'>-</td>			
				<td>$prixtotal</td><td>
				<a href='_gestion_caisse_ticket.php?delrem=pft'>x</a></td></tr>";
		}
	}
		echo"
		</table>
		<script language='JavaScript'>
			parent.document.nume.numbo.focus();
			parent.reso(\"$last_art\",\"$prixtotal\");
		</script><hr>
		$type_remise / $num_remise";
	/////////////////////////////////////////////////////////////////////////// MODE
	//('','','','','$content','$totalprixcom','$useravoir','$currrentdatetime','$choix','0','0','$livremoi')");
	//echo"debug:";
	//print_r($_SESSION["osier$caisid"]["mode"]);
	if($prixtotal < 0){
		$_SESSION["finalcontent"]=$content;
		$_SESSION["finalccome"]=$come;
		$_SESSION["finalccome"]=$come;
		echo"
		<script language='javascript'>
			function effectvente(){
				document.location='_gestion_caisse_ticket.php?userid=0&caisid=".$_SESSION['caisid']."&useravoir=0&choix=bo&totalprixcom=$prixtotal&content=$content&coment=bon d achat';
			}
			</script>
		<p align='right'><input type='button' value=\"générer un bon d'achat\" onclick='effectvente()'>";
	}
	elseif( sizeof($_SESSION["osier$caisid"]["mode"]) > 0){
		$modes = $_SESSION["osier$caisid"]["mode"];
		//$content = urlencode($content);
		$renduu = $prixtotal;
		echo"<hr><b>Paiement</b><br></form>
		<form action='#' onsubmit='effectvente();return false;' name='forpai' method='post'>
		<table><tr><td>mode</td><td>détails</td><td>montant</td><td></td></tr>";
		$come="";
		for($i=0 ; $i<sizeof($modes) ; $i++){
			$cmode = $modes[$i];
			if($cmode!=null){
				$mmode = $cmode[0];
				$mdeta = $cmode[1];
				$mmont = $cmode[2];
				$renduu-=$mmont;
				$renduu = round($renduu,2);
				echo"
				<tr><td><b>$modechoix[$mmode]</b></td>
				<td><input type='text' value='$mdeta' style='width:100px' onchange=\"document.location='_gestion_caisse_ticket.php?mmode&mmodeid=$i&mmodeki=1&mmodedif='+this.value\" name=\"coco$i\"></td>
				<td><input type='text' value='$mmont' style='width:40px' onchange=\"document.location='_gestion_caisse_ticket.php?mmode&mmodeid=$i&mmodeki=2&mmodedif='+this.value\" name=\"coucou$i\">€</td>
				<td><a href='_gestion_caisse_ticket.php?dmode=$i'>x</a></td></tr>
				";
				$come.="<br>- $modechoix[$mmode]: $mdeta $mmont €";
			}
		}
		$jairendu = $_SESSION["osier$caisid"]["rendu"];
		$come.="<br>- rendu : $jairendu €";
		$i--;
			echo"</table>		<br>
			rendu : <input type='text' value='$jairendu' onchange=\"document.location='_gestion_caisse_ticket.php?rendu='+this.value\">€<br>
			<script language='javascript'>
				document.forpai.coucou$i.focus();
				document.forpai.coucou$i.select();
				fuck=1;
			</script>";
	  $renduu+=$jairendu;
			//$come = urlencode($come);
		if($renduu==0){
			$_SESSION["finalcontent"]=$content;
			$_SESSION["finalccome"]=$come;
			$_SESSION["finalccome"]=$come;
			//&content=$content&coment=$come
			echo"
				<script language='javascript'>
				v=0;
			function effectvente(){
				if(v==0){
				document.location=\"_gestion_caisse_ticket.php?userid=0&caisid=".$_SESSION['caisid']."&useravoir=0&choix=bo&totalprixcom=$prixtotal\";
					v=1;
				}
			}
			</script>
			<hr>
			<input type='checkbox' value='1' name='etprint'>imprimer la facture 
			<p align='right'><input type='submit' value='valider vente' >
			</form>";
		}
		else{
		echo"<script language='javascript'>
			function effectvente(){
	
			}
			</script>
			<br>reste: <b>$renduu €</b><input type='submit' value='ok' onclick='effectvente()'>";
		}
	}
	else{
		echo"<p align='right'>vente en cours...";
	}
	
	echo"<br>
	<script language='javascript'>
			//parent.document.cbcode.codebarre.value = '';
			//document.location='_gestion_caisse_ticket.php?id=$last_art';
			function confannul(){
				conan = confirm('annuler cette vente?');
				if(conan){
					document.location='_gestion_caisse_ticket.php?annulvente';
				}
			}
			if(fuck==0){
			parent.document.nume.numbo.focus();
			}
			</script>
	<input type='button' value='annuler vente' onclick='confannul()'>
	</body>
	</html>
	";
}
?>