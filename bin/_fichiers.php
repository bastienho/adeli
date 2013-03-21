<?php // 84 > Gestionnaire de fichier pour site ;
if(isset($_GET['edit']) && $_GET['edit']!='' && !isset($_GET['clone'])){
	insert('pel');
echo"
<!--[if lt IE 8]>
<style>
div.fichinfos div.edition{
		display:block;
		position:relative;
}
</style>
<!--<![endif]-->
<div class='buttontd' onclick=\"sizpa('fichspanel')\" style='cursor:pointer'>Fichiers</div>
<div class='cadrebas' id='fichspanel' style='height:1px;padding:0px;overflow-y:hidden' ><table cellspacing='0' cellpadding='2'>";

if(isset($fichiers[$part])){		//////////////////////////////////////////// 			CUSTOM FILES 	
		$custom_files = $fichiers[$part];
		$custom_keys = array_keys($custom_files);
		$i=0;
		while($i<sizeof($custom_keys)){
			$custom_name = $custom_keys[$i];
			$custom_dir = substr($custom_files[$custom_name][0],strpos($custom_files[$custom_name][0],"/"),strlen($custom_files[$custom_name][0]));
			$custom_file = $custom_files[$custom_name][1];
			$custom_accept = '';
			$custom_classment=false;
			if(isset($custom_files[$custom_name][2])){
				$custom_accept='accept="'.$custom_files[$custom_name][2].'"';
			}
			
			if(isset($custom_files[$custom_name][3])){
				$custom_classment=$custom_files[$custom_name][3];
			}
			if(!is_dir("../$custom_dir")){
				if(!mkdir("../$custom_dir",0777)) echo"création de dossier echouée<br>";
			}
			else{
					$ext = strtolower(substr(strrchr($custom_file,"."),1));
					$custom_file = ereg_replace(".$ext","",$custom_file);
					if($ext=="ico"){
						echo"<tr><td valign='top'><b>$custom_name</b> <span class='petitext'>(jpg, png, gif, swf)</span></td><td valign='top'>";
						for($ic=0; $ic<sizeof($imacool) ; $ic++){
						//echo"$custom_dir.$custom_file.$imacool[$ic]";
							if(is_file("../".$custom_dir.$custom_file.".".$imacool[$ic])){
								
								if( in_array("picto",$opt) && is_file("bin/_picto.php")){
								$edition="";
									$edition="<img src='$style_url/$theme/modif.gif' alt='éditer avec picto' border='none' onclick=\"javascript: open('bin/_picto.php?fichier=$custom_dir$custom_file.$imacool[$ic]','picto','width=650,height=500,resizable=1')\">";
								}
								$siz = getimagesize("../$custom_dir$custom_file.$imacool[$ic]");
								$sizo='';
								if($siz[1] > 75) $sizo=" height='75'";
								if($siz[1] < $siz[0] && $siz[0]> 200) $sizo ="width='200'";
								echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
								<a href='../$custom_dir$custom_file.$imacool[$ic]'  class='vernissage'><img  src='bin/_ima.php?jeveuxW=180&file=../$custom_dir$custom_file.$imacool[$ic]' alt='icone' $sizo></a><br>$edition
			<a href=\"#\" onclick=\"delfile('".urlencode("</$custom_dir$custom_file.$imacool[$ic]")."')\">
								<img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a>
								</td></tr></table>";
								break;
							}
						}
					}
					elseif($ext=="dir"){
						  $allreps++;
						   if(!is_dir("../$custom_dir$custom_file")){
								if(!mkdir("../$custom_dir$custom_file",0777)) echo"création de dossier echouée<br>";
							}
							if(is_dir("../$custom_dir$custom_file")){
							$edition="";
							echo"<tr><td valign='top' colspan='2'><b>$custom_name</b><br>
							
							<table cellspacing='0'>
							<tr><td colspan='2' align='right'><a onclick=\"delfiles(document.fourmis)\"><img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a>
							<input type='hidden' name='dirdel' value='".urlencode("$custom_dir$custom_file/")."'/>
							</td></tr>";
							//$dir = dir("../$custom_dir$custom_file");
							$nbfil=0;
							$totpds=0;
							$nbent=0;
							$nbsup=0;
							$last_pref='';
							$pref_index=0;
							$dir = scandir("../$custom_dir$custom_file");
							foreach($dir as $entry){
								if($entry != "." && $entry != ".."){
									$nbent++;
									$nbsup++;
									$entrynom=$entry;
									if(isset($_GET['delfiles']) && isset($_POST['dirdel']) && isset($_POST['df_'.$nbsup]) && $_POST['df_'.$nbsup]==$entry){
										if( (is_dir("../$custom_dir$custom_file/$entry") && deldir("$custom_dir$custom_file/$entry")) ||(is_file("../$custom_dir$custom_file/$entry") && unlink("../$custom_dir$custom_file/$entry")) ){
											$return.=returnn("$entry a bien &eacutet&eacute supprim&eacute;","009933");
											$nbent--;
										}
										else{
											$return.=returnn("$entry n'a pu &ecirc;tre supprim&eacute;","990000");
										}
										
									}
									if(is_file("../$custom_dir$custom_file/$entry") || is_dir("../$custom_dir$custom_file/$entry")){
										$ent_ext = strtolower(substr(strrchr($entry,"."),1));
										echo"<tr><td valign='top' class='fich_ie_alacon'><div class='fichinfos'>";
										if($part=="clients" && is_file('bin/_transfert.php') &&  isset($menu["worknet"]) && in_array("adeli_messages",$menu["worknet"])){
											echo"<input type='checkbox' name='join_file_$nbfil' value=\"$custom_name/$entry\" class='joinfich'>";																						   
										}
										$cur_pref='';
										$pref_index_t = $pref_index;
										if($pref_index<10) $pref_index_t='0'.$pref_index;
										if($custom_classment==true){
											if(strpos($entry,'-') && is_numeric(substr($entry,0,strpos($entry,'-')))){
												$cur_pref = substr($entry,0,strpos($entry,'-'));
												$folow = substr($entry,strpos($entry,'-')+1);
												if($cur_pref!=$pref_index){
													
													if(rename("../$custom_dir$custom_file/$entry","../$custom_dir$custom_file/$pref_index_t-$folow")){
														$entry = $pref_index_t.'-'.$folow;
														
													}
													
												}
											}
											else{
												if(rename("../$custom_dir$custom_file/$entry","../$custom_dir$custom_file/$pref_index_t-$entry")){
													$entry = $pref_index_t.'-'.$entry;
													
												}
											}
											$folow = substr($entry,strpos($entry,'-')+1);
											$entrynom=$folow;
											if($pref_index>0){
												/*if($pref_index==1){
													$folow = substr($last_pref,strpos($last_pref,'-')+1);
													$pref_next = $pref_index+1;
													if($pref_next<10) $pref_next='0'.$pref_next;
													echo"<a onclick=\"renam('../$custom_dir$custom_file/','".addslashes($last_pref)."','".addslashes($pref_next.'-'.$folow)."')\" class='info'><img src='$style_url/$theme/class_up_off.jpg' alt='^' /><span>Monter</span></a>";	
												}
												else{*/
													$pref_next = abs($pref_index-2);
													if($pref_next==0) $pref_next='000';
													elseif($pref_next<10)$pref_next='0'.$pref_next;
													echo"<a onclick=\"renam('../$custom_dir$custom_file/','".addslashes($entry)."','".addslashes($pref_next.'-'.$folow)."')\" class='info'><img src='$style_url/$theme/class_up_off.jpg' alt='^'/><span>Monter</span></a>";
												//}
											}
											if($pref_index<sizeof($dir)-3){
													$pref_next = $pref_index+2;
													if($pref_next<10) $pref_next='0'.$pref_next;
													
													echo"<a onclick=\"renam('../$custom_dir$custom_file/','".addslashes($entry)."','".addslashes($pref_next.'-'.$folow)."')\" class='info'><img src='$style_url/$theme/class_down_off.jpg' alt='v' /><span>Descendre</span></a>";	
											}
											$last_pref = $entry;
											$pref_index++;
										}
										echo"<a href='../$custom_dir$custom_file/$entry' ";
										if(in_array($ent_ext,$imacool)){
											echo"class='vernissage'";
										}
										echo" target='_blank'>";
										$edition="";
										$nbfil++;
										$pds = filesize("../$custom_dir$custom_file/$entry");
										$totpds+=$pds;
										$exifs='';
										if( in_array($ent_ext,$imacool) ){
											if(sizeof($ishtmlll)>0){
												$exifs.="<p>
												Ins&eacute;rer dans : ";
												for($h=0 ; $h<sizeof($ishtmlll) ; $h++){
													$exifs.="<a onclick=\"putimg('".$ishtmlll[$h][0]."','$custom_dir$custom_file/$entry')\">".$ishtmlll[$h][1]."</a> ";
												}
												$exifs.="</p>";
											}
											if( in_array("picto",$opt) && is_file("bin/_picto.php") ){
												$edition.="<img src='$style_url/$theme/modif.gif' alt='éditer avec picto' border='none' height='20' onclick=\"javascript: open('bin/_picto.php?fichier=$custom_dir$custom_file/$entry','picto','width=650,height=500,resizable=1')\">";
											}
											echo"<img src='bin/_ima.php?jeveuxW=80&file=../$custom_dir$custom_file/$entry' alt='$entry'>  $entry";
											if(function_exists('exif_read_data') && $ent_ext=='jpg' && is_file("bin/pel.php")){
												$tags=array_flip($exif_tags);
												foreach($tags as $k=>$v){
													$tags[$k]='';
												}
												$exif = exif_read_data("../$custom_dir$custom_file/$entry",'FILE,COMMENT,EXIF',true);
												
												foreach($exif as $k=>$v){
													foreach($v as $ik=>$iv){
														if(is_array($iv)) $iv = implode(' ',$iv);
														$iv = urldecode(str_replace('%00','',urlencode($iv)));
														if(isset($tags[$ik]) && strlen($iv)>strlen($tags[$ik] )) $tags[$ik] = $iv;
													}
												}
												
												$exifs.="";
												foreach($tags as $k=>$v){
													$exifs.="<p>$k&nbsp;:&nbsp;<input type='text' name='adeli_exif_$nbfil_$k' value=\"$v\"/></p>";
													// onchange=\"exifchange('../../$custom_dir$custom_file/$entry','$k',this.value)\" 
												}
												$exifs.="";
											}
										}
										elseif(is_dir("../$custom_dir$custom_file/$entry")){
											echo"<img src='http://www.adeli.wac.fr/icos/dir.gif' alt='Dossier'> $entrynom";
										}
										else{
											if(sizeof($ishtmlll)>0){
												$exifs.="<p>
												Ins&eacute;rer dans : ";
												for($h=0 ; $h<sizeof($ishtmlll) ; $h++){
													$exifs.="<a onclick=\"crea_link('".$ishtmlll[$h][0]."','$custom_dir$custom_file/$entry','blank')\">".$ishtmlll[$h][1]."</a> ";
												}
												$exifs.="</p>";
											}
											echo"<img src='http://www.adeli.wac.fr/icos/$ent_ext.gif' alt='$ent_ext'> $entrynom";
										}
										echo"</a>
										<div class='edition'> $edition 										
										<a href='#' onclick=\"renam('../$custom_dir$custom_file/','".addslashes($entry)."')\"><img src=\"$style_url/lalie/renomer.png\" alt=\"renomer\" border=\"none\" height=\"16\"></a>
										</div>
										<div class='exif'>".ponderal($pds)." $exifs</div>
										</div>
										
				</td>
				<td align='right'><input type='checkbox' name='df_$nbent' value=\"$entry\" class='multidel' /></td></tr>";
									}
								}
							}
							}
							echo"</table>
							<br>
							$nbfil fichier(s) - ".ponderal($totpds)."
							";
							
					}
					else{
					 $edition="";
					 		echo"<tr><td valign='top'><b>$custom_name</b> ($ext)</td><td valign='top'>";
							if( is_file("../$custom_dir$custom_file.$ext") ){
								if( in_array($ext,$imacool) && sizeof($ishtmlll)>0){
									$edition.="<br><select onchange=\"putimg(this.value,'$absolu$custom_dir$custom_file.$ext');this.value=0;\">
									<option value='0'>ins&eacute;rer dans</option>";
									for($h=0 ; $h<sizeof($ishtmlll) ; $h++){
										$edition.="<option value='".$ishtmlll[$h][0]."'>".$ishtmlll[$h][1]."</option>";
									}
									$edition.="</select>";
								}											
								if( in_array($ext,$imacool) && in_array("picto",$opt) && is_file("bin/_picto.php") ){
									$edition.="<img src='$style_url/$theme/modif.gif' alt='éditer avec picto' border='none' onclick=\"javascript: open('bin/_picto.php?fichier=$custom_dir$custom_file.$ext','picto','width=650,height=500,resizable=1')\">";
								}
								if( in_array($ext,$imacool)){
									
									$siz = getimagesize("../$custom_dir$custom_file.$ext");
								$sizo='';
								if($siz[1] > 75) $sizo=" height='75'";
								if($siz[1] < $siz[0] && $siz[0]> 200) $sizo ="width='200'";
							echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
							<a href='../$custom_dir$custom_file.$ext'  class='vernissage'><img src='bin/_ima.php?jeveuxW=180&file=../$custom_dir$custom_file.$ext' alt='icone' $sizo></a><br>$edition
							<a href=\"#\" onclick=\"delfile('".urlencode("</$custom_dir$custom_file.$ext")."')\">
							<img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a>
							</td></tr></table>";
								}
								else{
								echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
								<a href='../$custom_dir$custom_file.$ext'  class='vernissage'>
							<img src='bin/_ima.php?jeveuxW=180&file=../$custom_dir$custom_file.$ext' alt='icone' border='none'></a><a href=\"#\" onclick=\"delfile('".urlencode("../$custom_dir$custom_file.$ext")."')\">
							<img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a>
							</td></tr></table>";										
								}
							}
					}
					if($editmode==0){
					echo"<br><input type='file' name='file[]' $custom_accept>";
					}
					echo"</td></tr>";
			}
			$i++;
		}
		echo"<tr><td>";
		if($editmode==0){
		echo"<input type='button' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh';document.fourmis.submit()\" value='charger' class='buttontd'><span id='spa_ico'></span><span id='spa_dir'></span>";
		}
		echo"</td></tr>";
}


					
else{		//////////////////////////////////////////// 			DEFAULT FILES										
	/*			
			if(in_array("ico",$comportement) && $edit==""){
				echo"vous pourrez ajouter une image après un premier enregistrement";			
			}
			$funico="";
			if(in_array("ico",$comportement) && $edit!=""){
				if(!is_dir("../$part")){
					mkdir("../$part",0777);
				} 
				echo"<tr><td valign='top'>image d'aperçu</td><td>";
				$funico = "<font style='font-size:9px'><a href='#' onclick=\\\"addspan('spa_ico','../$part/$edit.ico')\\\">charger un autre fichier</a></font>";
				for($ic=0; $ic<sizeof($imacool) ; $ic++){
					if(file_exists("../$part/$edit.$imacool[$ic]")){
							$edition="";
							if( in_array("picto",$opt) && is_file("bin/_picto.php")){
									$edition="<img src='$style_url/$theme/modif.gif' alt='éditer avec picto' border='none' onclick=\"javascript: open('bin/_picto.php?fichier=$part/$edit.$imacool[$ic]','picto','width=650,height=500,resizable=1')\">";
								}
						echo"<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>
						<a href='../$part/$edit.$imacool[$ic]'  class='vernissage'><img src='../$part/$edit.$imacool[$ic]' alt='icone' height='100'></a><br>$edition
						<a href=\"#\" onclick=\"delfile('".urlencode("</$part/$edit.$imacool[$ic]")."')\">
						<img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a>
						</td></tr></table>";
						break;
					}
				}
				if($editmode==0){
				echo"<br>&nbsp;- <span id='spa_ico'><input type='file' name='file[]' onchange=\"addfile('')\"></span>";
				}
				echo"</td></tr>";
			}
			else{
				$funico="";
				echo"<tr><td></td><td><span id='spa_ico'></span></td></tr>";
			}
			$fundir="";
			/////////////////////// DOSSIER
			if(in_array("dir",$comportement) && $edit!=""){			
				if(!is_dir("../$part/$edit")){
						if(!mkdir("../$part/$edit",0777)){
							echo"erreur de configuration serveur, le dossier ne peut être créé...";
						}
				} 
				$fundir="<font style='font-size:9px'><a href='#' onclick=\\\"addspan('spa_dir','../$part/$edit/')\\\">charger un autre fichier</a></font>";
				echo"<tr><td valign='top'>fichiers</td><td>
				
				<table cellpadding='3' width='200' class='fondmediumlignt'><tr><td align='right'>";
				if(!is_dir("../$part/$edit")){
					if(!mkdir("../$part/$edit",0777)) echo"création de dossier echouée<br>";
				}
				$dir = dir("../$part/$edit");
				while($entry = $dir->read()){
					if($entry != "." && $entry != ".."){
						$ent_ext = strtolower(substr(strrchr($entry,"."),1));
						echo"<div><a href='../$part/$edit/$entry' target='_blank'>";
						$edition="";
						if(in_array($ent_ext,$imacool)){
						
							if( in_array("picto",$opt) && is_file("bin/_picto.php")){
									$edition="<img src='$style_url/$theme/modif.gif' alt='éditer avec picto' border='none' onclick=\"javascript: open('bin/_picto.php?fichier=$part/$edit/$entry','picto','width=650,height=500,resizable=1')\">";
								}
							echo"<a href='../$part/$edit/$entry'  class='vernissage'><img src='../$part/$edit/$entry' width='80' alt='$entry' border='none'></a>";
						}
						else{
							echo"<img src='http://www.adeli.wac.fr/icos/$ent_ext.gif' alt='$ent_ext' border='none'>$entry";
						}
						echo"</a>$edition ";
									if($entry != geturl($entry)){
										echo"<a href='#' class='info' onclick=\"renam('../$custom_dir$custom_file/','".addslashes($entry)."')\"><img name='verifurl$i' src='$style_url/$theme/notok.gif' alt='!' border='none'><span style='left:-200px;top:-30px'>Le nom de ce fichier comporte des caractère non conformes à la norme internet (accents, espaces...), cliquez ici pour le renomer</span></a>";
									}
									echo"
						<a href='#' onclick=\"renam('../$part/$edit/','".addslashes($entry)."')\">
			<img src=\"$style_url/lalie/renomer.png\" alt=\"renomer\" border=\"none\" height=\"16\"></a> <a href=\"#\" onclick=\"delfile('".urlencode("../$part/$edit/$entry")."')\"><img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a></div>";
					}
				}
				echo"
				</td></tr></table>
				<br>&nbsp;- <span id='spa_dir'></span></td></tr>";
			}
			else{
				$fundir="";
				echo"<tr><td></td><td><span id='spa_dir'></span></td></tr>";
			}*/   
	}
	echo"</table></div>";
	
	$ouvert = get_pref("ouvert.$part.fichspanel.conf");
	if($ouvert>5){										
		echo"<script language='javascript'>
		sizpa('fichspanel');
		</script>";
	}		
}
elseif(isset($_GET['clone'])){
	echo"<div class='buttontd' >Fichiers</div>
<div class='cadrebas' id='fichspanel' >
Les fichiers seront dupliqu&eacute;s depuis l'objet #".$_GET['edit']."

</div></div>";	
}
?>