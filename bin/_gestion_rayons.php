<?php // 158 > Gestion des rayons de la boutique en ligne ;
$legal_entrys = array(id,ordre,ref,clon,active);

		$verifupdt = mysql_query("DESC `gestion_raytrad`");
		$allchamps = array();
		while($ra = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$ra->Field);
		}
		if(!in_array("url",$allchamps)){
			mysql_query("ALTER TABLE `gestion_raytrad` ADD `url` varchar(255) NOT NULL default ''");
		}
	
		
		if(isset($_GET['delsub'])){
			$del = $_GET['delsub'];
			$verif = mysql_query("SELECT * FROM `$part` WHERE `ref`='$edit' AND `id`='$del'");
			if($verif && mysql_num_rows($verif)==1){
				if(mysql_query("DELETE FROM `$part` WHERE `ref`='$edit' AND `id`='$del'")){
					mysql_query("UPDATE `$articles_db` SET `rayon`='$edit' WHERE `rayon`='$del'");
					$return.=returnn("Sous rayon supprimé avec succès","009900");
				}
				else{
					$return.=returnn("Erreur lors de la suppression de sous rayon","990000");
				}
			}
			else{
				$return.=returnn("Erreur de commande, le sous rayon n'a pas été identifié","FF9900");
			}
		}
	
	
		$art_ref = $ro->ref;
		$art_ordre = abs($ro->ordre);
		$art_active = abs($ro->active);

		$actouno = array("","checked");
		$actoudos = array("checked","");
		
		$rayonchemin="<a href='./?option=$option&part=$part'>Boutique</a> / ";
		
if(!isset($_SESSION['l_a'])){
	$_SESSION['l_a']=0;
}
if(isset($_GET['l_a'])){
	$_SESSION['l_a']=$_GET['l_a'];
}
if($edit=="" && isset($_GET["ref"])){
	$art_ref=$_GET["ref"];
}	
if(!is_dir("../gestion_rayons")){
	mkdir("gestion_rayons",0777);
}
if(isset($_GET['update']) && $_FILES['file']['name'][0] !=''){
	if(addfile("../gestion_rayons/$edit.jpg", $_FILES['file']['name'][0], $_FILES['file']['tmp_name'][0], $dangerous)){
		$return.=returnn("aperçu chargé avec succès","009900");
	}
	else{
		$return.=returnn("aperçu n'a pu être chargé correctement","990000");
	}
}

if(isset($fichiers[$part]) && (isset($_GET['update'])||isset($_GET['refresh']))){		//////////////////////////////////////////// 			CUSTOM FILES	
	$return.=returnn("chargement personnalisé","FF9900");
	$custom_files = $fichiers[$part];
	$custom_keys = array_keys($custom_files);
	$i=0;
	while($i<sizeof($custom_keys)){
		$r=$i+1;
		$custom_name = $custom_keys[$i];
		$custom_dir = $custom_files[$custom_name][0];
		$custom_file = $custom_files[$custom_name][1];
		if($_FILES['file']['name'][$r] !=''){
			if(addfile($custom_dir."/".$custom_file, $_FILES['file']['name'][$r], $_FILES['file']['tmp_name'][$r], $dangerous)){
				$return.=returnn($custom_name." chargé avec succès","009900");
			}
			else{
				$return.=returnn($custom_name." n'a pu être chargé correctement","990000");
			}
		}
		$i++;
	}
}

$depth=0;
get_rayon_parent($art_ref);

$rayonparent_nom=get_item_trans($art_ref,"ray","fr");	




	  echo" <tr><td colspan='3'>";
	  
	  if($u_droits == '' || $u_dgw == 1 ){
			$rayonchemin  = substr($rayonchemin,0, strrpos($rayonchemin ,'/'));
			echo"Rayon parent : 			
			<select name='ref'><option value='0'>Boutique</option>";	
			empil(0,0,$art_ref,$edit);
			echo"</select>
			";
			if($art_ref!=0) echo"> <a href='./?option=$option&part=$part&edit=$art_ref'>Consulter</a>";
			else echo"> <a href='./?option=$option&part=$part'>Consulter</a>";
		}
		else{
			echo $rayonchemin ;
		}
		echo"
	  </td>	  
	  </tr>
	  <tr><td colspan='3'><img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>
	  <tr>
	  <td valign='top' width='100'  valign='top' style='border-width:0px;border-right-width:1px;border-color:#CCCCCC;border-style:solid'>
	  classement
		 <input type=\"texte\" name=\"ordre\" value=\"$art_ordre\" size=\"4\">
	 <br><br><img src='$style_url/$theme/v$art_active.gif' border='none' alt='actif: $art_active'>";
		if($u_droits == '' || $u_active == 1 ){
		 echo"<br>
			<input type=\"radio\" name=\"active\" value=\"1\" $actouno[$art_active]> activé<br>
			<input type=\"radio\" name=\"active\" value=\"0\" $actoudos[$art_active]> désactivé
		 ";
		}
		else{
		 echo"<br>Activé :<br><img src='$style_url/$theme/mysqltype-special.png' alt='special'>sans droits<input type=\"hidden\" name=\"active\" value=\"0\">";
		}
	 echo"
	 </td>
	 <td colspan='2'><b>Traductions</b><br><table>
	 <td>Langue</td><td>Nom</td><td>URL</td><td>Mot clefs</td><td>Description</td>";
	  $ris = mysql_query("SELECT * FROM `$langue_db`");
		if($ris && mysql_num_rows($ris)>0){
			$i=0;
			while($riw=mysql_fetch_object($ris)){
				$lng_code = $riw->code;
				$lng_nom = $riw->nom;
				$i++;
				if((isset($_GET['update']) || isset($_GET['add'])) && isset($_POST["trad_$lng_code"])){
					$lng_nome=str_replace("'","''",$_POST["trad_$lng_code"]);
					$lng_keye=str_replace("'","''",$_POST["tradk_$lng_code"]);
					$lng_dese=str_replace("'","''",$_POST["tradd_$lng_code"]);
					$lng_urle=str_replace("'","''",$_POST["tradu_$lng_code"]);
					if(isset($_GET['update'])){
					//if( ( isset($_GET['update'])|| isset($_GET['add'])) && isset($_POST["trad_$lng_code"]) && is_numeric($edit)){
						if(mysql_query("UPDATE `gestion_raytrad` SET `nom`='$lng_nome',`url`='$lng_urle',`motsclefs`='$lng_keye',`description`='$lng_dese' WHERE `ref`='$edit' AND `lng`='$lng_code'")){
							$return.=returnn("mise à jour de traduction \"$lng_code\" effectuée avec succès","009900");
						}
						else{
							$return.=returnn("mise à jour de traduction \"$lng_code\" échouée","990000");
						}
					}
					elseif(is_numeric($edit)){
						if(mysql_query("INSERT INTO `gestion_raytrad` (`ref`,`lng`,`nom`,`url`,`motsclefs`,`description`)
										VALUES('$edit','$lng_code','$lng_nome','$lng_urle','$lng_keye','$lng_dese')")){
							$return.=returnn("création de traduction \"$lng_code\" effectuée avec succès","009900");
						}
						else{
							$return.=returnn("création de traduction \"$lng_code\" échouée","990000");
						}
					}
				}
				$ras = mysql_query("SELECT * FROM `gestion_raytrad` WHERE `ref`=$edit AND `lng`='$lng_code'");
				if($ras && mysql_num_rows($ras)==1){
					$raw=mysql_fetch_object($ras);
					$lng_val=$raw->nom;
					$lng_url=$raw->url;
					$lng_key=$raw->motsclefs;
					$lng_des=$raw->description;				
				}
				/*elseif($edit!=''){
					if(mysql_query("INSERT INTO `gestion_raytrad` (`ref`,`lng`) VALUES('$edit','$lng_code')")){
						$return.=returnn("création de traduction \"$lng_code\" effectuée avec succès","009900");
					}
					else{
						$return.=returnn("création de traduction \"$lng_code\" échouée","990000");
					}
					$lng_val="";
				}*/
				echo"<tr><td valign='top'><i>$lng_nom</i> : </td>
				<td valign='top'><input type=\"texte\" name=\"trad_$lng_code\" value=\"$lng_val\" size=\"10\"></td>
				<td valign='top'><input type=\"texte\" name=\"tradu_$lng_code\" value=\"$lng_url\" size=\"10\"></td>
				<td valign='top'><input type=\"texte\" name=\"tradk_$lng_code\" value=\"$lng_key\" size=\"20\"></td>
				<td valign='top'><textarea name=\"tradd_$lng_code\" style='width:230px;height:32px' class='editor'>$lng_des</textarea></td>
				</tr>";
				//editor("tradd_$lng_code",$lng_des,$i,'',1,1);
				//<textarea name=\"tradd_$lng_code\" style='width:230px;height:22px' onfocus=\"this.style.height='150px'\" onblur=\"this.style.height='22px'\">$lng_des</textarea>
			}
		}	  
	  echo"</table></td>
	 </tr>
	 <tr><td colspan='3'><img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>	 
	 <tr><td colspan='2' valign='top'>";
	 $art_indb="`rayon`='$edit'";
	 if($edit!="" && $depth < $depth_rayon){
///////////////////////////////////////////////////////: SOUS RAYOIN	 
		 
		 echo"
		 <script language='javascript'>
		 function confsur(id){
				conf = confirm(\"Êtes vous sûr de vouloir supprimer ce rayons ? \\nLes articles seront affectés au rayon parent.\");
				if(conf){
					document.location='./?option=$option&part=$part&edit=$edit&delsub='+id;	
				}
		 }
		 </script>
		 <b>Sous rayons</b><blockquote><table cellpadding='10'>";
		 if(isset($_GET['addchild']) && is_numeric($_GET['addchild'])){
			 if(mysql_query("UPDATE `$rayons_db` SET `ref`='$edit' WHERE `id`='".$_GET['addchild']."'")){
				 $return.=returnn("Le rayon enfant a bien été ajouté","009900");
			 }
			 else{
				 $return.=returnn("Le rayon enfant n'a pu être ajouté","990000");
			 }
		 }
			$ris = mysql_query("SELECT * FROM `$part` WHERE `ref`='$edit'");
			if($ris && mysql_num_rows($ris)>0){
				while($riw=mysql_fetch_object($ris)){
					$sub_id = $riw->id;				
					echo"<tr><td><a href='./?option=$option&part=$part&edit=$sub_id '><b>".get_item_trans($sub_id,"ray","fr")."</b></a></td><td><a href='#ray$sub_id' name='ray$sub_id' onclick='confsur($sub_id)' class='buttontd'>Supprimer</a></td></tr>";
					$art_indb.=" OR `rayon`='$sub_id'";
				}
			}
			echo"</table></blockquote>";		
			echo"<p><a href='./?option=$option&part=$part&edit&ref=$edit' class='buttontd'>Créer un sous-rayon</a><br>
			ou : <select name='gestion_child'><option>Ajouter un sous rayon</option>";	
			empil(0,0,$art_ref,$edit);
			echo"</select><input type='button' onclick=\"document.location='./?option=$option&part=$part&edit=$edit&addchild='+document.fourmis.gestion_child.value\" value='Définir comme enfant'>
			</p>
			
	 ";
	}
	 echo"</td>";
////////////////////////////////////////////////////////IMAGE
echo"<td valign='top'><b>Aperçu</b><br>";
	if($edit!=""){
			if(is_file("../gestion_rayons/$edit.jpg")){
				echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
				<img src='./?incpath=_ima.php&file=gestion_rayons/$edit.jpg' alt='icone' height='100'><br>
				<a href=\"#\" onclick=\"delfile('</gestion_rayons/$edit.jpg')\">
				<img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a>
				</td></tr></table>";
			}
			echo"<input type='file' name='file[]'>";
	}
	else{
		echo"le chargement d'une image sera possible après un premier enregistrement";
	}
	echo"<hr>
			<input type='submit' value='enregistrer'>
			</td>
	</tr>
	 <tr><td colspan='3'><img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>
	 <tr><td colspan='2'>";
	 if($edit!=""){
///////////////////////////////////////////////////////: ARTICLES
		$setopo = array("setvalid","unsetvalid");
		$setopot = array("activer cet élément","désactiver cet élément");
	 echo"
	 <b>Articles de ce rayon</b><br>";
	 	$ris = mysql_query("SELECT `id`,`active`,`nouveaute`,`clon` FROM `$articles_db` WHERE $art_indb");
		if($ris && mysql_num_rows($ris)>0){
			$nb = mysql_num_rows($ris);
	 		if($_SESSION['l_a']==1){
			echo"<a href='./?option=$option&part=$part&edit=$edit&l_a=0'>Masquer les articles</a> ($nb)<br>";			
				while($riw=mysql_fetch_object($ris)){
					if($riw->clon != 0){
						$sub_id = $riw->clon;
						$this_active = get($articles_db,'active',$sub_id);
						$this_focus = get($articles_db,'nouveaute',$sub_id);

					}
					else{
						$sub_id = $riw->id;
						$this_active = $riw->active;
						$this_focus = $riw->nouveaute;
					}
					$ima = "$style_url/$theme/fichier.gif";
					if(is_file("../gestion_articles/$sub_id.jpg")) $ima="../gestion_articles/$sub_id.jpg";  
					echo"<table class='cadre' width='75' cellspacing='0' cellpadding='0' border='0' style='float:left;width:75px;height:75px;margin:2px;'>
		   <tr><td><p align='center'><a href='./?option=$option&part=gestion_articles&edit=$sub_id' style='display:block;width:120px;height:70px;overflow:hidden;'><img src='$ima' alt='$sub_id' height='50' width='50' border='none'><br>".get_item_trans($sub_id,"ar","fr")."</a>
					</p></td></tr>
					<tr><td valign='bottom' align='right'>";
						if($u_droits==""){
							echo"<a href='#' onclick=\"document.fourmis.action+='&$setfoc[$this_focus]=$sub_id&effdb=gestion_articles';document.fourmis.submit()\" class='info'><img src='$style_url/images/star$this_focus.gif' border='none' alt='Focus: $this_focus'><span>$setfoct[$this_focus]</span></a>
							<a href='#' onclick=\"document.fourmis.action+='&$setopo[$this_active]=$sub_id&effdb=gestion_articles';document.fourmis.submit()\" class='info'>
							<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'><span>$setopot[$this_active]</span></a>
							<a href='#' onclick=\"confsup($sub_id,'gestion_articles&edit=$edit')\"><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
						}
						else{
							echo"<img src='$style_url/$theme/v$this_active.gif' border='none' alt='actif: $field_value'>";
						}
					echo"
					
					</td></tr></table>";
				}
			}			
			else{
				echo"<a href='./?option=$option&part=$part&edit=$edit&l_a=1'>Lister les articles</a> ($nb)";
			}
		}
		else{
			echo"<br><center>Aucun article dans ce rayon</center></br>";
		}
	}
	 echo"
	 </td>
	 <td>";
		if(isset($fichiers[$part])){
			
		insert('_fichiers');
		if(is_file('bin/_fichiers.php')){
			include('bin/_fichiers.php');
		}
		else{
			include('$style_url/update.php?file=_fichiers.php&1');
		}
	   }
			
			echo"</td>
	 </tr>
	 <tr><td colspan='3'>
	 <br><a href='./?option=$option&part=gestion_articles&edit&ray=$edit' class='buttontd'>Nouvel article</a>
	 <br><br>
	 <img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>
";
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
			 	echo"<td valign='top'>couleur</td><td valign='top'><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
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
				
				echo"<td valign='top'>$nameifthefield</td><td valign='top'><img src='$style_url/$theme/mysqltype-special.png' alt='special'><input type=\"text\"  id='pref_txt_$i' name=\"$field_name\" value=\"$field_value\" style=\"width:300px;border:none\" class=\"bando\" readonly onfocus=\"this.style.width='1px';document.getElementById('pref_sel_$i').style.display='inline';document.pref_sel_$i.focus();\">
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
					echo"<td valign='top'>$nameifthefield <a class='info'><img src='$style_url/$theme/pile.gif'>
					<span>Ce champ est à un élément personnel de session <b>$nameofoption</b></span></a></td><td valign='top'>
					 <img src='$style_url/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:100px\" class=\"bando\" readonly>
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
					echo"<td valign='top' valign='top'>$nameifthefield <a class='info'><img src='$style_url/$theme/pile.gif'>
					<span>Ce champ est relié au tableau <b>$refiled</b></span></a></td><td valign='top'>
					 <img src='$style_url/$theme/mysqltype-special.png' alt='special'>";
				   if(isset($fieldoptions[2]) && $fieldoptions[2]=='ch'){
					  echo"<input type='hidden' name=\"$field_name\" value=\"$field_value\">
						
				  ";
						$c=0;
						$hot=46;
						$ch=0;
						$prh='';
						$listres = mysql_query("SELECT * FROM `$refiled` ORDER BY `$fieldoptionprint`");
						while($rowlist = mysql_fetch_object($listres)){
							$rowvalue = $rowlist->$fieldoptionprint;
							$rowid = $rowlist->$fieldoption;
							$se = '';
							$c++;
							if(ereg('<'.$rowid.'>',$field_value)){
								$se = 'checked';
								$ch++;
							}
							$hot+=23;
							$prh.="<li><input type='checkbox' name='cho$i$c' onclick=\"oldv=parseInt(document.fourmis.ch_cu_$i.value);if(this.checked==true){if(document.fourmis.$field_name.value.indexOf('<$rowid>')==-1){document.fourmis.$field_name.value+='<$rowid>';oldv++;}}else{document.fourmis.$field_name.value=document.fourmis.$field_name.value.replace('<$rowid>','');oldv--;}document.fourmis.ch_cu_$i.value=oldv;\" $se>$rowvalue</li>";
						}
						if($hot>300) $hot=300;
						echo"<a href='#' onclick=\"dec('ch_$i',$hot)\"><b>v Développer v</b></a>
						<input type='text' name=\"ch_cu_$i\" value=\"$ch\" style='border:none;background:none;width:30px;text-align:right' readonly> / $c sélectionnés<br>
				  		<div id='ch_$i' style='display:block;width:380px;height:$hot;overflow:hidden;'>
						<a href='#' onclick=\"dec('ch_$i',1)\"><b>^ réduire ^</b></a>
						<ul>						
						$prh
						</ul>
						<a href='#' onclick=\"dec('ch_$i',1)\"><b>^ réduire ^</b></a>
						</div>
						<script language='javascript' type='text/javascript'>
						dec('ch_$i',1);
						</script>";					
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
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td valign='top'><img src='$style_url/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:$field_width"."px\" maxlength=\"$field_length\"></td>";
			 }
			 /////////////////////////////////////// INT
			 elseif($field_type == "int" || $field_type == "real"){			 	  
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Nombre</span></a></td><td valign='top'><img src='$style_url/$theme/mysqltype-int.png' alt='numérique'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:150px\" maxlength=\"$field_length\"></td>";
			 }
			 /////////////////////////////////////// DATE
			 elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
			 	if($field_value==""){
					$field_value=$defaultvalue[$field_type];
				}	
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Date au format standard<br>date: aaaa-mm-jj<br>heure: hh:mm:ss</span></a></td><td valign='top'>
				 <img src='$style_url/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"javascript:contextdate(event);cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=fourmis.$field_name&amp;date='+document.fourmis.$field_name.value+'&amp;type=$field_type'\"><input type='text' name=\"$field_name\" value=\"$field_value\" maxlength=\"$field_length\">
				 </td>";
			 }
		echo"</tr>";
	}
	
}	
 echo"</table>";
?>