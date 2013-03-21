<?php // 369 > Bibliothèque de fonctions ;


/******************************                              L I B                               **********************************/
$dangerous = array("php","php3","php4","php5","asp","js","jar","exe","html","htm","xhtml","xml");
$alphabet=array("a","z","e","r","t","y","u","i","o","p","q","s","d","f","g","h","j","k","l","m","w","x","c","v","b","n");
$imacool = array("jpg","jpeg","gif","png","bmp","swf");

$exif_tags = array('Author','Subject','Comments','Title');

/******************************                              M Y S Q L                               **********************************/
//'ImageDescription','DateTimeOriginal',
function sql_open(){
	global	$base, $host, $login, $passe, $conn;
	$conn = mysql_connect($host, $login, $passe);
	mysql_select_db($base);
}
function sql_close(){
	global $conn;
	mysql_close($conn);
}
function getext($str){
	return strtolower(substr(strrchr($str,"."),1));
} 
function getnext($db,$row,$where=''){
	$res = mysql_query("SELECT `$row` FROM `$db` $where ORDER BY `$row`DESC LIMIT 0,1");
	if($res && mysql_num_rows($res)>0){
		$ro = mysql_fetch_object($res);
		return abs($ro->$row)+1;
	}
	return '';
}
function isget($array){
	foreach($array as $k=>$v){
		if(isset($_GET[$v])) return $v;
	}
	return false;
}
function pass_create(){
	global $alphabet;
	return $alphabet[rand(0,26)].$alphabet[rand(0,26)].$alphabet[rand(0,26)].rand(0,9).rand(0,9).rand(0,9);
}
function get($base,$champ,$id,$from='id',$where=''){
	if($where!=''){
		$where=' AND '.$where;
	}
	$rus = mysql_query("SELECT `$champ` FROM `$base` WHERE `$from`='$id' $where");
	if($rus && mysql_num_rows($rus)>0){
		$ro = mysql_fetch_array($rus);
		return $ro[0];
	}
	return false;
}
function get_object($base,$champ,$id,$from='id',$where=''){
	if($where!=''){
		$where=' AND '.$where;
	}
	$rus = mysql_query("SELECT * FROM `$base` WHERE `$from`='$id' $where");
	if($rus && mysql_num_rows($rus)>0){
		$ro = mysql_fetch_object($rus);
		return $ro;
	}
	return false;
}
function get_pref($name,$user=0){
	global $preference_base;
	if($user==0) $user = $_SESSION['u_id'];
	sql_open();
	if(mysql_query("SHOW COLUMNS FROM adeli_preferences")  ){		
		$rus = mysql_query("SELECT `value` FROM `adeli_preferences` WHERE `user`='".$user."' AND `name`='$name'");
		if($rus && mysql_num_rows($rus)>0){
			$ro = mysql_fetch_array($rus);
			return $ro[0];
		}		
	}
	if(is_file("mconfig/$user.$name") && filesize("mconfig/$user.$name")>0){
		if(false!==$fp=@fopen("mconfig/$user.$name","r")){
			fseek($fp,0);
			return fread($fp,filesize("mconfig/$user.$name"));	
		}
	}
	return '';
}
function set_pref($name,$value,$user=0){
	global $preference_base;
	if($user==0) $user = $_SESSION['u_id'];
	sql_open();
	if(mysql_query("SHOW COLUMNS FROM adeli_preferences")  ){			
		$res = mysql_query("SELECT `id` FROM `adeli_preferences` WHERE `user`='".$user."' AND `name`='$name'");
		if($res && mysql_num_rows($res)>0){
			$ro = mysql_fetch_array($res);
			if( mysql_query("UPDATE `adeli_preferences` SET `value`='".str_replace("'","''",$value)."' WHERE `id`='".$ro[0]."'")){
				return true;
			}
		}
		if( mysql_query("INSERT INTO `adeli_preferences` (`user`,`name`,`value`) VALUES('".$user."','$name','".str_replace("'","''",$value)."')")){
			return true;
		}
	}
	if(false!== $fp=fopen("mconfig/$user.$name","w+")){
		if(fwrite($fp,$value)) return true;
	}
	return false;
}
function get_item_trans($id,$db="ray",$lng="fr",$champ="nom"){
	if($id!=0){
		$pluscode="";
		if($lng!="") $pluscode=" AND `lng`='$lng'";
		$ris = mysql_query("SELECT `$champ` FROM `gestion_$db"."trad` WHERE `ref`='$id' AND `$champ`!='' $pluscode LIMIT 0,1");
		if($ris && mysql_num_rows($ris)==1 ){
			$riw=mysql_fetch_object($ris);
			return $riw->$champ;
		}	
		else{
			$ris = mysql_query("SELECT `$champ` FROM `gestion_$db"."trad` WHERE `ref`='$id' AND `$champ`!='' LIMIT 0,1");
			if($ris && mysql_num_rows($ris)==1 ){
				$riw=mysql_fetch_object($ris);
				return $riw->$champ;
			}	
			else{
				return "...";
			}
		}		
	}
	else{
		if($db=="ray") return"boutique";
		if($db=="ar") return"article";
		return "non renseign&eacute;";
	}
}
if(!function_exists('scandir')){
	function scandir($dir){
		if(is_dir($dir)){
			$dh  = opendir($dir);
			$files=array();
			while (false !== ($filename = readdir($dh))) {
				$files[] = $filename;
			}	
			sort($files);	
			rsort($files);	
			return($files);
		}
		return false;
	}	
}

	//////////////////////////////////////////////// insert
function insertintodb($base,$db,$function=''){
   $command = "";
   $res_field = mysql_list_fields($base,$db);
   $columns = mysql_num_fields($res_field);
   for ($i=0 ; $i < $columns; $i++) {
    $field_name = mysql_field_name($res_field, $i);
	$field_value = str_replace("'","''",stripslashes(urldecode($_POST[$field_name])));
	if($function!='' && function_exists($function)){
		$field_value = $function($field_value);
	}
	$command.="'$field_value', ";
   }
   $videcom = str_replace("'","",$command);
   $videcom = trim(str_replace(",","",$videcom));
   //echo"<!-- INSERT INTO `$db` VALUES($command) -->";
   if($videcom != ""){
	   $command =substr($command,0,strlen($command)-2);
	   if(mysql_query("INSERT INTO `$db` VALUES($command)")){
			return mysql_insert_id();
	   }
	   else{ 
	   		return false; 
	   }
   }
   else{
   		return false;
   }
}

//////////////////////////////////////////////// update
function updatedb($base,$db,$id,$sup="",$function=''){
	global $pass_sql_encode;
   $command = "";
   $res_field = mysql_list_fields($base,$db);
   $columns = mysql_num_fields($res_field);
   for ($i=0 ; $i < $columns; $i++) {
    $field_name = mysql_field_name($res_field, $i);
    $field_type = mysql_field_type($res_field, $i);
	if(isset($_POST[$field_name.$sup])){
		$field_value = str_replace("'","''",stripslashes($_POST[$field_name.$sup]));
		if($function!='' && function_exists($function)){
			$field_value = $function($field_value);
		}
		if( ($field_name == "pass" || $field_name == "passe") && isset($pass_sql_encode) && in_array($db,$pass_sql_encode)){
			if($field_value!=''){
				$command.="`$field_name`=PASSWORD('$field_value'), ";
			}
		}
		else{			
			$command.="`$field_name`='$field_value', ";
		}
	}
   }
   $videcom = str_replace("'","",$command);
   $videcom = trim(str_replace(",","",$videcom));
   if($videcom != ""){
	   $command = substr($command,0,strlen($command)-2);
	   if(mysql_query("UPDATE `$db` SET $command WHERE `id`='$id'")){
		  return true;
	   }
	   else{ 
	   		return false; 
	   }
   }
   else{
   		return false;
   }
}

//////////////////////////////////////////////// delete
function deletefromdb($base,$db,$id){
   if(mysql_query("DELETE FROM `$db` WHERE `id`='$id'")){
   		mysql_query("UPDATE `$db` SET `clon`='0' WHERE `clon`='$id'");
			if(is_dir("../$db/$id")){
				deldir("$db/$id");
			}
			$imacool = array("jpg","jpeg","gif","png","bmp","swf");
			for($ic=0; $ic<sizeof($imacool) ; $ic++){
				if(file_exists("../$db/$id.$imacool[$ic]")){
					unlink("../$db/$id.$imacool[$ic]");
				}
			}
			if($db=='gestion_articles'){
					mysql_query("DELETE FROM `gestion_artrad` WHERE `ref`='$id'");
					mysql_query("DELETE FROM `gestion_artstock` WHERE `ref`='$id'");
			}
		return true;
	}
	else{ 
		return false; 
	}
}





/******************************                              F I C H I E R S                          **********************************/
/////////////////////////////////////////////// upload



function addfile($chemin,$dest,$file,$dangerous){	
	$imacool = array("jpg","jpeg","gif","png","bmp","swf");
	$ext = getext($dest);
	$chemin = str_replace("//","/",$chemin);
	$finalext = strtolower(substr(strrchr($chemin,"."),1));
	////////////////////// Icon file
	if($finalext=="ico"){
	
		//$des = strtolower(substr(strrchr($dest,"/"),1));
		echo"<!-- ICO $des -->";
		if(in_array($ext,$imacool) ){
			$others = str_replace(".ico","",$chemin);
			for($ic=0; $ic<sizeof($imacool) ; $ic++){
				if(file_exists("$others.$imacool[$ic]")){
					unlink("$others.$imacool[$ic]");
				}
			}
			echo"<!-- $others.$ext -->";
			if(copy($file,"$others.$ext")){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
		/////////////////////// free file
	elseif(is_dir($chemin) || $finalext=="dir"){
	
		if($finalext=="dir"){
			$chemin = str_replace(".dir","",$chemin);
		}
	echo"<!-- FILE $finalext -->";
		$ex="";	
		
		if(in_array($ext,$dangerous)){
			$ex = ".dng";
		}
		if(copy($file,"$chemin/$dest$ex")){
			return true;
		}
		else{
			return false;
		}
	}
	////////////////////// restreint file
	else{
	echo"<!-- ALONE $chemin > $dest -->";
		$des = strtolower(substr(strrchr($chemin,"/"),1));
		if(strtolower(substr(strrchr($dest,"."),1)) == strtolower(substr(strrchr($des,"."),1)) ){
			if(copy($file,$chemin)){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
}
/////////////////////////////////////////////////////////////////////// RENAME FILE
	if(isset($_GET['ren']) && isset($_GET['nen']) && $_GET['ren']!="" && $_GET['nen']!=""  && !ereg("/",$_GET['nen']) && !ereg("\.\./\.\./",$_GET['ren']) & !ereg("\.\.",$_GET['nen'])){
	$rena = str_replace('//','/',$_GET['ren']);
	$file_ren = substr(strrchr($rena,"/"),1);
	$path_ren = substr($rena,0,strrpos($rena,'/'));
	$nna = correcname($_GET['nen']);
	if(is_file("$path_ren/$file_ren")){
		if(rename("$path_ren/$file_ren","$path_ren/$nna")){
			if(is_file("$path_ren/-$file_ren.mta")) rename("$path_ren/-$file_ren.mta","$path_ren/-$nna.mta");
			$return.=returnn("renommage effectu&eacute;e avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("le renommage a &eacute;chou&eacute;e<br>$file_ren > $nna","990000",$vers,$theme);
		}
	}
	else{
			$return.=returnn("le fichier sp&eacute;cifi&eacute; n'existe pas ($file_ren)","990000",$vers,$theme);
		}

}

$js_url_replace="replace(/[\s]/g,'-').replace(/[,;\`|[)([~\\!']/g,'').toLowerCase().replace('&eacute;','e').replace('à','a').replace('ï','i').replace('ù','u').replace('ô','o')";
$js_www_replace="replace(/[\s]/g,'-').replace(/[,;:\`|[)($[=~&\\?\\/!']/g,'').toLowerCase().replace('&eacute;','e').replace('à','a').replace('ï','i').replace('ù','u').replace('ô','o')";

function delfile($file){	 ///////////////////////////////////////// DEL FILE
	if(substr($file,0,2)=='</'){
		$file = '../'.substr($file,2,strlen($file));
	}
	if(unlink($file)){
		//$des = strtolower(substr(strrchr($file,"/"),1));
		$metafile = '../'.str_replace($des,"-$des.mta",$file);
		if(is_file($metafile)){
			unlink($metafile);
		}
		return true;
	}
	else{
		return false;
	}
}

function deldir($d){   ///////////////////////////////////////////// DEL DIR
	if(!ereg("\.\.",$d) && is_dir("../".$d)){
		$dir = dir("../".$d);

		while($ent = $dir->read()){
			if($ent != "." && $ent != ".."){
				if(filetype("../$d/$ent")=="file"){
						echo"<!-- FICHIER $d/$ent -->\n";
						if(!unlink("../$d/$ent")){
								return false;
						}
						else{
							unlink("../$d/-$ent.mta");
						}
				}
				elseif(filetype("../$d/$ent")=="dir"){
						echo"<!-- DOSSIER $d/$ent -->\n";
						deldir("$d/$ent");
				}
			}
		}
		if(!rmdir("../".$d)){
			return false;
		}
		else{
			$des = strtolower(substr(strrchr($d,"/"),1));
			$metafile = '../'.str_replace($des,"-$des.mta",$d);
			if(is_file($metafile)){
				unlink($metafile);
			}
		}
	}
	else{
		return false;
	}
	return true;
}

function geticon($pathe,$tid,$alt='',$style='',$default=false,$echo=1){   //////////////////////////// GET ICONE
	$imacool = array('jpg','jpeg','gif','png','bmp','swf');
	$imaaa=$src=$default;
	if(basename(dirname($_SERVER['SCRIPT_FILENAME']))=='adeli'){
		$pathe='../'.$pathe;	
	}
	for($ic=0; $ic<sizeof($imacool) ; $ic++){
		if(is_file("$pathe/$tid.$imacool[$ic]")){
			if($alt==''){
				$alt=$tid;
			}
			if($ic==5){
				$imaaa="<embed quality=high scale='noscale' src='$pathe/$tid.$imacool[$ic]' type='application/x-shockwave-flash' pluginspace='http://www.macromedia.com/go/getflashplayer'  $style></embed>";
				
			}
			else{				
				$imaaa="<img src=\"$pathe/$tid.$imacool[$ic]\" alt=\"$alt\" $style/>";	
			}
			$src="$pathe/$tid.$imacool[$ic]";
			break;
		}
	}
	if($echo==1){
		return $imaaa;
	}
	else{
		return $src;	
	}
}

/******************************                              I N T E R F A C E                          **********************************/

//////////////////////////////////////// ENLEVER LES GUILLEMENTS
function unquote($str,$force=''){
	if( $force!='' ){
		$str = str_replace($force.$force,$force,$str);
	}	
	elseif( ( substr($str,0,1)=='"' && substr($str,strlen($str)-1,1)=='"' ) || (substr($str,0,1)=="'" && substr($str,strlen($str)-1,1)=="'" ) ){
		$quo = substr($str,0,1);
		$str = str_replace($quo.$quo,$quo,$str);
		$str = substr($str,1,strlen($str)-2);
	}	
	return $str;		
}
function urluntranslate($url){
	$url = 	str_replace("'","''",$url);
	$csnm = array('z','r','t','p','q','s','d','f','g','h','j','k','l','m','w','x','v','b','n','y');
	$url = str_replace("%2F","/",$url);
	$url = str_replace("_","[-|\.|\'_/& ]+",$url);
	$url = preg_replace('/[eéêèÊÈÉE]/','[eéêèÊÈÉE]+',$url);
	$url = preg_replace('/[aàâÂÀA]/','[aàâÂÀA]+',$url);
	$url = preg_replace('/[uùûÛÙU]/','[uùûÛÙU]+',$url);
	$url = preg_replace('/[oôÔO]/','[oôÔO]+',$url);
	$url = preg_replace('/[iîïÎÏÌI]/','[iîïÎÏÌI]+',$url);
	$url = preg_replace('/[cçC]/','[cçC]+',$url);
	foreach($csnm as $k=>$l){
		$url = str_replace($l,'['.$l.strtoupper($l).']+',$url);
	}
	
	for($i=0 ; $i<9 ; $i++){
		$url = str_replace($i,'('.$i.')+',$url);
	}
	$url = substr($url,0,strlen($url)-1);
	return $url;	
}
function geturl($url){
	if(ereg('://',$url) || ereg('www.',$url)){
		$url = ereg_replace("[ |\|\',;!§$£\\\"][’¨^°~#²}{)( ]",'',$url);
	}
	else{
		$url = ereg_replace("[ |\|\',;!§$£\\\"][’¨^°~#²}{)(=?&: ]",'',$url);
	}
	$url = str_replace(' ','',$url);
	$url = ereg_replace("[e&eacute;êèÊÈ&eacute;]","e",$url);
	$url = ereg_replace("[aàâÂÀ]","a",$url);
	$url = ereg_replace("[uùûÛÙ]","u",$url);
	$url = ereg_replace("[oôÔ]","o",$url);
	$url = ereg_replace("[iîïÎÏÌ]","i",$url);
	return trim(strtolower($url));
}

function html_my_text($txt,$alt="Image",$search=array(),$replace=array()){
	if(!strpos($txt,"</") && !strpos($txt,"/>") && !strpos(strtolower($txt),"<img") && !strpos(strtolower($txt),"<br") ){
		$taxt = split("[ \n]",$txt);
		$toxt="<!-- IMG ".strpos(trim($txt),"<img")." $txt -->";
		for($i=0 ; $i<sizeof($taxt) ; $i++){
		  $tuxt = trim(wordwrap($taxt[$i],40,"-<br>",1));
		  if(ereg('http://',$taxt[$i])){
		   $toxt.= "<a href='".trim($taxt[$i])."' target='_blank'>$tuxt</a> ";
		  }
		  elseif(ereg('www.',$taxt[$i])){
		   $toxt.= "<a href='http://".trim($taxt[$i])."' target='_blank'>$tuxt</a> ";
		  }
		  elseif(ereg('@',$taxt[$i])){
		   $toxt.= "<a href='mailto:".trim($taxt[$i])."'>$tuxt</a> ";
		  }
		  else{
		   $toxt.= $taxt[$i]." ";
		  }
		}
		$txt=nl2br($toxt);
	}
	else{
		$txt = str_replace("<img ","<img alt='$alt'",$txt);
	}
	if(is_array($search) && sizeof($search)>0 && is_array($replace) && sizeof($replace)>0){
		$txt = str_replace($search,$replace,$txt);
	}
	return $txt;
}

//////////////////////////// E D I T O R
if(!isset($edit_font_site)) $edit_font_site = array('Arial','Courier New','System','Tahoma','Times New Roman','Verdana','Webdings');
if(!isset($edit_size_site)) $edit_size_site = array('1','2','3','4','5','H3','H2','H1');

function editor($field_name='texteHTML',$field_value='',$i='',$stylo='',$edition=1, $editchange=1,$menu_include=''){
		global $vers,$prov,$part, $edit_size_site, $edit_font_site, $style_url, $debit,$fichiers,$html_canvas;
		if($edition==1){
			$field_val="<head>";
			if(is_file("../style.css")){
				$field_val.="<link rel='stylesheet' type='text/css' href='http://$prov/style.css'/>";
			}
			if(is_file("../$part/style.css")){
				$field_val.="<link rel='stylesheet' type='text/css' href='http://$prov/$part/style.css'/>";
			}
			$field_value="$field_val
			<style>
			body{ padding:10px; }
			td{ border:#CCC 1px dashed; }
			td:hover{ border:#36C 1px dashed; }
			</style></head><body>$field_value</body>";
		}
		elseif($editchange==1 && $field_value==''){
			$field_value="<body></body>";
		}
		/*echo" <table border='0' cellpadding='0' width='100%' cellspacing='0' style='margin-top:5px'>
			  <tr>
			<td>";
		if($debit==0 && ($edition==1 || $editchange==1) ){
			
			/*
				echo"
				
	  <div id='edit_tools_$i' class='editor_tool'>
			  				<div class='editor_td'>
							
									<img onclick=\"document.getElementById('edit_font_$i').style.display='block';\" src=\"$style_url/images/police.gif\" alt=\"Police\">
									<div id='edit_font_$i' class='buttontd'>";
									foreach($edit_font_site as $fonti){
										echo"<span unselectable='on' onclick=\"editbox_$i.document.execCommand('fontname', false, '$fonti'); editbox.focus();\"  style=\"font-family:$fonti\">$fonti</span><br>";	
									}
									echo"<hr>
										<a onclick=\"document.getElementById('edit_font_$i').style.display='none';\">Fermer</a>
										</div>
							
							</div><div class='editor_td'>
										
										<img onclick=\"document.getElementById('edit_size_$i').style.display='block';\" src=\"$style_url/images/size.gif\" alt=\"Taille\">
										<div id='edit_size_$i' class='buttontd'>
										";
									foreach($edit_size_site as $sizei){
										echo"<span unselectable='on' onClick=\"taille($i,'$sizei',false)\">$sizei</span><br>";	
									}
									echo"<hr>
										<a onclick=\"document.getElementById('edit_size_$i').style.display='none';\">Fermer</a>
										</div>
										
										</div><div class='editor_td'>
										
										<img 
										onclick=\"editbox_$i.document.execCommand('bold', false, null); editbox_$i.focus();\" src=\"$style_url/images/bold.gif\" alt=\"gras\"><img 
										onClick=\"editbox_$i.document.execCommand('italic', false, null); editbox_$i.focus();\" src=\"$style_url/images/italic.gif\" alt=\"italic\"><img 
										onClick=\"editbox_$i.document.execCommand('underline', false, null); editbox_$i.focus();\" src=\"$style_url/images/underline.gif\" alt=\"souligne\"><img 
										onClick=\"editbox_$i.document.execCommand('StrikeThrough', false, null); editbox_$i.focus();\" src=\"$style_url/images/strike.gif\" alt=\"barre\">
										
										</div><div class='editor_td'>
										
										<img onclick=\"document.getElementById('edit_align_$i').style.display='block';\" src=\"$style_url/images/left.gif\" alt=\"alignement\">
										<div id='edit_align_$i' class='buttontd'>
													<span unselectable='on' onClick=\"editbox_$i.document.execCommand('justifyleft', false, null); editbox_$i.focus();\" ><img src=\"$style_url/images/left.gif\" align='absmiddle' alt=\"gauche\">Aligner à gauche</span><br>
													<span unselectable='on' onClick=\"editbox_$i.document.execCommand('justifycenter', false, null); editbox_$i.focus();\"><img  src=\"$style_url/images/center.gif\" align='absmiddle' alt=\"centrer\">Centrer</span><br>
													<span unselectable='on' onClick=\"editbox_$i.document.execCommand('justifyright', false, null); editbox_$i.focus();\" ><img src=\"$style_url/images/right.gif\" align='absmiddle' alt=\"droite\">Aligner à droite</span><hr>
													<span unselectable='on' onClick=\"justify($i,'right');\"><img  src=\"$style_url/images/justify.gif\" align='absmiddle' alt=\"justifier\">Justifier</span><br>
													<span unselectable='on'n  onClick=\"float($i,'right');\"><img  src=\"$style_url/images/floatright.gif\" align='absmiddle' alt=\"flottant à droite\">Flottant à droite</span><br>
													<span unselectable='on'  onClick=\"float($i,'left');\"><img  src=\"$style_url/images/floatleft.gif\" align='absmiddle' alt=\"flottant à gauche\">Flottant à gauche</span><hr>
													<span unselectable='on' onClick=\"editbox_$i.document.execCommand('InsertUnorderedList', false, null); editbox_$i.focus();\"><img  src=\"$style_url/images/list.gif\" align='absmiddle' alt=\"liste\">Liste à puce</span><br>
													<span unselectable='on' onClick=\"editbox_$i.document.execCommand('InsertOrderedList', false, null); editbox_$i.focus();\"><img  src=\"$style_url/images/list.gif\" align='absmiddle' alt=\"liste\">Liste num&eacute;rot&eacute;e</span><br>
													<span unselectable='on' onClick=\"editbox_$i.document.execCommand('Indent', false, null); editbox_$i.focus();\" ><img src=\"$style_url/images/left.gif\" align='absmiddle' alt=\"liste\">Augmenter le retrait</span><br>
													<span unselectable='on' onClick=\"editbox_$i.document.execCommand('Outdent', false, null); editbox_$i.focus();\" ><img src=\"$style_url/images/left.gif\" align='absmiddle'  alt=\"liste\">Diminuer le retrait</span><hr>
													<a unselectable='on' onclick=\"document.getElementById('edit_align_$i').style.display='none';\">Fermer</a>
										</div>
										</div><div class='editor_td'>
										
										<img src='$style_url/images/fgcolor.gif' alt='couleur de texte' onclick=\"choosecolor($i,'ForeColor','$field_name','html',event)\"><img src='$style_url/images/bgcolor.gif' alt='couleur de fond' onclick=\"choosecolor($i,'Backcolor','$field_name','html',event)\">
										</div><div class='editor_td'>
										<img onclick=\"document.getElementById('edit_links_$i').style.display='block';\" src=\"$style_url/images/link.gif\" alt=\"lien\">
										<div id='edit_links_$i' class='buttontd'>
													<span onClick=\"addlink($i)\" unselectable='on'><img src=\"$style_url/images/link.gif\" align='absmiddle' alt=\"lien hypertexte\"> Lien hypertexte</span><br>
													<span  onClick=\"editbox_$i.document.execCommand('Unlink',false,null); editbox_$i.focus();\" unselectable='on'><img src=\"$style_url/images/unlink.gif\" align='absmiddle' alt=\"enlever les liens\"> Supprimer le lien</span><br>
													<span  onClick=\"addancre($i)\" unselectable='on'><img src=\"$style_url/images/ancre.gif\" align='absmiddle' alt=\"ancre\"> Ajouter une ancre</span><br>
													<span onClick=\"addlinkancor($i)\" unselectable='on'><img src=\"$style_url/images/link.gif\" align='absmiddle' alt=\"lien hypertexte\"> Lien vers une ancre</span><br>
													<hr>
													<a onclick=\"document.getElementById('edit_links_$i').style.display='none';\">Fermer</a>
										</div>
										</div><div class='editor_td'><img 
										onClick=\"editbox_$i.document.execCommand('InsertHorizontalRule', false, null); editbox_$i.focus();\" src=\"$style_url/images/line.gif\" alt=\"ligne\">
										<img  
										onClick=\"sautdeligne($i)\" src=\"$style_url/images/br.gif\" alt=\"saut de ligne\">
										<img   onClick=\"document.getElementById('tableau_$i').style.display='block';\" src=\"$style_url/images/table.gif\" alt=\"tableau\">		
										<div id='tableau_$i' class='buttontd'>
												<b>Ins&eacute;rer un tableau</b><hr>
												  <table>
												  <tr><td>Colones : </td><td><input type='text' value='1' onkeyup='javascript:tab_x_$i=this.value' size='2'></td></tr>
												  <tr><td>Lignes : </td><td><input type='text' value='1' onkeyup='javascript:tab_y_$i=this.value' size='2'></td></tr>
												  <tr><td>Contour : </td><td><input type='text' value='1' onkeyup='javascript:tab_c_$i=this.value' size='2'>px</td></tr>
												 <tr><td colspan='2'>Cellules</td></tr>
												 <tr><td>Espacement : </td><td><input type='text' value='0' onkeyup='javascript:tab_s_$i=this.value' size='2'>px</td></tr>
												  <tr><td>marge int&eacute;rieure : </td><td><input type='text' value='3' onkeyup='javascript:tab_p_$i=this.value' size='2'>px</td></tr>
												  <tr><td>Fond : </td><td><span id='tab_cou_$i' style='display:block;padding:5px' onclick=\"choosecolor($i,'table','$field_name','table',event)\">choisir fond</span></td></tr>
												  <tr><td colspan='2' align='right'>
												  <input type='button' value='annuler' class='buttontd' onclick=\"document.getElementById('tableau_$i').style.display='none';\">
												  <input type='button' value='ins&eacute;rer' class='buttontd' onclick=\"tableau($i,tab_x_$i,tab_y_$i,tab_b_$i,tab_c_$i,tab_s_$i,tab_p_$i);document.getElementById('tableau_$i').style.display='none';\">
												  </td></tr>
												  </table>
										</div>
												
		 								 </div><div class='editor_td'>
										 <img onclick=\"document.getElementById('edit_edition_$i').style.display='block';\" src=\"$style_url/images/code.gif\" alt=\"code\">
										<div id='edit_edition_$i' class='buttontd'>
													 <a onClick=\"addcode($i)\"><img src=\"$style_url/images/code.gif\" align='absmiddle' alt=\"<html>\">ins&eacute;rer du code</a><br>
													 <a  onclick='fenp_$i()'><img src=\"$style_url/images/code.gif\" align='absmiddle' alt=\"-> <-\">Fenêtre r&eacute;duite</a><br>
													 <a  onclick='feng_$i()'><img src=\"$style_url/images/code.gif\" align='absmiddle' alt=\"<- ->\">Fenêtre adapt&eacute;e</a><br>
													 <a onclick=\"srcsty = document.getElementById('htmlsrc$i').style;if(srcsty.display=='none'){srcsty.height='200px';srcsty.display='block';document.fourmis.$field_name.focus;}else{srcsty.height='5px';srcsty.display='none';}\"><img src=\"$style_url/images/code.gif\" align='absmiddle' alt=\"<source>\">Source</a><br>
													<a onClick=\"format($i)\"><img src=\"$style_url/images/eraz.gif\" align='absmiddle' alt=\"formater le texte\">Formater</a>
														 <hr>
														<a onclick=\"document.getElementById('edit_edition_$i').style.display='none';\">Fermer</a>
										</div>									 
										 </div>
										 $menu_include
										 <div class='editor_td'>";
		 if($editchange==1){
			 echo"<a onclick='editionmode_$i(0)'>Texte simple</a>";
		 }
		 if($edition==0 && isset($fichiers[$part])){
			 echo" <a onclick='document.fourmis.submit()' class='grosbouton'>Enregistrez pour ins&eacute;rer des images</a>";
		 }
		echo"
										 
										 </div>
										 
			</div>";
		}
		 /*if($debit==0 && $editchange==1){
			 echo"
			 <table cellpadding='3' cellspacing='0' border='0' bgcolor='FFFFFF' id='edit_simp_$i' style='display:none'>
					<tr valign=\"middle\" class='buttontd'> 
					  <td valign='top'>
					  <a onclick='editionmode_$i(1)'>Utiliser la mise en forme</a>
					  </td>
				  </tr>
			</table>";
		 }
		echo"
		  
		  <span id='context_$i'></span>
		</td>
	  </tr>
	  <tr>
		<td>
		 ";*/
		 if($debit==0 && ($edition==1 || $editchange==1) ){
			 $ifrw = 0;
			  $ifrh = 0;
			 if(isset($html_canvas[$part][$field_name])){
				 $ifrw = abs($html_canvas[$part][$field_name][0]);
				 $ifrh = abs($html_canvas[$part][$field_name][1]);
			 }
			 if($ifrw==0) $ifrw='100%';
			 else $ifrw = $ifrw.'px';
			 if($ifrh==0) $ifrh='350px';
			 else $ifrh = $ifrh.'px';
			/*  
		  echo"<textarea name=\"$field_name\" id=\"htmlsrc$i\"  onfocus='editionde$i=true' onblur='editionde$i=false'>$field_value</textarea>
		  <iframe src='about:blank' name='editbox_$i' id='editb_$i' style='height:$ifrh;width:$ifrw;display:block'></iframe>";*/
		  echo" <a href='#htmlsrc$i' title='Editeur' onclick='toogleEditorMode(\"htmlsrc$i\",this);'><b>Editeur</b> / Source</a>
		  <textarea name=\"$field_name\" style=\"width:$ifrw;height:100px;display:block;margin-top:5px;margin-left:0px;\" id=\"htmlsrc$i\" class='editor'>$field_value</textarea>";
		 }
		 else{
			 $ifrw = 0;
			  $ifrh = 0;
			 if(isset($html_canvas[$part][$field_name])){
				 $ifrw = abs($html_canvas[$part][$field_name][0]);
				 $ifrh = abs($html_canvas[$part][$field_name][1]);
			 }
			 if($ifrw==0) $ifrw='450px';
			 else $ifrw = $ifrw.'px';
			 if($ifrh==0) $ifrh='350px';
			 else $ifrh = $ifrh.'px';
		  /*echo"<textarea name=\"$field_name\" id=\"htmlsrc$i\"  onfocus=\"editionde$i=true;this.style.height='$ifrh'\" onblur=\"editionde$i=false;this.style.height='100px';\">$field_value</textarea>";*/ echo"<textarea name=\"$field_name\"style=\"width:$ifrw;height:100px;display:block;margin-top:5px;\" id=\"htmlsrc$i\">$field_value</textarea>";
		 }
		 //</td>	  </tr>	</table> 
		/*echo"			 
<script language=\"JavaScript\">
	tab_x_$i = 1;
	tab_y_$i = 1;
	tab_c_$i = 1;
	tab_s_$i = 0;
	tab_p_$i = 3;
	tab_b_$i = '';
	var isg$i=false;
	var ise$i=$edition;
	editionde$i=false;
	";
	   if($edition==1 || $editchange==1 ){
		echo"
	editbox_$i.document.designMode = \"On\";
	//editbox_$i.document.body.contentEditable=\"true\";
	editbox_$i.document.write(document.fourmis.$field_name.value);
	//editbox_$i.document.body.innerHTML=;	
	
	function save_$i(){
		if(ise$i==1){
		   if(editionde$i==false){
				document.fourmis.$field_name.value=editbox_$i.document.body.innerHTML;
		   }
		   else{
		  	  editbox_$i.document.body.innerHTML=document.fourmis.$field_name.value;
		   }		   
		   if(isg$i){
				feng_$i();
		   }
			document.getElementById('edit_tools_$i').style.display='block';
			document.getElementById('editb_$i').style.display='block';";
			if($editchange==1){
			echo"document.getElementById('edit_simp_$i').style.display='none';";
			}
			echo"
		}
		else{
			document.getElementById('edit_tools_$i').style.display='none';
			document.getElementById('editb_$i').style.display='none';";
			if($editchange==1){
			echo"document.getElementById('edit_simp_$i').style.display='block';";
			}
			echo"document.getElementById('htmlsrc$i').style.display='block';
			document.getElementById('htmlsrc$i').style.height='250px';
			if(document.fourmis.$field_name.value=='<body></body>'){
				document.fourmis.$field_name.value='';	
			}
		}
		setTimeout('save_$i()',10);
	}
	function feng_$i(){
	   var Hu;
		if(document.all) Hu=parseInt(editbox_$i.document.body.scrollHeight)+50;
		else Hu = parseInt(editbox_$i.document.height);
		//Hu+=50;
		if(Hu<250) Hu=250;
		document.getElementById('editb_$i').style.height=Hu+'px';
		isg$i=true;
	}
	function fenp_$i(){
	   document.getElementById('editb_$i').style.height='250px';
	   isg$i=false;
	}
	function editionmode_$i(mode){		
		
		if(mode==1){
			document.getElementById('htmlsrc$i').style.display='none';
			document.getElementById('htmlsrc$i').style.height='1px';
			editbox_$i.document.body.innerHTML=str_replace('\\n','<br>',document.fourmis.$field_name.value);
			ise$i=mode;		
		}
		else{
			if(confirm(\"Etes vous sûr de vouloir passer en mode simple ?\\nVous aller perdre toute mise en forme\")){
				document.fourmis.$field_name.value=strip_tags(editbox_$i.document.body.innerHTML);
				ise$i=mode;	
			}
		}
	}
	

	setTimeout('save_$i()',1000);";
	   }
	echo"</script>";*/
		 
}
///////////////////////////////////////// PRIX
function prix($num=0){
	return(number_format($num,2,',',' '));
}
///////////////////////////////////////// NOM CORRECTE
function correcname($nom){
	$nom = str_replace(" ","-",$nom);
	$nom = str_replace("&","-",$nom);
	$nom = str_replace(">","-",$nom);
	$nom = ereg_replace("[&eacute;êè]","e",$nom);
	$nom = ereg_replace("[àâ]","a",$nom);
	$nom = ereg_replace("[ùû]","u",$nom);
	$nom = ereg_replace("[ô]","o",$nom);
	$nom = ereg_replace("[îï]","i",$nom);
	$nom = str_replace("'","-",$nom);
	if($nom == "bin" || $nom == "mconfig" || $nom == "tmp"){ 
		$nom.="_dossier";
	}
	if(substr($nom,0,1) == "-"){ 
		$nom=substr($nom,1);
	}
	if(substr($nom,0,1) == "."){ 
		$nom=substr($nom,1);
	}
	//strtolower
	return ($nom);
}
/////////////////////////////////////////////// retour
function returnn($return,$color){
	return "<div style='background:#$color'>$return</div>";
}
/////////////////////////////////////////// POIDS
function ponderal($pds){
	if($pds >1000000000){
							$pds/=1000000000;
							$pds=round($pds,2);
							$pds.="</b> Go";
	}
	if($pds >1000000){
							$pds/=1000000;
							$pds=round($pds,2);
							$pds.="</b> Mo";
	}
	else{
		$pds/=1000;
		$pds=round($pds,2);
		$pds.="</b> Ko";
	}
	return $pds;
}

/////////////////////////////////////////// COLOR PICKER
function colorpicker($field_name,$field_value,$actiona,$left=-40,$titre="choisir couleur",$taille=20,$menu=true){
$rvb=array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
if($menu){
	$rvb=array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
	if(preg_match("!MSIE [0-6]\.[0-9]+!i",getenv("HTTP_USER_AGENT"))){
	//if(preg_match("!Firefox+!i",getenv("HTTP_USER_AGENT"))){
		$ret="<a href=\"#\" style=\"display:block;float:right\">$titre <font size='1' color='#EEEEEE'>IE6</font><span>
			<table class=\"buttontd\"><tr><td valign=\"top\">
			<div id=\"divo$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:70px;width:50px;border-style:solid;border-color:#000000;border-width:1px\"></div><br>
			<input type='text' id=\"pickercode\" size='6' value='$field_value'/>
			</td><td valign=\"top\">
			<table cellspacing=\"0\" style=\"border-style:solid;border-color:#000000;border-width:1px\"><tr>";
	}
	else{
		$ret="<a href=\"#\" class=\"info\">$titre<span >
			<table class=\"buttontd\" style=\"position:absolute;top:-20px;left:".$left."px;\"><tr><td valign=\"top\">
			<div id=\"divo$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:70px;width:50px;border-style:solid;border-color:#000000;border-width:1px\"></div><br>
			<input type='text' id=\"pickercode\" size='6' value='$field_value'/>
			</td><td valign=\"top\">
			<table cellspacing=\"0\" style=\"border-style:solid;border-color:#000000;border-width:1px\"><tr>";
	}
}
else{
	//$action = str_replace('COLOR','NONE',$actiona);
	$ret="<table class=\"buttontd\" style=\"position:absolute;top:0px;left:0px;\"><tr><td valign=\"top\">
								<div id=\"divo$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:70px;width:50px;border-style:solid;border-color:#000000;border-width:1px\"></div><br>
								<input type='text' id=\"pickercode\" size='6' value='$field_value'/><br>
								
								</td><td valign=\"top\" align=\"right\">
								<a style=\"cursor:pointer\" onclick=\"document.getElementById('menu_color').style.visibility='hidden'\" class=\"buttontd\" >x</a>
								<br>
								<div id='color_ajax'>
								<table cellspacing=\"0\" cellpadding='0' style=\"border-style:solid;border-color:#000000;border-width:1px\">
		
		";
}
///////////////////////////////////CLAIR
		for($a=15 ; $a>0 ; $a-=2){			
				$r=15;$v=0;$b=0;
				$ret.="<tr>";
				for($m=0 ; $m<6 ; $m++){
				 /////////////////////COULEUR
					if($m==1 || $m==4){$c = "v";}
					if($m==0 || $m==3){$c = "b";}
					if($m==2 || $m==5){$c = "r";}
					/////////////////////SENS
					if($m==0 || $m==2 || $m==4){$s = 1;}
					else{$s = -1;}
						/////////////////////// GO
					for($i=15 ; $i>0 ; $i--){		
					 if($r<$a){$r=$a;}if($v<$a){$v=$a;}if($b<$a){$b=$a;}
						if($r>15){$r=15;}if($v>15){$v=15;}if($b>15){$b=15;}
						$rc_r=$rvb[$r];$rc_v=$rvb[$v];$rc_b=$rvb[$b];
						$r+=$s;$v+=$s;$b+=$s;
						$$c -= $s;
						$color="$rc_r$rc_r$rc_v$rc_v$rc_b$rc_b";
						$action = str_replace("COLOR",$color,$actiona);
						$ret.="<td style=\"background-color:#$color;font-size:".$taille."px\"><a href='#a$field_name' onclick=\"$action\"
										onmouseover=\"document.getElementById('divo$field_name').style.backgroundColor='$color';document.getElementById('pickercode').value='$color';\">&nbsp;</a></td>";	
					}
				}		
				$moy = $rvb[($r+$v+$b)/3	];	
				$color="$moy$moy$moy$moy$moy$moy";
				$action = str_replace("COLOR",$color,$actiona);
				$ret.="<td style=\"background-color:#$color;font-size:".$taille."px\"><a   href='#a$field_name' onclick=\"$action\"
										onmouseover=\"document.getElementById('divo$field_name').style.backgroundColor='$color';document.getElementById('pickercode').value='$color';\">&nbsp;</a></td></tr>";			
			}
			///////////////////////////////////SOMBRE
		for($a=15 ; $a>0 ; $a-=2){			
				$r=15;$v=0;$b=0;
				$ret.="<tr>";
				for($m=0 ; $m<6 ; $m++){
				 /////////////////////COULEUR
					if($m==1 || $m==4){$c = "r";}
					if($m==0 || $m==3){$c = "v";}
					if($m==2 || $m==5){$c = "b";}
					/////////////////////SENS
					if($m==0 || $m==2 || $m==4){$s = 1;}
					else{$s = -1;}
						/////////////////////// GO
					for($i=0 ; $i<15 ; $i++){		
					 if($r>$a){$r=$a;}if($v>$a){$v=$a;}if($b>$a){$b=$a;}
						if($r<0){$r=0;}if($v<0){$v=0;}if($b<0){$b=0;}
						$rc_r=$rvb[$r];$rc_v=$rvb[$v];$rc_b=$rvb[$b];
						$$c += $s;
						$color="$rc_r$rc_r$rc_v$rc_v$rc_b$rc_b";
						$action = str_replace("COLOR",$color,$actiona);
						$ret.="<td style=\"background-color:#$color;font-size:".$taille."px\"><a href='#a$field_name' onclick=\"$action\"
										onmouseover=\"document.getElementById('divo$field_name').style.backgroundColor='$color';document.getElementById('pickercode').innerHTML='$color';\">&nbsp;</a></td>";	
					}
				}				
				$moy = $rvb[($r+$v+$b)/3	];	
				$color="$moy$moy$moy$moy$moy$moy";
				$action = str_replace("COLOR",$color,$actiona);
				$ret.="<td style=\"background-color:#$color;font-size:".$taille."px\"><a  href='#a$field_name' onclick=\"$action\"
										onmouseover=\"document.getElementById('divo$field_name').style.backgroundColor='$color';document.getElementById('pickercode').innerHTML='$color';\">&nbsp;</a></td></tr>";		
			}			

							$ret.="</table></div></td></tr></table>";
				if($menu){
							$ret.="</span></a>";
				}
							return($ret);
}

/****************************************************************************************************************** 
							                            R S S                             ********************************************************************************************************************/
class RSSreader {
    function RSSreader ($aa) {
        foreach ($aa as $k=>$v)
            $this->$k = $aa[$k];
    }
}

function readDatabase($filename) {
    $data = implode("",file($filename));
    $parser = xml_parser_create();
    xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
    xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
    xml_parse_into_struct($parser,$data,$values,$tags);
    xml_parser_free($parser);
    foreach ($tags as $key=>$val) {
        if (strtolower($key) == "item") {
            $molranges = $val;
            for ($i=0; $i < count($molranges); $i+=2) {
                $offset = $molranges[$i] + 1;
                $len = $molranges[$i + 1] - $offset;
                $tdb[] = parseMol(array_slice($values, $offset, $len));
            }
        } else {
            continue;
        }
    }
    return $tdb;
}

function parseMol($mvalues) {
    for ($i=0; $i < count($mvalues); $i++)
        if(isset($mvalues[$i]["value"])) $mol[$mvalues[$i]["tag"]] = strip_tags(utf8_decode($mvalues[$i]["value"]));
    return new RSSreader($mol);
}

function getrss($file,$num=3,$wi=0){
	$imacool = array("jpg","jpeg","gif","png","bmp");
	$ext = strtolower(substr(strrchr($file,"."),1));
	$db = readDatabase($file);
	if($num==0){
		$num = sizeof($db);
	}
	if(sizeof($db)>0){
		for($i=0 ; $i<$num ; $i++){
			$title = $db[$i]->title;
			if($wi > 0 && strlen($db[$i]->title)>$wi){
				$title = substr($db[$i]->title,0,$wi-3).'...';
			}
			echo"- <a href='".$db[$i]->link."' target='_blank' class='info'>$title
			<span style='left:-7px;width:300px;text-decoration:none;'><b>".$db[$i]->title."</b><br>".date("d/m/y - H:i",strtotime($db[$i]->pubDate))."<br>".nl2br($db[$i]->description)."</span>
			</a><br>";
		}
	}
	elseif(in_array($ext,$imacool)){
		echo"<img src='$file' alt='$file'>";
	}
	else{
		include($file);
	}
}

function get_date($date){
	global $NomDuMois,$NomDuJour;
	if($date==NULL) $date=time();
	if($date=="0000-00-00" || $date==strtotime("0000-00-00")){
		return "non renseign&eacute;";
	}
	elseif(date("ymd")==date("ymd",$date)){
		return date("H:i",$date);
	}
	elseif(date("ymd",$date)== date("ymd",strtotime("+1 day"))){
		return "demain, ".date("H:i",$date);
	}
	elseif(date("ymd",$date)== date("ymd",strtotime("-1 day"))){
		return "hier, ".date("H:i",$date);
	}
	else{
		return $NomDuJour[date("w",$date)].' '.date("d ",$date).$NomDuMois[date("n",$date)].date(" Y, H:i",$date);
	}
}
function parse_int($urle=array('','','','',''),$limite=3){
	global $menu_site,$site_menupart;
	if(sizeof($urle)==4){
		$lim="";
		if($limite>0){
			$lim = "LIMIT 0,$limite";
		}
		$s2 = $urle[2];
		if(strpos($urle[2],' DESC') > -1){
			$s2 = trim(substr($urle[2],0,strpos($urle[2],' ')));
		}
		$sepa='site';
			
		for($m=0; $m<sizeof($menu_site) ; $m++){
			$spart = $site_menupart[$m];
			$tablo = $menu_site[$spart];
			if(in_array($urle[0],$tablo)){							
				if(substr($spart,0,7)=='worknet') $sepa='worknet';
				if(substr($spart,0,7)=='gestion') $sepa='gestion';	
				break;
			}
		}
		$ordure = str_replace("`","",str_replace("'","",str_replace(" ","",str_replace("DESC","",$urle[2]))));
		$rus = mysql_query("SELECT `id`,`$urle[1]`,`$s2`,`$ordure` FROM `$urle[0]` WHERE $urle[3] ORDER BY $urle[2] $lim");
		$ty=mysql_field_type($rus, 3);
		$lasid='';
		$no = date('Ymd');
		if($rus && mysql_num_rows($rus)>0){
			while($ru = mysql_fetch_array($rus)){
				$rid = $ru[0];
				$rn = $ru[1];
				$ror = $ru[2];
				$ore = $ru[3];
				$col='555555';
				if($ty=='date' || $ty=='time' || $ty=='datetime'){
					$tim = strtotime($ore);
					$sd = date('Ymd',$tim);
					if($s2==$ordure) $ror = get_date($tim);
					if(ereg('desc',strtolower($urle[2]))){
						if($tim>strtotime("-1 minute")) $col='00FF00';
						elseif($tim>strtotime("-5 minutes")) $col='00CC33';
						elseif($tim>strtotime("-10 minutes")) $col='FF6600';
						elseif($tim>strtotime("-30 minutes")) $col='FF1100';
						elseif($tim>strtotime("-1 hour")) $col='990000';						
						elseif($tim>strtotime("-1 day")) $col='660000';
						if($lasid>=$no && $sd<=$no && $lasid!=$sd){
							echo'<hr style="margin:1px;border:#999999 0px dashed;border-bottom-width:1px;background:none;">';
						}
					}
					else{
						if($tim<strtotime("-7 days")) $col='FF0000';
						elseif($tim<strtotime("-2 days")) $col='990000';
						elseif($tim<strtotime("-1 days")) $col='660000';
						elseif($tim<time()) $col='330000';
						elseif($tim<strtotime("+1 days")) $col='FF6600';
						elseif($tim<strtotime("+2 days")) $col='FF9900';
						elseif($tim<strtotime("+7 days")) $col='FFCC00';
						elseif($tim<strtotime("+15 days")) $col='997722';
						if($lasid<=$no && $sd>=$no && $lasid!=$sd){
							echo'<hr style="margin:1px;border:#999999 0px dashed;border-bottom-width:1px;background:none;">';
						}
					}					
					$ore = $sd;
				}
				$lasid = $ore;
				echo"-<a href='./?option=$sepa&$urle[0]&edit=$rid' class='info'><font color='#$col'>$rn</font> <span style='width:300px;text-decoration:none;'>$ror</span></a><br>";
			}
		}
	}
}
///////////////////////////////: COMPTA
$defstat = array("attente","valid&eacute;","pr&eacute;par&eacute;","exp&eacute;di&eacute;","annul&eacute;");
$colorstatut = array("999999","009900","00CC00","00FF00","CCCCCC"); 
if(isset($custom_defstat) && is_array($custom_defstat)){
	$defstat = $custom_defstat;
	if(isset($custom_colorstatut) && is_array($custom_colorstatut)){
		$colorstatut = $custom_colorstatut;
	}
	else{
		$colorstatut = array_fill(0,sizeof($custom_defstat),'009999');
	}
}
$defstatl = array("ouvert","en cours","livr&eacute; en partie","livr&eacute;","annul&eacute;");
$colorstatutl = array("0055CC","990000","CC9900","00FF00","CCCCCC"); 
if(isset($custom_defstatl) && is_array($custom_defstatl)){
	$defstatl = $custom_defstatl;
	if(isset($custom_colorstatutl) && is_array($custom_colorstatutl)){
		$colorstatutl = $custom_colorstatutl;
	}
	else{
		$colorstatutl = array_fill(0,sizeof($custom_defstatl),'009999');
	}
}
//////////////////////////////////////////////////////////////MAIL
//
$aType = array (	'323'=>	'text/h323',
								'acx'		=>	'application/internet-property-stream',
								'ai'			=>	'application/postscript',
								'aif'		=>	'audio/x-aiff',
								'aifc'		=>	'audio/x-aiff',
								'aiff'		=>	'audio/x-aiff',
								'asf'		=>	'video/x-ms-asf',
								'asr'		=>	'video/x-ms-asf',
								'asx'		=>	'video/x-ms-asf',
								'au'			=>	'audio/basic',
								'avi'		=>	'video/x-msvideo',
								'axs'		=>	'application/olescript',
								'bas'		=>	'text/plain',
								'bcpio'		=>	'application/x-bcpio',
								'bin'		=>	'application/octet-stream',
								'bmp'		=>	'image/bmp',
								'c'			=>	'text/plain',
								'cat'		=>	'application/vnd.ms-pkiseccat',
								'cdf'		=>	'application/x-cdf',
								'cer'		=>	'application/x-x509-ca-cert',
								'class'		=>	'application/octet-stream',
								'clp'		=>	'application/x-msclip',
								'cmx'		=>	'image/x-cmx',
								'cod'		=>	'image/cis-cod',
								'cpio'		=>	'application/x-cpio',
								'crd'		=>	'application/x-mscardfile',
								'crl'		=>	'application/pkix-crl',
								'crt'		=>	'application/x-x509-ca-cert',
								'csh'		=>	'application/x-csh',
								'css'		=>	'text/css',
								'dcr'		=>	'application/x-director',
								'der'		=>	'application/x-x509-ca-cert',
								'dir'		=>	'application/x-director',
								'dll'		=>	'application/x-msdownload',
								'dms'		=>	'application/octet-stream',
								'doc'		=>	'application/msword',
								'dot'		=>	'application/msword',
								'dvi'		=>	'application/x-dvi',
								'dxr'		=>	'application/x-director',
								'eps'		=>	'application/postscript',
								'etx'		=>	'text/x-setext',
								'evy'		=>	'application/envoy',
								'exe'		=>	'application/octet-stream',
								'fif'		=>	'application/fractals',
								'flr'		=>	'x-world/x-vrml',
								'gif'		=>	'image/gif',
								'gtar'		=>	'application/x-gtar',
								'gz'			=>	'application/x-gzip',
								'h'			=>	'text/plain',
								'hdf'		=>	'application/x-hdf',
								'hlp'		=>	'application/winhlp',
								'hqx'		=>	'application/mac-binhex40',
								'hta'		=>	'application/hta',
								'htc'		=>	'text/x-component',
								'htm'		=>	'text/html',
								'html'		=>	'text/html',
								'htt'		=>	'text/webviewhtml',
								'ico'		=>	'image/x-icon',
								'ief'		=>	'image/ief',
								'iii'		=>	'application/x-iphone',
								'ins'		=>	'application/x-internet-signup',
								'isp'		=>	'application/x-internet-signup',
								'jfif'		=>	'image/pipeg',
								'jpe'		=>	'image/jpeg',
								'jpeg'		=>	'image/jpeg',
								'jpg'		=>	'image/jpeg',
								'js'			=>	'application/x-javascript',
								'latex'		=>	'application/x-latex',
								'lha'		=>	'application/octet-stream',
								'lsf'		=>	'video/x-la-asf',
								'lsx'		=>	'video/x-la-asf',
								'lzh'		=>	'application/octet-stream',
								'm13'		=>	'application/x-msmediaview',
								'm14'		=>	'application/x-msmediaview',
								'm3u'		=>	'audio/x-mpegurl',
								'man'		=>	'application/x-troff-man',
								'mdb'		=>	'application/x-msaccess',
								'me'			=>	'application/x-troff-me',
								'mht'		=>	'message/rfc822',
								'mhtml'		=>	'message/rfc822',
								'mid'		=>	'audio/mid',
								'mny'		=>	'application/x-msmoney',
								'mov'		=>	'video/quicktime',
								'movie'		=>	'video/x-sgi-movie',
								'mp2'		=>	'video/mpeg',
								'mp3'		=>	'audio/mpeg',
								'mpa'		=>	'video/mpeg',
								'mpe'		=>	'video/mpeg',
								'mpeg'		=>	'video/mpeg',
								'mpg'		=>	'video/mpeg',
								'mpp'		=>	'application/vnd.ms-project',
								'mpv2'		=>	'video/mpeg',
								'ms'			=>	'application/x-troff-ms',
								'mvb'		=>	'application/x-msmediaview',
								'nws'		=>	'message/rfc822',
								'oda'		=>	'application/oda',
								'p10'		=>	'application/pkcs10',
								'p12'		=>	'application/x-pkcs12',
								'p7b'		=>	'application/x-pkcs7-certificates',
								'p7c'		=>	'application/x-pkcs7-mime',
								'p7m'		=>	'application/x-pkcs7-mime',
								'p7r'		=>	'application/x-pkcs7-certreqresp',
								'p7s'		=>	'application/x-pkcs7-signature',
								'pbm'		=>	'image/x-portable-bitmap',
								'pdf'		=>	'application/pdf',
								'pfx'		=>	'application/x-pkcs12',
								'pgm'		=>	'image/x-portable-graymap',
								'pko'		=>	'application/ynd.ms-pkipko',
								'pma'		=>	'application/x-perfmon',
								'pmc'		=>	'application/x-perfmon',
								'pml'		=>	'application/x-perfmon',
								'pmr'		=>	'application/x-perfmon',
								'pmw'		=>	'application/x-perfmon',
								'png'		=> 	'image/png',
								'pnm'		=>	'image/x-portable-anymap',
								'pot'		=>	'application/vnd.ms-powerpoint',
								'ppm'		=>	'image/x-portable-pixmap',
								'pps'		=>	'application/vnd.ms-powerpoint',
								'ppt'		=>	'application/vnd.ms-powerpoint',
								'prf'		=>	'application/pics-rules',
								'ps'			=>	'application/postscript',
								'pub'		=>	'application/x-mspublisher',
								'qt'			=>	'video/quicktime',
								'ra'			=>	'audio/x-pn-realaudio',
								'ram'		=>	'audio/x-pn-realaudio',
								'ras'		=>	'image/x-cmu-raster',
								'rgb'		=>	'image/x-rgb',
								'rmi'		=>	'audio/mid',
								'roff'		=>	'application/x-troff',
								'rtf'		=>	'application/rtf',
								'rtx'		=>	'text/richtext',
								'scd'		=>	'application/x-msschedule',
								'sct'		=>	'text/scriptlet',
								'setpay'		=>	'application/set-payment-initiation',
								'setreg'		=>	'application/set-registration-initiation',
								'sh'			=>	'application/x-sh',
								'shar'		=>	'application/x-shar',
								'sit'		=>	'application/x-stuffit',
								'snd'		=>	'audio/basic',
								'spc'		=>	'application/x-pkcs7-certificates',
								'spl'		=>	'application/futuresplash',
								'src'		=>	'application/x-wais-source',
								'sst'		=>	'application/vnd.ms-pkicertstore',
								'stl'		=>	'application/vnd.ms-pkistl',
								'stm'		=>	'text/html',
								'svg'		=>	'image/svg+xml',
								'sv4cpio'		=>	'application/x-sv4cpio',
								'swf'		=>	'application/x-shockwave-flash',
								't'			=>	'application/x-troff',
								'tar'		=>	'application/x-tar',
								'tcl'		=>	'application/x-tcl',
								'tex'		=>	'application/x-tex',
								'texi'		=>	'application/x-texinfo',
								'texinfo'		=>	'application/x-texinfo',
								'tgz'		=>	'application/x-compressed',
								'tif'		=>	'image/tiff',
								'tiff'		=>	'image/tiff',
								'tr'			=>	'application/x-troff',
								'trm'		=>	'application/x-msterminal',
								'tsv'		=>	'text/tab-separated-values',
								'txt'		=>	'text/plain',
								'uls'		=>	'text/iuls',
								'ustar'		=>	'application/x-ustar',
								'vcf'		=>	'text/x-vcard',
								'vrml'		=>	'x-world/x-vrml',
								'wav'		=>	'audio/x-wav',
								'wcm'		=>	'application/vnd.ms-works',
								'wdb'		=>	'application/vnd.ms-works',
								'wks'		=>	'application/vnd.ms-works',
								'wmf'		=>	'application/x-msmetafile',
								'wps'		=>	'application/vnd.ms-works',
								'wri'		=>	'application/x-mswrite',
								'wrl'		=>	'x-world/x-vrml',
								'wrz'		=>	'x-world/x-vrml',
								'xaf'		=>	'x-world/x-vrml',
								'xbm'		=>	'image/x-xbitmap',
								'xla'		=>	'application/vnd.ms-excel',
								'xlc'		=>	'application/vnd.ms-excel',
								'xlm'		=>	'application/vnd.ms-excel',
								'xls'		=>	'application/vnd.ms-excel',
								'xlt'		=>	'application/vnd.ms-excel',
								'xlw'		=>	'application/vnd.ms-excel',
								'xof'		=>	'x-world/x-vrml',
								'xpm'		=>	'image/x-xpixmap',
								'xwd'		=>	'image/x-xwindowdump',
								'z'			=>	'application/x-compress',
								'zip'		=>	'application/zip'
								);

function geti($sKey) {
	global $aType;
	$sExtension = $sKey;		
	if (strpos ($sKey, '.') !== false)
		$sExtension = substr ($sKey, strrpos ($sKey, '.') + 1);

	if (!isset ($aType[$sExtension])){
		return "application/octet-stream";
	}
	return $aType[$sExtension];
}
if (!function_exists ('quoted_printable_encode')) {
	function quoted_printable_encode ($sStr) {
		return str_replace ("%", "=", rawurlencode ($sStr));
	}
}

class SimpleMail {
	/**
	 * @var Array $_aProperties
	 * Contain the property for the email
	 */
	var $_aProperties = array (	'XPriority' 				=> 3,	// To 1 (High) at 5 (Low), 3 is the common
								'Sender' 					=> '', 	// Default is FROM value
								'ReplyTo' 				=> '', 	// Default is FROM value, Reply to this email
								'ReturnPath' 				=> '', 	// Default is FROM value, Mail for delivery failed response
								'From' 					=> '',
								'To' 					=> array (),
								'Cc' 					=> array (),
								'Bcc' 					=> array (),
								'DispositionNotificationTo'	=> '',
								'XMailer' 				=> 'Adeli Mailer',
								'Organisation' 			=> 'Urbancube',
								'Date' 					=> '',
								'MimeVersion' 				=> '1.0',
								'Subject' 				=> '',
								'AbuseContact'				=> '',
								'Charset'					=> 'Utf-8',
								'Bodies' 					=> array (),
								'Attachment'				=> array ()
								);

	/**
	 * @var String $_sBreakLine
	 * Breakline style
	 */
	var $_sBreakLine = "\n";

	public function __construct ($aProperties = null) {
		if (!function_exists ('mail'))
			throw new Exception ('Function "mail" must exists to use this class');

		$this->_aProperties['Date'] = date("D, j M Y H:i:s");

		if (isset ($aProperties)) {
			foreach ($aProperties as $sKey=>$mProperty) {
				$this->__set ($sKey, $mProperty);
			}
		}
	}

	public function __set ($sKey, $mValue) {
		if (is_int ($this->_aProperties[$sKey]) && !is_int ($mValue))
			throw new Exception ('Invalid type, must be an Int');
		else if (is_string ($this->_aProperties[$sKey]) && !is_string ($mValue))
			throw new Exception ('Invalid type, must be an String');
		else if (is_array ($this->_aProperties[$sKey]) && !is_array ($mValue))
			throw new Exception ('Invalid type, must be an String');
		else {
			$this->_aProperties[$sKey] = $mValue;
		}
	}

	public function __geti($sKey) {
		if (!isset ($this->_aProperties[$sKey]))
			throw new Exception ('Invalid key "'.$sKey.'"');

		return $this->_aProperties[$sKey];
	}

	public function addAttachment ($sFilePath, $sType = 'Application/Octet-Stream', $sFileName = NULL, $oCompress = NULL, $sCid = NULL) {
		if (!file_exists ($sFilePath) || !is_readable ($sFilePath))
			throw new Exception ('The file "'.$sFilePath.'" is unreadable.');

		if (!is_string ($sType))
			throw new Exception ('Type must be a String');

		if (!isset ($sFileName))
			$sFileName = substr ($sFilePath, strrpos ($sFilePath, '/'));
		else if (!is_string ($sFileName))
			throw new Exception ('Filename must be a String');

		$sContent = file_get_contents ($sFilePath);

		if (isset ($oCompress))
			$sContent = $oCompress->compress ($sContent);

		$iCountAttachments = count ($this->_aProperties['Attachment']);

		$this->_aProperties['Attachment'][$iCountAttachments]['ContentType'] = $sType;
		$this->_aProperties['Attachment'][$iCountAttachments]['ContentTransfertEncoding'] = 'base64';
		$this->_aProperties['Attachment'][$iCountAttachments]['ContentDisposition'] = (isset ($sCid)) ? 'inline' : 'attachment';
		$this->_aProperties['Attachment'][$iCountAttachments]['Filename'] = $sFileName;

		if (isset ($sCid))
			$this->_aProperties['Attachment'][$iCountAttachments]['Content-ID'] = $sCid;

		$this->_aProperties['Attachment'][$iCountAttachments]['Content'] = chunk_split (base64_encode ($sContent));
	}

	public function addBody ($sBody, $sType = 'text/plain', $sCharset = null) {
		if (!is_string ($sBody))
			throw new Exception ('Body must be a String');

		if (!is_string ($sType))
			throw new Exception ('Type must be a String');
		
		if (!isset ($sCharset))
			$sCharset = $this->_aProperties['Charset'];

		if (!is_string ($sCharset))
			throw new Exception ('Charset must be a String');

		$iCountBodies = count ($this->_aProperties['Bodies']);

		$this->_aProperties['Bodies'][$iCountBodies]['ContentType'] = $sType;
		$this->_aProperties['Bodies'][$iCountBodies]['Charset'] = $sCharset;
		$this->_aProperties['Bodies'][$iCountBodies]['ContentTransfertEncoding'] = 'quoted-printable';
		$this->_aProperties['Bodies'][$iCountBodies]['ContentDisposition'] = 'inline';

		if ($sType == 'text/html')
			$sBody = preg_replace_callback ('#src[\ ]*=[\ ]*["|\']([^"|\']*)["|\']#i', array ($this, 'inlineAttachment'), $sBody);

		//$this->_aProperties['Bodies'][$iCountBodies]['Content'] = quoted_printable_encode (chunk_split ($sBody, 76, $this->_sBreakLine));
		$this->_aProperties['Bodies'][$iCountBodies]['Content'] = quoted_printable_encode ($sBody);
	}
	public function send () {
		if (count ($this->_aProperties['To']) == 0 && count ($this->_aProperties['Cc']) == 0 && count ($this->_aProperties['Bcc']) == 0)
			throw new Exception ('You need to specify at least one recipient (To, Cc or Bcc)');

		$iCountBodies = count ($this->_aProperties['Bodies']);
		$iCountAttachments = count ($this->_aProperties['Attachment']);

		if (($iCountBodies + $iCountAttachments) == 1) { // Qu'un seul truc !
			$sHeaders = $this->_createHeaders ();

			if ($iCountBodies == 1)
				$sHeaders .= $this->_createSection ($this->_aProperties['Bodies'][0]);
			else
				$sHeaders .= $this->_createSection ($this->_aProperties['Attachment'][0]);
			
			$sHeaders .= $this->_sBreakLine.$this->_sBreakLine;

		}
		else if ($iCountBodies == 1 && $iCountAttachments > 0) { // Mixed
			$sBoundary = $this->_boundaryGenerate ();

			$sHeaders = $this->_createHeaders ();

			$sHeaders .= 'Content-Type: multipart/mixed;'.$this->_sBreakLine."\t".'boundary="'.$sBoundary.'"'.$this->_sBreakLine.$this->_sBreakLine;

			$sHeaders .= $this->_createSection ($this->_aProperties['Bodies'][0], $sBoundary);

			foreach ($this->_aProperties['Attachment'] as $aAttachment) {
				$sHeaders .= $this->_createSection ($aAttachment, $sBoundary);
			}

			$sHeaders .= '--'.$sBoundary.'--'.$this->_sBreakLine.$this->_sBreakLine;
		}
		else if ($iCountBodies > 1 && $iCountAttachments == 0) { // Alternative
			$sBoundary = $this->_boundaryGenerate ();

			$sHeaders = $this->_createHeaders ();

			$sHeaders .= 'Content-Type: multipart/alternative;'.$this->_sBreakLine."\t".'boundary="'.$sBoundary.'"'.$this->_sBreakLine.$this->_sBreakLine;

			foreach ($this->_aProperties['Bodies'] as $aBody) {
				$sHeaders .= $this->_createSection ($aBody, $sBoundary);
			}

			$sHeaders .= '--'.$sBoundary.'--'.$this->_sBreakLine.$this->_sBreakLine;
		}
		else if ($iCountBodies > 1 && $iCountAttachments >= 1) { // Mixed + Alternative
			$sMixedBoundary = $this->_boundaryGenerate ();
			$sAlternativeBoundary = $this->_boundaryGenerate ();

			$sHeaders = $this->_createHeaders ();

			$sAlternativeBody = 'Content-Type: multipart/alternative;'.$this->_sBreakLine."\t".'boundary="'.$sAlternativeBoundary.'"'.$this->_sBreakLine.$this->_sBreakLine;

			foreach ($this->_aProperties['Bodies'] as $aBody) {
				$sAlternativeBody .= $this->_createSection ($aBody, $sAlternativeBoundary);
			}

			$sAlternativeBody .= '--'.$sAlternativeBoundary.'--'.$this->_sBreakLine.$this->_sBreakLine;

			$sHeaders .= 'Content-Type: multipart/mixed;'.$this->_sBreakLine."\t".'boundary="'.$sMixedBoundary.'"'.$this->_sBreakLine.$this->_sBreakLine;

			$sHeaders .= '--'.$sMixedBoundary.$this->_sBreakLine;
			$sHeaders .= $sAlternativeBody;

			foreach ($this->_aProperties['Attachment'] as $aAttachment) {
				$sHeaders .= $this->_createSection ($aAttachment, $sMixedBoundary);
			}

			$sHeaders .= '--'.$sMixedBoundary.'--'.$this->_sBreakLine.$this->_sBreakLine;
		}
		else
			throw new Exception ('Invalid Email structure');

		$sSubject = '=?'.$this->_aProperties['Charset'].'?q?'.quoted_printable_encode ($this->_aProperties['Subject']).'?=';
		//
		if (@mail (implode (', ', $this->_aProperties['To']), $sSubject, '', $sHeaders) === false)
			throw new Exception ('An error occured while sending the email');
	}
	public function getMessage () {
		if (count ($this->_aProperties['To']) == 0 && count ($this->_aProperties['Cc']) == 0 && count ($this->_aProperties['Bcc']) == 0)
			throw new Exception ('You need to specify at least one recipient (To, Cc or Bcc)');

		$iCountBodies = count ($this->_aProperties['Bodies']);
		$iCountAttachments = count ($this->_aProperties['Attachment']);

		if (($iCountBodies + $iCountAttachments) == 1) { // Qu'un seul truc !
			$sHeaders = $this->_createHeaders ();

			if ($iCountBodies == 1)
				$sHeaders .= $this->_createSection ($this->_aProperties['Bodies'][0]);
			else
				$sHeaders .= $this->_createSection ($this->_aProperties['Attachment'][0]);
			
			$sHeaders .= $this->_sBreakLine.$this->_sBreakLine;

		}
		else if ($iCountBodies == 1 && $iCountAttachments > 0) { // Mixed
			$sBoundary = $this->_boundaryGenerate ();

			$sHeaders = $this->_createHeaders ();

			$sHeaders .= 'Content-Type: multipart/mixed;'.$this->_sBreakLine."\t".'boundary="'.$sBoundary.'"'.$this->_sBreakLine.$this->_sBreakLine;

			$sHeaders .= $this->_createSection ($this->_aProperties['Bodies'][0], $sBoundary);

			foreach ($this->_aProperties['Attachment'] as $aAttachment) {
				$sHeaders .= $this->_createSection ($aAttachment, $sBoundary);
			}

			$sHeaders .= '--'.$sBoundary.'--'.$this->_sBreakLine.$this->_sBreakLine;
		}
		else if ($iCountBodies > 1 && $iCountAttachments == 0) { // Alternative
			$sBoundary = $this->_boundaryGenerate ();

			$sHeaders = $this->_createHeaders ();

			$sHeaders .= 'Content-Type: multipart/alternative;'.$this->_sBreakLine."\t".'boundary="'.$sBoundary.'"'.$this->_sBreakLine.$this->_sBreakLine;

			foreach ($this->_aProperties['Bodies'] as $aBody) {
				$sHeaders .= $this->_createSection ($aBody, $sBoundary);
			}

			$sHeaders .= '--'.$sBoundary.'--'.$this->_sBreakLine.$this->_sBreakLine;
		}
		else if ($iCountBodies > 1 && $iCountAttachments >= 1) { // Mixed + Alternative
			$sMixedBoundary = $this->_boundaryGenerate ();
			$sAlternativeBoundary = $this->_boundaryGenerate ();

			$sHeaders = $this->_createHeaders ();

			$sAlternativeBody = 'Content-Type: multipart/alternative;'.$this->_sBreakLine."\t".'boundary="'.$sAlternativeBoundary.'"'.$this->_sBreakLine.$this->_sBreakLine;

			foreach ($this->_aProperties['Bodies'] as $aBody) {
				$sAlternativeBody .= $this->_createSection ($aBody, $sAlternativeBoundary);
			}

			$sAlternativeBody .= '--'.$sAlternativeBoundary.'--'.$this->_sBreakLine.$this->_sBreakLine;

			$sHeaders .= 'Content-Type: multipart/mixed;'.$this->_sBreakLine."\t".'boundary="'.$sMixedBoundary.'"'.$this->_sBreakLine.$this->_sBreakLine;

			$sHeaders .= '--'.$sMixedBoundary.$this->_sBreakLine;
			$sHeaders .= $sAlternativeBody;

			foreach ($this->_aProperties['Attachment'] as $aAttachment) {
				$sHeaders .= $this->_createSection ($aAttachment, $sMixedBoundary);
			}

			$sHeaders .= '--'.$sMixedBoundary.'--'.$this->_sBreakLine.$this->_sBreakLine;
		}
		else
			throw new Exception ('Invalid Email structure');

		$sSubject = '=?'.$this->_aProperties['Charset'].'?q?'.quoted_printable_encode ($this->_aProperties['Subject']).'?=';
		
		return 'To: '.implode (', ',$this->_aProperties['To']).$this->_sBreakLine.$sHeaders;
	}
	
	public function inlineAttachment ($aMatches) {
		$sCID = $this->_cidGenerate ();
		try {
			$this->addAttachment ($aMatches[1], geti($aMatches[1]), null, null, $sCID);
			return 'src="cid:'.$sCID.'"';
		}
		catch (Exception $oE) {
			return $aMatches[0];
		}
	}

	private function _createHeaders () {
		if (empty ($this->_aProperties['From']))
			throw new Exception ('From must be set !');

		if (empty ($this->_aProperties['ReplyTo']))
			$this->_aProperties['ReplyTo'] = $this->_aProperties['From'];

		if (empty ($this->_aProperties['ReturnPath']))
			$this->_aProperties['ReturnPath'] = $this->_aProperties['From'];

		if (empty ($this->_aProperties['Sender']))
			$this->_aProperties['Sender'] = $this->_aProperties['From'];

		$sHeaders = 'X-Priority: '.$this->_aProperties['XPriority'].$this->_sBreakLine;
		$sHeaders .= 'X-Mailer: '.$this->_aProperties['XMailer'].$this->_sBreakLine;
		$sHeaders .= 'Organisation: '.$this->_aProperties['Organisation'].$this->_sBreakLine;
		$sHeaders .= 'Date: '.$this->_aProperties['Date'].$this->_sBreakLine;
		$sHeaders .= 'MIME-version: '.$this->_aProperties['MimeVersion'].$this->_sBreakLine;
		$sHeaders .= 'From: '.$this->_aProperties['From'].$this->_sBreakLine;
		//$sHeaders .= 'To: '.implode (', ',$this->_aProperties['To']).$this->_sBreakLine;
		$sHeaders .= 'Reply-To: '.$this->_aProperties['ReplyTo'].$this->_sBreakLine;
		$sHeaders .= 'Return-Path: '.$this->_aProperties['ReturnPath'].$this->_sBreakLine;
		$sHeaders .= 'Sender: '.$this->_aProperties['Sender'].$this->_sBreakLine;
		$sHeaders .= 'X-Sender: '.$this->_aProperties['Sender'].$this->_sBreakLine;
		//$sHeaders .= 'Subject: '.$this->_aProperties['Subject'].$this->_sBreakLine;

		if (!empty ($this->_aProperties['DispositionNotificationTo'])) {
			$sHeaders .= 'Disposition-Notification-To: '.$this->_aProperties['DispositionNotificationTo'].$this->_sBreakLine;
			$sHeaders .= 'X-Confirm-Reading-To: '.$this->_aProperties['DispositionNotificationTo'].$this->_sBreakLine;
			$sHeaders .= 'Return-receipt-to: '.$this->_aProperties['DispositionNotificationTo'].$this->_sBreakLine;
		}

		if (!empty ($this->_aProperties['AbuseContact'])) {
			$sHeaders .= 'X-abuse-contact: '.$this->_aProperties['AbuseContact'].$this->_sBreakLine;
		}
		if (count ($this->_aProperties['Cc']) > 0)
			$sHeaders .= 'Cc:'.implode (',', $this->_aProperties['Cc']).$this->_sBreakLine;

		if (count ($this->_aProperties['Bcc']) > 0)
			$sHeaders .= 'Bcc:'.implode (',', $this->_aProperties['Bcc']).$this->_sBreakLine;

		return $sHeaders;
	}
	private function _createSection ($aElement, $sBoundary = null) {
		$sMessage = '';
		if (isset ($sBoundary))
			$sMessage = '--'.$sBoundary.$this->_sBreakLine;

		$sMessage .= 'Content-Type: '.$aElement['ContentType'];

		if (!empty ($aElement['Charset']))
			$sMessage .= '; charset="'.$aElement['Charset'].'"';
		else if ($aElement['ContentTransfertEncoding'] == 'base64')
			$sMessage .= '; name="'.$aElement['Filename'].'"';

		$sMessage .= $this->_sBreakLine;
		$sMessage .= 'Content-Transfer-Encoding: '.$aElement['ContentTransfertEncoding'].$this->_sBreakLine;
		$sMessage .= 'Content-Disposition: '.$aElement['ContentDisposition'];

		if (!empty ($aElement['Filename']))
			$sMessage .= '; filename="'.$aElement['Filename'].'"';

		$sMessage .= $this->_sBreakLine;

		if (!empty ($aElement['Content-ID']))
			$sMessage .= 'Content-ID: <'.$aElement['Content-ID'].'>'.$this->_sBreakLine;


		$sMessage .= $this->_sBreakLine;
		$sMessage .= $aElement['Content'].$this->_sBreakLine.$this->_sBreakLine;

		return $sMessage;
	}
	private function _boundaryGenerate () {
		return '---=Part_'.md5 (uniqid (mt_rand ()));
	}
	private function _cidGenerate () {
		return 'CID_'.md5 (uniqid (mt_rand ()));
	}
}


////////////////////////////////////////////////////////////////////////////////////////////////// EXIF



?>