<?php // 41 > Outils d'édition LaLIE ;
$path = substr(getenv('SCRIPT_NAME'),0,strrpos(getenv('SCRIPT_NAME'),"/"));
include("mconfig/adeli.php");
if(!isset($style_url)) $style_url="http://www.adeli.wac.fr/vers/$vers";
	
if(is_file('bin/inc_func.php') && filesize('bin/inc_func.php')>0 ){
	require('bin/inc_func.php');
}
else{
	require("$style_url/update.php?file=inc_func.php");
}
if(is_file('bin/_tools.php')){
	$opentool='./?incpath=bin/_tools.php&1';
}
else{
	$opentool='$style_url/update.php?file=_tools.php?1';
}

if(isset($_GET['in_html'])){
	$inhtml = $_GET['in_html'];
	$opentool.="&in_html=$inhtml";
}

function correctname($group){
	$group = ereg_replace(" ","_",$group);
	$group = ereg_replace("-","_",$group);
	$group = ereg_replace("sans_groupe","",$group);
	$group = ereg_replace("[*+%:/;§µ><]","_",$group);
	$group = ereg_replace("[!?,]","",$group);
	$group = ereg_replace("'","",$group);
	$group = ereg_replace("[êèéë]","e",$group);
	$group = ereg_replace("[ôö]","o",$group);
	$group = ereg_replace("[âà]","a",$group);
	$group = ereg_replace("[ûüù]","u",$group);
	$group = ereg_replace("[ïî]","i",$group);
	return $group;
}

$num_type=$_GET['type'];
$num_name=$_GET['name'];
$art_name = $_SESSION["dtl".$num_name."_name"];
$art_attribute = $_SESSION["dtl".$num_name."_attribute"];


$vers = $_SESSION["vers"];
$theme = $_SESSION["theme"];

echo"
<html>
<head>
<title>tools $vers</title>
		<link rel='stylesheet' href='$style_url/$theme/style.css' type='text/css'>
		<link rel='icon' href='$style_url/favicon.png' type='image/x-icon'>
		<link rel='shortcut icon' href='$style_url/favicon.png' type='image/x-icon'>
</head>
<body marginwidth='0' marginheight='0' leftmargin='0' rightmargin='0' topmargin='0' bottommargin='0' bgcolor='FFFFFF'>
<div id='toul'>
<form name='toul'>
<table align='center' border='0' cellpadding='0'cellspacing='0' width='100%'>
  <tr height='10' bgcolor='999999'>
    <td align='left' valign='top'><font color='FFFFFF'><b>$art_name</b></font>
	</td>
  </tr>
  ";
if($num_type=='frame'){ //////////////////////////////////////////////// FRAME

	$sizeof = split("x",$art_attribute);
	$sw = $sizeof[0];
	$sh = $sizeof[1];
	echo"
	<tr bgcolor='CCCCCC'><td valign='top' align='left'>
	<img src='$style_url/lalie/tools_content.png'> <font color='FFFFFF'><b>format:</b></font>
	</td></tr>
	<tr height='10' bgcolor='FFFFFF'><td valign='top'>
	
	largeur: <input type='text' name='large' style='width:50px' value='$sw' onchange=\"parent.document.crea.dtl".$num_name."_attribute.value=document.toul.large.value+'x'+document.toul.haute.value;\"> pixels<br>	
	hauteur: <input type='text' name='haute' style='width:50px' value='$sh' onchange=\"parent.document.crea.dtl".$num_name."_attribute.value=document.toul.large.value+'x'+document.toul.haute.value;\"> pixels<br>	
	</td></tr>
	<tr bgcolor='CCCCCC'><td valign='top' align='left'>
	<img src='$style_url/lalie/tools_link.png'> <a class='info'><span style='width:130px;left:-20px'>lien de la page à afficher</span><font color='FFFFFF'><b>action:</b></font></a>
	</td></tr>
	<tr bgcolor='EEEEEE'><td valign='top' align='left'>
	</td></tr>
	<tr height='10' bgcolor='FFFFFF'><td valign='top'>
	
	
	<input type='text' value='' style=\"width:100%\" name='acte' onchange=\"parent.document.crea.dtl".$num_name."_action.value=this.value\">
		<script language='javascript'>
	document.toul.acte.value=parent.document.crea.dtl".$num_name."_action.value;
	</script>
	";
}

if($num_type=='img' || $num_type=='piece' ){	

if($num_type=='img'){	 /////////////////////////: image
echo"<tr><td>
		<a href='#' onclick=\"parent.document.visu_$art_name.src='$style_url/lalie/vide.gif';parent.document.crea.dtl".$num_name."_attribute.value='$style_url/lalie/vide.gif'\">sans image</a>
		</td></tr>
 <tr height='10' bgcolor='CCCCCC'><td valign='top'>
 	<script language='javascript'>
	function putimg(fil){";	
	  if(isset($_GET['in_html'])){
	echo"if (document.all) {
			var oRng = parent.editbox_$inhtml.document.selection.createRange();
			oRng.pasteHTML('<img src=\"'+fil+'\" border=none alt=\"'+fil+'\">');
		}
		else{
			parent.editbox_$inhtml.document.execCommand('insertHTML', false, '<img src=\"'+fil+'\" lowsrc=\"'+fil+'\" border=none alt=\"'+fil+'\">');
		}
		parent.editbox_$inhtml.focus();
		";
	}
	else{
		echo"
		parent.document.visu_$art_name.src=fil;
		parent.document.crea.dtl".$num_name."_attribute.value=fil;	
		";
	}	
	echo"parent.untoulbar();
	}
	</script>
   ";
   if(fopen("http://adeli.wac.fr/vers/$vers/lalie_get_img.php","rb")){
			
			echo"Images prédéfinies</td></tr><tr><td>";
			include("http://adeli.wac.fr/vers/$vers/lalie_get_img.php");
			echo"<tr height='10' bgcolor='CCCCCC'><td valign='top'>&nbsp;";
	}

   echo"
    </td>
  </tr>";
}
///////////////////////////////////////PIECE
if($num_type=='piece'){	
echo"<script language='javascript'>
	function putfil(fil){
		nom = fil.substr(fil.lastIndexOf('/')+1,fil.length);
		if (document.all) {
			var oRng = parent.editbox_$inhtml.document.selection.createRange();
			oRng.pasteHTML('<a href=\"'+fil+'\" target=\"_blank\">'+nom+'</a>');
		}
		else{
			parent.editbox_$inhtml.document.execCommand('insertHTML', false, '<a href=\"'+fil+'\" target=\"_blank\">'+nom+'</a>');
		}
		parent.editbox_$inhtml.focus();		
		parent.untoulbar();
	}
	</script>
   ";
}		
echo" <tr height='10' bgcolor='CCCCCC'><td valign='top'>";
	
if(is_dir("img")){
echo"<a name='fichs'></a>
  Mes fichiers
    <input type='button' onclick=\"javascript:document.location='$opentool&type=$num_type&name=$num_name&import=#fichs'\" value='importer'>
    </td>
  </tr>
  <tr bgcolor='FFFFFF'>
    <td valign='top'>
";
/********************************************************************************************************
                                            ADMIN
********************************************************************************************************/
////////////////////////////////// IMPORT
if(isset($_GET['import'])){
 echo"</form>
 <form action='$opentool&type=$num_type&name=$num_name&upload=#fichs' method='post' enctype='multipart/form-data'>
 ajouter un fichier:<br>
 <input type='file' name='file[]'>
 <input type='button' onclick=\"javascript:document.location='$opentool&type=$num_type&name=$num_name'\" value='annuler'>
 <input type='submit' value='charger'>
 </form>
  ";
}


if($num_type=='piece'){
	 if(!is_dir('pj')){
		mkdir('pj',0777); 
	 }
	if(is_dir('pj') && isset($_SESSION['lalie_id'])){
		if(!is_dir('pj/'.$_SESSION['lalie_id'])){
			mkdir('pj/'.$_SESSION['lalie_id'],0777); 
	 	}
		if(is_dir('pj/'.$_SESSION['lalie_id'])){
			$base_dir = "pj/".$_SESSION['lalie_id'];
	 	}	
	}
	 
	 
}
else{
	$base_dir = 'img';
}
////////////////////////////////// DELETE
if(isset($_GET['delete'])){
 $delete = $_GET['delete'];
  if(unlink("$base_dir/$delete")){
   echo"<font color='009900'>fichier $delete supprimé</font><br>";
  }
  else{
   echo"<font color='FF0000'> suppression du fichier $delete échouée</font><br>";
  }
}
////////////////////////////////// RESIZE
if(isset($_GET['resize'])){
 $resize = $_POST['resize'];
 $newsize = $_POST['newsize'];
 $oldsize = getimagesize("img/$resize");

 $neww = $oldsize[0]*$newsize/100;
 $snewh = $oldsize[1]*$newsize/100;

 echo "<font color='00FF00'>demande de formatage de fichier... $resize $oldsize[0] x $oldsize[1] -> $neww x $snewh</font><br>";
 $des = imagecreatetruecolor ($neww, $snewh);
 if($oldsize[2]==1){ $src = imagecreatefromgif("$base_dir/$resize"); }
 elseif($oldsize[2]==2){ $src = imagecreatefromjpeg("$base_dir/$resize"); }
 elseif($oldsize[2]==3){ $src = imagecreatefrompng("$base_dir/$resize"); }
 else{ echo"<font color='FF0000'>mauvais format d'image</font><br>"; }
  if(imagecopyresampled( $des, $src, 0, 0, 0, 0, $neww, $snewh, $oldsize[0], $oldsize[1])){
   echo "<font color='00FF00'>$resize à bien été formaté à $newsize %</font><br>";
  }
  else{
   echo "<font color='FF0000'>échec lors du formatage de $resize à $newsize %!</font><br>";
  }
  
   if($oldsize[2]==1){ 
			 if(imagegif($des, "$base_dir/$resize")){
		   echo "<font color='00FF00'>$resize à bien été enregistré</font><br>";
		  }
		  else{
		   echo "<font color='FF0000'>échec lors de l'enregistrement de $resize!</font><br>";
		  }
    }
   elseif($oldsize[2]==2){ 
	  if(imagejpeg($des, "$base_dir/$resize")){
	   echo "<font color='00FF00'>$resize à bien été enregistré</font><br>";
	  }
	  else{
	   echo "<font color='FF0000'>échec lors de l'enregistrement de $resize!</font><br>";
	  }   
    }
   elseif($oldsize[2]==3){  
	   if(imagepng($des, "$base_dir/$resize")){
	   echo "<font color='00FF00'>$resize à bien été enregistré</font><br>";
	  }
	  else{
	   echo "<font color='FF0000'>échec lors de l'enregistrement de $resize!</font><br>";
	  }
   }
  

  
  
}
////////////////////////////////// UPLOAD
if(isset($_GET['upload'])){
 $file_name = $_FILES['file']['name'][0];
	$fole_name = correctname($file_name);

 echo"demande d'upload vers: <i>$file_name</i><br>";
 $file_extension = strtolower(substr(strrchr($fole_name,"."),1));
 if( 
	(($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'gif' || $file_extension == 'png') && $num_type=='img') 
	|| 
	($num_type=='piece') 
	){    
   if(copy($_FILES['file']['tmp_name'][0], "$base_dir/$fole_name")){
	  echo "<font color='009900'>le document à bien été chargé( $fole_name )</font><br>
	  <script language='javascript'>
	  putimg('http://$prov$path/img/$fole_name');
	  </script>
	  ";
	 }
	 else{
	  echo "<font color='FF0000'>échec lors du chargement de fichier! ( $fole_name )</font><br>";
	 }
  }
		else{
  		echo"<font color='FF0000'>mauvais format de fichier</font><br>";
  }
}
/********************************************************************************************************
                                            LISTAGE
********************************************************************************************************/
if( $num_type=='piece'){
	/*echo"
								<a href='#' onclick=\"							
								parent.document.crea.dtl".$num_name."_action.value='';
								parent.document.crea.dtl".$num_name."_attribute.value='';
								parent.document.getElementById('vs".$num_name."').innerHTML='piece jointe: aucune';\">
								<b>sans piece jointe</b></a>
						";*/
	}

	echo"

	<table>
";
////////////////////////////////// LISTE


$dir = dir($base_dir);
while($entry = $dir->read()){
  $extension = strtolower(substr($entry,strrpos($entry,"."),strlen($entry)));
  $name = ereg_replace($extension,'',$entry);
  $type = filetype("$base_dir/$entry");
		$size = filesize("$base_dir/$entry")/1000;
   $size = round($size,2);
  if($extension!='.jpg' && $extension!='.gif' && $extension!='.png' && $extension!='.db' && $entry!='.' && $entry!='..' && $num_type=='piece' 	){
			$tot++;
			$dirrr = substr(getenv('SCRIPT_NAME'),0,strrpos(getenv('SCRIPT_NAME'),"/"));
			echo"<tr><td> 
					<a href='#' onclick=\"putfil('http://$prov$path/$base_dir/$entry')\" class='info'>							
					<img src='http://www.adeli.wac.fr/icos/".substr($extension,1,strlen($extension)).".gif' border='none'>
					<b>$entry</b>
					</a>
			</td>
			<td>
			<a href='$opentool&type=$num_type&name=$num_name&delete=$entry' title='supprimer'><font color='FF0000'>[x]</font></a><br>
			<font size='1'>
			 (<font color='$alertcolor'>$size Ko</font>)
				</font>
			</td></tr>";

		
  }
  elseif( ($extension=='.jpg' || $extension=='.gif' || $extension=='.png') && $num_type=='img' ){
		   $tot++;
		   $dim = getimagesize("img/$entry");
		   if($dim[0] <= $dim[1]){
			$dimtoprint = " height='60'";
		   }
		   else{
			$dimtoprint = " width='60'";
		   }
		   
		   $alertcolor='000000';
		   if($size > 1000){     $alertcolor='FF0000';    }
		   $dimension = getimagesize("img/$entry");
			
			
			
		   echo"<tr><td>
		   <a href='#' onclick=\"putimg('http://$prov$path/$base_dir/$entry')\" class='info'>";
		
			//echo"<a href='#' onclick=\"parent.document.visu_$art_name.src='http://$prov$path/img/$entry';parent.document.crea.dtl".$num_name."_attribute.value='http://$prov$path/img/$entry';parent.document.getElementById('dragdiv').style.visibility='hidden'\">";
		
			
		   
		   
		   echo"<img src='$base_dir/$entry' $dimtoprint alt='$entry'><span style='top:-50px'><img src='img/$entry' alt='$entry'></span></a></td><td>
			 <a href='$opentool&type=$num_type&name=$num_name&delete=$entry' title='supprimer'><font color='FF0000'>[x]</font></a>
			 <a href='$opentool&type=$num_type&name=$num_name&getresize=$entry' title='redimensioner'><font color='000099'>>|<</font></a>
			 </td><td>
			 <font size='1'>
			 ($dimension[0]  x $dimension[1] , <font color='$alertcolor'>$size Ko</font>)
			 </font>
			 <br>
			 ";
		
		  if(isset($_GET['getresize'])){
		   $getresize = $_GET['getresize'];
		   if($getresize == $entry){
		   echo"
		   </form>
		   <form name='res' method='post' action='$opentool&type=$num_type&name=$num_name&resize'>nouvelle taille:
		   <select name='newsize'>
			<option value='10'>10%</option>
			<option value='20'>20%</option>
			<option value='30'>30%</option>
			<option value='40'>40%</option>
			<option value='50'>50%</option>
			<option value='60'>60%</option>
			<option value='70'>70%</option>
			<option value='80'>80%</option>
			<option value='90'>90%</option>
		   </select>
		   <input type='button' onclick=\"javascript:document.location='$opentool&type=$num_type&name=$num_name'\" value='annuler'>
		   <input type='hidden' name='resize' value='$entry'>
		   <input type='submit' value='redimensioner'>
		   </form>
		   ";
		   }
		  }
		  echo"</td></tr>";
  }
}
echo"</table>";
///////////////////////////////// LISTE VIDE
if($tot == 0){
 echo"répertoir vide";
}
if($num_type==='piece'){
 /*echo"<hr><b>Attention !</b><br>
	les fichiers joints ne sont pas intégrés au mail,<br>
	si vous joignez un fichier, ne le supprimez pas avant d'être sûr qu'il n'est plus nécessaire.";*/
}
 
}
echo"</td></tr>";
if($num_type!='piece'){
	echo"	<tr bgcolor='CCCCCC'><td valign='top' align='left'>
		<img src='$style_url/lalie/tools_link.png'> <font color='FFFFFF'><b>action:</b></font>
		</td></tr>
		<tr bgcolor='EEEEEE'><td valign='top' align='left'>
		<font color='000000' size='1'>(liens vers lequel va pointer l'élément)</font>
		</td></tr>
		<tr height='10' bgcolor='FFFFFF'><td valign='top'>
		
		
		<input type='text' value='' style=\"width:100%\" name='acte' onchange=\"parent.document.crea.dtl".$num_name."_action.value=this.value\"><a href='#'>ok</a>";
		if(!isset($_GET['libre'])){
		echo"
	<script language='javascript'>
		document.toul.acte.value=parent.document.crea.dtl".$num_name."_action.value;
		</script>
	";
}
}

}

echo"  </td>
  </tr>
</table>

</form>
</div>
<script language='javascript'>
	Hu = document.getElementById('toul').scrollHeight;
	//alert(Hu);
	//parent.document.getElementById('dragdiv').style.height=(parseInt(Hu)+20)+'px';
	//parent.document.getElementById('dragframe').style.height=Hu;	
	//parent.document.getElementById('dragframe').scrolling='no';
</script>
</body>
</html>";
?>