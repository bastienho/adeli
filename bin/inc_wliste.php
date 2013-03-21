<?php // 53 > Liste des éléments Worknet ;
 $wheredb="WHERE `clon`='0'";
 echo"<hr>";
  if($incwhere !== null){
  	$wheredb = "$incwhere AND `clon`='0'";
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

			if(isset($_GET['pp'])){
				$pp=abs($_GET['pp']);
				set_pref("pp.$part.conf",$pp);
			}
			else{
				$pp = get_pref("pp.$part.conf");			
			}
			if($pp==0)$pp=30;

			if(!isset($_SESSION['pa'])){
				$pa=1;$_SESSION['pa']=$pa;
			}
			if(isset($_GET['pa'])){
				$pa=abs($_GET['pa']);$_SESSION['pa']=$pa;
			}
			echo"afficher <input type='text' style='width:30px;font-size:10px' value='$pp' onchange=\"affichload;document.location='./?option=$option&part=$part&d=$d&pp='+this.value\"> enregistrements par page";
			$result = mysql_query("SELECT `id` FROM `$tabledb` $wheredb");
			$totro = mysql_num_rows($result);	
			if(isset($_GET['d']) && $_GET['d']!=''){
				$pp = $totro+1;			
			 }
			if($totro == 1){ //in_array("nonew",$comportement) && 
				$ro = mysql_fetch_object($result);
				$godi = $ro->id;
				echo"
				<script language='javascript' type='text/javascript'>
				document.location='./?option=$option&part=$part&d=$d&edit=$godi';
				</script>
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
			echo"</select> (<b>$totro enregistrements</b>) &nbsp;";
			
			
		$allcols=array();	
		
if($al==="l" || !isset($al)){
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
	<table width='100%' cellspacing='1' cellpadding='0' border='0' bgcolor='#EEEEEE'><tr class='buttontd'>";
   $res_field = mysql_list_fields($base,$tabledb);
   $columns = mysql_num_fields($res_field);
   $kiwi=1;
   for ($i = 0; $i < $columns; $i++) {
		$field_name = mysql_field_name($res_field, $i);
		$field_type = mysql_field_type($res_field, $i);
		$field_name_human=ereg_replace("nochange_","",$field_name);
		if($field_name_human == "dest"){
			$field_name_human = "destinataire";
		}
		if($field_name_human == "prov"){
			$field_name_human = "expéditeur";
		}
		array_push($allcols,"`$field_name`");
		array_push($allcols,"`$field_name`DESC");
		if( $field_name != "clon" ){
			$cu = "off";
			$du = "off";
			$wid="";
			$tds='';
			$sty="overflow:hidden";
			if($order == "`$field_name`" || $order == "`$field_name`DESC"){
				$cu = "on";
				if($order == "`$field_name`DESC"){
					$cu = "off";
					$du = "on";
				}
				$tds="class='menuselected'";
				$wid = "width='120'";
				$sty="";
				$kiwi=$i;
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
			
		$opcol.="<input type='checkbox' name='column_$i' value='1' class='noche' ";
		 if(in_array($i,$cols)){
			echo"<td align='center' $tds  nowrap='nowrap' $wid><font size='1'><span  id='col_0"."_$i' style='display:block;$sty;height:12px;width:100%;overflow-x:hidden'><b>$field_name_human&nbsp;</b></span><a href=\"./?option=$option&part=$part&d=$d&amp;order=`$field_name`\" title='$field_type'><img src='$style_url/$theme/class_up_$cu.jpg' alt='^' border='none'></a><a href=\"./?option=$option&part=$part&d=$d&amp;order=`$field_name`DESC\" title='$field_type'><img src='$style_url/$theme/class_down_$du.jpg' alt='^' border='none'></a></font></td>";
			$opcol.="checked";
		 }
		 $opcol.="/> $field_name_human<br>";
		
			
		 }
   }
			
			
			
			
   $bgtd == '1';
 
  echo"<td width='20'>
  <div style='position:relative; text-align:right;'>
  <a onclick=\"document.getElementById('opticol').style.visibility='visible';\">Colonnes</a>
  <div class='cadre' id='opticol' style='position:absolute;top:0px; left:-120px; width:150px; text-align:left; visibility:hidden; z-index:1500'>
  <a onclick=\"document.getElementById('opticol').style.visibility='hidden';\">Annuler</a><br>
  $opcol
  <input type='button' onclick=\"document.listage.action='./?option=$option&part=$part&d=$d&columns=1';document.listage.submit();\" value='ok'>
  </div>
  </div>
  </td></tr>";
		} 
	$pl = ($pa-1)*$pp;
	if($pl<0){
	$pl=0;
	}
	if(!in_array($order,$allcols)){
				$order='`id` DESC';
			}
	//echo"\\n<!-- SELECT * FROM `$tabledb` $wheredb ORDER BY $order LIMIT $pl,$pp -->";
	 $result = mysql_query("SELECT * FROM `$tabledb` $wheredb ORDER BY $order LIMIT $pl,$pp");
  if(mysql_num_rows($result) > 0){
  
		$setopo = array("setvalid","unsetvalid");
						$setopot = array("activer cet élément","désactiver cet élément");
						$l=0;
	  while ($row = mysql_fetch_object($result)) {
	   $this_id= $row->id;
	   $l++;
				$this_active= $row->active;
				
				/////////////////////////////////////////AFFICHAGE LISTE
				if($al==="l" || !isset($al)){
	   if($bgtd == '1'){
		$bgtd='2';
		echo"<tr class='listone' ondblclick=\"javascript:document.location='index.php?$part&d=$d&amp;edit=$this_id'\">";
	   }
	   else{
		$bgtd='1';
		echo"<tr class='listtwo' ondblclick=\"javascript:document.location='./?option=$option&part=$part&d=$d&amp;edit=$this_id'\">";
	   }
	   echo"<td><input id='che' type='checkbox' name='sel$this_id'><a href='./?option=$option&part=$part&d=$d&amp;edit=$this_id' class='info'><img src='$style_url/$theme/modif.gif' border='none' alt='éditer'><span>Modifier cet élément</span></a>";
				if(!in_array("nonew",$comportement)){
				echo"<a href='./?option=$option&part=$part&d=$d&amp;edit=$this_id&clone' class='info'><img src='$style_url/$theme/clone.gif' border='none' alt='cloner'><span>Cloner cet élément</span></a>";
				}
				echo"</td>";
	for ($i = 0; $i < $columns; $i++) {
		 if(in_array($i,$cols)){
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
					if(strpos($fieldoptionprint,'/')>-1){
						$fopa = explode('/',$fieldoptionprint);	
						$fieldoptionprint="CONCAT(' '";
						foreach($fopa as $fopv){
							$fieldoptionprint.=",' ',`$fopv`";
						}
						$fieldoptionprint.=")";
					}
					$fieldoption = $fieldoption[0];	
					if($nameifthefield != $fieldoption){
						$listres = mysql_query("SELECT $fieldoptionprint FROM `$nameifthefield` WHERE `$fieldoption`LIKE'".addslashes($field_value)."'");
						$rowlist = mysql_fetch_array($listres);
						$field_value = $rowlist[0];		
					}					
				}
				if($field_value == "000-00-00 00:00:00" || $field_value == "000-00-00" || $field_value == "00:00:00"){
					$field_value = "non définie";	
				}
				elseif($field_type == "date"){
					$field_value = date("d/m/Y",strtotime($field_value));	
				}
				elseif($field_type == "time"){
					$field_value = date("H:m",strtotime($field_value));	
				}
				elseif($field_type == "datetime"){
					$field_value = date("d/m/Y - H:m",strtotime($field_value));	
				}
				elseif($field_name == "etat" && $part=="adeli_messages"){
					$field_value = $stat_message[$field_value];	
				}
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
				if(strlen($field_value) > 40){     
					$field_value = substr($field_value,0,37)."...";     
				}
				if($field_name == "couleur" && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";
					}
					$field_value = "<center><div style=\"border-style:solid;border-width:1px;border-color:#999999;background-color:#$field_value;height:10px;width:30px\"></div></center>";
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
				echo"<td align='center'><span  id='col_$l"."_$i' style='display:block;overflow-x:hidden;height:12px;width:100%;text-align:left'><font size='1'>$field_value</font></span></td>";
			}
		 }
	}
			echo"<td>";
			if(!in_array("nonew",$comportement) && ($u_droits == '' || $u_active == 1 )){
			echo"<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
			}
			echo"</td></tr>\n";
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
				echo"<a href='./?option=$option&part=$part&d=$d&edit=$this_id' class='info'><font size='1'>$im<br>$print_value<span>$entiervalue</span></font></a>";
			echo"</td></tr><tr><td valign='bottom' align='right'><input id='che' type='checkbox' name='sel$this_id'>";
			

						if($u_droits == '' || $u_active == 1 ){
							echo"<a href='./?option=$option&part=$part&d=$d&$setopo[$this_active]=$this_id' class='info'>
							<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'><span>$setopot[$this_active]</span></a> ";
							if(isset($comportement) && !in_array("nonew",$comportement)){
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
			<td colspan='$columns' align='right'><table cellspacing='0' cellpadding='0' border='0'><tr>
			<td><a href='./?option=$option&part=$part&d=$d&amp;edit=$this_idc' class='info'><img src='$style_url/$theme/modif.gif' border='none' alt='éditer'><span>Modifier ce clone</span></a></td>";
				   	for ($i = 0; $i < $columns; $i++) {
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
								$rowlist = mysql_fetch_object($listres);
								$field_value = $rowlist->$fieldoptionprint;							
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
			echo"<td>";
			if(isset($comportement) && !in_array("nonew",$comportement) && ($u_droits == '' || $u_active == 1 )){
			echo"<a href='#' onclick='confsup($this_idc)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
			}
			echo"</td></tr></table>
			</td></tr>";
	   }
		  }
	  }
  
  }
  else{
  	echo"<td colspan='$columns' align='center'><br>ce tableau est vide...<br>
	><a href=\"./?option=$option&part=$part&amp;edit\">ajouter</a>
	<br><br></td></tr>\n";
  }
  echo"</table>\n";
		
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
			for(l=0 ; l<".($l+1)." ; l++){
				for(i=1 ; i<cols.length ; i++){
					c = cols[i];
					if(c!=''){
						wu = colw;
						if(c==$kiwi){
							wu = document.getElementById('col_'+l+'_'+c).innerWidth;
							if(wu>120){
								w=120;
							}
						}
						if(document.getElementById('col_'+l+'_'+c)) document.getElementById('col_'+l+'_'+c).style.width=wu+'px';
					}
				}
			}
				
			 setTimeout('larglist()',20);
		 }
		 larglist();
	</script>";
			}
			 echo"";
 ?>