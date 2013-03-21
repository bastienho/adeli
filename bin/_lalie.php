<?php // 750 > LA Lettre d'Information Electronique ;

$dblist=$laliedb;
$edit_dblist=true;
$email_field='email';
	if( $db_for_lalie!=''){
		if(isset($_GET['use_db'])){
			$_SESSION['use_db']=$_GET['use_db'];		   
		}
		if(isset($_SESSION['use_db']) && !mysql_query("SHOW COLUMNS FROM ".$_SESSION['use_db']) ){
			$return.=returnn("la base <b>".$db_names[$_SESSION['use_db']]."</b> n'est pas accessible","FF9900",$vers,$theme);	
			unset($_SESSION['use_db']);
		}
		if(isset($_SESSION['use_db']) ){			
			$verifupdt = mysql_query("DESC ".$_SESSION['use_db']);
			$email_field='';
			while($ro = mysql_fetch_object($verifupdt)){
				if(ereg('mail',$ro->Field)){
					$email_field = 	$ro->Field;
					break;
				}
			}
			if($email_field!=''){
				$dblist=$_SESSION['use_db'];
				$edit_dblist=false;
				$return.=returnn("Vous utilisez la base <b>".$db_names[$_SESSION['use_db']]."</b> comme carnet d'adresse","009900",$vers,$theme);	
			}
			else{
				$return.=returnn("la base <b>".$db_names[$_SESSION['use_db']]."</b> ne peut être utilisée comme carnet d'adresse","FF9900",$vers,$theme);	
				unset($_SESSION['use_db']);
			}
		}
	}
	if($order=="id" || trim($order)==""){
		$order="groupe";
	}

	
	$verifupdt = mysql_query("DESC `$lalierp`");
	$allchamps = array();
	while($ro = mysql_fetch_object($verifupdt)){
		array_push($allchamps,$ro->Field);
	}
	if(!in_array("code",$allchamps)){
		mysql_query("ALTER TABLE `$lalierp` ADD `code` longtext NOT NULL");
	}
	if(!in_array("moule",$allchamps)){
		mysql_query("ALTER TABLE `$lalierp` ADD `moule` varchar(255) NOT NULL default ''");
	}
	if(!in_array("active",$allchamps)){
		mysql_query("ALTER TABLE `$lalierp` ADD `active` INT( 1 ) NOT NULL");
		mysql_query("UPDATE `$lalierp` SET `active`=1");			
	}	
	if(!in_array("secure",$allchamps)){
		mysql_query("ALTER TABLE `$lalierp` ADD `secure` varchar(255) NOT NULL default ''");
	}
	if(!in_array("dests",$allchamps)){
		mysql_query("ALTER TABLE `$lalierp` ADD `dests` longtext NOT NULL");
	}	
	if($edit_dblist){
		$verifupdt = mysql_query("DESC `$dblist`");
		$allchamps = array();
		while($ro = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$ro->Field);
		}
		if(!in_array("groupe",$allchamps)){
			mysql_query("ALTER TABLE `$dblist` ADD `groupe` varchar(255) NOT NULL default ''");
		}
		if(!in_array("email",$allchamps)){
			mysql_query("ALTER TABLE `$dblist` ADD `email` varchar(255) NOT NULL default ''");
		}
		if(!in_array("portable",$allchamps)){
			mysql_query("ALTER TABLE `$dblist` ADD `portable` varchar(255) NOT NULL default ''");
		}
		if(!in_array("adresse",$allchamps)){
			mysql_query("ALTER TABLE `$dblist` ADD `adresse` text NOT NULL");
		}		
	}

	
if(!is_dir("lalie")){				@mkdir("lalie",0777);	} 
if(!is_dir("img")){				@mkdir("img",0777);	} 
if(!is_dir("lalie/models")){		@mkdir("lalie/models",0777);	} 
if(!is_dir("lalie/drafts")){		@mkdir("lalie/drafts",0777);	} 

function correctgroup($group){
	global $return;
	global $vers;
	global $theme;
	if(ereg("[[:punct:][^_]]",$group)||ereg("[êèéëÊÈÊôöòÔÔÒ@âàâÂÀÄûüùÛÛÙïîìÎÎÌ]",$group)){
		$return.=returnn("les caractères accentués ou de ponctuation ne sont pas supportés dans les noms de groupes","FF9900",$vers,$theme);
	}
	$group = str_replace(" ","_",$group);
	$group = str_replace("-","_",$group);
	$group = str_replace("sans_groupe","",$group);
	$group = str_replace("\&","_et_",$group);
	$group = ereg_replace("[\*\+\%\:;\/\-\>\<]","_",$group);
	$group = str_replace(".","_",$group);
	$group = ereg_replace("[!?,]","",$group);
	$group = str_replace("'","",$group);
	$group = ereg_replace("[êèéëÊÈÊ]","e",$group);
	$group = ereg_replace("[ôöòÔÔÒ]","o",$group);
	$group = ereg_replace("[@âàâÂÀÄ]","a",$group);
	$group = ereg_replace("[ûüùÛÛÙ]","u",$group);
	$group = ereg_replace("[ïîìÎÎÌ]","i",$group);
	return $group;
}
function correctportable($numero){
	$numero = ereg_replace("[[:space:]]","",$numero);
	$numero = ereg_replace("[[:punct:]]","",$numero);
	$numero = ereg_replace("[[:alpha:]]","",$numero);
	return $numero;
}
insert('_tools');
insert('_lalie_async');
if($edit_dblist){
	//echo"<!-- SELECT * from `$dblist` WHERE 1 $wherelalaie -->";
	//////////////////////////////////////////////////////////////////////[ DELETE GROUPE ]
	if(isset($_GET['sug'])){
	  
	  $sug = $_GET['sug'];
			$res = mysql_query("DELETE FROM `$dblist` WHERE `groupe`='$sug' $wherelalaie");
	  if($res){
					$afc = abs(mysql_affected_rows($res)); 
					$return.=returnn("effacement de <b>$sug</b>  effectué avec succès,<br><b>$afc</b> contacts concernés","009900",$vers,$theme);
				}
				else{
					$return.=returnn("erreur lors de l'effacement de <b>$sug</b>","990000",$vers,$theme);
				}
	  
	}
	//////////////////////////////////////////////////////////////////////[ DELETE ARCHIVE ]
	if(isset($_GET['dela'])){
	  $dela = $_GET['dela'];
		
		//if(deletefromdb($dbase,$lalierp,$dela)){
		if(mysql_query("UPDATE $lalierp SET `active`='0' WHERE `id`='$dela'")){
			$return.=returnn("suppression effectuée avec succès","009900",$vers,$theme);
			//if(is_file("lalie/$dela.pdf")) unlink("lalie/$dela.pdf");
		}
		else{
			$return.=returnn("la suppression a échouée","990000",$vers,$theme);
		}
		
	}
	//////////////////////////////////////////////////////////////////////[ DELETE ]
	if(isset($_GET['laliedel'])){
	  
	  $result = mysql_query("SELECT * from $dblist WHERE 1 $wherelalaie");
	  while ($row = mysql_fetch_object($result)) {
		  $this_id = $row->id;
		  $this_mail = $row->email;
		  if(isset($_POST["d".$this_id])){
			  if(mysql_query("DELETE from `$dblist` WHERE id='$this_id'")){
			   $return.=returnn("effacement de <b>$this_mail</b>  effectué avec succès","009900",$vers,$theme);
			  }
			  else{
			   $return.=returnn("erreur lors de l'effacement de <b>$this_mail</b>","990000",$vers,$theme);
			  }
		  }
	  }
	  
	}
	//////////////////////////////////////////////////////////////////////[ AFFECT ]
	if(isset($_GET['affectgroup'])){
	  
	  $affectgroup = correctgroup($_GET['affectgroup']);
	  $result = mysql_query("SELECT * from `$dblist` WHERE 1 $wherelalaie");
	  while ($row = mysql_fetch_object($result)) {
		  $this_id = $row->id;
		  $this_mail = $row->email;
		  if(isset($_POST["d".$this_id])){
			  if(mysql_query("UPDATE `$dblist` SET `groupe`='$affectgroup' WHERE id='$this_id'")){
			   $return.=returnn("affectation de <b>$this_mail</b> à <b>$affectgroup</b> effectué avec succès","009900",$vers,$theme);
			  }
			  else{
			   $return.=returnn("erreur lors de l'affectation de <b>$this_mail</b> à <b>$affectgroup</b>","990000",$vers,$theme);
			  }
		  }
	  }
	 
	}
	//////////////////////////////////////////////////////////////////////[ RENAME ]
	if(isset($_GET['renamegroup'])){
		$oldgroup = $_GET['oldgroup'];
		$group = correctgroup($_GET['renamegroup']);
		
		 if(mysql_query("UPDATE `$dblist` SET `groupe`='$group' WHERE `groupe`='$oldgroup' $wherelalaie")){
		   $return.=returnn("changement d'intitulé effectué avec succès","009900",$vers,$theme);
		  }
		  else{
		   $return.=returnn("erreur lors du changement d'intitulé","990000",$vers,$theme);
		  }
		 
	}
	//////////////////////////////////////////////////////////////////////[ ADD EMPTY GROUP ]
	if(isset($_POST['emptygroup'])){
		$addg = correctgroup($_POST['emptygroup']);
			$command = "";	
			
			$res_field = mysql_list_fields($base,$dblist);
			$columns = mysql_num_fields($res_field);					   
		   for ($i=0 ; $i < $columns; $i++) {
			$field_name = trim(mysql_field_name($res_field, $i));
			$field_value="";
			if(ereg("mail",$field_name)){
				$field_value = "groupe@lalie.wac.fr";
			}
			elseif(ereg("group",$field_name)){
				$field_value = $addg;
			}
			elseif($field_name=="clid"){
									$field_value = $x_id;
								}
			$command.="'$field_value', ";
		   }
		   $videcom = trim(str_replace(",","",str_replace("'","",$command)));
		  if($videcom != ""){
			   $command = substr($command,0,strlen($command)-2);
						
			   if(mysql_query("INSERT INTO `$dblist` VALUES($command)")){
							$return.=returnn("groupe <b>$addg</b> créé avec succès","009900",$vers,$theme);
			   }
			   else{ 
							$return.=returnn("<b>$addg</b> n'a pas été créé","990000",$vers,$theme); 
			   }
		   }
		   else{
				$return.=returnn("erreur de commande ($addg)","990000",$vers,$theme);
		   }
			
	}
	//////////////////////////////////////////////////////////////////////[ IMPORT UPLOAD ]
	if(isset($_GET['impupload'])){
		$allcons = split("\n",$_SESSION["imp"]);
		
		$dedansg=$_SESSION['updedansg'];
		$return.=returnn("tentative d'importation","FF9900",$vers,$theme);
		
		 $res_field = mysql_list_fields($base,$dblist);
		   $columns = mysql_num_fields($res_field);
		   $email = $_POST["email"];
		   
		// print_r($dedansg);
		 if(sizeof($dedansg)==0){
			$dedansg = array("import_".date("YmdHis"));
		 }
		/* if(isset($_POST["is_new_g_lalie"]) && $_POST["newgroup"]!=""){
			array_push($dedansg,correctgroup($_POST["newgroup"]));
		}*/
		   
		for($gr=0 ; $gr<sizeof($dedansg) ; $gr++){
			$group = $dedansg[$gr];
			
			if($group==''){
				$group = correctgroup($_POST['group']);
			}
			$res_field = mysql_list_fields($base,$dblist);
			$columns = mysql_num_fields($res_field);
		
			$chamdeva = array();
			$vadecham = array();
			$mkch = split("[,;]",$allcons[0]);
			for($e=0 ; $e<sizeof($mkch)-1 ; $e++){
				if(isset($_POST["champ$e"]) && $_POST["champ$e"]!=""){
					$chamdeva[$_POST["champ$e"]]=$e;
					$vadecham[$e]=$_POST["champ$e"];
					//echo"<br>-$e ".$_POST["champ$e"];
				}
			}
			$nbvd = sizeof($mkch)-1;
								   
			for($m=0 ; $m<sizeof($allcons) ; $m++){
				if(trim($allcons[$m])!=""){
					$verif=" AND `groupe`='$group'";
					$ligne = split("[,;]",$allcons[$m]);
					//echo"<br>- $ligne[0]";
					$hea = "`groupe`";	
					$command = "'$group'";	
					   for ($i=0 ; $i < $columns; $i++) {
						$field_name = trim(mysql_field_name($res_field, $i));						
							if( isset($chamdeva[$field_name]) ){
								$field_value = '';
								if($field_name=="clid"){
									$field_value = $x_id;
								}
								/*elseif( isset($chamdeva[$field_name]) ){
									$field_value = unquote($ligne[$chamdeva[$field_name]]);
								}*/
								else{
									for($e=0 ; $e<$nbvd ; $e++){
										if($field_name == $vadecham[$e]){								
											$field_value .= str_replace("'","''",unquote($ligne[$e])).' ';
										}
									}
								}
								$field_value = trim($field_value);
								if($field_name=="email" && $field_value!=''){
									$verif.=" AND `email`='$field_value' "; //  AND `portable`!=''
								}
								if($field_name=="portable" && $field_value!=''){
									$verif.=" AND `portable`='$field_value'"; // AND `portable`!='' 
								}
								//$field_value = str_replace("'","''",$field_value);
								$hea.=",`$field_name`";
								$command.=",'$field_value'";
							}
					   }
				   $reso = mysql_query("SELECT `email` FROM `$dblist` WHERE 1 $verif");
				   if(mysql_num_rows($reso)==0 || $verif==''){
					   	  $videcom = trim(str_replace(",","",str_replace("'","",$command)));
						  if($videcom != ""){
							   //$hea = substr($hea,0,strlen($hea)-2);
							   //$command = substr($command,0,strlen($command)-2);
							   if(mysql_query("INSERT INTO `$dblist` ($hea) VALUES($command)")){
									$return.=returnn("élément $m ajouté avec succès au groupe <b>$group</b>","009900",$vers,$theme);
							   }
							   else{ 
									$return.=returnn("élément $m n'a pas été ajouté au groupe <b>$group</b>","990000",$vers,$theme); 
							   }
						   }
						   else{
								$return.=returnn("erreur de commande (élément $m, groupe <b>$group</b>)","990000",$vers,$theme);
						   }							
					}
					else{
							$ro = mysql_fetch_array($resc);
						$return.=returnn("(élément $m $ro[0]) déjà présent dans le groupe <b>$group</b>","FF9900",$vers,$theme);
					}			
				
				}
			}	
		}
	}
	//////////////////////////////////////////////////////////////////////[ IMPORT SOLO ]
	if(isset($_GET['addsolo'])){
		
		$dedansg=array();
		$result = mysql_query("SELECT DISTINCT `groupe` FROM `$dblist` WHERE 1 $wherelalaie");
		while($row = mysql_fetch_object($result)){
			$thisgroup = correctgroup($row->groupe);
			if(isset($_POST["is_$thisgroup"])){
				array_push($dedansg,$thisgroup);
			}
		}
		if(isset($_POST["is_new_g_lalie"]) && $_POST["newgroup"]!=""){
			array_push($dedansg,correctgroup($_POST["newgroup"]));
		}
		
		 $res_field = mysql_list_fields($base,$dblist);
		   $columns = mysql_num_fields($res_field);
		   $email = $_POST["email"];
		  if( ereg("@",$email) || $_POST['adresse']!='' || $_POST['portable']!='' ){
		   
		for($gr=0 ; $gr<sizeof($dedansg) ; $gr++){
			$group = $dedansg[$gr];
			$command="";
			 $rys = mysql_query("SELECT * FROM `$dblist` WHERE  `groupe`='$group' AND `email`='$email' $wherelalaie");
			if(mysql_num_rows($rys) == 0 || $email==''){
		   for ($i=0 ; $i < $columns; $i++) {
			$field_name = mysql_field_name($res_field, $i);		
			if($field_name=="groupe"){
				$field_value = $group;
			}
			elseif($field_name=="clid"){
									$field_value = $x_id;
								}
			else{
				$field_value = str_replace("'","''",stripslashes(urldecode($_POST[$field_name])));
			}
			$command.="'$field_value', ";
		   }
		   $videcom = str_replace("'","",$command);
		   $videcom = trim(str_replace(",","",$videcom));
		   if($videcom != ""){
			   $command = substr($command,0,strlen($command)-2);
			   if(mysql_query("INSERT INTO `$dblist` VALUES($command)")){
					$return.=returnn("$email ajouté avec succès au groupe <b>$group</b>","009900",$vers,$theme);
			   }
			   else{ 
					$return.=returnn("$email n'a pas été ajouté  dans le groupe <b>$group</b>","990000",$vers,$theme); 
			   }
		   }
		   else{
				$return.=returnn("erreur de commande ($email)","990000",$vers,$theme);
		   }
		   }
			else{
				$return.=returnn("$email est déja présent dans le groupe <b>$group</b>","FF9900",$vers,$theme);
			}
	   }
	   }
	   else{
		$return.=returnn("l'email $email est mal formaté","990000",$vers,$theme); 
	   }
		
				
	}
	//////////////////////////////////////////////////////////////////////[ IMPORT ]
	if(isset($_GET['mkimport'])){
		
		$import = $_POST['import'];
		$posi = $_POST['posi'];
		$oktounn=1;
		//$group = correctgroup($_POST['group']);
			
		if($posi=="email"){
			$nbat = substr_count($import,"@");
			$nbsep = substr_count($import,"[,;\n]");
			if( $nbat == $nbsep || $nbat == $nbsep-1 ){
				$return.=returnn("Le texte que vous avez insérez est ma formaté !","990000",$vers,$theme);
				$oktounn=0;
			}
		}
		if($oktounn==1){
			$import = split("[,;\n]",stripslashes($import));
			$dedansg=array();
			$result = mysql_query("SELECT DISTINCT `groupe` FROM `$dblist` WHERE 1 $wherelalaie");
			while($row = mysql_fetch_object($result)){
				$thisgroup = correctgroup($row->groupe);
				if(isset($_POST["is_$thisgroup"])){
					array_push($dedansg,$thisgroup);
				}
			}
			if(isset($_POST["is_new_g_lalie"]) && $_POST["newgroup"]!=""){
				array_push($dedansg,correctgroup($_POST["newgroup"]));
			}
			
			
			for($gr=0 ; $gr<sizeof($dedansg) ; $gr++){
				$group = $dedansg[$gr];
			
			
			$res_field = mysql_list_fields($base,$dblist);
			$columns = mysql_num_fields($res_field);
							   
			for($m=0 ; $m<sizeof($import) ; $m++){
			if(trim($import[$m])!=""){
			
			$import[$m] = strtolower(ereg_replace("[&\" ]","",trim($import[$m])));
			if($posi=="portable"){
				$import[$m] = ereg_replace("[\. ]","",$import[$m]);
			}
			$res = mysql_query("SELECT * FROM `$dblist` WHERE  `groupe`='$group' AND ( `$posi`='$import[$m]' OR `$posi`='$import[$m]') $wherelalaie");
				if(mysql_num_rows($res) == 0){
					if( ($posi=="email" && ereg("@",$import[$m])) || ($posi=="portable" && is_numeric($import[$m]))){
					
								$command = "";						   
							   for ($i=0 ; $i < $columns; $i++) {
								$field_name = trim(mysql_field_name($res_field, $i));
								$field_value="";
								if($posi=="email" && ereg("mail",$field_name)){
									$field_value = $import[$m];
								}
								elseif($posi=="portable" && $field_name=="portable"){
									$field_value = $import[$m];
								}
								elseif(ereg("group",$field_name)){
									$field_value = $group;
								}
								elseif($field_name=="clid"){
									$field_value = $x_id;
								}
								$command.="'$field_value', ";
							   }
							   $videcom = trim(str_replace(",","",str_replace("'","",$command)));
							  if($videcom != ""){
								   $command = substr($command,0,strlen($command)-2);
								   if(mysql_query("INSERT INTO `$dblist` VALUES($command)")){
										$return.=returnn("$import[$m] ajouté avec succès au groupe <b>$group</b>","009900",$vers,$theme);
								   }
								   else{ 
										$return.=returnn("$import[$m] n'a pas été ajouté  dans le groupe <b>$group</b> (".mysql_error($conn).")","990000",$vers,$theme); 
								   }
							   }
							   else{
									$return.=returnn("erreur de commande ($import[$m])","990000",$vers,$theme);
							   }
						
					}
					else{
						$return.=returnn("$import[$m] est mal formaté","990000",$vers,$theme);
					}
				}
				else{
					$return.=returnn("$import[$m] est déja présent dans le groupe <b>$group</b>","FF9900",$vers,$theme);
				}
				}
			}
		   }
			
		}	
		
				
	}	
	//////////////////////////////////////////////////////////////////////[ LIST GROUPS ]	
	
		$groups = array();
		$result = mysql_query("SELECT DISTINCT `groupe` FROM `$dblist` WHERE 1 $wherelalaie");
		while($row = mysql_fetch_object($result)){
			$thisgroup = $row->groupe;
			if(correctgroup($thisgroup) != $thisgroup){
				$thisgoodgroup = correctgroup($thisgroup);
				$thisgroup = str_replace("'","''",$thisgroup);
				mysql_query("UPDATE `$dblist` SET `groupe`='$thisgoodgroup' WHERE `groupe`='$thisgroup' $wherelalaie");
				$thisgroup = $thisgoodgroup;
			}
			if($thisgroup == ""){
				$thisgroup = "sans_groupe";
			}
			array_push($groups,$thisgroup);
		}
	
	sort($groups);
	
	$wherelalaie .= " AND `email`!='groupe@lalie.wac.fr'";
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////[ LIST CONTACTS ]		
	//echo'
	
	
	$contacts = array();
	$jscontacts="";
	$mobs=array();
	$jsmobs="";
	if($part=="mail" || $part=="sms" || $part=="edition_mail"){
		
		$result = mysql_query("SELECT DISTINCT(`email`) from `$dblist` WHERE `groupe`!='desinscrits' $wherelalaie ORDER BY `email`");
		while($row = mysql_fetch_object($result)){
			$thisemail = trim($row->email);
			if(substr_count($thisemail,"@")==1 ){
				array_push($contacts,$thisemail);
				$jscontacts.="$thisemail;";
			}
		}
		$result = mysql_query("SELECT DISTINCT(`portable`) from `$dblist` WHERE  `groupe`!='desinscrits' $wherelalaie ORDER BY `portable`");
		while($row = mysql_fetch_object($result)){
			$thismob = $row->portable;
			if(correctportable($thismob) != $thismob){
				mysql_query("UPDATE `$dblist` SET `portable`='".correctportable($thismob)." WHERE `portable`='$thismob' $wherelalaie");
			}
			if(strlen($thismob)>=10 && is_numeric($thismob)){
				array_push($mobs,$thismob);
				$jsmobs.="$thismob,";
			}
		}
		
	}
	echo"
	<div style='margin:20px'>
   ";
   if(isset($_GET['choose_db'])){
	   echo"<form method='get' action='./?option=$option&part=$part&choose_db'>
	   <select name='use_db'>
	   $db_for_lalie
	   </select>
	   <input type='submit' class='buttontd' value='Ok'> <a href='./?option=$option&part=$part&use_db'>Annuler</a>
	   </form>";
   }
   else{
		//echo"<a href='./?option=$option&part=$part&choose_db'>Utiliser une autre base de données comme carnet d'adresse</a>";   
   }
   echo"
   </div>";
}
else{
	echo"
	<div style='margin:20px'>
   Vous utiliser la base de données <b>".$db_names[$dblist]."</b> comme carnet d'adresse. <a href='./?option=$option&part=$part&use_db'>Annuler</a>
   </div>";
	if($part=="mail" || $part=="sms" || $part=="edition_mail"){
		
		$result = mysql_query("SELECT DISTINCT(`$email_field`) from `$dblist` WHERE ORDER BY `$email_field`");
		while($row = mysql_fetch_object($result)){
			$thisemail = trim($row->$email_field);
			if(substr_count($thisemail,"@")==1 ){
				array_push($contacts,$thisemail);
				$jscontacts.="$thisemail;";
			}
		}
		$jsmobs="";
		
	}
}


echo"
<SCRIPT LANGUAGE=JavaScript>	
	function context(cib,tar,loca,e){	
			if(document.all){
				curX = event.clientX;
				curY = event.clientY;
			}			
			//netscape 4
			if(document.layers){
				curX = e.pageX;
				curY = e.pageY;
			}			
			//mozilla
			if(document.getElementById){
				curX = e.clientX;
				curY = e.clientY;
			}			
			document.getElementById(cib).style.visibility=\"visible\";
			targ = document.getElementsByName(tar);
			targ[0].src=loca;
			document.getElementById(cib).style.left=curX;
			document.getElementById(cib).style.top=curY;
	}
	function closconte(cib){	
			document.getElementById(cib).style.visibility=\"hidden\";
	}	
</script>
<div id='menu_date' style=\"position:absolute;left:0px;top:0px;width:100%;height:100%;visibility:hidden;background:url('$style_url/$theme/bgalpha.gif');z-index:200\">
				 <table style=\"width:100%;height:100%\">
				 <tr><td  align='center' valign='middle'>	 
					  <table cellspacing='1' cellpadding='2' border='0' class='alert'>
					   <tr><td align='left'>
					   <iframe name='cal' src='about:blank' width='170' height='270' scrolling='no' frameborder='0'></iframe>
					   </td></tr>
					   </table>		   
					  </td></tr>
					   </table>
				  </div>";

if($part=="contacts"){	

			if(!isset($_GET['sg'])){  ////////////////////////////////////////////////////// G R O U P E S	
								echo"
								<script language='javascript'>
								
								function confdelg(ki){
							glok = confirm(\"êtes vous sûr de vouloir supprimer ce groupe et tous les contacts qu'il contient ?\");
							if(glok){
								document.location='./?option=$option&part=$part&sug='+ki;
							}		
							}
							
							function renameg(legrou){
								glok = prompt(\"veuillez entrer un nouveau nom\",legrou);
								if(glok){
									document.location='./?option=$option&part=$part&renamegroup='+glok+'&oldgroup='+legrou;
								}
							}		
							
				</script>
								
								<table width='80%' cellspacing='0' cellpadding='0' border='0' class='cadrebas'>
		   <tr><td class='buttontd'><span class='textegras'><b>vos groupes de contacts</b></span> </td></tr>
		   <tr><td>";
					for($z=0 ; $z<sizeof($groups) ; $z++){
							$fdg = $groups[$z];
							if($groups[$z]=="sans_groupe"){
								$fdg = "";
							}
							
								$res = mysql_query("SELECT `id` FROM `$dblist` WHERE `groupe`='$fdg' $wherelalaie");
								$nbcon = mysql_num_rows($res);
								
								$hg = str_replace("_"," ",$groups[$z]);
								$color='000000';
								if($hg == 'desinscrits'){
									$color='990000';
									$hg="Désinscrits";
								}
							echo"
							<table class='cadrebas' width='80%' cellspacing='0' cellpadding='0' border='0' style='float:left;width:150px;height:130px;margin:2px;'>
		   <tr><td class='buttontd'>&nbsp;</td></tr>
		   <tr><td><p align='center'>
					<a href='./?option=$option&part=$part&sg=$fdg'><b><font color='#$color'>$hg</font></b><br>$nbcon contacts</a>
					</p></td></tr>
					<tr><td valign='bottom' align='right'>
					<a href='#' onclick=\"renameg('$fdg')\" title=\"renomer\"><img src=\"$style_url/lalie/renomer.png\" border='none' alt='renomer'></a>
					<a href='./?option=site&part=$laliedb&subpart=exporter&d&filt=groupe&filtv=$fdg' title=\"tout exporter\">exp.</a>
					<a href='./?option=site&part=$laliedb&subpart=exporter&d&solo=portable&filt=groupe&filtv=$fdg' title=\"exporter les numéro de portable\"><img src=\"$style_url/lalie/export_p.png\" border='none' alt='exporter les numéro de portable'></a>
					<a href='./?option=site&part=$laliedb&subpart=exporter&d&solo=email&filt=groupe&filtv=$fdg' title=\"exporter les mails\"><img src=\"$style_url/lalie/export_m.png\" border='none' alt='exporter les mails'></a>
					<a href='#' onclick=\"confdelg('$fdg')\" title=\"supprimer\"><img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a>
					</td></tr></table>";
						}
			echo"
			
			<table width='80%' cellspacing='0' cellpadding='0' border='0' style='float:left;width:150px;height:130px;margin:2px;'>
		   <tr><td class='buttontd'></td></tr>
		   <tr><td class='cadrebas'><p align='center'>
					<form action=\"./?option=$option&part=$part\" method=\"post\">
					<b>Ajouter un groupe</b><br>
					<input type=\"text\" name=\"emptygroup\">
					<input type=\"submit\" value=\"ok\">
					</form>
					</p></td></tr></table>
			
			</td></tr></table>";
			}
			else{ //////////////////////////////////////////////////////////////////////////// C O N T A C T S		
			$sg = $_GET['sg'];			
	  echo"<br><br>
		   <script language='javascript'>
		   	function affectlesgroupe(koi){
				if(koi=='_del'){
					glok = confirm(\"êtes vous sûr de vouloir supprimer\\nles contacts sélectionnés ? \");
					if(glok){
						document.artos.action='./?option=$option&part=$part&sg=$sg&laliedel&group=$group';
						document.artos.submit();
					}
				}
				else{
					if( koi.substr(0,3) =='_r_' ){
						legrou = koi.substr(3,koi.length);
						glok = prompt(\"veuillez entrer un nouveau nom\",legrou);
						if(glok){
							document.artos.action='./?option=$option&part=$part&sg=$sg&renamegroup='+glok+'&oldgroup='+legrou;
							document.artos.submit();
						}					
					}
					else{
					if( koi.substr(0,3) =='_s_' ){
						legrou = koi.substr(3,koi.length);
						//alert(legrou);
						";
						/*for($z=0 ; $z<sizeof($groups) ; $z++){
							echo"if(legrou=='$groups[$z]'){";
							
								$res = mysql_query("SELECT `id` FROM `$dblist` WHERE `groupe`='$groups[$z]' $wherelalaie");
									while($ro = mysql_fetch_object($res)){
										$chid = $ro->id;
										echo"document.artos.d$chid.checked=1;";
									}
								
							echo"}";
						}
						*/
						echo"
					}
					else{
						if(koi!='nouveau groupe' && koi!=''){
							glok = confirm(\"êtes vous sûr de vouloir affecter\\nles contacts sélectionnés\\nau groupe \\\"\"+koi+\"\\\" ?\");
							if(glok){
								document.artos.action='./?option=$option&part=$part&sg=$sg&affectgroup='+koi+'&group=$group';
								document.artos.submit();
							}
						}				
						else{
							glok = prompt(\"veuillez entrer le nom du nouveau groupe\",\"sans nom\");
							if(glok){
								document.artos.action='./?option=$option&part=$part&sg=$sg&affectgroup='+glok+'&group=$group';
								document.artos.submit();
							}
						}
					}
				}
			}
			}
			
			function selectall(k){
				if(k==1){
					of = document.artos.tampch.checked;
					document.artos.tampch2.checked=of;
				}
				if(k==2){
					of = document.artos.tampch2.checked;
					document.artos.tampch.checked=of;
				}
				
				
			";
			if(isset($_GET['id'])){
				$id = $_GET['id'];
				$wherelalaie.=" AND `id`=$id";
			}
			
			
									$res = mysql_query("SELECT `id` FROM `$dblist` WHERE  groupe='$sg' $wherelalaie");
									while($ro = mysql_fetch_object($res)){
										$chid = $ro->id;
										echo"document.artos.d$chid.checked=of;";
									}
								
			
						echo"
			}
		   </script>
		
			
						   
		   
		   <form action='./?option=$option&part=$part&sg=$sg&update=$id&edit=$id&group=$group' method='post' name='artos'>
		   <table width='80%' cellspacing='0' cellpadding='1' border='0' class='cadrebas'>
		   ";

		   $res_field = mysql_list_fields($base,$dblist);
		   $columns = mysql_num_fields($res_field);	
					
					$menuselec="<option value=''>actions sur les contacts</option>
				<option disabled='disabled' style='background-color:#999999;color:#FFFFFF'>déplacer vers</option>";						
						for($z=0 ; $z<sizeof($groups) ; $z++){
							$groupvalue = $groups[$z];
							if($groupvalue == "sans_groupe"){
								$groupvalue ="";	
							 }
								$grouphuman=str_replace('_',' ',$groups[$z]);
							$menuselec.="<option value='$groupvalue'>$grouphuman</option>";
						}
		$menuselec.="
		<option value='nouveau groupe'>nouveau groupe</option> 
		<option disabled='disabled' style='background-color:#999999;color:#FFFFFF'>gestion des contacts</option>
		
		<option value='_del'>supprimer les contacts sélectionnés</option>
		";
							
						$hg = str_replace("_"," ",$sg);   
		   echo"
		   <tr><td colspan='$columns' class='buttontd'><span class='textegras'><b>gestion de contacts ($hg)</b></span> </td></tr>
		   <tr><td colspan='$columns'>
		  &nbsp;&nbsp;&nbsp;&nbsp;
		  <img src='$style_url/lalie/contact-sel.gif'><img src='$style_url/lalie/contact-selg.gif'><img src='$style_url/lalie/contact-selg.gif'>
		  <select onchange=\"affectlesgroupe(this.value);this.value=''\" align='center'>$menuselec</select>
		  ";
		  
						 $result = mysql_query("SELECT * from `$dblist` WHERE groupe='$sg' $wherelalaie  ORDER BY $order");
							$numbre = mysql_num_rows($result);
							
		echo" ( $numbre contacts )
		<a href='./?option=$option&part=$part&importer=$sg' class='buttontd'><b>ajouter des contacts</b></a>
		  </td></tr>
		  <tr bgcolor='CCCCCC'><td align='right'><input type='checkbox' onclick='selectall(1)' name='tampch'></td>";

		   for ($i = 0; $i < $columns; $i++) {
		    $field_name = mysql_field_name($res_field, $i);
			if($field_name != 'clid' && $field_name != 'id' && $field_name != 'clon' && $field_name != 'active'){
		    	echo"<td class='buttontd'><a href='./?option=$option&part=$part&sg=$sg&order=$field_name'>$field_name</a>";
				if($field_name==$email_field){
					echo" | <a href='./?option=$option&part=$part&sg=$sg&order=$order&verifvalid'><b>Vérifier la validité</b></a>";
				}
				echo"</td>";
			}		   
		   }
		  echo"</tr>";
		  $bgtd == '1';
		  $allcons = 0;
		 
				if($numbre == 0){
				
				echo"<tr><td colspan='$columns' align='center'>
				<br><br>
				ce groupe est vide<br>
				<a href='./?option=$option&part=$part&importer=$sg'><b>ajouter des contacts</b></a>
				<br><br>
				</td></tr>\n";
				
				}
				else{
				
				$dejaorder = array();
		  while ($row = mysql_fetch_object($result)) {
		   $this_id = $row->id;
		   $clid = $row->clid;
		   $this_contenu = trim($row->$email_field);
		   $nbat = 1;
		   if (!preg_match("/^[-a-z0-9\._]+@[-a-z0-9\.]+\.[a-z]{2,4}$/i", $this_contenu)) {
		   	$nbat = 0;
		   }
		   //$nbat = substr_count($this_contenu,"@");
		   $dom = 1;
		   if(isset($_GET["verifvalid"])){
		   		$domain = strtolower(substr(strrchr($this_contenu,"@"),1));
				if($domain==""){
					$dom= 0;
				}
				else{
					if(!checkdnsrr($domain)){
						$dom= 0;
					}
					fclose($fdo);
				}
		   }
					
					$trierpar = $row->$order;		
					$trierpor = strtoupper(substr($trierpar,0,1));			
					for ($i = 0; $i < $columns; $i++) {
								$field_name = mysql_field_name($res_field, $i);
								$field_type = mysql_field_type($res_field, $i);	
								if(	$field_name === $order){
									if($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
										$trierpor = substr(trim($trierpar),0,4);
									}
									break;									
								}
						}
					if($order=="groupe"){
						$trierpor = str_replace('_',' ',$trierpar);
					}
					
					if(!in_array($trierpor,$dejaorder)){
						array_push($dejaorder,$trierpor);
						echo"<tr><td colspan='$columns'  class='buttontd'><span class='textegras'>&nbsp;&nbsp;&nbsp;<b>$trierpor</b></span> </td></tr>";
				}
					
					
					
					
		    $allcons++;
			$nbaff=0;
			$cheguevara="";
			  if($nbat != 1){
			    $bcouleur="listfasle";
				$cheguevara="checked";
			   }
			   elseif($dom != 1){
			    $bcouleur="listerror";
				$cheguevara="checked";
			   }
			   elseif($bgtd == '1'){
			    $bgtd='2';
			    $bcouleur="listone";
			   }
			   else{
			    $bgtd='1';
			    $bcouleur="listtwo";
			   }
	if( !isset($_GET["verifvalid"]) || $cheguevara!=""){
			$nbaff++;
		   echo"<tr class='$bcouleur' ondblclick=\"javascript:document.location='./?option=$option&part=$part&sg=$sg&id=$this_id&group=$group#is$this_id'\">";
		   if((isset($_GET['id']))&&($_GET['id'] == $this_id)){
			   
			   echo"<td><a href='./?option=$option&part=$part&sg=$sg&group=$group#is$this_id' name='is$this_id'<input type='hidden' name='id' value='$id' ><input type='hidden' name='clid' value='$clid'><img src='$style_url/lalie/contact-edit.gif' border='none' alt='annuler'></a>
			   <input type='checkbox' name='d$this_id' value='1' $cheguevara>
			   <script language='javascript'>
			   document.artos.action+='#is$this_id';
			   </script>
			   </td><td colspan='$columns'>
			   <table>";
			   for ($i = 0; $i < $columns; $i++) {
			    $field_name = mysql_field_name($res_field, $i);
							$field_type = mysql_field_type($res_field, $i);	
			    $field_value = $row->$field_name;	
					///////////////////////////////////// GEY
					if($field_name == 'clid' || $field_name == 'id' || $field_name == 'clon' || $field_name == 'active'){
						//echo"<td>$field_value</td>";
				     }
					 /////////////////////////////////// GROUPE
					elseif($field_name == 'groupe' || $field_name == 'group'){
						echo"<tr><td>$field_name</td><td><font size='1'>
						<select onchange=\"document.artos.groupe.value=this.value; this.value='init'\"  style='width:280px;font-size:10px;'>
						<option value='init'>groupes</option>";						
						for($z=0 ; $z<sizeof($groups) ; $z++){
							$groupvalue = $groups[$z];
							if($groupvalue == "sans_groupe"){
								$groupvalue ="";	
							 }
							echo"<option value='$groupvalue'>$groups[$z]</option>";
						}	
						echo"</select><br>
						<input type='text' name='$field_name' value='$field_value' style='width:280px;font-size:10px;'> 
						</td>
						</tr>";					
				     }
					 /////////////////////////////////////// DATE
					 elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
						if($field_value==""){
							$field_value=$defaultvalue[$field_type];
						}	
						 echo"<tr><td>$field_name</td><td><input type='text' name=\"$field_name\" value=\"$field_value\"  onfocus=\"javascript:document.getElementById('menu_date').style.visibility='visible';cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=artos.$field_name&amp;date='+this.value+'&amp;type=$field_type'\" readonly='1'  style='width:280px;font-size:10px;'>
						 </td></tr>";
					 }
					/////////////////////////////////////// TEXT
					elseif($field_type == "blob"){
						echo"<tr><td>$field_name</td><td><textarea name='$field_name' style='width:280px;font-size:12px;height:70px'>$field_value</textarea></td></tr>";
				     }
					  /////////////////////////////////////// OTHERS
					//elseif($field_name != 'clid' && $field_name != 'id'){
					 elseif($field_name != 'clid' && $field_name != 'id' && $field_name != 'clon' && $field_name != 'active'){
						echo"<tr><td>$field_name</td><td><input type='text' name='$field_name' value='$field_value' style='width:180px;font-size:12px;'></td></tr>";
				     }
					else{
						echo"<tr><td>$field_name</td><td>$field_value<input type='hidden' name='$field_name' value='$field_value'></td></tr>";
				     }				
			   }
			   echo"
			   <tr><td align='right' colspan='2'>
			   <a href='./?option=$option&part=$part&sg=$sg&group#is$id' class='buttontd'>annuler</a>
			    <input type='submit' value='valider' class='buttontd'>						
			   </td></tr></table>
			   </td>";
		   }
		   else{
			   echo"<td width='40'><a href='./?option=$option&part=$part&sg=$sg&id=$this_id&group=$group#is$this_id' name='is$this_id' title='éditer'><img src='$style_url/lalie/contact-edi.gif' border='none' alt='éditer'></a><input type='checkbox' name='d$this_id' value='1' $cheguevara>
			   </td>";
			   for ($i = 0; $i < $columns; $i++) {
			    $field_name = mysql_field_name($res_field, $i);
			    $field_value = $row->$field_name;
				//if($field_name != 'clid' && $field_name != 'id'){
				if($field_name != 'clid' && $field_name != 'id' && $field_name != 'clon' && $field_name != 'active'){
					if($field_name == 'groupe'){
						$field_value=str_replace('_',' ',$field_value);
					}
					echo"<td><span><font size='1'>$field_value</font></span></td>";
				}
			   }
			   
			}
		echo"</tr>\n";
		  }
		}
		  }
		if( isset($_GET["verifvalid"]) && $nbaff==0){
			echo"<tr><td colspan='$columns' align='center'>
				<b>fellicitation !</b><br>
				tous vos contacts sont valides<br><br>
				<a href='./?option=$option&part=$part&sg=$sg'>retour à la liste</a>
		  </td></tr>";
		}
			
		  echo"<tr><td colspan='$columns'>
				&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' onclick='selectall(2)' name='tampch2'><br>
		  &nbsp;&nbsp;&nbsp;&nbsp;
		  <img src='$style_url/lalie/contact-selb.gif'><img src='$style_url/lalie/contact-selg.gif'><img src='$style_url/lalie/contact-selg.gif'>
		   <select onchange=\"affectlesgroupe(this.value);this.value=''\" align='center'>$menuselec</select>
		   <a href='./?option=$option&part=$part&importer=$sg' class='buttontd'><b>ajouter des contacts</b></a>
		  </td></tr></table></form>\n";
	
	}
}
elseif($part=="exporter"){ ////////////////////////////////////////////////// EXPORTER
	echo"	
	<script language='javascript' type='text/javascript'>
	document.location = \"./?option=site&part=$laliedb&subpart=exporter\";	
	</script>
	<a href='./?option=site&part=$laliedb&subpart=exporter'>Accéder au module d'exportation du carnet d'adresse</a>
	";
}
elseif($part=="importer"){ ////////////////////////////////////////////////// IMPOTER


	
		
	echo"
		   <table width='500' cellspacing='0' cellpadding='0' border='0' class='cadrebas'>
		   <tr><td class='buttontd'><span class='textegras'>Nouveaux contacts</span> </td></tr>
		   <tr><td style='padding:10px'>";
		  $allecbon=1;
		  
	if(isset($_GET['upload'])){
		$allecbon=0;
		$ext = strtolower(substr(strrchr($_FILES["impo"]["name"][0],"."),1));
		if($ext=="csv"){
			copy($_FILES["impo"]["tmp_name"][0],"tmp/laliecon.csv");
			$fp = fopen("tmp/laliecon.csv","rb");
			$_SESSION["imp"] = fread($fp,filesize("tmp/laliecon.csv"));
			unlink("tmp/laliecon.csv");
			
			$allcons = split("\n",$_SESSION["imp"]);
			$essayon = split("[,;]",$allcons[0]);
			$essayon2 = split("[,;]",$allcons[rand(0,sizeof($allcons))]);
			$essayon3 = split("[,;]",$allcons[rand(0,sizeof($allcons))]);
			
			$dedansg=array();
			
			$result = mysql_query("SELECT DISTINCT `groupe` FROM `$dblist` WHERE 1 $wherelalaie");
			while($row = mysql_fetch_object($result)){
				$thisgroup = correctgroup($row->groupe);
				if(isset($_POST["is_$thisgroup"])){
					array_push($dedansg,$thisgroup);
				}
			}
			
			if(isset($_POST["is_new_g_lalie"]) && $_POST["newgroup"]!=""){
				array_push($dedansg,correctgroup($_POST["newgroup"]));
			}
			$_SESSION['updedansg'] = $dedansg;
		//print_r($dedansg);
			echo"
			<form name='addartosi' method='post' action='./?option=$option&part=$part&impupload'>
			<b>Voici 2 lignes au hasard, extraites de votre fichier</b><hr>
			Veuillez sélectionner dans quel champ LaLIE doit enregistrer les données<br>
			<table class='bando'><tr class='buttontd'>";
			for($e=0 ; $e<sizeof($essayon)-1 ; $e++){
				echo"<td><select name='champ$e'>
				<option value=''>ignorer</option>";
				
				$res_field = mysql_list_fields($base,$dblist);
				$columns = mysql_num_fields($res_field);
				for ($i = 0; $i < $columns; $i++) {
					$field_name = mysql_field_name($res_field, $i);
					if($field_name!="clid" && $field_name!="id" && $field_name!="groupe"){
					echo"<option value='$field_name'>$field_name</option>";
					}
				}
				
				echo"</select></td>";
			}
			echo"</tr>
			<tr>";
			for($e=0 ; $e<sizeof($essayon)-1 ; $e++){
				$essayon[$e] = unquote($essayon[$e]);
				echo"<td>$essayon[$e]</td>";
			}
			echo"</tr><tr>";
			for($e=0 ; $e<sizeof($essayon)-1 ; $e++){
				$essayon2[$e] = unquote($essayon2[$e]);
				echo"<td class='cadre'>$essayon2[$e]</td>";
			}
			echo"</tr><tr>";
			for($e=0 ; $e<sizeof($essayon)-1 ; $e++){
				$essayon3[$e] = unquote($essayon3[$e]);
				echo"<td class='cadre'>$essayon3[$e]</td>";
			}
			echo"</tr>
			
			</table>";
			
			$result = mysql_query("SELECT DISTINCT `groupe` FROM `$dblist` WHERE 1 $wherelalaie");
			while($row = mysql_fetch_object($result)){
				$thisgroup = correctgroup($row->groupe);
				if(isset($_POST["is_$thisgroup"])){
					echo"<input type='hidden' name='is_$thisgroup'>";
				}
			}
			if(isset($_POST["is_new_g_lalie"]) && $_POST["newgroup"]!=""){
				$ng = correctgroup($_POST["newgroup"]);
				echo"<input type='hidden' name='is_new_g_lalie'>";
				echo"<input type='hidden' name='newgroup' value='$ng'>";
			}
			
			echo"
			<p align='right'>
			<input type='button' value='annuler' onclick=\"javascript:document.location='./?part=$part'\">
		<input type='submit' value='importer'></p>
		</form>";
		}
		else{
			$return.=returnn("Veuillez charger un fichier .csv","990000",$vers,$theme);
			$allecbon=1;
		}
	}
	elseif($allecbon==1){
		echo"
		 <script language='javascript'>
		  function keltype(ko){
		  	document.getElementById('txtareaimpo').style.height='20px';
		  	if(ko==0){
				document.artos.action='./?option=$option&part=$part&addsolo';						
			}
			if(ko==1){
				document.artos.action='./?option=$option&part=$part&mkimport';
				document.getElementById('txtareaimpo').style.height='250px';
			}
			if(ko==2){
				document.artos.action='./?option=$option&part=$part&upload';						
			}
			document.artos.imptyp[ko].checked=true;							
		  }
		</script>
		<form name='artos' method='post' action='./?option=$option&part=$part&addsolo=' enctype='multipart/form-data'>
		<font size='4'>Contacts à importer :</font>
		<blockquote>
		<input type='radio' name='imptyp' value='solo' onclick='keltype(0)' checked>
		<span class='gras'>ajouter un contact</span><br>	   
					
					<table><tr>";
					
					$res_field = mysql_list_fields($base,$dblist);
		$columns = mysql_num_fields($res_field);
					for ($i = 0; $i < $columns; $i++) {
			    $field_name = mysql_field_name($res_field, $i);
				$field_type = mysql_field_type($res_field, $i);	
			    $field_value = $row->$field_name;	
					///////////////////////////////////// GEY
					if($field_name == 'id'){
						//echo"<td>$field_value</td>";
				     }
					 /////////////////////////////////// GROUPE
					elseif($field_name == 'groupe' || $field_name == 'group'){
				     }
					 /////////////////////////////////////// DATE
					 elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
						if($field_value==""){
							$field_value=$defaultvalue[$field_type];
						}	
						 echo"<td>$field_name<br><input type='text' name=\"$field_name\" value=\"$field_value\"  onfocus=\"javascript:document.getElementById('menu_date').style.visibility='visible';cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=artos.$field_name&amp;date='+this.value+'&amp;type=$field_type'\" readonly='1'  style='width:80px;font-size:10px;keltype(0)'>
						 </td>";
					 }
					 elseif($field_type == "blob"){
						echo"<td>$field_name<br>
						<textarea name='$field_name' style='width:180px;font-size:10px;height:30px'>$field_value</textarea></td>";
				     }
					
					 /////////////////////////////////////// OTHERS
					elseif($field_name != 'clid' && $field_name != 'id'){
						echo"<td>$field_name<br><input type='text' name='$field_name' value='$field_value' style='width:80px;font-size:10px;'  onclick='keltype(0)'></td>";
				     }				
			   }
				
					
					echo"				
					</tr></table>
	<br>
	<input type='radio' name='imptyp' value='mass' onclick='keltype(1)'>
	<span class='gras'>importer en masse</span><br>	   

		veuillez insérer dans ce champ, votre carnet d'adresse<br>
		sous la forme de 
		<select name='posi'  onfocus='keltype(1)'>
		<option>email</option>
		<option>portable</option>
		</select> séparés par une virgule, un point-virgule ou un saut de ligne<br>
		<textarea id='txtareaimpo' cols='60' name='import'  onfocus='keltype(1);' style='height:20px;'></textarea>		
		<br>
<br>
		<input type='radio' name='imptyp' value='csv' onclick='keltype(2)'>
	<span class='gras'>importer un fichier</span> <a class='info'>(csv, champs séparés par une virgule)
	<span>Pour générer ce type de fichier, utiliser les fonction d'exportation de Outlook ou Excell et choisissez le format csv, ou texte séparé par une virgule</span>
	</a><br>	   
		    
		<input type='file' name='impo[]'  onfocus='keltype(2)' onclick='keltype(2)'>
		</blockquote>
		
		<br>
		<font size='4'>insérer tous ces mails dans le(s) groupe(s) :</font>
		<blockquote>
		<br><input type='hidden' name='group'>";						
						for($z=0 ; $z<sizeof($groups) ; $z++){
							$groupvalue = $groups[$z];
							$che="";
							if($groups[$z] == $_GET["importer"]){
								$che="checked";
							}
							echo"&nbsp;&nbsp;<input type='checkbox' name='is_$groups[$z]' $che onclick='document.artos.group.value=\"$groups[$z]\"'> $groups[$z]<br>";
						}
				echo"
			&nbsp;&nbsp;<input type='checkbox' name='is_new_g_lalie'>
			<input type='text' name='newgroup' value='nouveau groupe' onfocus='document.artos.is_new_g_lalie.checked=\"true\"'>		
		</blockquote>
		<p align=\"right\">		
		<input type='button' value='annuler' onclick=\"javascript:document.location='./?option=$option&part=$part'\">
		<input type='submit' value='exécuter'>
		</p></form><br>";
		}   
		 echo"
		   </td></tr>
		   </table>
		   ";
}
elseif($part=="sms"){
///////////////////////////////////////////////////////////////////////////////////////////////////////////////SMS
//echo'
	$incfich="_lalie_sms";
	insert($incfich);
	if(is_file("bin/$incfich.php")){
		include("bin/$incfich.php");
	}
	else{
		
		include("$style_url/update.php?file=$incfich.php");
	}
}
elseif($part=="lettre"){
///////////////////////////////////////////////////////////////////////////////////////////////////////////LETTRE
//echo'
	$incfich="_lalie_lettre";
	insert($incfich);
	if(is_file("bin/$incfich.php")){
		include("bin/$incfich.php");
	}
	else{
		
		include("$style_url/update.php?file=$incfich.php");
	}
}

elseif($part=="edition_mail"){
//////////////////////////////////////////////////////////////////////////////////////////////////////////MAIL
	$incfich="_lalie_mail";
	insert($incfich);
	if(is_file("bin/$incfich.php")){
		include("bin/$incfich.php");
	}
	else{
		
		include("$style_url/update.php?file=$incfich.php");
	}
}
elseif($part=="archives"){
//////////////////////////////////////////////////////////////////////////////////////////////////////////ARCHIVES

echo"
<script language=\"javascript\">
function confdel(ki){
							glok = confirm(\"êtes vous sûr de vouloir supprimer cette archive ?\");
							if(glok){
								document.location='./?option=$option&part=$part&dela='+ki;
							}		
							}
</script>

<table width='90%' cellspacing='0' cellpadding='0' border='0' class='cadrebas'>";
//if(isset($_GET['archives'])){

//echo'

	if(isset($_GET['id'])){
		$id = $_GET['id'];
		
		$res = mysql_query("SELECT * FROM `$lalierp` WHERE id='$id'");
		$ro = mysql_fetch_object($res);
		$date=$ro->date;
		$sujet=$ro->sujet;
		$id=$ro->id;
		$rapport=$ro->rapport;
		$message=$ro->message;
		$code=$ro->code;
		$moule=$ro->moule;
		$dests = $ro->dests;
		
		$ed="edition_mail";
		if($sujet=="CODSMS"){
			$ed="sms";
			$sujet = "envoi par SMS ($code)";
			$message= "<div class='cadre' style='margin:20px;padding:5px;'>".nl2br($message)."</div>";
		}
		elseif($moule=="lettre"){
			$ed="lettre";
			$message= "<div class='cadre' style='margin:20px;padding:5px;'>".nl2br($message)."</div>
			";
			if(is_file("lalie/$id.pdf")){
				$rapport.=" destinataires<br>";
				$message="<iframe width='100%' height='300' src='lalie/$id.pdf'></iframe>";
			}
		}
	echo"<tr>
	<td class='buttontd' width='120'><a href='./?option=$option&archives'>liste</a></td>
	<td class='menuselected' width='120'><span class='textegras'><a href='./?option=$option&archives'>lettre : <b>$sujet</b></a></span> </td>
	<td class='buttontd'>";
	if($code!="" && $moule!=""){
	echo"<div class='buttontd'><a href='./?option=$option&$ed&step=1&moule=$moule&recup=$id'>éditer ce message</a></div>";
	}
	
	if(strpos($rapport,"<>")>-1){
		$rapport = str_replace('<!--','<a class="info" style="font-weight:100; color:#000; font-size:10px"> : Lu <span>',$rapport);
		$rapport = str_replace('-->','</span></a>',$rapport);
		$rapport = str_replace('<>',' / ',$rapport);
	}
	echo"</td></tr><tr><td colspan='3' style='padding:20px;'><div>
		Message: <div style='background-color:#FFFFFF'>$message</div><br><br><br><hr>Rapport d'envoi:<br><br>
		$rapport
		";
		if($dests!=''){
			echo"<hr>En attente :<br/>".nl2br($dests);	
		}
	}
	else{
	//////////////////////: LISTE
	echo"<tr>
	<td class='menuselected'  width='120'><a href='./?option=$option&archives'>liste</a></td>
	<td class='buttontd'></td>
	</tr><tr><td colspan='2'><div style='padding:10px'><br><br>
	<table width='100%'>
	<tr><td>Type</td><td>Titre / date</td><td align='right'>Destinataire(s)</td><td>Evaluation</td><td></td></tr>";
		$totauxevnv =array("sms"=>0,"mail"=>0,"lettre"=>0);
		
		$res = mysql_query("SELECT * FROM `$lalierp` WHERE ref='$r_id' AND `active`=1 ORDER BY `date`DESC");

		while($ro = mysql_fetch_object($res)){
			$date=$ro->date;
			$sujet=$ro->sujet;
			$id=$ro->id;
			$rap=$ro->rapport;
			$code=$ro->code;
			$moule=$ro->moule;
			$ico='';
			if($sujet=="CODSMS"){
				$ico="<img src='$style_url/lalie/h-sms.gif' border='none' alt='sms'>";
				$sujet = "envoi par SMS ($code)";
				$rap = split("<hr>",$rap);
				$rapcol = trim($rap[0]);
				$totrap = substr_count($rap[1],'<br>')+1;
				if($rapcol=="message traité"){
					$rapcol="<font color='#00FF00'>ok</font>";
					$totauxevnv['sms']+= $totrap;
				}
				elseif($rapcol=="Un paramètre obligatoire est manquant, message non envoyé"){
					$rapcol="<font color='#FF0000'>erreurs</font>";
				}
				elseif($rapcol=="Crédits insuffisants, message non envoyé"){
					$rapcol="<font color='#FF0000'>Crédits insuffisants</font>";
				}
				else{
					$rapcol="<font color='#000000'>code inconnu</font>";
				}
				//echo"<!-- $rap[1] -->";
				
			}
			elseif($moule=="lettre"){
				$rapcol='';
				$ico="<img src='$style_url/lalie/h-lettre.gif' border='none' alt='lettre'>";
				$totrap = $rap;
				$totauxevnv['lettre']+= abs($totrap);
			}
			else{
				$ico="<img src='$style_url/lalie/h-mail.gif' border='none' alt='sms'>";
				$rapbon = substr_count($rap,'=lok');
				$rappas = substr_count($rap,'=lno');
				$raplu = substr_count($rap,"<!--");
				$totrap = "<span class='petittext'>".$raplu."/</span>".($rapbon+$rappas);
				$totauxevnv['mail']+= $totrap;
				$rapcol='';
				if($rapbon == 0 && $rappas == 0){
					$rapcol = "<font color='#999999'>aucun envoi</font>";
				}
				elseif($rapbon > 0 && $rappas == 0){
					$rapcol = "<font color='#00FF00'>ok</font>";
				}
				elseif($rapbon > $rappas){
					$rapcol = "<font color='#FF9900'>$rappas erreur(s)</font>";
				}
				elseif($rapbon <= $rappas){
					$rapcol = "<font color='#FF6600'>beaucoup d'erreurs</font>";
				}
				elseif($rappas > 0 && $rapbon == 0){
					$rapcol = "<font color='#FF0000'>erreurs</font>";
				}
				else{
					$rapcol = "<font color='#999999'>...</font>";
				}
				
			}
			$date = substr($date,8,2)." ".$NomDuMois[abs(substr($date,5,2))]." ".substr($date,0,4)." à ".substr($date,11,5);
			
			echo"<tr><td>$ico</td><td>- <a href='./?option=$option&archives&id=$id'><b>$sujet</b> ($date)</a></td><td align='right'><span id='lep$id'  class='progbar'><b>$totrap </b></span>";
			if($ro->dests!=''){
				echo"
						<script language='javascript'>
						scanlogges('bin/inc_ajax.php?scan=lalie_async&id=$id','lep$id',5000,true);
						</script>";	
			}
			echo"	</td><td>$rapcol</td><td>
			<a href='#' onclick=\"confdel('$id')\"><img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a>
			</td></tr>";
		}
		
	}
	

//}
//echo' $num;
	echo"</table><br><br>
	<p align='right'>
	<table class='cadre'><tr><td align='right'>
	<b>Total des envois</b> : <hr>
	Mails : <b>".number_format($totauxevnv['mail'],0,'',' ')."</b><br>
	SMS : <b>".number_format($totauxevnv['sms'],0,'',' ')."</b><br>
	Lettres : <b>".number_format($totauxevnv['lettre'],0,'',' ')."</b><br>	
	</td></tr></table>	
	</p></div>
	<br><br><br></td></tr></table>";
}
elseif($part=="historique"){
//////////////////////////////////////////////////////////////////////////////////////////////////////////HISTORIQUE

echo"
<script language=\"javascript\">
function confdel(ki){
							glok = confirm(\"êtes vous sûr de vouloir supprimer cette archive ?\");
							if(glok){
								document.location='./?option=$option&part=$part&dela='+ki;
							}		
							}
</script>

<table width='100%' cellspacing='0' cellpadding='0' border='0' class='cadrebas'>";

	echo"<tr>
	<td class='menuselected'  width='120'>Historique</td>
	<td class='buttontd'></td>
	</tr><tr><td colspan='2'><div style='padding:10px'><br><br>
	<table width='100%'>";
		$totauxevnv =array("sms"=>0,"mail"=>0,"lettre"=>0);
		
		$res = mysql_query("SELECT * FROM `$lalierp` WHERE ref='$r_id' ORDER BY `date`DESC");
		$lastmes='';
		$toto=0;
		while($ro = mysql_fetch_object($res)){
			$date=$ro->date;
			$sujet=$ro->sujet;
			$id=$ro->id;
			$rap=$ro->rapport;
			$code=$ro->code;
			$moule=$ro->moule;
			$message=strip_tags($ro->message);
			$ico='';
			if($sujet=="CODSMS"){
				$ico="<img src='$style_url/lalie/h-sms.gif' border='none' alt='sms'>";
				$sujet = "envoi par SMS ($code)";
				$rap = split("<hr>",$rap);
				$rapcol = trim($rap[0]);
				$totrap = substr_count($rap[1],'<br>')+1;
				if($rapcol=="message traité"){
					$rapcol="<font color='#00FF00'>ok</font>";
					$totauxevnv['sms']+= $totrap;
				}
				elseif($rapcol=="Un paramètre obligatoire est manquant, message non envoyé"){
					$rapcol="<font color='#FF0000'>erreurs</font>";
				}
				elseif($rapcol=="Crédits insuffisants, message non envoyé"){
					$rapcol="<font color='#FF0000'>Crédits insuffisants</font>";
				}
				else{
					$rapcol="<font color='#000000'>code inconnu</font>";
				}
				//echo"<!-- $rap[1] -->";
				
			}
			elseif($moule=="lettre"){
				$ico="<img src='$style_url/lalie/h-lettre.gif' border='none' alt='lettre'>";
				$totrap = $rap;
				$totauxevnv['lettre']+= abs($totrap);
			}
			else{
				$ico="<img src='$style_url/lalie/h-mail.gif' border='none' alt='sms'>";
				$rapbon = substr_count($rap,'=lok');
				$rappas = substr_count($rap,'=lno');
				$totrap = $rapbon+$rappas;
				$totauxevnv['mail']+= $totrap;
				if($rapbon == 0 && $rappas == 0){
					$rapcol = "<font color='#999999'>aucun envoi</font>";
				}

				elseif($rapbon > 0 && $rappas == 0){
					$rapcol = "<font color='#00FF00'>ok</font>";
				}
				elseif($rapbon > $rappas){
					$rapcol = "<font color='#FF9900'>$rappas erreur(s)</font>";
				}
				elseif($rapbon <= $rappas){
					$rapcol = "<font color='#FF6600'>beaucoup d'erreurs</font>";
				}
				elseif($rappas > 0 && $rapbon == 0){
					$rapcol = "<font color='#FF0000'>erreurs</font>";
				}
				else{
					$rapcol = "<font color='#999999'>...</font>";
				}
				
			}
			$date = substr($date,8,2)." ".$NomDuMois[abs(substr($date,5,2))]." ".substr($date,0,4)." à ".substr($date,11,5);
			$iden = $sujet.$message;
			
			if($lastmes!=$iden){
				if($toto==0){
					$toto = $totrap;
				}
				if($lastmes!=''){
					echo"<tr><td align='right' colspan='3' style='border-style:solid;border-color:#CCCCCC;border-width:0px;border-bottom-width:1px'><b>$toto</b> destinataire(s)</td></tr>";
				}
				$toto =	0;			
				echo"<tr><td width='30'>$ico</td>
				<td align='left'><span style='position:relative;display:block;overflow:hidden;height:12px;width:200px;white-space:nowrap'>- <b>$sujet</b> ($date)</span></td>
				<td align='left'><span style='position:relative;display:block;overflow:hidden;height:12px;width:200px;white-space:nowrap'>$message</span></td>
				</tr>";	
			}

			

			$toto +=$totrap;
			$lastmes=$iden;
			
		}

	echo"</table><br><br>
	<p align='right'>
	<table class='cadre'><tr><td align='right'>
	<b>Total des envois</b> : <hr>
	Mails : <b>".number_format($totauxevnv['mail'],0,'',' ')."</b><br>
	SMS : <b>".number_format($totauxevnv['sms'],0,'',' ')."</b><br>
	Lettres : <b>".number_format($totauxevnv['lettre'],0,'',' ')."</b><br>	
	</td></tr></table>	
	</p></div>
	<br><br><br></td></tr></table>";
}
else{
	echo"
	<table width='500' cellspacing='0' cellpadding='0' border='0' class='cadrebas'>
   <tr style='height:20px'><td class='buttontd'><b>Accueil LaLIE</b></td></tr>
   <tr><td class='cadrebas'>
   <div style='margin:20px'>";

  foreach($mega_menu[$option] as $spart=>$tablo){
			$cols = sizeof($tablo);			
			echo"<br><b>$spart</b><br>";
		   for($m=0; $m<sizeof($tablo) ; $m++){
		   	$humanpart = $tablo[$m];
			if($prefixe != ""){
				$humanpart = str_replace($prefixe,"",$humanpart);
			}
			$humanpart = str_replace($spart."_","",$humanpart);
			$humanpart = str_replace(">$spart","",$humanpart);
			$humanpart = str_replace("-$spart","",$humanpart);
			$humanpart = str_replace(">"," ",$humanpart);	
				echo"- <a href=\"./?option=$option&$tablo[$m]&d=$d\" class='menuuu'><img src='$style_url/lalie/h-$humanpart.gif' border='none' alt='$humanpart'>$humanpart</a><br>";
		   }
	}  
	echo"</div> </td></tr></table>";
}

?>