<?php // 28 > Suivi de commande de Gestion ;
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
			 	if($u_droits == '' || $u_active == 1 ){
				 echo"<td>activé</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
				 	oui<input type=\"radio\" name=\"$field_name\" value=\"1\" $actouno[$field_value] onclick=\"document.getElementById('warn').innerHTML=''\">
				 	non<input type=\"radio\" name=\"$field_name\" value=\"0\" $actoudos[$field_value] onclick=\"document.getElementById('warn').innerHTML='<br><font color=FF0000>attention, le statut désactivé empechera votre client de voir sa commande dans son suivi de commandes !</font>'\">
					<span id='warn'></span>
				 </td>";
				}
				else{
				 echo"<td>activé</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>sans droits<input type=\"hidden\" name=\"$field_name\" value=\"0\"></td>";
				}
			 }
				//////////////////////////////////////// CLIENT DE COMMANDE	
				elseif($field_name == "idclient" || $field_name == "client"){
					if($field_value==0){
						echo"<td>client</td><td> inconnu ou de passage</td>";
					}
					else{
						$rec = mysql_query("SELECT `nom` FROM `clients` WHERE `id`=$field_value");
						$roc = mysql_fetch_object($rec);
						$noc = $roc->nom;
						echo"<td>client</td><td><a href='./?option=worknet&clients&edit=$field_value'><img src='$style_url/$theme/modif.gif' border='none' alt='éditer'>$noc</a></td>";
					}
			 }
				//////////////////////////////////////// CONTENU DE COMMANDE				
				elseif(($field_name == "contenu" || $field_name == "content") && ($part=="commandes" || (isset($compta_base) && $part==$compta_base))){
									$tcm=split("<!>",$field_value);
									echo"<td> contenu</td><td><table cellspacing='0'>";
									if(isset($gestion_commande_ord)){
										$t = 0;
										while($t<sizeof($gestion_commande_ord)){
															echo"<td bgcolor='#999999'><b>".$gestion_commande_ord[$t]."</b></td>";
															$t++;
										}
									}
									$s = 1;
									while($s<sizeof($tcm)){
													$ntc = split("<>",$tcm[$s]);
													echo"<tr>";
													$t = 0;
													while($t<sizeof($ntc)){
																		if(is_numeric($ntc[$t]) 
																		&& isset($gestion_commande_refid[$t]) && $gestion_commande_refid[$t]!="" 
																		&& isset($gestion_commande_refnom[$t]) && $gestion_commande_refnom[$t]!=""){
																			$res_sub_cont = mysql_query("SELECT `$gestion_commande_refnom[$t]` FROM `$gestion_commande_refid[$t]` WHERE `id`= '$ntc[$t]'");
																			$row_sub_cont = mysql_fetch_object($res_sub_cont);
																			$ntc[$t] = "<a href='./?option=$option&$gestion_commande_refid[$t]&edit=$ntc[$t]'><img src='$style_url/$theme/modif.gif' border='none' alt='éditer'>".$row_sub_cont->$gestion_commande_refnom[$t]."</a>";
																		}
																		echo"<td style='border-style:solid;border-width:1px;border-color:#999999'>".nl2br($ntc[$t])."</td>";
																		$t++;
													}
													echo"</tr>";
													$s++;
									}
									echo"</table></td>";
				}
				//////////////////////////////////////// ETAT DE COMMANDE				
				elseif($field_name == "mode" && ($part=="commandes" || (isset($compta_base) && $part==$compta_base))){
					echo"<td colspan='2'></td>";
				}
				elseif($field_name == "etat" && ($part=="commandes" || (isset($compta_base) && $part==$compta_base))){
					$statut = $field_value;
					$mode = $ro->mode;
					echo"<td>état</td><td>
					
					<textarea name='mode' style='display:none'>$mode</textarea>
							<table><tr>";
		
		if(!ereg('<>',$mode)){
			$tmode='';
			for($s=0; $s<$statut ; $s++){
				$tmode.='<>';		
			}
			if($mode=='') $mode='#';
			$tmode.=$mode;
			for($s=$statut; $s<sizeof($defstat) ; $s++){
				$tmode.='<>';		
			}
			$mode = $tmode;
		}		
		$cmode = explode('<>',$mode);
		$barre='';
		for($s=0; $s<sizeof($defstat) ; $s++){
				echo"<td >$defstat[$s]<br>
				<input type='text' value=\"$cmode[$s]\" name='etat_$s' onkeyup='expi()'>
				</td>";
				$ch='';
				if($s==$statut) $ch='checked';
				$barre.="<td id='colorstat$s' style='padding:0px;font-size:3px;'><input type='radio' value='$s' name='etat' onclick='expi()' $ch></td>";
		}
		
		echo"	 </tr><tr>$barre</tr></table>
		<script language='javascript'>
		var staco=new Array('".implode("','",$colorstatut)."');
		function expi(){
			ki=0;
			cesta='';
			for(i=0; i<".sizeof($cmode)." ; i++){
				if(eval('document.fourmis.etat_'+i)){
					va = eval('document.fourmis.etat_'+i).value;				
					cesta+=va+'<>';
					if(document.fourmis.etat[i].checked==true){
						ki=i;
					}
				}
			}
			for(i=0 ; i<".sizeof($cmode)." ; i++){
				if(document.getElementById('colorstat'+i)){
					if(i<=ki){
						document.getElementById('colorstat'+i).style.background='#'+staco[i];
					}
					else{
						document.getElementById('colorstat'+i).style.background='#FFFFFF';
					}
				}
			}
			document.fourmis.etat[ki].checked=true;
			document.fourmis.mode.value=cesta;
		}
		expi();
		</script> 
					
					
					</td>";
			 }
			 //////////////////////////////////////// EXPEDITION			
				elseif($field_name == "expedition" && ($part=="commandes" || (isset($compta_base) && $part==$compta_base))){
					echo"<td>informations d'expédition</td><td>
					<input type='text' name='expedition' onblur=\"if(this.value!=''){ isex=confirm('Vous avez indiqué une information relative à l\\'expedition\\nvoulez vous passer l\\'état de la commande sur expédié ?');  if(isex) document.fourmis.etat.value=3; }\"></td>";
			 }
				/////////////////////////////////////// COULEUR
			elseif($field_name == "couleur" && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";
					}
			 	echo"<td>couleur</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
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
				echo"<td>$nameifthefield</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:200px\" readonly>
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
					echo"<td>$nameifthefield <a class='info'><img src='$style_url/$theme/pile.gif'>
					<span>Ce champ est à un élément personnel de session <b>$nameofoption</b></span></a></td><td>
					 <img src='$style_url/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:300px\" readonly>
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
					echo"<td>$nameifthefield <a class='info'><img src='$style_url/$theme/pile.gif'>
					<span>Ce champ est relié au tableau <b>$refiled</b></span></a></td><td>
					 <img src='$style_url/$theme/mysqltype-special.png' alt='special'><select name=\"$field_name\" style=\"width:300px\">
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
			/////////////////////////////////////// DEFAULT COMMANDE
			 elseif($part=="commandes" || (isset($compta_base) && $part==$compta_base)){
				 echo"<td>$nameifthefield</td><td>$field_value</td>";
			 }
			 /////////////////////////////////////// STRING
			 elseif($field_type == "string"){			 	  
				 echo"<td><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td><img src='$style_url/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:field_width px\" maxlength=\"$field_length\"></td>";
			 }
			 /////////////////////////////////////// INT
			 elseif($field_type == "int" || $field_type == "real"){			 	  
				 echo"<td><a class='info'>$nameifthefield<span>Nombre</span></a></td><td><img src='$style_url/$theme/mysqltype-int.png' alt='numérique'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:150px\" maxlength=\"$field_length\"></td>";
			 }
			 /////////////////////////////////////// DATE
			 elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
			 	if($field_value==""){
					$field_value=$defaultvalue[$field_type];
				}
				 echo"<td><a class='info'>$nameifthefield<span>Date au format standard<br>date: aaaa-mm-jj<br>heure: hh:mm:ss</span></a></td><td>
				 <img src='$style_url/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"javascript:document.getElementById('menu_date').style.visibility='visible';cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=fourmis.$field_name&amp;date='+document.fourmis.$field_name.value+'&amp;type=$field_type'\"><input type='text' name=\"$field_name\" value=\"$field_value\" maxlength=\"$field_length\">
				 </td>";
			 }

			 /////////////////////////////////////// DEFAULT
			 else{
				 echo"<td><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td><img src='$style_url/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:300px\" maxlength=\"$field_length\"></td>";
			 }
			 echo"</tr>
			 <tr><td colspan='2'><img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>";
	   }
?>