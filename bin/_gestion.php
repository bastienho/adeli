<?php // 161 > Gestion de boutique et commerce ;
if(isset($fournisseurs_db) && isset($_GET['create_fournisseurs_db'])){
	if(!mysql_query("SHOW COLUMNS FROM $fournisseurs_db")){
	if(mysql_query("CREATE TABLE `$fournisseurs_db` (
  `id` bigint(20) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL default '',
  `contact` varchar(255) NOT NULL default '',
  `adresse` text NOT NULL,
  `telephone` varchar(255) NOT NULL default '',
  `fax` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `site` varchar(255) NOT NULL default '',
  `mobile` varchar(255) NOT NULL default '',
  `clon` bigint(20) NOT NULL default '0',
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)") ){
			$return.=returnn("La table <b>\"Fournisseur\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour redémarrer <b>\"$option\"</b></a>","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La table Fournisseur n'a pu être créée correctement","990000",$vers,$theme);
		}
	}
	if(!mysql_query("SHOW COLUMNS FROM `gestion_artfour`")){
	if(mysql_query("CREATE TABLE `gestion_artfour` (
  `id` bigint(20) NOT NULL auto_increment,
  `art` bigint(20) NOT NULL default '0',
  `four` bigint(20) NOT NULL default '0',
  `prix` float(10,2) NOT NULL default '0.00',
  `reference` varchar(255) NOT NULL default '',
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)") ){
			$return.=returnn("La table <b>\"Prix d'achat\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour redémarrer <b>\"$option\"</b></a>","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La table Prix d'achat n'a pu être créée correctement","990000",$vers,$theme);
		}
	}
}


$normal=false;
if($part=="gestion_langue") $tabledb = $langue_db;
elseif($part=="gestion_rayons") $tabledb = $rayons_db;
elseif($part=="gestion_articles") $tabledb = $articles_db;
elseif($part=="gestion_rappel") $tabledb = $rappel_db;
elseif($part=="gestion_commandes" || $part=="gestion_caisse" || $_GET['part']=="gestion_inventaire" || (isset($compta_base) && $part==$compta_base)){
	
}
else{
	$normal=true;
}

if($normal && is_file('bin/_site.php')){
	include('bin/_site.php');
}
else{
$comportement = split(",",$types[$part]);

eval(get_pref("compta.conf","x"));
if(!isset($taxe)){
	$taxe='HT';
}
$taxe_cible = $taxe;

$subpart='';
if(isset($_GET["subpart"]) && $_GET["subpart"]!=""){
	$subpart = $_GET["subpart"];
}

$taille_g_nom = "Taille";
$couleur_g_nom = "Couleur";
$desc_g_nom = 'Desc.';
	
if(isset($gestion_articles_taille_name)) $taille_g_nom=$gestion_articles_taille_name;
if(isset($gestion_articles_couleur_name)) $couleur_g_nom=$gestion_articles_couleur_name;
if(isset($gestion_articles_desc_name)) $desc_g_nom=$gestion_articles_desc_name;


$setopo = array("setvalid","unsetvalid");
$setopot = array("activer cet élément","désactiver cet élément");
$setfoc = array("focus","unfocus");
$setfoct = array("Mettre en avant","Ne plus mettre en avant");

$depth_rayon=0;
if(isset($depth_gestion["rayon"])) $depth_rayon=$depth_gestion["rayon"];
if($part=="" && isset($_GET["part"]) && $_GET["part"]!=""){
	$part = $_GET["part"];
}


$tabledb = $part;

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
function empil($id,$level,$art_rayon,$israyon=false){
	global $rayons_db;
	$we='';
	if($israyon!=false) $we=" AND `id`!='$israyon'";
	$res = mysql_query("SELECT `id` FROM `$rayons_db` WHERE ref='$id' $we");
	if($res && mysql_num_rows($res)>0){
		while($rou=mysql_fetch_array($res)){
			$c='';
			if($rou[0]==$art_rayon) $c='selected';
			if($israyon!=$rou[0]) echo"<option value='$rou[0]' $c>".str_repeat(" - ",$level).get_item_trans($rou[0],"ray")."</option>";
			empil($rou[0],$level+1,$art_rayon,$israyon);
		}
	}
}

/*********************************************************** END FUNC GESTION ****/
if($part != ""){
if( !isset($comportement) || (sizeof($comportement)==1 && $comportement[0]=="") || in_array("txt",$comportement)){	
	
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

		
	
	if(isset($_GET['unsetclon'])){
		if($u_droits == '' || $u_active == 1 ){
			
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
			
			$setvalid = $_GET['setvalid'];
			if( mysql_query("UPDATE `$tabledb` SET `active`='1' WHERE id='$setvalid'") ){
				$return.=returnn("mise en ligne effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise en ligne a échouée","990000",$vers,$theme);
			}
			
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
			
			$unsetvalid = $_GET['unsetvalid'];
			if( mysql_query("UPDATE `$tabledb` SET `active`='0' WHERE id='$unsetvalid'") ){
				$return.=returnn("mise hors ligne effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise hors ligne a échouée","990000",$vers,$theme);
			}
			
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre hors ligne ce texte","990000",$vers,$theme);
		}
		$tabledb=$tmpdb;
	}
	if(isset($_GET['focus'])){
		$tmpdb=$tabledb;
		if(isset($_GET['effdb']) && $_GET['effdb']!=''){
			$tabledb=$_GET['effdb'];
		}
		if($u_droits == '' || $u_active == 1 ){
			
			$focus = $_GET['focus'];
			if( mysql_query("UPDATE `$tabledb` SET `nouveaute`='1' WHERE id='$focus'") ){
				$return.=returnn("Focalisation effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la focalisation a échouée","990000",$vers,$theme);
			}
			
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour focaliser","990000",$vers,$theme);
		}
		$tabledb=$tmpdb;
	}
	if(isset($_GET['unfocus'])){
		$tmpdb=$tabledb;
		if(isset($_GET['effdb']) && $_GET['effdb']!=''){
			$tabledb=$_GET['effdb'];
		}
		if($u_droits == '' || $u_active == 1 ){
			
			$unfocus = $_GET['unfocus'];
			if( mysql_query("UPDATE `$tabledb` SET `nouveaute`='0' WHERE id='$unfocus'") ){
				$return.=returnn("Annulation de focalisation effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("Annulation de focalisation a échouée","990000",$vers,$theme);
			}
			
		}
		else{
			$return.=returnn("Vous n'avez pas les droits annuler de focalisation","990000",$vers,$theme);
		}
		$tabledb=$tmpdb;
	}

	$filepart = ereg_replace(">","-",$part);
	if(file_exists("$filepart.php")){
		echo"<a class='info'><img src='$style_url/$theme/mesure.gif'>
				<span>Partie sur mesure</span></a>";
		
		include("$filepart.php");
		
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
		echo"<form action='./?option=$option&part=$part&$action&edit=$edit' method='post' name='fourmis' enctype='multipart/form-data'  onsubmit=\"affichload()\">";
	}
	
	
	
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
				document.listage.action='./?option=$option&part=$part&subpart=exporter';
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
		
		<table cellspacing='0' cellpadding='3' border='0' width='100%'>";
		
		if($part!='gestion_caisse' && $_GET['part']!='gestion_inventaire'){
			/////////////////////////////// ONGLETS
						echo"<tr style='height:20px;'><td class=\"buttontd\" width=\"10\">&nbsp;</td>";
						
				if($part!="gestion_rappel" && ( !isset($gestion_rappel_base) || $part!=$gestion_rappel_base)){			
						if(!isset($_GET['edit']) && $subpart!='exporter' && $subpart!='statistiques' ){
							echo"<td class=\"menuselected\" width='100'><a href=\"./?option=$option&part=$part\">Liste</a></td>";
							if(!in_array("nonew",$comportement)){echo"<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part&amp;edit\">Nouveau</a></td>";}
							echo"<td class=\"buttontd\" width='100'><a href='#' onclick='exporter()'>Exporter</a></td>
							<td class=\"buttontd\" width='80'><a href=\"./?option=$option&part=$part&subpart=statistiques\">Statistiques</a></td>
							";
						}
						elseif($subpart=='statistiques'){	
							echo"<td class=\"buttontd\" width='80'><a href=\"./?option=$option&part=$part\">Liste</a></td>";
							if(!in_array("nonew",$comportement)){echo"<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part&amp;edit\">Nouveau</a></td>";}
							echo"<td class=\"buttontd\" width='80'><a href='#' onclick='exporter()'>Exporter</a></td>
							<td class=\"menuselected\" width='80'><a href=\"./?option=$option&part=$part&ssubpart=tatistiques\">Statistiques</a></td>
							";
						}
						elseif($subpart=='exporter'){	
							echo"<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part\">Liste</a></td>";
							if(!in_array("nonew",$comportement)){echo"<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part&amp;edit\">Nouveau</a></td>";}
							echo"<td class=\"menuselected\" width='100'><a href='#' onclick='exporter()'>Exporter</a></td>
							<td class=\"buttontd\" width='80'><a href=\"./?option=$option&part=$part&subpart=statistiques\">Statistiques</a></td>
							";
						}
						elseif( ($_GET['edit']=='' || isset($_GET['new'])) && !in_array("nonew",$comportement)){	
							echo"<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part\">Liste</a></td>
							<td class=\"menuselected\" width='100'><a href=\"./?option=$option&part=$part&amp;edit\">Nouveau</a></td>
							<td class=\"buttontd\" width='80'><a href=\"./?option=$option&part=$part&subpart=statistiques\">Statistiques</a></td>
							";
						}
						elseif(isset($_GET['clone']) && !in_array("nonew",$comportement)){	
							echo"<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part\">Liste</a></td>
							<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part&amp;edit\">Nouveau</a></td>
							<td class=\"menuselected\" width='100'><a href=\"./?option=$option&part=$part&amp;edit=$edit&clone\">Clone($edit)</a></td>
							<td class=\"buttontd\" width='80'><a href=\"./?option=$option&part=$part&subpart=statistiques\">Statistiques</a></td>";
						}
						else{
							echo"<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part\">Liste</a></td>";
							if(!in_array("nonew",$comportement)){echo"<td class=\"buttontd\" width='100'><a href=\"./?option=$option&part=$part&amp;edit\">Nouveau</a></td>";}
							echo"<td class=\"menuselected\" width='100'><a href=\"./?option=$option&part=$part&amp;edit=$edit\">&Eacute;dition ($edit)</a></td>
							<td class=\"buttontd\" width='80'><a href=\"./?option=$option&part=$part&subpart=statistiques\">Statistiques</a></td>";
						}	
				}
				else{
						echo"<td class=\"buttontd\" width='100'><a href='#' onclick='exporter()'>Exporter</a></td>";
				}
						echo"<td class=\"buttontd\" align='left'><p align='left'>&nbsp;";
						
						if(!isset($_GET['edit'])){	
						
							if(isset($_GET['al'])){
								set_pref("list.$part.conf",$_GET['al']);
							}
							else{
							 if(get_pref("list.$part.conf")==''){
									set_pref("list.$part.conf",'l');
								}
							}
							$al = get_pref("list.$part.conf");
							if($al==""){
								$al = "l";
							}
						
						if($al==="i"){
							echo"
							<a href='./?option=$option&part=$part&al=l'><img src='$style_url/$theme/view-list.png' border='none' alt='affichage liste'></a>
							<a href='./?option=$option&part=$part&al=i'><img src='$style_url/$theme/view-icon.png' border='1' alt='affichage icônes'></a>";
						}
						else{
							echo"
							<a href='./?option=$option&part=$part&al=l'><img src='$style_url/$theme/view-list.png' border='1' alt='affichage liste'></a>
							<a href='./?option=$option&part=$part&al=i'><img src='$style_url/$theme/view-icon.png' border='none' alt='affichage icônes'></a>";
						}
						}
						echo"</td></tr>";
		}
		echo"
		<tr><td align='center' colspan='8' class='cadrebas'>";
		
	
	if(!isset($_GET['edit'])){	
	for ($i = 0; $i < $columns; $i++) {
		$field_name = mysql_field_name($res_field, $i);
		$fieldoption = substr($field_name,strpos($field_name,"_")+1,strlen($field_name));
		if(substr($field_name,0,1) == "_"){
			$field_named = substr($field_name,1,strlen($field_name));
			echo"$field_named<select onchange=\"javascript:document.location='./?option=$option&part=$part&s=$field_name&v='+this.value\" style=\"width:100px\">
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

				echo"$nameifthefield<select onchange=\"javascript:document.location='./?option=$option&part=$part&s=$field_name&v='+this.value\" style=\"width:100px\">
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
	}
	if($part=="gestion_articles"){	
		 $autoref = abs(get_pref("ga.autoref.conf"));
		 $visus = array('','checked');
		 $vis = $visus[$autoref];
		if(isset($_GET['autoref'])){ 
			$autoref=$_GET['autoref'];
			if(!set_pref("ga.autoref.conf",$autoref)){
				$return.=returnn("Enregistrement de vos préférences échoué","FF6600",$vers,$theme);
			}
			$vis = $visus[$autoref];
			
		}
		echo"<input type='checkbox' name='autoref' $vis onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&autoref=".(abs($autoref-1))."&refresh=1';document.fourmis.submit();\"> Essayer de remplir automatiquement les champs ";
	}

			
		echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'><tr><td valign='top'>";
	
if(isset($_GET['edit']) || $part=="gestion_rappel"|| $part=="gestion_caisse"|| $_GET['part']=="gestion_inventaire" || ( isset($gestion_rappel_base) && $part==$gestion_rappel_base)){		

if(isset($_GET['new'])){
	$edit='';
}	
		
	$res = mysql_query("SELECT * FROM `$tabledb` WHERE `id`='$edit'");
	$ro = mysql_fetch_object($res);
	echo"<table>";
	
	if( $part=="gestion_commandes" || ( isset($compta_base) && $part==$compta_base) ){
		insert("_gestion_commandes");
		if(is_file("bin/_gestion_commandes.php")){
			include("bin/_gestion_commandes.php");
		}
		else{
			include("$style_url/update.php?file=_gestion_commandes.php");
		}
	}
	elseif( $part=="gestion_caisse"){
		insert("_gestion_caisse");
		if(is_file("bin/_gestion_caisse.php")){
			include("bin/_gestion_caisse.php");
		}
		else{
			include("$style_url/update.php?file=_gestion_caisse.php");
		}
	}
	elseif( $part=="gestion_clients" || ( isset($gestion_clients_base) && $part==$gestion_clients_base) ){
		insert("_gestion_clients");
		if(is_file("bin/_gestion_clients.php")){
			include("bin/_gestion_clients.php");
		}
		else{
			include("$style_url/update.php?file=_gestion_clients.php");
		}
	}
	elseif( $part=="gestion_rayons" || ( isset($gestion_rayons_base) && $part==$gestion_rayons_base) ){
		insert("_gestion_rayons");
		if(is_file("bin/_gestion_rayons.php")){
			include("bin/_gestion_rayons.php");
		}
		else{
			include("$style_url/update.php?file=_gestion_rayons.php");
		}
	}
	elseif( $part=="gestion_langue" || ( isset($gestion_langue_base) && $part==$gestion_langue_base) ){
		insert("_gestion_langue");
		if(is_file("bin/_gestion_langue.php")){
			include("bin/_gestion_langue.php");
		}
		else{
			include("$style_url/update.php?file=_gestion_langue.php");
		}
	}
	elseif( $part=="gestion_articles" || ( isset($gestion_articles_base) && $part==$gestion_articles_base) ){
		insert("_gestion_articles");
		if(is_file("bin/_gestion_articles.php")){
			include("bin/_gestion_articles.php");
		}
		else{
			include("$style_url/update.php?file=_gestion_articles.php");
		}
	}
	elseif( $part=="gestion_rappel" || ( isset($gestion_rappel_base) && $part==$gestion_rappel_base) ){
		insert("_gestion_rappel");
		if(is_file("bin/_gestion_rappel.php")){
			include("bin/_gestion_rappel.php");
		}
		else{
			include("$style_url/update.php?file=_gestion_rappel.php");
		}
	}
	elseif($_GET['part']=='gestion_inventaire'){	
		insert('_gestion_inventaire');
		if(is_file("bin/_gestion_inventaire.php")){
			include("bin/_gestion_inventaire.php");
		}
		else{
			include("$style_url/update.php?file=_gestion_inventaire.php");
		}
	}
   
	   echo"</td></tr><tr><td colspan='2'>";
	   if($part!='gestion_caisse' && $part!='gestion_inventaire'){
		    if($part!="gestion_rappel" && ( !isset($gestion_rappel_base) || $part!=$gestion_rappel_base)){
					  	
				   echo"
				   	 <p><input class=\"grosbouton\" type=\"submit\" value=\"Enregistrer\"></p><br/>
						<input class=\"buttontd\" type=\"button\" value=\"annuler\" onclick=\"document.location='./?option=$option&part=$part';\">
						&nbsp;	&nbsp;	&nbsp;	&nbsp;	";
						
						if($u_droits == '' || $u_dgw == 1 ){
							echo"
						
					<input class=\"buttontd\" type=\"button\" value=\"enregistrer et revenir\" onclick=\"document.fourmis.action='./?option=$option&part=$part&$action=$edit';document.fourmis.submit()\"> ";
					if( !in_array("nonew",$comportement)){	
					echo"<input class=\"buttontd\" type=\"button\" value=\"enregistrer et ajouter\" onclick=\"document.fourmis.action+='&new';document.fourmis.submit()\"> ";
					}
					//echo"<input class=\"buttontd\" type=\"submit\" value=\"enregistrer\">";
				}
					
			}
	   }
	   ?>
       
       <script type="text/javascript" src="http://adeli.wac.fr/vers/<?=$vers?>/tiny_mce/tiny_mce.js"></script>
           
		<script language="javascript" type="text/javascript">
		
		tinyMCE.init({
				theme : "advanced",
				skin : "o2k7",
				language : "fr",
				plugins : "table,contextmenu,paste,-externalplugin",
				mode : "specific_textareas",
        		editor_selector : "editor",
				imagemanager_contextmenu: false,
				document_base_url : "http://<?=$serv?>/",
				theme_advanced_toolbar_location : "top",
   				theme_advanced_toolbar_align : "center",
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull",
				theme_advanced_buttons2 : "removeformat,cleanup,separator,sub,sup,separator,unlink,link,forecolor,backcolor",
				theme_advanced_buttons3 : ""
				
		});
		</script>
       <?php
	
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
	<form action='./?option=$option&part=$part&alert&l=$edit' method='post'>
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
				<div class=\"buttontd\" style='width:140px'><a href=\"./?option=$option&part=$part&edit=$l\">retour à l'article</a></div>
				<div class=\"buttontd\" style='width:140px'><a href=\"./?option=$option&part=$part\">retour à la liste</a></div>
			</td></tr>
		
		
		</table>
		
		</td></tr>
		</table>
	";
}
/********************************************************************************************************************

									STATISTIQUES

**********************************************************************************************************************/

elseif($subpart=="statistiques"){	
	$tabledb = $part;
	insert('_statistiques');
	if(is_file("bin/_statistiques.php")){
		include("bin/_statistiques.php");
	}
	else{
		include("$style_url/update.php?file=_statistiques.php");
	}
}

/********************************************************************************************************************

									EXPORTER

**********************************************************************************************************************/

elseif($subpart=="exporter"){	
	insert('_exporter');
	if(is_file("bin/_exporter.php")){
		include("bin/_exporter.php");
	}
	else{
		include("$style_url/update.php?file=_exporter.php");
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
	<form name='listage' action='./?option=$option&part=$part' method='post'>
	<table width='100%' cellspacing='1' cellpadding='0' border='0'><tr>
	<td align='left'><input type='checkbox' onclick='sela(this.checked)'>
	 -	
	<a href='#' onclick=\"conmulti('active')\"><img src='$style_url/$theme/v1.gif' border='none' alt='activer'></a>
	<a href='#' onclick=\"conmulti('desactive')\"><img src='$style_url/$theme/v0.gif' border='none' alt='désactiver'></a>
	<a href='#' onclick=\"conmulti('delete')\"><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>
	</td>
	<td align='right'>
		n'afficher que les actifs ";
		if($affdesac==0){
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=1'\">";
		}
		else{
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=0'\" checked>";
			$incwhere.=" AND `active`=1";
		}		
		echo"
		<td>
	</tr>
	<tr><td colspan='2'>
	
"; 
	if( $part=="gestion_articles" || ( isset($gestion_articles_base) && $part==$gestion_articles_base) ){
		echo"
		<a href='./?option=$option&part=gestion_inventaire'>Inventaire</a>
		";
	}
	
		insert("inc_gliste");
		if(is_file("bin/inc_gliste.php")){
			include("bin/inc_gliste.php");
		}
		else{
			include("$style_url/update.php?file=inc_gliste.php");
		}
	echo"
		</td></tr>
		</table>
		</form>";
	

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
				include("$style_url/update.php?file=inc_dir.php");
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
			//if( ($option=="site" && substr($spart,0,7)!="gestion" && substr($spart,0,7)!="worknet") || (($option==substr($spart,0,7) && substr($spart,0,7)=="worknet") || ($option==substr($spart,0,7) && substr($spart,0,7)=="gestion")) ){		
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
					$vasaj = " | <a href='./?option=$option&$tablo[$tk]&edit'>nouveau</a>";
				}
				if( !isset($comportement) || in_array("txt",$comportement) || !in_array("dir",$comportement) ){	
					$vasaj .= " | <a href='./?option=$option&$tablo[$tk]&exporter'>exporter</a>   | <a href='./?option=$option&$tablo[$tk]&statistiques'>statistiques</a>";
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
			
				echo"<tr><td> - <a href='./?option=$option&$tablo[$tk]&d=$d' class='menuuu'><b>$humanpart</b></a></td>
				<td>$vasaj</td><td>$nbro</td></tr>";
			}
		  // }
	}  
	echo"</table> </td></tr></table>";
}	  
echo"<div id='delfilemask' style=\"position:absolute;left:0px;top:0px;width:100%height:100%;visibility:hidden;background:url('$style_url/$theme/bgalpha.gif')\">
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
}
?>