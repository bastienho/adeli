<?php // 1416 > Gestion de comptes clients ;

if($part!="clients" && $part!="adeli_messages" && $part!="adeli_message_template"){
	insert('_site');
	include('bin/_site.php');
}
else{

insert('_transfert');
echo"<script language='javascript'>
	   	resizteaxtarea=true;
	</script>";

	
$alias["adeli_messages"]["dest"]="destinataire";
$alias["adeli_messages"]["prov"]="expéditeur";
$alias["adeli_messages"]["text"]="message";
$alias["clients"]["adres"]="adresse";
$alias["clients"]["last"]="dernière connexion";
$alias["clients"]["text"]="message";

$types["adeli_messages"]="txt";

$stat_message = array("non lu","lu","répondu");
if($part=="" && isset($_GET["part"]) && $_GET["part"]!=""){
	$part = $_GET["part"];
}

$tabledb = $part;	
$comportement = split(",",$types[$part]);

if(in_array("compta",$opt) && isset($compta_base)){
	$_SESSION['pdf_base']=$base;
	$_SESSION['pdf_$host']=$host;
	$_SESSION['pdf_$login']=$login;
	$_SESSION['pdf_$passe']=$passe;
	$_SESSION['compta_base']=$compta_base;
	
	echo"<div id='compta_valid_form' style='width:300px;height:200px;visibility:hidden;position:absolute;z-index:150'>
		<form action='./' method='get' name='validexp'>
		<input type='hidden' name='option' value='site'>
		<input type='hidden' name='adeli_messages' value='1'>
		<input type='hidden' name='dest' value='$edit'>
		<input type='hidden' name='sujet' value='Expédition de votre commande'>
		<input type='hidden' name='message' value=''>
		<input type='submit' value='enregistrer'>
		</form>			  
	</div>";
	insert('_compta_pdf');
	if(is_file('bin/_compta_pdf.php')){
		$openpdf='./?incpath=bin/_compta_pdf.php&1';
	}
	else{
		$openpdf='http://www.adeli.wac.fr/vers/$vers/update.php?file=_compta_pdf.php?1';
		//include("http://www.adeli.wac.fr/vers/$vers/update.php?file=$incfich.php");
	}
}

if($part != ""){
if( !isset($comportement) || (sizeof($comportement)==1 && $comportement[0]=="") || in_array("txt",$comportement) ){	

if( connecte($base, $host, $login, $passe)){
	//mysql_close($conn);	
	
	$conn = connecte($base, $host, $login, $passe);
		$verifupdt = mysql_query("DESC `$tabledb`");
		$allchamps = array();
		while($ro = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$ro->Field);
		}
		if(!in_array("clon",$allchamps)){
			mysql_query("ALTER TABLE `$tabledb` ADD `clon` BIGINT NOT NULL");
		}
		if(!in_array("active",$allchamps)){
			mysql_query("ALTER TABLE `$tabledb` ADD `active` INT( 1 ) NOT NULL ;");
		}
	@mysql_close($conn);	
	
	if(isset($_GET['unsetclon'])){
		if($u_droits == '' || $u_active == 1 ){
			$conn = connecte($base, $host, $login, $passe);
			$unsetclon = $_GET['unsetclon'];
			$res = mysql_query("SELECT `clon` FROM `$tabledb` WHERE id='$unsetclon'");
			$ro = mysql_fetch_object($res);
			$ref = $ro->clon;
			if( mysql_query("UPDATE `$tabledb` SET `clon`='0' WHERE id='$unsetclon'") && deletefromdb($base,$part,$ref)){
				$return.=returnn("mise à jour effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise à jour a échouée","990000",$vers,$theme);
			}
			mysql_close($conn);
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre en ligne ce texte","990000",$vers,$theme);
		}
	}
	
	if($part=="adeli_messages" && isset($_GET["add"])){
		$clid = $_POST["dest"];
		$sujet = stripslashes($_POST["sujet"]);
		$texte = stripslashes($_POST["text"]);
		$conn = connecte($base, $host, $login, $passe);
		$ros = mysql_query("SELECT `nom`,`email` FROM `clients` WHERE `id`='$clid'");
		$rows = mysql_fetch_object($ros);
		$nom = $rows->nom;
		if($rows->email!=""){
			$easysend=false;
			$res = mysql_query("SELECT * FROM `$mail_base` WHERE `active`=1 AND (`only`='0' OR `only`='$u_id') AND `adresse`='$u_email'");
			if($res && mysql_num_rows($res)>0){
				$ro = mysql_fetch_object($res);
				$b_serveur = $ro->serveur;
				$b_port = $ro->port;
				$b_dossier = $ro->dossier;
				$b_login = $ro->login;
				$b_pass = $ro->pass;
				$mbox = imap_open("\{$b_serveur:143/imap/notls}INBOX.sent-mail","$b_login","$b_pass");						
				if($mbox==NULL){
					$easysend=true;
				}
				else{
					
					
					
						$oEmail = new SimpleMail ();
						$oEmail->From = "$u_nom<$u_email>";
						$oEmail->To = split("[,;]",stripslashes($_POST['message_dest_email']));
						$oEmail->Bcc = array();
						
						$oEmail->Subject = $sujet;
						
						$oEmail->addBody ("<html><head></head><body>".$texte."</body></html>",'text/html','ISO-8859-1');
						
						$oEmail->send ();
					
						$return.=returnn("message envoyé à $nom ".$rows->email,"009900",$vers,$theme);
	
						$mail = "Subject:$sujet\n".$oEmail->getMessage ();
						
						if(imap_append($mbox,"\{$b_serveur:143/imap/notls}INBOX.sent-mail",$mail,"\\Seen")){
							$return.=returnn("Le message a été enregistré dans les éléments envoyés !","009900",$vers,$theme);
						}
						else{
							$return.=returnn("Le message n'a pas pu être sauvegardé !","009900",$vers,$theme);
						}
						
					
				}
				
			}
			else{
				$easysend=true;
			}
			if($easysend==true){
				if(ereg("</",$texte)){
					$head=  "Content-Type: text/html;charset=ISO-8859-1\nFrom: $u_nom<$u_email>";
				}
				else{
					$head = "from: $u_nom<$u_email>";
				}
				if(mail(stripslashes($_POST['message_dest_email']),$sujet,$texte,$head)){
					$return.=returnn("message envoyé à $nom ".$rows->email,"009900",$vers,$theme);
				}
			}
			
		}
		mysql_close($conn);
	}
	
		if(isset($_GET['indep'])){
		if($u_droits == '' || $u_active == 1 ){
		 $conn = connecte($base, $host, $login, $passe);
			if( mysql_query("UPDATE `$tabledb` SET `clon`='0' WHERE id='$edit'")){
				$return.=returnn("mise à jour effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise à jour a échouée","990000",$vers,$theme);
			}
			mysql_close($conn);
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre en ligne ce texte","990000",$vers,$theme);
		}
	}
	
	if(isset($_GET['setvalid'])){
		if($u_droits == '' || $u_active == 1 ){
			$conn = connecte($base, $host, $login, $passe);
			$setvalid = $_GET['setvalid'];
			if( mysql_query("UPDATE `$tabledb` SET `active`='1' WHERE id='$setvalid'") ){
				$return.=returnn("mise en ligne effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise en ligne a échouée","990000",$vers,$theme);
			}
			mysql_close($conn);
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre en ligne ce texte","990000",$vers,$theme);
		}
	}
	if(isset($_GET['unsetvalid'])){
		if($u_droits == ''){
			$conn = connecte($base, $host, $login, $passe);
			$unsetvalid = $_GET['unsetvalid'];
			if( mysql_query("UPDATE `$tabledb` SET `active`='0' WHERE id='$unsetvalid'") ){
				$return.=returnn("mise hors ligne effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la mise hors ligne a échouée","990000",$vers,$theme);
			}
			mysql_close($conn);
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre hors ligne ce texte","990000",$vers,$theme);
		}
	}
	if(isset($_GET['rename']) && isset($_GET['in']) && isset($_GET['en'])){
		if($u_droits == ''){
			$conn = connecte($base, $host, $login, $passe);
			$rename = $_GET['rename'];
			$in = $_GET['in'];
			$en = $_GET['en'];
			if( mysql_query("UPDATE `$tabledb` SET `$in`='$en' WHERE `$in`='$rename'") ){
				$nbaf = mysql_affected_rows();
				$return.=returnn("renomage effectué avec succès<br>($nbaf champs affectés)","009900",$vers,$theme);
			}
			else{
				$return.=returnn("le renomage a échouée","990000",$vers,$theme);
			}
			mysql_close($conn);
		}
		else{
			$return.=returnn("Vous n'avez pas les droits pour mettre hors ligne ce texte","990000",$vers,$theme);
		}
	}

	$filepart = ereg_replace(">","-",$part);
	if(file_exists("$filepart.php")){
		echo"<a class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mesure.gif'>
				<span>Partie sur mesure</span></a>";
		$conn = connecte($base, $host, $login, $passe);
		include("$filepart.php");
		mysql_close($conn);
	}
	else{	
	if(isset($_GET['edit'])){		
		$action="update";		
		if(!isset($_GET['add'])){
			$edit = $_GET['edit'];
		}
		if($edit == '' || isset($_GET['clone']) || isset($_GET['new'])){
			$action='add';
		}
	}
	
	$conn = connecte($base, $host, $login, $passe);
	
	$res_field = mysql_list_fields($base,$tabledb);
   	$columns = mysql_num_fields($res_field);
	
if(isset($_GET['s'])){
	$sa = $_GET['s'];
	$va = $_GET['v'];
	$_SESSION[$sa] = $va;
}	


		echo"<script language='javascript' type='text/javascript'>
		var incwh='';
			function exporter(unik){
				if(document.listage){
					document.listage.action='./?opion=$option&$part&exporter&incwhere='+incwh;
					bbsel='';
					var allche = document.listage.getElementsByTagName(\"input\");
					for (var i=2; i<allche.length; i++) {
						if(allche[i].checked==true && allche[i].className!='noche'){
							bbsel+=allche[i].name;
						}
					}
					if(bbsel!=''){
						document.listage.action+='&selected='+bbsel;
					}
					document.listage.submit();
				}
				else if(unik!=null){
					document.location='./?option=$option&$part&exporter&incwhere='+incwh+'&selected=sel'+unik;
				}
				else{
					document.location='./?option=$option&$part&exporter&incwhere=".urlencode($incwhere)."';
				}
				
			}
		 </script>
		 <table cellspacing='0' cellpadding='3' border='0' width='100%'><tr style='height:20px;'><td class=\"buttontd\"  width=\"10\">&nbsp;</td>";
		 $is_liste=false;
		 if(!in_array("nonew",$comportement) && (!isset($u_restreint) || $u_restreint[1]!=$part )){
			$submen = array("liste"=>"liste","edit"=>"nouveau","importer"=>"importer","exporter"=>"exporter","statistiques"=>"statistiques");	
		 }
		 else{
			$submen = array("liste"=>"liste","exporter"=>"exporter","statistiques"=>"statistiques");	
		 }
		$mensub = array_keys($submen);
		$i=0;
		foreach($submen as $k=>$v){
			$gm = isget($mensub);
			if(isset($_GET[$k]) || ($i==0 && ( $gm==false || $gm=='liste' ))){
				if( $gm==false || $gm=='liste' ) $is_liste=true;				
				if($k=='edit' && is_numeric($_GET['edit'])){
					echo"<td class=\"menuselected\" width='80'><a href=\"./?option=$option&part=$part&option=$option&$k=".$_GET['edit']."\">Edition</a></td>";
				}
				else{
					echo"<td class=\"menuselected\" width='80'><a href=\"./?option=$option&part=$part&option=$option&$k\">".ucfirst($v)."</a></td>";
				}
			}
			elseif($v=='exporter'){
				echo"<td class=\"buttontd\" width='80'><a href=\"#\" onclick='exporter()'>".ucfirst($v)."</a></td>";
			}
			else{
				echo"<td class=\"buttontd\" width='80'><a href=\"./?option=$option&part=$part&option=$option&$k\">".ucfirst($v)."</a></td>";
			}
			$i++;
		}
		
		echo"<td class=\"buttontd\" align='left'><table width='100%'><tr><td align='left'><p align='left'>&nbsp;";
		
		if(!isset($_GET['edit']) && !isset($_GET['exporter'])){	
		
			if(isset($_GET['al'])){
				$al = $_GET['al'];
				$fp = fopen("mconfig/$u_id.list.$part.conf","w+");
				fwrite($fp,$al);
				fclose($fp);
			}
			else{
			 if(!is_file("mconfig/$u_id.list.$part.conf")){
					$fp = fopen("mconfig/$u_id.list.$part.conf","w+");
					fwrite($fp,"l");
					fclose($fp);
				}
				else{
					$fp = fopen("mconfig/$u_id.list.$part.conf","r");
					$al = trim(fread($fp,5));
					fclose($fp);
				}
			}
			if($al==""){
				$al = "l";
			}
		
		if($al==="i"){
			echo"
			<a href='./?option=$option&part=$part&al=l'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/view-list.png' border='none' alt='affichage liste'></a>
			<a href='./?option=$option&part=$part&al=i'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/view-icon.png' border='1' alt='affichage icônes'></a>";
		}
		else{
			echo"
			<a href='./?option=$option&part=$part&al=l'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/view-list.png' border='1' alt='affichage liste'></a>
			<a href='./?option=$option&part=$part&al=i'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/view-icon.png' border='none' alt='affichage icônes'></a>";
		}
		}
		//echo"</p></td></tr>		<tr><td align='center' colspan='6' class='cadrebas'>";
		echo"</p></td><td align='right'>";
		//if(!isset($_GET['edit'])){
		echo"<form action='./' method='get' name='search'><input type='hidden' name='$part'>
		<table cellpadding='0' cellspacing='0' border='0'><tr>
		<td><a href='./?option=$option&part=$part' style='background:#FFFFFF;padding:2px;height:20px;border:#000000 thin solid;border-right-width:0px'>x</a></td><td><input type='text' name='d' value='";
		if($d!=''){
			echo $d;
		}
		else{
			echo 'recherche';
		}
		echo"' onfocus='this.value=\"\"' style='background:#FFFFFF;width:60px;padding:2px;height:20px;border:#000000 thin solid;border-right-width:0px;border-left-width:0px'></td><td><input type='submit' value='ok' style='background:#FFFFFF;padding:2px;height:20px;border:#000000 thin solid;border-left-width:0px;font-weight:bold;'></td></tr></table></form>";
		//}
		echo"</p></td></tr></table></td></tr>
		<tr><td align='center' colspan='8' class='cadrebas'>";
		$is_printaffich=0;
		$modifouille="";
		
	if($is_liste==true){		
	for ($i = 0; $i < $columns; $i++) {
		$field_name = mysql_field_name($res_field, $i);
		$field_act = $field_name;
		if(isset($r_alias[$part][$field_name])){
			$field_act = $r_alias[$part][$field_name];
		}
		if(substr(strrev($field_act),0,3)=='hc_'){
			$mot = explode('_',strrev($field_act),4);	
			$mot = strrev($mot[3]);		
		}
		else{			
			$mot = explode('_',strrev($field_act),3);	
			$mot = strrev($mot[2]);		
		}
		$fieldoption = substr($field_act,strlen($mot)+1,strlen($field_act));
		//$fieldoption = substr($field_name,strpos($field_name,"_")+1,strlen($field_name));
		if(substr($field_act,0,1) == "_"){
			$field_named = substr($field_act,1,strlen($field_act));
			if($is_printaffich==0){
				$is_printaffich=1;
				echo"<span class='gras'>Filtrer les résultats : </span> ";
			}
			$modifouille.="<select onchange=\"changedenom('$field_named','$field_name',this.value,this)\" style=\"width:100px\"><option value=\"\" selected>$field_named</option>";
			echo"$field_named<select onchange=\"javascript:document.location='./?option=$option&part=$part&s=$field_name&v='+this.value\" style=\"width:100px\">
			<option value=\"\" selected>tou(te)s</option>";
				$allready=array();
				$listres = mysql_query("SELECT DISTINCT `$field_name` FROM `$tabledb` WHERE `$field_name`!='' ORDER BY `$field_name`");
				while($rowlist = mysql_fetch_object($listres)){
					$rowvalue = $rowlist->$field_name;
					$s="";
					if(isset($_SESSION[$field_name]) && $rowvalue == $_SESSION[$field_name] ){
						$s = "selected";
						$incwhere.=" AND `$field_name`LIKE'$rowvalue'";
					}
					echo"<option value=\"$rowvalue\" $s>$rowvalue</option>";
					$modifouille.="<option value=\"$rowvalue\">$rowvalue</option>";					
				}
			echo"</select>&nbsp;";
			$modifouille.="</select>&nbsp;";
		}
		elseif(substr($fieldoption,0,1) != "@" && ereg("_",$field_act) && !ereg("nochange",$field_act) &&  mysql_query("SHOW COLUMNS FROM $mot") && substr($field_act,-3)!='_ch' && substr($field_act,-5)!='_nlch' && substr($field_act,-5)!='_nlse' ){
		
		if($is_printaffich==0){
				$is_printaffich=1;
				echo"<span class='gras'>Filtrer les résultats : </span> ";
			}
				$refiled = $mot;//substr($field_name,0,strpos($field_name,"_"));				
				$nameifthefield = $refiled;
				$fieldoption = split("[_>]",$fieldoption);
				$fieldoptionprint = $fieldoption[1];
				
				$fieldoption = $fieldoption[0];		
				$refiled = trim($refiled);
				if(isset($alias[$part][$field_name])){
					$nameifthefield = $alias[$part][$field_name];
				}
				elseif(!isset($r_alias[$part][$field_name])){
					if($prefixe!=""){
							$nameifthefield = trim(ereg_replace($prefixe,"",$nameifthefield));
						}	
					if(ereg(">",$field_act)){
							$nameifthefield .= " ".substr($field_act,strpos($field_act,">")+1,strlen($field_act));
					}
				}

				echo "$nameifthefield<select onchange=\"javascript:document.location='./?option=$option&part=$part&s=$field_name&v='+this.value\" style=\"width:100px\">
			<option value=\"\" selected>tou(te)s</option>";
				$listres = mysql_query("SELECT `$fieldoptionprint`,`$fieldoption` FROM `$refiled` WHERE `$fieldoptionprint`!='' ORDER BY `$fieldoptionprint`");
				while($rowlist = mysql_fetch_array($listres)){
					$rowvalue = $rowlist[0];
					$rowid = $rowlist[1];
						$s = "";
						if(isset($_SESSION[$field_name]) && $rowid == $_SESSION[$field_name] ){
							$s = "selected";
							$incwhere.=" AND `$field_name`LIKE'$rowid'";
						}
						//echo"<!-- sess val ".$_SESSION[$field_name]." -->";
						echo"<option value=\"$rowid\" $s>$rowvalue</option>";
				}
			echo"</select> ";
		}
	}
	
	}
	
	if($modifouille!="" && !isset($_GET['exporter']) ){
		echo"
		<script language='javascript' type='text/javascript'>
		function changedenom(parta,part,ki,koi){
			glok = prompt(parta+\"\\nVeuillez saisir le nouvel intitulé\",ki);	
			if(glok){
				document.location='./?option=$option&part=$part&rename='+ki+'&en='+glok+'&in='+part;
			}	
			koi.value='';
		}
		</script>		
		<hr><p align='right'><span class='gras'>renomer </span> $modifouille</p>";
	
	}
	echo"
	<script language='javascript' type='text/javascript'>
	function renam(path,old){
		ne = prompt(\"Veuillez saisir le nouveau nom de fichier\",old);
		ok=0;
		if(ne!='' && ne!=old){
			exto = old.substr(old.lastIndexOf('.'),old.length);
			extn = ne.substr(ne.lastIndexOf('.'),ne.length);
			if(exto!=extn){
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
			document.fourmis.action+='&ren='+path+old.replace('&','%26')+'&nen='+ne.replace('&','%26');
			document.fourmis.submit();
		}
	}
	</script>
	
	<script language='javascript' type='text/javascript'>
	
	var clientPC = navigator.userAgent.toLowerCase(); // Get client info
	var is_gecko = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
var is_safari = ((clientPC.indexOf('applewebkit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
// For accesskeys
var is_ff2_win = (clientPC.indexOf('firefox/2')!=-1 || clientPC.indexOf('minefield/3')!=-1) && clientPC.indexOf('windows')!=-1;
var is_ff2_x11 = (clientPC.indexOf('firefox/2')!=-1 || clientPC.indexOf('minefield/3')!=-1) && clientPC.indexOf('x11')!=-1;


	function mkpl(id){
		document.getElementById('txtap'+id).style.visibility='hidden';
		var txtarea;
		var espas = /\\n/g;
		var areas = document.getElementsByTagName('textarea');
		txtarea = areas[id];
		txtarea.focus();
	}
	function ereg_replace(rep,msk,str){
		 tmp = \"\";
		 var espas = /\\s/g;
		 a = str.split(espas);		
		 for(i=0 ; i<a.length ; i++){
			 tmp += a[i].replace(rep,msk)+' ';	
		 }	
	     return tmp;
	 } 
	function prt(id){
		document.getElementById('txtap'+id).style.visibility='visible';
		var txtarea;
		var espas = /\\n/g;
		var areas = document.getElementsByTagName('textarea');
		txtarea = areas[id];
		lmn = txtarea.value;
		lmn = lmn.replace(espas,'<br />');
		
		lmn = ereg_replace('[/]',\"</span>\",lmn);
		lmn = ereg_replace(']',\"'>\",lmn);
		lmn = ereg_replace('[',\"<span class='\",lmn);

		
		lmn = \"<body onclick='parent.mkpl(\"+id+\")'><link rel='stylesheet' type='text/css' href='http://$prov/$part/style.css'/><font face='arial'>\"+lmn+\"</font></body>\";
		
		eval('ap'+id+'.document.write(lmn);');
	}
	function mkpa(id){
		eval(\"ap\"+id+\".document.location='about:blank';\");
		setTimeout(\"prt(\"+id+\")\",500);
	}

	function insertTags(tagOpen, tagClose, sampleText, texid) {
	var txtarea;
	var areas = document.fourmis.getElementsByTagName('textarea');
	txtarea = areas[texid];


	// IE
	if (document.selection  && !is_gecko) {
		var theSelection = document.selection.createRange().text;
		if (!theSelection) {
			theSelection=sampleText;
		}
		txtarea.focus();
		if (theSelection.charAt(theSelection.length - 1) == \" \") { // exclude ending space char, if any
			theSelection = theSelection.substring(0, theSelection.length - 1);
			document.selection.createRange().text = tagOpen + theSelection + tagClose + \" \";
		} else {
			document.selection.createRange().text = tagOpen + theSelection + tagClose;
		}

	// Mozilla
	} else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
		var replaced = false;
		var startPos = txtarea.selectionStart;
		var endPos = txtarea.selectionEnd;
		if (endPos-startPos) {
			replaced = true;
		}
		var scrollTop = txtarea.scrollTop;
		var myText = (txtarea.value).substring(startPos, endPos);
		if (!myText) {
			myText=sampleText;
		}
		var subst;
		if (myText.charAt(myText.length - 1) == \" \") { // exclude ending space char, if any
			subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + \" \";
		} else {
			subst = tagOpen + myText + tagClose;
		}
		txtarea.value = txtarea.value.substring(0, startPos) + subst +
			txtarea.value.substring(endPos, txtarea.value.length);
		txtarea.focus();
		//set new selection
		if (replaced) {
			var cPos = startPos+(tagOpen.length+myText.length+tagClose.length);
			txtarea.selectionStart = cPos;
			txtarea.selectionEnd = cPos;
		} else {
			txtarea.selectionStart = startPos+tagOpen.length;
			txtarea.selectionEnd = startPos+tagOpen.length+myText.length;
		}
		txtarea.scrollTop = scrollTop;

	}
	// reposition cursor if possible
	if (txtarea.createTextRange) {
		txtarea.caretPos = document.selection.createRange().duplicate();
	}
}
		</script>
	
	
	";
	mysql_close($conn);
	if(isset($_GET['edit'])){
		if( !in_array("noedit",$comportement)){
			echo"<form action='./?option=$option&part=$part&$action&edit=$edit' method='post' name='fourmis' enctype='multipart/form-data'  onsubmit=\"affichload()\">";
		}
		else{
			echo"<form action='./?option=$option&part=$part&edit=$edit' >";
		}
	}

			
		echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'><tr><td valign='top'>";
	
if(isset($_GET['edit'])){		

$ishtmlll=array();
if(isset($_GET['new'])){
	$edit='';
}	
	$conn = connecte($base, $host, $login, $passe);	
	$res = mysql_query("SELECT * FROM `$tabledb` WHERE `id`='$edit'");
	$ro = mysql_fetch_object($res);
	$nochnb =0;
	
	$fp = fopen("mconfig/$u_id.editmode.conf","rb");
			$editmode = abs(fread($fp,10));
			fclose($fp);
			if(isset($_GET['modif'])){
				$editmode=0;
				$fp = fopen("mconfig/$u_id.editmode.conf","w+");
				fwrite($fp,'0');
				fclose($fp);
			}
			if(isset($_GET['view'])){
				$editmode=1;
				$fp = fopen("mconfig/$u_id.editmode.conf","w+");
				fwrite($fp,'1');
				fclose($fp);
			}
			if($edit==''){
				echo"<i>Modification</i> | <i>Lecture</i> | <b>Nouveau</b>";
			}
			elseif($editmode==0){
				echo"<b><a href=\"./?option=$option&part=$part&amp;edit=$edit\">Modification</a></b> | <a href=\"./?option=$option&part=$part&amp;edit=$edit&view\">Lecture</a>			
				";
				if(!in_array("nonew",$comportement) && (!isset($u_restreint) || $u_restreint[1]!=$part )){
					echo" | <a href=\"./?option=$option&part=$part&amp;edit\">Nouveau</a>";
				}
			}
			else{
				echo"<a href=\"./?option=$option&part=$part&amp;edit=$edit&modif\">Modification</a> | 	<b><a href=\"./?option=$option&part=$part&amp;edit=$edit\">Lecture</a></b>			
				";
				if(!in_array("nonew",$comportement) && (!isset($u_restreint) || $u_restreint[1]!=$part )){
					echo" | <a href=\"./?option=$option&part=$part&amp;edit\">Nouveau</a>";
				}
			}
			if($edit=='') $editmode=0;
	echo"<hr>
	<script language='javascript'>
	   	resizteaxtarea=true;
	</script>
	<a name='nochange0'></a>
	<div><table width='100%'>";
	$txid=0;
	   for ($i = 0; $i < $columns; $i++) {
			$field_name = mysql_field_name($res_field, $i);
			$field_type = mysql_field_type($res_field, $i);			
			$field_length = mysql_field_len($res_field, $i);
			$field_value = '';
			if(isset($_GET['refresh'])){
				$field_value = stripslashes($_POST[$field_name]);
			}
			elseif($_GET['edit']!=''){
				$field_value = $ro->$field_name;	
			}	
			$field_width=$field_length*12;
			
			$field_act = $field_name;
			if(isset($r_alias[$part][$field_name])){
				$field_act = $r_alias[$part][$field_name];
			}
			if(substr(strrev($field_act),0,3)=='hc_'){
				$mot = explode('_',strrev($field_act),4);	
				$mot = strrev($mot[3]);		
			}
			else{			
				$mot = explode('_',strrev($field_act),3);	
				$mot = strrev($mot[2]);		
			}
			
			if($field_width > 300){
				$field_width=300;
			}
			$nameifthefield = str_replace(">"," ",$field_name);
			$nameifthefield = ucfirst(trim(str_replace("_"," ",$nameifthefield)));	
			if(isset($alias[$part][$field_name])){
				$nameifthefield = $alias[$part][$field_name];
			}
			echo"<tr><!-- $field_name $field_type $field_width -->";
			/////////////////////////////////////// ID
			 if($field_name == "id"){
			 	if(isset($_GET['clone'])){
					$field_value='';
				}
				 echo"<td>Identifiant</td><td><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"><b>$field_value</b></td>";
			 }
			 //////////////////////////////////////RESTREINT
			 elseif(isset($u_restreint) && $field_act=="$u_restreint[1]_$u_restreint[2]_$u_restreint[3]"){
			 	if($edit==''){
					$field_value = $u_d;
				}
				echo"<td width='100'>$nameifthefield</td><td><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"><b>";
				if($edit!='' && $field_value != $u_d ){
					echo"Vous n'avez pas accès à ce fichier !<br>
					<a href='./?option=$option&part=$part'>retour à la liste</a>
					<script language='javascript'>document.location='./?option=$option&part=$part';</script>
					";
					exit();
				}
				$resn = mysql_query("SELECT `$u_restreint[3]` FROM `$u_restreint[1]` WHERE `$u_restreint[2]`='$u_d'");
				$ron = mysql_fetch_object($resn);
				echo $ron->$u_restreint[3];
				echo"</b></td>";
			 }
			/////////////////////////////////////// CLONE
			 elseif($field_name == "clon"){			 	
			 	if(isset($_GET['clone'])){
					$field_value=$_GET['edit'];
				}
				$clonid = $field_value;
				 echo"<td></td><td><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"></td>";
			 }
			 /////////////////////////////////////// ACTIVE
			 elseif($field_name == "active"){
			 	$actouno = array("","checked");
				$actoudos = array("checked","");
				if($editmode==0){
					if($u_droits == '' || $u_active == 1 ){
					 echo"<td>activé</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>
						oui<input type=\"radio\" name=\"$field_name\" value=\"1\" $actouno[$field_value]>
						non<input type=\"radio\" name=\"$field_name\" value=\"0\" $actoudos[$field_value]>
					 </td>";
					}
					else{
					 echo"<td>activé</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>sans droits<input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"></td>";
					}
				}
				else{
					echo"<td>activé</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/v$field_value.gif' border='none' alt='actif: $field_value'></td>";
				}
			 }
				/////////////////////////////////////// COULEUR
			 elseif($field_name == "couleur" && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";
					}
			 	echo"<td width='100'>$nameifthefield</td><td><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>
				 	#<input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" maxlength='6' size='6' onchange=\"document.getElementById('div$field_name').style.backgroundColor='#'+this.value\">
						<div id=\"div$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:20px;width:30px;border-color:#000000;border-style:solid;border-width:1px\"></div>";
						if($editmode==0){
						echo"<a href='#a$field_name' name='a$field_name' onclick=\"choosecolor($i,'Backcolor','$field_name','hexa',event)\">changer la couleur</a>
							";	
						}						
				echo"</td>";
			 }
				///////////////////////////////////// NO CHANGE
			 elseif(substr($field_act,0,9) == "nochange_"){
			 			$nochnb++;
						$nameifthefield = str_replace("nochange_","",$field_name);
						if($field_value===0){
							$field_value='';
						}
						if($field_value!=''){
							$field_value=' : '.$field_value;
						}
						$fp = @fopen("mconfig/$u_id.ouvert.$part.noch_$nochnb.conf","a+");
						@fseek($fp,0);
						$ouvert = abs(@fread($fp,255));
						@fclose($fp);
						echo"
						
						</tr></table></div><div><table width='100%'><tr><td colspan='2' class='buttontd'>
						<a href='#nochange".($nochnb-1)."' title='titre précédent'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'></a><a href=\"#nochange".($nochnb+1)."\" title='titre suivant'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_down_off.jpg' alt='v' border='none'></a>
						<a onclick=\"sizpa('noch_$nochnb')\" style='cursor:pointer' name='nochange$nochnb'><b>$nameifthefield $field_value</b></a></td></tr>
						</table></div>
						<div id='noch_$nochnb' style='position:relative;overflow-y:hidden;width:600px;height:$ouvert"."px'><table width='590'><tr><td colspan='2'>";
				}
				
				///////////////////////////////////// WORKNET EXPEDITEUR/DESTINATAIRE
			 elseif( ($field_name=="prov" || $field_name=="dest" ) && $part=="adeli_messages"){
					if($field_value==0){ 
						$field_value_name="moi"; 
						if($field_name=="dest"){
							mysql_query("UPDATE `adeli_messages` SET `etat`=1 WHERE `id`=$edit");
						}
					}
					else{
						$ros = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$field_value'");
						$rows = mysql_fetch_object($ros);
						$field_value_name="<a href='./?clients&edit=$field_value'>".($rows->nom)."</a>"; 
					}
					if( abs($edit)!=0 || $field_name=="prov"){
						echo"<td>$nameifthefield</td><td><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"><b>$field_value_name</b></td>";
					}
					else{
						echo"<td>$nameifthefield</td><td>
						<select name='$field_name'>";
						$ros = mysql_query("SELECT `id`,`nom`,`email` FROM `clients` WHERE `nom`!='' ORDER BY `nom`");
						while($rows = mysql_fetch_object($ros)){
							$cid = $rows->id;
							$cno=$rows->nom;
							$s="";
							if(isset($_GET["dest"]) && $_GET["dest"]==$cid){
								$s="selected";
								$cem=$rows->email;
							}
							echo"<option value='$cid' $s>$cno</option>";
						}
						echo"</select><br>
						envoi par email : <input type='text' name='message_dest_email' value='$cem' style=\"width:200px;\">
						</td>";
					}
					
			 }

				
			 ///////////////////////////////////// PREFIXE
			 elseif(substr($field_act,0,1) == "_"){
				if($nameifthefield == $field_name){
					$nameifthefield = substr($field_name,1,strlen($field_name));
				}
				if($field_value==""){
					$field_value=$_SESSION[$field_name];
				}
				$nameifthefield = ucfirst(trim(str_replace("_"," ",$nameifthefield)));	
				echo"<td width='100'>$nameifthefield</td><td>";
				 if($editmode==0){
				 echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'><input type=\"text\"  id='pref_txt_$i' name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:300px;\"   onfocus=\"if(this.readonly=='true'){this.style.width='1px';document.getElementById('pref_sel_$i').style.display='inline';this.blur();document.fourmis.pref_sel_$i.focus();this.readonly='true';}\" onblur=\"this.readonly='true'\">
				 <select id='pref_sel_$i' name='pref_sel_$i' onchange=\"javascript:set$field_name(this.value);this.style.display='none';document.getElementById('pref_txt_$i').style.width='300px';\" onblur=\"this.style.display='none';document.getElementById('pref_txt_$i').style.width='300px';\" style=\"width:300px;display:none;\">
				 	<option value=''></option>";
					//document.fourmis.action=document.fourmis.action.replace(new RegExp('&update'), '&refresh').replace(new RegExp('&add'), '&refresh');document.fourmis.submit();
					$listres = mysql_query("SELECT DISTINCT `$field_name` FROM `$tabledb` $incwhere $prefixselection");
					$prefixselection.=" AND `$field_name`='$field_value'";
					while($rowlist = mysql_fetch_object($listres)){
						$rowvalue = $rowlist->$field_name;
						if(trim($rowvalue)!=''){
							$s='';
							if($rowvalue==$field_value) $s='selected';
							echo"<option value=\"$rowvalue\" $s>$rowvalue</option>";
						}
					}
					echo"
					<option value='_-_' style='font-weight:bold'>- Nouveau</option>
				 </select>
				 <script language='javascript' type='text/javascript'>
				 	document.fourmis.$field_name.readonly='true';
					function set$field_name(koi){
						if(koi == '_-_'){
							/*pro = prompt(\"veuillez entrer un nom pour le nouvel élément\",document.fourmis.$field_name.value);
							if(pro){
								document.fourmis.$field_name.value=pro;
							}	*/
							document.fourmis.$field_name.readonly='false';
							document.fourmis.$field_name.focus();
							document.getElementById('pref_sel_$i').style.display='none';
							document.getElementById('pref_txt_$i').style.width='300px';
							
						}
						else if(koi!=''){
							document.fourmis.$field_name.value=koi;
							document.fourmis.$field_name.readonly='true';
						}						
					}
				 </script>";
				 }
				 else{
				 	$listres = mysql_query("SELECT `$field_name` FROM `$tabledb` $incwhere $prefixselection AND `$field_name`='$field_value'");
					$prefixselection.="";
					$rowlist = mysql_fetch_array($listres);
					echo"$rowlist[0]";
				 }
				 echo"				 
				 </td>";
			}
			 ///////////////////////////////////// SUFIXE substr($field_act,0,strlen($field_act)-strpos(strrev($field_act),"_",2))
			 
			 elseif( ereg("_",$field_act) && ( mysql_query("SHOW COLUMNS FROM $mot") || ereg('@',$field_act)) ){
			 	$refiled = $mot;//substr($field_act,0,strpos($field_act,"_"));
				
				$fieldoption = substr($field_act,strlen($mot)+1,strlen($field_act));
				//echo "$fieldoption<br>";
				
				if($nameifthefield == ucfirst(str_replace("_"," ",$field_act))){
					$nameifthefield = ucfirst($refiled);
				}
				
				
				if(ereg(">",$field_act)){
					$fieldoption = substr($fieldoption,0,strpos($fieldoption,">"));
					$nameifthefield .= " : ".substr($field_act,strpos($field_act,">")+1,strlen($field_act));
				}
				
				
				if(substr($fieldoption,0,1) == "@"){
					$nameofoption = substr($fieldoption,1,strlen($fieldoption));	
					$field_value = $_SESSION[$nameofoption];	
					echo"<td width='100'>$nameifthefield <a class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/pile.gif'>
					<span>Ce champ est à un élément personnel de session <b>$nameofoption</b></span></a></td><td>
					 <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:100px\" class=\"bando\" readonly>
					 </td>";		
				}
				else{				
					$fieldoptions = split("_",$fieldoption);
					$fieldoptionprint = $fieldoptions[1];
					if(strpos($fieldoptionprint,'/')>-1){
						$fopa = explode('/',$fieldoptionprint);	
						$fieldoptionprint="CONCAT(' '";
						foreach($fopa as $fopv){
							$fieldoptionprint.=",' ',`$fopv`";
						}
						$fieldoptionprint.=")";
					}
					$fieldoption = $fieldoptions[0];
					$refiled = trim($refiled);	
					if($prefixe!=""){
						$nameifthefield = trim(ereg_replace($prefixe,"",$nameifthefield));
					}
					
					$sepa='site';
						
					for($m=0; $m<sizeof($menu) ; $m++){
						$spart = $menupart[$m];
						$tablo = $menu[$spart];
						if(in_array($refiled,$tablo)){							
							if(substr($spart,0,7)=='worknet') $sepa='worknet';
							if(substr($spart,0,7)=='gestion') $sepa='gestion';	
							break;
						}
					}
					echo"<td valign='top' width='100'>$nameifthefield <a class='info' href='./?option=$sepa&$refiled&edit=$field_value'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/pile.gif' border='none'>
					<span>Ce champ est relié au tableau <b>$refiled</b></span></a></td><td>";
					
					
						echo"
						 <img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-special.png' alt='special'>";
		
			if((sizeof($fieldoptions)==3 && $fieldoptions[2]=='ch') || (sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlch')){
				   
				   	if($editmode==0){
					  echo"<input type='hidden' name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\">
				  ";
						$c=0;
						$hot=46;
						$ch=0;
						$prh='';
						$hut=0;
						$seled = '';
						if(sizeof($fieldoptions)==3){
							$listres = mysql_query("SELECT $fieldoptionprint,`$fieldoption`,`id` FROM `$refiled` ORDER BY 0");
							while($rowlist = mysql_fetch_array($listres)){
								$rowvalue = $rowlist[0];
								$rowid = $rowlist[1];
								$roid = $rowlist[2];
								$se = '';
								$c++;
								if(ereg('<'.$rowid.'>',$field_value)){
									$se = 'checked';
									$seled .= "$rowvalue<br>";
									$hut+=20;
									$ch++;
								}
								$hot+=23;
								$rowvaluu = str_replace("'","\'",$rowvalue);
								$rowid = str_replace("'","\'",$rowid);
								$rowid = str_replace('"','&quot;',$rowid);
								$prh.="<li><input type='checkbox' name='cho$i$c' onclick=\"oldv=parseInt(document.fourmis.ch_cu_$i.value);if(this.checked==true){if(document.fourmis.$field_name.value.indexOf('<$rowid>')==-1){document.fourmis.$field_name.value+='<$rowid>'; document.getElementById('chu_$i').innerHTML+='$rowvaluu<br>';oldv++;}}else{document.fourmis.$field_name.value=document.fourmis.$field_name.value.replace('<$rowid>','');document.getElementById('chu_$i').innerHTML=document.getElementById('chu_$i').innerHTML.replace('$rowvaluu<br>','');oldv--;}document.fourmis.ch_cu_$i.value=oldv;hut=oldv*20;\" $se>$rowvalue <a href='./?$refiled&edit=$roid'>></a></li>";
							}
						}
						if(sizeof($fieldoptions)==2){
							$listres = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled`");
							$rowlist = mysql_fetch_array($listres);
							$gvl = explode("\n",$rowlist[0]);
							foreach($gvl as $rowvalue){
								$rowvalue=trim($rowvalue);
								$se = '';
								$c++;
								if(ereg('<'.$rowvalue.'>',$field_value)){
									$se = 'checked';
									$seled .= "$rowvalue<br>";
									$hut+=20;
									$ch++;
								}
								$hot+=23;
								$rowvaluu = str_replace("'","\'",$rowvalue);
								$rowvaluu = str_replace('"','&quot;',$rowvaluu);
								$rowid = str_replace("'","\'",$rowid);
								$prh.="<li><input type='checkbox' name='cho$i$c' onclick=\"oldv=parseInt(document.fourmis.ch_cu_$i.value);if(this.checked==true){if(document.fourmis.$field_name.value.indexOf('<$rowvaluu>')==-1){document.fourmis.$field_name.value+='<$rowvaluu>'; document.getElementById('chu_$i').innerHTML+='$rowvaluu<br>';oldv++;}}else{document.fourmis.$field_name.value=document.fourmis.$field_name.value.replace('<$rowvaluu>','');document.getElementById('chu_$i').innerHTML=document.getElementById('chu_$i').innerHTML.replace('$rowvaluu<br>','');oldv--;}document.fourmis.ch_cu_$i.value=oldv;hut=oldv*20;\" $se>$rowvalue</li>";
							}
						}
						
						if($hot>300) $hot=300;
						echo"
						<script language=\"JavaScript\">
						hut = $hut;
						</script>
						<a href='#ch$i' name='ch$i' onclick=\"dec('ch_$i',$hot);dec('chu_$i',1)\"><b><img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_down_off.jpg' alt='v' border='none'> Développer <img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_down_off.jpg' alt='v' border='none'></b></a>
						<input type='text' name=\"ch_cu_$i\" value=\"$ch\" style='border:none;background:none;width:30px;text-align:right' readonly> / $c sélectionnés<br>
				  		<div id='ch_$i' style='display:block;width:380px;height:1px;overflow:hidden;'>
						<a href='#ch$i' onclick=\"dec('ch_$i',1);dec('chu_$i',hut)\"><b><img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'> réduire <img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
						<ul>
						<li><input type='checkbox' name='all$i$c' onclick=\"\"> Tout</li>
						$prh
						</ul>
						<a href='#ch$i' onclick=\"dec('ch_$i',1);dec('chu_$i',hut)\"><b><img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'> réduire <img src='http://www.adeli.wac.fr/vers/$vers/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
						</div>
						<div id='chu_$i' style='display:block;width:380px;height:$hut"."px;overflow:hidden;'  onclick=\"dec('ch_$i',$hot);dec('chu_$i',1)\">						
						$seled
						</div>
						
						";	
						}	
						else{
							if(sizeof($fieldoptions)==3){
								$listres = mysql_query("SELECT $fieldoptionprint,`$fieldoption`,`id` FROM `$refiled` ORDER BY 0");
								while($rowlist = mysql_fetch_array($listres)){
									$rowvalue = $rowlist[0];
									$rowid = $rowlist[1];
									$roid = $rowlist[2];
									if(ereg('<'.$rowid.'>',$field_value)){
										echo"- $rowvalue<br>";
									}
								}	
							}
							if(sizeof($fieldoptions)==3){
								echo str_replace('><','<br>','>'.$field_value.'<');	
							}
						}				
					
					}	
					elseif(sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlse'){
						if($editmode==0){
						 echo"<select name=\"$field_name\" style=\"width:300px\">
							<option value=' '>liste des choix</option>";
							$listres = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled`");
							while($rowlist = mysql_fetch_array($listres)){
								$gvl = explode("\n",$rowlist[0]);
								foreach($gvl as $rowvalue){
									$rowvalue = trim($rowvalue);
									if($rowvalue!=''){
										$se = "";
										if($rowvalue == $field_value){
											$se = "selected";
										}
										$rowvaluu=str_replace('"','&quot;',$rowvalue);
										echo"<option value=\"$rowvaluu\" $se>$rowvalue</option>";
									}
								}
							}
							echo"</select>";
						}
						else{
							echo $field_value;
						}
					}
					else{
						if($editmode==0){
							 echo"<select name=\"$field_name\" style=\"width:300px\">
								<option value=' '>liste des choix</option>";
								$listres = mysql_query("SELECT DISTINCT(`$fieldoption`),$fieldoptionprint  FROM `$refiled` ORDER BY 1");
								while($rowlist = mysql_fetch_array($listres)){
									$rowvalue = $rowlist[1];
									$rowid = $rowlist[0];
									$se = "";
									if($rowid == $field_value){
										$se = "selected";
									}
									echo"<option value=\"$rowid\" $se>$rowvalue</option>";
								}
								echo"</select>";
						}
						else{
							$listres = mysql_query("SELECT `$fieldoptionprint`  FROM `$refiled` WHERE `$fieldoption`='$field_value' ");
								$rowlist = mysql_fetch_array($listres);
								echo $rowlist[0];
						}
					}
					echo"</td>";
				 }	 
				 
			}
			////////////////////////////////////////// WORKNET ETAT
			elseif($field_name == "etat" && $part=="adeli_messages"){
					$field_value = $stat_message[$field_value];	
					echo"<td>$nameifthefield</td><td>$field_value</td>";
			}
			////////////////////////////////////////// WORKNET DATE
			elseif(($field_type == "date" || $field_type == "time" || $field_type == "datetime") && $part=="adeli_messages"){
				if( $edit!="" ){
					echo"<td>$nameifthefield</td><td>".date("d/m/y H:i",strtotime($field_value))."</td>";
				}
				else{
					$field_value=$defaultvalue[$field_type];
					echo"<td>$nameifthefield</td><td><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\">".date("d/m/y H:i")."</td>";
				}
			}
			 /////////////////////////////////////// DATE
			 elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
			 	/*if($field_value==""){
					$field_value=$defaultvalue[$field_type];
				}*/	
				 echo"<td><a class='info'>$nameifthefield<span>Date au format standard<br>date: aaaa-mm-jj<br>heure: hh:mm:ss</span></a></td><td>";
				 if($editmode==0){
				 echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"javascript:contextdate(event);cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=fourmis.$field_name&amp;date='+document.fourmis.$field_name.value+'&amp;type=$field_type'\"><input type='text' name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" maxlength=\"$field_length\">";
				 }
				 else{
				 	if($field_value == "0000-00-00 00:00:00" || $field_value == "0000-00-00" || $field_value == "00:00:00"){
						$field_value = "...";	
					}
					elseif($field_type == "date"){
						$field_value = date("d/m/Y",strtotime($field_value));	
					}
					elseif($field_type == "time"){
						$field_value = substr($field_value,0,5);	
					}
					elseif($field_type == "datetime"){
						$field_value = date("d/m/Y - H:i",strtotime($field_value));	
					}
				 	echo $field_value;
				 }
				 if($editmode==0){
				 	echo"<a style='cursor:pointer' onclick=\"document.fourmis.$field_name.value='$defaultvalue[$field_type]';\" class='petittext'>Maintenant</a>";
				 }
				 echo"
				 
				 </td>";
			 }
			 ////////////////////////////////////////// WORKNET SUJET
			elseif($part=="adeli_messages" && $field_name=="sujet"){
				echo"<td>$nameifthefield</td><td>";
				if($edit!=""){  
					echo"$field_value";
					if( $ro->dest==0){
					 echo" | <a href='./?option=$option&part=$part&edit&dest=".($ro->prov)."&sujet=re:$field_value'>répondre</a>";
					}
				}
				else{ 
					if(isset($_GET["sujet"])){
						$field_value = stripslashes($_GET["sujet"]);
					} 
					elseif(isset($_GET['rec'])){
						$rec = $_GET['rec'];
						$rem = mysql_query("SELECT `nom` FROM `adeli_message_template` WHERE `id`='$rec'");
						if($rem && mysql_num_rows($rem)>0){
							$rom = mysql_fetch_array($rem);
							$field_value = $rom[0];
						}
					}
					echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:300px;\">";
				}
				
				echo"</td>";
			}
			
			  ////////////////////////////////////////// PASSWORD
			elseif($field_name == "pass" || $field_name == "passe"){
				echo"<td width='100'>$nameifthefield</td><td>";
				 if($editmode==0){
				 echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-string.png' alt='texte'><input  autocomplete=\"off\" type=\"password\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\">";
				 }
				 else{
				  echo str_repeat('*',strlen($field_value));
				 }
				 echo"";
				if(isset($aff_pass) && $aff_pass==true){
					echo"<a class='info'>voir<span style='left:0px;top:0px'>$field_value</span></a>";
				}
				echo"</td>";
			}
			 /////////////////////////////////////// STRING
			 elseif($field_type == "string"){			 	  
				 echo"<td><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td>";
				 if($editmode==0){
				 echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:$field_width"."px\" maxlength=\"$field_length\">";
				 }
				 else{
				 echo"$field_value";
				 }
				 echo"</td>";
			 }
			 /////////////////////////////////////// INT
			 elseif($field_type == "int" || $field_type == "real"){			 	  
				 echo"<td><a class='info'>$nameifthefield<span>Nombre</span></a></td><td>";
				 if($editmode==0){
				 echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-int.png' alt='numérique'><input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:150px\" maxlength=\"$field_length\">";
				 }
				 else{
				 echo"$field_value";
				 }
				 echo"</td>";
				 
			 }
			 
			 /////////////////////////////////////// TEXTE
			 elseif($field_type == "blob"){
			 		if($editmode==1){
						echo "<td><a class='info'>$nameifthefield</a></td> <td>".html_my_text("$field_value")."</td>";
					}
					else{
			 
					  ////////////////////////////////////////// WORKNET MESSAGE
					if($part=="adeli_messages" && $field_name=="text"){
					
					$sign_file = "mconfig/$u_id.mails.signature";
						$signa='';
						if(is_file($sign_file)){
							$fp = fopen($sign_file ,"rb");
							$signa="\n\n\n".fread($fp,filesize($sign_file));
							fclose($fp);
						}
						
						$nameifthefield="Message";
						if($edit==''){  
							if(isset($_GET["message"])){
								$field_value = stripslashes($_GET["message"]).$signa;
							} 
							elseif(isset($_GET['rec'])){
								$rec = $_GET['rec'];
								$rem = mysql_query("SELECT `texte` FROM `adeli_message_template` WHERE `id`='$rec'");
								if($rem && mysql_num_rows($rem)>0){
									$rom = mysql_fetch_array($rem);
									$field_value = str_replace('$variable','$variable'.stripslashes($_POST["adeli_message_var"]),$rom[0]);
									if(mysql_query("SHOW COLUMNS FROM `clients`")){
										if(isset($_GET['dest'])){
											$dest = $_GET['dest'];
										}
										elseif(isset($_POST['dest'])){
											$dest = $_POST['dest'];
										}
										$res_cl = mysql_list_fields($dbase,'clients');
										$colcli = mysql_num_fields($res_cl);
										$rem = mysql_query("SELECT * FROM `clients` WHERE `id`='$dest'");
										$rom = mysql_fetch_object($rem);		 	 			 		   
									   	for ($c = 0; $c < $colcli; $c++) {
											$fdn = mysql_field_name($res_cl, $c);
											$field_var = $rom->$fdn;
											$field_value = str_replace('$'.$fdn,$field_var,$field_value);
									   	}
									 }
								}
							}
							else{
								$field_value = $signa;
							}
							if( is_file('bin/_transfert.php') && in_array("adeli_messages",$menu["worknet"]) && isset($_POST["adeli_message_var"]) && (isset($_POST['dest']) || isset($_GET['dest']))){
								if(isset($_GET['dest'])){
									$dest = $_GET['dest'];
								}
								elseif(isset($_POST['dest'])){
									$dest = $_POST['dest'];
								}
								$plval='';
								$scna = str_replace('index.php','',getenv("SCRIPT_NAME"));
								foreach($_POST as $k=>$v){
									if(substr($k,0,5)=='join_'){
										$k = substr($k,5,strlen($k));
										if(substr($k,0,7)=='compta_'){
											$mid = substr($k,7,strlen($k));
											$plval .= "<li><a href='http://$prov$scna/bin/_transfert.php?i=$dest&c=$mid'><b>$v</b></a></li>";	
										}
										if(substr($k,0,5)=='file_'){
											$nam = basename($v);
											$plval .= "<li><a href='http://$prov$scna/bin/_transfert.php?i=$dest&f=$v'><b>$nam</b></a></li>";	
										}										
									}
								}
								if($plval!=''){
									$plval="<div style='border:#CCC 1px solid; padding:10px'><b>Fichiers joints : </b><ul>$plval</ul></div>";	
								}
								if(strpos($field_value,'$variable')>-1){
									$field_value=str_replace('$variable',$plval,$field_value);	
								}
								elseif($plval!=''){
									$field_value = "<font face='arial'>".$plval."<br><br></font>".html_my_text($field_value);
								}
							}
							else{
								$field_value = "<font face='arial'>".html_my_text($field_value)."</font>";
							}
						}
					}
			 
			 
				 $field_value=str_replace('$variable','',$field_value);	
			 	$stylo="";
				if(is_file("../$part/style.css")){
					$stylo.="<hr><b>Feuille de style</b><br>
					<a href='#' onclick=\"mkpa($txid)\">aperçu</a>
					<ul>";
					$getstyle = fopen("../$part/style.css","rb");
					$styleval = fread($getstyle,filesize("../$part/style.css"));
					$stylo.="<!-- $styleval -->";
					$getstyle = fopen("../$part/style.css","rb");
					while( !feof($getstyle)){
						$lin =  trim(fgets($getstyle, 4096));	
						if( $lin!="" && substr(trim($lin),0,1)==="." ){
								$lin_a = substr($lin,1,strlen($lin)-2);
								$lin_m = substr($styleval,strpos($styleval,$lin)+strlen($lin),strlen($styleval));
								$lin_s = substr($lin_m, 0, strpos($lin_m,"[}]"));
								$stylo.="<li><a href=\"#\" onclick=\"stylize_$i('$lin_a')\" title='$lin' style=\"$lin_m\">$lin_a</a></li>";
						}
						
					}$stylo.="
						<li><a href=\"#\" onclick=\"normalize_$i()\" title='reinitialiser'>reinitialiser</a></li>
						</ul>
						";
					fclose($getstyle);

				}
				$ishtml=false;
				if($part=="adeli_messages" && $field_name=="text" && $edit!=''){					
					echo "<td><a class='info'>$nameifthefield</a></td> <td>".html_my_text("$field_value")."<br><br>";
					if( $ro->dest==0){
					 echo" | <a href='./?option=$option&part=$part&edit&dest=".($ro->prov)."&sujet=re:$field_value&message=".urlencode("\n\n\n-------\n".$field_value)."'>répondre</a>";
					}
					echo"</td>";
				}
			 	elseif( (!isset($types[$part]) || (isset($types[$part]) && !ereg("plain",$types[$part]))) && ((ereg("</",$field_value) || isset($_GET["html"]) ) && !isset($_GET["plain"]) )){
				 array_push($ishtmlll,array($i,$nameifthefield));
				 echo"<td><a class='info'>$nameifthefield<span>Texte libre multiligne</span></a><br>
				 <b>mode HTML</b><br>
				 <a href='./?option=$option&part=$part&edit=$edit&plain'><font size='1'>passer en mode simple</font></a>
				 $stylo </td>				 
				 <td>";
				 editor($field_name,$field_value,$i,$stylo);
					echo" </td>";	 
					if($part=="adeli_messages" && $field_name=="text"){
						$idtxt = $i;
					}
					$ishtml=true;
				}
				elseif(!ereg("</",$field_value) || isset($_GET["plain"]) || ereg("plain",$types[$part]) ){	
				 //$field_value = strip_tags($field_value);			
				 echo"<td><a class='info'>$nameifthefield<span>Texte libre multiligne</span></a><br>
				 <b>texte simple</b><br>
				 
					";
					if( !isset($types[$part]) || (isset($types[$part]) && !ereg("plain",$types[$part]) ) ){
				 		echo"<a href='./?option=$option&part=$part&edit=$edit&html'><font size='1'>passer en mode HTML</font></a>						
						";
						
					}
					echo"$stylo</td>				 
				 <td><table cellpadding='0' cellspacing='0'><tr><td valign='top'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-text.png' alt='texte' style='float:left'><br><a onclick=\"view$part$edit=open('about:blank','view$part$edit','scrollbars=1,resizable=1,width=490,height='+(document.fourmis.$field_name.scrollHeight)); view$part$edit.document.write('<pre>'+document.fourmis.$field_name.value+'<pre>')\"><img src='http://www.adeli.wac.fr/vers/$vers/images/externe.png' alt='/^'></a></td><td valign='top'>
				<div style='position:relative'> ";
				 if(is_file("../$part/style.css")){
				 echo"<script language=\"JavaScript\">
					function stylize_$i(ste){
					   insertTags('['+ste+']', '[/]', 'texte',$txid);
					   //mkpa($txid);
					}
					function normalize_$i(){
					   ltxt = document.fourmis.$field_name.value;
					   var tags = /\[[^/^>]*]/g;
					   var tags2 = /\[[/^>]*]/g;
					   document.fourmis.$field_name.value = ltxt.replace(tags, '').replace(tags2, '');
					   //mkpa($txid);
					}
				</script>
				 <div id='txtap$txid' style='position:absolute;width:310px;height:200px;top:0px;left:0px;background-color:#FFFFFF;visibility:hidden;z-index:110' onclick=\"mkpl($txid)\">	<a href='#' onclick=\"mkpl($txid)\">fermer l'aperçu</a>
				 <iframe name='ap$txid' id='ap$txid' style='width:300px;height:180px;' width='300' height='200' src='about:blank'></iframe>
				 </div>";
				 }
				 echo"
				<textarea name=\"$field_name\" cols=\"10\" rows=\"10\" style=\"width:300px;height:50px;z-index:100\" onfocus=\"if(resizteaxtarea){ this.style.height=this.scrollHeight; }\"
onkeyup=\"if(resizteaxtarea){ this.style.height=this.scrollHeight;}if(parseInt(this.style.height)<50){this.style.height='50px';}\"  onblur=\"if(resizteaxtarea){ this.style.height=this.scrollHeight;}if(parseInt(this.style.height)>50){this.style.height='50px';}\">$field_value</textarea>
				 </div></td></tr></table>
				 
				 <script language='javascript' type='text/javascript'>
				 //mkpa($txid);
				 </script>
				 </td>";
				 $txid++;
				}
				}
			 }
			 /////////////////////////////////////// DEFAULT
			 else{
			 	echo"<td><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td>";
			 	if($editmode==0){
				 echo"<img src='http://www.adeli.wac.fr/vers/$vers/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:300px\" maxlength=\"$field_length\">";
				 }
				 else{
				 echo"$field_value";
				 }
				 echo"</td>";
			 }
			 echo"</tr>
			 <tr><td colspan='2'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/g.gif' alt='_' height='3' width='100%'>";
			 if(isset($_GET[$field_name])){
			 	echo"<script language='javascript' type='text/javascript'>
				document.fourmis.$field_name.value = \"".str_replace('"','\"',stripslashes($_GET[$field_name]))."\";
				 </script>";
			 }
			 echo"</td></tr>";
	   }
	   mysql_close($conn);
	   echo"</table></div>";
	   if($nochnb>1){
	   echo"<script language='javascript'>
	   	resizteaxtarea=false;";
	   /*for($l=1 ; $l<=$nochnb ; $l++){
	   	echo"sizpa('noch_$l',3);";
	   }*/
	   echo"</script>";
	   }
	$allreps=0;
	echo"</td><td align='left' valign='top' id='coldroit'>";
	   			 
	   		
		if($part=="adeli_messages" && $edit==''){
				$conn = connecte($base, $host, $login, $passe);
				 if(mysql_query("SHOW COLUMNS FROM `adeli_message_template`")  ){
				 	echo"
					<script>
					function assvar(koi){
						";
						if($ishtml){
						echo"
						if (document.all) {
							var oRng = eval('editbox_$idtxt').document.selection.createRange();
							if(oRng.text) sampleText=oRng.text;
							oRng.pasteHTML('$'+koi);
						}
						else{
							eval('editbox_$idtxt').document.execCommand('insertHTML', false, '$'+koi);
						}	
						eval('editbox_$idtxt').focus();
						";
						}
						else{
							echo"insertTags('$'+koi, '', '',0);";
						}
						echo"
					}					
					</script>
					";
					$colcli=0;
					if(mysql_query("SHOW COLUMNS FROM `clients`")){
						$res_cl = mysql_list_fields($dbase,'clients');
						$colcli = mysql_num_fields($res_cl);
					}
					echo"
					
					<b>Modèles de messages</b><br><br>
					<a href='#' onclick=\"document.getElementById('new_temp').style.height='".(300+($colcli*14))."px';document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh&new';\">
					<b>+</b> enregistrer le texte courant comme modèle</a>
				  <div id='new_temp' style='display:block;width:280px;height:1px;overflow:hidden;'>
					<a href='#' onclick=\"document.getElementById('new_temp').style.height='1px';document.fourmis.action='./?option=$option&part=$part&$action&edit=$edit';\" class='buttontd'>annuler</a>
					<br><br>
					insérer une variable :<br>
					- <a href='#' onclick=\"assvar('variable');\">variable dynamique</a><br>
					ou :
					<br>
					";
					if($colcli!=0){
						for ($i = 0; $i < $colcli; $i++) {
							$field_name = mysql_field_name($res_cl, $i);
							echo"- <a href='#' onclick=\"assvar('$field_name');\">$field_name</a><br>";
						}
					 }
					echo"
					<br><br>
					nom du nouveau modèle :<br>
					<input type='text' name='adeli_message_tit' value='nouveau modèle' onfocus=\"this.value='';\">
					<br><br>
					<input class='buttontd' type='submit' value='ok'>
					</div>
					<br><br>
					";
					if(isset($_GET['new'])){
						$tit = str_replace("'","''",stripslashes($_POST['adeli_message_tit']));
						$tex = str_replace("'","''",stripslashes($_POST['text']));
						if(!mysql_query("INSERT INTO `adeli_message_template` (`nom`,`texte`,`active`) VALUES ('$tit','$tex',1)")){
							echo"une erreur est survenue...<br>";
						}
					}
					$rem = mysql_query("SELECT `id`,`nom` FROM `adeli_message_template` WHERE `active`=1 ORDER BY `nom`");
					if($rem && mysql_num_rows($rem)>0){
						echo"
						Nouveau message à partir d'un modèle<br><br>
						<input type='text' name='adeli_message_var' value='' onfocus=\"this.value='';\"><br>
						<table>";
						while($rom = mysql_fetch_array($rem)){
							echo"<tr><td>- <a href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh&rec=$rom[0]&dest='+document.fourmis.dest.value;document.fourmis.submit()\">
					$rom[1]</a></td><td><a href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh&del=$rom[0]&effdb=adeli_message_template';document.fourmis.submit()\">
					<img src=\"http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif\" border='none' alt='supprimer'></a></td></tr>";
						}
						echo"</table>";
					}
				 }
				 else{
				 	if(isset($_GET['mktb'])){	
						if(mysql_query("CREATE TABLE `adeli_message_template` (
					  `id` bigint(20) NOT NULL auto_increment,
					  `nom` varchar(255) NOT NULL default '',	  	  
					  `texte` text NOT NULL,
					  `clon` int(1) NOT NULL default '0',	  
					  `active` int(1) NOT NULL default '0',
					  PRIMARY KEY  (`id`)
					)") ){
							echo"La base de donnée <b>\"Modèles de messages\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part&edit=$edit'>cliquez ici pour l'utiliser</a>";
						}
						else{
							echo"La table <b>\"Modèles de messages\"</b> n'a pu être créée correctement";
						}
					}
					else{
						echo"<a href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh&mktb';document.fourmis.submit()\">configurer les <b>Modèles de messages</b></a>";
					}
				 }
				 mysql_close($conn);		
			}
		elseif($part=="clients"  && !isset($_GET['clone']) && $_GET['edit']!=''){ ///////////////:MESSAGES
	   		
				if(in_array("adeli_messages",$menu["worknet"])){
					echo"
					<script language='javascript'>	    
					function det(ki){
						document.getElementById('msg_cli').style.height=ki;
					}
				  </script>
				  <div id='msg_cli' style='display:block;position:relative;width:200px;height:16px;overflow:hidden;'>";
					$conn = connecte($base, $host, $login, $passe);
					$ros = mysql_query("SELECT * FROM `adeli_messages` WHERE `dest`='$edit' OR `prov`='$edit' ORDER BY `date`DESC");
					$hot = (mysql_num_rows($ros)*14)+40;
					echo"<a href='#' onclick='det($hot)'><b>Messages client</b></a> |
					<a  href='#' onclick=\"document.fourmis.action='./?adeli_messages&edit&dest=$edit';document.fourmis.submit()\" class='buttontd'><b>&Eacute;crire</b></a><br>";
					while($rew=mysql_fetch_object($ros)){
						$dest = $rew->dest;
						$prov = $rew->prov;
						$sujet = $rew->sujet;
						$etat = $rew->etat;
						$dat = date("d/m/y H:i",strtotime($rew->date));
						$mid = $rew->id;
						if($prov==0){ 
							$prov="moi"; 
						}
						else{
							$prov=$ro->nom;  
						}
						if($dest==0){ 
							$dest="moi"; 
						}
						else{
							$dest=$ro->nom;  
						}
						echo"<span style='white-space:nowrap'><a href='./?adeli_messages&edit=$mid'>$prov > $dest : $sujet</a> $dat</span><br>";
					}
					echo"<a href='#' onclick='det(16)'><b>masquer les messages</b></a>
					</div>";
					if(mysql_query("SHOW COLUMNS FROM `adeli_message_template`")  ){
						$rem = mysql_query("SELECT `id`,`nom` FROM `adeli_message_template` WHERE `active`=1 ORDER BY `nom`");
						if($rem && mysql_num_rows($rem)>0){
							echo"
							envoyer :<br>
							<input type='text' name='adeli_message_var' value='' onfocus=\"this.value='';\"><br>
							avec le modèle :<br>";
							while($rom = mysql_fetch_array($rem)){
								echo"- <a href='#' onclick=\"document.fourmis.action='./?adeli_messages&edit&rec=$rom[0]&dest=$edit';document.fourmis.submit()\">
						$rom[1]</a><br>";
							}
						}
					 }					
					mysql_close($conn);
					if(is_file('bin/_transfert.php')){
						echo"
						<style type='text/css'>
							.joinfich{
								display:none;
								margin-right:15px;
							}
						</style>
						<script language='javascript'>
							 function affichfichs(){
								chs = document.getElementById('coldroit').getElementsByTagName('input');
								for(i=0 ; i<chs.length ; i++){
									if(chs[i].type=='checkbox' && chs[i].className=='joinfich'){
										chs[i].style.display='inline';
									}
								}
							 }						
						</script>
						<br>
						
						<a onclick=\"affichfichs()\">Joindre les fichiers</a>";
					}
					echo"<hr>";
				}
				else{
					//echo"pas de système de messagerie client...<hr>";
				}
				/////////////////////////////////////////////////////: COMPTA
				$conn = connecte($base, $host, $login, $passe);
				if(isset($compta_base) && mysql_query("SHOW COLUMNS FROM $compta_base") && in_array('compta',$opt) ){
					
					$lastyp='';
					$subto=0;
					$subdehors=0;
					$dismoidehors='';
					echo"<a href='#' onclick=\"sizpa('cmt_cli')\"><b>Compta</b></a> <a href=\"./?option=compta&edit&freecontent&forclient=$edit\" class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/+.png' alt='+' border='none'><span>nouveau document compta</span></a>
						<div id='cmt_cli' style='display:block;width:280px;height:1px;overflow:hidden;'><table>";
					$rus = mysql_query("SELECT DISTINCT(`type`) FROM `$compta_base` WHERE `client`='$edit' ORDER BY `type`DESC");
					while($ruw=mysql_fetch_array($rus)){
						$type = $ruw[0];
						if($type!=$lastyp){
							if($lastyp!=''){
								echo"<tr><td></td><td align='right'>$subto&euro;<td></td></tr>";
								$dismoidehors.="$lastyp: $subdehors&euro; &nbsp; ";
								$subdehors=0;
								$subto=0;
							}
							echo"<tr><td colspan='3'><b>$type</td></tr>";
						}
						$lastyp = $type;
						
						$ros = mysql_query("SELECT * FROM `$compta_base` WHERE `client`='$edit' AND `type`='$type' ORDER BY `numero`DESC");
						while($rew=mysql_fetch_object($ros)){							
							$code = $rew->code;
							$numero = $rew->numero;
							$intitule = $rew->intitule;
							$montant = $rew->montant;
							$acompte = $rew->acompte;
							
							$solde = $montant-$acompte;
							$acc='';
							if($acompte > 0){
								$acc="<br>solde sur $montant&euro;";
							}
							$etat = $rew->etat;
							$subto+=$solde;
							if($etat==0){
								$subdehors+=$solde;
							}
							$dat = date("d/m/y",strtotime($rew->date));
							$mid = $rew->id;
							echo"<tr><td>";
							if(is_file('bin/_transfert.php') && in_array("adeli_messages",$menu["worknet"])){
								echo"<input type='checkbox' name='join_compta_$mid' value='".ucfirst($type)." $code$numero' class='joinfich'>";	
							}
							echo"
							<span style='white-space:nowrap'><a href='./?option=compta&$type&edit=$mid&getcontent' class='info'>$code$numero<span><b>$intitule</b><br><font size='1'> $dat$acc</font></span></a>
							<td align='right'><a href='./?option=compta&$type&edit=$mid&getcontent'><font color='#$colorstatut[$etat]'>$solde&euro;</font></font></td>
							</td><td><a href='#' onclick=\"javascript:open('$openpdf&mkpdf=$mid','pdf','width=400,height=500,scrollbars=1,resizable=1,top='+((screen.height-500)/2)+',left='+((screen.width-400)/2))\" class='info'><img src='http://www.adeli.wac.fr/icos/pdf.gif' border='none' alt='éditer'><span>voir le pdf</span></a></span></td>
							<td align='right'><font color='#$colorstatut[$etat]'>$defstat[$etat]</font></td>
							</tr>";
						}
					}
					$dismoidehors.="$lastyp: $subdehors&euro; &nbsp; ";
					
					echo"
					<tr><td></td><td align='right'>$subto&euro;<td></td></tr>
					</table><a href='#' onclick=\"sizpa('cmt_cli')\"><b>masquer les comptes</b></a>
					</div><span class='petittext'>$dismoidehors</span><hr>";
					$fp = @fopen("mconfig/$u_id.ouvert.$part.cmt_cli.conf","a+");
					@fseek($fp,0);
					$ouvert = abs(@fread($fp,255));
					@fclose($fp);
					if($ouvert>5){										
						echo"<script language='javascript'>
						sizpa('cmt_cli');
						</script>";
					}
				}
				else{
					//echo"<!-- sans compta -->";
				}
				mysql_close($conn);
		   }
		 
		if( (sizeof($comportement) > 1 || isset($fichiers[$part])) && !isset($_GET['clone']) ){  

	   
	   insert('_fichiers');
		if(is_file('bin/_fichiers.php')){
			include('bin/_fichiers.php');
		}
		else{
			include('http://www.adeli.wac.fr/vers/$vers/update.php?file=_fichiers.php&1');
		}
		
	   } 
	   if(is_file('bin/_agenda_link.php')){
			include('bin/_agenda_link.php');
		}
		else{
			include('http://www.adeli.wac.fr/vers/$vers/update.php?file=_agenda_link.php&1');
		}	
 ////////////////////////////////////////////////////////////////// CHILDREN	 
$children=''; 
if($edit != ''){ 
for($i=0; $i<sizeof($menu) ; $i++){
	$spart = $menupart[$i];
	$tablo = $menu[$spart];
	$cols = sizeof($tablo);			
	$tablk = array_keys($tablo);
	$sepa='site';
	if(substr($spart,0,7)=='worknet') $sepa='worknet';
	if(substr($spart,0,7)=='gestion') $sepa='gestion';
	for($m=0; $m<sizeof($tablo) ; $m++){
		$tk = $tablk[$m];
		$tart = $tablo[$tk];
		if(!is_numeric($tk)){
			$humanpart = $tk;
		}
		else{
			$humanpart = $tablo[$tk];
			if($prefixe != ""){
				$humanpart = ereg_replace($prefixe,"",$humanpart);
			}
			$humanpart = ereg_replace($spart."_","",$humanpart);
			$humanpart = ereg_replace("adeli_","",$humanpart);
			$humanpart = ereg_replace(">$spart","",$humanpart);
			$humanpart = ereg_replace("-$spart","",$humanpart);
			$humanpart = ereg_replace(">"," ",$humanpart);	
		}
		$conn = connecte($base, $host, $login, $passe);
		if(mysql_query("SHOW COLUMNS FROM `$tart`")){	
			$res_field = mysql_list_fields($base,$tart);
   			$columns = mysql_num_fields($res_field);
			for ($c=0 ; $c < $columns; $c++) {
				$field_name = mysql_field_name($res_field, $c);
				$field_act = $field_name;
				if(isset(  $r_alias[$tart][$field_act] )){
					$field_act = $r_alias[$tart][$field_act];
				}
				if(substr(strrev($field_act),0,3)=='hc_'){
					$mot = explode('_',strrev($field_act),4);	
					$mot = strrev($mot[3]);		
				}
				else{			
					$mot = explode('_',strrev($field_act),3);	
					$mot = strrev($mot[2]);		
				}
				if($mot == $part){
					
					$fieldoptions = split("_",substr($field_act,strlen($mot)+1,strlen($field_act)));
					$fieldoptionprint = $fieldoptions[1];
					$fieldoption = $fieldoptions[0];
					$children.="<a href='./?option=$sepa&$tart'><b>$humanpart</b></a> <a href='./?option=$sepa&$tart&edit&$field_name=".($ro->$fieldoption)."'class='info'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/+.png' alt='+' border='none'><span>ajouter</span></a>";
					$listres = mysql_query("SELECT * FROM `$tart` WHERE `$field_name`='".($ro->$fieldoption)."'");
						while($rowlist = mysql_fetch_object($listres)){
							$roid = $rowlist->id;
							$roac = $rowlist->active;
							$clac='';
							if($roac==0){
								$clac="class='petittext'";
							}
							$children.="<div style='position:relative;width:140px;height:16px;white-space:nowrap;'>- <a href='./?option=$sepa&$tart&edit=$roid' class='info'>";
							$fti='';
							for ($q=0 ; $q < $columns; $q++) {
								$ft = mysql_field_type($res_field, $q);
								$fn = mysql_field_name($res_field, $q);
								if(($ft=='string' || $ft=='blob') && $fn!='pass'){
									$fv = strip_tags($rowlist->$fn);
									if(!ereg($fv,$fti)){
										$fti.=$fv.' ';
									}
								}
							}
							if(trim($fti)==''){
								$fti = $humanpart.'#'.$roid;
							}
							$ftit = substr($fti,0,20);
							$children.="<font $clac>$ftit</font><span style='white-space:normal;position:absolute;left:-140px;top:-10px;width:140px;height:140px;white-space:normal;overflow:scroll;text-align:left;'>$fti</span></a></div>";
						}
					$children.="<br><br>";
				}		
			}
		}
		mysql_close($conn);
	}
} 
if($children!=''){
	echo"<hr>$children";
}
}
	      
	   echo"</td></tr>
	   <tr><td colspan='2'><img src='http://www.adeli.wac.fr/vers/$vers/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>
	  <tr><td colspan='2' align='left'>	";
	  if($editmode==0){
	  		echo"
			<input class=\"buttontd\" type=\"button\" value=\"Annuler\" onclick=\"document.location='./?option=$option&part=$part';\">
			&nbsp;	&nbsp;	&nbsp;	&nbsp;	
			";
			$nochnb++;
		echo"<a name='nochange$nochnb'></a>";
			if( $clonid!=0 ){	
			echo"<input class=\"buttontd\" type=\"button\" value=\"Rendre indépendant\" onclick=\"document.fourmis.action+='&indep';document.fourmis.submit()\"> ";
			}
			echo"
			<input class=\"buttontd\" type=\"button\" value=\"Enregistrer et revenir\" onclick=\"document.fourmis.action='./?option=$option&part=$part&$action=$edit';document.fourmis.submit()\"> ";
			if( !in_array("nonew",$comportement)){	
			echo"<input class=\"buttontd\" type=\"button\" value=\"Enregistrer et ajouter\" onclick=\"document.fourmis.action+='&new';document.fourmis.submit()\"> ";
			}
			echo"<input class=\"buttontd\" type=\"submit\" value=\"Enregistrer\">
			";
		}
		else{
			echo"<a href=\"./?option=$option&part=$part&amp;edit=$edit&modif\" class='buttontd'>Modifier</a>";
		}
	
}	
elseif(isset($_GET['alert'])){	
	$message = $_POST['message'];
	$l = $_GET['l'];
	$message = nl2br($message);
	echo"
	<table cellspacing='0' cellpadding='2' class='cadrebas'>
		<tr><td class='buttontd'>Alerte</td></tr>		
		<tr><td>
		
		<table>
			<tr><td>
			
			$message
			
			
			</td></tr>
			
		
		 <tr><td align='right'>				
				<div class=\"buttontd\" style='width:140px'><a href=\"./?option=$option&part=$part&edit=$l\">retour à l'article</a></div>
				<div class=\"buttontd\" style='width:140px'><a href=\"./?option=$option&part=$part\">retour à la liste</a></div>
			</td></tr>
		
		
		</table>
		
		</td></tr>
		</table>
	";
}
/********************************************************************************************************************

									STATISTIQUES

**********************************************************************************************************************/

elseif(isset($_GET["statistiques"])){	
	$tabledb = $part;
	insert('_statistiques');
	if(is_file("bin/_statistiques.php")){
		include("bin/_statistiques.php");
	}
	else{
		include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_statistiques.php");
	}
}
/********************************************************************************************************************

									EXPORTER

**********************************************************************************************************************/

elseif(isset($_GET["exporter"])){	
	insert('_exporter');
	if(is_file("bin/_exporter.php")){
		include("bin/_exporter.php");
	}
	else{
		include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_exporter.php");
	}
}
/********************************************************************************************************************

									IMPORTER

**********************************************************************************************************************/
elseif(isset($_GET["importer"])){	
	insert('_importer');
	if(is_file("bin/_importer.php")){
		include("bin/_importer.php");
	}
	else{
		include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_importer.php");
	}
}
/********************************************************************************************************************

									ANNUAIRE

**********************************************************************************************************************/

elseif(isset($_GET["annuaire"])){	
	insert('_annuaire');
	if(is_file("bin/_annuaire.php")){
		include("bin/_annuaire.php");
	}
	else{
		include("http://www.adeli.wac.fr/vers/$vers/update.php?file=_annuaire.php");
	}
}
/********************************************************************************************************************

									LISTE

**********************************************************************************************************************/
else{	
	
	
echo"
	<tr><td valign='top' colspan='3' align='center'>
	<script language='javascript'>
	function sela(k){
		var allche = document.listage.getElementsByTagName(\"input\");

		for (var i=2; i<allche.length; i++) {
			if(allche[i].className!='noche'){
				allche[i].checked=k;
			}
		}
	}
	function conmulti(k){
		var transk = new Array();
		transk['active']='activer';
		transk['desactive']='désactiver';
		transk['delete']='supprimer';
		nbsel=0;
		var allche = document.listage.getElementsByTagName(\"input\");
		for (var i=2; i<allche.length; i++) {
			if(allche[i].checked==true && allche[i].className!='noche') nbsel++;
		}
		if(nbsel>0){
			pro = confirm(\"êtes vous certain de vouloir \"+transk[k]+\" les \"+nbsel+\" objets sélectionnés ?\");
			if(pro){
				document.listage.action+='&multi='+k;
				document.listage.submit();
			}
		}
		else{
			alert(\"aucun objet n'est sélectionné\");
		}
	}
	</script>
	<form name='listage' action='./?option=$option&part=$part' method='post'>
	<table width='100%' cellspacing='1' cellpadding='0' border='0'><tr>
	<td align='left'><input type='checkbox' onclick='sela(this.checked)'>
	 -	";
	 if($u_droits == '' || $u_active == 1 ){
	 echo"
	<a href='#' onclick=\"conmulti('active')\"><img src='http://www.adeli.wac.fr/vers/$vers/$theme/v1.gif' border='none' alt='activer'></a>
	<a href='#' onclick=\"conmulti('desactive')\"><img src='http://www.adeli.wac.fr/vers/$vers/$theme/v0.gif' border='none' alt='désactiver'></a>
	<a href='#' onclick=\"conmulti('delete')\"><img src='http://www.adeli.wac.fr/vers/$vers/$theme/trash.gif' border='none' alt='supprimer'></a>";
	}
	echo"</td>
	<td align='right'>
		n'afficher que les actifs ";
		if($affdesac==0){
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=1'\">";
		}
		else{
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=0'\" checked>";
			$incwhere.=" AND `active`=1";
		}		
		echo"
		<td>
	</tr>
	<tr><td colspan='2'>
	
"; 
	$conn = connecte($base, $host, $login, $passe);
		insert("inc_wliste");
		if(is_file("bin/inc_wliste.php")){
			include("bin/inc_wliste.php");
		}
		else{
			include("http://www.adeli.wac.fr/vers/$vers/update.php?file=inc_wliste.php");
		}
	echo"
		</td></tr>
		</table>
		</form>";
	mysql_close($conn);
}

echo"</td></tr></table></td></tr></table>";		  
		  if(isset($_GET['edit'])){		
			echo"</form>";
		}
	}
	}
	else{
		echo"
		La communication n'a pas pu être établie avec la base de données... <br>travail impossible.<br>
		<!-- $base, $host, $login -->
		";
	}
	
	}
	elseif( in_array("dir",$comportement) && isset($dirfiles)){
		if(is_dir("../".$dirfiles[$part]) && $dirfiles[$part]!=""){
			include("http://www.adeli.wac.fr/vers/$vers/inc_dir.php?x_id=$x_id&$query");
		}
		else{
			echo"<table cellspacing='0' cellpadding='2' width='80%' class='cadrebas'><tr>
		<td class='menuselected' width='80'>répertoire</b></td>
		<td class='buttontd'></td></tr>		
		<tr><td colspan='2'>le dossier recherché n'existe pas</td></tr></table>";
		mkdir("../".$dirfiles[$part],0777);
		}
	}
	
}	
else{
		echo"
	<table cellspacing='0' cellpadding='0' border='0' class='cadrebas'>
   <tr style='height:20px'><td class='buttontd'><b>Accueil Worknet</b><br>Gestion de comptes clients</td></tr>
   <tr><td class='cadrebas'>
   <table style='margin:20px'>";
 for($i=0; $i<sizeof($menu) ; $i++){
 
 
			$spart = $menupart[$i];
			$tablo = $menu[$spart];
			$cols = sizeof($tablo);	
			if( ($option=="site" && substr($spart,0,7)!="gestion" && substr($spart,0,7)!="worknet") || (($option==substr($spart,0,7) && substr($spart,0,7)=="worknet") || ($option==substr($spart,0,7) && substr($spart,0,7)=="gestion")) ){		
			echo"<tr class='bando'><td colspan='3'><b>$spart</b></td></tr>";
			$tablk = array_keys($tablo);
		   for($m=0; $m<sizeof($tablo) ; $m++){
		   	$tk = $tablk[$m];
		   	if(!is_numeric($tk)){
				$humanpart = $tk;
			}
			else{
				$humanpart = $tablo[$tk];
				if($prefixe != ""){
					$humanpart = ereg_replace($prefixe,"",$humanpart);
				}
				$humanpart = ereg_replace($spart."_","",$humanpart);
				$humanpart = ereg_replace("adeli_","",$humanpart);
				$humanpart = ereg_replace(">$spart","",$humanpart);
				$humanpart = ereg_replace("-$spart","",$humanpart);
				$humanpart = ereg_replace(">"," ",$humanpart);	
			}
			$humanpart = ucfirst($humanpart);	
			$conn = connecte($base, $host, $login, $passe);
			$nbro="";
			if(mysql_query("SHOW COLUMNS FROM `$tablo[$tk]`")  ){
				$res = mysql_query("SELECT `id` FROM `$tablo[$tk]`");
				$nbro = "(".mysql_num_rows($res)." enregistrements)";
			}
			$vasaj="";
			if(isset($types[$tablo[$tk]])){
				$comportement = split(",",$types[$tablo[$tk]]);
			}
			if(mysql_query("SHOW COLUMNS FROM `$tablo[$tk]`")){	
				if( !isset($comportement) || !in_array("nonew",$comportement) ){
					$vasaj = " | <a href='./?$tablo[$tk]&option=$option&edit'>nouveau</a> | <a href='./?$tablo[$tk]&option=$option&importer'>importer</a>";
				}
				if( !isset($comportement) || in_array("txt",$comportement) || !in_array("dir",$comportement) ){	
					$vasaj .= " | <a href='./?$tablo[$tk]&option=$option&exporter'>exporter</a>   | <a href='./?$tablo[$tk]&statistiques'>statistiques</a>";
				}
				else{
					//$vasaj .= " | <font class='petittext'>exporter</font>   | <font class='petittext'>statistiques</font> ";
				}
			}
			elseif( isset($dirfiles[$tablo[$tk]]) && is_dir('../'.$dirfiles[$tablo[$tk]]) ){
				$php = phpversion();
				$vasaj = " dossier de fichiers";
				if($php >= 5){
					$sca = scandir('../'.$dirfiles[$tablo[$tk]]);
					$nbro = '('.(sizeof($sca)-2)." éléments)";
				}
			}			
			mysql_close($conn);
				echo"<tr><td> - <a href='./?$tablo[$tk]&option=$option&d=$d' class='menuuu'><b>$humanpart</b></a></td>
				<td>$vasaj</td><td>$nbro</td></tr>";
			}
		   }
	}  
	echo"</table> </td></tr></table>";
}	  
echo"
<script language='javascript'>
 incwh='".urlencode($incwhere)."';
</script>
<div id='delfilemask' style=\"position:absolute;left:0px;top:0px;width:100%;height:100%;visibility:hidden;background:url('http://www.adeli.wac.fr/vers/$vers/$theme/bgalpha.gif')\">
	 <table style=\"width:100%;height:100%;\">
	 <tr><td  align='center' valign='middle'>	 
	 <table width='300' cellpadding='5'  class='alert'>
	 <tr><td  align='center' valign='middle'>
	 <b>êtes vous sûr de vouloir supprimer maintenant?</b><br><br>	 
	 <table cellspacing='5' cellpadding='0' border='0'>	   <tr>	   
	  <td class=\"buttontd\"><a href=\"./?decon\"><b>oui</b></a></td>	  
	  <td class=\"buttontd\"><a href=\"#\" onclick=\"reconnect()\"><b>non</b></a></td>	  
	  </tr></table>	 
	 </td></tr>
	 </table>	 
	 </td></tr>
	 </table>
	 </div>	 
	 ";
}
?>
