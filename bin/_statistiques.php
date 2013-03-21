<?php // 44 > Outil de statistiques ;
	$conn = connecte($base, $host, $login, $passe);
	insert('_graphique');
	$res_field = mysql_list_fields($base,$tabledb);
   	$columns = mysql_num_fields($res_field);
	echo"<table cellpadding='10'><tr><td valign='top' style='white-space:nowrap'>
	 <form name='choix_stat' method='post' action='./?option=$option&part=$part&subpart=statistiques&tri'> ";
	$cho='';
	$uni='';
	$uno=1;
	$chcol='';
	$otherok=true;
	for ($i = 1; $i < $columns; $i++) {
		$field_name = mysql_field_name($res_field, $i);
		$field_type = mysql_field_type($res_field, $i);
		$field_act = $field_name;
		if(isset($r_alias[$part][$field_name])){
			$field_act = $r_alias[$part][$field_name];
		}
		//$refiled = substr($field_name,0,strpos($field_name,"_"));	
		$field_name_human=ereg_replace("nochange_","",$field_name);			
		if(substr($field_name_human,0,1) == "_"){
			$field_name_human = substr($field_name_human,1,strlen($field_name_human));
		}
		//elseif(ereg("_",$field_name_human)){
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
		$nameifthefield = ucfirst(ereg_replace(">"," ",$field_name_human));
			echo"<br>- ";
		if(isset($_GET["tri"]) && isset($_POST["tri_$field_name"])){
			if($otherok==true){
				echo"<input type='checkbox' name='tri_$field_name' value='1' checked onclick='document.choix_stat.submit()'> <b>$nameifthefield</b> ";
			}
			else{
				echo"<input type='checkbox' disabled='1'> $nameifthefield ";	
			}
			$pluss=': ';
			$headcho = "`$field_name`,";
			if(substr($field_act,strlen($field_act)-3,strlen($field_act))=='_ch' || substr($field_act,strlen($field_act)-5,strlen($field_act))=='_nlch'){
				$otherok=false;
				$cho = $nameifthefield;
				$uni = $headcho;				
				$chcol = "<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?option=$option&part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">".ucfirst($field_name)."</a> </td>";
				$uno++;
			}
			elseif(($field_type=='date' || $field_type=='datetime') && $otherok==true){
				$c_a = '';
				$c_m = '';
				$c_j = '';
				$headcho = "";
				if(isset($_POST["tri_a_$field_name"])){
				 $c_a = 'checked';
				 $cho .= ' '.$nameifthefield." année";
				 $uni .= " year(`$field_name`),";
				 $chcol .= "<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?option=$option&part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">$field_name : année</a></td>";
				 $uno++;
				}
				if(isset($_POST["tri_m_$field_name"])){
				 $c_m = 'checked';
				 $cho .= ' '.$nameifthefield." mois";
				 $uni.= " month(`$field_name`),";
				 $chcol .= "<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?option=$option&part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">$field_name : mois</a></td>";
				 $uno++;
				}
				if(isset($_POST["tri_j_$field_name"])){
				 $c_j = 'checked';
				 $cho .= ' '.$nameifthefield." jour";
				 $uni.= " day(`$field_name`),";
				 $chcol .= "<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?option=$option&part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">$field_name : jour</a></td>";
				 $uno++;
				}
				if($c_a == '' &&	$c_m == '' && $c_j==''){
				 $c_a = 'checked';
				 $cho .= ' '.$nameifthefield." année";
				 $uni .= " year(`$field_name`),";
				 $chcol .= "<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?option=$option&part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">$field_name : année</a></td>";
				 $uno++;
				}
				
				echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='tri_a_$field_name' value='1'  onclick='document.choix_stat.submit()' $c_a> année ";
				echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='tri_m_$field_name' value='1'  onclick='document.choix_stat.submit()' $c_m> mois ";
				echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='tri_j_$field_name' value='1'  onclick='document.choix_stat.submit()' $c_j> jour ";
			}
			else{	
				$c='';
				if(isset($_POST["sub_$field_name"]) && is_numeric($_POST["sub_$field_name"]) && $_POST["sub_$field_name"]>0  && $otherok==true){
				 $c = $_POST["sub_$field_name"];
				 $cho .= ' '.$nameifthefield." ($c premiers caractères)";
				 $uni.= " SUBSTR(`$field_name`,1,$c),";
				 $chcol .= "<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?option=$option&part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">$field_name : ($c premiers caractères)</a></td>";
				 $uno++;
				}
				else{
					$cho .= ' '.$nameifthefield;
					$uni .= $headcho;	
					$chcol .= "<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?option=$option&part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">".ucfirst($field_name)."</a> </td>";
					$uno++;
					
				}
				if($otherok==true){
					echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; limiter aux <input type='text' name='sub_$field_name' value='$c'  onchange='document.choix_stat.submit()' size='3'> premier carcatères";
				}
				else{
					echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; limiter aux <input type='text' name='sub_$field_name' value='$c'  onchange='document.choix_stat.submit()' size='3' disabled='1'> premier carcatères";
				}
			}
		}
		else{
			if($otherok==true){
				echo"<input type='checkbox' name='tri_$field_name' value='1' onclick='document.choix_stat.submit()'> $nameifthefield ";	
			}
			else{
				echo"<input type='checkbox' disabled='1'> $nameifthefield ";	
			}
		}
	}
	$uni = substr($uni,0,strlen($uni)-1);
	echo"</td><td valign='top'>
			<br>
		";
		if($uni!=''){
			if($otherok==true){
				$nbg=0;
				$vrb='';
				$tot_res = mysql_query("SELECT `id` FROM `$tabledb`");			$tot_reg = mysql_num_rows($tot_res);
				//`,COUNT(`$uni`)
				if(!isset($_GET['ordre'])){
					$ordre = $uno;
				}
				else{
					$ordre = abs($_GET['ordre']);
					if($ordre==0){
						$ordre = $uno;
					}
				}
				$ent_res = mysql_query("SELECT DISTINCT$uni,COUNT(1) FROM `$tabledb` GROUP BY $uni ORDER BY $ordre DESC");
				$ent_num = mysql_num_rows($ent_res);
				echo"
				- <b>$tot_reg</b> enregistrements<br>
				- <b>$ent_num</b> groupe(s) de <b>$cho</b><br>
				
				<br><br>
				<table cellpadding='0' cellspacing='0'><tr><td valign='top' class='cadrebas'>
				<table cellpadding='5' cellspacing='0'>
				<tr>
					$chcol
					<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?option=$option&part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">Proportions</a></td></tr>
				";
				while($ro = mysql_fetch_array($ent_res,MYSQL_NUM)){
					$ent_val='';
					
					$print_cols='';
					for($r=0; $r<$uno-1 ; $r++){
						$ent_val .= ' '.$ro[$r];
						$print_cols .= "<td align='left' valign='middle'style='border-style:solid;border-width:0px;padding-left:1px;border-bottom-width:1px;border-bottom-color:#333333;border-right-style:dashed;border-right-width:1px'>&nbsp;$ro[$r]</td>";
					}
					
					$nbm = $ro[$uno-1];
					
					$ent_reg = $ro[$uno-1];
					$field_value = $ent_val;
					$field_name = $uni;
					if(ereg("_",$field_name) && substr($field_name,0,1) != "_" && !ereg("@",$field_name) && substr($field_name,0,9) != "nochange_" && mysql_query("SHOW COLUMNS FROM ".substr($field_name,0,strpos($field_name,'_'))) ){
					
					//field_name_human
						$nameifthefield = substr($field_name,0,strpos($field_name,"_"));
						$fieldoption = substr($field_name,strpos($field_name,"_")+1,strlen($field_name));
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
					}
					else{
						$field_value=strip_tags($field_value);
					}
					$ent_val = $field_value;
					$ent_prc = round($ent_reg/$tot_reg*100);
					if(strlen($ent_val)>40){
						$ent_val = substr($ent_val,0,18).'...'.substr($ent_val,strlen($ent_val)-18,18) ;
					}
					elseif(strlen($ent_val)>21){
						$ent_val = substr($ent_val,0,18).'...';
					}
					$moins = 100-$ent_prc;
					$peti_val = strtoupper(substr($ent_val,0,2));
					if($peti_val==''){
						$peti_val='-';
					}
					if($ent_prc>0){
						$nbg++;				
						$vrb.="&c$nbg=".urlencode($ent_val)."&p$nbg=".($ent_prc*360/100);
					}
					$ent_prci = $ent_prc;		
					echo"<tr>
					$print_cols
					<td valign='bottom' align='left'  style='width:100px;border-style:solid;border-width:0px;padding-left:1px;padding-bottom:1px;border-bottom-width:1px;border-bottom-color:#333333;'>
					
					<table><tr><td class='buttontd' valign='middle' style='height:25px;width:$ent_prci;padding:0px;'>
					<div style='width:$ent_prci;height:25px;position:relative;overflow:hidden;font-weight:bold;padding:5px'>$nbm</div>
					</td><td>$ent_prc%</td></tr></table>
					</td></tr>";
				}
				echo"</table>
				</td><td valign='top' style='padding-left:20px'>";
				if(is_file('bin/_graphique.php')){
					echo"<img src='bin/_graphique.php?nb=$nbg$vrb'>";
				}
				echo"</td></tr></table>";
			}
			else{
				$nbg=0;
				$vrb='';
				$arr_tamp=array();
				$ent_res = mysql_query("SELECT $uni,`id` FROM `$tabledb`");
				while($ro = mysql_fetch_array($ent_res)){
					$list_in_item = explode('><',ereg_replace(">[:blank:]<",'><','>'.$ro[0].'<'));
					array_pop($list_in_item);
					array_shift($list_in_item);

					$arr_tamp = array_merge($arr_tamp,$list_in_item);		
				}
				$mkst = array_count_values($arr_tamp);
				reset($mkst);
				arsort ($mkst);
				//$mkst = array_reverse($mkst,true);
				$tot_reg = sizeof($arr_tamp);
				$ent_num = sizeof($mkst);
				echo"
				- <b>$tot_reg</b> enregistrements<br>
				- <b>$ent_num</b> groupe(s) de <b>$cho</b> (Choix multiple par élément)<br>
				
				<br><br>
				<table cellpadding='0' cellspacing='0'><tr><td valign='top' class='cadrebas'>
				<table cellpadding='5' cellspacing='0'>
				<tr>
					$chcol
					<td class='buttontd'><a href='#' onclick=\"document.choix_stat.action='./?part=$part&statistiques&tri&ordre=$uno';document.choix_stat.submit()\">Proportions</a></td></tr>
				";
				foreach($mkst as $k=>$ent_reg){
					$ent_prc = round($ent_reg/$tot_reg*100);
					if($k=='') $k="<i>Vide</i>";
				echo"<tr>
					<td align='left' valign='middle'style='border-style:solid;border-width:0px;padding-left:1px;border-bottom-width:1px;border-bottom-color:#333333;border-right-style:dashed;border-right-width:1px'>$k</td>
					<td valign='bottom' align='left'  style='width:100px;border-style:solid;border-width:0px;padding-left:1px;padding-bottom:1px;border-bottom-width:1px;border-bottom-color:#333333;'>
					
					<table><tr><td class='buttontd' valign='middle' style='height:25px;width:$ent_prci;padding:0px;'>
					<div style='width:$ent_prci;height:25px;position:relative;overflow:hidden;font-weight:bold;padding:5px'>$ent_reg</div>
					</td><td>$ent_prc%</td></tr></table>
					</td></tr>";	
					$nbg++;				
					$vrb.="&c$nbg=".urlencode($k)."&p$nbg=".($ent_prc*360/100);
				}
				echo"</table>
				</td><td valign='top' style='padding-left:20px'>";
				if(is_file('bin/_graphique.php')){
					echo"<img src='bin/_graphique.php?nb=$nbg$vrb'>";
				}
				echo"</td></tr></table>";
			}
		}
		else{
			echo"$cho<br><br>";
		}
		echo"
		</td></tr></table>";
?>