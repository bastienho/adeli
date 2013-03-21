<!-- 3 -->
<?php
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
			 	if($u_droits == ""){
				 echo"<td>activé</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
				 	oui<input type=\"radio\" name=\"$field_name\" value=\"1\" $actouno[$field_value]>
				 	non<input type=\"radio\" name=\"$field_name\" value=\"0\" $actoudos[$field_value]>
				 </td>";
				}
				else{
				 echo"<td>activé</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>sans droits<input type=\"hidden\" name=\"$field_name\" value=\"0\"></td>";
				}
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