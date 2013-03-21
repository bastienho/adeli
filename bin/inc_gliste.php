<?php // 64 > Liste des élément Gestion ;
 $wheredb="WHERE `clon`='0'";
  if($incwhere !== null){
  	$wheredb = "$incwhere AND `clon`='0'";
  }
echo"<hr>";
			if(isset($_GET['pp'])){
				$pp=abs($_GET['pp']);
				set_pref("pp.$part.conf",$pp);
			}
			else{
				$pp = abs(get_pref("pp.$part.conf"));		
			}
			if(!isset($_SESSION['pa'])){
				$pa=1;$_SESSION['pa']=$pa;
			}
			if(isset($_GET['pa'])){
				$pa=abs($_GET['pa']);$_SESSION['pa']=$pa;
			}
			echo"afficher <input type='text' style='width:30px;font-size:10px' value='$pp' onchange=\"affichload;document.location='./?option=$option&part=$part&pp='+this.value\"> enregistrements par page";
			$result = mysql_query("SELECT `id` FROM `$tabledb` $wheredb");
			$totro = mysql_num_rows($result);		
			$np = round($totro/$pp);
			if($np*$pp < $totro){
				$np++;
			}
			if($_SESSION['pa']>$np){
				$_SESSION['pa']=1;
				$pa = $_SESSION['pa'];
			}
			$tnp=1;
			if(in_array("nonew",$comportement) && $totro == 1){
				$ro = mysql_fetch_object($result);
				$godi = $ro->id;
				echo"
				<script language='javascript' type='text/javascript'>
				document.location='./?option=$option&part=$part&edit=$godi';
				</script>
				";
			}

			echo"<select style='font-size:10px' onchange=\"affichload;document.location='./?option=$option&part=$part&pa='+this.value\">";
			while($tnp<=$np){
				if($tnp == $pa){
					echo"<option value='$tnp' selected>$tnp</option> ";
				}
				else{
					echo"<option value='$tnp'>$tnp</option> ";
				}
				$tnp++;
			}	
			$pl = ($pa-1)*$pp;
			if($pl<0){$pl=0;}
			echo"</select> (<b>$totro enregistrements</b>) &nbsp;";
	
/******************************************************* SPECIALS ************/
if($part=="gestion_articles"){ //////////////////////////////////////////ARTICLES
	$result = mysql_query("SELECT 
						  		DISTINCT 
						  		`gestion_articles`.`id`,`gestion_articles`.`active`,`gestion_articles`.`rayon`,`gestion_articles`.`nouveaute` ,`gestion_artrad`.`nom` 
						  FROM 
						  		`gestion_articles` , `gestion_artrad` 
						  WHERE 
						  		`gestion_articles`.`clon`=0 AND `gestion_artrad`.`ref`=`gestion_articles`.`id` AND `gestion_artrad`.`nom`!=''
							ORDER BY 
								`gestion_artrad`.`nom`
						  LIMIT $pl,$pp");
  if($result && mysql_num_rows($result) > 0){
  
		$setopo = array("setvalid","unsetvalid");
		$setopot = array("activer cet élément","désactiver cet élément");
		$setfoc = array("focus","unfocus");
		$setfoct = array("Focaliser sur cet élément","Ne plus focaliser");
						
	echo"<style>
	.ophe{
		border-color:#FF0000;
		border-width:2px;
	}
	.ga_link{
		display:block;
		width:120px;
		height:40px;
		overflow:hidden;
	}
	</style>";
	  while ($row = mysql_fetch_array($result)) {
	  	$this_id = $row[0];
		$this_active = $row[1];
		$this_rayon = $row[2];
		$this_focus = $row[3];
		$hg=get_item_trans($this_id,"ar");
		$ima = "$style_url/$theme/fichier.gif";
		$co='';
		$com='';
		if($this_rayon==0){
			$co=' orphe';
			$com='article orphelin !';
		}
		if(is_file("../gestion_articles/$this_id.jpg")) $ima="../gestion_articles/$this_id.jpg";  
		
		if($al==="l" || !isset($al)){
		echo"
	<table class='cadre$co' width='250' cellspacing='0' cellpadding='5' border='0' style='float:left;width:200px;height:50px;margin:5px;' >
		   <tr><td><input id='che' type='checkbox' name='sel$this_id'></td><td>$com	   			
					<a href='./?option=$option&part=$part&edit=$this_id' class='ga_link'><b>$hg</b></a>
					</td><td align='right'>";
		}
		else{
			echo"
	<table class='cadre$co' width='150' cellspacing='0' cellpadding='0' border='0' style='float:left;width:150px;height:130px;margin:2px;'>
		   <tr><td><p align='center'>	$com	   			
					<a href='./?option=$option&part=$part&edit=$this_id' style='display:block;width:120px;height:70px;overflow:hidden;'><img src='$ima' alt='$sub_id' height='50' width='50' border='none'><br>
					<b>$hg</b></a>
					</p></td></tr>
					<tr><td valign='bottom' align='right'><input id='che' type='checkbox' name='sel$this_id'>";
		}
						if($u_droits == '' || $u_active == 1 ){
							echo"<a href='./?option=$option&part=$part&$setfoc[$this_focus]=$this_id' class='info'><img src='$style_url/images/star$this_focus.gif' border='none' alt='Focus: $this_focus'><span>$setfoct[$this_focus]</span></a>&nbsp;<a href='./?option=$option&part=$part&$setopo[$this_active]=$this_id' class='info'><img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $this_active'><span>$setopot[$this_active]</span></a>&nbsp;<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
						}
						else{
							echo"<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'>";
						}
					echo"
					
					</td></tr></table>";
		 }
  }
  else{
  	echo"<br>Aucun article n'a été créé...<br><br>\n";
  }
}
elseif($part=="gestion_rayons"){ //////////////////////////////////////////RAYONS
	$result = mysql_query("SELECT * FROM `gestion_rayons` $wheredb  AND `ref`=0 ORDER BY `id`DESC LIMIT $pl,$pp");
  if($result && mysql_num_rows($result) > 0){
  
		$setopo = array("setvalid","unsetvalid");
		$setopot = array("activer cet élément","désactiver cet élément");
						
	  while ($row = mysql_fetch_object($result)) {
	  	$this_id = $row->id;
		$this_active = $row->active;
		$hg=get_item_trans($this_id,"ray");
		$ima = "$style_url/$theme/fichier.gif";
		if(is_file("../gestion_rayons/$this_id.jpg")) $ima="../gestion_rayons/$this_id.jpg";
		
		if($al==="l" || !isset($al)){
		echo"
	<table class='cadre' width='250' cellspacing='0' cellpadding='5' border='0' style='float:left;width:200px;height:50px;margin:5px;$co'>
		   <tr><td><input id='che' type='checkbox' name='sel$this_id'></td><td>$com	   			
					<a href='./?option=$option&part=$part&edit=$this_id' style='display:block;width:120px;height:40px;overflow:hidden;'><b>$hg</b></a>
					</td><td align='right'>";
		}
		else{
			echo"
	<table class='cadre' width='150' cellspacing='0' cellpadding='0' border='0' style='float:left;width:130px;height:100px;margin:2px;'>
		   <tr><td valign='bottom'><p align='center'>
					<a href='./?option=$option&part=$part&edit=$this_id' style='display:block;width:120px;height:70px;overflow:hidden;'><img src='$ima' alt='$sub_id' height='50' border='none'><br>
					<b>$hg</b></a>
					</p></td></tr>
					<tr><td valign='bottom' align='right'><input id='che' type='checkbox' name='sel$this_id'>";
		}
						if($u_droits == '' || $u_active == 1 ){
							echo"<a href='./?option=$option&part=$part&$setopo[$this_active]=$this_id' class='info'>
							<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'><span>$setopot[$this_active]</span></a>
							<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a> ";
						}
						else{
							echo"<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'>";
						}
					echo"
					
					</td></tr></table>";
		 }
  }
  else{
  	echo"<br>Aucun rayon n'a été créé...<br><br>\n";
  }
}
/******************************************************* DEFAULTS ************/
else{
		$allcols=array();	
		
		if($al==="l"){
	echo"
	<table width='100%' cellspacing='1' cellpadding='0' border='0' bgcolor='#EEEEEE'><tr class='buttontd'>";
   $res_field = mysql_list_fields($base,$tabledb);
   $columns = mysql_num_fields($res_field);
   for ($i = 0; $i < $columns; $i++) {
		$field_name = mysql_field_name($res_field, $i);
		$field_type = mysql_field_type($res_field, $i);
		$field_name_human=ereg_replace("nochange_","",$field_name);
		array_push($allcols,"'$field_name'");
		array_push($allcols,"'$field_name'DESC");
		if( $field_name != "clon" ){
			$cu = "off";
			$du = "off";
			$tds='';
			if($order == "`$field_name`"){
				$cu = "on";
				$tds="class='menuselected'";
			}
			if($order == "`$field_name`DESC"){
				$du = "on";
				$tds="class='menuselected'";
			}
			if(substr($field_name_human,0,1) == "_"){
				$field_name_human = substr($field_name_human,1,strlen($field_name_human));
			}
			elseif(ereg("_",$field_name_human)){
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
			$wid="";
			if($field_name=="active" || $field_name=="ordre"){ $wid="width='20'"; }
			 echo"<td align='center' $tds nowrap='nowrap' $wid><font size='1'><b>$field_name_human&nbsp;</b><a href=\"./?option=$option&part=$part&amp;order=`$field_name`\" title='$field_type'><img src='$style_url/$theme/class_up_$cu.jpg' alt='^' border='none'></a><a href=\"./?option=$option&part=$part&amp;order=`$field_name`DESC\" title='$field_type'><img src='$style_url/$theme/class_down_$du.jpg' alt='^' border='none'></a></font></td>";
		 }
   }
			
		}
   $bgtd == '1'; 
  echo"<td width='20'></td></tr>";

/*****************************************************************************/
	
	if(!in_array($order,$allcols)){$order='`id` DESC';}
	$result = mysql_query("SELECT * FROM `$tabledb` $wheredb ORDER BY $order LIMIT $pl,$pp");
  if(mysql_num_rows($result) > 0){
  
		$setopo = array("setvalid","unsetvalid");
						$setopot = array("activer cet élément","désactiver cet élément");
						
	  while ($row = mysql_fetch_object($result)) {
	   $this_id= $row->id;
				$this_active= $row->active;
				
				/////////////////////////////////////////AFFICHAGE LISTE
				if($al==="l"){
	   if($bgtd == '1'){
		$bgtd='2';
		echo"<tr class='listone' ondblclick=\"javascript:document.location='index.php?$part&amp;edit=$this_id'\">";
	   }
	   else{
		$bgtd='1';
		echo"<tr class='listtwo' ondblclick=\"javascript:document.location='./?option=$option&part=$part&amp;edit=$this_id'\">";
	   }
	   echo"<td><input id='che' type='checkbox' name='sel$this_id'><a href='./?option=$option&part=$part&amp;edit=$this_id' class='info'><img src='$style_url/$theme/modif.gif' border='none' alt='éditer'><span>Modifier cet élément</span></a></td>";
	   	for ($i = 0; $i < $columns; $i++) {
			$field_name = mysql_field_name($res_field, $i);
			$field_type = mysql_field_type($res_field, $i);
			$field_value = strip_tags($row->$field_name);
			$field_length = mysql_field_len($res_field, $i);
			if($field_name != "id" && $field_name != "clon" ){			
				if(ereg("_",$field_name) && substr($field_name,0,1) != "_" && !ereg("@",$field_name) && substr($field_name,0,9) != "nochange_" ){
					$nameifthefield = substr($field_name,0,strpos($field_name,"_"));
					$fieldoption = substr($field_name,strpos($field_name,"_")+1,strlen($field_name));
					$fieldoption = split("_",$fieldoption);
					$fieldoptionprint = $fieldoption[1];
					if(ereg(">",$fieldoptionprint)){
						$fieldoptionprint = substr($fieldoptionprint,0,strpos($fieldoptionprint,">"));
					}
					$fieldoption = $fieldoption[0];	
					
					$listres = mysql_query("SELECT * FROM `$nameifthefield` WHERE `$fieldoption`='".addslashes($field_value)."'");
					$rowlist = mysql_fetch_object($listres);
					$field_value = $rowlist->$fieldoptionprint;							
				}
	
				if(strlen($field_value) > 40){     
					$field_value = substr($field_value,0,37)."...";     
				}
				if($field_name == "couleur" && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";
					}

					$field_value = "<center><div style=\"border-style:solid;border-width:1px;border-color:#999999;background-color:#$field_value;height:10px;width:30px\"></div></center>";
			 }
				if($field_name == "contenu" || $field_name == "content"){
					$field_value=substr_count($row->$field_name,'<!>')." articles";
			 }
				if($field_name == "idclient" || $field_name == "client" ){
					if($field_value==0){
						$field_value="client inconnu ou de passage";
					}
					else{
						$rec = mysql_query("SELECT `nom` FROM `clients` WHERE `id`=$field_value");
						$roc = mysql_fetch_object($rec);
						$noc = $roc->nom;
						$field_value="<a href='./?option=worknet&clients&edit=$field_value'>$noc</a>";
					}
			 }
				if($field_name == "etat"){
					$statut = $field_value;
					$field_value="<center><table bgcolor='#FFFFFF' cellspacing='1' cellpadding='0'><tr><td style='font-size:9px;color:#$colorstatut[$statut]' valign='top'>$defstat[$statut]</td>";
						for($s=0; $s<=$statut ; $s++){
								$field_value.="<td bgcolor=\"#$colorstatut[$statut]\">.</td>";
						}
						for($s=$statut-1; $s<sizeof($defstat)-2 ; $s++){
								$field_value.="<td bgcolor='#CCCCCC'>.</td>";
						}
						$field_value.="</tr></table></center>";
			 }
				
				if($field_type == "datetime" && $part=="gestion_rappel"){
					if(date('U')>date('U',strtotime($field_value))){
						$field_value = "<b>".date("d/m/Y - H:i",strtotime($field_value))."</b>";	
					}				
					elseif($field_value!= "0000-00-00 00:00:00"){
						$field_value = date("d/m/Y - H:i",strtotime($field_value));
					}	
					else $field_value="-";
				}
				elseif($field_type == "date"){
					if($field_value != "0000-00-00")	$field_value = date("d/m/Y",strtotime($field_value));	
					else $field_value="-";
				}
				elseif($field_type == "time"){
					if($field_value!= "00:00:00")	$field_value = date("H:i",strtotime($field_value));	
					else $field_value="-";
				}
				elseif($field_type == "datetime"){
					if($field_value!= "0000-00-00 00:00:00")	$field_value = date("d/m/Y - H:i",strtotime($field_value));	
					else $field_value="-";
				}
 			if($field_name == "pass" || $field_name == "passe"){
					$field_value = ereg_replace(".","*",$field_value);
			 }
		
		
				if($field_name == "active"){
					if($u_droits == '' || $u_active == 1 ){
						$field_value = "<center><a href='./?option=$option&part=$part&$setopo[$field_value]=$this_id' class='info'>
						<img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'><span>$setopot[$field_value]</span></a></center>";
					}
					else{
						$field_value = "<img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'>";
					}
				}
				echo"<td align='left' style='overflow:hidden;padding:1px'><span style='display:block;overflow:hidden;height:12px;width:100%'><font size='1'>$field_value</font></span></td>";
			}
		 }
			echo"<td>
			<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>
			</td></tr>\n";
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
				if($field_type=="string" && $field_name != "id" && $field_name != "couleur" && $field_name != "clon" && substr($field_name,0,1) != "_" && !ereg("@",$field_name) && $field_value!='' && substr_count($field_name,"_") < 2 && $field_type!="int" && $field_type!="real" ){						  				$print_value = $field_value;
						break;
				}		
				}			
				$im = geticon($part,$this_id,$print_value," width='50' height='50' border='none'");
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
				echo"<a href='./?option=$option&part=$part&edit=$this_id' class='info'><font size='1'>$im<br>$print_value<span>$entiervalue</span></font></a>";
			echo"</td></tr><tr><td valign='bottom' align='right'><input id='che' type='checkbox' name='sel$this_id'>";
			

						if($u_droits == '' || $u_active == 1 ){
							echo"<a href='./?option=$option&part=$part&$setopo[$this_active]=$this_id' class='info'>
							<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'><span>$setopot[$this_active]</span></a> ";
							echo"<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a></td></tr></table>";
						}
						else{
							$field_value = "<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'>";
						}
					
			
			}
  
		  
	  }
  
  }
  else{
  	echo"<td colspan='$columns' align='center'><br>ce tableau est vide...
	<br><br></td></tr>\n";
  }
} 
  echo"</table>\n";
?>