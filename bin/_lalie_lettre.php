<?php // 309 > LALIE Lettre ;

insert('fpdf');
$fontfree=false;
if(!is_dir("lalie/fonts")) @mkdir("lalie/fonts");
if(is_dir("lalie/fonts")) $fontfree=true;
insert('_lalie_lettre_font');
if(is_file('bin/_lalie_lettre_font.php')){
	$openimg='bin/_lalie_lettre_font.php';
}
else{
	$openimg="$style_url/update.php?file=_lalie_lettre_font.php";
}
$step = abs($_GET['step']);
	if(!isset($_SESSION['lettretxt'])){
		$_SESSION['lettretxt']="";
		$_SESSION['lettretitre']="";
		$_SESSION['lettresigne']="";
		$_SESSION['lettredate']="le : ".date("d/m:Y");
	}
	if(isset($_POST['cad'])){
		$_SESSION['lettretxt'] = stripslashes($_POST['html']);
		$_SESSION['lettretitre'] = stripslashes($_POST['title']);
		$_SESSION['lettresigne']=stripslashes($_POST['author']);
		$_SESSION['lettredate']=stripslashes($_POST['date']);
		$_SESSION['lettrecadre']=stripslashes($_POST['cadre']);
		if( isset($_GET['addente'])){
			if( $_FILES['ente']['name']!=NULL ){
				if(copy($_FILES['ente']['tmp_name'],"mconfig/entente.jpg")){
					$return.=returnn("fichier d'entête chargé avec succès","009900",$vers,$theme);
				}
				else{
					$return.=returnn("chargement de fichier d'entête \"".$_FILES['ente']['name']."\" échoué","990000",$vers,$theme);
				}
			}
			set_pref('enventpos.conf',$_POST['enventpos']);
		}
		if( isset($_GET['setcol']) ){
			set_pref('lettrecouleurbandeau.conf',$_POST['col']);
		}
		if( isset($_POST['font']) ){
			set_pref('lettrefont.conf',$_POST['font']);
		}
		if( isset($_POST['relax']) ){
			set_pref('lettrerelax.conf',$_POST['relax']);
		}
		if( isset($_POST['relay']) ){
			set_pref('lettrerelay.conf',$_POST['relay']);
		}
		if( isset($_GET['setcad']) ){
			set_pref('lettrecadre.conf',$_POST['cad']);
			if( $_FILES['police']['name']!=NULL ){
				if(copy($_FILES['police']['tmp_name'],"lalie/fonts/".$_FILES['police']['name'])){
					$return.=returnn("fichier de police chargé avec succès","009900",$vers,$theme);
				}
				else{
					$return.=returnn("chargement de fichier de police \"".$_FILES['police']['name']."\" échoué","990000",$vers,$theme);
				}
				set_pref('lettrefont.conf',$_FILES['police']['name']);
			}
			
		}
		if( isset($_GET['setligne']) ){
			set_pref('lettreligne.conf',$_POST['lig']);
		}
	}
	
	function mm2px($mm){
		return round($mm*(595/21)/20);
	}
	
	$lettretxt =$_SESSION['lettretxt']; 
	$lettretitre =$_SESSION['lettretitre']; 
	$lettresigne =$_SESSION['lettresigne'];
	$lettredate =$_SESSION['lettredate']; 
	$currentstep = $step+1;
	
	$actouno = array("","checked");
	$actoudos = array("checked","");
	echo"
	
	<script language='javascript' type='text/javascript'>
	
	var clientPC = navigator.userAgent.toLowerCase(); // Get client info
	var is_gecko = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
var is_safari = ((clientPC.indexOf('applewebkit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
// For accesskeys
var is_ff2_win = (clientPC.indexOf('firefox/2')!=-1 || clientPC.indexOf('minefield/3')!=-1) && clientPC.indexOf('windows')!=-1;
var is_ff2_x11 = (clientPC.indexOf('firefox/2')!=-1 || clientPC.indexOf('minefield/3')!=-1) && clientPC.indexOf('x11')!=-1;
	function insertVar(sampleText,tag,arg) {
	if(tag!=null){
		tagClose='</'+tag+'>';
		tagOpen='<'+tag;
		if(arg!=null){
			tagOpen+=' '+arg;
		}
		tagOpen+='>';
	}
	else{
		tagClose='';
		tagOpen='';
	}
	txtarea = document.fourmis.html;


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
		
	<table style='width:600px;' cellspacing='0' cellpadding='5' border='0'>
   <tr style='height:20px'><td class='buttontd'><span class='textegras'>édition de lettre</span> étape $currentstep/3 </td></tr>
   <tr><td class='cadrebas'>";
   	$fd="";
	if(isset($_GET['sbg'])){
		$sbg = $_GET['sbg'];
		if($sbg=='none'){
			unlink("mconfig/lettrefond.jpg");
		}
		else{
			$fd = @fopen("http://adeli.wac.fr/vers/$vers/lalie/lettres/$sbg.jpg","rb");
			if($fd){	
				$fc = @fopen("mconfig/lettrefond.jpg","w+");
				while (!feof ($fd)) {
						$buffer = @fgets($fd, 4096);
						@fwrite($fc,$buffer);
				}
				@fclose($fd);
				@fclose($fc);
			}
		}
	}
	if(is_file("mconfig/lettrefond.jpg")){
		$fd="background:url(mconfig/lettrefond.jpg?".time()."=refresh) no-repeat top left;";
	}
	$co = get_pref('lettrecouleurbandeau.conf');
	if($co=='') $co="none";
	
	$fo = get_pref('lettrefont.conf');
	
	$bgco="";
	if(strlen($co)==6){
		$bgco="bgcolor='$co'";
	}
	
	$rx=abs(get_pref('lettrerelax.conf'));	
	$ry=abs(get_pref('lettrerelay.conf'));
	
	if(!is_numeric($rx)) $rx = abs($rx);
	if(!is_numeric($ry)) $ry = abs($ry);
	
	$ca = get_pref('lettrecadre.conf');
	if($ca=='') $ca="1";
	$cadre="";
	if($ca==1){
		$cadre="border-color:#CCCCCC;border-width:1px;border-style:solid";
	}
	
	$li = get_pref('lettreligne.conf');
	if($li=='') $li="1";	
	$ligne="";
	if($li==1){
		$ligne="border-color:#CCCCCC;border-width:0px;border-top-width:1px;border-style:solid";
	}
	
	$enventpos = get_pref('enventpos.conf');
	if($enventpos=='') $enventpos="tl";	
	

	
if($step==0){
	echo"
	<table cellpadding='0' cellspacing='5'><tr>
	<td valign='top' class='cadrebas'>
	
	<table width='200' cellspacing='0' cellpadding='5'><tr><td class='buttontd'><b>Enveloppe</b></td></tr>
	<tr><td>
			- <a href='./?option=$option&part=$part&step=1&moule=env-1010'>10x10 cm</a><br>
			- <a href='./?option=$option&part=$part&step=1&moule=env-1015'>10x15 cm</a><br>
			- <a href='./?option=$option&part=$part&step=1&moule=env-1616'>16x16 cm</a><br>
			- <a href='./?option=$option&part=$part&step=1&moule=env-1021'>10x21 cm</a><br>
			- <a href='./?option=$option&part=$part&step=1&moule=env-2121'>21x21 cm</a><br>
			- <a href='./?option=$option&part=$part&step=1&moule=env-2130'>21x29,7 cm</a><br>
		
	</td></tr></table>
	</td><td valign='top' class='cadrebas'>
	
	<table width='200' cellspacing='0' cellpadding='5'><tr><td class='buttontd'><b>Etiquettes</b></td></tr>
	<tr><td>
			- <a href='./?option=$option&part=$part&step=1&moule=eti-planche&crea'>Planche d'étiquettes</a><br>
		
	</td></tr></table>
	</td><td valign='top' class='cadrebas'>
	<table width='200' cellspacing='0' cellpadding='5'><tr><td class='buttontd'><b>Lettre</b></td></tr>
	<tr><td>
		
			- <a href='./?option=$option&part=$part&step=1&moule=let-a4'>Lettre A4</a><br>
		
	</td></tr></table>
	</td></tr></table>
	";

}
else{
	if(isset($_GET['moule'])){
		$_SESSION['moule']=$_GET['moule'];
	}
	$moule=$_SESSION['moule'];
	$typemou=substr($moule,0,3);
	$typequi=substr($moule,4,strlen($moule));
	
		$dim  = array(100,100);
		$adx = 35;
		 $ady = 80;
		if($typequi=="1010"){
		 $dim = array(100,100);
		 $adx = 35;
		 $ady = 50;
		}
		if($typequi=="1015"){
		 $dim = array(150,100);
		 $adx = 50;
		 $ady = 50;
		}
		if($typequi=="1616"){
		 $dim = array(160,160);
		 $adx = 50;
		 $ady = 80;
		}
		if($typequi=="1021"){
		 $dim = array(210,100);
		 $adx = 70;
		 $ady = 50;
		}
		if($typequi=="2121"){
		 $dim = array(210,210);
		 $adx = 70;
		 $ady = 100;
		}
		if($typequi=="2130"){
		 $dim = array(297,210);
		 $adx = 120;
		 $ady = 100;
		}
		
		$adx+=$rx;
		$ady+=$ry;
		
		$py = $enventpos[0];
		$px = $enventpos[1];
		$ety=5;
		$etx=5;
		
		$enth = 50;
		$entw = 50;
		if(is_file("mconfig/entente.jpg")){
			$si = getimagesize("mconfig/entente.jpg");
			$enth = round($si[1]*35/100);
			$entw = round($si[0]*35/100);
		}
		if($py=='t') $ety=5;
		if($py=='c') $ety=(($dim[1]-$enth)/2);
		if($py=='b') $ety=($dim[1]-$enth);
		if($px=='l') $etx=5;
		if($px=='c') $etx=(($dim[0]-$entw)/2);
		if($px=='r') $etx=($dim[0]-$entw);
		
		$paw = $dim[0]-$adx;
		$pah = $dim[1]-$ady;
		$adw = round($paw*100/35);
		$adh = round($pah*100/35);
		
}
$posrely = array('t','c','b');
$posrelx = array('l','c','r');
if($step==1){
	
	if(isset($_GET["recup"])){
		$recup = $_GET['recup'];
		$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
		$res = mysql_query("SELECT `code`,`moule` FROM `$lalierp` WHERE id='$recup'");
		$ro = mysql_fetch_object($res);
		eval($ro->code);
		$_SESSION['moule'] = $ro->moule;
		deconnecte($conn);
	}

	echo"
  <form action='./?option=$option&part=$part&step=2' method='post' name='fourmis' enctype='multipart/form-data'>
  <table><tr><td>
  ";
  
  if($typemou=="let"){///////////////////////// LETTRE
   if(isset($_GET['chf'])){
   	    if(fopen("http://adeli.wac.fr/vers/$vers/lalie_get_lbg.php","rb")){			
			echo"<br>
			- choisissez le modèle de fond
			<br><br>
			- <a href='./?lettre&step=1&sbg=none'>aucun</a><br><br>";
			include("http://adeli.wac.fr/vers/$vers/lalie_get_lbg.php");
		}
   }
   else{
   	echo"<a href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&step=1&chf';document.fourmis.submit()\">changer le fond</a>";
   }
	echo"
	</td></tr></table>
	<hr>
  <table style='background-color:#FFFFFF;$fd' cellpadding='5' cellspacing='0'>
  <tr>
   <td colspan='2'></td></tr>
  <tr $bgco>
  <td>
    <b>entête</b><br>";
	if(is_file("mconfig/entente.jpg")){
		echo"<img src='mconfig/entente.jpg' alt='entete' width='300'>";
	}
	echo"<br>
	<input type='file' name='ente' onchange=\"document.fourmis.action='./?option=$option&part=$part&step=1&addente'\">
	<input type='submit' value='charger'>
	<br>
	couleur du bandeau :
	<input type='text' name='col' value='$co'  maxlength='6' size='6' onchange=\"document.fourmis.action='./?option=$option&part=$part&step=1&setcol';document.fourmis.submit()\">
	";
	echo colorpicker("col",$co,"document.fourmis.col.value='COLOR';document.fourmis.action='./?option=$option&part=$part&step=1&setcol';document.fourmis.submit();",-50,"<img border='none' src='$style_url/images/fgcolor.gif' alt='couleur de texte'>",5);
	echo"
   </td>
   <td valign='top' align='right'>
   <input type='text' name='date' value=\"$lettredate\"><br><br>
   
   <table style='height:153px;$cadre'><tr><td>
   <b>cadre d'adresse</b><br>
   <b>encadré</b> : 
   oui <input type=\"radio\" name=\"cad\" value=\"1\" $actouno[$ca] onclick=\"document.fourmis.action='./?option=$option&part=$part&step=1&setcad';document.fourmis.submit()\">
   non <input type=\"radio\" name=\"cad\" value=\"0\" $actoudos[$ca] onclick=\"document.fourmis.action='./?option=$option&part=$part&step=1&setcad';document.fourmis.submit()\">
   </td></tr></table>
   </td>
   </tr>
   <tr>
   <td colspan='2'>
	
   <b>Titre</b> : <input type=\"text\" value=\"$lettretitre\" style='width:400px;' name=\"title\">
	</td></tr>
	<tr>
   <td colspan='2'>
	
	<b>Lettre</b>
	<table><tr class='buttontd'><td width='100'>
	<select onchange='insertVar(this.value);this.value=0;'>
	<option value='0'>insérer une variable</option>
		
		<option value='$"."nom'>nom</option>
		<option value='$"."portable'>portable</option>
		<option value='$"."email'>email</option>
	
	</select>
	</td><td>
	<img src='$style_url/images/bold.gif' onclick=\"insertVar('texte','b','')\" alt='gras'>
	<img src='$style_url/images/italic.gif' onclick=\"insertVar('texte','i','')\" alt='italic'>
	</td></tr>
	<tr><td colspan='2'>
	<textarea name='html' style='width:400px;height:500px;'>$lettretxt</textarea>
	</td></tr>
	<tr><td colspan='2' style='height:30px;$ligne'>	
	   <b>ligne</b> : 
   oui <input type=\"radio\" name=\"lig\" value=\"1\" $actouno[$li] onclick=\"document.fourmis.action='./?option=$option&part=$part&step=1&setligne';document.fourmis.submit()\">
   non <input type=\"radio\" name=\"lig\" value=\"0\" $actoudos[$li] onclick=\"document.fourmis.action='./?option=$option&part=$part&step=1&setligne';document.fourmis.submit()\">
   <br>
	<b>bas de page</b> : <input type=\"text\" value=\"$lettresigne\" style='width:400px;' name=\"author\">
	</td></tr>
	</table>";
	}
	elseif($typemou=="env"){ //////////////////////////////////////////// ENVELOPPE
		//if($typequi=="carre"){
				echo"
				</td></tr></table>
				
			  <table style='background-color:#FFFFFF; border:#000000 1px solid; border-bottom-width:3px; border-right-width:2px; width:$dim[0]mm; height:$dim[1]mm; margin:10px;' cellpadding='0' cellspacing='0'>
			  <tr>
			  <td valign='top' align='left'><div style='position:relative;'>
				<div style='position:absolute; top:$ety"."mm; left:$etx"."mm; border:#CCCCCC 1px dashed; padding:5px;'><a style='cursor:pointer' onclick=\"document.getElementById('diventete').style.visibility='visible';document.fourmis.action='./?option=$option&part=$part&step=1&addente'\">";
				if(is_file("mconfig/entente.jpg")){
					echo"<img src='mconfig/entente.jpg' alt='entete'>";
				}
				else{
					echo"Modifier l'entête";
				}
				echo"</a>
				<div id='diventete' style='position:absolute; visibility:hidden; background:#FFFFFF; top:10px; left:10px; border:#CCCCCC 1px solid; padding:5px;'>
				<b>Image</b><br>
				<input type='file' name='ente'>
				
				<br><br>
				<b>Position</b><br>
				<table bgcolor='#999999' cellspacing='1' cellpadding='3'>";
				for($y=0; $y<3 ; $y++){
					echo'<tr>';
						for($x=0; $x<3 ; $x++){
							$s='';
							if($enventpos == $posrely[$y].$posrelx[$x]) $s='checked';
							echo"<td bgcolor='#FFFFFF'><input type='radio' name='enventpos' value='$posrely[$y]$posrelx[$x]' $s></td>";
						}
					echo'</tr>';
				}
					echo"
				</table>
				<br>
				<p align='right'>
				<a style='cursor:pointer' onclick=\"document.getElementById('diventete').style.visibility='hidden';document.fourmis.action='./?option=$option&part=$part&step=2'\">Fermer</a>
				<input type='submit' value='Valider'>
				</p>
				</div>
			   </div>
			   <div style='position:absolute;height:153px; top:$ady"."mm; left:$adx"."mm;padding:5px;$cadre'>
				 <a style='cursor:pointer' onclick=\"document.getElementById('divadres').style.visibility='visible';document.fourmis.action='./?option=$option&part=$part&step=1&setcad'\">";
				if($fontfree){
					echo"<img src='$openimg?str=Adresse du destinataire&w=$adw&h=$adh&s=12&font=$fo' border='none'>";
				}
				else{
				 echo"<b>Adresse</b>";
				}
				 echo"</a>
			   <div id='divadres' style='position:absolute; visibility:hidden; background:#FFFFFF; top:10px; left:10px; width:300px; border:#CCCCCC 1px solid; padding:5px;'>
			   <b>Encadré</b><br> 
			   oui <input type=\"radio\" name=\"cad\" value=\"1\" $actouno[$ca]>
			   non <input type=\"radio\" name=\"cad\" value=\"0\" $actoudos[$ca]>
			   <br><br>";
			   if($fontfree){
				   echo"
				   <b>Police</b><br>";
				   $dir = dir("lalie/fonts");
				   while(false !== $entry = $dir->read()){
				   	if(is_file("lalie/fonts/$entry")){
						$s='';
						if($fo == $entry) $s='checked';
						echo"<input type='radio' name='font' value='$entry' $s><img src='$openimg?str=$entry&w=150&h=12&s=8&font=$entry' border='none' alt='$entry'><br>";
					}
				   }
				   echo"<br>Ajouter une nouvelle police de caractère (.ttf)
				   <input type='file' name='police'><br><br>";
			   }
			   echo"
			   <b>Position relative</b>
			   <table><tr><td>
			   Horizontal : </td><td><select name='relax'>";
				for($i=5-round($dim[0]/2) ; $i<round($dim[0]/2) ; $i+=5){
					$s='';
					if($i==$rx) $s='selected';
					$si = '';
					if($i>=0) $si='+';
					echo"<option value='$i' $s>$si $i mm</option>";
				}			   
			   echo"</select></td></tr><tr><td>
			   Vertical : </td><td><select name='relay'>";
				for($i=5-round($dim[1]/2) ; $i<round($dim[1]/2) ; $i+=5){
					$s='';
					if($i==$ry) $s='selected';
					$si = '';
					if($i>=0) $si='+';
					echo"<option value='$i' $s>$si $i mm</option>";
				}		   
			   echo"</select></td></tr></table>
				<p align='right'>
				<a style='cursor:pointer' onclick=\"document.getElementById('divadres').style.visibility='hidden';document.fourmis.action='./?option=$option&part=$part&step=2'\">Fermer</a>
				<input type='submit' value='Valider'>
				</p>
				</div>
			   </div>
			   </div>
			   ";
		//}
	}
	elseif($typemou=="eti"){ //////////////////////////////////////////// ETIQUETTE
		if($typequi=="planche"){
			if(!isset($_SESSION['page_largeur']) || isset($_GET['crea'])){
			 	$_SESSION['page_largeur']=210;
				$_SESSION['page_hauteur']=297;
				$_SESSION['nb_colones']=2;
				$_SESSION['nb_lignes']=4;
				$_SESSION['marge_haut']=10;
				$_SESSION['marge_bas']=10;
				$_SESSION['marge_droite']=5;
				$_SESSION['marge_gauche']=5;
				$_SESSION['espace_horizontal']=2;
				$_SESSION['espace_vertical']=0;
			}	
			if(isset($_POST['page_largeur'])){
				$_SESSION['page_largeur'] = $_POST['page_largeur'];
				$_SESSION['page_hauteur'] = $_POST['page_hauteur'];
				$_SESSION['nb_colones'] = $_POST['nb_colones'];
				$_SESSION['nb_lignes'] = $_POST['nb_lignes'];
				$_SESSION['marge_haut'] = $_POST['marge_haut'];
				$_SESSION['marge_bas'] = $_POST['marge_bas'];
				$_SESSION['marge_droite'] = $_POST['marge_droite'];
				$_SESSION['marge_gauche'] = $_POST['marge_gauche'];
				$_SESSION['espace_horizontal'] = $_POST['espace_horizontal'];
				$_SESSION['espace_vertical'] = $_POST['espace_vertical'];
			}
			

			
			$px_page_largeur = mm2px($_SESSION['page_largeur']);
			$px_page_hauteur = mm2px($_SESSION['page_hauteur']);
			$px_marge_haut = mm2px($_SESSION['marge_haut']);
			$px_marge_bas = mm2px($_SESSION['marge_bas']);
			$px_marge_droite = mm2px($_SESSION['marge_droite']);
			$px_marge_gauche = mm2px($_SESSION['marge_gauche']);
			$px_espace_horizontal = mm2px($_SESSION['espace_horizontal']);
			$px_espace_vertical = mm2px($_SESSION['espace_vertical']);
			$px_nb_colones = $_SESSION['nb_colones'];
			$px_nb_lignes = $_SESSION['nb_lignes'];
			$px_cellule_hauteur = ($px_page_hauteur-$px_marge_bas-$px_marge_haut-($px_espace_vertical*$px_nb_lignes))/$px_nb_lignes;
			$px_cellule_largeur = ($px_page_largeur-$px_marge_gauche-$px_marge_droite-($px_espace_horizontal*$px_nb_colones))/$px_nb_colones;
			echo"
			
			
			<table><tr><td valign='top'>
			
			<table>
				<tr><td colspan='2' class='textegrasfonce'><br>Page</td></tr>
				<tr><td>Largeur : </td><td><input size='3' type='text' name='page_largeur' value='".$_SESSION['page_largeur']."'>mm</td></tr>
				<tr><td>Hauteur : </td><td><input size='3' type='text' name='page_hauteur' value='".$_SESSION['page_hauteur']."'>mm</td></tr>
				
				<tr><td colspan='2' class='textegrasfonce'><br>Marges</td></tr>
				<tr><td>Haut : </td><td><input size='3' type='text' name='marge_haut' value='".$_SESSION['marge_haut']."'>mm</td></tr>
				<tr><td>Droite : </td><td><input size='3' type='text' name='marge_droite' value='".$_SESSION['marge_droite']."'>mm</td></tr>
				<tr><td>Bas : </td><td><input size='3' type='text' name='marge_bas' value='".$_SESSION['marge_bas']."'>mm</td></tr>
				<tr><td>Gauche : </td><td><input size='3' type='text' name='marge_gauche' value='".$_SESSION['marge_gauche']."'>mm</td></tr>
				
				<tr><td colspan='2' class='textegrasfonce'><br>Etiquettes</td></tr>
				<tr><td>Nombre de lignes : </td><td><input size='3'type='text' name='nb_lignes' value='".$_SESSION['nb_lignes']."'>mm</td></tr>
				<tr><td>Nombre de colones : </td><td><input size='3' type='text' name='nb_colones' value='".$_SESSION['nb_colones']."'>mm</td></tr>
				<tr><td>Espacement horizontal : </td><td><input size='3' type='text' name='espace_horizontal' value='".$_SESSION['espace_horizontal']."'>mm</td></tr>
				<tr><td>Espacement vertical : </td><td><input size='3' type='text' name='espace_vertical' value='".$_SESSION['espace_vertical']."'>mm</td></tr>

				<tr><td colspan='2' align='right'>
				<input type='button' onclick=\"document.fourmis.action='./?option=$option&part=$part&step=1&addente';document.fourmis.submit()\" value='modifier'>
				</td></tr>
			</table>
			
			</td><td valign='top'>
			
			
			<div style='width:".($px_page_largeur+12).";padding-top:1px;padding-left:1px;padding-right:2px;padding-bottom:2px;background:#CCCCCC'>		  	
			<div style='width:".($px_page_largeur+9).";padding-top:1px;padding-left:1px;padding-right:2px;padding-bottom:2px;background:#BBBBBB'>
		  	<div style='width:".($px_page_largeur+6).";padding-top:1px;padding-left:1px;padding-right:2px;padding-bottom:2px;background:#AAAAAA'>
		  	<div style='width:".($px_page_largeur+3).";padding-top:1px;padding-left:1px;padding-right:2px;padding-bottom:2px;background:#999999'>
		  	<div style='position:relative;background:#FFFFFF;border:#000000 thin solid;width:$px_page_largeur;height:$px_page_hauteur'>
			
			";
			$to = $px_marge_haut;
			$ce = 0;
			for($l=0 ; $l<$px_nb_lignes ; $l++){
				$le = $px_marge_gauche;
				for($c=0 ; $c<$px_nb_colones ; $c++){
					$ce++;
					echo"<div style='position:absolute;border:#000000 thin solid;width:$px_cellule_largeur;height:$px_cellule_hauteur;top:$to;left:$le;display:table-cell;vertical-align:middle;text-align:center'>$ce</div>";
					$le+=	$px_cellule_largeur+$px_espace_horizontal;
				}	
				$to+=	$px_cellule_hauteur+$px_espace_vertical;	
			}
			
			echo"</div></div></div></div></div>
			
			</td></tr></table>
			
		   ";
		}
	}
	echo"	
	</td></tr>
	</table>
	<p align='right'><input type='submit' class='buttontd' value='continuer'></p>
	</form>
	";
}
elseif($step==2){

$prtxt = nl2br($_SESSION['lettretxt']);


	echo"
	<table><tr>
		<td>";
	if($typemou=="let"){///////////////////////// LETTRE	
		echo"
   		<table style='width:595px;height:842px;background-color:#FFFFFF;$fd;border-style:dashed;border-width:1px;border-color:#AAAAAA' cellspacing='0' cellpadding='20'>
		<tr style='height:100px' $bgco><td align='left' valign='top'>";
	
	if(is_file("mconfig/entente.jpg")){
		echo"<img src='mconfig/entente.jpg' alt='entete' width='300'>";
	}
	echo"</td><td align='right' valign='top'><font color='#000000'>$lettredate</font></td>
		</tr>
		<tr style='height:153px'><td></td><td style='width:433px;$cadre' align='left' valign='top'><font color='#000000'>Adresse des destinataires</font></td>
		</tr>
		<tr><td colspan='2' valign='top'><font color='#999999'>
		".nl2br($lettretitre)."</font><br><br><font color='#000000'>
		".nl2br($lettretxt)."
		</font></td></tr>
		<tr><td style='height:40px;$ligne' colspan='2' align='center' valign='bottom'>
		<font color='#000000'>$lettresigne</font>
		</td></tr>
		</table>";
	}
	elseif($typemou=="env"){///////////////////////// ENVELOPPE	
			echo"
			<table style='background-color:#FFFFFF; border:#000000 1px solid; border-bottom-width:3px; border-right-width:2px; width:$dim[0]mm; height:$dim[1]mm; margin:10px;' cellpadding='0' cellspacing='0'>
			  <tr>
			  <td valign='top' align='left'><div style='position:relative;'>
				<div style='position:absolute; top:$ety"."mm; left:$etx"."mm; border:#CCCCCC 1px dashed; padding:5px;'>";
				if(is_file("mconfig/entente.jpg")){
					echo"<img src='mconfig/entente.jpg' alt='entete'>";
				}
				else{
					echo"Entête";
				}
				echo"
			   </div>
			   <div style='position:absolute;height:153px; top:$ady"."mm; left:$adx"."mm;padding:5px;$cadre'>
				 ";
				if($fontfree){
					echo"<img src='$openimg?str=Adresse du destinataire&w=$adw&h=$adh&s=12&font=$fo' border='none'>";				}
				else{
				 echo"<b>Adresse</b>";
				}
				 echo"
			   </div>
			   </div>
			</td>
			</tr>
			</table>";
	}
	elseif($typemou=="eti"){///////////////////////// ETIQUETTES	
			
			$px_page_largeur = mm2px($_SESSION['page_largeur']);
			$px_page_hauteur = mm2px($_SESSION['page_hauteur']);
			$px_marge_haut = mm2px($_SESSION['marge_haut']);
			$px_marge_bas = mm2px($_SESSION['marge_bas']);
			$px_marge_droite = mm2px($_SESSION['marge_droite']);
			$px_marge_gauche = mm2px($_SESSION['marge_gauche']);
			$px_espace_horizontal = mm2px($_SESSION['espace_horizontal']);
			$px_espace_vertical = mm2px($_SESSION['espace_vertical']);
			$px_nb_colones = $_SESSION['nb_colones'];
			$px_nb_lignes = $_SESSION['nb_lignes'];
			$px_cellule_hauteur = ($px_page_hauteur-$px_marge_bas-$px_marge_haut-($px_espace_vertical*$px_nb_lignes))/$px_nb_lignes;
			$px_cellule_largeur = ($px_page_largeur-$px_marge_gauche-$px_marge_droite-($px_espace_horizontal*$px_nb_colones))/$px_nb_colones;
			echo"<div style='position:relative;background:#FFFFFF;border:#000000 thin solid;width:$px_page_largeur;height:$px_page_hauteur'>";
			$to = $px_marge_haut;
			$ce = 0;
			for($l=0 ; $l<$px_nb_lignes ; $l++){
				$le = $px_marge_gauche;
				for($c=0 ; $c<$px_nb_colones ; $c++){
					$ce++;
					echo"<div style='position:absolute;border:#000000 thin solid;width:$px_cellule_largeur;height:$px_cellule_hauteur;top:$to;left:$le;display:table-cell;vertical-align:middle;text-align:center'>$ce</div>";
					$le+=	$px_cellule_largeur+$px_espace_horizontal;
				}	
				$to+=	$px_cellule_hauteur+$px_espace_vertical;	
			}			
			echo"</div>";
	}
	
	
	
		echo"
		</td>
		<td>
		<form action='./?option=$option&part=$part&moule=$prevurl$moule&step=3' method='post' name='glob'>
					
					
		
							<table  width='120' cellspacing='1' cellpadding='1'  style='border-width:1px; border-style:solid; border-color:CCCCCC CCCCCC CCCCCC CCCCCC;'>
					<tr><td align='left' valign='top'>						
							<font color='999999'><b>envoyer à:</b></font><br>
							<input type='radio' name='aki' value='g' onclick=\"akito('g')\"> Groupes<br>
							<input type='checkbox' name='tous' value='1' onclick=\"selectall();akito('g')\">tous<br>
							";		
							for($i=0 ; $i<sizeof($groups) ; $i++){
								$grpupforjava = ereg_replace("_","",$groups[$i]);
								$grpforhuman = ereg_replace("_"," ",$groups[$i]);
								$conn = connecte($base, $host, $login, $passe);	
								$resultnum = mysql_query("SELECT `groupe` FROM `$dblist` WHERE `groupe`='$groups[$i]' AND `adresse`!='' $wherelalaie");
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
								   	debutsol += \"<option value='\"+valeur+\"'>\"+valeur+\"</option>\";
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
										debutsel += \"<option value='\"+tousmel[i]+\"'>\"+tousmel[i]+\"</option>\";
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
					<a href='./?option=$option&part=$part&step=1' title='retour' class='buttontd'><b>retour</b> à l'édition</a>
					<a href='#' onclick='sendmailer()' title='envoyer' class='buttontd'><b>envoyer</b></a>
		
		
		
		</td>
	</table></tr>	

	</form>";
}
elseif($step==3){
	$kelenv = $_POST['aki'];
	$libreki = $_POST['libreki'];
	if($kelenv == "g"){
		$stradr=array();
		for($i=0 ; $i<sizeof($groups) ; $i++){
			if(isset($_POST["g$groups[$i]"])){
				if($groups[$i] == "sans_groupe"){
					$groups[$i] = "";
				}
				$typeenv.=" $groups[$i] ";
				$conn = connecte($base, $host, $login, $passe);	
					$res = mysql_query("SELECT `adresse` FROM `$dblist` WHERE `adresse`!='' $wherelalaie AND `groupe`='$groups[$i]'");
					while($ro = mysql_fetch_object($res)){
						array_push($stradr,$ro->adresse);
					}
				deconnecte($conn);
			}
		}
	}
	elseif($kelenv == "u"){
		$stradr = split("|",$_POST["libreki"]);
	}
	///////////////////////////////////////////////////////////////////
	if($typemou=="let"){ ////////////////////////////////////// LETTRE
		$pdf = new LETTRE( 'P', 'mm', 'A4' );
		$pdf->Open();
		$pdf->SetAutoPageBreak(0);
		$entete_img="";
		if(is_file("mconfig/entente.jpg")){
			$entete_img = "mconfig/entente.jpg";
		}
		$fond_img="";
		if(is_file("mconfig/lettrefond.jpg")){
			$fond_img = "mconfig/lettrefond.jpg";
		}
		
		$nbtot = sizeof($stradr);
		
		if($co!=''){
			$co = hexdec(substr($co, 0, 2)).','.hexdec(substr($co, 2, 2)).','.hexdec(substr($co, 4, 2));
		}
		
		$code = ereg_replace("'","''","
		$"."lettretxt =\"$lettretxt\"; 
		$"."lettretitre =\"$lettretitre\"; 
		$"."lettresigne =\"$lettresigne\";
		$"."lettredate =\"$lettredate\";
		");
		$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
		mysql_query("INSERT INTO `$lalierp` 
		(`ref`, `sujet`, `date`, `rapport`, `message`, `code`, `moule`, `active`) VALUES 
		('$r_id','".str_replace("'","''",$lettretitre)."',NOW(),'$nbtot','".str_replace("'","''",$lettretxt)."','$code','lettre','1')");
		$regid = mysql_insert_id();
		mysql_close($conn);
		
		echo"<br><br>$nbtot destinataires<br><br>
		<a href='lalie/$regid.pdf' target='_blank'>télécharger le pdf</a>";
		
				
		for($n=0 ; $n<$nbtot ; $n++){
			$html = $lettretxt;
			$clienadress = trim(strip_tags($stradr[$n]));
			$adclienadress = preg_replace("/([0-9][0-9][0-9][0-9][0-9])/","\n$1",$clienadress);
			if($clienadress!=''){
			$key=ereg_replace("'","''",$clienadress);
			$conn = connecte($base, $host, $login, $passe);	
				$rem = mysql_query("SELECT * FROM `$dblist` WHERE 1 $wherelalaie AND `adresse`LIKE'%$key%'");
				$rom = mysql_fetch_object($rem);
			deconnecte($conn);
			$cliennom= $rom->nom;
			$clienmail= $rom->email;
			$clienportable= $rom->portable;
		
				$html = str_replace('$nom',$cliennom ,$html);
				$html = str_replace('$email',$clienmail ,$html);
				$html = str_replace('$portable',$clienportable ,$html);
		
		$date=date("Y-m-d");
		
		$adclienadress = $cliennom."\n".$adclienadress;
		
		
		$pdf->ajout($lettretitre,$date,$adclienadress,$lettresigne,$entete_img,$fond_img,$co,$ca,$li);
		$_SESSION['y']=97;
	
		
		$pdf->SetXY(20,$_SESSION['y']);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Arial','',12);
		$pdf->bi=true;
		$html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr><b><i><u><strong><em>");
		
	
			//$html=str_replace("\n",' ',$html); 
			$html = str_replace('&trade;','™',$html);
			$html = str_replace('&copy;','©',$html);
			$html = str_replace('&euro;','€',$html);
			$html= html_entity_decode ($html);
	
			$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
			$skip=false;
			foreach($a as $i=>$e){
				if (!$skip) {
					if($pdf->HREF)
						$e=str_replace("\n","",str_replace("\r","",$e));
					if($i%2==0){
						if($pdf->PRE) $e=str_replace("\r","\n",$e);
						else $e=str_replace("\r","",$e);
						if($pdf->HREF) {
							$pdf->PutLink($pdf->HREF,$e);
							$skip=true;
						} 
						else{
							$pdf->Write(5,stripslashes( ($e)));
						}
					}
					 else {
						if (substr(trim($e),0,1)=='/')
							$pdf->CloseTag(strtoupper(substr($e,strpos($e,'/'))));
						else {
							$a2=explode(' ',$e);
							$tag=strtoupper(array_shift($a2));
							$attr=array();
							foreach($a2 as $v) if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3)) {
								$attr[strtoupper($a3[1])]=$a3[2];
							}
							$pdf->OpenTag($tag,$attr);
						}
					}
				} 
				else {
					$pdf->HREF='';
					$skip=false;
				}
			}
		
	
			}
	
		
		
		}
	}
	elseif($typemou=="env"){ ///////////////////////////////////// ENVELOPPE
		
		$pdf = new LETTRE( 'P', 'mm', $dim );
		$pdf->Open();
		$pdf->SetAutoPageBreak(0);
		$entete_img="";
		if(is_file("mconfig/entente.jpg")){
			$entete_img = "mconfig/entente.jpg";
		}
		$nbtot = sizeof($stradr);
		$pdf->SetDrawColor(200,200,200);
		$pdf->SetTextColor(0, 0, 0);
		//$pdf->AddFont('Century Gothic');
		$pdf->SetFont('Arial','',12);
		//$pdf->SetFont('Century Gothic','',12);
		$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
		//mysql_query("INSERT INTO `$lalierp` VALUES ('','$r_id','".ereg_replace("'","''",$lettretitre)."','$mysqlnow','$nbtot','".ereg_replace("'","''",$lettretxt)."','$code','lettre','1')");
		mysql_query("INSERT INTO `$lalierp` 
		(`ref`, `sujet`, `date`, `rapport`, `message`, `code`, `moule`, `active`) VALUES 
		('$r_id','".str_replace("'","''",$lettretitre)."',NOW(),'$nbtot','".str_replace("'","''",$lettretxt)."','$code','lettre','1')");
		$regid = mysql_insert_id();
		mysql_close($conn);
		
		echo"<br><br>$nbtot destinataires<br><br>
		<a href='lalie/$regid.pdf' target='_blank'>télécharger le pdf</a>";
		
				
		for($n=0 ; $n<$nbtot ; $n++){
			$clienadress = trim(strip_tags($stradr[$n]));
			$adclienadress = preg_replace("/([0-9][0-9][0-9][0-9][0-9])/","\n$1",$clienadress);
			if($clienadress!=''){
			$key=ereg_replace("'","''",$clienadress);
				$conn = connecte($base, $host, $login, $passe);	
				$rem = mysql_query("SELECT * FROM `$dblist` WHERE 1 $wherelalaie AND `adresse`LIKE'%$key%'");
				$rom = mysql_fetch_object($rem);
				deconnecte($conn);
				$cliennom= $rom->nom;
				$clienmail= $rom->email;
				$clienportable= $rom->portable;
		
				if($cliennom!=""){ $adclienadress = $cliennom."\n".$adclienadress; }
				$pdf->AddPage();			
			
				if($entete_img!=""){
					$pdf->Image($entete_img,$etx,$ety,$etw,$eth);
				}			
				
				$pdf->SetXY(30,75);
				
				
				if($fontfree){
					$imf="tmp/adr$n.jpg";
					$str = explode("\n",$adclienadress."\n");
					$des = imagecreatetruecolor($adw, $adh);
					
					$bg = imagecolorallocate($des,255,255,255);
					$co = imagecolorallocate($des,0,0,0);
					imagefilledrectangle ( $des, 0, 0, $adw, $adh, $bg);
					
					if(is_file("lalie/fonts/$fo")){
						$font = "lalie/fonts/$fo";
						for($i=0 ; $i<sizeof($str) ; $i++){
							imagettftext($des, 12, 0, 0, ($i+1)*14, $co, $font,$str[$i]);
						}
					}
					else{
						for($i=0 ; $i<sizeof($str) ; $i++){
							imagestring($des,12, 0, $i*14, $str[$i], $co);	
						}
					}
					imagejpeg($des,$imf,100);	
					$pdf->Image($imf,$adx,$ady,$paw,$pah);					
				}
				else{
					$pdf->Cell(100,35,'', $ca,0, "L", 0);
					$pdf->SetXY($adx,$ady);
					$pdf->MultiCell(100,5,$adclienadress, 0, "L", 0); 
				}
			}
		}
	}
	elseif($typemou=="eti"){ ///////////////////////////////////////////////// ETIQUETTES
	
	
			$px_page_largeur = ($_SESSION['page_largeur']);
			$px_page_hauteur = ($_SESSION['page_hauteur']);
			$px_marge_haut = ($_SESSION['marge_haut']);
			$px_marge_bas = ($_SESSION['marge_bas']);
			$px_marge_droite = ($_SESSION['marge_droite']);
			$px_marge_gauche = ($_SESSION['marge_gauche']);
			$px_espace_horizontal = ($_SESSION['espace_horizontal']);
			$px_espace_vertical = ($_SESSION['espace_vertical']);
			$px_nb_colones = $_SESSION['nb_colones'];
			$px_nb_lignes = $_SESSION['nb_lignes'];
			
			
			$px_cellule_hauteur = round(($px_page_hauteur-$px_marge_bas-$px_marge_haut-($px_espace_vertical*$px_nb_lignes))/$px_nb_lignes);
			$px_cellule_largeur = round(($px_page_largeur-$px_marge_gauche-$px_marge_droite-($px_espace_horizontal*$px_nb_colones))/$px_nb_colones);
		function alouettes(){
			global $pdf,$px_page_largeur,$px_page_hauteur,$px_nb_colones,$px_nb_lignes,$px_cellule_hauteur,$px_cellule_largeur,$px_marge_haut,$px_marge_gauche,$px_espace_vertical,$px_espace_horizontal;
			$to = $px_marge_haut;
			$le = $px_marge_gauche;
			for($l=0 ; $l<=$px_nb_lignes ; $l++){
				$pdf->Line(0, $to, 3, $to);
				$pdf->Line($px_page_largeur-3, $to, $px_page_largeur, $to);
				$to+=$px_cellule_hauteur;
				$pdf->Line(0, $to, 3, $to);
				$pdf->Line($px_page_largeur-3, $to, $px_page_largeur, $to);
				$to+=$px_espace_vertical;
			}
			for($l=0 ; $l<=$px_nb_colones ; $l++){
				$pdf->Line($le, 0, $le, 3);
				$pdf->Line($le, $px_page_hauteur-3, $le, $px_page_hauteur);
				$le+=$px_cellule_largeur;
				$pdf->Line($le, 0, $le, 3);
				$pdf->Line($le, $px_page_hauteur-3, $le, $px_page_hauteur);
				$le+=$px_espace_horizontal;
			}
			$pdf->SetXY(1,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell($px_page_largeur-30,2,"Adeli LaLIE Lettre Etiquettes 1.2 : page ".$pdf->PageNo()."/{nb}", 0, "L", 0); 
			$pdf->SetFont('Arial','',12);			
		}
		$pdf = new LETTRE( 'P', 'mm', array($px_page_largeur,$px_page_hauteur) );
		$pdf->Open();
		$pdf->SetAutoPageBreak(0);
		$pdf->AddPage();
		$nbtot = sizeof($stradr);
		$pdf->SetDrawColor(200,200,200);
		$pdf->SetTextColor(0, 0, 0);
		alouettes();
		
		$conn = connecte($dbase, $dhost, $dlogin, $dpasse);
		//mysql_query("INSERT INTO `$lalierp` VALUES ('','$r_id','".ereg_replace("'","''",$lettretitre)."','$mysqlnow','$nbtot','".ereg_replace("'","''",$lettretxt)."','$code','lettre','1')");
		mysql_query("INSERT INTO `$lalierp` 
		(`ref`, `sujet`, `date`, `rapport`, `message`, `code`, `moule`, `active`) VALUES 
		('$r_id','etiquettes',NOW(),'$nbtot','".str_replace("'","''",$lettretxt)."','$code','lettre','1')");
		$regid = mysql_insert_id();
		mysql_close($conn);
		
		echo"<br><br>$nbtot étiquettes<br><br>
		<a href='lalie/$regid.pdf' target='_blank'>télécharger le pdf</a>
		";
		
		$to = $px_marge_haut;
		$le = $px_marge_gauche;
		$li = 0;
		$co = 0;
		
		
			
		for($n=0 ; $n<$nbtot ; $n++){
			$clienadress = trim(strip_tags($stradr[$n]));
			$adclienadress = preg_replace("/([0-9][0-9][0-9][0-9][0-9])/","\n$1",$clienadress);
			$adclienadress = ereg_replace("\n\n","\n",$clienadress);
			if($clienadress!=''){
				$key=ereg_replace("'","''",$clienadress);
				$conn = connecte($base, $host, $login, $passe);	
				$rem = mysql_query("SELECT `nom`,`email`,`portable` FROM `$dblist` WHERE 1 $wherelalaie AND `adresse`LIKE'%$key%'");
				$rom = mysql_fetch_object($rem);
				deconnecte($conn);
				$cliennom= $rom->nom;
				$clienmail= $rom->email;
				$clienportable= $rom->portable;
		
				if($cliennom!=""){ $adclienadress = $cliennom."\n".$adclienadress; }
							
				$pdf->SetXY($le+10,$to+10);
				$pdf->MultiCell($px_cellule_largeur-10,5,$adclienadress, 0, "L", 0); 
				$co++;
				$le+=$px_cellule_largeur+$px_espace_horizontal;				
				if($co==$px_nb_colones){
					$co = 0;
					$le = $px_marge_gauche;
					$li++;
					$to+=$px_cellule_hauteur+$px_espace_vertical;
				}
				
				if($li == $px_nb_lignes){
					$pdf->AddPage();
					alouettes();
					$to = $px_marge_haut;
					$le = $px_marge_gauche;
					$li = 0;
					$co = 0;
				}
			}
		}
	}
	$pdf->AliasNbPages();
	$pdf->Output("lalie/$regid.pdf");	
		
	  //$_SESSION['lettretxt']="";	
	  echo"<script language='javascript'>document.location='./?archives&id=$regid';</script>";	
}
	echo"</td></tr></table>";
?>