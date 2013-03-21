<?php // 107 > Gestion de boutique et commerce ;
	
$comportement = split(",",$types[$part]);


$setopo = array("setvalid","unsetvalid");
$setopot = array("activer cet élément","désactiver cet élément");

$langue_db = "gestion_langue";
$rayons_db = "gestion_rayons";
$articles_db = "gestion_articles";
$rappel_db = "gestion_rappel";

if(isset($gestion_db["langue"])) $langue_db=$gestion_db["langue"];
if(isset($gestion_db["rayons"])) $rayons_db=$gestion_db["rayons"];
if(isset($gestion_db["articles"])) $articles_db=$gestion_db["articles"];
if(isset($gestion_db["rappel"])) $rappel_db=$gestion_db["rappel"];

$depth_rayon=0;
if(isset($depth_gestion["rayon"])) $depth_rayon=$depth_gestion["rayon"];
if($part=="" && isset($_GET["part"]) && $_GET["part"]!=""){
	$part = $_GET["part"];
}
if($part=="gestion_langue") $tabledb = $langue_db;
if($part=="gestion_rayons") $tabledb = $rayons_db;
if($part=="gestion_articles") $tabledb = $articles_db;
if($part=="gestion_rappel") $tabledb = $rappel_db;

$tabledb = $part;

$defstat = array("attente","validée","préparée","expédiée","paiement refusée");
$colorstatut = array("999999","FF9900","009944","00FF00","FF0000"); 
/*********************************************************** FUNCTION GESTION ****/
function get_rayon_parent($id){
	global $rayonchemin;
	global $depth;
	if($id!=0){
		$ris = mysql_query("SELECT `ref` FROM `$rayons_db` WHERE `id`='$id'");
		$riw=mysql_fetch_object($ris);
		$ref = $riw->ref;
		if($ref!=0){
			get_rayon_parent($ref);
		}	
		$hg="rayon #$id";
		$rls=mysql_query("SELECT * FROM `gestion_raytrad` WHERE `ref`='$id' AND `nom`!='' LIMIT 0,1");
		if($rls && mysql_num_rows($rls)==1){
			$rws = mysql_fetch_object($rls);
			$hg=$rws->nom;
		}
		$rayonchemin.="<a href='./?gestion_rayons&edit=$id '>$hg</a> /";
		$depth++;			
	}
}
function get_item_trans($id,$db="ray",$lng=""){
	if($id!=0){
		$pluscode="";
		if($code!="") $pluscode=" AND `lng`='$lng'";
		$ris = mysql_query("SELECT `nom` FROM `gestion_$db"."trad` WHERE `ref`='$id' AND `nom`!='' $pluscode LIMIT 0,1");
		if($ris && mysql_num_rows($ris)==1 ){
			$riw=mysql_fetch_object($ris);
			return $riw->nom;
		}	
		else{
			return "$db #$id";
		}		
	}
	else{
		if($db=="ray") return"boutique";
		if($db=="ar") return"articles";
		return $db;
	}
}
/*********************************************************** END FUNC GESTION ****/
if($part != ""){
if( !isset($comportement) || (sizeof($comportement)==1 && $comportement[0]=="") || in_array("txt",$comportement)){	
	$conn = connecte($base, $host, $login, $passe);
		$verifupdt = mysql_query("DESC `$tabledb`");
		$allchamps = array();
		while($ro = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$ro->Field);
		}
		if(!in_array("clon",$allchamps)){
			mysql_query("ALTER TABLE `$tabledb` ADD `clon` BIGINT NOT NULL");
		}
		if(!in_array("active",$allchamps)){
			mysql_query("ALTER TABLE `$tabledb` ADD `active` INT( 1 ) NOT NULL ;");
		}
	mysql_close($conn);	
	
	if(isset($_GET['unsetclon'])){
		if($u_droits == '' || $u_active == 1 ){
			$conn = connecte($base, $host, $login, $passe);
			$unsetclon = $_GET['unsetclon'];
			$res = mysql_query("SELECT `clon` FROM `$tabledb` WHERE id='$unsetclon'");
			$ro = mysql_fetch_object($res);
			$ref = $ro->clon;
			if( mysql_query("UPDATE `$tabledb` SET `clon`='0' WHERE id='$unsetclon'") && deletefromdb($base,$part,$ref)){
				$return.=returnn("mise à jour effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise à jour a échouée","990000",$vers,$theme);
			}
			mysql_close($conn);
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre en ligne ce texte","990000",$vers,$theme);
		}
	}
	
	if(isset($_GET['setvalid'])){
		$tmpdb=$tabledb;
		if(isset($_GET['effdb']) && $_GET['effdb']!=''){
			$tabledb=$_GET['effdb'];
		}
		if($u_droits == '' || $u_active == 1 ){
			$conn = connecte($base, $host, $login, $passe);
			$setvalid = $_GET['setvalid'];
			if( mysql_query("UPDATE `$tabledb` SET `active`='1' WHERE id='$setvalid'") ){
				$return.=returnn("mise en ligne effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise en ligne a échouée","990000",$vers,$theme);
			}
			mysql_close($conn);
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre en ligne ce texte","990000",$vers,$theme);
		}
		$tabledb=$tmpdb;
	}
	if(isset($_GET['unsetvalid'])){
		$tmpdb=$tabledb;
		if(isset($_GET['effdb']) && $_GET['effdb']!=''){
			$tabledb=$_GET['effdb'];
		}
		if($u_droits == '' || $u_active == 1 ){
			$conn = connecte($base, $host, $login, $passe);
			$unsetvalid = $_GET['unsetvalid'];
			if( mysql_query("UPDATE `$tabledb` SET `active`='0' WHERE id='$unsetvalid'") ){
				$return.=returnn("mise hors ligne effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise hors ligne a échouée","990000",$vers,$theme);
			}
			mysql_close($conn);
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre hors ligne ce texte","990000",$vers,$theme);
		}
		$tabledb=$tmpdb;
	}

	$filepart = ereg_replace(">","-",$part);
	if(file_exists("$filepart.php")){
		echo"<a class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mesure.gif'>
				<span>Partie sur mesure</span></a>";
		$conn = connecte($base, $host, $login, $passe);
		include("$filepart.php");
		mysql_close($conn);
	}
	else{	
	if(isset($_GET['edit'])){		
		$action="update";		
		if(!isset($_GET['add'])){
			$edit = $_GET['edit'];
		}
		if($edit == '' || isset($_GET['clone']) || isset($_GET['new'])){
			$action='add';
		}
		echo"<form action='./?$part&$action&edit=$edit' method='post' name='fourmis' enctype='multipart/form-data'  onsubmit=\"affichload()\">";
	}
	
	$conn = connecte($base, $host, $login, $passe);
	
	$res_field = mysql_list_fields($base,$tabledb);
   	$columns = mysql_num_fields($res_field);
	
if(isset($_GET['s'])){
	$sa = $_GET['s'];
	$va = $_GET['v'];
	$_SESSION[$sa] = $va;
}	


		echo"
		<script language='javascript' type='text/javascript'>
			function exporter(){
				document.listage.action='./?$part&exporter';
				bbsel='';
				var allche = document.listage.getElementsByTagName(\"input\");
				for (var i=2; i<allche.length; i++) {
					if(allche[i].checked==true){
						bbsel+=allche[i].name;
					}
				}
				if(bbsel!=''){
					document.listage.action+='&selected='+bbsel;
				}
				document.listage.submit();
			}
		 </script>
		
		<table cellspacing='0' cellpadding='3' border='0' width='100%'><tr style='height:20px;'><td class=\"buttontd\" width=\"10\">&nbsp;</td>";
		
if($part!="gestion_rappel" && ( !isset($gestion_rappel_base) || $part!=$gestion_rappel_base)){			
		if(!isset($_GET['edit']) && !isset($_GET['exporter']) && !isset($_GET['statistiques']) ){
			echo"<td class=\"menuselected\" width='100'><a href=\"./?$part\">Liste</a></td>";
			if(!in_array("nonew",$comportement)){echo"<td class=\"buttontd\" width='100'><a href=\"./?$part&amp;edit\">Nouveau</a></td>";}
			echo"<td class=\"buttontd\" width='100'><a href='#' onclick='exporter()'>Exporter</a></td>
			<td class=\"buttontd\" width='80'><a href=\"./?$part&statistiques\">Statistiques</a></td>
			";
		}
		elseif(isset($_GET['statistiques'])){	
			echo"<td class=\"buttontd\" width='80'><a href=\"./?$part\">Liste</a></td>";
			if(!in_array("nonew",$comportement)){echo"<td class=\"buttontd\" width='100'><a href=\"./?$part&amp;edit\">Nouveau</a></td>";}
			echo"<td class=\"buttontd\" width='80'><a href='#' onclick='exporter()'>Exporter</a></td>
			<td class=\"menuselected\" width='80'><a href=\"./?$part&statistiques\">Statistiques</a></td>
			";
		}
		elseif(isset($_GET['exporter'])){	
			echo"<td class=\"buttontd\" width='100'><a href=\"./?$part\">Liste</a></td>";
			if(!in_array("nonew",$comportement)){echo"<td class=\"buttontd\" width='100'><a href=\"./?$part&amp;edit\">Nouveau</a></td>";}
			echo"<td class=\"menuselected\" width='100'><a href='#' onclick='exporter()'>Exporter</a></td>
			<td class=\"buttontd\" width='80'><a href=\"./?$part&statistiques\">Statistiques</a></td>
			";
		}
		elseif( ($_GET['edit']=='' || isset($_GET['new'])) && !in_array("nonew",$comportement)){	
			echo"<td class=\"buttontd\" width='100'><a href=\"./?$part\">Liste</a></td>
			<td class=\"menuselected\" width='100'><a href=\"./?$part&amp;edit\">Nouveau</a></td>
			<td class=\"buttontd\" width='80'><a href=\"./?$part&statistiques\">Statistiques</a></td>
			";
		}
		elseif(isset($_GET['clone']) && !in_array("nonew",$comportement)){	
			echo"<td class=\"buttontd\" width='100'><a href=\"./?$part\">Liste</a></td>
			<td class=\"buttontd\" width='100'><a href=\"./?$part&amp;edit\">Nouveau</a></td>
			<td class=\"menuselected\" width='100'><a href=\"./?$part&amp;edit=$edit&clone\">Clone($edit)</a></td>
			<td class=\"buttontd\" width='80'><a href=\"./?$part&statistiques\">Statistiques</a></td>";
		}
		else{
			echo"<td class=\"buttontd\" width='100'><a href=\"./?$part\">Liste</a></td>";
			if(!in_array("nonew",$comportement)){echo"<td class=\"buttontd\" width='100'><a href=\"./?$part&amp;edit\">Nouveau</a></td>";}
			echo"<td class=\"menuselected\" width='100'><a href=\"./?$part&amp;edit=$edit\">&Eacute;dition ($edit)</a></td>
			<td class=\"buttontd\" width='80'><a href=\"./?$part&statistiques\">Statistiques</a></td>";
		}	
}
else{
		echo"<td class=\"buttontd\" width='100'><a href='#' onclick='exporter()'>Exporter</a></td>";
}
		echo"<td class=\"buttontd\" align='left'><p align='left'>&nbsp;";
		
		if(!isset($_GET['edit'])){	
		
			if(isset($_GET['al'])){
				$al = $_GET['al'];
				$fp = fopen("mconfig/$u_id.list.$part.conf","w+");
				fwrite($fp,$al);
				fclose($fp);
			}
			else{
			 if(!is_file("mconfig/$u_id.list.$part.conf")){
					$fp = fopen("mconfig/$u_id.list.$part.conf","w+");
					fwrite($fp,"l");
					fclose($fp);
				}
				else{
					$fp = fopen("mconfig/$u_id.list.$part.conf","r");
					$al = trim(fread($fp,5));
					fclose($fp);
				}
			}
			if($al==""){
				$al = "l";
			}
		
		if($al==="i"){
			echo"
			<a href='./?$part&al=l'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/view-list.png' border='none' alt='affichage liste'></a>
			<a href='./?$part&al=i'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/view-icon.png' border='1' alt='affichage icônes'></a>";
		}
		else{
			echo"
			<a href='./?$part&al=l'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/view-list.png' border='1' alt='affichage liste'></a>
			<a href='./?$part&al=i'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/view-icon.png' border='none' alt='affichage icônes'></a>";
		}
		}
		echo"</td></tr>
		<tr><td align='center' colspan='8' class='cadrebas'>";
		
	for ($i = 0; $i < $columns; $i++) {
		$field_name = mysql_field_name($res_field, $i);
		$fieldoption = substr($field_name,strpos($field_name,"_")+1,strlen($field_name));
		if(substr($field_name,0,1) == "_"){
			$field_named = substr($field_name,1,strlen($field_name));
			echo"$field_named<select onchange=\"javascript:document.location='./?$part&s=$field_name&v='+this.value\" style=\"width:100px\">
			<option value=\"\" selected>tou(te)s</option>";
				$allready=array();
				$listres = mysql_query("SELECT `$field_name` FROM `$tabledb` WHERE `$field_name`!='' ORDER BY `$field_name`");
				while($rowlist = mysql_fetch_object($listres)){
					$rowvalue = $rowlist->$field_name;
					$s="";
					if($rowvalue == $_SESSION[$field_name] ){
						$s = "selected";
							$incwhere.=" AND `$field_name`LIKE'$rowvalue'";
					}
					if(!in_array($rowvalue,$allready) && trim($rowvalue)!=""){
						echo"<option value=\"$rowvalue\" $s>$rowvalue</option>";
						array_push($allready,$rowvalue);
					}
				}
			echo"</select>&nbsp;";
		}
		elseif(substr($fieldoption,0,1) != "@" && ereg("_",$field_name)){
				$refiled = substr($field_name,0,strpos($field_name,"_"));				
				$nameifthefield = $refiled;
				$fieldoption = split("[_>]",$fieldoption);
				$fieldoptionprint = $fieldoption[1];
				$fieldoption = $fieldoption[0];		
				$refiled = trim($refiled);	
				if($prefixe!=""){
						$nameifthefield = trim(ereg_replace($prefixe,"",$nameifthefield));
					}	
				if(ereg(">",$field_name)){
						$nameifthefield .= " ".substr($field_name,strpos($field_name,">")+1,strlen($field_name));
				}

				echo"$nameifthefield<select onchange=\"javascript:document.location='./?$part&s=$field_name&v='+this.value\" style=\"width:100px\">
			<option value=\"\" selected>tou(te)s</option>";
				$listres = mysql_query("SELECT `$fieldoptionprint`,`$fieldoption` FROM `$refiled` WHERE `$fieldoptionprint`!='' ORDER BY `$fieldoptionprint`");
				while($rowlist = mysql_fetch_object($listres)){
					$rowvalue = $rowlist->$fieldoptionprint;
					$rowid = $rowlist->$fieldoption;
						$s = "";
						if($rowvalue == $_SESSION[$field_name] ){
							$s = "selected";
								$incwhere.=" AND `$field_name`LIKE'$rowvalue'";
						}
						echo"<option value=\"$rowid\" $s>$rowvalue</option>";
				}
			echo"</select> ";
		}
	}
	

			mysql_close($conn);
		echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'><tr><td valign='top'>";
	
if(isset($_GET['edit']) || $part=="gestion_rappel" || ( isset($gestion_rappel_base) && $part==$gestion_rappel_base)){		

if(isset($_GET['new'])){
	$edit='';
}	
	$conn = connecte($base, $host, $login, $passe);	
	$res = mysql_query("SELECT * FROM `$tabledb` WHERE `id`='$edit'");
	$ro = mysql_fetch_object($res);
	echo"<table>";
	
	if( $part=="gestion_commandes" || ( isset($compta_base) && $part==$compta_base) ){
		insert("_gestion_commandes");
		if(is_file("bin/_gestion_commandes.php")){
			include("bin/_gestion_commandes.php");
		}
		else{
			include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_gestion_commandes.php");
		}
	}
	elseif( $part=="gestion_clients" || ( isset($gestion_clients_base) && $part==$gestion_clients_base) ){
		insert("_gestion_clients");
		if(is_file("bin/_gestion_clients.php")){
			include("bin/_gestion_clients.php");
		}
		else{
			include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_gestion_clients.php");
		}
	}
	elseif( $part=="gestion_rayons" || ( isset($gestion_rayons_base) && $part==$gestion_rayons_base) ){
		insert("_gestion_rayons");
		if(is_file("bin/_gestion_rayons.php")){
			include("bin/_gestion_rayons.php");
		}
		else{
			include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_gestion_rayons.php");
		}
	}
	elseif( $part=="gestion_langue" || ( isset($gestion_langue_base) && $part==$gestion_langue_base) ){
		insert("_gestion_langue");
		if(is_file("bin/_gestion_langue.php")){
			include("bin/_gestion_langue.php");
		}
		else{
			include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_gestion_langue.php");
		}
	}
	elseif( $part=="gestion_articles" || ( isset($gestion_articles_base) && $part==$gestion_articles_base) ){
		insert("_gestion_articles");
		if(is_file("bin/_gestion_articles.php")){
			include("bin/_gestion_articles.php");
		}
		else{
			include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_gestion_articles.php");
		}
	}
	elseif( $part=="gestion_rappel" || ( isset($gestion_rappel_base) && $part==$gestion_rappel_base) ){
		insert("_gestion_rappel");
		if(is_file("bin/_gestion_rappel.php")){
			include("bin/_gestion_rappel.php");
		}
		else{
			include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_gestion_rappel.php");
		}
	}
	else{
	   for ($i = 0; $i < $columns; $i++) {
			$field_name = mysql_field_name($res_field, $i);
			$field_type = mysql_field_type($res_field, $i);			
			$field_length = mysql_field_len($res_field, $i);
			$field_value = $ro->$field_name;	
			$field_width=300;
			if($field_length < $field_length*12){
				$field_width=$field_length*12;
			}
			$nameifthefield = ereg_replace(">"," ",$field_name);	
			echo"<tr><!-- $field_name $field_type -->";
			/////////////////////////////////////// ID
			 if($field_name == "id" || $field_name == "clon"){
			 	if(isset($_GET['clone'])){
					$field_value='';
				}
				 echo"<td></td><td><input type=\"hidden\" name=\"$field_name\" value=\"$field_value\"></td>";
			 }
			 /////////////////////////////////////// ACTIVE
			 elseif($field_name == "active"){
			 	$actouno = array("","checked");
				$actoudos = array("checked","");
			 	if($u_droits == ""){
				 echo"<td>activé</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>
				 	oui<input type=\"radio\" name=\"$field_name\" value=\"1\" $actouno[$field_value]>
				 	non<input type=\"radio\" name=\"$field_name\" value=\"0\" $actoudos[$field_value]>
				 </td>";
				}
				else{
				 echo"<td>activé</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>sans droits<input type=\"hidden\" name=\"$field_name\" value=\"0\"></td>";
				}
			 }
			/////////////////////////////////////// COULEUR
			elseif($field_name == "couleur" && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";
					}
			 	echo"<td>couleur</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>
				 	<input type=\"text\" name=\"$field_name\" value=\"$field_value\">
						<div id=\"div$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:20px;width:30px;border-color:#000000;border-style:solid;border-width:1px\"></div>
							";
							
							echo colorpicker($field_name,$field_value,"document.fourmis.$field_name.value='COLOR';document.getElementById('div$field_name').style.backgroundColor='COLOR'");
							
							echo"
				 </td>";
			 }
			 ///////////////////////////////////// PREFIXE
			 elseif(substr($field_name,0,1) == "_"){
				$nameifthefield = substr($field_name,1,strlen($field_name));
				if($field_value==""){
					$field_value=$_SESSION[$field_name];
				}
				echo"<td>$nameifthefield</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:200px\" readonly>
				 <select onchange=\"javascript:set$nameifthefield(this.value);this.value=' '\" style=\"width:100px\">
				 	<option value=' '>-- $nameifthefield --</option>";
					$allready=array();
					$listres = mysql_query("SELECT * FROM `$tabledb` $incwhere");
					while($rowlist = mysql_fetch_object($listres)){
						$rowvalue = $rowlist->$field_name;
						if(!in_array($rowvalue,$allready) && trim($rowvalue)!=""){
							echo"<option value=\"$rowvalue\">$rowvalue</option>";
							array_push($allready,$rowvalue);
						}
					}
					echo"
					<option value=''>-- nouveau -- </option>
				 </select>
				 <script language='javascript' type='text/javascript'>
				 	function set$nameifthefield(koi){
						if(koi == ''){
							pro = prompt(\"veuillez entrer un nom pour le nouvel élément\",\"nouveau\");
							if(pro){
								document.fourmis.$field_name.value=pro;
							}							
						}
						else{
							document.fourmis.$field_name.value=koi;
						}						
					}
				 </script>				 
				 </td>";
			}
			 ///////////////////////////////////// SUFIXE
			 elseif(ereg("_",$field_name)){
			 	$refiled = substr($field_name,0,strpos($field_name,"_"));
				$fieldoption = substr($field_name,strpos($field_name,"_")+1,strlen($field_name));
				$nameifthefield = $refiled;
				if(ereg(">",$field_name)){
					$fieldoption = substr($fieldoption,0,strpos($fieldoption,">"));
					$nameifthefield .= " : ".substr($field_name,strpos($field_name,">")+1,strlen($field_name));
				}
				
				
				if(substr($fieldoption,0,1) == "@"){
					$nameofoption = substr($fieldoption,1,strlen($fieldoption));	
					$field_value = $_SESSION[$nameofoption];	
					echo"<td>$nameifthefield <a class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/pile.gif'>
					<span>Ce champ est à un élément personnel de session <b>$nameofoption</b></span></a></td><td>
					 <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:300px\" readonly>
					 </td>";		
				}
				else{				
					$fieldoption = split("_",$fieldoption);
					$fieldoptionprint = $fieldoption[1];
					$fieldoption = $fieldoption[0];		
					$refiled = trim($refiled);	
					if($prefixe!=""){
						$nameifthefield = trim(ereg_replace($prefixe,"",$nameifthefield));
					}
					echo"<td>$nameifthefield <a class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/pile.gif'>
					<span>Ce champ est relié au tableau <b>$refiled</b></span></a></td><td>
					 <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'><select name=\"$field_name\" style=\"width:300px\">
						<option value=' '>liste des choix</option>";
						$listres = mysql_query("SELECT * FROM `$refiled`");
						while($rowlist = mysql_fetch_object($listres)){
							$rowvalue = $rowlist->$fieldoptionprint;
							$rowid = $rowlist->$fieldoption;
							$se = "";
							if($rowid == $field_value){
								$se = "selected";
							}
							echo"<option value=\"$rowid\" $se>$rowvalue</option>";
						}
						echo"</select></td>";
				 }	 
				 
			}
				/////////////////////////////////////// DATE
			 elseif($part=="commandes" && ($field_type == "date" || $field_type == "time" || $field_type == "datetime")){
			 	if($field_value==""){
					$field_value=$defaultvalue[$field_type];
					}
					$field_value = date("d/m/Y - H:m",strtotime($field_value));	
					echo"<td>$nameifthefield</td><td>$field_value</td>";	
				}	
			////////////////////////////////////////// PASSWORD
			elseif($field_name == "pass" || $field_name == "passe"){
				echo"<td>$nameifthefield</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-string.png' alt='texte'><input type=\"password\" name=\"$field_name\" value=\"$field_value\"></td>";
			}
			 /////////////////////////////////////// STRING
			 elseif($field_type == "string"){			 	  
				 echo"<td><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:field_width px\" maxlength=\"$field_length\"></td>";
			 }
			 /////////////////////////////////////// INT
			 elseif($field_type == "int" || $field_type == "real"){			 	  
				 echo"<td><a class='info'>$nameifthefield<span>Nombre</span></a></td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-int.png' alt='numérique'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:150px\" maxlength=\"$field_length\"></td>";
			 }
			 /////////////////////////////////////// DATE
			 elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
			 	if($field_value==""){
					$field_value=$defaultvalue[$field_type];
				}
				 echo"<td><a class='info'>$nameifthefield<span>Date au format standard<br>date: aaaa-mm-jj<br>heure: hh:mm:ss</span></a></td><td>
				 <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"javascript:document.getElementById('menu_date').style.visibility='visible';cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=fourmis.$field_name&amp;date='+document.fourmis.$field_name.value+'&amp;type=$field_type'\"><input type='text' name=\"$field_name\" value=\"$field_value\" maxlength=\"$field_length\">
				 </td>";
			 }

			 /////////////////////////////////////// DEFAULT
			 else{
				 echo"<td><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:300px\" maxlength=\"$field_length\"></td>";
			 }
			 echo"</tr>
			 <tr><td colspan='2'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>";
	   }
	  }
	   mysql_close($conn);
	   echo"</table>";

	   if( (sizeof($comportement) > 1 || isset($fichiers[$part])) && !isset($_GET['clone']) ){	   
	   		if(!is_dir("../$part")){
							mkdir("../$part",0777);
						} 			 
	   		echo"</td><td align='left' valign='top'><table>";
			if(isset($fichiers[$part])){		//////////////////////////////////////////// 			CUSTOM FILES	
					$custom_files = $fichiers[$part];
					$custom_keys = array_keys($custom_files);
					$i=0;
					while($i<sizeof($custom_keys)){
						$custom_name = $custom_keys[$i];
						$custom_dir = substr($custom_files[$custom_name][0],strpos($custom_files[$custom_name][0],"/"),strlen($custom_files[$custom_name][0]));
						$custom_file = $custom_files[$custom_name][1];
						if($edit==""){
								echo"vous pourrez ajouter une image après un premier enregistrement";								}
						else{
								echo"<tr><td valign='top'><b>$custom_name</b></td><td>";
								if(is_file("../".$custom_dir.$custom_file)){
									echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
									<img src='./?incpath=_ima.php&file=$custom_dir$custom_file' alt='icone' height='100'><br>
									<a href=\"#\" onclick=\"delfile('../$custom_dir$custom_file')\">
									<img src=\"http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif\" border='none' alt='supprimer'></a>
									</td></tr></table>";
								}
								echo"<input type='file' name='file[]'><hr></td></tr>";
						}
						$i++;
					}
			}
			else{		//////////////////////////////////////////// 			DEFAULT FILES										
							
						if(in_array("ico",$comportement) && $edit==""){
							echo"vous pourrez ajouter une image après un premier enregistrement";			
						}
						if(in_array("ico",$comportement) && $edit!=""){
							echo"<tr><td valign='top'>image d'aperçu</td><td>";
							$funico = "<font style='font-size:9px'><a href='#' onclick=\\\\\"addspan('spa_ico','../$part/$edit.ico')\\\\\">charger un autre fichier</a></font>";
							for($ic=0; $ic<sizeof($imacool) ; $ic++){
								if(file_exists("../$part/$edit.$imacool[$ic]")){
									echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
									<img src='../$part/$edit.$imacool[$ic]' alt='icone' height='100'><br>
									<a href=\"#\" onclick=\"delfile('../$part/$edit.$imacool[$ic]')\">
									<img src=\"http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif\" border='none' alt='supprimer'></a>
									</td></tr></table>";
									break;
								}
							}
							echo"<br>&nbsp;- <span id='spa_ico'><input type='file' onchange=\"addfile('')\"></span>
							</td></tr>";
						}
						else{
							$funico="";
							echo"<tr><td></td><td><span id='spa_ico'></span></td></tr>";
						}
						if(in_array("dir",$comportement) && $edit!=""){			
							if(!is_dir("../$part/$edit")){
									if(!mkdir("../$part/$edit",0777)){
										echo"erreur de configuration serveur, le dossier ne peut être créé...";
									}
							} 
							$fundir="<font style='font-size:9px'><a href='#' onclick=\\\\\"addspan('spa_dir','../$part/$edit/')\\\\\">charger un autre fichier</a></font>";
							echo"<tr><td valign='top'>fichiers</td><td>
							
							<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>";
							$dir = dir("../$part/$edit");
							while($entry = $dir->read()){
								if($entry != "." && $entry != ".."){
									$ent_ext = strtolower(substr(strrchr($entry,"."),1));
									echo"<div><a href='../$part/$edit/$entry' target='_blank'>";
									if(in_array($ent_ext,$imacool)){
										echo"<img src='../$part/$edit/$entry' width='80' alt='$entry' border='none'>";
									}
									else{
										echo"<img src='http://www.adeli.wac.fr/icos/$ent_ext.gif' alt='$ent_ext' border='none'>$entry";
									}
									echo"</a><a href=\"#\" onclick=\"delfile('../$part/$edit/$entry')\"><img src=\"http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif\" border='none' alt='supprimer'></a></div>";
								}
							}
							echo"
							</td></tr></table>
							<br>&nbsp;- <span id='spa_dir'></span></td></tr>";
						}
						else{
							$fundir="";
							echo"<tr><td></td><td><span id='spa_dir'></span></td></tr>";
						}
			}
			echo"</table>
	<script language='javascript' type='text/javascript'>	
	function addfile(ou){
		
			document.fourmis.action+='&addfile='+ou;
	 	}
		function addspan(ki,ou){				 	
			document.getElementById('spa_ico').innerHTML=\"$funico\";
			document.getElementById('spa_dir').innerHTML=\"$fundir\";
			if(ki != ''){
				document.getElementById(ki).innerHTML=\"<input type='file' name='file' onchange=addfile('\"+ou+\"')>\";
			}
		}
		addspan('','');
	</script>";   		
	   } 	   
	   echo"</td></tr>
	   <tr><td colspan='2'>";
	   if($part!="gestion_rappel" && ( !isset($gestion_rappel_base) || $part!=$gestion_rappel_base)){	
	   echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>
	  <tr><td colspan='2' align='left'>	
			<input class=\"buttontd\" type=\"button\" value=\"annuler\" onclick=\"document.location='./?$part';\">
			&nbsp;	&nbsp;	&nbsp;	&nbsp;	
			
		<input class=\"buttontd\" type=\"button\" value=\"enregistrer et revenir\" onclick=\"document.fourmis.action='./?$part&$action=$edit';document.fourmis.submit()\"> ";
		if( !in_array("nonew",$comportement)){	
		echo"<input class=\"buttontd\" type=\"button\" value=\"enregistrer et ajouter\" onclick=\"document.fourmis.action+='&new';document.fourmis.submit()\"> ";
		}
				echo"<input class=\"buttontd\" type=\"submit\" value=\"enregistrer\">
				";
				
		}
	
if($u_droits!="" && ereg("@",$r_alerte)){	

	
	$path = getenv('SCRIPT_NAME');

$urltovalid="http://$prov$path?option=$option&$part&setvalid=$edit";	
if($clonid != 0){
	$urltovalid="http://$prov$path?option=$option&$part&unsetclon=$edit";
}		
	echo"
	</td></tr>
	<tr><td colspan='2' align='center'>
	</form>
	
	envoyer une alerte pour la validation<br>
	<form action='./?$part&alert&l=$edit' method='post'>
	<textarea name='message' cols='50' rows='6'>Alerte mise à jour
	
$u_login a modifié un élément qui doit être validé

valider > $urltovalid
	
	</textarea><br><br>
	<input class='buttontd' type='submit' value='envoyer'>		
		";
	
}

}	
elseif(isset($_GET['alert'])){
if(isset($_GET['alert'])){
	$message = ereg_replace("'","''",$_GET['post1']);	
	$l = $_GET['l'];
	$p_date = date("Y-m-d H:i:s");
	mail($r_alerte,"alerte mise à jour",$message,"from: $u_login<$u_email>");
	if(mysql_query("INSERT INTO atouts_caroline_message VALUES('','$x_id','$p_date','$u_id','0','alerte mise à jour','$message','0')")){
		$return.=returnn("message envoyé avec succès","009900",$vers,$theme);
	}
	else{
		$return.=returnn("votre message n'a pu &ecirc;tre envoyé","990000",$vers,$theme);
	}
}
	$message = $_POST['message'];
	$l = $_GET['l'];
	$message = nl2br($message);
	echo"
	<table cellspacing='0' cellpadding='2' class='cadrebas'>
		<tr><td class='buttontd'>Alerte</td></tr>		
		<tr><td>
		
		<table>
			<tr><td>
			
			$message
			
			
			</td></tr>
			
		
		 <tr><td align='right'>				
				<div class=\"buttontd\" style='width:140px'><a href=\"./?$part&edit=$l\">retour à l'article</a></div>
				<div class=\"buttontd\" style='width:140px'><a href=\"./?$part\">retour à la liste</a></div>
			</td></tr>
		
		
		</table>
		
		</td></tr>
		</table>
	";
}
/********************************************************************************************************************

									STATISTIQUES

**********************************************************************************************************************/

elseif(isset($_GET["statistiques"])){	
	$tabledb = $part;
	insert('_statistiques');
	if(is_file("bin/_statistiques.php")){
		include("bin/_statistiques.php");
	}
	else{
		include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_statistiques.php");
	}
}

/********************************************************************************************************************

									EXPORTER

**********************************************************************************************************************/

elseif(isset($_GET["exporter"])){	
	insert('_exporter');
	if(is_file("bin/_exporter.php")){
		include("bin/_exporter.php");
	}
	else{
		include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_exporter.php");
	}
}

else{	//////////////////////////////////////////////////////// LISTE	

if( $part=="gestion_rayons" || ( isset($gestion_rayons_base) && $part==$gestion_rayons_base) ){
	$incwhere = "WHERE `ref`=0";
}


echo"
	<tr><td valign='top' colspan='3' align='center'>
	<script language='javascript'>
	function sela(k){
		var allche = document.listage.getElementsByTagName(\"input\");
		for (var i=2; i<allche.length; i++) {
			allche[i].checked=k;
		}
	}
	function conmulti(k){
		var transk = new Array();
		transk['active']='activer';
		transk['desactive']='désactiver';
		transk['delete']='supprimer';
		nbsel=0;
		var allche = document.listage.getElementsByTagName(\"input\");
		for (var i=2; i<allche.length; i++) {
			if(allche[i].checked==true) nbsel++;
		}
		if(nbsel>0){
			pro = confirm(\"êtes vous certain de vouloir \"+transk[k]+\" les \"+nbsel+\" objets sélectionnés ?\");
			if(pro){
				document.listage.action+='&multi='+k;
				document.listage.submit();
			}
		}
		else{
			alert(\"aucun objet n'est sélectionné\");
		}
	}
	</script>
	<form name='listage' action='./?$part' method='post'>
	<table width='100%' cellspacing='1' cellpadding='0' border='0'><tr>
	<td align='left'><input type='checkbox' onclick='sela(this.checked)'>
	 -	
	<a href='#' onclick=\"conmulti('active')\"><img src='http://www.adeli.wac.fr/vers/$vers/$theme/v1.gif' border='none' alt='activer'></a>
	<a href='#' onclick=\"conmulti('desactive')\"><img src='http://www.adeli.wac.fr/vers/$vers/$theme/v0.gif' border='none' alt='désactiver'></a>
	<a href='#' onclick=\"conmulti('delete')\"><img src='http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif' border='none' alt='supprimer'></a>
	</td>
	<td align='right'>
		n'afficher que les actifs ";
		if($affdesac==0){
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?$part&affdesac=1'\">";
		}
		else{
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?$part&affdesac=0'\" checked>";
			$incwhere.=" AND `active`=1";
		}		
		echo"
		<td>
	</tr>
	<tr><td colspan='2'>
	
"; 
	$conn = connecte($base, $host, $login, $passe);
		insert("inc_gliste");
		if(is_file("bin/inc_gliste.php")){
			include("bin/inc_gliste.php");
		}
		else{
			include("http://www.adeli.wac.fr/vers/$vers/update.php?file=inc_gliste.php");
		}
	echo"
		</td></tr>
		</table>
		</form>";
	mysql_close($conn);

}

echo"</td></tr></table></td></tr></table>";		  
		  if(isset($_GET['edit'])){		
			echo"</form>";
		}
	}
	
	}
	elseif( in_array("dir",$comportement) && isset($dirfiles)){
		if(is_dir("../".$dirfiles[$part]) && $dirfiles[$part]!=""){
			insert("inc_dir");
			if(is_file("bin/inc_dir.php")){
				include("bin/inc_dir.php");
			}
			else{
				include("http://www.adeli.wac.fr/vers/$vers/update.php?file=inc_dir.php");
			}
		}
		else{
			echo"<table cellspacing='0' cellpadding='2' width='80%' class='cadrebas'><tr>
		<td class='menuselected' width='80'>répertoire</b></td>
		<td class='buttontd'></td></tr>		
		<tr><td colspan='2'>le dossier recherché n'existe pas</td></tr></table>";
		}
	}
	
}	

else{ ///////////////////////////////: LISTE
		echo"
	<table cellspacing='0' cellpadding='0' border='0' class='cadrebas'>
   <tr style='height:20px'><td class='buttontd'><b>Accueil Gestion</b><br>Gestion de boutique et commerce</td></tr>
   <tr><td class='cadrebas'>
   <table style='margin:20px'>";
 for($i=0; $i<sizeof($menu) ; $i++){
 
 
			$spart = $menupart[$i];
			$tablo = $menu[$spart];
			$cols = sizeof($tablo);	
			if( ($option=="site" && substr($spart,0,7)!="gestion" && substr($spart,0,7)!="worknet") || (($option==substr($spart,0,7) && substr($spart,0,7)=="worknet") || ($option==substr($spart,0,7) && substr($spart,0,7)=="gestion")) ){		
			echo"<tr class='bando'><td colspan='3'><b>$spart</b></td></tr>";
			$tablk = array_keys($tablo);
		   for($m=0; $m<sizeof($tablo) ; $m++){
		   	$tk = $tablk[$m];
		   	if(!is_numeric($tk)){
				$humanpart = $tk;
			}
			else{
				$humanpart = $tablo[$tk];
				if($prefixe != ""){
					$humanpart = ereg_replace($prefixe,"",$humanpart);
				}
				$humanpart = ereg_replace($spart."_","",$humanpart);
				$humanpart = ereg_replace("adeli_","",$humanpart);
				$humanpart = ereg_replace(">$spart","",$humanpart);
				$humanpart = ereg_replace("-$spart","",$humanpart);
				$humanpart = ereg_replace(">"," ",$humanpart);	
			}
			$humanpart = ucfirst($humanpart);	
			$conn = connecte($base, $host, $login, $passe);
			$nbro="";
			if(mysql_query("SHOW COLUMNS FROM `$tablo[$tk]`")  ){
				$res = mysql_query("SELECT `id` FROM `$tablo[$tk]`");
				$nbro = "(".mysql_num_rows($res)." enregistrements)";
			}
			$vasaj="";
			if(isset($types[$tablo[$tk]])){
				$comportement = split(",",$types[$tablo[$tk]]);
			}
			if(mysql_query("SHOW COLUMNS FROM `$tablo[$tk]`")){	
				if( !isset($comportement) || !in_array("nonew",$comportement) ){
					$vasaj = " | <a href='./?$tablo[$tk]&edit'>nouveau</a>";
				}
				if( !isset($comportement) || in_array("txt",$comportement) || !in_array("dir",$comportement) ){	
					$vasaj .= " | <a href='./?$tablo[$tk]&exporter'>exporter</a>   | <a href='./?$tablo[$tk]&statistiques'>statistiques</a>";
				}
				else{
					//$vasaj .= " | <font class='petittext'>exporter</font>   | <font class='petittext'>statistiques</font> ";
				}
			}
			elseif( isset($dirfiles[$tablo[$tk]]) && is_dir('../'.$dirfiles[$tablo[$tk]]) ){
				$php = phpversion();
				$vasaj = " dossier de fichiers";
				if($php >= 5){
					$sca = scandir('../'.$dirfiles[$tablo[$tk]]);
					$nbro = '('.(sizeof($sca)-2)." éléments)";
				}
			}			
			mysql_close($conn);
				echo"<tr><td> - <a href='./?$tablo[$tk]&d=$d' class='menuuu'><b>$humanpart</b></a></td>
				<td>$vasaj</td><td>$nbro</td></tr>";
			}
		   }
	}  
	echo"</table> </td></tr></table>";
}	  
echo"<div id='delfilemask' style=\"position:absolute;left:0px;top:0px;width:100%;height:100%;visibility:hidden;background:url('http://www.adeli.wac.fr/vers/$vers/$theme/bgalpha.gif')\">
	 <table style=\"width:100%;height:100%;\">
	 <tr><td  align='center' valign='middle'>	 
	 <table width='300' cellpadding='5'  class='alert'>
	 <tr><td  align='center' valign='middle'>
	 <b>êtes vous sûr de vouloir supprimer maintenant?</b><br><br>	 
	 <table cellspacing='5' cellpadding='0' border='0'>	   <tr>	   
	  <td class=\"buttontd\"><a href=\"./?decon\"><b>oui</b></a></td>	  
	  <td class=\"buttontd\"><a href=\"#\" onclick=\"reconnect()\"><b>non</b></a></td>	  
	  </tr></table>	 
	 </td></tr>
	 </table>	 
	 </td></tr>
	 </table>
	 </div>	 
	 ";
?>