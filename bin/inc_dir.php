<?php // 78 > explorateur de Répertoires ;
$d = $_GET['d'];
		if(!ereg("\.\.",$d)){
		
		if(isset($_GET['deldir'])){
		if(deldir($_GET['deldir'])){
				$return.=returnn("effacement de dossier effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("l'effacement de dossier a échouée","990000",$vers,$theme);
			}
		}
		if(isset($_GET['rena']) && isset($_POST['nna']) && $_GET['rena']!="" && $_POST['nna']!=""  && !ereg("/",$_GET['rena']) && !ereg("/",$_POST['nna']) && !ereg("\.\.",$_GET['rena']) & !ereg("\.\.",$_POST['nna'])){
		$rena = $_GET['rena'];
		$nna = correcname($_POST['nna']);
			if(rename("../$dirfiles[$part]/$d/$rena","../$dirfiles[$part]/$d/$nna")){
				if(is_file("../$dirfiles[$part]/$d/-$rena.mta")) rename("../$dirfiles[$part]/$d/-$rena.mta","../$dirfiles[$part]/$d/-$nna.mta");
				$return.=returnn("nommage effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("le nommage a échouée","990000",$vers,$theme);
			}
		}
		if(isset($_GET['re']) && isset($_GET['ne']) && $_GET['re']!="" && $_GET['ne']!=""  && !ereg("/",$_GET['re']) && !ereg("/",$_GET['ne']) && !ereg("\.\.",$_GET['re']) & !ereg("\.\.",$_GET['ne'])){
		$rena = $_GET['re'];
		$nna = correcname($_GET['ne']);
			if(rename("../$dirfiles[$part]/$d/$rena","../$dirfiles[$part]/$d/$nna")){
				if(is_file("../$dirfiles[$part]/$d/-$rena.mta")) rename("../$dirfiles[$part]/$d/-$rena.mta","../$dirfiles[$part]/$d/-$nna.mta");
				$return.=returnn("nommage effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("le nommage a échouée","990000",$vers,$theme);
			}
		}
		if(isset($_GET['rema'])){
		$rema = $_GET['rema'];
		$mta = ereg_replace('"',"",stripslashes($_POST['mta']));
		$mp = fopen("../$dirfiles[$part]/$d/-$rema.mta","w+");
			if(fwrite($mp,$mta)){
				$return.=returnn("modification effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la modification a échouée","990000",$vers,$theme);
			}
		}
		
		
		
		if(!ereg("/",$d)){
			$d="/$d";
		}
		$d = ereg_replace("//","/",$d);
		$do = split("/",$d);
		$depth=substr_count($d,"/");
		if(strlen($d)==1){
			$depth=0;
		}
		$backs = "";
		$linko='';
		for($di=1 ; $di<sizeof($do) ; $di++){
			if($di>1){
				$backs = "/$backs/";
			}
			$backs .= $do[$di];
			if($do[$di]!=""){
				$linko.="<a href='./?option=$option&part=$part&d=$backs'>$do[$di]</a>/";
			}
		}
		$aftershave = urlencode("./?option=$option&part=$part&d=$d");
		
		
		insert("upload");
		
		
	echo"
	<style>
		#diri div{
			display:block;width:200px;height:170px;float:left;text-align:center;vertical-align:middle;overflow:scroll;
		}
		#diri div td{
			text-align:left;
			vertical-align:top;
			font-size:12px;
		}
		
		
		
		
		#dirl div{
			display:block;text-align:center;vertical-align:middle;
		}
		#dirl div table{
			width:100%;
		}
		#dirl div td{
			width:30%;
			text-align:left;
			vertical-align:top;
			font-size:12px;
		}
		#dirl div br{
			display:none;
		}
		#dirl div form{
			color:#999;
		}
	</style>
		<table cellspacing='0' cellpadding='2' width='100%' class='cadrebas'>
		<tr>
		";
		if( !isset($depth_dir[$part]) || ($depth < $depth_dir[$part])){
			echo"<td class='buttontd' width='120'><a href='#' onclick=\"makedir()\">Nouveau dossier</a>";
		}
		else{
			echo"<td class='buttontd' width='1'>";
		}
		echo"</td><td class='buttontd' width='120'><a href='#' onclick=\"openfile()\">Nouveau fichier</a>
		</td>
		<td class='buttontd' style='text-align:left'>";
			if(isset($_GET['al'])){
				$al = $_GET['al'];
				set_pref("list.$part.conf",$al);
			}
			$al=set_pref("list.$part.conf");
			if($al==""){
				$al = "i";
			}
			$list_of_views = array('list','icon');
			foreach($list_of_views as $this_view){
				$border='none';
				$this_view_id = substr($this_view,0,1);
				if($this_view_id ==$al) $border='1';
				echo"<a href='./?option=$option&part=$part&al=$this_view_id&d=$d'><img src='$style_url/img/view-$this_view.png' border='$border' alt='affichage $this_view'></a> ";
			}
		echo"</td></tr>		
		<tr><td colspan='3'><br>
		<table width='100%'><tr>
		<td align='left'>
		<img src=\"http://www.adeli.wac.fr/icos/dir.gif\" alt=\"$entry\" border=\"none\">
		<a href='./?option=$option&part=$part&d='>$dirfiles[$part]</a>/$linko
	<hr>";

		echo"<script language='javascript' type='text/javascript'>";
		if(is_file("bin/upload.php")){
			echo"
			function openfile(){
				da = new Date();
				fenfi = da.getTime();
				open('bin/upload.php?dir=$dirfiles[$part]/$d&refreshafter=$aftershave',fenfi,'width=300,height=100');
			}
			function makedir(){
				da = new Date();
				fenfi = da.getTime();
				open('bin/upload.php?makedir&dir=$dirfiles[$part]/$d&refreshafter=$aftershave',fenfi,'width=300,height=100');
			}";
		}
		else{
			echo"
			function openfile(){
				da = new Date();
				fenfi = da.getTime();
				open('./?incpath=upload.php&dir=$dirfiles[$part]/$d&refreshafter=$aftershave',fenfi,'width=300,height=100');
			}
			function makedir(){
				da = new Date();
				fenfi = da.getTime();
				open('./?incpath=upload.php&makedir&dir=$dirfiles[$part]/$d&refreshafter=$aftershave',fenfi,'width=300,height=100');
			}";
		}
		echo" 	
		
	
	function renam(old,fic){
		if(!fic) fic=false;
		ne = prompt(\"Veuillez saisir le nouveau nom de fichier\",old);
		ok=0;
		if(ne!='' && ne!=old){
			exto = old.substr(old.lastIndexOf('.'),old.length);
			extn = ne.substr(ne.lastIndexOf('.'),ne.length);
			if(!fic && exto!=extn){
				paspa = confirm(\"êtes vous sur de vouloir modifier\\nl'extension du fichier de\\n\"+exto+\" à \"+extn+\" ?\\n\\nCeci peut rendre le fichier inutilisable.\");
				if(paspa){
					ok=1;
				}
			}
			else{
				ok=1;
			}
		}
		if(ok==1){
			document.location='./?option=$option&part=$part&d=$d&re='+old+'&ne='+ne;
		}
	}
	</script>
		</td><td align='right'>";
		
		echo"</td></tr></table>
		<br><br><br>
		<div id='dir$al'>";
		$liste = array();	
		$f=0;
		if(function_exists('scandir')){
			$liste = scandir("../".$dirfiles[$part].$d);
		}
		else{
			$dir = dir("../".$dirfiles[$part].$d);
			while($entry = $dir->read()){
				array_push($liste,$entry);
			}
			reset($liste);
			sort($liste);
		}
		
			
		foreach($liste as $entry){
			$entrym=$entry;
			if(substr($entry,0,1) != "." && substr($entry,0,1) != "-" ){
				$file_extension = strtolower(substr(strrchr($entry,"."),1));
				$path = "../$dirfiles[$part]$d/$entry";
				$meta_path = "../$dirfiles[$part]$d/-$entry.mta";
				if(!file_exists($meta_path)){
					//@fopen($meta_path,"w+");
					$meta_content = "";
				}
				else{
					$mp = fopen($meta_path,"rb");
					$meta_content = fread($mp,filesize($meta_path));
				}
				
				$f++;
				$path = str_replace('//','/',$path);
				
				if(is_dir($path)){	
						
							
						echo"<div class='dirliste' ondblclick=\"document.location='./?option=$option&part=$part&d=$d/$entry'\" ><table><tr>
						<td>
							<a href='./?option=$option&part=$part&d=$d/$entry'>
								<img src=\"http://www.adeli.wac.fr/icos/dir.gif\" alt=\"$entry\" border=\"none\"/><br/>
								<b>$entry</b>
							</a>
						</td>
						<td>
							<a href='#' onclick='renam(\"$entry\",true)'>
							<img src=\"$style_url/lalie/renomer.png\" alt=\"renomer\" border=\"none\" height=\"16\"> 
							Renomer</a>
							<br>
							<a href='./?option=$option&part=$part&deldir=$dirfiles[$part]$d/$entry'>
							<img src=\"$style_url/$theme/f_trash.gif\" alt=\"supprimer\" border=\"none\"> 
							Supprimer</a>
						</td>";
						if($al=='i') echo"</tr><tr>";
						echo"
						<td colspan='2'>";
							if(isset($meta_dir[$part]) && $meta_dir[$part]==="oui"){
								echo"
								<form action='./?option=$option&part=$part&d=$d&rema=$entrym' method='post' name='mo$f'>	
								description:<br>
								<input type='text' name='mta' value=\"$meta_content\" style='width:150px'>
								<a href='#' onclick='mo$f.submit()'>ok</a>
								</form>";
							}
							echo"
						</td>
						</tr></table>
						</div>";	
				}
				
				else{ //if(is_file($path)){
					echo'<!--';
					$statentry = stat($path);
					$size = getimagesize($path);
					$time = date ("d", $statentry[9])." ".$NomDuMois[date ("n", $statentry[9])]." ".date ("Y", $statentry[9]);
					$poids = $statentry[7];
						if($poids >1000000){
							$poids/=1000000;
							$poids=round($poids,2);
							$poids.="</b> Mo";
						}
						else{
							$poids/=1000;
							$poids=round($poids,2);
							$poids.="</b> Ko";
						}		
						$tail="height";
							if($size[1] < $size[0]){
								$tail="width";
							}			
					echo"-->";
					if($al=='i'){
						echo"<div class='dircontext' onmouseover=\"this.style.overflow='scroll';\" onmouseout=\"this.style.overflow='hidden';\"><table><tr><td>";
					}
					else{
						echo"<div class='dirliste'><table><tr><td>";
					}
					$edition="";
					
					if( in_array($file_extension,$imacool) ){
						echo"<a href='$path' class='vernissage' target='_blank'><img src='./?incpath=_ima.php&file=$dirfiles[$part]$d/$entry' alt='icone' $tail='50' class='cadre'></a>";
						if( in_array("picto",$opt) && is_file("bin/_picto.php") ){
								$edition="<br>
								<a href='#' onclick=\"javascript: open('./?incpath=bin/_picto.php&fichier=$dirfiles[$part]$d/$entry','picto','width=650,height=500,resizable=1')\">
								<img src='$style_url/img/picto.gif' alt='éditer avec picto' border='none' width='20'> Retoucher</a>";
							}
							
					}
					else{
						echo"<a style='cursor:pointer' onclick=\"file=open('$path','file','width=100,height=100,resizable=1');file.focus();\" ><img src=\"http://www.adeli.wac.fr/icos/$file_extension.gif\" alt=\"$entry\" border=\"none\"></a>";
					}
					$entro=$entry;
					$entry = ereg_replace("[-_]"," ",$entry);
						
							echo"<a href='#' onclick=\"open('$path','file','width=100,height=100,resizable=1');file.focus();\"><b>$entry</b></a>
							<br>$poids $time
						</td>
						<td>
							<a href='./?incpath=download.php&file=$path'>
							<img src=\"$style_url/$theme/f_down.gif\" alt=\"télécharger\" border=\"none\"> Télécharger</a>	
							$edition
							<br>
							<a href='#' onclick='renam(\"$entro\")'>
							<img src=\"$style_url/lalie/renomer.png\" alt=\"renomer\" border=\"none\" height=\"16\"> Renomer</a>
							<br>													
							<a href=\"#\" onclick=\"delfile('".addslashes(str_replace('../','</',$path))."')\">
							<img src=\"$style_url/$theme/f_trash.gif\" alt=\"supprimer\" border=\"none\"> supprimer</a>
							
						</td>";
						if($al=='i') echo"</tr><tr>";
						echo"<td colspan='2'><form action='./?option=$option&part=$part&d=$d&rema=$entrym' method='post' name='mo$f'>";
						if(isset($meta_dir[$part]) && $meta_dir[$part]==="oui"){
							echo"
								
							description:<br>
							<input type='text' name='mta' value=\"$meta_content\" style='width:150px'>
							<a href='#' onclick='mo$f.submit()'>ok</a>";
						}
						echo"<br>
						lien :<br>
						<input type='text' readonly value=\"http://$prov/".substr($path,3,strlen($path))."\" style='width:150px' onfocus='this.select()'>
						
						</form>		
					</td></tr></table>
					</div>";				
				}
			}		
		}	
		}	
		echo"	</div>
		</td></tr>
		</table>";
 ?>