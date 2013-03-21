<?php // 418 > Liste des éléments ;

	$res_field = mysql_list_fields($base,$tabledb);
	$columns = mysql_num_fields($res_field);
	   
	echo"<div id='incliste'>"; 
	
	$selected0 = array('selected','');
	$selected1 = array('','selected');
	
	$r_alias['adeli_messages'] = array('dest'=>'clients_id_nom','prov'=>'clients_id_nom');
	
	/*
	elseif( ($field_name == "dest" || $field_name == "prov") && $part=="adeli_messages"){
					if($field_value==0){ 
						$field_value="moi"; 
					}
					else{ 
						$ros = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$field_value'");
						$rows = mysql_fetch_object($ros);
						$field_value=$rows->nom; 
					}
				}
	*/
if($al==="d"){
			
			$d = date("Ymd");
			if(isset($_GET['d']) && strlen($_GET['d']) == 8 && is_numeric($_GET['d']) ){
				$d = $_GET['d'];
			}
			
			$lannee = substr($d,0,4);
			$lemois = substr($d,4,2);
			$lejour = substr($d,6,2);
			
			$sqldate = $lannee."-".$lemois."-".$lejour;
			$JourMax =date("t",mktime(0,0,0,$lemois,1,$lannee));
			$Joursemin =date("w",mktime(0,0,0,$lemois,1,$lannee));
			$julienlast = gregoriantojd ( $lemois,$lejour,$lannee );
			$lemoislettre = $NomDuMois[abs($lemois)];
			$lejourlettre = $NomDuJour[jddayofweek($julienlast,0)];
			$selectedday = mktime(0,0,0,$lemois,$lejour,$lannee);
			$lenumdesemaine = date("W", $selectedday);


				$pMois = $lemois-1;
				$pAnnee = $lannee;
				if($pMois < 1){
					$pMois = 12;
					$pAnnee--;
				}
			$pJourMax =date("t",mktime(0,0,0,$pMois,1,$pAnnee));

			$interval = "months";
			$printcurrent="$lemoislettre $lannee";
			$nextjour = date("d",strtotime("+1 $interval",$selectedday));
			$nextmois = date("m",strtotime("+1 $interval",$selectedday));
			$nextan = date("Y",strtotime("+1 $interval",$selectedday));
			
			$prevjour = date("d",strtotime("-1 $interval",$selectedday));
			$prevmois = date("m",strtotime("-1 $interval",$selectedday));
			$prevan = date("Y",strtotime("-1 $interval",$selectedday));

			if(strlen ($nextjour)==1){ $nextjour="0$nextjour";}
			if(strlen ($nextmois)==1){ $nextmois="0$nextmois";}
			if(strlen ($prevjour)==1){ $prevjour="0$prevjour";}
			if(strlen ($prevmois)==1){ $prevmois="0$prevmois";}
			$nextdate = "$nextan$nextmois$nextjour";
			$prevdate="$prevan$prevmois$prevjour";


echo"<table cellspacing='0' cellpadding='3' border='0' width='95%'>
	<tr>
		<td valign='top' width='20' class='buttontd'  style='text-align:right'><a href='./?option=$option&part=$part&d=$prevdate'><img src='$style_url/$theme/fl_g.png' alt='<<' border='none'></a></td>
		<td valign='top' class='menuselected' width='120'><span class='gras'>$printcurrent</span></td>
		<td class='buttontd' style='text-align:left'><a href='./?option=$option&part=$part&d=$nextdate'><img src='$style_url/$theme/fl_r.png' alt='>>' border='none'></a>
		</td></tr>
	</table>";
		
				echo"<table width='95%' class='bando' cellpadding='2' cellspacing='1' border='0'>
				<tr><td><font size='1'>semaine</font></td>";
				for($e=1 ; $e<=7 ; $e++){
					echo"<td style='padding:5px' width='20%'><span class='textegrasfonce'>$NomDuJoursemaine[$e]</span></td>";
				}
				echo"</tr>";
			
			$nbj = 0;
			$NoJour = -date("w",mktime(0,0,0,$lemois,1,$lannee));     
			$NoJour +=2 ;          
			if ($NoJour >0) { 
				$NoJour -=7;
			}           
			
			for ($semaine=0;$semaine <=5;$semaine++) {   
				$semo = date("W",mktime (0,0,0,$lemois,$NoJour,$lannee) );
				echo"<tr><td align='justify' width='40' class='fondmediumlignt' valign='top'><span class='gras'>$semo</span></td>";
				for($e=1 ; $e<=7 ; $e++){
					$decal = $NoJour-1;
					if($decal >= 0){
						$decal = "+ $decal";
					}
					$CeNoJour = date("j",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
					
					$cenojour = date("d",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
					$cenomois = date("m",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
					$cenoan = date("Y",strtotime("$decal days",mktime(0,0,0,$lemois,1,$lannee)));
					
					$sqldate = "$cenoan-$cenomois-$cenojour";
					
					if ($NoJour >0 && $NoJour <= $JourMax ){
						if( mktime(0,0,0,$lemois,$NoJour,$lannee) == $selectedday){
							echo"<td class='cadre' style='padding:5px' valign='top'>";
						}	
						else{
							echo"<td valign='top'  class='cadrebas' style='border-width:0px'>";
						}
						echo"<a href='./?option=$option&part=$part&edit=&insert_date_into=$sqldate' class='buttontd' title='Ajouter'>$CeNoJour <img src='$style_url/$theme/+.png' alt='+' border='none'></a>";
					}
					else{
						echo"<td class='fondmedium' valign='top'>";
					}
					echo"<div id='age_$sqldate'></div>
					</td>";
					$NoJour++;
				}
			}	
				echo"</table>";
			
			 $wheredb = "WHERE `clon`='0'"; 
			 if($incwhere !== null){
  	$wheredb = "$incwhere AND `clon`='0'";
  }
  if(isset($wheredbplus)){
 	$wheredb.=" $wheredbplus";
 }
 $wheredb.=" AND (";
				  
				  $wdbin = '';
			for ($i = 0; $i < $columns; $i++) {
				$field_name = mysql_field_name($res_field, $i);
				$field_type = mysql_field_type($res_field, $i);
				if($field_type=="date" || $field_type=="datetime"){
					 $wdbin.=" (`$field_name`>='$lannee-$lemois-01' AND `$field_name`<='$lannee-$lemois-31') OR";
				}					
			}
			$wdbin  = substr($wdbin,0,strlen($wdbin)-3);
	 $wheredb.=" $wdbin )";		
			$jsagenda='';
			$limitation='';
}
else{
 $wheredb="WHERE `clon`='0'";
 
 
  if($incwhere !== null){
  	$wheredb = "$incwhere AND `clon`='0'";
  }
  if(isset($wheredbplus)){
 	$wheredb.=" $wheredbplus";
 }
 if(isset($_GET['d']) && $d!=''){
 	$q = stripslashes($_GET['d']);
	for ($i=0 ; $i < $columns; $i++) {
		$field_type = mysql_field_type($res_field, $i);
		if($field_type=='string' || $field_type=='blob'){
			$field_name = mysql_field_name($res_field, $i);
			$command.="`$field_name`REGEXP'".urluntranslate($q)."' OR";
		}  		
    }
	$command = substr($command,0,strlen($command)-3);
	$wheredb.=" AND ($command)";
	echo"recherche pour <b>\"$q\"</b> | ";
 }
 if(isset($u_restreint) && $u_restreint[1]==$part){
	$wheredb.=" AND `$u_restreint[2]`='$u_d'";
 }
 if(isset($u_restreint) && $u_restreint[1]!=$part){
 	for ($i = 0; $i < $columns; $i++) {
		$field_name = mysql_field_name($res_field, $i);
		$field_act = $field_name;
		if(isset($r_alias[$part][$field_name])){
			$field_act = $r_alias[$part][$field_name];
		}
		if($field_act == "$u_restreint[1]_$u_restreint[2]_$u_restreint[3]"){
			$wheredb.=" AND `$field_name`='$u_d'";
		}
	}	
 }

			$np=0;
			$result = mysql_query("SELECT `id` FROM `$tabledb` $wheredb");
			if($result && 0 !== $totro = mysql_num_rows($result)){	
				 if(isset($_GET['d']) && $_GET['d']!=''){
					//$pp = $totro+1;			
				 }
				 if(ereg('AND `active`=1',$wheredb)){
					$result = mysql_query("SELECT `id` FROM `$tabledb` ".str_replace('AND `active`=1','',$wheredb)."");
					$totra = mysql_num_rows($result);	
				 }
				if(isset($comportement)  && $totro == 1 &&  in_array("nonew",$comportement)){ //&& ($totra==1 || $q!='' || in_array('nonew',$comportement))
					$ro = mysql_fetch_object($result);
					$godi = $ro->id;
					echo"
					<script language='javascript' type='text/javascript'>
					document.location='./?option=$option&part=$part&d=$d&edit=$godi';
					</script>$totro / $totra
					";
				}
					
				$np = round($totro/$pp);
				if($np*$pp < $totro){
					$np++;
				}
				if($_SESSION['pa']>$np){
					$_SESSION['pa']=1;
					$pa = $_SESSION['pa'];
				}
				$tnp=1;
			}
			$param = str_replace('&pa=','&npa=',$_SERVER['QUERY_STRING']);
			if($np>1){
				echo"
				Aller à la page : <select style='font-size:10px' onchange=\"affichload;document.location='./?option=$option&$param&pa='+this.value\">";
				while($tnp<=$np){
					if($tnp == $pa){
						echo"<option value='$tnp' selected>$tnp/$np</option> ";
					}
					else{
						echo"<option value='$tnp'>$tnp/$np</option> ";
					}
					$tnp++;
				}	
				echo"</select> (<b>$totro enregistrements</b>) &nbsp;";
				if(isset($comportement)  && $totro != $totra && $totra!=''){ 
					echo"(<b>$totra au total</b>) &nbsp;";
				}
				
				$pl = ($pa-1)*$pp;
				if($pl<0){
					$pl=0;
				}
		
				$limitation="LIMIT $pl,$pp";
			
			}
			
			
			
}		
		$allcols=array();	
		
if($al==="l" || $al==="t" || !isset($al)){
	if(get_pref("columns.$part.conf")=='' ){
		  $cols='';
		  for ($i = 0; $i < $columns; $i++) {
			  $cols.=",$i";
		  }
		  set_pref("columns.$part.conf",$cols);
	  }
	  if(isset($_GET['columns'])){
		  $cols='';
		  for ($i = 0; $i < $columns; $i++) {
			  if(isset($_POST["column_$i"])){
				  $cols.=",$i";	
			  }
		  }
		  set_pref("columns.$part.conf",$cols);
	  }
	  else{
		  $cols = get_pref("columns.$part.conf");			
	  }
	  
	  $cols = explode(',',$cols);
		$opcol="";	
			
			echo"

	<div id='liste_larg' style='position:relative;'>
	<table width='100%' cellspacing='0' cellpadding='1' border='0'><tr class='buttontd'>";
   $nowi = 1;
   $kiwi=1;
   //for ($i = 0; $i < $columns; $i++) 
   $param = str_replace('&order=','&norder=',$_SERVER['QUERY_STRING']);
   $paramc = str_replace('&columns=','&ncolumns=',$_SERVER['QUERY_STRING']);	
   for ($i=0 ; $i<$columns ; $i++) {
	   
	   
		$field_name = mysql_field_name($res_field, $i);
		$field_type = mysql_field_type($res_field, $i);
		$field_name_human=ereg_replace("nochange_","",$field_name);
		array_push($allcols,"`$field_name`");
		array_push($allcols,"`$field_name`DESC");
		if( $field_name != "clon"  && ( (isset($multiple_depend[$part]) && $field_name!=$multiple_depend[$part][4]) || !isset($multiple_depend[$part])) ){
			$cu = "off";
			$du = "off";
			$wid="";
			$tds='';
			$sty="position:relative;overflow:hidden";
			if($order == "`$field_name`" || $order == "`$field_name`DESC"){
				$cu = "on";
				if($order == "`$field_name`DESC"){
					$cu = "off";
					$du = "on";
				}
				//$tds="class='menuselected'";
				$sty="";
				$nowi=$i;
				$kiwi=$i;
			}
			if(substr($field_name_human,0,1) == "_"){
				$field_name_human = substr($field_name_human,1,strlen($field_name_human));
			}
			//elseif(ereg("_",$field_name_human))
			elseif( ereg("_",$field_name_human) && mysql_query("SHOW COLUMNS FROM ".substr($field_name_human,0,strpos($field_name_human,'_'))) || ereg('@',$field_name_human)  ){
						$fieldoption = substr($field_name_human,strpos($field_name_human,"_")+1,strlen($field_name_human));
 		 		$field_name_human = substr($field_name_human,0,strpos($field_name_human,"_"));	
				if(ereg(">",$fieldoption)){
						$field_name_human .= " ".substr($fieldoption,strpos($fieldoption,">")+1,strlen($fieldoption));
				}
			}
			if($prefixe!=""){
				$field_name_human = trim(ereg_replace($prefixe,"",$field_name_human));
			}
			$field_name_human = ereg_replace(">"," ",$field_name_human);
			
			if(isset($alias[$part][$field_name])){
				$field_name_human = $alias[$part][$field_name];
			}
		$opcol.="<input type='checkbox' name='column_$i' value='1' class='noche' ";
		 if(in_array($i,$cols)){	
			if($field_name=="active" || $field_name=="ordre") $wid="width='20'";
			 echo"<td align='center' $tds  nowrap='nowrap' $wid class='nomob'><font size='1'><span  id='col_0"."_$i' style='display:block;$sty;height:12px;width:100%;overflow-x:hidden'><b>$field_name_human&nbsp;</b></span><a href=\"./?option=$option&$param&order=`$field_name`\" title='$field_type'><img src='$style_url/$theme/class_up_$cu.jpg' alt='^' border='none'></a><a href=\"./?option=$option&$param&order=`$field_name`DESC\" title='$field_type'><img src='$style_url/$theme/class_down_$du.jpg' alt='^' border='none'></a></font></td>";
			 $opcol.="checked";
		 }
		 $opcol.="/> $field_name_human<br>";
		
			}
}
			
   $bgtd = '1';
 
  echo"<td width='20'>
  <div style='position:relative; text-align:right;'>
  <a onclick=\"document.getElementById('opticol').style.visibility='visible';\">Colonnes</a>
  <div class='cadre' id='opticol' style='position:absolute;top:0px; left:-120px; width:150px; text-align:left; visibility:hidden; z-index:1500'>
  <a onclick=\"document.getElementById('opticol').style.visibility='hidden';\">Annuler</a><br>
  $opcol
  <input type='button' onclick=\"document.listage.action='./?option=$option&$paramc&columns=1';document.listage.submit();\" value='ok'>
  </div>
  </div>
  </td></tr>";
   
	} 
	
	if(isset($allcols) && !in_array($order,$allcols)){
		$order='`id` DESC';
	}
	if($al==="t" && ($u_droits == '' || $u_active == 1) ){
		$limitation='';	
	}
	
	
	 $result = mysql_query("SELECT * FROM `$tabledb` $wheredb ORDER BY $order $limitation");
	//echo"<!-- SELECT * FROM `$tabledb` $wheredb ORDER BY $order $limitation-->";
  if($result && mysql_num_rows($result) > 0){
	  

		$setopo = array("setvalid","unsetvalid");
						$setopot = array("activer cet élément","désactiver cet élément");
						$l=0;
	  
	  
	  if(isset($_POST['multienreg']) && !isset($_GET['multi'])){
		 $result = mysql_query("SELECT * FROM `$tabledb` ORDER BY $order $limitation");
		 while ($row = mysql_fetch_object($result)) {
			$this_id= $row->id;
			 if(isset($_POST["id".$this_id])){
				$h = "`id`='$this_id'";
				for ($i = 0; $i < $columns; $i++) {
					 $field_name = mysql_field_name($res_field, $i);
					 if(isset($_POST[$field_name.$this_id])){
						 $h.=",`$field_name`='".str_replace("'","''",stripslashes($_POST[$field_name.$this_id]))."'";
					 }
				}
				if(mysql_query("UPDATE `$tabledb` SET $h WHERE `id`='$this_id'")){
					$return.=returnn("Modification pour id #$this_id dans $part effectuée avec succès","009900",$vers,$theme);
				} 
				else{
					$return.=returnn("La Modification pour id #$this_id dans $part a échoué","990000",$vers,$theme);
				}
			  }			  
		 	}
			if(is_numeric($_POST['addfields']) && $_POST['addfields']>0){
			  for($i=0 ; $i<$_POST['addfields'] ; $i++){
				  if(mysql_query("INSERT INTO `$tabledb` (`clon`) VALUES ('0')")){
					  $wheredb.=" OR `id`='".mysql_insert_id($conn)."'";
				  }
			  }
			}
		  $result = mysql_query("SELECT * FROM `$tabledb` $wheredb ORDER BY $order $limitation");
	  }

	  while ($row = mysql_fetch_object($result)) {
	  $l++;
	  $this_id= $row->id;
	  $this_active= $row->active;
				
/////////////////////////////////////////AFFICHAGE LISTE
if($al==="l" || !isset($al)){
	   if($bgtd == '1'){
		$bgtd='2';
		echo"<tr class='listone' ondblclick=\"javascript:document.location='index.php?option=$option&part=$part&amp;edit=$this_id'\">";
	   }
	   else{
		$bgtd='1';
		echo"<tr class='listtwo' ondblclick=\"javascript:document.location='./?option=$option&part=$part&d=$d&amp;edit=$this_id'\">";
	   }
	   echo"<td><span id='col_$l"."_0'></span><input id='che' type='checkbox' name='sel$this_id'><a href='./?option=$option&part=$part&d=$d&amp;edit=$this_id' class='info'><img src='$style_url/$theme/modif.gif' border='none' alt='éditer'><span>Modifier cet élément</span></a>";
				if(isset($comportement) && !in_array("nonew",$comportement)){
				echo"<a href='./?option=$option&part=$part&d=$d&amp;edit=$this_id&clone' class='info'><img src='$style_url/$theme/clone.gif' border='none' alt='cloner'><span>Cloner cet élément</span></a>";
				}
				echo"</td>";
	  for ($i = 0; $i < $columns; $i++) {
		  if(in_array($i,$cols)){
			$field_name = mysql_field_name($res_field, $i);
			$field_act = $field_name;
			if(isset($r_alias[$part][$field_name])){
				$field_act = $r_alias[$part][$field_name];
			}
			if(substr(strrev($field_act),0,3)=='hc_'){
				$mot = explode('_',strrev($field_act),4);	
				$mot = strrev($mot[3]);		
			}
			else{			
				$mot = explode('_',strrev($field_act),3);	
				if(isset($mot[2])) $mot = strrev($mot[2]);		
			}
			$field_type = mysql_field_type($res_field, $i);
			$field_value = $row->$field_name;
			$field_length = mysql_field_len($res_field, $i);
			if($field_name != "id" && $field_name != "clon" && ( (isset($multiple_depend[$part]) && $field_name!=$multiple_depend[$part][4]) || !isset($multiple_depend[$part])  )){	
				$cu = "off";
				$du = "off";
				$wid="";
				$tds='';
				$sty="position:relative;overflow:hidden";
				if($order == "'$field_name'" || $order == "'$field_name'DESC"){
					$cu = "on";
					$tds="class='menuselected'";
					//$wid = "width='120'";
					$sty="";
					$nowi=$i;
				}		
				if(ereg("_",$field_act) && substr($field_act,0,1) != "_" && !ereg("@",$field_act) && substr($field_act,0,9) != "nochange_" && mysql_query("SHOW COLUMNS FROM $mot") ){
				//field_name_human
					$nameifthefield = $mot;//substr($field_act,0,strpos($field_act,"_"));
					//$refiled = substr($field_act,0,strpos($field_act,"_"));				
					$fieldoption = substr($field_act,strlen($mot)+1,strlen($field_act));
					/*$fieldoption = substr($field_act,strpos($field_act,"_")+1,strlen($field_act));*/
					$fieldoptions = split("_",$fieldoption);
					$fieldoptionprint = $fieldoptions[1];
					if(ereg(">",$fieldoptionprint)){
						$fieldoptionprint = substr($fieldoptionprint,0,strpos($fieldoptionprint,">"));
					}
					if(strpos($fieldoptionprint,'/')>-1){
						$fopa = explode('/',$fieldoptionprint);	
						$fieldoptionprint="CONCAT(' '";
						foreach($fopa as $fopv){
							$fieldoptionprint.=",' ',`$fopv`";
						}
						$fieldoptionprint.=")";
					}
					$fieldoption = $fieldoptions[0];
					if(isset($fieldoptions[2]) && $fieldoptions[2]=='ch'){
						$field_value = substr_count($field_value,'>')." éléments";
					}
					elseif($nameifthefield != $fieldoption){
						$listres = mysql_query("SELECT $fieldoptionprint FROM `$nameifthefield` WHERE `$fieldoption`LIKE'".addslashes($field_value)."'");
						if($listres && mysql_num_rows($listres)>0){
							$rowlist = mysql_fetch_array($listres);
							$field_value = $rowlist[0];		
						}
					}					
				}
				else{
					$field_value=strip_tags($field_value);
				}
				if($field_value == "0000-00-00 00:00:00" || $field_value == "0000-00-00" || $field_value == "00:00:00"){
					$field_value = "...";	
				}
				elseif($field_type == "date"){
					$field_value = date("d/m/Y",strtotime($field_value));	
				}
				elseif($field_type == "time"){
					$field_value = substr($field_value,0,5);	
				}
				elseif($field_type == "datetime"){
					$field_value = date("d/m/Y - H:i",strtotime($field_value));	
				}
				if(strlen($field_value) > 40){     
					$field_value = substr($field_value,0,37)."...";     
				}
				if(ereg("couleur",$field_name) && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";
					}
					$field_value = "<center><div style=\"border-style:solid;border-width:1px;border-color:#999999;background-color:#$field_value;height:10px;width:30px\"></div></center>";
				 }
				 if(ereg("couleur",$field_name) && $field_length==7){
					if($field_value==""){
						$field_value="#FFFFFF";
					}
					$field_value = "<center><div style=\"border-style:solid;border-width:1px;border-color:#999999;background-color:$field_value;height:10px;width:30px\"></div></center>";
				 }
				 if($field_name == "pass" || $field_name == "passe"){
						$field_value = ereg_replace(".","*",$field_value);
				 }
				if($field_name == "active"){
					if($u_droits == '' || $u_active == 1 ){
						$field_value = "<center><a href='./?option=$option&part=$part&d=$d&$setopo[$field_value]=$this_id' class='info'>
						<img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'><span>$setopot[$field_value]</span></a></center>";
					}
					else{
						$field_value = "<img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'>";
					}
				}
				echo"<td align='center'><span id='col_$l"."_$i' style='$sty;display:block;height:12px;width:100%;text-align:left'><font size='1'>$field_value</font></span></td>";
			}
		 }
	}
			echo"<td align='center'>";
			if( (!isset($comportement) || (isset($comportement) && !in_array("nonew",$comportement))) && ($u_droits == '' || $u_active == 1 )){
			echo"<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
			}
			echo"</td></tr>\n";
			}
/////////////////////////////////////////AFFICHAGE TABLE
elseif($al==="t" && ($u_droits == '' || $u_active == 1) ){
	   echo"<tr>
	   <td valign='top'>$this_id<input type='hidden' name='id$this_id' value=\"$this_id\"><input id='che' type='checkbox' name='sel$this_id'></td>";
	  for ($i = 0; $i < $columns; $i++) {
		  if(in_array($i,$cols)){
			$field_name = mysql_field_name($res_field, $i);
			$field_act = $field_name;
			if(isset($r_alias[$part][$field_name])){
				$field_act = $r_alias[$part][$field_name];
			}
			if(substr(strrev($field_act),0,3)=='hc_'){
				$mot = explode('_',strrev($field_act),4);	
				$mot = strrev($mot[3]);		
			}
			else{			
				$mot = explode('_',strrev($field_act),3);	
				$mot = strrev($mot[2]);		
			}
			$field_type = mysql_field_type($res_field, $i);
			$field_value = $row->$field_name;
			$field_length = mysql_field_len($res_field, $i);
			if($field_name != "id" && $field_name != "clon" && ( (isset($multiple_depend[$part]) && $field_name!=$multiple_depend[$part][4]) || !isset($multiple_depend[$part])  )){	
				echo"<td valign='top'>";		
				
				if( ( ereg("_",$field_act) && mysql_query("SHOW COLUMNS FROM `$mot`") ) || ereg('@',$field_act) ){
					$refiled = $mot;
					$fieldoption = substr($field_act,strlen($mot)+1,strlen($field_act));
					if($nameifthefield == ucfirst(str_replace("_"," ",$field_act))){
						$nameifthefield = ucfirst($refiled);
					}
					
					
					if(ereg(">",$field_act)){
						$fieldoption = substr($fieldoption,0,strpos($fieldoption,">"));
						$nameifthefield .= " : ".substr($field_act,strpos($field_act,">")+1,strlen($field_act));
					}
					
					
					if(substr($field_act,0,1) == "@"){
						$nameofoption = substr($field_act,1,strlen($field_act));	
						$field_value = $_SESSION[$nameofoption];	
						echo"<input type=\"text\" name='$field_name$this_id' value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:80px\" readonly>";		
					}
					else{				
						$fieldoptions = explode("_",$fieldoption);
						$fieldoptionprint = $fieldoptions[1];
						if(strpos($fieldoptionprint,'/')>-1){
							$fopa = explode('/',$fieldoptionprint);	
							$fieldoptionprint="CONCAT(' '";
							foreach($fopa as $fopv){
								$fieldoptionprint.=",' ',`$fopv`";
							}
							$fieldoptionprint.=")";
						}
						$fieldoption = $fieldoptions[0];
						$refiled = trim($refiled);	
						if($prefixe!=""){
							$nameifthefield = trim(str_replace($prefixe,"",$nameifthefield));
						}
						$nameifthefield = ucfirst(trim(str_replace("_"," ",$nameifthefield)));
						
						$sepa='site';
							
						for($m=0; $m<sizeof($menu) ; $m++){
							$spart = $menupart[$m];
							$tablo = $menu[$spart];
							if(in_array($refiled,$tablo)){							
								if(substr($spart,0,7)=='worknet') $sepa='worknet';
								if(substr($spart,0,7)=='gestion') $sepa='gestion';	
								break;
							}
						}
						
					   if((sizeof($fieldoptions)==3 && $fieldoptions[2]=='ch') || (sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlch')){
					   
						  echo"<input type='hidden'  name='$field_name$this_id' value=\"".str_replace('"','&quot;',$field_value)."\">";
							$c=0;
							$hot=46;
							$ch=0;
							$prh='';
							$hut=0;
							$seled = '';
							if(sizeof($fieldoptions)==3){
								$listres = mysql_query("SELECT $fieldoptionprint,`$fieldoption`,`id` FROM `$refiled` WHERE `$fieldoptionprint`!='' ORDER BY 1");
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
									$prh.="<li><input type='checkbox' name='cho$this_id$i$c'   value=\"$rowid\"  title=\"$rowvaluu\" onclick=\"rempli(document.getElementById('ulch_$this_id$i'),document.listage.$field_name$this_id,document.listage.ch_cu_$this_id$i,document.getElementById('chu_$this_id$i'))\" $se>$rowvalue <a href='./?option=$option&$refiled&edit=$roid'>></a></li>";
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
									$prh.="<li><input type='checkbox' name='cho$this_id$i$c' value=\"$rowvaluu\"  title=\"$rowvaluu\"  onclick=\"rempli(document.getElementById('ulch_$this_id$i'),document.listage.$field_name$this_id,document.listage.ch_cu_$this_id$i,document.getElementById('chu_$this_id$i'))\" $se>$rowvalue</li>";
								}
							}
							
							if($hot>300) $hot=300;
							echo"
							<script language=\"JavaScript\">
							hut = $hut;
							</script>
							<a href='#ch$this_id$i' name='ch$this_id$i' onclick=\"dec('ch_$this_id$i',$hot);dec('chu_$this_id$i',1)\"><b><img src='$style_url/$theme/class_down_off.jpg' alt='v' border='none'> Développer <img src='$style_url/$theme/class_down_off.jpg' alt='v' border='none'></b></a>
							<input type='text' name=\"ch_cu_$this_id$i\" value=\"$ch\" style='border:none;background:none;width:30px;text-align:right' readonly> / $c sélectionnés<br>
							<div id='ch_$this_id$i' style='display:block;width:380px;height:1px;overflow:hidden;'>
							<a href='#ch$this_id$i' onclick=\"dec('ch_$this_id$i',1);dec('chu_$this_id$i',hut)\"><b><img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'> réduire <img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
							<li><input type='checkbox' name='all$this_id$i$c' onclick=\"tout(document.getElementById('ulch_$this_id$i'),this, document.listage.$field_name$this_id,document.listage.ch_cu_$this_id$i,document.getElementById('chu_$this_id$i'))\"> Tout</li>
							<ul id='ulch_$this_id$i'>	
							
							$prh
							</ul>
							<a href='#ch$this_id$i' onclick=\"dec('ch_$this_id$i',1);dec('chu_$this_id$i',hut)\"><b><img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'> réduire <img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
							</div>
							<div id='chu_$this_id$i' style='display:block;width:380px;height:$hut"."px;overflow:hidden;'  onclick=\"dec('ch_$this_id$i',$hot);dec('chu_$this_id$i',1)\">						
							$seled
							</div>
							
							";					
						}	
						elseif(sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlse'){
							echo"<select name=\"$field_name$this_id\" style=\"width:80px\">
								<option value=' '>liste des choix</option>";
								$listres = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled`");
								while($rowlist = mysql_fetch_array($listres)){
									$gvl = explode("\n",$rowlist[0]);
									foreach($gvl as $rowvalue){
										$rowvalue = trim($rowvalue);
										if($rowvalue!=''){
											$se = "";
											if($rowvalue == $field_value){
												$se = "selected";
											}
											$rowvaluu=str_replace('"','&quot;',$rowvalue);
											echo"<option value=\"$rowvaluu\" $se>$rowvalue</option>";
										}
									}
								}
								echo"</select>";
							
						}
						else{
							echo"<select name=\"$field_name$this_id\" style=\"width:80px\">
								<option value=''></option>";
								$listres = mysql_query("SELECT DISTINCT(`$fieldoption`),$fieldoptionprint  FROM `$refiled` ORDER BY 2");
								if(isset($where_multi) && $edit!='' && isset($this_from_multiple)){
									if(mysql_query("SELECT `$m_field` FROM `$refiled`")){
										$listres = mysql_query("SELECT DISTINCT(`$fieldoption`),$fieldoptionprint  FROM `$refiled` WHERE `$m_field`='$this_from_multiple' ORDER BY 1");
									}
								
								}
								while($rowlist = mysql_fetch_array($listres)){
									$rowvalue = $rowlist[1];
									$rowid = $rowlist[0];
									$se = "";
									if($rowid == $field_value){
										$se = "selected";
									}
									if($rowvalue!=''){
									echo"<option value=\"$rowid\" $se>$rowvalue</option>";
									}
								}
								echo"</select>";
						}
					 }	 
					 
				}
				elseif(ereg("couleur",$field_name) && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";
					}
					echo "#<input type='text' value='$field_value' name='$field_name$this_id' size='6' maxlength='6'>";
			   }
			   elseif(ereg("couleur",$field_name) && $field_length==7){
					if($field_value==""){
						$field_value="#FFFFFF";
					}
					echo "<input type='color' value='$field_value' name='$field_name$this_id'>";
			   }
			   elseif($field_name == "pass" || $field_name == "passe"){
					  echo"<input type='password' name='$field_name$this_id' value=\"$field_value\">";
			   }
				elseif($field_name == "mail" || $field_name == "email"){
					  echo"<input type='email' name='$field_name$this_id' value=\"$field_value\">";
			   }
				elseif($field_act == "url" || $field_act == "lien"){
					  echo"<input type='text' name='$field_name$this_id' value=\"$field_value\">";
			   }
				elseif($field_name == "active"){
					echo "<select name='active$this_id'><option value='1' $selected1[$this_active]>Oui</option><option value='0' $selected0[$this_active]>Non</option></select>";
				}
				elseif($field_type == "blob"){
					echo "<textarea name='$field_name$this_id' style='width:150px;height:20px;' onfocus=\"this.style.height='75px';\" onblur=\"this.style.height='22px';\">$field_value</textarea>";
				}
				
				elseif($field_type == "date"){
					echo "<input type='date' name='$field_name$this_id' value='$field_value'>";
				}
				elseif($field_type == "time"){
					echo "<input type='time' step='300' name='$field_name$this_id' value='$field_value'>";
				}
				elseif($field_type == "datetime"){
					echo "<input type='datetime-local' step='60' name='$field_name$this_id' value='".str_replace(' ','T',substr($field_value,0,16)).":00'>";
				}
				elseif($field_type == "int"){
					echo "<input type='number' name='$field_name$this_id' value='$field_value'>";
				}
				elseif($field_type == "real" || $field_type == "float" || $field_type == "int"){
					echo "<input type='decimal' name='$field_name$this_id' value='$field_value'>";
				}
				else{
					echo"<input type='text' name='$field_name$this_id' value=\"$field_value\">";
				}
				echo"</td>";
			}
		 }
	}
			echo"<td align='center'>&nbsp;</td></tr>\n";
			}
/////////////////////////////////////////AFFICHAGE DATE
elseif($al==="d"){
				$print_value="";
				$entiervalue='';
				$datefiles=array();
				for ($i = 0; $i < $columns; $i++) {
					$field_name = mysql_field_name($res_field, $i);
					$field_value = strip_tags($row->$field_name);
					$field_type = mysql_field_type($res_field, $i);
					$field_length = mysql_field_len($res_field, $i);
					$field_act = $field_name;
					if(isset($r_alias[$part][$field_name])){
						$field_act = $r_alias[$part][$field_name];
					}
					if(substr(strrev($field_act),0,3)=='hc_'){
						$mot = explode('_',strrev($field_act),4);	
						$mot = strrev($mot[3]);		
					}
					else{			
						$mot = explode('_',strrev($field_act),3);	
						$mot = strrev($mot[2]);		
					}
					$fieldoption = substr($field_act,strlen($mot)+1,strlen($field_act));
					if($field_type=="string" && $field_name != "id" && $field_name != "couleur" && $field_name != "clon" && substr($field_name,0,1) != "_" && !ereg("@",$field_name) && $field_value!='' ){						  
							$print_value .= str_replace("'","\\'",$field_value).' ';
					}
					elseif(($field_type == "int" || $field_type=="real") &&  ereg("_",$field_act) && substr($field_act,0,1) != "_" && !ereg("@",$field_act) && substr($field_act,0,9) != "nochange_" && mysql_query("SHOW COLUMNS FROM $mot") ){
						$nameifthefield = $mot;
						$fieldoption = substr($field_act,strlen($mot)+1,strlen($field_act));
						$fieldoptions = split("_",$fieldoption);
						$fieldoptionprint = $fieldoptions[1];
						if(ereg(">",$fieldoptionprint)){
							$fieldoptionprint = substr($fieldoptionprint,0,strpos($fieldoptionprint,">"));
						}
						$fieldoption = $fieldoptions[0];
						if(isset($fieldoptions[2]) && $fieldoptions[2]=='ch'){
							$field_value = substr_count($field_value,'>')." éléments";
						}
						elseif($nameifthefield != $fieldoption){
							$listres = mysql_query("SELECT * FROM `$nameifthefield` WHERE `$fieldoption`LIKE'".addslashes($field_value)."'");
							$rowlist = mysql_fetch_object($listres);
							$field_value = $rowlist->$fieldoptionprint;									
						}	
						$print_value .= str_replace("'","\\'",$field_value).' ';
						$entiervalue .= str_replace("'","\\'",$field_value).' ';				
					}
					if(($field_type=="string" || $field_type=="blob") && $field_name != "id" && $field_name != "couleur" && $field_name != "clon" && substr($field_name,0,1) != "_" && !ereg("@",$field_name) && $field_value!='' ){						  
							$entiervalue .= str_replace("'","\\'",$field_value).' ';
					}
					if($field_type=="date" || $field_type=="datetime"){
						$fieldate = date('Y-m-d',strtotime($field_value));
						$datefiles[$fieldate]='';
						$entiervalue .= date("d/m/Y",strtotime($field_value)).' ';
					}
					//echo"<!-- $field_type -->";
				}	
				$agebody='';
				$bodi=split("\n",strip_tags(trim($entiervalue)));
				for($e=0 ; $e<sizeof($bodi) ; $e++){
					$agebody.=trim(trim($bodi[$e]))." ";
				}
				$entiervalue = urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode($agebody))));
				$print_value = substr($print_value,0,40);
				foreach($datefiles as $kd=>$vd){
					$jsagenda.="if(document.getElementById('age_$kd')){
					document.getElementById('age_$kd').innerHTML+=\"<table class='bando' width='100%' style='margin:1px;'><tr><td><a href='./?option=$option&part=$part&d=$d&edit=$this_id' class='info'><font size='1'>$im $print_value<span>$entiervalue</span></font></a></td><td align='right'>";
					    if($u_droits == '' || $u_active == 1 ){
							//if(isset($comportement) && !in_array("nonew",$comportement) ){
								$jsagenda.="<input id='che' type='checkbox' name='sel$this_id'><a href='./?option=$option&part=$part&d=$d&$setopo[$this_active]=$this_id' class='info'><img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'><span>$setopot[$this_active]</span></a>";
							/*}
							$jsagenda.="";
							if(isset($comportement) && !in_array("nonew",$comportement) ){
								$jsagenda.="<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
							}*/
						}
						else{
							$jsagenda.="<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'>";
						}
					
					$jsagenda.="</td></tr></table>\";
					}";
				}
			}

/////////////////////////////////////////AFFICHAGE ICONES
else{
					echo"
					<table class='buttontd' width='80%' cellspacing='0' cellpadding='0' border='0' style='float:left;width:100px;height:100px;margin:2px;padding:2px;'>
					<tr><td align='center'>";
				$print_value="";
			for ($i = 0; $i < $columns; $i++) {
				$field_name = mysql_field_name($res_field, $i);
				$field_value = strip_tags($row->$field_name);
				$field_type = mysql_field_type($res_field, $i);
				$field_length = mysql_field_len($res_field, $i);
				if($field_type=="string" && $field_name != "id" && $field_name != "couleur" && $field_name != "clon" && substr($field_name,0,1) != "_" && !ereg("@",$field_name) && $field_value!='' && substr_count($field_name,"_") < 2 ){						  
						$print_value = $field_value;
						break;
				}		
				}			
				$im = geticon($part,$this_id,$print_value," height='50' border='none'");
				if($im==""){
					$im="<img src='$style_url/$theme/fichier.gif' border='none' alt='print_value'>";
				}
				if(trim($print_value)===""){
					$print_value = "fichier n°$this_id";
				}				
				$entiervalue=$print_value;
					if(strlen($print_value) > 15){
						$print_value = substr($print_value,0,12)."...";
						}
				echo"<a href='./?option=$option&part=$part&d=$d&edit=$this_id' class='info'  style='position:relative;display:block;width:90px;height:70px;overflow:hidden;'><font size='1'>$im<br>$print_value<span>$entiervalue</span></font></a>";
			echo"</td></tr><tr><td valign='bottom' align='right'><input id='che' type='checkbox' name='sel$this_id'>";
			

						if($u_droits == '' || $u_active == 1 ){
							echo"<a href='./?option=$option&part=$part&d=$d&$setopo[$this_active]=$this_id' class='info'>
							<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'><span>$setopot[$this_active]</span></a> ";
							if(isset($comportement) && !in_array("nonew",$comportement) ){
								echo"<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
							}
						}
						else{
							$field_value = "<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'>";
						}
					
					echo"</td></tr></table>";
			
			}
	  // echo"<td><a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a></td>";
	  if($this_id!=0 && $this_id!=''){
	   $resultclon = mysql_query("SELECT * FROM `$tabledb` WHERE `clon`='$this_id' ORDER BY $order");
	   while ($rowclon = mysql_fetch_object($resultclon)) {
			$this_idc= $rowclon->id;
			echo"<tr>
			<td style='white-space:nowrap'>
			<img src='$style_url/images/sous.gif' border='none' alt='|_'>
			<input id='che' type='checkbox' name='sel$this_idc'>
			<a href='./?option=$option&part=$part&d=$d&amp;edit=$this_idc' class='info'><img src='$style_url/$theme/modif.gif' border='none' alt='éditer'><span>Modifier ce clone</span></a></td>";
				   	for ($i = 0; $i < $columns; $i++) {
						if(in_array($i,$cols)){
							$field_name = mysql_field_name($res_field, $i);
							$field_value = strip_tags($rowclon->$field_name);
							if($field_name != "id" && $field_name != "clon" ){						
								if(ereg("_",$field_name) && substr($field_name,0,1) != "_" && !ereg("@",$field_name)){
									$nameifthefield = substr($field_name,0,strpos($field_name,"_"));
									$fieldoption = substr($field_name,strpos($field_name,"_")+1,strlen($field_name));
									$fieldoption = split("_",$fieldoption);
									$fieldoptionprint = $fieldoption[1];
									if(ereg(">",$fieldoptionprint)){
										$fieldoptionprint = substr($fieldoptionprint,0,strpos($fieldoptionprint,">"));
									}
									$fieldoption = $fieldoption[0];								
									$listres = mysql_query("SELECT * FROM `$nameifthefield` WHERE `$fieldoption`=$field_value");							
									if($listres && mysql_num_rows($listres)>0){
										$rowlist = mysql_fetch_object($listres);
										$field_value = $rowlist->$fieldoptionprint;
									}
								}			
								if(strlen($field_value) > 50){     
									$field_value = substr($field_value,0,47)."...";     
								}
								if($field_name == "active"){
									$field_value = "<img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'>";
								}
								echo"<td class='cadre'><font size='1'>$field_value</font></td>";
							}
						}
					 }
			echo"<td align='center'>";
			if( (!isset($comportement) || (isset($comportement) && !in_array("nonew",$comportement))) && ($u_droits == '' || $u_active == 1 )){
			echo"<a href='#' onclick='confsup($this_idc)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
			}
			echo"
			</td></tr>";
	   }
	   }
		  
	  }
  
  }
  elseif($totro < $totra){ 
	echo"<td colspan='$columns' align='center'><br>ce tableau est vide...<br>
	><a href=\"./?option=$option&part=$part&affdesac=0\">afficher aussi les désactivés</a><br><br>
	<a href=\"./?option=$option&part=$part&d=$d&amp;edit$addlien\" class='grosbouton'>ajouter</a>
	<br><br>";
	if($validouille!=''){
		echo" <p align='left'>Filtres actifs :<br/>$validouille</p>";	
	}
	echo"</td></tr>\n";
  }
  else{
  	echo"<td colspan='$columns' align='center'><br>ce tableau est vide...<br><br>
	<a href=\"./?option=$option&part=$part&d=$d&amp;edit$addlien\" class='grosbouton'>ajouter</a>
	<br><br>";
	if($validouille!=''){
		echo" <p align='left'>Filtres actifs :<br/>$validouille</p>";	
	}
	echo"</td></tr>\n";
  }
  
  
  if($al==="t"){
	  echo"<td colspan='$columns' align='left'>
	  <input type='hidden' name='multienreg' value='1' />
	  Ajouter : <input type='number' value='0' name='addfields'> lignes
	  &nbsp;
	  &nbsp;
	  <input type='submit' class='buttontd' value='Tout enregistrer'>
	 
	  </td></tr>\n";
  }
  echo"</table></div>\n";
		
			if($al==="l" || !isset($al)){
				echo"
  	<script language='javascript'>
		function larglist(){
			if (navigator.appName.indexOf('Microsoft')!=-1) {
				var winW = parseInt(document.body.offsetWidth);
			 }
			 else{
				var winW = window.innerWidth;
			 }
			 winW-=parseInt(document.getElementById('panelp').style.width)+220;
			 if(winW<400) winW=400;
			 
			 colw = Math.round(winW/".(sizeof($cols)-1).");
			 var cols=new Array(''".implode(",",$cols).");
			for(l=0 ; l<=".($l)." ; l++){
				for(i=1 ; i<cols.length ; i++){
					c = cols[i];
					if(c!='' && document.getElementById('col_'+l+'_'+c)){
						wu = colw;
						if(c==$kiwi){
							wu = document.getElementById('col_'+l+'_'+c).innerWidth;
							if(wu>120){
								w=120;
							}
						}
						tr=document.getElementById('col_'+l+'_'+c).style;
						tr.width=wu+'px';
					}
				}
			}
			 setTimeout('larglist()',20);
		 }
		 larglist();
	</script>";
			}
			elseif($al==="d"){
				echo"
  	<script language='javascript'>
		$jsagenda
	</script>";
			}
			
			
			 echo"</div>";
?>