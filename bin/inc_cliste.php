<?php // 72 > Compta, liste ;


			if(isset($_GET['pp'])){
				set_pref("pp.$part.conf",$_GET['pp']);
			}
			$pp = abs(get_pref("pp.$part.conf"));			
			if($pp==0)$pp=50;
			if(!isset($_SESSION['pa'])){
				$pa=1;$_SESSION['pa']=$pa;
			}
			if(isset($_GET['pa'])){
				$pa=abs($_GET['pa']);$_SESSION['pa']=$pa;
			}
			echo"afficher <input type='text' style='width:30px;font-size:10px' value='$pp' onchange=\"affichload;document.location='./?option=$option&part=$part&pp='+this.value\"> enregistrements par page";
			$result = mysql_query("SELECT `id` FROM `$compta_base` $wheredb");
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
			
			$setopo = array("setvalid","unsetvalid");
						$setopot = array("activer cet élément","désactiver cet élément");
						
		$allcols=array();	
		
		if($al==="l" || !isset($al)){
	echo"
	<table width='90%' cellspacing='1' cellpadding='0' border='0' bgcolor='#EEEEEE'><tr class='buttontd'><td width='20'></td>";
   //$res_field = mysql_list_fields($base,$compta_base);
   //$columns = mysql_num_fields($res_field);
   $columns = array('code','numero','date','montant','intitule','client','etat','adresse','content','acompte','acomptele','mode','active');
   $coltype = array('char','int','date','float','char','char','int','blob','blob','float','acomptele','int','int');
   $coltnom = array('code','n°','date','montant','intitulé','client','état','adresse','contenu','acompte','date ac.','mode','active');
   if($part=='bdl'){
	   $columns = array('code','numero','date','montant','intitule','client','adresse','content','acompte','acomptele','mode');
	   $coltype = array('char','int','date','float','char','char','blob','blob','float','acomptele','int');
	   $coltnom = array('code','n°','date','montant','intitulé','client','adresse','contenu','acompte','date ac.','mode');
   }
   $nbcols = sizeof($columns);
   for ($i = 0; $i < $nbcols; $i++) {
		$field_name = $columns[$i];
		array_push($allcols,"'$field_name'");
		array_push($allcols,"'$field_name'DESC");
			$cu = "off";
			$du = "off";
			$tds='';
			if($order == "`$field_name`"){
				$cu = "on";
				$tds="class='menuselected'";
			}
			if($order == "`field_name`DESC"){
				$du = "on";
				$tds="class='menuselected'";
			}
			echo"<td align='center' $tds nowrap='nowrap'><font size='1'><b>$coltnom[$i]&nbsp;</b><a href=\"./?option=$option&part=$part&amp;order=`$field_name`\" title='$field_name décroissant'><img src='$style_url/$theme/class_up_$cu.jpg' alt='^' border='none'></a><a href=\"./?option=$option&part=$part&amp;order=`$field_name`DESC\" title='$field_name croissant'><img src='$style_url/$theme/class_down_$du.jpg' alt='^' border='none'></a></font></td>";
   }
			
   $bgtd == '1';
 
  echo"<td width='20'></td></tr>";
		} 
	$pl = ($pa-1)*$pp;
	if($pl<0){
	$pl=0;
	}
	if(!in_array($order,$allcols)){
				$order='`id` DESC';
			}
	//echo"\\n<!-- SELECT * FROM `$compta_base` $wheredb ORDER BY $order LIMIT $pl,$pp -->";
	 $result = mysql_query("SELECT * FROM `$compta_base` $wheredb ORDER BY $order LIMIT $pl,$pp");
  if(mysql_num_rows($result) > 0){
  
						
	  while ($row = mysql_fetch_object($result)) {
	   $this_id= $row->id;
				
				/////////////////////////////////////////AFFICHAGE LISTE
	   if($bgtd == '1'){
		$bgtd='2';
		echo"<tr class='listone' ondblclick=\"javascript:document.location='./?option=$option&$part&amp;edit=$this_id&getcontent'\">";
	   }
	   else{
		$bgtd='1';
		echo"<tr class='listtwo' ondblclick=\"javascript:document.location='./?option=$option&part=$part&amp;edit=$this_id&getcontent'\">";
	   }
	   echo"<td>";
	    if($part!='bdl'){
			echo"<input id='che' type='checkbox' name='sel$this_id'>
			
			<a href='./?option=$option&part=$part&amp;edit=$this_id&getcontent' class='info'><img src='$style_url/$theme/modif.gif' border='none' alt='éditer'><span>Modifier cet élément</span></a>
			
			<a href='#' onclick=\"javascript:pdf=open('$openpdf&mkpdf=$this_id','pdf','width=400,height=500,scrollbars=1,resizable=1,top='+((screen.height-500)/2)+',left='+((screen.width-400)/2));pdf.focus();\" class='info'><img src='http://www.adeli.wac.fr/icos/pdf.gif' border='none' alt='éditer'><span> $part en PDF</span></a>
			
			<a href='#' onclick=\"javascript:bdl=open('$openpdf&mkbdl=$this_id','bdl','width=400,height=500,scrollbars=1,resizable=1,top='+((screen.height-500)/2)+',left='+((screen.width-400)/2));bdl.focus();\" class='info'><img src='$style_url/images/colis.png' border='none' alt='éditer'><span> Bon de livraison</span></a>";
		}
		else{
			echo"<a href='#' onclick=\"javascript:open('$openpdf&mkbdl=$this_id','pdf','width=400,height=500,scrollbars=1,resizable=1,top='+((screen.height-500)/2)+',left='+((screen.width-400)/2))\" class='info'><img src='http://www.adeli.wac.fr/icos/pdf.gif' border='none' alt='éditer'><span>voir le pdf</span></a>";
		}
	   echo"</td>";
	 for ($i = 0; $i < $nbcols; $i++) {
			$field_name = $columns[$i];
			if($field_name != "clon"  && $field_name != "type"){
			$field_type = $coltype[$i];
			$field_value = strip_tags($row->$field_name);
			$align='left';
			$wii='100px';
				if($field_type == "date"){
					$wii='100%';
				 	if($field_value == "0000-00-00"){
						$field_value="-";
					}
					else{
						$field_value = date("d/m/Y",strtotime($field_value));	
					}
				}
				if(strlen($field_value) > 40){     
					$field_value = substr($field_value,0,37)."...";     
				}
				if($field_name == "contenu" || $field_name == "content"){
					$field_value=substr_count($row->$field_name,'<!>')." articles";
					$wii='100%';
			 	}
				if($field_name == "etat"){
					$statut = $field_value;
					$wii='100%';
					$field_value="<center><table bgcolor='#FFFFFF' cellspacing='1' cellpadding='0'><tr><td style='font-size:9px;color:#$colorstatut[$statut]' valign='top'>$defstat[$statut]</td>";
						for($s=0; $s<=$statut ; $s++){
								$field_value.="<td bgcolor=\"#$colorstatut[$statut]\">.</td>";
						}
						for($s=$statut-1; $s<sizeof($defstat)-2 ; $s++){
								$field_value.="<td bgcolor='#CCCCCC'>.</td>";
						}
						$field_value.="</tr></table></center>";
				 }
				 elseif( $field_name == "client" ){
						$wii='100%';
						$ros = mysql_query("SELECT `nom` FROM `$clients_db` WHERE `id`='$field_value' AND `id`!='0'");
						$rows = mysql_fetch_object($ros);
						$field_value="<a href='./?option=worknet&clients&edit=$field_value'>".substr($rows->nom,0,30)."</a>"; 
		
				}
				if($field_name == "numero" || $field_name == "code"){
					$wii='100%';
				}
				if($field_name == "active"){
					$wii='100%';
					if($u_droits==""){
						$field_value = "<center><a href='./?option=$option&part=$part&$setopo[$field_value]=$this_id' class='info'>
						<img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'><span>$setopot[$field_value]</span></a></center>";
					}
					else{
						$field_value = "<img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'>";
					}
				}
				if($field_type=='float' || $field_type=='real' ){
					$align='right';
					$wii='100%';
				}
				echo"<td align='$align' style='padding:5px'><div style=\"overflow:hidden;height:12px;width:$wii;\"><font size='1'>$field_value</font></div></td>";
			}
			}
			echo"<td>";
			if($part!='bdl'){
				echo"<a href='#' onclick='confsup($this_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
			}
			echo"</td></tr>\n";
			}

  
  }
  else{
  	echo"<td colspan='$nbcols' align='center'><br>ce tableau est vide...<br>
	><a href=\"./?option=$option&part=$part&amp;edit&freecontent\">ajouter</a>
	<br><br></td></tr>\n";
  }
  echo"</table>\n";
 ?>
