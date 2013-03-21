<?php // 349 > LaLIE Mail ;
if(is_file('bin/_tools.php')){
	$opentool='./?option=lalie&part=incpath=bin/_tools.php&1';
}
else{
	$opentool='http://www.adeli.wac.fr/vers/$vers/update.php?file=_tools.php?1';
}
$step = abs($_GET['step']);
$currentstep = $step+1;
insert('_lalie_trace');
	echo"
	<table width='90%' cellspacing='0' cellpadding='0' border='0' class='cadrebas'>
   <tr><td class='buttontd'><span class='textegras'>édition de lettre</span> étape $currentstep/4 </td></tr>
   <tr><td class='cadrebas' style='padding:10px'>";
if(ereg("@",$u_email) && strrpos($u_email,"@")<strrpos($u_email,".")){
//////////////////////////////////////////////////////////////////////////////// CHOIX
if($step==0){
		if(fopen("http://adeli.wac.fr/vers/$vers/lalie_get_mli.php","rb")){
			
			echo"<br>
			- choisissez le modèle de mail que vous souhaitez envoyer
			<br><br>";
			include("http://adeli.wac.fr/vers/$vers/lalie_get_mli.php");
		}
			
			$estedirlo = substr(getcwd(),strrpos(getcwd(),"/")+1,strlen(getcwd()));
			if(is_dir("lalie") || is_dir("reg/$x_id/models/")){				
				echo"<br><br>- modèles personnalisés<br><br>";		
			if($estedirlo=="LaLIE"){
				$dir = dir("reg/$x_id/models/");
			}	
			else{
				$dir = dir("lalie/models");
			}
			while($entry = $dir->read()){
				$file_extension = strtolower(substr(strrchr($entry,"."),1));
				$ima="pas d'aperçu...";	
				if($file_extension == "mli"){
						$model = str_replace(".mli","",$entry);
						echo"&nbsp;&nbsp;- <a href='./?option=$option&part=$part&step=1&moule=$model&create' class='info'><b>$model</b>
						<span>$ima</span></a><br>";
				}
			}
			$dir->close();			
			}
			echo"<br><br><table>";
			
			if(is_dir("lalie/drafts")){				
				echo"<br><br>- brouillons<br><br>";		
				$tabl=array();
				$dir = dir("lalie/drafts");
				while($entry = $dir->read()){
					$file_extension = strtolower(substr(strrchr($entry,"."),1));
					if($file_extension == "mli"){
						if(strlen($entry)>15){
							$model =substr($entry,0,strlen($entry)-19);
							$dtmod = substr($entry,strlen($entry)-18,14);
							array_push($tabl,"$dtmod$model");
						}
					}
				}
				reset($tabl);
				rsort($tabl);
				foreach($tabl as $v){					
					$model =substr($v,14,strlen($v));
					$dtmod = substr($v,0,14);
					$entry = "$model-$dtmod.mli";
					$dtmod = date("d/m/Y H:i", mktime( substr($dtmod,8,2), substr($dtmod,10,2), substr($dtmod,12,2),substr($dtmod,4,2), substr($dtmod,6,2), substr($dtmod,0,4)));
					$hmoedl = str_replace("http","",$model);
					$fp = fopen("lalie/drafts/$entry","r");
					$pd = filesize("lalie/drafts/$entry");						
					$getcontent = fread($fp,$pd);
					fclose($fp);
					$contentoupas="";
					$readcontent = split("<!>",$getcontent);
					for($d=0 ; $d<sizeof($readcontent) ; $d++){
						 $moule_atom = split("<>",$readcontent[$d]);
							if($moule_atom[0]=="texte"){
								$contentoupas .= strip_tags(trim($moule_atom[2]))." ";	
							}				
					}
					$titroupa = substr($contentoupas,0,30);
			
					echo"<tr><td>&nbsp;&nbsp;- <a href='./?option=$option&part=$part&step=1&moule=$model&draft=$entry&create' class='info'><b>$titroupa...$v..</b>
					&nbsp;&nbsp;<br><i>($hmoedl)</i> $dtmod
					<span>$contentoupas</span></a></td><td> 
					<a href=\"#\" onclick=\"delfile('lalie/drafts/$entry')\"><img src=\"http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif\" border='none' alt='supprimer'></a>
					</td></tr>";
				}			
				$dir->close();			
			}
			echo"</table><br><br>";
			if($_SESSION["recuplast"]!=""){
			echo"- récupérer la session courante<br><br>
			&nbsp;&nbsp;- <a href='./?option=$option&part=$part&moule=".$_SESSION["recuplast"]."&step=1'><b>lettre en cours</b></a><br><br>";
			}
}
else{

	if( isset($_GET['moule']) && $_GET['moule']!=''){
		$moule = $_GET['moule'];
		$antes=".";
		$prevurl="";
	if(substr($moule,0,4)=="http"){
			$antes="http://www.adeli.wac.fr/vers/$vers";
			$moule = str_replace("http","",$moule);
			$prevurl="http";			
		}
	$mllast = $prevurl.$moule;
	//echo"$mllast / ".$_SESSION["recuplast"]." ".$_SESSION["option"]."<hr>";
	//if(isset($_SESSION['recuplast']) && $_SESSION['recuplast'] != $mllast ){
	if(isset($_GET['create'])){
		unset($_SESSION["dtl0_type"]);
		echo"vidage de la lettre<hr>";
	}
	$_SESSION['recuplast']=$mllast;
		
		
				
		if(isset($_GET['recup'])){
			$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
			$res = mysql_query("SELECT * FROM `$lalierp` WHERE id=".$_GET['recup']);
			$ro = mysql_fetch_object($res);
			$bck=$ro->code;
			$moule=$ro->moule;
			deconnecte($conn);	
			
			$moule_details=split("<!>",$bck);	
			for($d=0 ; $d<sizeof($moule_details) ; $d++){
					$moule_atom = split("<>",$moule_details[$d]);
						$_SESSION["dtl".$d."_type"] = trim($moule_atom[0]);
						$_SESSION["dtl".$d."_name"] = trim($moule_atom[1]);
						$_SESSION["dtl".$d."_attribute"] = trim($moule_atom[2]);
						$_SESSION["dtl".$d."_action"] = trim($moule_atom[3]);					
				}
		}
		elseif(isset($_GET['draft'])){
					$draft = $_GET['draft'];
					$_SESSION["recuplast"]=$mllast.$draft;
					if($fp=fopen("lalie/drafts/$draft","rb")){
						$moule_details=split("<!>",fread($fp,4000000));
						$rectification=array_pop($moule_details);
						fclose($fp);
					}
					else{
						echo"erreur lors de l'ouverture de fichier (lalie/draft/$draft)";
					}
		}
		else{
			if($fp=fopen("$antes/lalie/models/$moule.mli","rb")){
				$moule_details=split("<!>",fread($fp,4000000));
				$rectification=array_pop($moule_details);
				fclose($fp);
			}
			else{
				echo"erreur lors de l'ouverture de fichier ($antes/lalie/models/$moule.mli)";
			}
			array_push($moule_details,"piece<>piecejointe<><><!>");
		}

		
		
			if(!isset($_SESSION["dtl0_type"])){
				for($d=0 ; $d<sizeof($moule_details) ; $d++){
					$moule_atom = split("<>",$moule_details[$d]);
						$_SESSION["dtl".$d."_type"] = trim($moule_atom[0]);
						$_SESSION["dtl".$d."_name"] = trim($moule_atom[1]);
						$_SESSION["dtl".$d."_attribute"] = $moule_atom[2];
						if(ereg("</",$_SESSION["dtl".$d."_attribute"]) || ereg("<br>",$_SESSION["dtl".$d."_attribute"])){
							$_SESSION["dtl".$d."_attribute"] = trim($moule_atom[2]);
						}
						$_SESSION["dtl".$d."_action"] = trim($moule_atom[3]);	
						$_SESSION["currentmodel"] = "$prevurl$moule-".date("YmdHis");			
				}				
				$return.=returnn("création d'une session","009966",$vers,$theme);
			}
			
			
			$enregmachin="";
						///////////////////////////////////COLLECTE
						for($d=0 ; $d<sizeof($moule_details) ; $d++){
							if(isset($_POST["dtl".$d."_type"])){
								$_SESSION["dtl".$d."_type"] = $_POST["dtl".$d."_type"];
							}
							if(isset($_POST["dtl".$d."_name"])){
								$_SESSION["dtl".$d."_name"] = $_POST["dtl".$d."_name"];
							}
							if(isset($_POST["dtl".$d."_attribute"])){
								$_SESSION["dtl".$d."_attribute"] = stripslashes($_POST["dtl".$d."_attribute"]);
							}
							if(isset($_POST["dtl".$d."_action"])){
								$_SESSION["dtl".$d."_action"] = $_POST["dtl".$d."_action"];
							}
							$enregmachin.=$_SESSION["dtl".$d."_type"]."<>".$_SESSION["dtl".$d."_name"]."<>".$_SESSION["dtl".$d."_attribute"]."<>".$_SESSION["dtl".$d."_action"]."<!>";
						}
						
						if(isset($_GET['savedraft'])){
							$sp = fopen("lalie/drafts/".$_SESSION["currentmodel"].".mli","w+");
							fwrite($sp,$enregmachin);
							fclose($sp);
						}
						
						if(isset($_GET['load'])){
							$load = $_GET['load'];
							copy("reg/$clid/mails/$load","reg/$clid/mails/$load.php");
						    include("reg/$clid/mails/$load.php");
							unlink("reg/$clid/mails/$load.php");
							$return.="- chargement de données depuis un fichier existant...\n";
						}

/////////////////////////////////////////////////////////////////////////////// EDITION
if($step==1){


			echo"
<script language='javascript'>
curo=1;
var Hu = 20;
alcur=0;
asso=new Array();
function sele(cadre){
	curo=cadre;
	//document.visu.cc.value=curo;
	for(i=1; i<=alcur ; i++){
		document.getElementById('editbox_'+i).style.borderWidth='0px';
		document.getElementById('editbox_'+i).style.borderColor='#FFFFFF';
		document.getElementById('editbox_'+i).style.height='0px';
		Hu = 20;
		if(document.all) Hu=eval('editbox_'+i).document.body.scrollHeight; 
		else Hu = eval('editbox_'+i).document.height;
		document.getElementById('cadro_'+i).style.height=Hu+'px';
		document.getElementById('cadro_'+i).style.overflow='visible';
	}
	document.getElementById('editbox_'+curo).style.borderWidth='1px';
	document.getElementById('editbox_'+curo).style.borderColor='#0099FF';
	document.getElementById('editbox_'+curo).style.height='50px';
	document.getElementById('cadro_'+curo).style.height='1px';	
	document.getElementById('cadro_'+curo).style.overflow='hidden';	
	if(document.all) eval('cadro_'+curo).style.overflow='hidden'; eval('cadro_'+curo).style.height='1px';
}
function creature(com,opt,koi){
	eval('editbox_'+curo).document.execCommand(com, opt, koi); 
	eval('editbox_'+curo).focus();
}
function addvar(ki){
   if (document.all) {
	  var oRng = eval('editbox_'+curo).document.selection.createRange();
	  oRng.pasteHTML(ki);
   } else {
	  eval('editbox_'+curo).document.execCommand('insertHTML', false, ki);
   }
   eval('editbox_'+curo).focus();
}
</script>					
	<table cellspacing='0' cellpadding='0' width='100%'><tr><td align='left'>
	
<table border='1' cellpadding='0' cellspacing='0' width='100%'>
  <tr>
<td>
 <table cellpadding='0' cellspacing='0' border='0' bgcolor='FFFFFF'>
<tr valign=\"middle\" class='buttontd'> 
			  <td valign='top'>
					<img onClick=\"format(curo)\" border='none'  src=\"http://www.adeli.wac.fr/vers/$vers/images/eraz.gif\" alt=\"formater le texte\">
		<select onchange='addvar(this.value);'>
	<option value='0'>insertion de variables</option>
	";
			$conn = connecte($base, $host, $login, $passe);					
			$res_field = mysql_list_fields($base,$dblist);
			$columns = mysql_num_fields($res_field);		   
			for ($i = 0; $i < $columns; $i++) {
				$field_name = mysql_field_name($res_field, $i);
				$field_type = mysql_field_type($res_field, $i);
				if($field_type == "string"){
					echo"<option value='$"."$field_name'>$field_name</option>";
				}
			} 
			mysql_close($conn);
			
if(!isset($edit_font_lalie)) $edit_font_lalie = array('Arial','Courier New','System','Tahoma','Times New Roman','Verdana','Webdings');
if(!isset($edit_size_lalie)) $edit_size_lalie = array('1','2','3','4','5','H3','H2','H1');
	echo"
	</select>
	<select onchange=\"creature('fontname', false, this.value);this.value='';\">
									<option value='' selected>police</option>";
									foreach($edit_font_lalie as $fonti){
										echo"<option value=\"$fonti\" style=\"font-family:$fonti\">$fonti</option>\n";	
									}
									echo"
							</select></td><td valign='top'><img 
							border='none'   src=\"http://www.adeli.wac.fr/vers/$vers/images/size.gif\" alt=\"taille du texte\"></td><td valign='top'><select onchange=\"taille(curo,this.value,this)\">
										<option value='0' selected></option>";
									foreach($edit_size_lalie as $sizei){
										echo"<option value=\"$sizei\">$sizei</option>\n";	
									}
									echo"
										</select></td><td valign='top'><img 
										onclick=\"creature('bold', false, null); \" border='none'  src=\"http://www.adeli.wac.fr/vers/$vers/images/bold.gif\" alt=\"gras\"><img 
										onClick=\"creature('italic', false, null); \" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/italic.gif\" alt=\"italic\"><img 
										onClick=\"creature('underline', false, null); \" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/underline.gif\" alt=\"souligne\"><img 
										onClick=\"creature('justifyleft', false, null); \" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/left.gif\" alt=\"aligner à gauche\"><img 
										onClick=\"creature('justifycenter', false, null); \" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/center.gif\" alt=\"centrer\"><img 
										onClick=\"creature('justifyright', false, null); \" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/right.gif\" alt=\"aligner à droite\"><img 
										onClick=\"justify(curo,'right');\" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/justify.gif\" alt=\"justifier le texte\"><img 
										onClick=\"float(curo,'right');\" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/floatright.gif\" alt=\"flottant à droite\"><img 
										onClick=\"float(curo,'left');\" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/floatleft.gif\" alt=\"flottant à gauche\"><img  
										onClick=\"creature('StrikeThrough', false, null); \" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/strike.gif\" alt=\"barre\"><img border='none' src='http://www.adeli.wac.fr/vers/$vers/images/fgcolor.gif' alt='couleur de texte' onclick=\"choosecolor(curo,'ForeColor','$field_name','html',event)\"><img border='none' src='http://www.adeli.wac.fr/vers/$vers/images/bgcolor.gif' alt='couleur de fond' onclick=\"choosecolor(curo,'Backcolor','$field_name','html',event)\"><img 
										onClick=\"creature('InsertHorizontalRule', false, null); \" border='none'  src=\"http://www.adeli.wac.fr/vers/$vers/images/line.gif\" alt=\"ligne\"><img 
										onClick=\"addlink(curo)\" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/link.gif\" alt=\"lien hypertexte\"><img  
										onClick=\"creature('Unlink',false,null); \" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/unlink.gif\" alt=\"enlever les liens\"><img
										onClick=\"creature('InsertUnorderedList', false, null); \" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/list.gif\" alt=\"liste\"><img  
										onClick=\"document.getElementById('tableau_').style.visibility='visible';\" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/table.gif\" alt=\"tableau\"><img  
										onClick=\"sautdeligne(curo)\" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/br.gif\" alt=\"saut de ligne\"><img  
										onclick=\"context('dragdiv','tools','$opentool&type=img&name=$d&in_html='+curo,event)\" border='none' src=\"http://www.adeli.wac.fr/vers/$vers/images/img.gif\" alt=\"insérer une image\"><input 
										type='button' class='buttontd' value='Source' onclick='sourcev()'>
										</td>
										<td><div style='position:relative'>
		  <script language=\"JavaScript\">
		  	var tab_x_ = 2;
			var tab_y_ = 2;
			var tab_b_ = '';
			editsource = new Array();
			function sourcev(){
			 vis=document.getElementById('dtl'+curo+'_source').style.visibility;
			 if(vis=='visible'){
				 editsource[curo]=false;
				 document.getElementById('dtl'+curo+'_source').style.visibility='hidden';
			 }
			 else if(vis=='hidden'){
				 editsource[curo]=true;
				 document.getElementById('dtl'+curo+'_source').style.visibility='visible';
				document.getElementById('dtl'+curo+'_source').focus();
			 }
			 
			}
		  </script>
		  <div id='tableau_' class='buttontd' style='z-index:150;position:absolute;top:-20px;visibility:hidden'><b>Insérer un tableau</b><hr>
		  <table>
		  <tr><td>colones : </td><td><input type='text' value='1' autocomplete='false' onkeyup='tab_x_=this.value'></td></tr>
		  <tr><td>lignes : </td><td><input type='text' value='1' autocomplete='false' onkeyup='tab_y_=this.value'></td></tr>
		  <tr><td>fond : </td><td><div id='tab_cou' style='padding:5px'>".colorpicker("tabb$i",'FFFFFF',"tab_b_='COLOR';document.getElementById('tab_cou').style.backgroundColor='COLOR';document.getElementById('menu_color').style.visibility='hidden';",-200,"choisir fond",6,true)."</div></td></tr>
		  <tr><td colspan='2' align='right'>
		  <input type='button' value='annuler' class='buttontd' onclick=\"document.getElementById('tableau_').style.visibility='hidden';\">
		  <input type='button' value='insérer' class='buttontd' onclick=\"tableau(curo,tab_x_,tab_y_,tab_b_);document.getElementById('tableau_').style.visibility='hidden';\">
		  </td></tr>
		  </table>
		  </div></div></td>			  
			</tr>
		  </table>
		  
</td></tr>
 <tr>
<td bgcolor='#FFFFFF'> 
		  		<form name='visu'>
					<div style='width:100%;height:350px;overflow:scroll'>
					
						<table  cellspacing='1' cellpadding='1'  style='border-width:1px; border-style:solid; border-color:#000000 #000000 #000000 #000000;background-color:#FFFFFF'>
						<tr><td align='center' style='position:relative'>";
						$curo=0; //<input type='text' name='cc'>
						$styleref = '';
					///////////////////////////////////AFFICHAGE
						for($d=0 ; $d<sizeof($moule_details) ; $d++){
							$this_type = $_SESSION["dtl".$d."_type"];
							$this_name = $_SESSION["dtl".$d."_name"];
							$this_attribute = $_SESSION["dtl".$d."_attribute"];
							$this_action = $_SESSION["dtl".$d."_action"];							
							
							if(ereg('stylesheet',$this_type)){
								$styleref = '<'.str_replace('<><><><!>','',$this_type).'>';
							}
							
							$write_name="";
							if($this_name != NULL){
								$write_name = " name='visu_$this_name' id='visu_$this_name'";
							}
							$write_attribute="";
							if($this_attribute != NULL){
								$write_attribute = " $this_attribute";
							}
							

							if($this_type == 'texte' && $this_name!=''){
								$curo++;
								if(!ereg("</",$write_attribute) && !ereg("<br>",$write_attribute)){
									$write_attribute=nl2br($write_attribute);
								}
								echo"<iframe id=\"editbox_$curo\" name=\"editbox_$curo\" src=\"about:blank\" width=\"100%\"  frameborder=\"0\" scrolling=\"no\"  style=\"z-index:90;border-color:#0099FF;border-style:solid;border-width:0px;height:0px;display:block\"></iframe>
								<div id='cadro_$curo' onclick='sele($curo)' border='0' style='width:100%;height:20px;overflow:hidden;display:block'>$write_attribute</div>
<script language=\"JavaScript\">
	alcur = $curo;
	asso[$curo] = $d;	
	function save_$curo(){		
		Hu = 0;
		if(curo == $curo){
			if(editsource[$curo]==true){
				inh = document.crea.dtl".$d."_attribute.value;
				editbox_$curo.document.body.innerHTML = inh;
			}
			else{
				inh = editbox_$curo.document.body.innerHTML;
				document.crea.dtl".$d."_attribute.value = inh;
			}
			
			document.getElementById('cadro_$curo').innerHTML=inh;
			Hu = 20;
			if(document.all){ 
				Hu = editbox_$curo.document.body.scrollHeight; 
			}
			else{ 
				Hu = editbox_$curo.document.height; 
			}	
			if(isNaN(Hu)){
				Hu=100;
			}
			if(navigator.userAgent.indexOf('Firefox')!= -1){
				  //Hu+=30;	
			}	
		}
		document.getElementById('editbox_$curo').style.height=Hu+'px';
		setTimeout('save_$curo()',10);
	}
</script>
								";
							}
							elseif($this_type == 'piece'){
								echo "<span class='edition' id='vs$d'
								style='display:block;' onclick=\"context('dragdiv','tools','$opentool&type=$this_type&name=$d',event)\">piece jointe: $write_attribute</span>";
							}
							elseif($this_type == 'frame'){
								$sizeof = split("x",$write_attribute);
								$sw = $sizeof[0];
								$sh = $sizeof[1];
								echo "
								<table class='edition' $write_name width='$sw' height='$sh' onclick=\"context('dragdiv','tools','$opentool&type=$this_type&name=$d',event)\"><tr><td>&nbsp;</td></tr></table>
								";
							}
							elseif($this_type == 'td'){
								echo "
								<$this_type  class='edition' $write_name $write_attribute>
								";
							}
							elseif($this_type == 'img'){
								$src=trim(str_replace("src=","",$write_attribute));
								if($this_name != ''){
									echo "
									<$this_type $write_name src='$src'    onclick=\"context('dragdiv','tools','$opentool&type=$this_type&name=$d',event)\">
									";
								}
								else{
									echo "
									<$this_type $write_name src='$src'>
									";
								}
							}
							
							elseif( substr($this_type,0,1)!='a' && substr($this_type,0,4)!='link'){							
								echo "<$this_type $write_name $write_attribute  class='edition' readonly >";
							}							
						}
					///////////////////////////////////VARIABLES						
					echo "</td></tr></table>
					</div>
					</form>
</td></tr>
  <tr>
<td align='right'>					
			<form action='./?option=$option&part=$part&moule=$prevurl$moule&step=2' method='post' name='crea'>

					<table cellspacing='5' cellpadding='5'><tr>
					
					<td class='buttontd'><a href='#' onclick=\"document.crea.action='./?option=$option&part=$part&moule=$prevurl$moule&step=1&savedraft';document.crea.submit()\">
					enregistrer comme brouillon</a></td>
					
					<td class='buttontd'><a href='./?option=$option&part=$part' title='retour'><b>retour</b> aux modèles</a></td>
										
					<td class='buttontd'><a href='#' onclick='document.crea.submit()'><b>continuer</b></a></td>
					
					</tr></table>							
										
";
					$ci=0;
						for($d=0 ; $d<sizeof($moule_details) ; $d++){							
							$this_type = $_SESSION["dtl".$d."_type"];
							$this_name = $_SESSION["dtl".$d."_name"];
							$this_attribute = $_SESSION["dtl".$d."_attribute"];
							$this_action = $_SESSION["dtl".$d."_action"];							
							if((!ereg("/",$this_type)) && ($this_name != '')){							
								$write_name="";
								if($this_name != NULL){
									$write_name = " name='$this_name' id='$this_name'";
								}
								$write_attribute="";
								if($this_attribute != NULL){
									$write_attribute = " $this_attribute";
								}								
								echo"								
								<input type='hidden' readonly name='dtl".$d."_type' value=\"$this_type\">
								<input type='hidden' readonly name='dtl".$d."_name' value=\"$this_name\">";
								if($this_type == 'texte'){
									$ci++;
									echo "<textarea rows='1' cols='1' id='dtl".$ci."_source' name='dtl".$d."_attribute' style='border:#999 2px inset;color:#000;width:300px;height:400px;position:absolute;visibility:hidden;top:200px; right:10px;' onblur='sourcev()'>$this_attribute</textarea>
									<script language='javascript'>
									editbox_$ci.document.designMode = \"On\";
									editsource[$ci] = false;
									editbox_$ci.document.write(\"<html><head>\");";
									if($styleref!=''){
									echo"
									editbox_$ci.document.write(\"$styleref\");
									";
									}
									echo"
									editbox_$ci.document.write(\"</head><body>\");
									editbox_$ci.document.write(document.crea.dtl".$d."_attribute.value);
									editbox_$ci.document.write(\"</body></html>\");
									Hu = 20;
									if(document.all) Hu=editbox_$ci.document.body.scrollHeight; 
									else Hu = editbox_$ci.document.height;
									document.getElementById('cadro_$ci').style.height=Hu+'px';
									setTimeout('save_$ci()',2000);
									
	</script>";
								}
								else{
									echo"<input type='hidden' readonly name='dtl".$d."_attribute' value=\"$this_attribute\">";
								}								
								echo"<input type='hidden' readonly name='dtl".$d."_action' value=\"$this_action\">";							
							}							
						}
					echo "</form>
					
					</td>
				</tr></table>	

			<div id='dragdiv'  style='position:absolute;visibility:hidden;left:100px;top:100px;'>
				<table cellspacing='1' cellpadding='1' class='buttontd'>
				<tr><td align='left'>	<span class='textegrasfonce'>Outils</span></td>				
				<td align='right'>	<a href='about:blank' target='tools' onclick=\"document.getElementById('dragdiv').style.visibility='hidden'\">fermer [x]</a>	
				</td>
				</tr>			
				<tr><td align='center' colspan='2'>	
				<iframe src='about:blank' width='380' height='400' frameborder='0' name='tools'></iframe>
				</td></tr></table>
		</div>
<script language=\"JavaScript\">
sele(1);
</script>
				";
}
/////////////////////////////////////////////////////////////////////////// DESTINATIARES
elseif($step==2){

					$htmlbody="";	
					///////////////////////////////////AFFICHAGE
						for($d=0 ; $d<sizeof($moule_details) ; $d++){
							
							$this_type = $_SESSION["dtl".$d."_type"];
							$this_name = $_SESSION["dtl".$d."_name"];
							$this_attribute = $_SESSION["dtl".$d."_attribute"];
							$this_action = $_SESSION["dtl".$d."_action"];							
							
							$write_name="";
							if($this_name != NULL){
								$write_name = " name='visu_$this_name' id='visu_$this_name'";
							}
							$write_attribute="";
							if($this_attribute != NULL){
								$write_attribute = " $this_attribute";
							}
							
							if($this_action != NULL){
								if(!ereg("http://",$this_action) && !ereg("@",$this_action)){
									$this_action = "http://".$this_action;
								}
								elseif(!ereg("mailto",$this_action) && ereg("@",$this_action)){
									$this_action = "mailto:".$this_action;
								}
								$htmlbody.= "<a href='$this_action' target='_blank'>";
							}
							
							if($this_type == 'texte'){								
								//echo trim(nl2br($write_attribute));
								if(!ereg("</",		$write_attribute)){					
									$htmlbody.=  trim(nl2br($write_attribute));
								}
								else{					
									$htmlbody.=  trim($write_attribute);
								}
							}
							elseif($this_type == 'piece'){								
								$htmlbody.=  trim($write_attribute);
							}
							elseif($this_type == 'img'){
								$htmlbody.=  "
								<$this_type $write_name src='$write_attribute' border='none'>
								";
							}
							elseif($this_type == 'frame'){
								$sizeof = split("x",$write_attribute);
								$sw = $sizeof[0];
								$sh = $sizeof[1];
								$htmlbody.=  "
								<iframe $write_name width='$sw' height='$sh' src='$this_action'>$this_action</iframe>
								";
							}
							else{
								$htmlbody.=  "
								<$this_type $write_name $write_attribute>
								";
							}
							
							if($this_action != NULL){
								$htmlbody.=  "</a>";
							}							
						}
					///////////////////////////////////VARIABLES	
					$htmlbody.= "<p align='right'><a href='http://www.lalie.wac.fr/?desinsc=$x_id' target='_blank'><font color='999999' size='1'>se désincrire</font></a> <font color='999999' size='1'>-</font> </p>";
					
					$htmlbody = str_replace('"','\"',$htmlbody);	
					$hta = explode("\n",$htmlbody);
					$htmlbody='';
					foreach($hta as $b){
						$htmlbody.='\\n'.trim($b);
					}	
					echo "
	<form action='./?option=$option&part=$part&moule=$prevurl$moule&step=3' method='post' name='glob'>
	<table  cellspacing='0' cellpadding='0' class='buttontd' width='100%'>
		<tr><td align='left' valign='top'>
					<span class='textegrasfonce'>Aperçu</span>
		</td><td width='120'>
					<span class='textegrasfonce'>Actions</span>
		</td></tr>
		<tr><td bgcolor='#FFFFFF' valign='top'>
					<iframe name='apercu' frameborder='0' src='about:blank' style='border-width:1px; border-style:solid; border-color:000000 000000 000000 000000;width:100%;height:500px'></iframe>
					<script language='javascript'>
					apercu.document.write(\"$htmlbody\");
					</script>
	
	</td><td valign='top' align='right'  width='120'>
			
					
					
					<table  width='120' cellspacing='1' cellpadding='5'  style='border:#CCCCCC 1px solid;'>
					<tr><td align='left' valign='top'>						
							<font color='999999'><b>Envoyer à:</b></font><br>
							<input type='radio' name='aki' value='g' onclick=\"akito('g')\"> Groupes
							<div id='ch_g'  style='width:200px;height:1px;overflow:hidden'>
							<input type='checkbox' name='tous' value='1' onclick=\"selectall();\">tous<br>
							";		
							for($i=0 ; $i<sizeof($groups) ; $i++){
								$grpupforjava = str_replace("_","",$groups[$i]);
								
								$grpforhuman = str_replace("_"," ",$groups[$i]);
								$conn = connecte($base, $host, $login, $passe);	
								$resultnum = mysql_query("SELECT `groupe` FROM `$dblist` WHERE `groupe`='$groups[$i]' AND `email`!='' $wherelalaie");
								$nbbon = mysql_num_rows($resultnum);
								mysql_close($conn);
								if($groups[$i]=='desinscrits'){
									echo"&nbsp;&nbsp;<input type='checkbox' disabled='disabled' name='g$groups[$i]'><font color='#990000'><i>$grpforhuman ($nbbon contacts)</i></font><br>";
								}
								elseif($nbbon>0){
									echo"&nbsp;&nbsp;<input type='checkbox' name='g$groups[$i]' value='1'>$grpforhuman ($nbbon contacts)<br>";
									$tousdesoufs.="\ndocument.glob.g$groups[$i].checked=1;";
								}
								else{
									echo"&nbsp;&nbsp;<input type='checkbox' disabled='disabled' name='g$groups[$i]'><span class='petittext'><i>$grpforhuman ($nbbon contacts)</i></span><br>";
								}
								
								$tousdesloufs.="\ndocument.glob.g$groups[$i].checked=0;";
							}		
							echo"</div>
							<br>
							<input type='radio' name='aki' value='u' onclick=\"akito('u')\" checked> Unité
							<div id='ch_u'  style='width:200px;height:150px;overflow:hidden'>
							<textarea name='libreki' style='width:200;height:100px' onKeyup=\"chech()\" onfocus=\"chech()\" readonly>$u_email;</textarea>
							<br>							  
							<span id='selboy'></span>
							</div>
							<br>
							<input type='radio' name='aki' value='p' onclick=\"akito('p')\"> Liste personnalisée
							<div id='ch_p' style='width:200px;height:1px;overflow:hidden'>
							<textarea name='persos' style='width:200;height:100px'\">$u_email;</textarea>
							</div>
							
							
							<script language='javascript'>
							  function confsup(id){
								is_confirmed = confirm('êtes vous sûr de vouloir supprimer définitivement l\'enregistrement '+id+' ?');
								if (is_confirmed) {
								 document.location='./?option=lalie&part=supp='+id+'&part=$part';
								}
							  }
							  
							  function selectall(){
								if(document.glob.tous.checked == 1){
									$tousdesoufs
								}
								if(document.glob.tous.checked == 0){
									$tousdesloufs
								}
							  }
							  function akito(ko){
							  	if(ko=='u'){
									document.glob.tous.checked=0;
									document.glob.aki[1].checked=1;	
									selectall();
									document.glob.persos.value='$u_email';
									document.glob.libreki.focus();	
									sizpa('ch_g',1,1);
									sizpa('ch_p',1,1);							
								}
								else{
								 	if(ko=='g'){
										document.glob.libreki.value='$u_email;';
										document.glob.persos.value='$u_email';
										document.glob.aki[0].checked=1;	
										sizpa('ch_u',1,1);
										sizpa('ch_p',1,1);
									}
									else{
										document.glob.libreki.value='$u_email;';
										document.glob.aki[2].checked=1;	
										document.glob.persos.focus();	
										sizpa('ch_g',1,1);
										sizpa('ch_u',1,1);							
									}	
								}								
								sizpa('ch_'+ko);							
							  }
							
							sep=';';
							esp=' ';
							tousmeli = \"$jscontacts\";
							tousmel = new Array();
							tousmel= tousmeli.split(sep);
							
							function trim(string){
								return string.replace(/(^\s*)|(\s*$)/g,'');
							} 
	
							function in_array(valeur) {
								tabl = document.glob.libreki.value;
								tableau = tabl.split(';');
								 for(e=0 ; e<tableau.length ; e++){								 	
								   if(trim(tableau[e]) == trim(valeur)){ 
								   	debutsol += \"<option value='\"+valeur+\"'>\"+valeur+\"</option>\";
									return false;
								   }						   
								 }
								 return true;
							}
							function checsup(valeur){
								tabl = document.glob.libreki.value;
								tableau = tabl.split(';');
								document.glob.libreki.value='';
								 for(e=0 ; e<tableau.length-1 ; e++){								 	
								   if(trim(tableau[e]) != trim(valeur)){ 
								   	document.glob.libreki.value+=esp+tableau[e]+sep;
								   }						   
								 }	
								 chech();						
							}
							function chech(){
								document.getElementById('selboy').innerHTML='';
								//akito('u');
							  	debutsel = \"<select onchange='document.glob.libreki.value+=esp+this.value+sep;chech()'><option>ajouter</option>\";
								debutsol = \"<select onchange='checsup(this.value)'><option>supprimer</option>\";
								for(i=0 ; i<tousmel.length ; i++){
									vef = in_array(tousmel[i]);
									if(vef){
										debutsel += \"<option value='\"+tousmel[i]+\"'>\"+tousmel[i]+\"</option>\";
									}
								}	
								debutsel += \"</select><br>\";
								debutsol += \"</select><br><br>\";
								document.getElementById('selboy').innerHTML=debutsel+debutsol;
							}
						 function sendmailer(){
							   if(document.glob.object.value!='' && document.glob.de.value!=''){
								   document.glob.submit();
								}
								 else{
								   alert(\"veuillez insérer un nom et un titre\");
								   document.glob.object.focus();
								}
							 }
							 akito('u');
							</script>
							
							<br><br>
							<font color='999999'><b>De :</b></font><br>
							<input type='text' name='de' value='$denomclient' style='width:200'><br><br>
							<font color='999999'><b>Adresse de réponse :</b></font><br>
							<input type='text' name='from' value='$u_email' style='width:200'><br><br>							
							<font color='999999'><b>Sujet du message :</b></font><br>
							<input type='text' name='object' value='' style='width:200'><br><br>							
							<font color='999999'><b>Envoi le :</b></font><br>
							Date : <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"contextdate(event);cal.location='$opencalendar&x_id=$x_id&amp;cible=glob.date&amp;date='+document.glob.date.value+'&amp;type=date'\"><input type='text' name=\"date\" value=\"".date('Y-m-d')."\" maxlength=\"10\" style=\"width:80px;background:none;\"><br>
							Vers :  <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"contextdate(event);cal.location='$opencalendar&x_id=$x_id&amp;cible=glob.heure&amp;date='+document.glob.heure.value+'&amp;type=time'\"><input type='text' name=\"heure\" value=\"".date('H:i').":00\" maxlength=\"10\" style=\"width:60px;background:none;\">
							<br><br>							
							
							
						
							</form>
					<br>
					</td></tr><td colspan='2' align='right'>
							
							<table cellspacing='5' cellpadding='5'><tr>
				
					<td class='buttontd'><a href='./?option=$option&part=$part&moule=$prevurl$moule&step=1' title='retour'><b>retour</b> à l'édition</a></td>
										
					<td class='buttontd'><a href='#' onclick='sendmailer()' title='envoyer'><b>envoyer</b></a></td>
					
					</tr></table>
					";	



}		
/////////////////////////////////////////////////////////////////////////// ENVOIS
elseif($step==3){
	insert('_lalie_async');
	$object = stripslashes($_POST['object']);
	$kelenv = $_POST['aki'];
	$libreki = $_POST['libreki'];
	$persos = $_POST['persos'];
	$de = $_POST['de'];
	$u_email = $_POST['from'];
	$e_heure=$_POST['heure'];
	$e_date=$_POST['date'];
	$typeenv="";
	$totlml=0;
	$listmail=array();
	if($kelenv == "g"){
		$typeenv = "envoi par groupe (";
		for($i=0 ; $i<sizeof($groups) ; $i++){
			if(isset($_POST["g$groups[$i]"])){
				if($groups[$i] == "sans_groupe"){
					$groups[$i] = "";
				}
				$typeenv.=" $groups[$i] ";
				$conn = connecte($base, $host, $login, $passe);	
					$res = mysql_query("SELECT `email` FROM `$dblist` WHERE 1 $wherelalaie AND `groupe`='$groups[$i]'");
					$totlml+=mysql_num_rows($res);
					while($ro = mysql_fetch_object($res)){
						array_push($listmail,trim($ro->email));
					}
				deconnecte($conn);
			}
		}
		$typeenv.=") ";		
	}
	elseif($kelenv == "u"){
		$typeenv = "envoi unitaire";
		$listmail = split(";",$libreki);
		$totlml = sizeof($listmail)-1;
	}
	elseif($kelenv == "p"){
		$typeenv = "envoi libre";
		$listmail = split("[\n;,]",$persos);
		$totlml = sizeof($listmail);
	}	
	$listmail = array_unique(array_values ($listmail));
	
	if($typeenv != ""){		
		echo"Envoi....<hr>";
		$message="";
		$code="";

		for($d=0 ; $d<sizeof($moule_details) ; $d++){			
				$this_type = $_SESSION["dtl".$d."_type"];
				$this_name = $_SESSION["dtl".$d."_name"];
				$this_attribute = $_SESSION["dtl".$d."_attribute"];
				$this_action = $_SESSION["dtl".$d."_action"];							
				$code.="$this_type<>$this_name<>$this_attribute<>$this_action<!>
	";
				$write_name="";
				if($this_name != NULL){
					$write_name = " name='visu_$this_name' id='visu_$this_name'";
				}
				$write_attribute="";
				if($this_attribute != NULL){
					$write_attribute = " $this_attribute";
				}			
				if($this_action != NULL){
					if(!ereg("http://",$this_action) && !ereg("@",$this_action)){
						$this_action = "http://".$this_action;
					}
					elseif(!ereg("mailto",$this_action) && ereg("@",$this_action)){
						$this_action = "mailto:".$this_action;
					}
					$message.= "<a href='$this_action' target='_blank'>";
				}			
				if($this_type == 'texte'){							
					//$message.= trim(nl2br($write_attribute));
					if(!ereg("</",		$write_attribute)){					
										$message.= trim(nl2br($write_attribute));
									}
									else{					
										$message.= trim($write_attribute);
									}
				}
				elseif($this_type == 'piece'){							
					$message.= trim($write_attribute);
				}
				elseif($this_type == 'img'){
					$src=trim(str_replace("src=","",$write_attribute));								
					$message.= "\n<$this_type $write_name src='$src' border='none'>\n";
				}
				elseif($this_type == 'frame'){
					$sizeof = split("x",$write_attribute);
					$sw = $sizeof[0];
					$sh = $sizeof[1];
					$message.="\n<iframe width='$sw' height='$sh' src='$this_action'>$this_action</iframe>\n";
				}
				
				else{
					$message.= "\n	<$this_type $write_name $write_attribute>\n";
				}			
				if($this_action != NULL){
					$message.= "</a>";
				}							
			}
		$messago = str_replace("'","''",$message);
		$code = str_replace("'","''",$code);
		$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
		$secure = md5($object.date('YmdHis'));
		if(mysql_query("INSERT INTO `$lalierp` (`ref`, `sujet`, `date`, `message`, `code`,`dests`,`secure`, `moule`, `active`)  VALUES 
		('$r_id','".str_replace("'","''",$object)."',NOW(),'$messago','$code','".implode("\n",$listmail)."','$secure','$prevurl$moule','1')")){
			$regid=mysql_insert_id();			
			$envnow=true;
			$urltp = explode('/',getenv("SCRIPT_NAME"));
			$urlsent=urlencode("http://".$prov.'/'.$urltp[1]."/bin/_lalie_async.php?i=$regid&s=$secure&f=$u_email&d=$de");
			if(is_file('bin/_lalie_async.php')){
				$ar = fopen("http://urbancube.fr/adeli/autocron/autocron_reg.php?ref=$x_id&date=$e_date%20$e_heure&alerte=$u_email&url=$urlsent","rb");
				if($ar){
					if(fread($ar,3)=='<1>'){
						$envnow=false;
						echo"<script language='javascript'>document.location='./?option=lalie&part=archives';</script>";
					}
					while (!feof($ar)) {
						$buffer = fgets($ar, 4096);
						echo $buffer;
					}
						
				}
				else{
					echo"L'envoi n'a pas pu être programmé";	
				}
			}
			$envnow=false;
			if($envnow==true){		
				echo"<center><span class='devant'><br>
				$typeenv <b>$object</b><br>
				Envoi du message <b><span id='celuiml'>0</span></b> sur <b>$totlml</b>.<br><br><img src='http://www.adeli.wac.fr/vers/$vers/lalie/envoi.gif' alt='envoie des messages en cours'><br><br>
				<b>Ne rechargez pas la page et n'utilisez pas les fonctions page suivante ou page précédente pendant ce temps. Merci.</b></span></center>";		
				$eol="\n";
				$now = time();
				$headers .= "From: $de <$u_email>".$eol;
				$headers .= "Reply-To: $de <$u_email>".$eol;
				$headers .= "Return-Path: $de <$u_email>".$eol;    
				$headers .= "Message-ID: <".$secure."@".$_SERVER['SERVER_NAME'].">".$eol;
				$headers .= "X-Mailer: PHP v".phpversion().$eol;         
				$mime_boundary="----=_NextPart_".md5(time());
				$headers .= 'MIME-Version: 1.0'.$eol;
				$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"; Content-Transfer-Encoding: quoted-printable; boundary=\"".$mime_boundary."\"".$eol;
					/*echo"<!--";
					print_r($totlml);
					echo"-->";	*/
				$e=0;
				$path = substr(getenv('SCRIPT_NAME'),0,strrpos(getenv('SCRIPT_NAME'),"/"));
				$messi = str_replace(" src=\"img/"," src=\"http://$prov$path/img/",$message);
				$messi = str_replace(" src='img/"," src='http://$prov$path/img/",$messi);
				$messi = str_replace("$"."object",urlencode($object),$messi);
				$messi = str_replace("$"."objet",urlencode($object),$messi);
				
				$res_field = mysql_list_fields($dbase,$dblist);
				$columns = mysql_num_fields($res_field);
				
				
				$rapport="";
				for($m=0 ; $m<$totlml ; $m++){
					$email = trim($listmail[$m]);
					if($email != "" && substr_count($email, '@')==1 ){
						
						$mess = $messi;
						
						/*echo"				
		<!-- emaig $email -->				
						";*/
					
					
						$rem = mysql_query("SELECT * FROM `$dblist` WHERE 1 $wherelalaie AND `email`='$email' AND `groupe`='desinscrits'");
					   if(!$rem || mysql_num_rows($rem)==0){
							$rem = mysql_query("SELECT * FROM `$dblist` WHERE 1 $wherelalaie AND `email`='$email' LIMIT 0,1");
						   $rom = mysql_fetch_object($rem);		 	 			 		   
						   for ($i = 0; $i < $columns; $i++) {
								$field_name = mysql_field_name($res_field, $i);
								$field_var = $rom->$field_name;
								//echo"\n<!-- REPLACE  $field_name : $field_var -->";
								$mess = str_replace('$'.$field_name,$field_var ,$mess);
						   }
						 
					   
							$e++;
								$msg_txt = trim(stripslashes(strip_tags($mess)))."\n\n\n\n\n\n\n se désincrire: http://www.lalie.wac.fr/?desinsc=$x_id \nsignaler un abus: http://www.lalie.wac.fr/?abus=$x_id";
								
								$msg_html = stripslashes($mess)."<a href='http://www.lalie.wac.fr/?desinsc=$x_id&email=$email' target='_blank'><font color='999999' size='1'>se désincrire</font></a>";
								if(is_file('bin/_lalie_trace.php')){
									$msg_html.= "<img src='http://$prov$path/bin/_lalie_trace.php?r=$regid&m=$email' />";
								}
								$msg="";
								
								/*$msg .= "Content-Type: text/html".$eol;
								$msg .= "charset=\"iso-8859-1\"".$eol;
								$msg .= "Content-Transfer-Encoding: 8bits".$eol;*/
								$msg .= $msg_html.$eol.$eol;
								
								
			
							echo"<script language='javascript'>document.getElementById('celuiml').innerHTML='$e';</script>";
							if(mail($email,$object,$msg,$headers)){
								$rapport.="<div class=lok>$email</div>";
							}
							else{
								$rapport.="<div class=lno>$email</div>";
							}	
						}
						else{
							$rapport.="<div class=lno>$email (Désinscrit)</div>";
						}
					}
				}	
				if( mysql_query("UPDATE `$lalierp` SET `rapport`='".str_replace("'","''",$rapport)."',`active`=1 WHERE `id`='$regid'") ){
					echo"<script language='javascript'>document.location='./?option=lalie&part=archives&id=$regid';</script>";
				}
				else{
					$return.=returnn("la lettre a bien été envoyée, mais le rapport a échoué","990000",$vers,$theme);
				}					
				
			}
		}
		else{
			echo"La lettre n'a pu être enregistrée ni envoyée...";	
		}
		deconnecte($conn);	
	}
	else{
			echo"Cette lettre comporte des erreurs et ne sera pas envoyée...";
	}
}
}
}
	
}
else{
	echo"
	Vous n'avez pas renseigné d'email pour l'envoi...<br>
	Veuillez compléter votre compte en accédant à la <a href='./?option=reglages&compte'>personnalisation</a>
	";
}
echo"</td></tr></table></td></tr></table>";

//}
//echo'
?>