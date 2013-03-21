<?php // 323 > Fournisseur de données externes ;
session_name("adeli"); session_start();
header("Content-Transfer-Encoding: UTF8");
function callback($buffer){
	return utf8_encode($buffer); //
}				
ob_start("callback");
$scan = $_GET['scan'];
$edit='';
$vers='1.2';
if(!is_file("../mconfig/adeli.php") ){
	$base=$_SESSION["db_base"];
	$login=$_SESSION["db_user"];
	$passe=$_SESSION["db_pass"];
	$host=$_SESSION["db_host"];
	
}
else{
	include("../mconfig/adeli.php");
}
if(!isset($style_url)) $style_url="http://www.adeli.wac.fr/vers/$vers";
if(!isset($lalierp)) $lalierp='LaLIE_rapports';	

include("inc_func.php");
$u_id = $_SESSION['u_id'];
$x_id = $_SESSION['x_id'];
$vers=$_SESSION["vers"];
$theme=$_SESSION["theme"];
$mail_base=$_SESSION["mail_base"];
if(false !== $conn = @mysql_connect($host, $login, $passe)){
mysql_select_db($base);
if($scan=='exif'){////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// EXIF
	if(isset($_POST['f']) && isset($_POST['c']) && isset($_POST['v']) && is_file("pel.php")){
		include("pel.php");
		 
		$jpeg = new PelJpeg($_POST['f']);
  		$jpeg->clearExif();
		$ifd0 = $jpeg->getExif()->getTiff()->getIfd();
 		 $entry = $ifd0->getEntry(PelTag::IMAGE_DESCRIPTION);
  		 $entry->setValue('Edited by PEL');
		file_put_contents($_POST['f'], $jpeg->getBytes());
		echo $_POST['c'].'/'.$_POST['v'];
	}
}
elseif($scan==='rech'){ ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// SEARCH
	$option = $_GET['option'];
	$dbop = $db = $_GET['db'];
	$q = ($_GET['str']);
	$qs=explode(' ',$q);
	$gcom='';
	$plurl='';
	if($option=='gestion'){
		if($db == 'gestion_articles') $db = 'gestion_artrad';
		if($db == 'gestion_rayons') $db = 'gestion_raytrad';	
	}
	if($option=='compta'){
		$gcom = " `type`='$db' AND ";
		$db = $compta_base;	
		$plurl.='&getcontent&option=compta';
	}	
	
	$res_field = mysql_list_fields($base,$db);
	$columns = mysql_num_fields($res_field);
	$nbf = -1;
	$head='';
	$command='1';
	if($db=='gestion_artrad' || $db=='gestion_raytrad'){
		$head="`$db`.`ref`";
	}
	else{
		$head='`id`';
	}
	foreach($qs as $qe){
		if(trim($qe)!=''){
			$que=urluntranslate($qe);
			$command.=" AND ( ";
			for ($i=0 ; $i < $columns; $i++) {			
				$field_type = mysql_field_type($res_field, $i);
				$field_name = mysql_field_name($res_field, $i);
				//header('field-'.$field_name.':'.$field_type);
				if($field_type=='string' || $field_type=='blob'){
					$command.="`$db`.`$field_name`REGEXP'".$que."' OR ";
					$head.=",`$db`.`$field_name`";
					$nbf++;
				}
				elseif($option=='compta' && $field_name=='numero'){
					$command.="`$db`.`$field_name`LIKE'%".$qe."%' OR ";
					$head.=",`$db`.`$field_name`";
					$nbf++;
				}  		
			}
			$command = substr($command,0,-3).')  ';
		}
	}
	//echo $command."\n";
	//$command = substr($command,0,-4);
	if($db=='gestion_artrad'){
		$db = 'gestion_artstock';
		$command = '`gestion_artrad`.`ref`=`gestion_artstock`.`ref` AND ('.$command.' OR';
		$res_field = mysql_list_fields($base,$db);
		$columns = mysql_num_fields($res_field);
		for ($i=0 ; $i < $columns; $i++) {
			$field_type = mysql_field_type($res_field, $i);
			if($field_type=='string' || $field_type=='blob'){
				$field_name = mysql_field_name($res_field, $i);
				$command.="`$db`.`$field_name`REGEXP'".urluntranslate($q)."' OR";
				$head.=",`$db`.`$field_name`";
				$nbf++;
			}   		
		}
		$command = substr($command,0,strlen($command)-3).')';
		
		$query = "SELECT $head FROM `gestion_artrad`,`gestion_artstock` WHERE $gcom ($command) GROUP BY 1";
	}
	else{
		$query = "SELECT $head FROM `$db` WHERE $gcom ($command)";
	}
	//echo ($query);
	//header("sql-query: yo,".$query);
	
	$result1 = mysql_query($query);
	$i=0;
	$bgtd=1;
	if($result1 && mysql_num_rows($result1)>0){
		$nbres = mysql_num_rows($result1);
		 while ($row = mysql_fetch_array($result1)) {
			 $g_id = $row[0];
			 $stret='';
			 for($e=1 ; $e<$nbf ; $e++){
				$form  = strtolower(strip_tags($row[$e]));
				$motif = substr($form,0,10);
				if(ereg($q,$form)){
					$stret .= str_replace($q,"<b>$q</b>",$form)." ";
				}
				elseif(!strpos($stret,$motif)){
					$stret .= $motif." ";
				}
			 }
			 if($i===0){
				echo"<!--$nbres,$g_id$plurl--><table width='140' cellspacing='0' cellpadding='2'>";
			}
			$i++;
			
			if($bgtd == '1'){
			$bgtd='2';
			echo"<tr class='listone'";
		   }
		   else{
			$bgtd='1';
			echo"<tr class='listtwo'";
		   }
			echo (" onclick=\"document.location='./?option=$option&part=$dbop&edit=$g_id&wsearch=$db$plurl'\"><td style='white-space:'><span>$stret</span></td></tr>");
		}
	}
	else{
		echo ("<!--0,0--><table width='140' class='cadre'><tr><td>Aucun résultat</td></tr>");
	}
	echo"</table>";
	mysql_free_result($result1);

}
elseif($scan==='lalie_async'){ //////////////////////////////////////////////////////////////////////////////////////////////////////////// LALIE ASYNC
	$id = $_GET['id'];
	$res = mysql_query("SELECT `rapport`,`dests` FROM `$lalierp` WHERE `id`=$id");
	if($res && mysql_num_rows($res)>0){
			$ro = mysql_fetch_array($res);
			$rap_sent = substr_count($ro[0],"<div");
			$rap_wait = substr_count($ro[1],"\n");
			if($rap_wait==0 && $ro[1]!='') $rap_wait=1;
			$raplu = substr_count($rap,"<!--");
			$tot = $rap_sent+$rap_wait;
			$sent = round($rap_sent/$tot*100,2);
			if($sent==100){
				echo " <div style='top:-3px;left:-0px;width:100px; background:#FFF;height:15px'><b><span class='petittext'>".$raplu."/</span>".($rap_sent)."<b></div>";
			}
			else{
				echo"envoi en cours <div style='top:-3px;left:93px;border:#999 1px solid;width:100px; background:#FFF;height:15px'><div style='background:#CCC;'width:$sent"."px;height:15px;'></div><div style='width:100px;height:15px;text-align:center;font-size:10px;'>$rap_sent/$tot - $sent%</div></div>";
			}
	}
	else{
		echo"Erreur";
	}
}
elseif($scan=='compta_livraison'){////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// COMPTA LIVRAISON
	if(isset($_POST['w'])){
		if($_POST['w']=='defart'){ // DEFINITION
			if(isset($_POST['ref']) && isset($_POST['ligne']) && isset($_POST['def'])){
				$edit = $_POST['ref'];
				$result = mysql_query("SELECT `content` FROM $compta_base WHERE id='$edit'");
				if($result && mysql_num_rows($result)){
					$row = mysql_fetch_array($result);
					/*$content = explode("<!>",$row[0]);
					$lignecontent = explode('<>',$content[$_POST['ligne']-1]);
					$label= $lignecontent[0];
					$type= $lignecontent[1];
					$pu= $lignecontent[2];
					$tva= $lignecontent[3];
					$quant= $lignecontent[4];
					$id= $lignecontent[5];
					$coderef= $lignecontent[6];
					$hidden = $lignecontent[7];
					$remise = abs($lignecontent[8]);*/
					
					
					$lns = explode('<!>',$ro[0]);
					$new_val='';
					$i=0;
					foreach($lns as $ln){							
						$i++;
						$new_val.='<!>';
						if($i==$_POST['ligne']){
							$lignecontent = explode('<>',$content[$_POST['ligne']-1]);	
							$lignecontent[5]='GAF_'.$_POST['def'];
							$new_val.=implode('<>',$lignecontent);
						}
						else{
							$new_val.=$ln;	
						}
					}
					echo "affectation : ";	
					$arid= get('gestion_artfour','art',$_POST['def']);
					$id= get('gestion_artstock','ref',$arid);
					if(mysql_query("UPDATE `$compta_base` SET `content`='$new_val' WHERE `id`='$edit'")){
						echo"ok<br><a href='./?option=gestion&part=gestion_articles&edit=$id'>Consulter</a>";
					}
					else{
						echo"échouée";	
					}
					
					
					
					
					
						
				}
				else{
					echo "article non trouvé";	
				}
			}
		}
	}
}
elseif($scan==='gestion_artfour'){ 
////////////////////////////////////////////////////////////////////////////////////////////////////////////// GESTION ARTFOUR
	
	if($_POST['w']=='read'){ ///////// READ
	
		$ref = $_POST['ref'];
		if(isset($compta_base)){
			$to=0;
			$res = mysql_query("SELECT `content` FROM `$compta_base` WHERE `content`LIKE'%GAF_$ref%' AND `type`='achat' AND `etat`='0' AND `active`=1");
			if($res && mysql_num_rows($res)>0){
				while($ro=mysql_fetch_array($res)){	
					$content = explode("<!>",$ro[0]);
					$i=0;
					while($i < sizeof($content) ){
						$lignecontent = explode("<>",$content[$i]);
						if($lignecontent[5]=="GAF_$ref"){
							$to+=$lignecontent[4];
						}
						$i++;
					}
				}
				
			}
			echo $to;
		}	
	}
	if($_POST['w']=='commande'){ ///////// COMMANDE
		eval(get_pref("compta.conf","x"));
		if(!isset($taxe)){
			$taxe='TTC';
		}
		
		$ref = $_POST['ref'];	
		$quant = $_POST['quant'];	
		$res = mysql_query("SELECT `art`,`four`,`reference`,`prix`,`tva`,`remise` FROM `gestion_artfour` WHERE `id`='$ref'");
		 if($res && mysql_num_rows($res)>0){
			$ro=mysql_fetch_array($res);
			$article = $ro[0];
			$fournisseur = $ro[1];
			$reference = $ro[2];
			$prix = $ro[3];
			$tva = $ro[4];
			$remise = $ro[5];
			$adresse=get($fournisseurs_db,'nom',$fournisseur)."\n".get($fournisseurs_db,'adresse',$fournisseur);
			$label = get_item_trans(get('gestion_artstock','ref',$article),"ar","fr","nom")." ".get('gestion_artstock','taille',$article)." ".get('gestion_artstock','couleur',$article)." ".get('gestion_artstock','libre',$article);
			$refart = get('gestion_artstock','code',$article);
			$insetcon=str_replace("'","''","<!>$label<>chiffre<>$prix<>$tva<>$quant<>GAF_$ref<>$reference<>$refart<>$remise");
			if(isset($compta_base)){
				$res = mysql_query("SELECT `id`,`content`,`acompte` FROM `$compta_base` WHERE `client`='$fournisseur' AND `type`='achat' AND `etat`='0' AND `active`=1 ORDER BY `id`DESC LIMIT 0,1");
		 		if($res && mysql_num_rows($res)>0){
					$ro=mysql_fetch_array($res);
					if(strpos($ro[1],"<>GAF_$ref<>")){
						$lns = explode('<!>',$ro[1]);
						$new_val='';
						foreach($lns as $ln){
							if(strpos($ln,"<>GAF_$ref<>")){
								$new_val.=$insetcon;	
							}
							elseif(strpos($ln,"<>")){
								$new_val.=str_replace("'","''","<!>$ln");	
							}
						}
						$ro[1]=$new_val;
						echo" mise à jour d'une ligne :";
					}
					else{
						$ro[1].="\n".$insetcon;
						echo" ajout d'une ligne :";
					}
					if(mysql_query("UPDATE `$compta_base` SET `content`='$ro[1]' WHERE `id`='$ro[0]'")){
						echo"ok";
						
						$tottc=0;
						$cont = explode('<!>',$ro[1]);
						foreach($cont as $lin){
							$cols = explode('<>',$lin);
							$pu = $cols[2];
							$tva = $cols[3];
							$quant = $cols[4];
							$rem = abs($cols[8]);
							if($taxe=='HT'){
								$pt = $pu*$quant;
								$pt-=round($pt*$rem/100,2);
								$ptva = round($pt*$tva/100,2);
								$tottc += $pt+$ptva;
							}
							else{
								$ttc = $pu*$quant;
								$ttc-=round($ttc*$rem/100,2);
								$tottc +=  $ttc;
							}
						}						
						$tottc-=$ro[2];
						if(!mysql_query("UPDATE `$compta_base` SET `montant`='$tottc' WHERE `id`='$ro[0]'")){
							echo", calcul du montant total échoué...";	
						}
					}
					else{
						echo"échouée";	
					}
				}
				else{
					$ro=mysql_fetch_array($res);	
					echo" insertion ";
					$numero = getnext($compta_base,'numero',"WHERE `type`='achat'");
					if(mysql_query("INSERT INTO `$compta_base` (`numero`,`type`,`client`, `adresse`, `intitule`, `date`,`etat`,`active`,`content`) VALUES('$numero','achat','$fournisseur', '".str_replace("'","''",$adresse)."', 'Bon de commande', NOW(), '0', '1','$insetcon')")){
						echo"ok";
					}
					else{
						echo"échouée";	
					}
				}
			}			
		 }
		 else{
			echo"référence inconnue"; 
		 }
		 ////////////// CALCULER TOTAL !!!
	}
}
elseif($scan==='gestion_artstock'){ ///////////////////////////////////////////////////////////////////////////////////////////////////////// GESTION ARTSTOCK
	if(isset($_POST['multi']) && isset($_POST['action'])){
		$multi = $_POST['multi'];	
		$action = $_POST['action'];	
		$pb=-1;
		$res = mysql_query("SELECT `id` FROM `gestion_artstock` WHERE `ref`='$multi'");
		 if($res && mysql_num_rows($res)>0){
			$pb=0;
			while($ro=mysql_fetch_array($res)){
				//echo"stch$ro[0] ";
				if(isset($_POST['stch'.$ro[0]])){
					if($action=='delete'){
						if(!mysql_query("DELETE FROM `gestion_artstock` WHERE `id`='$ro[0]'")) $pb++;	
					}
					if($action=='active'){
						if(!mysql_query("UPDATE`gestion_artstock` SET `active`='1' WHERE `id`='$ro[0]'")) $pb++;	
					}
					if($action=='desactive'){
						if(!mysql_query("UPDATE`gestion_artstock` SET `active`='0' WHERE `id`='$ro[0]'")) $pb++;	
					}
				}
			}
		 }
		 echo $pb;
	}
}
elseif($scan==='gestion_article'){ /////////////////////////////////////////////////////////////////////////////////////////////////////////////// GESTION ARTICLES
	eval(get_pref("compta.conf","x"));
	if(!isset($taxe)){
		$taxe='TTC';
	}
	$sufx='';
	if($taxe=='HT') $sufx='_pro';
	$rayons_db = "gestion_rayons";
	$articles_db = "gestion_articles";
	
	if(isset($gestion_db["rayons"])) $rayons_db=$gestion_db["rayons"];
	if(isset($gestion_db["articles"])) $articles_db=$gestion_db["articles"];
	
	if(isset($_POST['q'])){
		$q = $_POST['q'];
		$part=$_POST['part'];
		$client=$_POST['client'];
		$req = urluntranslate(str_replace("'","''",utf8_decode($q)));
		echo"<table class='cadre' width='300'>
		";
		if($part=='achat'){
			//id 	art 	four 	prix 	reference 	active 	tva
			$cli='';
			if($client!=NULL) $cli=  "AND `four`='$client'";
			$res = mysql_query("SELECT 
		DISTINCT 
			`gestion_artfour`.`prix`,
			`gestion_artfour`.`tva`,
			`gestion_artfour`.`prix`,
			`rayon`,
			`nom`,
			`desc`,
			`plus1`,
			`plus2`,
			`gestion_artfour`.`reference`,
			`gestion_artstock`.`ean`,
			`gestion_artstock`.`libre`,
			`gestion_artstock`.`taille`,
			`gestion_artstock`.`couleur`,
			`gestion_artstock`.`stock`,
			`gestion_artstock`.`id`,
			`gestion_artfour`.`id`,
			`gestion_artfour`.`remise`
		FROM 
			`$articles_db`,`gestion_artstock`,`gestion_artfour`,`gestion_artrad` 
		WHERE 
			 `$articles_db`.`id`=`gestion_artrad`.`ref` 
			AND `$articles_db`.`active`=1 
			AND	(`nom`REGEXP'$req' OR `desc`REGEXP'$req' OR `plus1`REGEXP'$req' OR `plus2`REGEXP'$req' OR `gestion_artstock`.`code`REGEXP'$req' OR `gestion_artstock`.`code`LIKE'%$q%' OR `gestion_artstock`.`libre`REGEXP'$req' OR `gestion_artstock`.`ean`LIKE'%$q%') 
			AND	 `$articles_db`.`active`=1
			AND	 `nom`!=''
			AND `gestion_artstock`.`active`=1 
			$cli
			AND `gestion_artstock`.`id`=`$gestion_artfour`.`art`
			AND `gestion_artstock`.`ref`=`$articles_db`.`id`
			AND `gestion_artstock`.`prix$sufx`>0");
		}
		else{
			$res = mysql_query("SELECT 
		DISTINCT 
			`prix$sufx`,
			`tva`,
			`promo$sufx`,
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
			`gestion_artstock`.`stock`,
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
			AND `gestion_artstock`.`prix$sufx`>0");
		}

			
		if($res && mysql_num_rows($res)>0 ){
				echo"<tr class='buttontd'><td>Libell&eacute;</td><td>Prix</td><td>Stock</td></tr>";
			while($ro=mysql_fetch_array($res)){
				$str=$ro[4];
				$des=explode("\n",str_replace('"','\"',strip_tags($ro[5])));
				$desc="";
				for($e=0 ; $e<sizeof($des) ; $e++){
					$desc.=trim($des[$e])."\\n";
				}
				//if(trim($desc)!='') $str.="\\n".trim($desc);
				
				if(isset($_POST['chp_desc']) && trim($desc)!='') $str.="\\n".trim($desc); 
				if(isset($_POST['chp_plus1']) && trim($ro[6])!='') $str.="\\n".trim($ro[6]);
				if(isset($_POST['chp_plus2']) && trim($ro[7])!='') $str.="\\n".trim($ro[7]);
				if(isset($_POST['chp_taille']) && trim($ro[11])!='') $str.="\\n".trim($ro[11]);
				if(isset($_POST['chp_couleur']) && trim($ro[12])!='') $str.="\\n".trim($ro[12]); 
				if(isset($_POST['chp_libre']) && trim($ro[10])!='') $str.="\\n".trim($ro[10]); 
				if(isset($_POST['chp_ean']) && trim($ro[9])!='') $str.="\\n".trim($ro[9]);
				$ro[13]= abs($ro[13]);
				$str = str_replace("'","\'",$str);
				$prix= $ro[0];
				if($ro[2]>0) $prix = $ro[2];
				$remise=0;
				if($part=='achat'){
					$ro[14]="GAF_$ro[15]";
					$remise = $ro[16];
				}
				//if($ro[4]!=''){
					echo"<tr style='cursor:pointer' onclick=\"ajoutlign('$str','$prix',1,'$ro[1]','$ro[8]','$ro[14]','$remise')\"><td><b>$ro[4] $ro[8]</b></td><td align='right'>$prix &euro;</td><td align='right'>$ro[13]</td></tr>
					<tr><td class='petittext' style='padding-bottom:6px' colspan='3'>".str_replace('\n',' ',$str);
					if(mysql_num_rows($res)==1 ){					
						echo"<iframe onload=\"ajoutlign('$str','$prix',1,'$ro[1]','$ro[8]','$ro[14]','$remise')\" src='about:blank' style='width:1px; height:1px'></iframe>";
					}
					echo"</td></tr>";
				//}
			}			
		}
		else{
			echo"<tr><td>Aucun résultat<br></td></tr>";	
		}
		$reg_chp='';
		if(isset($_POST['chp_desc']))$reg_chp.="<desc>";
		if(isset($_POST['chp_plus1']) )$reg_chp.="<plus1>";
		if(isset($_POST['chp_plus2']) ) $reg_chp.="<plus2>";
		if(isset($_POST['chp_taille']) ) $reg_chp.="<taille>";
		if(isset($_POST['chp_couleur']) ) $reg_chp.="<couleur>";
		if(isset($_POST['chp_libre']) ) $reg_chp.="<libre>";
		if(isset($_POST['chp_ean']) ) $reg_chp.="<ean>";
		set_pref("compta_search_print.conf",$reg_chp,"x");
			
		echo"<table>";
	}
}
	
	
elseif($scan==='rappel'){ ///////////////////////////////////////////////////////////////////////////////////////////////////////// RAPPEL
	$ros = mysql_query("SELECT * FROM `gestion_rappel` WHERE `active`='0' ORDER BY `rappel`ASC");
	if(mysql_num_rows($ros)>0){
		$totem_mes='';
		while($rew=mysql_fetch_object($ros)){
			$commentaires  = $rew->commentaires ;
			$telephone = $rew->telephone;
			$dat = date("d/m/y H:i",strtotime($rew->rappel));
			$mid = $rew->id;
			$totem_mes .= "
				- <a href='./?option=gestion&gestion_rappel&edit=$mid' class='info'><b>$dat</b><span>
				$telephone<br>
				$commentaires
				</span></a><br>
				";
		}
	}
	else{
		$totem_mes = "aucun rappel en attente";
	}
	echo ($totem_mes);
}
elseif($scan==="gafour"){   ////////////////////////////////////////////////////////////////////////////////////////////////////// GESTION ARTICLES FOURNISSEUR ACHAT
  if(set_pref("ga.four.conf",stripslashes($_POST['w']))){
	  echo (" :D ");
  }
  else{
	  echo ("enregistrement échoué");
  }
}
elseif($scan==="aidew"){   ////////////////////////////////////////////////////////////////////////////////////////////////////////// AIDE WIDTH
	if(set_pref("aidew",stripslashes($_POST['w']))){
			echo (" ");
		}
		else{
			echo ("enregistrement échoué");
		}
}
elseif($scan==="locfil"){ //////////////////////////////////////////////////////////////////////////////////////////////////////////// LOC FILE
	$f =   stripslashes($_POST['f']);
		if(set_pref($f,stripslashes($_POST['w']))){
			echo (":)");
		}
		else{
			echo ("enregistrement échoué");
		}
}
elseif($scan==="panelw"){   //////////////////////////////////////////////////////////////////////////////////////////////////////////// PANEL WIDTH
	if(set_pref("panw",stripslashes($_POST['w']))){
			echo (" ");
		}
		else{
			echo ("enregistrement échoué");
		}
}
elseif($scan==="notepad"){   /////////////////////////////////////////////////////////////////////////////////////////////////////////// NOTEPAD
	if(set_pref("notepad.txt",stripslashes($_POST['texte']))){
			echo (" ");//enregistrement effectué avec succès
		}
		else{
			echo ("enregistrement échoué");
		}
}
elseif($scan==="calcul"){   //////////////////////////////////////////////////////////////////////////////////////////////////////// CALCUL
	if(set_pref("calcul.txt",stripslashes($_POST['texte']))){
			echo (";)");
		}
		else{
			echo (":(");
		}
}
elseif($scan==="calcutva"){   ////////////////////////////////////////////////////////////////////////////////////////////////////////// CALCUL TVA
	if(set_pref("calcutva.txt",stripslashes($_POST['texte']))){
			echo (";)");
		}
		else{
			echo (":(");
		}
}
elseif($scan==="agenda"){  ///////////////////////////////////////////////////////////////////////////////////////////////////// AGENDA
	if(isset($_POST['chdate'])){
		$id = $_POST['id'];
		$date = $_POST['date'];
		$j = substr($date,7,10);
		$h = substr($date,17,strlen($date));
		if($h=='none'){
			$sh='';	
		}
		else{
			if($h<10) $f='0'.$h;
			$sh=",`heure`='$h:00:00'";
		}
		if(mysql_query("UPDATE `".$_SESSION['agenda_base']."` SET `date`='$j' $sh WHERE `id`='$id'")){
			echo (returnn("modification de $id@agenda effectuée avec succès","009900",$vers,$theme));
		}
		else{
			echo (returnn("la modification de $id@$part a échouée","990000",$vers,$theme));
		}
	}
	elseif(isset($_POST['linkdb'])){ /////////////////////////// SEARCH
		$db = $_POST['linkdb'];
		if($option=='gestion'){
			if($db == 'gestion_articles') $db = 'gestion_artrad';
			if($db == 'gestion_rayons') $db = 'gestion_raytrad';	
		}	
		echo"<option value=''></option>";
		$res_field = mysql_list_fields($base,$db);
		$columns = mysql_num_fields($res_field);
		$nbf = -1;
		$head='';
		if($db=='gestion_artrad' || $db=='gestion_raytrad'){
			$head='`ref`';
		}
		else{
			$head='`id`';
		}
		for ($i=0 ; $i < $columns; $i++) {
			$field_type = mysql_field_type($res_field, $i);
			if($field_type=='string' || $field_type=='blob'){
				$field_name = mysql_field_name($res_field, $i);
				$head.=",`$field_name`";
				$nbf++;
			}  		
		}
		$command = substr($command,0,strlen($command)-3);
		$result1 = mysql_query("SELECT $head FROM `$db` ORDER BY 2");
		$i=0;
		if($result1 && mysql_num_rows($result1)>0){
			$nbres = mysql_num_rows($result1);
			 while ($row = mysql_fetch_array($result1)) {
				 $g_id = $row[0];
				 $stret='';
				 for($e=1 ; $e<$nbf ; $e++){
					$form  = strtolower(strip_tags($row[$e]));
					$stret .= $form." ";
				 }
				 $stret = substr($stret,0,50);
				 echo ("<option value='$g_id'>$stret</option>");
			}
		}
		else{
			echo ("<option>Aucun résultat</option>");
		}
		mysql_free_result($result1);
	
	}
	elseif(isset($_POST['maj'])){
		echo ("<div style='position:absolute;top:60px;left:0px;width:100%;text-align:center;z-index:500'><center>");
		$id = $_POST['id'];
		if($_POST['maj']=='Modifier'){
			if(updatedb($base,$_SESSION['agenda_base'],$id,'','utf8_decode')){
				echo (returnn("modification de $id@agenda effectuée avec succès","009900",$vers,$theme));
			}
			else{
				echo (returnn("la modification de $id@$part a échouée","990000",$vers,$theme));
			}
		}
		elseif($_POST['maj']=='Ajouter une date'){
			if(insertintodb($base,$_SESSION['agenda_base'],'utf8_decode')){
				echo (returnn("ajout de $edit effectué avec succès dans agenda","009900",$vers,$theme));
			}
			else{
				echo (returnn("l'ajout dans agenda a échouée","990000",$vers,$theme));
			}
		}
		$datm = date("Y-m-d",strtotime($_POST['date'].' '.$_POST['heure']));
		$h = date("G",strtotime($_POST['date'].' '.$_POST['heure']));
		echo ("<table class='contenu'>	
	<tr>	
	<td align='right'>		
	<a href='#' onclick=\"document.getElementById('htmlreturn').innerHTML='';\"><font class='petittext'>fermer</font> <img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></a>	
	</td></tr></table></center></div>");
	}
	elseif(isset($_POST['delete'])){
		echo ("<div style='position:absolute;top:60px;left:0px;width:100%;text-align:center;z-index:500'><center>");
		if(deletefromdb($base,$_SESSION['agenda_base'],$_POST['delete'])){
			echo (returnn("suppression effectuée avec succès","009900",$vers,$theme));
		}
		else{   
			echo (returnn("la suppression a échouée","990000",$vers,$theme));
		}
	echo ("<table class='contenu'>	
	<tr>	
	<td align='right'>		
	<a href='#' onclick=\"document.getElementById('htmlreturn').innerHTML='';\"><font class='petittext'>fermer</font> <img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></a>	
	</td></tr></table></center></div>");
	}
	elseif(isset($_POST['scan'])){
	
	$sqldate="now";
	$h="none";
	$print=1;
	$maj='agenda_totem';
	$before=0;
	$onlyon=0;
	if($_POST['scan']!=NULL){
		$sqldate=$_POST['scan'];
	}
	if($_POST['h']!=NULL){
		$h=$_POST['h'];
	}
	if($_POST['print']!=NULL){
		$print=$_POST['print'];
	}
	if($_POST['before']!=NULL){
		$before=$_POST['before'];
	}
	if($_POST['onlyon']!=NULL){
		$onlyon=$_POST['onlyon'];
	}
	if($_POST['dest']!=NULL){
		$maj=$_POST['dest'];
	}
	if($sqldate=="now"){
		$sqldate = date("Y-m-d");
	}
	$requ="`date`='$sqldate'";
	if(is_numeric($h)){
		$next = $h+1;
		$requ .=  " AND `heure` >= '$h:00:00'	AND `heure` < '$next:00:00' ";
	}
	if($before==1){
		$requ =  " ($requ) OR (`date`<NOW() AND `etat`='0') ";
	}
	if($onlyon==1){
		$requ =  " ($requ) AND `etat`='0' ";
	}
	/*if(!is_numeric($h)){
		echo "<div class='aam' ondblClick=\"contextage('$sqldate','10:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\">";
	 }
	 else{
		echo "<div style='height:100%; min-height:30px; padding:5px; padding-bottom:10px;'>";
	 }*/

	$midi=0;
	$res = mysql_query("SELECT * FROM `".$_SESSION['agenda_base']."` WHERE (`only`='0' OR `only`='$u_id') AND ($requ) ORDER BY `date`,`heure`");
	if($res && mysql_num_rows($res)>0){
			while($ro = mysql_fetch_object($res)){
				 $m_id = $ro->id;
				 $m_usr = $ro->usr;
				 $m_heure = $ro->heure;
				 $m_date = $ro->date;
				 $m_qui = (addslashes($ro->qui));
				 $m_type = (addslashes($ro->type));
				 $m_client = (addslashes($ro->client));
				 $m_note = (addslashes($ro->note));
				 $m_etat = $ro->etat;
				 $m_priority = $ro->priority;
				 $m_only = $ro->only;
				 $m_lien = $ro->lien;
				/* if(!is_numeric($h) && $midi==0 && str_replace(':','',$m_heure)>120000){
					 $midi=1;
					 echo"</div><div class='apm' ondblClick=\"contextage('$sqldate','14:00:00',event,'Ajouter une date','add&$part','99CCCC',0,'agenda_$sqldate"."none','$sqldate&h=none&print=1')\">";
				 }*/
				 $printki=$m_client;
				 $nots=substr($m_note,0,20);
				 $m_couleur = $ro->couleur;
					$size=2;
					$marj=1;
					$b_couleur = "-color:#$m_couleur";
					$co=' style="font-weight:bold" ';
						if($m_etat==1){
							$size=1;
							$marj=0;
							$co="color='#$m_couleur'";
							$b_couleur = ':none';
							$nots='';
							$b_couleur = 'none';
						}
						if($m_date==$sqldate && $m_heure>date('h:i:s') && $onlyon==1){
							$size=1;	
							//$b_couleur = ":#$m_couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
						}
						if($u_id!=$m_usr){
							$b_couleur = ":#$m_couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
						}
						
				 if(is_numeric($m_client) && mysql_query("SHOW COLUMNS FROM `clients`") ){
				 	$ris = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$m_client'");
					if($ris && mysql_num_rows($ris)==1){
						$ri = mysql_fetch_object($ris);
						$printki="<a href='./?option=site&clients&edit=$m_client'><font size='$size' $co>".($ri->nom)."&nbsp;</font></a>";
					}
				 }
				if($m_priority==0) $m_priority=1;
				
				
				$agebody='';
				$bodi=split("\n",strip_tags(trim($m_note)));
				for($e=0 ; $e<sizeof($bodi) ; $e++){
					$agebody.=trim(trim($bodi[$e]))." ";
				}
				$m_note = urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode(str_replace('"',"`",$agebody)))));
				
					
					if($print==2){
						$prio = "<font style='font-size:".($m_priority*3)."px'>".str_repeat("*",$m_priority)."</font>";		 
						$printo="<table cellpadding='1' cellspacing='0' width='90%' id='$m_date$h"."_$m_id' style='background$b_couleur;cursor:default;z-index:150;margin:$marj;";
						if(!is_numeric($h) && $midi==0 && str_replace(':','',$m_heure)>120000){
							 $midi=1;
							 $printo.="margin-top:10px;";
						 }
						$printo.="' onclick=\"fillage('$m_qui','$m_type','$m_client','$m_priority','$m_etat','$m_note','$m_only','$m_usr','$m_lien'); 			contextage('$m_date','$m_heure',event,'Modifier','update=$m_id&$part','$m_couleur',$m_id,'$maj','$sqldate&h=$h&print=$print&before=$before&onlyon=$onlyon');
				
				\"><tr><td><font size='$size' $co>$printki</font></td><td align='right'>$prio</td></tr></table>";
					}
					elseif($print==1){
						$prio = "<font style='font-size:".($m_priority*3)."px'>".str_repeat("*",$m_priority)."</font>";		 
						$printo="<table cellpadding='1' cellspacing='0' width='90%' id='$m_date$h"."_$m_id' style='background$b_couleur;cursor:default;z-index:150;margin:$marj;";
						if(!is_numeric($h) && $midi==0 && str_replace(':','',$m_heure)>120000){
							 $midi=1;
							 $printo.="margin-top:10px;";
						 }
						$printo.="' onclick=\"fillage('$m_qui','$m_type','$m_client','$m_priority','$m_etat','$m_note','$m_only','$m_usr','$m_lien'); contextage('$m_date','$m_heure',event,'Modifier','update=$m_id&$part','$m_couleur',$m_id,'$maj','$sqldate&h=$h&print=$print&before=$before&onlyon=$onlyon');\"><tr><td><font size='$size' $co>$printki </font></td><td align='right'>$prio</td></tr><tr><td colspan='2'><font size='$size' $co></font></td></tr></table>";
					}
					else{
						$prio = "<font style='font-size:".($m_priority*3)."px'>*</font>";
						$printo="<a onclick=\"
				document.agendaform.qui.value='$m_qui';
				document.agendaform.type.value='$m_type';
				document.agendaform.client.value='$m_client';				
				//document.agendaform.clients.value='$m_client';		
				document.agendaform.priority.value='$m_priority';					
				document.agendaform.etat.value='$m_etat';
				document.agendaform.note.value='$m_note';
				document.agendaform.only.value='$m_only';
				document.agendaform.usr.value='$m_usr';
				document.agendaform.lien.value='$m_lien';
				age_link_db();
				contextage('$m_date','$m_heure',event,'Modifier','update=$m_id&$part','$m_couleur',$m_id,'agenda_$sqldate$h','$sqldate&h=$h&print=$print&before=$before&onlyon=$onlyon');\"><font color='#$m_couleur'>$prio</font></a>";
					}
				echo ($printo);
			}
		}
	}
	echo"</div>";
}
elseif($scan==="mail"){   /////////////////////////////////////////////////////////////////////////////////////////////////////// MAIL


function imap_command ($query) {
	global $fp,$imapid;
    if ($fp) {
		fputs($fp,"$imapid ".$query . "\r\n");
		fputs("\r\n");
		fseek($fp, 4096,SEEK_CUR);
		$imapid++;
		$read = '';
		$buffer = 4096;
		$results = '';
		$offset = 0;
		while (strpos($results, "\r\n", $offset) === false) {
			if (!($read = fgets($fp, $buffer))) {
				$results = false;
				break;
			}
			if ( $results != '' ) {
				$offset = strlen($results) - 1;
			}
			$results .= $read;
		}
		return $results;
    } 
	else {
        return "error $query";
    }
}

 $res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') ORDER BY `nom`");
	if($res && mysql_num_rows($res)>0){
		while($ro = mysql_fetch_object($res)){
			$b_nom = $ro->nom;
			$b_mail = $ro->adresse;
			$b_id = $ro->id;
			$b_serveur = $ro->serveur;
			$b_login = $ro->login;
			$b_pass = $ro->pass;
			$b_dossier = $ro->dossier;
			$b_port = $ro->port;
			
			if(false !== $mbox = imap_open("\{$b_serveur:$b_port$b_dossier}",$b_login,$b_pass) ){
				$num_msg = imap_num_msg ($mbox);	
				$status = imap_status($mbox, "\{$b_serveur:$b_port$b_dossier}", SA_ALL);
				if ($status) {
					$num_msg = $status->messages;
					$difmes = $status->unseen;
					if($difmes>0){				
						echo ("- <a href='./?option=mail&b=$b_id&lecture' class='info'>$b_nom&nbsp;<b>($difmes)</b><span><b>$b_login</b><br>$num_msg messages</span></a><br>");				
					}
					else{
						echo ("- <a href='./?option=mail&b=$b_id&lecture' class='info'>$b_nom&nbsp;<span><b>$b_login</b><br>$num_msg messages</span></a><br>");
					}
				}
			}
			/*elseif(false !== $fp = fsockopen($b_serveur, $b_port, $errno, $errstr, 15)){
				$imapid=1;
				if(ereg('OK',imap_command ("login $b_login $b_pass"))){
					$num_msg =  imap_command ("status INBOX (messages)");
					//$num_msg =  imap_command ("status INBOX (messages)");
					$difmes =  imap_command ("status INBOX (unseen)");
					//$difmes  = sscanf($difmes, '* STATUS "INBOX" (%d MESSAGE', $difmes);
					//$difmes =  imap_command ("status INBOX (unseen)");
					if($difmes>0){				
						echo ("- <a href='./?option=mail&b=$b_id&lecture' class='info'>$b_nom&nbsp;<b>($difmes)</b><span><b>$b_login</b><br>$num_msg messages</span></a><br>");				
					}
					else{
						echo ("- <a href='./?option=mail&b=$b_id&lecture' class='info'>$b_nom&nbsp;<span><b>$b_login</b><br>$num_msg messages ($difmes)</span></a><br>");
					}
					imap_command('LOGOUT');
				}
			}*/
			else{
				//print_r(error_get_last());

				echo"<a class='info'><font color='CCCCCC'>- $b_nom</font><span>".implode(' ',imap_errors())."</span></a><br>";
			}
		}
		mysql_free_result($res);
  	}
}
elseif($scan==="color"){ /////////////////////////////////////////////////////////////////////////////////////////////////// COLOR

if(isset($_POST['field_name']) && isset($_POST['field_value']) && isset($_POST['actiona'])){
	
	$field_name=$_POST['field_name'];
	$field_value=$_POST['field_value'];
	$actiona=stripslashes($_POST['actiona']);
	
	$taille=20;
	if($_POST['taille']!=NULL){
		$taille=$_POST['taille'];
	}
	$id=0;
	if($_POST['id']!=NULL){
		$id=$_POST['id'];
	}
	$preci=abs(get_pref("colorpreci.conf"));
	if($_POST['preci']!=NULL){
		$preci=abs($_POST['preci']);
		set_pref("colorpreci",$preci);
	}
	if($preci<2 || $preci>20){
		$preci=10;
	}
	$a = $_POST['a'];
	$func = $_POST['fun'];
	$ten = $_POST['tem'];
	$ty = $_POST['t'];

	$rvb=array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
		$ret="<p align='left'>Précision de la palette :";
		for($p=2 ; $p<20 ; $p+=2){
			$nu = round((20-$p)/2);
			if($p==$preci){
				$nu="<u>$nu</u>";
			}
			$ret.="<a onclick=\"choosecolor('$a','$func','$ten','$ty','','$p')\">$nu</a> ";
		}
		$ret.="</p><table cellspacing=\"0\" cellpadding='0' style=\"border-style:solid;border-color:#000000;border-width:1px\">
			
			";
	//CLAIR
			for($a=15 ; $a>0 ; $a-=$preci){			
					$r=15;$v=0;$b=0;
					$ret.="<tr>";
					for($m=0 ; $m<6 ; $m++){
					 /////////////////////COULEUR
						if($m==1 || $m==4){$c = "v";}
						if($m==0 || $m==3){$c = "b";}
						if($m==2 || $m==5){$c = "r";}
						/////////////////////SENS
						if($m==0 || $m==2 || $m==4){$s = 1;}
						else{$s = -1;}
							/////////////////////// GO
						for($i=15 ; $i>0 ; $i--){		
						 if($r<$a){$r=$a;}if($v<$a){$v=$a;}if($b<$a){$b=$a;}
							if($r>15){$r=15;}if($v>15){$v=15;}if($b>15){$b=15;}
							$rc_r=$rvb[$r];$rc_v=$rvb[$v];$rc_b=$rvb[$b];
							$r+=$s;$v+=$s;$b+=$s;
							$$c -= $s;
							$color="$rc_r$rc_r$rc_v$rc_v$rc_b$rc_b";
							$action = str_replace("COLOR",$color,$actiona);
							$ret.="<td style=\"background-color:#$color;font-size:".$taille."px\"><a href='#a$field_name' onclick=\"$action\" 
											onmouseover=\"document.getElementById('divo$field_name').style.backgroundColor='$color';document.getElementById('pickercode').innerHTML='$color';\">&nbsp;</a></td>";	
						}
					}		
					$moy = $rvb[($r+$v+$b)/3	];	
					$color="$moy$moy$moy$moy$moy$moy";
					$action = str_replace("COLOR",$color,$actiona);
					$ret.="<td style=\"background-color:#$color;font-size:".$taille."px\"><a href='#a$field_name' onclick=\"$action\"
											onmouseover=\"document.getElementById('divo$field_name').style.backgroundColor='$color';document.getElementById('pickercode').innerHTML='$color';\">&nbsp;</a></td></tr>";			
				}
			//SOMBRE
			for($a=15 ; $a>0 ; $a-=$preci){			
					$r=15;$v=0;$b=0;
					$ret.="<tr>";
					for($m=0 ; $m<6 ; $m++){
					 /////////////////////COULEUR
						if($m==1 || $m==4){$c = "r";}
						if($m==0 || $m==3){$c = "v";}
						if($m==2 || $m==5){$c = "b";}
						/////////////////////SENS
						if($m==0 || $m==2 || $m==4){$s = 1;}
						else{$s = -1;}
							/////////////////////// GO
						for($i=0 ; $i<15 ; $i++){		
						 if($r>$a){$r=$a;}if($v>$a){$v=$a;}if($b>$a){$b=$a;}
							if($r<0){$r=0;}if($v<0){$v=0;}if($b<0){$b=0;}
							$rc_r=$rvb[$r];$rc_v=$rvb[$v];$rc_b=$rvb[$b];
							$$c += $s;
							$color="$rc_r$rc_r$rc_v$rc_v$rc_b$rc_b";
							$action = str_replace("COLOR",$color,$actiona);
							$ret.="<td style=\"background-color:#$color;font-size:".$taille."px\"><a href='#a$field_name' onclick=\"$action\" 
											onmouseover=\"document.getElementById('divo$field_name').style.backgroundColor='$color';document.getElementById('pickercode').value='$color';\">&nbsp;</a></td>";	
						}
					}				
					$moy = $rvb[($r+$v+$b)/3	];	
					$color="$moy$moy$moy$moy$moy$moy";
					$action = str_replace("COLOR",$color,$actiona);
					$ret.="<td style=\"background-color:#$color;font-size:".$taille."px\"><a  href='#a$field_name' onclick=\"$action\"
											onmouseover=\"document.getElementById('divo$field_name').style.backgroundColor='$color';document.getElementById('pickercode').value='$color';\">&nbsp;</a></td></tr>";		
				}			
	
								$ret.="</table>";
					echo ($ret);
	}
}
echo"\n\n\n\n<!--\nscan = ".$scan."\n";
foreach($_POST as $k=>$v){
	echo"$k = $v\n";	
}
echo"-->";
mysql_close($conn);
ob_end_flush();
exit();
}
else{
	echo ("CONNECTION ERROR $base@$host");
}

?>