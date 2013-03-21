<?php // 39 > LaLIE SMS ;

$step = abs($_GET['step']);
	if(!isset($_SESSION['smstxt'])){
		$_SESSION['smstxt']="";
	}
	if(isset($_POST['texte'])){
		$texte = stripslashes($_POST['texte']);
		$texte = str_replace("’","'",$texte);
		$texte = str_replace("€","Eur",$texte);
		$_SESSION['smstxt'] = $texte;
	}
	$smstxt =$_SESSION['smstxt']; 
$currentstep = $step+1;
	$r_sms = 'erreur de communication avec votre compte';
	if($fp = fopen("$style_url/smsgetcredits.php?id=".$_SESSION['r_id']."&url=".str_replace('www.','',getenv('SERVER_NAME'))."","r")){
		$r_sms = abs(fread($fp,255));
		fclose($fp);
	}
	$r_smsemetteur = '';	
	if($fp = fopen("$style_url/smsgetemetteur.php?id=".$_SESSION['r_id']."&url=".str_replace('www.','',getenv('SERVER_NAME'))."","r")){
		$r_smsemetteur = fread($fp,255);
		fclose($fp);
	}
	echo"
	<table width='500' cellspacing='0' cellpadding='5' border='0' class='cadrebas'>
   <tr style='height:20px'><td class='buttontd'><span class='textegras'>édition de lettre</span> étape $currentstep/3 </td></tr>
   <tr><td class='cadrebas'>
   <p align='right'>crédits : $r_sms SMS</p><hr>";
	if($r_sms > 0 ){
	if(sizeof($mobs) > 0 ){
	if($step==0){
		
		if(isset($_GET["recup"])){
			$recup = $_GET['recup'];
			$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
			$res = mysql_query("SELECT `message` FROM `$lalierp` WHERE id='$recup'");
			$ro = mysql_fetch_object($res);
			$smstxt=$ro->message;
			deconnecte($conn);
		}
			
		echo"
		<b>Tapez ici votre message</b>
		<script language='javascript' type='text/javascript'>
		
		var clientPC = navigator.userAgent.toLowerCase(); // Get client info
		var is_gecko = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1)
					&& (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
	var is_safari = ((clientPC.indexOf('applewebkit')!=-1) && (clientPC.indexOf('spoofer')==-1));
	var is_khtml = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
	// For accesskeys
	var is_ff2_win = (clientPC.indexOf('firefox/2')!=-1 || clientPC.indexOf('minefield/3')!=-1) && clientPC.indexOf('windows')!=-1;
	var is_ff2_x11 = (clientPC.indexOf('firefox/2')!=-1 || clientPC.indexOf('minefield/3')!=-1) && clientPC.indexOf('x11')!=-1;
	
		function insertVar(sampleText) {
		tagClose='';
		tagOpen='';
		txtarea = document.fourmis.texte;
	
	
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
		<form action='./?option=$option&part=$part&step=1' method='post' name='fourmis'><table><tr><td>";
	/*	<select onchange='insertVar(this.value);this.value=0;'>
		<option value='0'>insérer une variable</option>";
		
		$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
		
		$res_field = mysql_list_fields($base,$laliedb);
			   $columns = mysql_num_fields($res_field);		   
			   for ($i = 0; $i < $columns; $i++) {
				$field_name = mysql_field_name($res_field, $i);
				$field_type = mysql_field_type($res_field, $i);
				if($field_type == "string"){
					echo"<option value='$"."$field_name'>$field_name</option>";
				}
			   } 
	  mysql_close($conn);	
		echo"
		</select><br>*/
		echo" (160 caractères max. reste <span id='resto'>160</span>)<br>
		<textarea name='texte' style='width:400px;height:200px;' maxlength='160' onkeyup=\"javascript:document.getElementById('resto').innerHTML=160-this.value.length;if(this.value.length>=160){this.value=this.value.substr(0,160);}\" onkeydown=\"javascript:document.getElementById('resto').innerHTML=160-this.value.length;if(this.value.length>=160){this.value=this.value.substr(0,160);}\"  onchange=\"javascript:document.getElementById('resto').innerHTML=160-this.value.length;if(this.value.length>=160){this.value=this.value.substr(0,160);}\">$smstxt</textarea>
		</td></tr>
		<tr><td align='right'>
		<input type='submit' class='buttontd' value='continuer'>
		</td></tr>
		</table>
		</form>
		";
	}
	elseif($step==1){
	
								
	$prtxt = nl2br($_SESSION['smstxt']);
		echo"
		<table><tr>
			<td>
			<div style='width:400px;height:200px;padding:10px;' class='bando'>
			$prtxt
			</div>
			</td>
			<td>
			<form action='./?option=$option&part=$part&moule=$prevurl$moule&step=2' method='post' name='glob'>
						
						
			
								<table  width='120' cellspacing='1' cellpadding='1'  style='border-width:1px; border-style:solid; border-color:CCCCCC CCCCCC CCCCCC CCCCCC;'>
						<tr><td align='left' valign='top'>						
								<font color='999999'><b>envoyer à:</b></font><br>
								<input type='radio' name='aki' value='g' onclick=\"akito('g')\"> Groupes<br>
								<input type='checkbox' name='tous' value='1' onclick=\"selectall();akito('g')\">tous<br>
								";		
								for($i=0 ; $i<sizeof($groups) ; $i++){
									$grpupforjava = str_replace("_","",$groups[$i]);
									$grpforhuman = str_replace("_"," ",$groups[$i]);
									$conn = connecte($base, $host, $login, $passe);	
									$resultnum = mysql_query("SELECT `groupe` FROM `$dblist` WHERE `groupe`='$groups[$i]' AND `portable`!='' $wherelalaie");
									$nbbon = mysql_num_rows($resultnum);
									mysql_close($conn);
									if($nbbon>0){
									echo"&nbsp;&nbsp;<input type='checkbox' name='g$groups[$i]' value='1' onclick=\"akito('g')\">$grpforhuman ($nbbon contacts)<br>";
									$tousdesoufs.="\ndocument.glob.g$groups[$i].checked=1;";
									}
									else{
									echo"&nbsp;&nbsp;<input type='checkbox' disabled='disabled' name='g$groups[$i]' value='1' onclick=\"akito('g')\">$grpforhuman ($nbbon contacts)<br>";
									}
									
									$tousdesloufs.="\ndocument.glob.g$groups[$i].checked=0;";
								}		
								echo"
								<br>
								<input type='radio' name='aki' value='u' onclick=\"akito('u')\" checked> Unité<br>
								<textarea name='libreki' style='width:200;height:100px' onKeyup=\"akito('u');chech()\" onfocus=\"chech()\" readonly></textarea>
								<br>
								  <script language='javascript'>
								  function confsup(id){
									is_confirmed = confirm('êtes vous sûr de vouloir supprimer définitivement l\'enregistrement '+id+' ?');
									if (is_confirmed) {
									 document.location='./?supp='+id+'&part=$part';
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
										document.glob.libreki.focus();								
									}
									else{
										document.glob.libreki.value='';
										document.glob.aki[0].checked=1;	
									}								
								  }
								
								sep=',';
								esp=' ';
								tousmeli = \"$jsmobs\";
								mobsnom= new Array();\n";
	$conn = connecte($base, $host, $login, $passe);	
	$result = mysql_query("SELECT DISTINCT(`portable`),`nom` FROM `$dblist` WHERE `portable`!='' $wherelalaie");
	while($row = mysql_fetch_object($result)){
		echo"mobsnom['".$row->portable."']=\"".$row->nom."\";\n";
	}
	mysql_close($conn);								
								echo"

								tousmel = new Array();
								tousmel= tousmeli.split(sep);
								
								function trim(string){
									return string.replace(/(^\s*)|(\s*$)/g,'');
								} 
		
								function in_array(valeur) {
									tabl = document.glob.libreki.value;
									tableau = tabl.split(',');
									 for(e=0 ; e<tableau.length ; e++){								 	
									   if(trim(tableau[e]) == trim(valeur)){ 
									   	valnom = mobsnom[valeur];
										debutsol += \"<option value='\"+valeur+\"'>\"+valeur+\" - \"+valnom+\"</option>\";
										return false;
									   }						   
									 }
									 return true;
								}
								function checsup(valeur){
									tabl = document.glob.libreki.value;
									tableau = tabl.split(',');
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
									akito('u');
									debutsel = \"<select onchange='document.glob.libreki.value+=esp+this.value+sep;chech()'><option>ajouter</option>\";
									debutsol = \"<select onchange='checsup(this.value)'><option>supprimer</option>\";
									for(i=0 ; i<tousmel.length ; i++){
										vef = in_array(tousmel[i]);
										if(vef){
											valnom = mobsnom[tousmel[i]];
											debutsel += \"<option value='\"+tousmel[i]+\"'>\"+tousmel[i]+\" - \"+valnom+\"</option>\";
										}
									}	
									debutsel += \"</select><br>\";
									debutsol += \"</select><br><br>\";
									document.getElementById('selboy').innerHTML=debutsel+debutsol;
								}
							 function sendmailer(){
									document.glob.submit();
								 }
								</script>
								<span id='selboy'></span>
								<b>Expéditeur : $r_smsemetteur</b>
								</td></tr></table>
								
								
								<p align='right'>
						<a href='./?option=$option&part=$part&step=0' title='retour' class='buttontd'><b>retour</b> à l'édition</a>
						<a href='#' onclick='sendmailer()' title='envoyer' class='buttontd'><b>envoyer</b></a>
			
			
			
			</td>
		</table></tr>	
	
		</form>";
	
	}
	elseif($step==2){
		$reto = array();
		$reto[0] = "message traité";
		$reto[31] = "Erreur interne, message non envoyé";
		$reto[32] = "Erreur d'authentification, message non envoyé";
		$reto[33] = "Crédits insuffisants, message non envoyé";
		$reto[35] = "Un paramètre obligatoire est manquant, message non envoyé";
		$reto[50] = "Temporairement inaccessible, message non envoyé";
		$kelenv = $_POST['aki'];
		if($kelenv == "g"){
			$strsms=array();
			for($i=0 ; $i<sizeof($groups) ; $i++){
				if(isset($_POST["g$groups[$i]"])){
					if($groups[$i] == "sans_groupe"){
						$groups[$i] = "";
					}
					$typeenv.=" $groups[$i] ";
					$conn = connecte($base, $host, $login, $passe);	
						$res = mysql_query("SELECT `portable` FROM `$dblist` WHERE `portable`!='' $wherelalaie AND `groupe`='$groups[$i]' LIMIT 0,$r_sms");
						$totlml+=mysql_num_rows($res);
						while($ro = mysql_fetch_object($res)){
							array_push($strsms,trim($ro->portable));
						}
					deconnecte($conn);
				}
			}
		}
		elseif($kelenv == "u"){
			$strsms = split(",",$_POST["libreki"],$r_sms+1);
		}
		
		$strsms = array_unique($strsms); 
		$salves = array_chunk ($strsms, 200); 
		
		for($s=0 ; $s<sizeof($salves) ; $s++){
		
				$strsms = $salves[$s];
				echo"<!--
				";
				print_r($strsms);
				echo"-->
				";
				$smstxt = $_SESSION['smstxt'];
			$prtxt = trim(urlencode($smstxt));
			$sendsms = ereg_replace("[\. ]","",trim(implode(",",$strsms)));
			$numb = sizeof($strsms)-1;
			$fp = fopen("$style_url/sendsms.php?id=".$_SESSION['r_id']."&url=".str_replace('www.','',getenv('SERVER_NAME'))."&message=".urlencode($prtxt)."&emetteur=$r_smsemetteur&numero=$sendsms","r");
			if (!$fp) {
				echo "connection impossible...<br>$errstr ($errno)<br>\n";
			} 
			else {
				$ret = trim(fread($fp,255));
				$reti=$reto[$ret];
				fclose($fp);
				echo"connexion établie...<hr>envoi de $numb SMS <hr>\n";
				if($reti==""){
					$reti = "err > ".$ret;
				}
				echo"<hr>$reti";
				$reti = str_replace("'","''",$reti);
				$smstxt = str_replace("'","''",$smstxt);
				//code_retour|description_status|smsID
				$rapp = "$reti
		<hr>".str_replace(",","<br>",$sendsms);
				$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
				//mysql_query("INSERT INTO `$lalierp` VALUES ('','$r_id','CODSMS','$mysqlnow','$smstxt','$r_smsemetteur','$rets[2]','1')");
				mysql_query("INSERT INTO `$lalierp` 
		(`ref`, `sujet`, `date`, `rapport`, `message`, `code`, `moule`, `active`) VALUES 
		('$r_id','CODSMS',NOW(),'$rapp','$smstxt','$r_smsemetteur','sms','1')");
				$regid = mysql_insert_id();
				mysql_close($conn);		
				
				/*if(trim($rets[0])=="0"){
					$fp = fopen("$style_url/smscounter.php?ref=$r_id&numb=$numb","rb");					
				}*/
			}
				
		 }
		  $_SESSION['smstxt']="";	
		  echo"<script language='javascript'>document.location='./?archives&id=$regid';</script>";
		
	}
	}
	else{
		echo"Aucun de vos contacts ne comporte de numéro de mobile valide...";
	}
	}
	else{
		echo"Vous n'avez pas ou plus de crédits SMS.<br>
		Pour en commander, veuillez nous contacter ou utiliser le lien suivant:<br><br><center>
		<b><a href='http://www.adeli.wac.fr/sms/' target='_blank'>Acheter des crédits SMS</a></b></center><br><br><br>";
	}

	echo"</td></tr><tr><td align='left' class='bando'>
	> <a href='http://www.adeli.wac.fr/sms/' target='_blank'>Acheter des crédits SMS</a>
	</td></tr></table>";
?>