<?php // 280 > Gestion des articles ;
$legal_entrys = array(id,clon,rayon,nouveaute,conseil,modification,active);

insert('_ean');
if(is_file('bin/_ean.php')){
	$openean='bin/_ean.php?1';
}
else{
	$openean="http://www.adeli.wac.fr/vers/$vers/update.php?file=_ean.php&1";
}

		$art_rayon = $ro->rayon;
		$art_nouveaute = abs($ro->nouveaute);
		$art_conseil = $ro->conseil;
		$art_modification = $ro->modification;
		$art_active = abs($ro->active);
		
		

if($edit=="" && isset($_GET["ray"])){
	$art_rayon=$_GET["ray"];
}	
if((isset($_GET['update'])||isset($_GET['refresh'])) && $_FILES['file']['name'][0] !=''){
	if(addfile("../gestion_articles/$edit.jpg", $_FILES['file']['name'][0], $_FILES['file']['tmp_name'][0], $dangerous)){
		$return.=returnn("aperçu chargé avec succès","009900",$vers,$theme);
	}
	else{
		$return.=returnn("aperçu n'a pu être chargé correctement","990000",$vers,$theme);
	}
}
if((isset($_GET['update'])||isset($_GET['refresh'])) && $_FILES['file']['name'][1] !=''){
	if(addfile("../gestion_articles/g$edit.jpg", $_FILES['file']['name'][1], $_FILES['file']['tmp_name'][1], $dangerous)){
		$return.=returnn("deuxième vue chargée avec succès","009900",$vers,$theme);
	}
	else{
		$return.=returnn("deuxième vue n'a pu être chargée correctement","990000",$vers,$theme);
	}
}
if(isset($fichiers[$part]) && (isset($_GET['update'])||isset($_GET['refresh']))){		//////////////////////////////////////////// 			CUSTOM FILES	
	$return.=returnn("chargement personnalisé","FF9900",$vers,$theme);
	$custom_files = $fichiers[$part];
	$custom_keys = array_keys($custom_files);
	$i=0;
	while($i<sizeof($custom_keys)){
		$r=$i+2;
		$custom_name = $custom_keys[$i];
		$custom_dir = $custom_files[$custom_name][0];
		$custom_file = $custom_files[$custom_name][1];
		if($_FILES['file']['name'][$r] !=''){
			if(addfile($custom_dir."/".$custom_file, $_FILES['file']['name'][$r], $_FILES['file']['tmp_name'][$r], $dangerous)){
				$return.=returnn($custom_name." chargé avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn($custom_name." n'a pu être chargé correctement","990000",$vers,$theme);
			}
		}
		$i++;
	}
}

		$actouno = array("","checked");
		$actoudos = array("checked","");
		//`id`, `ref`, `lng`, `nom`, `desc`, `plus1`, `plus2`
	  echo"<tr>
	  <td valign='top' style='border-width:0px;border-right-width:1px;border-color:#CCCCCC;border-style:solid'>
	  
	  <script language='javascript' type='text/javascript'>
	 	function addinref(koi,addf,notforall){
			var fa='forall';
			if(notforall) fa='';
			if(!addf) addf = prompt(\"Veuillez saisir le nom de votre nouvelle \"+koi+\"\\ndans le champs ci-dessous\",'');
			if(addf){
				document.fourmis.action='./?$part&edit=$edit&update&'+fa+'&addref='+koi+'&val='+addf+'#_'+addf;
				document.fourmis.submit();
			}
		}
	 
		   function confsupt(id,foc){
			is_confirmed = confirm('êtes vous sûr de vouloir supprimer définitivement cette ligne ?');
			if (is_confirmed) {
				document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&delref='+id+'#f'+foc;
				document.fourmis.submit();
			}
		   }
		   function confsupa(id,foc){
			is_confirmed = confirm('êtes vous sûr de vouloir supprimer définitivement cette entrée ?');
			if (is_confirmed) {
				document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&fours='+foc+'&delach='+id+'#four'+foc;
				document.fourmis.submit();
			}
		   }
		   
		   function confsupf(ki,koi){
			is_confirmed = confirm(\"êtes vous sûr de vouloir supprimer définitivement\\n l'ensemble des \"+ki+\" : \"+koi+\" ?\");
			if (is_confirmed) {
				document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&delens='+ki+'&ref='+koi+'#chif';
				document.fourmis.submit();
			}
		   }
		</script>
	";
////////////////////////////////////////////////////////////////// GENEARLE
	
	echo"	  <b>Généralités</b>
	  <br><br>
	  <input type='hidden' name='modification' value='$mysqlnow'>
	  	<u>focus</u>: <br>
		<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>
			oui<input type=\"radio\" name=\"nouveaute\" value=\"1\" $actouno[$art_nouveaute]>
			non<input type=\"radio\" name=\"nouveaute\" value=\"0\" $actoudos[$art_nouveaute]>
	  <br><br>
	<u>activé</u>:<br>
		";
		if($u_droits == '' || $u_active == 1 ){
		 echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>
			oui<input type=\"radio\" name=\"active\" value=\"1\" $actouno[$art_active]>
			non<input type=\"radio\" name=\"active\" value=\"0\" $actoudos[$art_active]>
		 ";
		}
		else{
		 echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>sans droits<input type=\"hidden\" name=\"active\" value=\"0\">";
		}
	 echo"
	<br><br>
	<u>classement</u>:<br>";
	$ray_path=array();
	function empil($id){
		global $ray_path;
		global $rayons_db;
		array_push($ray_path,$id);
		$res = mysql_query("SELECT `ref` FROM `$rayons_db` WHERE id='$id'");
		if($res && mysql_num_rows($res)==1){
			$rou=mysql_fetch_object($res);
			empil($rou->ref);
		}
	}
	empil($art_rayon);
	if(!in_array(0,$ray_path)) array_push($ray_path,0);
	$ray_path=array_reverse($ray_path);
	$seled=0;

	for($i=0 ; $i<sizeof($ray_path)-1 ; $i++){
		$val = $ray_path[$i];
		$parent = $ray_path[$i-1];
		$res = mysql_query("SELECT `id` FROM `$rayons_db` WHERE ref='$val'");
		if($res && mysql_num_rows($res)>0){
			echo"<select onchange='document.fourmis.rayon.value=this.value'>
			<option value='$val'>sous-rayon de : ".get_item_trans($val,"ray")."</option>";
			while($rou=mysql_fetch_object($res)){
				$rid=$rou->id;
				$s="";
				if(isset($ray_path[$i+1]) && $rid==$ray_path[$i+1]){ $s="selected"; $seled=$rid; }
				echo"<option value='$rid' $s>".get_item_trans($rid,"ray")."</option>";
			}
			echo"</select><br>";
		}
	}
	$res = mysql_query("SELECT `id` FROM `$rayons_db` WHERE ref='$art_rayon'");
		if($res && mysql_num_rows($res)>0){
			echo"<hr>placer dans un sous rayon...<br>
			<select onchange='document.fourmis.rayon.value=this.value'>
				<option value='$art_rayon'>sous-rayon de : ".get_item_trans($art_rayon,"ray")."</option>";
			while($rou=mysql_fetch_object($res)){
				$rid=$rou->id;
				echo"<option value='$rid'>".get_item_trans($rid,"ray")."</option>";
			}
			echo"</select><br>";
		}	
	
	echo"<hr>
	<a href='./?gestion_rayons&edit=$art_rayon'>accéder au rayon</a><br><br>
	<input type='hidden' name='rayon' value='$art_rayon'>	<input type='submit' value='enregistrer'>	
	</td>
	";
////////////////////////////////////////////////////////////////// TRADUCTION
	
	echo"
	<td valign='top' style='border-width:0px;border-right-width:1px;border-color:#CCCCCC;border-style:solid'>
	<b>Appelation</b><br>";
	
	  $ris = mysql_query("SELECT * FROM `$langue_db`");
		if($ris && mysql_num_rows($ris)>0){
			while($riw=mysql_fetch_object($ris)){
				$lng_code = $riw->code;
				$lng_nom = $riw->nom;
				if( ( isset($_GET['update'])|| isset($_GET['add'])) && isset($_POST["trad_nom_$lng_code"]) && is_numeric($edit)){
					$lng_nome=ereg_replace("'","''",$_POST["trad_nom_$lng_code"]);
					$lng_dese=ereg_replace("'","''",$_POST["trad_des_$lng_code"]);
					$lng_pl1e=ereg_replace("'","''",$_POST["trad_pl1_$lng_code"]);
					$lng_pl2e=ereg_replace("'","''",$_POST["trad_pl2_$lng_code"]);
					if(mysql_query("UPDATE `gestion_artrad` SET `nom`='$lng_nome',`desc`='$lng_dese',`plus1`='$lng_pl1e',`plus2`='$lng_pl2e' WHERE `ref`='$edit' AND `lng`='$lng_code'")){
						$return.=returnn("mise à jour de traduction \"$lng_code\" effectuée avec succès","009900",$vers,$theme);
					}
					else{
						$return.=returnn("mise à jour de traduction \"$lng_code\" échouée","990000",$vers,$theme);
					}
				}
				$ras = mysql_query("SELECT * FROM `gestion_artrad` WHERE `ref`=$edit AND `lng`='$lng_code'");
				if($ras && mysql_num_rows($ras)==1){
					$raw=mysql_fetch_object($ras);
					$lng_nom_val=$raw->nom;
					$lng_des_val=$raw->desc;
					$lng_pl1_val=$raw->plus1;
					$lng_pl2_val=$raw->plus2;
				}
				elseif(is_numeric($edit)){
					if(mysql_query("INSERT INTO `gestion_artrad` VALUES('', '$edit', '$lng_code', '$lng_nome', '$lng_dese', '$lng_pl1e', '$lng_pl2e')")){
						$return.=returnn("création de traduction \"$lng_code\" effectuée avec succès","009900",$vers,$theme);
					}
					else{
						$return.=returnn("création de traduction \"$lng_code\" échouée","990000",$vers,$theme);
					}
					$lng_nom_val="";
					$lng_des_val="";
					$lng_pl1_val="";
					$lng_pl2_val="";
				}
				echo"<br>		
				<a onclick=\"sizpa('tr_$lng_code')\" style='cursor:pointer'><b>$lng_nom</b></a>
				<div id='tr_$lng_code' style='width:385px;height:5px;overflow:hidden'>
				<table class='cadre' style='float:left;width:380px;height:130px;margin:2px;'>
				<tr><td>nom:</td>
					<td><input type=\"texte\" name=\"trad_nom_$lng_code\" value=\"$lng_nom_val\" size=\"30\" style='width:270px;'></td>
				</tr>
				<tr><td>description:</td>
					<td><textarea name=\"trad_des_$lng_code\"  style='width:270px;height:70px;'>$lng_des_val</textarea></td>
				</tr>
				<tr><td>complément 1 :</td>
					<td><input type=\"texte\" name=\"trad_pl1_$lng_code\" value=\"$lng_pl1_val\" size=\"30\" style='width:270px;'></td>
				</tr>
				<tr><td>complément 2:</td>
					<td><input type=\"texte\" name=\"trad_pl2_$lng_code\" value=\"$lng_pl2_val\" size=\"30\" style='width:270px;'></td>
				</tr>
				</table></div>";
			}
		}	  
	  echo"</td>
	  <td valign='top' style='border-width:0px;border-right-width:1px;border-color:#CCCCCC;border-style:solid'>
	";
////////////////////////////////////////////////////////////////// IMAGE
	
	echo"	<b>Aperçu</b><br>";
	if($edit!=""){
			if(is_file("../gestion_articles/$edit.jpg")){
				echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
				<img src='./?incpath=_ima.php&file=gestion_articles/$edit.jpg' alt='icone' height='100'><br>
				<a href=\"#\" onclick=\"delfile('</gestion_articles/$edit.jpg')\">
				<img src=\"http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif\" border='none' alt='supprimer'></a>
				</td></tr></table>";
			}
			echo"<input type='file' name='file[0]'><br>
				<b>Deuxième vue</b><br>";
			if(is_file("../gestion_articles/g$edit.jpg")){
				echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
				<img src='./?incpath=_ima.php&file=gestion_articles/g$edit.jpg' alt='icone' height='100'><br>
				<a href=\"#\" onclick=\"delfile('</gestion_articles/g$edit.jpg')\">
				<img src=\"http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif\" border='none' alt='supprimer'></a>
				</td></tr></table>";
			}
			echo"<input type='file' name='file[1]'>";
	}
	else{
		echo"le chargement d'une image sera possible après un premier enregistrement";
	}
	echo"<hr>
			<input type='submit' value='enregistrer'></td>
	 </tr>
	 <tr><td colspan='3'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>
	";
////////////////////////////////////////////////////////////////// CHIFFRES
	if(is_numeric($edit)){
	echo"	 <tr><td colspan='3'>
	 <b>Références</b><br>
	 ";
	 $assoclas = array("col"=>"couleur","tai"=>"taille");
	 $assoclasd = array("tai"=>"couleur","col"=>"taille");
	 $rang_names = array('taille'=>$taille_g_nom,'couleur'=>$couleur_g_nom);
	 
	 $clasg = array_flip($assoclas);
	 $fp = @fopen("mconfig/$u_id.gestion.articles.classpar","rb");
	 $claspar = trim(@fread($fp,10));
	 //echo $claspar;
	 @fclose($fp);
	 if(isset($_GET['clp']) && ($_GET['clp']=='tai' || $_GET['clp']=='col')){
	 	 $claspar=$_GET['clp'];
		 $fp = @fopen("mconfig/$u_id.gestion.articles.classpar","w+");
		 fwrite($fp,$claspar);
		 @fclose($fp); 
	 }
	 if($claspar=="") $claspar="tai";
	 $clasnom = $assoclas[$claspar];
	 $clasoth = $assoclasd[$claspar];
	 $getoth = $clasg[$clasoth];
	 
	 
///////////////////////////////////////////////////// MODIF REF	 
	 if(isset($_GET['delref']) ){
		if(deletefromdb($base,"gestion_artstock",$_GET['delref'])){
			$return.=returnn("suppression effectuée avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("la suppression a échouée","990000",$vers,$theme);
		}
	 }
	 if(isset($_GET['delach']) ){
		if(deletefromdb($base,"gestion_artfour",$_GET['delach'])){
			$return.=returnn("suppression effectuée avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("la suppression a échouée","990000",$vers,$theme);
		}
	 }
	 if(isset($_GET['delens']) && in_array($_GET['delens'],$assoclas) && isset($_GET['ref']) && $_GET['ref']!=''){
	 	$delens = $_GET['delens'];
		$ref = $_GET['ref'];
		$res = mysql_query("SELECT * FROM `gestion_artstock` WHERE `ref`='$edit' AND `$delens`='".str_replace("'","''",$ref)."'");
		 while($rou=mysql_fetch_object($res)){
		 	$s_id = $rou->id;
			if(deletefromdb($base,"gestion_artstock",$s_id)){
				$return.=returnn("suppression effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la suppression a échouée","990000",$vers,$theme);
			}
		}
		
	 }

	if(isset($_GET['update'])){
		$res = mysql_query("SELECT `id` FROM `gestion_artstock` WHERE `ref`='$edit'");
		while($rou=mysql_fetch_object($res)){
			$s_id = $rou->id;
			if(updatedb($base,"gestion_artstock",$s_id,$s_id)){
				$return.=returnn("modification de chiffre effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la modification de chiffre a échouée","990000",$vers,$theme);
			}			
			if(isset($fournisseurs_db) &&  mysql_query("SHOW COLUMNS FROM $fournisseurs_db") && mysql_query("SHOW COLUMNS FROM `gestion_artfour`") ){
				$ref = mysql_query("SELECT `id` FROM `gestion_artfour` WHERE `art`='$s_id'");
				if($ref && mysql_num_rows($ref)>0){
					while($fo=mysql_fetch_array($ref)){
						if(updatedb($base,"gestion_artfour",$fo[0],"pa_$fo[0]")){
							$return.=returnn("modification de prix d'achat effectuée avec succès","009900",$vers,$theme);
						}
						else{
							$return.=returnn("la modification de prix d'achat a échouée","990000",$vers,$theme);
						}
					}
				}
			}
		}
	}
	
	if(isset($_GET['addref'])){
		$addref = $_GET['addref'];
		$val = $_GET['val'];
		$quiquequoi = "taille";
		if($addref=="taille") $quiquequoi = "couleur";
		$allkoi=array();
		
		if(isset($_GET['forall'])){
			 $res = mysql_query("SELECT DISTINCT `$quiquequoi` FROM `gestion_artstock` WHERE `ref`='$edit' $plr");
			 if($res && mysql_num_rows($res)>0){
				while($rou=mysql_fetch_object($res)){
					array_push($allkoi,$rou->$quiquequoi);
				}
			 }
		}
		else{
			$allkoi[0]='';	
		}
		 if(is_numeric($edit)){
			  for($i=0 ; $i<sizeof($allkoi) ; $i++){
				$ligne = $allkoi[$i];
				if(!mysql_query("INSERT INTO `gestion_artstock` (`ref`,`$addref`,`$quiquequoi`,`active`) VALUES ($edit,'$val','$ligne',1)")){
					$return.=returnn("impossible d'insérer la ligne $val...","990000",$vers,$theme);
				}
				else{
					$return.=returnn("insertion de $val...","009900",$vers,$theme);
				}
			 }
		 }
	}
	if(isset($_GET['addfour']) && isset($_POST['selfour_'.$_GET['addfour']])){
		$addfour = $_GET['addfour'];
		$four = $_POST['selfour_'.$addfour];
		if(!mysql_query("INSERT INTO `gestion_artfour` (`art`,`four`,`active`) VALUES ('$addfour','$four',1)")){
			$return.=returnn("impossible d'insérer la ligne fournisseur $four...","990000",$vers,$theme);
		}
	}
	elseif(isset($_GET['addfour']) && $_GET['addfour']=='all'){
		$res = mysql_query("SELECT `id` FROM `gestion_artstock` WHERE `ref`='$edit'");
		 while($rou=mysql_fetch_array($res)){
		 	$s_id = $rou[0];
			if(isset($_POST["selfour_$s_id"]) && $_POST["selfour_$s_id"]!=NULL){
				$four = $_POST["selfour_$s_id"];
				if(!mysql_query("INSERT INTO `gestion_artfour` (`art`,`four`,`active`) VALUES ('$s_id','$four',1)")){
					$return.=returnn("impossible d'insérer la ligne fournisseur $four...","990000",$vers,$theme);
				}	
			}
		}
	}
///////////////////////////////////////////////////// CHANGE LES LES FAMILLES 
	if(isset($_GET['update'])){
	 $allkoi=array();
	 $res = mysql_query("SELECT DISTINCT `$clasnom` FROM `gestion_artstock` WHERE `ref`='$edit'");
	 if($res && mysql_num_rows($res)>0){
	 	while($rou=mysql_fetch_object($res)){
			array_push($allkoi,$rou->$clasnom);
		}
	 }
	 for($i=0 ; $i<sizeof($allkoi) ; $i++){
	 	$ligne = $allkoi[$i];
		if(isset($_POST["$clasnom$i"]) && $_POST["$clasnom$i"]!=$ligne && !isset($_GET['addref'])){
			if(!mysql_query("UPDATE `gestion_artstock` SET `$clasnom`='".str_replace("'","''",$_POST["$clasnom$i"])."' WHERE `ref`='$edit' AND `$clasnom`='".str_replace("'","''",$ligne)."'")){			
				$return.=returnn("modification de la famille $clasnom: $ligne échouée...","990000",$vers,$theme);
			}
		}
	 }
	}
	 
	 
///////////////////////////////////////////////////// LISTE LES FAMILLES (FINAL)	 
	 $allkoi=array();
	 $res = mysql_query("SELECT DISTINCT `$clasnom` FROM `gestion_artstock` WHERE `ref`='$edit'");
	 if($res && mysql_num_rows($res)>0){
	 	while($rou=mysql_fetch_object($res)){
			array_push($allkoi,$rou->$clasnom);
		}
	 }
	 
	 if(sizeof($allkoi)==0 && is_numeric($edit)){
	 	if(!mysql_query("INSERT INTO `gestion_artstock` (`ref`,`couleur`,`taille`,`active`) VALUES ($edit,'','',1)")){
			$return.=returnn("impossible de gérer les chiffres...","990000",$vers,$theme);
		}
	 }
	 	$verifupdt = mysql_query("DESC `gestion_artstock`");
		$allchamps = array();
		while($roi = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$roi->Field);
		}
		if(!in_array("libre",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `libre` varchar(255) NOT NULL default ''");
		}
		if(!in_array("ean",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `ean` varchar(13) NOT NULL default ''");
		}
		if(!in_array("active",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `active` int(1) NOT NULL default '0'");
		}
		if(!in_array("prix_pro",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `prix_pro` float(10,2) NOT NULL default '0'");
		}
		if(!in_array("promo_pro",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `promo_pro` float(10,2) NOT NULL default '0'");
		}
	 //`id`, `ref`, `ordre`, `code`, `ean`, `couleur`, `taille`, `prix`, `tva`, `promo`, `stock`, `active`
	  echo"<hr>
	 <table cellspacing='0' cellpadding='2' width='700' id='stoc'>
	 <tr><td colspan='13' align='left'>";
	 if(isset($fournisseurs_db)){
		 if(mysql_query("SHOW COLUMNS FROM $fournisseurs_db") && mysql_query("SHOW COLUMNS FROM `gestion_artfour`") ){
			 echo"
			 <a class='buttontd' style='cursor:pointer' onclick=\"document.getElementById('achafour').style.visibility='visible';\">Achats / fournisseurs</a>";
			 $fourtruc='';
			 $ref = mysql_query("SELECT `id`,`nom` FROM `$fournisseurs_db` ORDER BY `nom`");
			 if($ref && mysql_num_rows($ref)>0){
				$foursel="<select name='FOUR_NAME'><option value=''></option>"; 
				while($fo=mysql_fetch_array($ref)){
					$foursel.="<option value='$fo[0]'>".ucfirst($fo[1])."</option>"; 	
				}
				$foursel.="</select>"; 
			 }
		 }
		 else{
			 echo"<a class='buttontd' href='./?option=$option&part=$part&edit=$edit&create_fournisseurs_db=1'>Installer le module achats / fournisseurs</a>";
		 }
	 }
	 echo"</td></tr>";
	 if($taxe_cible=='HT'){
		 echo"
		 <tr class='buttontd'>
			<td colspan='8' align='right'>Particuliers</td>
			<td colspan='2' class='cadre' style='border-bottom-width:0px'>PRO</td>
			<td colspan='3'></td>
		 </tr>";
	 }
	 else{
		 echo"
		 <tr class='buttontd'>
			<td colspan='6'></td>
			<td colspan='2' class='cadre' style='border-bottom-width:0px'>Particuliers</td>
			<td colspan='5' align='left'>PRO</td>
		 </tr>";
	 }
	 echo"
	 <tr class='buttontd'>
	 	<td width='10'><b>".ucfirst($rang_names[$clasnom])."</b></td>
		<td width='10'>^v</td>
		<td>Référence</td>
		<td>Code barre</td>
		<td><a href='./?$part&edit=$edit&clp=$getoth' name='chif'><u>".ucfirst($rang_names[$clasoth])."</u></a></td>
		<td>$desc_g_nom</td>";
	 if($taxe_cible=='HT'){
		 echo"
		<td>Prix (€)</td>
		<td>Promo. (€)</td>
		<td class='cadre' style='border-width:0px;border-left-width:1px'>Prix(€)</td>
		<td class='cadre' style='border-width:0px;border-right-width:1px'>Promo. (€)</td>";
	 }
	 else{
		 echo"
		<td class='cadre' style='border-width:0px;border-left-width:1px'>Prix (€)</td>
		<td class='cadre' style='border-width:0px;border-right-width:1px'>Promo. (€)</td>
		<td>Prix(€)</td>
		<td>Promo. (€)</td>";
	 }
	 echo"
		
		<td>TVA(%)</td>
		<td>Stock</td>
		<td width='60'>";
		if($u_droits == '' || $u_active == 1 ){
			echo"
			<script language='javascript'>
	function conmulti(k){
		var transk = new Array();
		transk['active']='activer';
		transk['desactive']='désactiver';
		transk['delete']='supprimer';
		nbsel=0;
		posch='';
		var allche = document.getElementById('stoc').getElementsByTagName(\"input\");
		var selche=new Array();
		for (var i=2; i<allche.length; i++) {
			if(allche[i].type == 'checkbox' && allche[i].checked==true){
				nbsel++;
				posch+='&stch'+allche[i].value+'=1';
				selche.push(allche[i].value);
			}
		}
		if(nbsel>0){
			pro = confirm(\"êtes vous certain de vouloir \"+transk[k]+\" les \"+nbsel+\" objets sélectionnés ?\");
			if(pro){
				envht=envoyer('bin/inc_ajax.php?scan=gestion_artstock','w','&multi=$edit&action='+k+''+posch);
				if(envht===false){
					alert('erreur');
				}
				else{
					if(envht=='0'){
						for (var i=0; i<selche.length; i++) {
							if(k=='delete') document.getElementById('tr'+selche[i]).innerHTML='';
							if(k=='active') document.getElementById('img'+selche[i]).src='http://www.adeli.wac.fr/vers/$vers/$theme/v1.gif';
							if(k=='desactive') document.getElementById('img'+selche[i]).src='http://www.adeli.wac.fr/vers/$vers/$theme/v0.gif';
						}	
					}
				}
			}
		}
		else{
			alert(\"aucun objet n'est sélectionné\");
		}
	}
	</script>
			
			<input type='checkbox' name='all$i$c' onclick=\"tout(document.getElementById('stoc'),this)\">&nbsp;<a href='#suppall' name='suppall' onclick=\"conmulti('delete')\"><img src='http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif' border='none' alt='supprimer'></a>&nbsp;<a href='#suppall' onclick=\"conmulti('active')\" class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/v1.gif' border='none' alt='v'><span>Activer les articles cochés</span></a>&nbsp;<a href='#suppall' onclick=\"conmulti('desactive')\" class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/v0.gif' border='none' alt='o'><span>Désactiver les articles cochés</span></a>
			";
		}
		else{
			echo".";
		}
		
		echo"</td>
	 </tr>";
	 for($i=0 ; $i<sizeof($allkoi) ; $i++){
	 	$ligne = $allkoi[$i];
		echo"<tr class='buttontd'><td align='left'><a name='f$i'></a>";
		if(isset(${"gestion_articles_".$clasnom."_liste"})){
			echo"<select name='$clasnom$i'>";
			foreach(${"gestion_articles_".$clasnom."_liste"} as $accval){
				$s='';
				if($accval==$ligne) $s='selected';
				echo"<option value=\"$accval\" $s>$accval</option>";
			}
			echo'</select>';
		}
		else{
			echo"<input type='text' name='$clasnom$i' value=\"$ligne\">";
		}
		echo"<a name=\"_$ligne\"></td>";
		 if($taxe_cible=='HT'){
			 echo"
			 <td colspan='7'></td>
			 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
			 <td colspan='2'></td>
			";
		 }
		 else{
			 echo"
			<td colspan='5'></td>
			 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
			 <td colspan='4'></td>";
		 }
		
	 echo"<td align='right'>
		";
						
					echo"</td></tr>";
		$res = mysql_query("SELECT * FROM `gestion_artstock` WHERE `ref`='$edit' AND `$clasnom`='".str_replace("'","''",$ligne)."' ORDER BY `ordre`");
		 while($rou=mysql_fetch_object($res)){
		 	$s_id = $rou->id;
			$s_ordre = $rou->ordre;
			$s_code = $rou->code;
			$s_ean = $rou->ean;
			$s_other = $rou->$clasoth;
			$s_libre = $rou->libre;
			$s_prix = $rou->prix;
			$s_promo = $rou->promo;
			$s_prix_pro = $rou->prix_pro;
			$s_promo_pro = $rou->promo_pro;
			$s_tva = $rou->tva;
			$s_stock = $rou->stock;
			$s_active = $rou->active;
			
			if(isset($fourtruc)  ){
				$fourtruc.="<tr><td align='left'><table cellpadding='4' cellspacing='0'><tr>";
				$ref = mysql_query("SELECT `id`,`four`,`prix` FROM `gestion_artfour` WHERE `art`='$s_id'");
				if($ref && mysql_num_rows($ref)>0){
					while($fo=mysql_fetch_array($ref)){
					$fourtruc.="
						<td  style='white-space:nowrap; border-left:#999 1px solid;'>
						<a title='Marge : ".($s_prix-$fo[2])." &euro; - Coef.: ".round($s_prix/$fo[2],1)." / Marge Pro : ".($s_prix_pro-$fo[2])." &euro; - Coef. pro : ".round($s_prix_pro/$fo[2],1)."'>".get($fournisseurs_db,'nom',$fo[1])."</a></td>
						<td style='white-space:nowrap;'>
						<input type='text' name='prixpa_$fo[0]' value='$fo[2]' style='width:50px'> &euro;";
						if($u_droits == '' || $u_active == 1 ){
							$fourtruc.="</td><td><a onclick='confsupa($fo[0],$s_id)'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif' border='none' alt='supprimer'></a>";
						}
					$fourtruc.="</td>
						";
					}
				}
				$fourtruc.="
				<td style='white-space:nowrap;border-left:#999 1px solid;'>
				Ajouter
				".str_replace('FOUR_NAME',"selfour_$s_id",$foursel)."
				
				<a class='buttontd' name='four$s_id' href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&fours=$s_id&addfour=$s_id#four$s_id';document.fourmis.submit();\">ok</a></td></tr></table>
				</td>				
				</tr>";
			}
			
			echo"
			<tr id='tr$s_id'><td align='right'><input type='hidden' name='$clasnom$s_id' value='$ligne'><a class='info'><img name='im_ean$s_id' src='$openean&ean=$s_ean' alt='$s_ean' height='20' width='30' border='none' onclick='document.fourmis.ean$s_id.focus()'><span><img name='img_ean$s_id' src='$openean&ean=$s_ean' alt='$s_ean' border='none'></span></a></td>
				<td width='10'><input type='text' name='ordre$s_id' value='$s_ordre' size='1'>
				<input type='hidden' name='$clasnom$s_id' value='$ligne' size='1'></td>
				<td><input type='text' name='code$s_id' value='$s_code' size='5'></td>
				<td><input type='text' name='ean$s_id' value='$s_ean' size='13' maxlength='13' style='font-size:10px'  onchange=\"document.im_ean$s_id.src='$openean&ean='+this.value; document.img_ean$s_id.src='$openean&ean='+this.value;\"></td>
				<td>";
				if(isset(${"gestion_articles_".$clasoth."_liste"})){
					echo"<select name='$clasoth$s_id'>";
					foreach(${"gestion_articles_".$clasoth."_liste"} as $accval){
						$s='';
						if($accval==$s_other) $s='selected';
						echo"<option value=\"$accval\" $s>$accval</option>";
					}
					echo'</select>';
				}
				else{
					echo"<input type='text' name='$clasoth$s_id' value='$s_other' size='6'>";
				}
				echo"
				</td>
				<td><input type='text' name='libre$s_id' value='$s_libre' size='8'></td>";
	 if($taxe_cible=='HT'){
		 echo"
		 <td><input type='text' name='prix$s_id' value='$s_prix' size='6'></td>
		<td><input type='text' name='promo$s_id' value='$s_promo' size='6'></td>
		<td class='cadre' style='border-width:0px;border-left-width:1px;'><input type='text' name='prix_pro$s_id' value='$s_prix_pro' size='6'></td>
		<td class='cadre' style='border-width:0px;border-right-width:1px;'><input type='text' name='promo_pro$s_id' value='$s_promo_pro' size='6'></td>
		";
	 }
	 else{
		 echo"
		 <td class='cadre' style='border-width:0px;border-left-width:1px;'><input type='text' name='prix$s_id' value='$s_prix' size='6'></td>
		<td class='cadre' style='border-width:0px;border-right-width:1px;'><input type='text' name='promo$s_id' value='$s_promo' size='6'></td>
		<td><input type='text' name='prix_pro$s_id' value='$s_prix_pro' size='6'></td>
		<td><input type='text' name='promo_pro$s_id' value='$s_promo_pro' size='6'></td>
		";
	 }
	 echo"				
				<td><input type='text' name='tva$s_id' value='$s_tva' size='3'></td>
				<td><input type='text' name='stock$s_id' value='$s_stock' size='2'></td>
				<td width='10' valign='middle' align='left'><a name='l$s_id'></a>";
						if($u_droits == '' || $u_active == 1 ){
							//echo"
							//<a href='#l$s_id' onclick='confsupt($s_id,$i)'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif' border='none' alt='supprimer'></a>";
							echo"<input type='checkbox' name='stch$s_id' value='$s_id'> <a href='#l$s_id' onclick=\"document.fourmis.action+='&l_a&$setopo[$s_active]=$s_id&effdb=gestion_artstock#l$s_id';document.fourmis.submit()\" class='info'>
							<img src='http://www.adeli.wac.fr/vers/$vers/$theme/v$s_active.gif' id='img$s_id' border='none' alt='actif: $s_active'><span>$setopot[$s_active]</span></a>";
						}
						else{
							echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/v$s_active.gif' border='none' alt='actif: $s_active'>";
						}
					echo"
					
					</td>
	 		</tr>";
		 }
		 echo"<tr class='buttontd'>
		 ";
	 if($taxe_cible=='HT'){
		 echo"
		 <td colspan='8'></td>
		 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
		 <td colspan='3' align='right'>
		";
	 }
	 else{
		 echo"
		<td colspan='6'></td>
		 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
		 <td colspan='4' align='right'>";
	 }
	 echo"
		 
		 	<input type='button' value='Ajouter' class='buttontd' onclick=\"addinref('$clasnom','$ligne',true);\">
			
		 </td></tr>
		 <tr>";
	 if($taxe_cible=='HT'){
		 echo"
		 <td colspan='8'></td>
		 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
		 <td colspan='3'></td>
		";
	 }
	 else{
		 echo"
		<td colspan='6'></td>
		 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
		 <td colspan='5'></td>";
	 }
	 echo"</tr>";
	  	if(isset($fourtruc)  )$fourtruc.="<tr><td style='height:55px;'>&nbsp;</td></tr>";				
	 }
	 
	 echo"<tr>";
	 if($taxe_cible=='HT'){
		 echo"
		 <td colspan='8'' align='left' valign='top'>
		 <a onclick=\"document.getElementById('newtai').style.visibility='visible';\" class='buttontd'>Ajouter ".$rang_names['taille']."</a>
		 <a onclick=\"document.getElementById('newcol').style.visibility='visible';\" class='buttontd'>Ajouter ".$rang_names['couleur']."</a>
		 </td>
		 <td colspan='2' class='cadre' style='border-width:1px;border-top-width:0px'>&nbsp;</td>
		 <td colspan='3 align='right' valign='bottom'>
		 	<input type='submit' value='Enregistrer' class='buttontd'>	
		 </td>
		";
	 }
	 else{
		 echo"
		<td colspan='6' align='left' valign='top'>
		<a onclick=\"document.getElementById('newtai').style.visibility='visible';\" class='buttontd'>Ajouter ".$rang_names['taille']."</a>
		<a onclick=\"document.getElementById('newcol').style.visibility='visible';\" class='buttontd'>Ajouter ".$rang_names['couleur']."</a>
		</td>
		 <td colspan='2' class='cadre' style='border-width:1px;border-top-width:0px'>&nbsp;</td>
		 <td colspan='5' align='right' valign='bottom'>
		 	<input type='submit' value='Enregistrer' class='buttontd'>	
		 </td>";
	 }
	 echo"</tr>";
	 if(isset($fournisseurs_db) &&  mysql_query("SHOW COLUMNS FROM $fournisseurs_db") && mysql_query("SHOW COLUMNS FROM `gestion_artfour`") ){
		 $vis = 'hidden';
		 if(isset($_GET['fours'])) $vis='visible';
			 echo"
			 <tr><td colspan='13' align='left'>
			 <a class='buttontd' style='cursor:pointer' onclick=\"document.getElementById('achafour').style.visibility='visible';\">Achats / fournisseurs</a>
			 <div style='position:relative;'>
			 <div id='achafour' style='position:absolute;bottom:15px; left:220px;visibility:$vis; width:200;z-index:600;' class='cadre'>
			 
			 <b>Prix d'achats</b><br><br>
			 <p align='right'><a class='buttontd' style='cursor:pointer' onclick=\"document.getElementById('achafour').style.visibility='hidden';\">Fermer</a>
			 </p>
			 <div id='achafour' class='cadre' style='position:relative;width:190px; overflow-x:scroll; border-style:inset;'>
			 <table cellspacing='0' cellpadding='2'>
			 	$fourtruc
			 </table>
			 </div>
			 <a style='position:absolute; left:10px; bottom:30px; display:block;' class='buttontd' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&fours=all&addfour=all#four$s_id';document.fourmis.submit();\">
			 Ajouter pour tous
			 </a>
			 </div>			 
			 </div>
			 </td></tr>";
	 }
	 echo"</table>
	 
	 <div style='position:relative'>
	 	<div id='newtai' class='buttontd' style='position:absolute; visibility:hidden;'>
		<b>".ucfirst($rang_names['taille'])." : </b>
			<table><tr><td>entrées existantes</td><td>Nouvelle entrée</td><td></td></tr>
			<tr><td>
			<select onchange=\"addinref('taille',this.value);\">
			<option value=''></option>
				";
				
				if(isset($gestion_articles_taille_liste)){
					foreach($gestion_articles_taille_liste as $accval){
						echo"<option value=\"$accval\">$accval</option>";
					}
					echo'</select>';
				}
				else{
					$res = mysql_query("SELECT DISTINCT `taille` FROM `gestion_artstock`");
				 if($res && mysql_num_rows($res)>0){
					while($rou=mysql_fetch_array($res)){
						echo"<option value=\"$rou[0]\">".ucfirst($rou[0])."</option>";
					}
				 }
				echo"
			</select>
			</td><td><input type='text' value='' name='newtail'><input type='button' value='ok' class='buttontd' onclick=\"addinref('taille',document.fourmis.newtail.value);\">";
				}
				
			
			echo"
			
			</td><td>
			<a onclick=\"document.getElementById('newtai').style.visibility='hidden';\" class='buttontd'>Annuler</a>
			</td></tr></table>
		</div>
		<div id='newcol' class='buttontd' style='position:absolute; visibility:hidden;'>
		<b>".ucfirst($rang_names['couleur'])." : </b>
			<table><tr><td>entrées existantes</td><td>Nouvelle entrée</td><td></td></tr>
			<tr><td>
			<select onchange=\"addinref('couleur',this.value);\">
			<option value=''></option>
				";
				if(isset($gestion_articles_couleur_liste)){
					foreach($gestion_articles_couleur_liste as $accval){
						echo"<option value=\"$accval\">$accval</option>";
					}
					echo'</select>';
				}
				else{
				$res = mysql_query("SELECT DISTINCT `couleur` FROM `gestion_artstock`");
				 if($res && mysql_num_rows($res)>0){
					while($rou=mysql_fetch_array($res)){
						echo"<option value=\"$rou[0]\">".ucfirst($rou[0])."</option>";
					}
				 }
				echo"
			</select>
			</td><td><input type='text' value='' name='newcolo'><input type='button' value='ok' class='buttontd' onclick=\"addinref('couleur',document.fourmis.newcolo.value);\">";
				}
			echo"
			
			</td><td>
			<a onclick=\"document.getElementById('newcol').style.visibility='hidden';\" class='buttontd'>Annuler</a>
			</td></tr></table>
		</div>
	 </div>
	 
	 </td></tr>
	";
	 }
	 
//////////////////////////////////////////////////////////////////////// ASSOC
	 echo"
<tr><td colspan='3'>
	 <br><br><b>Articles associés</b><hr>
	 ";
echo"<input type='hidden' name=\"conseil\" value=\"$art_conseil\">
						
				  ";
		$c=0;
		$hot=46;
		$ch=0;
		$prh='';

$listrays = mysql_query("SELECT DISTINCT `$rayons_db`.`id`,`gestion_raytrad`.`nom` FROM `$rayons_db`,`gestion_raytrad` 
WHERE 
	`gestion_raytrad`.`ref`=`$rayons_db`.`id`
GROUP BY `$rayons_db`.`id`
ORDER BY `gestion_raytrad`.`nom`");
		while($raylist = mysql_fetch_object($listrays)){
			$raaynom = $raylist->nom;
			$raayid = $raylist->id;				
						$listres = mysql_query("SELECT DISTINCT `$articles_db`.`id`,`gestion_artrad`.`nom` FROM `gestion_articles`,`gestion_artrad` 
WHERE 
	`$articles_db`.`rayon`='$raayid'
AND	`gestion_artrad`.`ref`=`gestion_articles`.`id`
AND	`gestion_articles`.`id`!='$edit'
GROUP BY `gestion_articles`.`id`
ORDER BY `gestion_artrad`.`nom`");
				$prh.="<br><b>$raaynom</b>";
				$hot+=23;
						while($rowlist = mysql_fetch_object($listres)){
							$rowvalue = $rowlist->nom;
							$rowid = $rowlist->id;
							$se = '';
							$c++;
							if(ereg('<'.$rowid.'>',$art_conseil)){
								$se = 'checked';
								$ch++;
							}
							$hot+=23;
							$prh.="<li><input type='checkbox' name='cons$c' onclick=\"oldv=parseInt(document.fourmis.ch_co.value);if(this.checked==true){if(document.fourmis.conseil.value.indexOf('<$rowid>')==-1){document.fourmis.conseil.value+='<$rowid>';oldv++;}}else{document.fourmis.conseil.value=document.fourmis.conseil.value.replace('<$rowid>','');oldv--;}document.fourmis.ch_co.value=oldv;\" $se>$rowvalue</li>";
						}
			}
						if($hot>300) $hot=300;
						echo"<a href='#conse' name='conse' onclick=\"dec('conseil',$hot)\"><b>v Développer v</b></a>
						<input type='text' name=\"ch_co\" value=\"$ch\" style='border:none;background:none;width:30px;text-align:right' readonly> / $c sélectionnés<br>
				  		<div id='conseil' style='display:block;width:380px;height:$hot;overflow:scroll;'>
						<a href='#conse' onclick=\"dec('conseil',1)\"><b>^ réduire ^</b></a>
						<ul>						
						$prh
						</ul>
						<a href='#conse' onclick=\"dec('conseil',1)\"><b>^ réduire ^</b></a>
						</div>
						<script language='javascript' type='text/javascript'>
						dec('conseil',1);
						</script>";	 
						
	$row = $columns-sizeof($legal_entrys);
	if($row<=0)$row=1;
	$row++;
	 echo"	 
	 </td>
</tr>
<tr>
		<td colspan='2'><br><br><b>Champs supplémentaires</b><hr></td>
		<td rowspan='$row'>
		";
		if(isset($fichiers[$part])){	
		insert('_fichiers');
		if(is_file('bin/_fichiers.php')){
			include('bin/_fichiers.php');
		}
		else{
			include('http://www.adeli.wac.fr/vers/$vers/update.php?file=_fichiers.php&1');
		}
	   }
		echo"
		</td>
		</tr>";
//////////////////////////////////////////////////////: CHAMPS PERSONNALISES
for ($i = 0; $i < $columns; $i++) {
	$field_name = mysql_field_name($res_field, $i);
	if(!in_array($field_name,$legal_entrys)){
		
			$field_act = $field_name;
			if(isset($r_alias[$part][$field_name])){
				$field_act = $r_alias[$part][$field_name];
			}
			$field_type = mysql_field_type($res_field, $i);			
			$field_length = abs(mysql_field_len($res_field, $i));
			if(isset($_GET['refresh'])){
				$field_value = stripslashes($_POST[$field_name]);
			}
			else{
				$field_value = $ro->$field_name;	
			}
			$field_width=$field_length*12;
			if($field_width > 300){
				$field_width=300;
			}
			$nameifthefield = ereg_replace(">"," ",$field_name);	
			if(isset($alias[$part][$field_name])){
				$nameifthefield = $alias[$part][$field_name];
			}
			//$baz = substr($field_name,0,strpos($field_name,'_'));
			echo"<tr>";
			/////////////////////////////////////// COULEUR
			if(ereg("couleur",$field_name) && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";
					}
			 	echo"<td valign='top'>couleur</td><td valign='top'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>
				 	#<input type=\"text\" name=\"$field_name\" value=\"$field_value\" maxlength='6' size='6' onchange=\"document.getElementById('div$field_name').style.backgroundColor='#'+this.value\">
						<div id=\"div$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:20px;width:30px;border-color:#000000;border-style:solid;border-width:1px\"></div>
						<a href='#a$field_name' name='a$field_name' onclick=\"choosecolor($i,'Backcolor','$field_name','hexa',event)\">changer la couleur</a>
				 </td>";
			 }
			 /////////////////////////////////////// CARTE
			 elseif($field_type == "int" && isset($mapcoord[$part]) && is_file('../'.$mapcoord[$part]) && ($field_name == "x" || $field_name == "y") ){
			 	if(	$field_name == "x"){
					$cx = $ro->x;	
					$cy = $ro->y;
					$getsi  = getimagesize('../'.$mapcoord[$part]);
					$minx = $getsi[0];
					if($minx > 300) $minx=300;  
					$miny = $getsi[1];
					if($miny > 300) $miny=300;  
					 echo"<td valign='top'><a class='info'>Position<span>coordonnées <b>XY</b></span></a></td><td valign='top'>
					<script language=\"JavaScript\">
					function point_it(event){
						pos_x = event.offsetX?(event.offsetX):event.pageX-document.getElementById(\"position\").offsetLeft;
						pos_y = event.offsetY?(event.offsetY):event.pageY-document.getElementById(\"position\").offsetTop;
						document.getElementById(\"poscros\").style.left = (pos_x-5) ;
						document.getElementById(\"poscros\").style.top = (pos_y-5) ;
						document.fourmis.x.value = pos_x;
						document.fourmis.y.value = pos_y;
					}
					</script>

					 x:<input type=\"text\" name=\"x\" value=\"$cx\" style=\"width:30px\" maxlength=\"4\" onkeyup=\"document.getElementById('poscros').style.left = (parseInt(this.value)-5)\">
					 |
					 y:<input type=\"text\" name=\"y\" value=\"$cy\" style=\"width:30px\" maxlength=\"4\" onkeyup=\"document.getElementById('poscros').style.top = (parseInt(this.value)-5)\"><br>
					 <div id='position' class='cadre' style='position:relative;display:block;width:$minx;height:$miny;overflow:hidden;padding:0px' onMouseOver='this.style.width=$getsi[0];this.style.height=$getsi[1];' 
onClick='this.style.width=$getsi[0];this.style.height=$getsi[1];' onMouseOut='this.style.width=$minx;this.style.height=$miny;'
onblur='this.style.width=$minx;this.style.height=$miny;'><img id='imgcoor' src='../$mapcoord[$part]' border='none'  onclick='point_it(event)'>
					 <div id='poscros' style='position:absolute;left:$cx;top:$cy;width:10;height:10;font-size:10px;border-width:1px;border-color:#00000;border-style:dashed;font-color:#FFFFFF;background-color:#FF0000'>+</div>
					 </div>
					 </td>";
				 }
			 }
				
			 ///////////////////////////////////// PREFIXE
			 elseif(substr($field_name,0,1) == "_"){
				if($nameifthefield == $field_name){
					$nameifthefield = substr($field_name,1,strlen($field_name));
				}
				if($field_value==""){
					$field_value=$_SESSION[$field_name];
				}
				
				echo"<td valign='top'>$nameifthefield</td><td valign='top'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'><input type=\"text\"  id='pref_txt_$i' name=\"$field_name\" value=\"$field_value\" style=\"width:300px;border:none\" class=\"bando\" readonly onfocus=\"this.style.width='1px';document.getElementById('pref_sel_$i').style.display='inline';document.pref_sel_$i.focus();\">
				 <select id='pref_sel_$i' name='pref_sel_$i' onchange=\"javascript:set$nameifthefield(this.value);this.value=' ';document.fourmis.action=document.fourmis.action.replace(new RegExp('&update'), '&refresh').replace(new RegExp('&add'), '&refresh');document.fourmis.submit();\" onblur=\"this.style.display='none';document.getElementById('pref_txt_$i').style.width='300px';\" style=\"width:300px;display:none;\">
				 	<option value=' '>-- $nameifthefield --</option>";
					$listres = mysql_query("SELECT DISTINCT `$field_name` FROM `$tabledb` $incwhere $prefixselection");
					$prefixselection.=" AND `$field_name`='$field_value'";
					while($rowlist = mysql_fetch_object($listres)){
						$rowvalue = $rowlist->$field_name;
						$s='';
						if($rowvalue==$field_value) $s='selected';
						echo"<option value=\"$rowvalue\" $s>$rowvalue</option>";
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
				 </td>";			}
			 ///////////////////////////////////// SUFIXE
			 elseif( ereg("_",$field_act) && ( mysql_query("SHOW COLUMNS FROM ".substr($field_act,0,strpos($field_act,'_'))) || ereg('@',$field_act) ) ){
			 	$refiled = substr($field_act,0,strpos($field_act,"_"));
				$fieldoption = substr($field_act,strpos($field_act,"_")+1,strlen($field_act));
				if($nameifthefield == $field_act){
					$nameifthefield = $refiled;
				}
				if(ereg(">",$field_act)){
					$fieldoption = substr($fieldoption,0,strpos($fieldoption,">"));
					$nameifthefield .= " : ".substr($field_act,strpos($field_act,">")+1,strlen($field_act));
				}
				
				
				if(substr($fieldoption,0,1) == "@"){
					$nameofoption = substr($fieldoption,1,strlen($fieldoption));	
					$field_value = $_SESSION[$nameofoption];	
					echo"<td valign='top'>$nameifthefield <a class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/pile.gif'>
					<span>Ce champ est à un élément personnel de session <b>$nameofoption</b></span></a></td><td valign='top'>
					 <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:100px\" class=\"bando\" readonly>
					 </td>";		
				}
				else{				
					$fieldoptions = split("_",$fieldoption);
					$fieldoptionprint = $fieldoptions[1];
					$fieldoption = $fieldoptions[0];
					$refiled = trim($refiled);	
					if($prefixe!=""){
						$nameifthefield = trim(ereg_replace($prefixe,"",$nameifthefield));
					}
					echo"<td valign='top' valign='top'>$nameifthefield <a class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/pile.gif'>
					<span>Ce champ est relié au tableau <b>$refiled</b></span></a></td><td valign='top'>
					 <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>";
				   if((sizeof($fieldoptions)==3 && $fieldoptions[2]=='ch') || (sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlch')){
				   
					  echo"<input type='hidden' name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\">
				  ";
						$c=0;
						$hot=46;
						$ch=0;
						$prh='';
						$hut=0;
						$seled = '';
						if(sizeof($fieldoptions)==3){
							$listres = mysql_query("SELECT `$fieldoptionprint`,`$fieldoption`,`id` FROM `$refiled` ORDER BY `$fieldoptionprint`");
							while($rowlist = mysql_fetch_array($listres)){
								$rowvalue = $rowlist[0];
								$rowid = $rowlist[1];
								$roid = $rowlist[2];
								$se = '';
								$c++;
								if(ereg('<'.$rowid.'>',$field_value)){
									$se = 'checked';
									$seled .= "$rowvalue<br>";
									$hut+=20;
									$ch++;
								}
								$hot+=23;
								$rowvaluu = str_replace("'","\'",$rowvalue);
								$rowid = str_replace("'","\'",$rowid);
								$rowid = str_replace('"','&quot;',$rowid);
								$prh.="<li><input type='checkbox' name='cho$i$c'   value=\"$rowid\"  title=\"$rowvaluu\" onclick=\"rempli(document.getElementById('ulch_$i'),document.fourmis.$field_name,document.fourmis.ch_cu_$i,document.getElementById('chu_$i'))\" $se>$rowvalue <a href='./?$refiled&edit=$roid'>></a></li>";
								//oldv=parseInt(document.fourmis.ch_cu_$i.value);if(this.checked==true){if(document.fourmis.$field_name.value.indexOf('<$rowid>')==-1){document.fourmis.$field_name.value+='<$rowid>'; document.getElementById('chu_$i').innerHTML+='$rowvaluu<br>';oldv++;}}else{document.fourmis.$field_name.value=document.fourmis.$field_name.value.replace('<$rowid>','');document.getElementById('chu_$i').innerHTML=document.getElementById('chu_$i').innerHTML.replace('$rowvaluu<br>','');oldv--;}document.fourmis.ch_cu_$i.value=oldv;hut=oldv*20;
							}
						}
						if(sizeof($fieldoptions)==2){
							$listres = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled`");
							$rowlist = mysql_fetch_array($listres);
							$gvl = explode("\n",$rowlist[0]);
							foreach($gvl as $rowvalue){
								$rowvalue=trim($rowvalue);
								$se = '';
								$c++;
								if(ereg('<'.$rowvalue.'>',$field_value)){
									$se = 'checked';
									$seled .= "$rowvalue<br>";
									$hut+=20;
									$ch++;
								}
								$hot+=23;
								$rowvaluu = str_replace("'","\'",$rowvalue);
								$rowvaluu = str_replace('"','&quot;',$rowvaluu);
								$rowid = str_replace("'","\'",$rowid);
								$prh.="<li><input type='checkbox' name='cho$i$c' value=\"$rowvaluu\"  title=\"$rowvaluu\"  onclick=\"rempli(document.getElementById('ulch_$i'),document.fourmis.$field_name,document.fourmis.ch_cu_$i,document.getElementById('chu_$i'))\" $se>$rowvalue</li>";
								//oldv=parseInt(document.fourmis.ch_cu_$i.value);if(this.checked==true){if(document.fourmis.$field_name.value.indexOf('<$rowvaluu>')==-1){document.fourmis.$field_name.value+='<$rowvaluu>'; document.getElementById('chu_$i').innerHTML+='$rowvaluu<br>';oldv++;}}else{document.fourmis.$field_name.value=document.fourmis.$field_name.value.replace('<$rowvaluu>','');document.getElementById('chu_$i').innerHTML=document.getElementById('chu_$i').innerHTML.replace('$rowvaluu<br>','');oldv--;}document.fourmis.ch_cu_$i.value=oldv;hut=oldv*20;
							}
						}
						
						if($hot>300) $hot=300;
						echo"
						<script language=\"JavaScript\">
						hut = $hut;
						</script>
						<a href='#ch$i' name='ch$i' onclick=\"dec('ch_$i',$hot);dec('chu_$i',1)\"><b><img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_down_off.jpg' alt='v' border='none'> Développer <img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_down_off.jpg' alt='v' border='none'></b></a>
						<input type='text' name=\"ch_cu_$i\" value=\"$ch\" style='border:none;background:none;width:30px;text-align:right' readonly> / $c sélectionnés<br>
				  		<div id='ch_$i' style='display:block;width:380px;height:1px;overflow:hidden;'>
						<a href='#ch$i' onclick=\"dec('ch_$i',1);dec('chu_$i',hut)\"><b><img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'> réduire <img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
						<li><input type='checkbox' name='all$i$c' onclick=\"tout(document.getElementById('ulch_$i'),this, document.fourmis.$field_name,document.fourmis.ch_cu_$i,document.getElementById('chu_$i'))\"> Tout</li>
						<ul id='ulch_$i'>	
						
						$prh
						</ul>
						<a href='#ch$i' onclick=\"dec('ch_$i',1);dec('chu_$i',hut)\"><b><img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'> réduire <img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
						</div>
						<div id='chu_$i' style='display:block;width:380px;height:$hut"."px;overflow:hidden;'  onclick=\"dec('ch_$i',$hot);dec('chu_$i',1)\">						
						$seled
						</div>
						
						";	
		
					}	
					else{
					 echo"<select name=\"$field_name\" style=\"width:300px\">
						<option value=' '>liste des choix</option>";
						$listres = mysql_query("SELECT * FROM `$refiled` ORDER BY `$fieldoptionprint`");
						while($rowlist = mysql_fetch_object($listres)){
							$rowvalue = $rowlist->$fieldoptionprint;
							$rowid = $rowlist->$fieldoption;
							$se = "";
							if($rowid == $field_value){
								$se = "selected";
							}
							echo"<option value=\"$rowid\" $se>$rowvalue</option>";
						}
						echo"</select>";
					}
					echo"</td>";
				 }	 
				 
			}
			/////////////////////////////////////// STRING
			 elseif($field_type == "string"){			 	  
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td valign='top'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:$field_width"."px\" maxlength=\"$field_length\"></td>";
			 }
			 /////////////////////////////////////// INT
			 elseif($field_type == "int" || $field_type == "real"){			 	  
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Nombre</span></a></td><td valign='top'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-int.png' alt='numérique'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:150px\" maxlength=\"$field_length\"></td>";
			 }
			 /////////////////////////////////////// DATE
			 elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
			 	if($field_value==""){
					$field_value=$defaultvalue[$field_type];
				}	
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Date au format standard<br>date: aaaa-mm-jj<br>heure: hh:mm:ss</span></a></td><td valign='top'>
				 <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"javascript:contextdate(event);cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=fourmis.$field_name&amp;date='+document.fourmis.$field_name.value+'&amp;type=$field_type'\"><input type='text' name=\"$field_name\" value=\"$field_value\" maxlength=\"$field_length\">
				 </td>";
			 }
		
		
		
		
		
		
		
		
		
		
		
		
		
		echo"
		</tr>
		";
	}
}	 
	 
	 echo"<tr><td colspan='3'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>";

?>