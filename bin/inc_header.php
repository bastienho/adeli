<?php // 919 > Personnalisation d'affichage ;
if(isset($_GET["edit"])){		
		$edit=$_GET["edit"];
}
if(!isset($d)) $d='';
if(isset($_GET['d'])){
 	$d = stripslashes($_GET['d']);
}


$abspa = split('/',getenv("SCRIPT_NAME"));
$absolu='';

for($i=0; $i<sizeof($abspa)-2 ; $i++){
	$absolu.=$abspa[$i].'/';
}
$absolu=substr($absolu,0,strlen($absolu)-1);
//echo"<!-- $absolu -->";
$statg = array("en cours","validé");

$_SESSION['opt']=$opt;

insert("_inc");
insert("_agenda_link");
insert("_calendar");

if(is_file('bin/_calendar.php')){
	$opencalendar='./?incpath=bin/_calendar.php&1';
}
else{
	$opencalendar='./?incpath=calendar.php&1';
}

$_SESSION['theme'] = $theme = 'simple';



if(isset($_GET['option'])){
	$option = $_GET['option'];
	$titro = "";
}
if($option==""){
	$option="accueil";
}
//echo"<!-- mconfig/$u_id.theme.conf $theme -->";

$incwhere=" WHERE 1 ";


$defaultvalue = array(
"int"=>"",
"string"=>"",
"blob"=>"",
"u_id"=>$_SESSION['u_id'],
"date"=>date("Y-m-d"),
"time"=>date("H:i:s"),
"datetime"=>date("Y-m-d H:i:s")
);

$NomDuMois=array("err","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
$NomDuJour=array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi","erreur");
$NomDuJoursemaine=array("erreur","lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche");	
$rvb=array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
$alphabeta=array("a","z","e","r","t","y","u","i","o","p","q","s","d","f","g","h","j","k","l","m","w","x","c","v","b","n");
$colorz = array('000000','FFFFFF','FFFF00','FF9900','FF6600','FF0000','FF0066','FF0099','FF00FF','9900FF','6600FF','0066FF','0099FF','00FFFF','00FF00','009900','008844','97ADC1','E4F1FF','cfd3d7','EEEEEE','CCCCCC','999999','666666','333333');


echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">		
		<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />
		<meta name='robots' content='noindex,nofollow' />
		<title>$option Adeli $vers | $prov</title>
		<link rel='icon' href='$style_url/favicon.png' type='image/x-icon'>
		<link rel='shortcut icon' href='$style_url/favicon.png' type='image/x-icon'>
		";
		//<link rel='stylesheet' href='$style_url/$theme/style.css' type='text/css'>

		
		insert("style.css");		
		insert("handheld.css");		
		insert("handheld_ls.css");		
		insert("scriptaculous_prototype.js");		
		insert("scriptaculous.js");		
		insert("builder.js");
		insert("controls.js");
		insert("dragdrop.js");
		insert("effects.js");
		insert("slider.js");
		insert("sound.js");
		insert("unittest.js");		
		insert("func.js");
		
		$tim = time();
		
		if(is_file("bin/style.css")) echo"
		<link media='screen and (min-device-width: 801px)' href='bin/style.css?v=$tim' type='text/css' rel='stylesheet' /> 
		<!--[if lt IE 9]>
		<link rel='stylesheet' type='text/css' href='bin/style.css?v=$tim' media='screen' />
		<![endif]-->
		<link media='handheld' href='bin/handheld.css?v=$tim' type='text/css' rel='stylesheet' />
		<!--[if !IE]>-->
		<link rel='stylesheet' type='text/css' href='bin/handheld.css?v=$tim' media='only screen and (max-device-width: 800px)' />
		<!--<![endif]-->
		<meta name='viewport' content='width=device-width, height=device-height' />
		
		";
		else echo"
		<link media='screen and (min-device-width: 801px)' href='$style_url/bin/style.css?v=$tim' type='text/css' rel='stylesheet' /> 
		<!--[if lt IE 9]>
		<link rel='stylesheet' type='text/css' href='$style_url/bin/style.css?v=$tim' media='screen' />
		<![endif]-->
		<link media='handheld' href='$style_url/bin/handheld.css' type='text/css?v=$tim' rel='stylesheet' />
		<link media='handheld and (orientation:landscape) ' href='$style_url/bin/handheld_ls.css?v=$tim' type='text/css' rel='stylesheet' />
		<!--[if !IE]>-->
		<link rel='stylesheet' type='text/css' href='$style_url/bin/handheld.css?v=$tim' media='only screen and (max-device-width: 800px)' />
		<link rel='stylesheet' type='text/css' href='$style_url/bin/handheld_ls.css?v=$tim' media='only screen and (max-device-width: 800px)  and (orientation:landscape)' />
		<!--<![endif]-->
		<meta name='viewport' content='width=device-width, height=device-height' />";		
		
		if(is_file("bin/scriptaculous_prototype.js")) echo"<script language=\"JavaScript\" src=\"bin/scriptaculous_prototype.js?v=$tim\"></script>";
		else echo"<script language=\"JavaScript\" src=\"$style_url/bin/scriptaculous_prototype.js?v=$tim\"></script>";		
		
		if(is_file("bin/func.js")) echo"<script language=\"JavaScript\" src=\"bin/scriptaculous.js?v=$tim\"></script>";
		else echo"<script language=\"JavaScript\" src=\"$style_url/bin/scriptaculous.js?v=$tim\"></script>";
		
		if(is_file("bin/func.js")) echo"<script language=\"JavaScript\" src=\"bin/func.js?v=$tim\"></script>";
		else echo"<script language=\"JavaScript\" src=\"$style_url/bin/func.js?v=$tim\"></script>";
		
		echo"</head>";
		


$co =get_pref('co.conf');
$bgmod =get_pref('bgmod.conf');
$ima =get_pref('bg.conf');
$co =get_pref('co.conf');

if($co==""){
	$co="FFFFFF";
}


$_SESSION['u_login']=$u_login;
$_SESSION['u_nom']=$u_nom;
$debit = $_SESSION['u_debit'];
$debits = array('Classique','Mobile');
//echo"<!-- $ima $co $bgmod $theme -->";
echo"<body style=\"background:#$co\"";
if($debit==0){
echo" onunload='unaffichload()' onload='totref()' >
<script language=\"JavaScript\">
	document.body.style.overflow='hidden';
	ulogin = '$u_nom';
	gop = '$option';
	option='$option';
	d= '$d';
</script> 
<div id='loadmask' class=\"popupload\">
	 <table style=\"width:100%;height:100%\">
	 <tr><td  align='center' valign='middle'>
	 <img src='$style_url/$theme/loading.gif' alt='chargement en cours' border='none'>
	 </td></tr>
	 </table>
</div>
<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"width:100%;height:100%\">
<tr style=\"height:50px\"><td>
<div id='header'>";
}
else{
	echo" >
	<script language=\"JavaScript\">
	ulogin = '$u_nom';
	gop = '$option';
	option='$option';
	d= '$d';
</script>
<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr ><td>
<div id='header'>";
}
echo"<a href='./?option' id='logo'><img src=\"$style_url/$theme/favicon.png\" alt=\"Adeli $debits[$debit]\" border=\"none\"  height='24'/></a>";
		if($debit==0){
			echo"<span style='font-size:20px' id='tim'></span>";
		}
		
		
		echo"<div id='dash'>";
		
		
$cur_opt="";
if($option != "" ){
	for($i = 0 ; $i<sizeof($opt)-1 ; $i++){	
		if($opt[$i] == $option && $debit==0){
		  $cur_opt=$option;
	   }
		if($opt[$i]!='aide' && $opt[$i]!='reglages' && $opt[$i]!=''){
			if($debit==0){ 
			$class='off';
			if($opt[$i]==$option) $class='on';
				echo"<div class='option$class'><a href='./?option=$opt[$i]' class='info'><b>".strtoupper($opt[$i])."</b><span style='top:10px;left:40px;'><i>".ucfirst($opt[$i])."</i>";
					if(is_file("bin/_$opt[$i].php")){
						$fo = @fopen("bin/_$opt[$i].php","r");
						$ginf = trim(str_replace("//","",str_replace("<?","",str_replace("<?php","",str_replace("<!--","",str_replace("-->","", @fread($fo,100)))))));
						if(ereg(">",$ginf)){
							echo '<br/>'.trim(substr($ginf, strpos($ginf,">")+1, strpos($ginf,";")-strpos($ginf,">")-1 )); 
						}
						fclose ($fo);
					}
				echo"</span></a></div>";
			}
			else{
				//echo"<a href='./?option=$opt[$i]' class='info'><img src='/$theme/img/$opt[$i].png' alt='$opt[$i]' height='20' border='none'></a>";
			}
		}
	}
}	

if($u_email=='' && $option!="reglages" && !isset($_GET["compte"]) ){
	$return.=returnn("Votre compte n'est pas associé à un email<br>Ceci peut causer des problèmes lors des alertes ou pour récupérer vos mots de passe.<br><br>Veuillez le renseigner dans <a href='./?option=reglages&compte'><u>votre compte</u></a>","FF9900",$vers,$theme);
}	
		echo"</div>";
		//END DASH
		
		
		
		
		
		echo"
		<div id='acount' class=\"petittext\">";
		if($debit==0){ 
			if(date('H')>=22){
				$phra ="Bonne nuit";
			}
			elseif(date('H')>=18){
				$phra ="Bonsoir";
			}
			elseif(date('H')>=6){
				$phra ="Bonjour";
			}
			else{
				$phra ="Bonne nuit";
			}
		echo"<a href='./?option=reglages&compte'><span class=\"texteclair\">$phra, $u_nom";
				if(isset($u_gname) && $u_gname !=''){
					echo" ($u_gname)";
				}
				echo"</span></a>&nbsp;&nbsp;
		<a style='cursor:pointer' onclick=\"deconnect()\" title=\"quitter\">Quitter</a>
				";
		}
		else{
			echo"
		<a href='./?option=reglages&compte'><span class=\"textefonce\">$u_nom</span></a>
		<a href=\"./?decon\" class='buttontd'>Quitter</a>";
		}
		echo"</div>";
		
		
		///////////////////////////////////////////////// RECHERCHE
		if(in_array('site',$opt) && isset($menu_site)){
		
			if(isset($_GET['wsearch'])){
				set_pref("wsearch",$_GET['wsearch']);
			}
			$ws = trim(get_pref("wsearch"));			
			
			$site_menupart = array_keys($menu_site);
			$db_names = array();
			$gepa='site';
			$jsoptio='';
			$db_for_lalie='';
			//$db_for_mobile='';
			for($i=0; $i<sizeof($menu_site) ; $i++){
	 			
				$spart = $site_menupart[$i];
				$sepa='site';
				if(substr($spart,0,7)=='worknet') $sepa='worknet';
				if(substr($spart,0,7)=='gestion') $sepa='gestion';
				$db_for_lalie.="<optgroup label='$spart'>";
				//$db_for_mobile.="<optgroup label='$spart'>";
				//$tablo = $menu_site[$spart];
				//$cols = sizeof($tablo);			
				//$tablk = array_keys($tablo);
				foreach($menu_site[$spart] as $tk=>$tv){
					//$tk = $tablk[$m];
					if(!is_numeric($tk)){
						$humanpart = $tk;
					}
					else{
						$humanpart = $tv;
						if($prefixe != ""){
							$humanpart = str_replace($prefixe,"",$humanpart);
						}
						$humanpart = str_replace($spart."_","",$humanpart);
						$humanpart = str_replace("adeli_","",$humanpart);
						$humanpart = str_replace(">$spart","",$humanpart);
						$humanpart = str_replace("-$spart","",$humanpart);
						$humanpart = str_replace(">"," ",$humanpart);	
					}
					$humanpart = ucfirst($humanpart);	
					if(mysql_query("SHOW COLUMNS FROM `$tv`")  ){
						$s='';
						if($ws == $tv){
							$s='selected';
							$gepa=$sepa;
						}
						$jsoptio.="jsoptio['$tv']='$sepa';\n";						
						$db_for_lalie.="<option value='$tv' $s>$humanpart</option>";
					//	$db_for_mobile.="<option value='./?option=$sepa&part=$tablo[$tk]&d' $s>$humanpart</option>";
						$db_names[$tv] = $humanpart;
					}				
				}
				$db_for_lalie.="</optgroup>";
				//$db_for_mobile.="</optgroup>";
				
			}
			
		}
		if(in_array('compta',$opt) && isset($mega_menu['compta'])){
			foreach($mega_menu['compta'] as $ck=>$cv){
				if($ck!='outils'){
					$db_for_lalie.="<optgroup label='compta $ck'>";
					foreach($cv as $tk=>$tv){
						if(is_numeric($tk)){
							$tk = $tv;
						}						
						$s='';
						if($ws == $tv){
							$s='selected';
							$gepa='compta';
						}
						$jsoptio.="jsoptio['$tv']='compta';\n";	
						$db_for_lalie.="<option value='$tv' $s>$tk</option>";
					}
					/*$mega_menu['compta']=array(
					"vente"=>array("Devis"=>"devis","Commandes"=>"commande","Factures"=>"facture","avoirs"=>"avoir"),
					"achat"=>array("Achats / Bon de commande"=>"achat","livraison","Frais personnel"=>"frais"),
					"outils"=>array("statistiques","bilan","réglages"=>"reglages")
					);*/
					$db_for_lalie.="</optgroup>";
				}
			}
		}
		if($db_for_lalie!=''){
			echo"<div id='searchfrom'>
			<form action='./' method='get' name='multisearch'>
			<input type='text' name='d' style='font-size:10px;width:170px'  onKeyUp='keyword()' onChange='keyword()' placeholder='Recherche'><br>
			
			<input type='hidden' name='option' value='site'>			
			<input type='hidden' name='$ws' id='tablsearch'>			
			<input type='hidden' name='q' id='plus'>								
			<select onchange=\"document.getElementById('tablsearch').name=this.value;document.multisearch.option.value=jsoptio[this.value];keyword();\" name='wsearch' style='font-size:10px;width:90px;'>$db_for_lalie</select><input type='submit' value='Recherche' style='font-size:9px;border:none;background:none;padding:0px;'> <a onclick=\"document.getElementById('tablsearch').name=document.multisearch.wsearch.value;document.multisearch.option.value=jsoptio[document.multisearch.wsearch.value];document.getElementById('plus').name='annuaire';document.multisearch.submit();\" class='info'>A</a><a onclick=\"document.getElementById('tablsearch').name=document.multisearch.wsearch.value;document.multisearch.option.value=jsoptio[document.multisearch.wsearch.value];document.getElementById('plus').name='edit';document.multisearch.submit();\" class='info'><img src='$style_url/$theme/+.png' alt='+' border='none' align='absbottom'><span>Nouvel élément</span></a>
			</form>
			 <div style='position:relative'>
				<div id='listedeclient' class='cadre' style='position:absolute;left:-5;top:0;z-index:450;width:160px;display:none'>
					  <span id='resluto'></span>
				  </div> 
			 </div> 
			<script language='javascript'> 
				jsoptio=new Array();
				$jsoptio
				document.multisearch.option.value='$gepa'; 
				
				function Clos(){
				document.getElementById('resluto').innerHTML='';
				}
				function keyword(){
				db = document.multisearch.wsearch.value;
				option = jsoptio[db];
					
				clef = document.multisearch.d.value;
				clef = clef.toLowerCase();
				if(clef != ''){
					document.getElementById('listedeclient').style.display='block';
					var req = null; 
					if (window.XMLHttpRequest){
							req = new XMLHttpRequest();
							if (req.overrideMimeType) {
								//req.overrideMimeType('text/xml');
							}
					} 
					else if (window.ActiveXObject){
							try {
								req = new ActiveXObject('Msxml2.XMLHTTP');
							} 
							catch (e){
								try {
									req = new ActiveXObject('Microsoft.XMLHTTP');
								} 
								catch (e) {}
							}
					}
					
					req.onreadystatechange = function(){ 
							if(req.readyState == 4){
								if(req.status == 200){					
									maskinfo = req.responseText.substr(4,req.responseText.indexOf('-->')-4);
									maskofo = maskinfo.split(',');
									masknb = maskofo[0];									
									maskid = maskofo[1];
									if(masknb == 1){
										document.location='./?option='+option+'&'+db+'&wsearch='+db+'&edit='+maskid;
									}
									else{
										document.getElementById('resluto').innerHTML=req.responseText;
										hote = parseInt(document.getElementById('listedeclient').scrollHeight);
										if(hote>150){
											document.getElementById('listedeclient').style.height='150px';
											document.getElementById('listedeclient').style.overflowY='scroll';
										}
										else{
											document.getElementById('listedeclient').style.height='auto';
											document.getElementById('listedeclient').style.overflowY='auto';
										}
									}
									
								}	
								else	{
									document.getElementById('resluto').innerHTML='erreur';
								}	
							} 
						}; 
						req.open('GET', 'bin/inc_ajax.php?scan=rech&db='+db+'&option='+option+'&str='+clef, true); 
						req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
						req.send(null);	
				
				
					
				}
				else{
					document.getElementById('resluto').innerHTML='';
					document.getElementById('listedeclient').style.display='none';
				}
				}
			</script>
			</div>";
		}
		
	echo"</div>";
//<!-- end HEADER -->	

	echo"";	
		
	

	$modul_part="";
	$menuonglets="<ul class='menuoptiglets'>";
	$db_for_mobile='';
					
	   foreach($mega_menu as $opti=>$menu){
		   $class='off';
		   if($opti==$option){
				$class='on';   
		   }
		   $menuonglets.="<li class='optiontotem$class'><h3>$opti</h3><ul class='menuonglets'>";
		   foreach($menu as $spart=>$tablo){
			
				$affspart = ucfirst($spart);
				if(isget($tablo) || (isset($_GET['part']) && in_array($_GET['part'],$tablo)) ){
					$menuonglets.="<li class='menuselected'>";
				}
				else{
					$menuonglets.="<li class='buttontd'>";
				}	
				$tab_val = array_values($tablo);
				$menuonglets.="<div class='menuu'><a href=\"./?option=$opti&part=".$tab_val[0]."&d=\"><h4>$affspart</h4></a><ul>";
				$db_for_mobile.="<optgroup label='$affspart'>";
			   foreach($tablo as $humanpart=>$tk){
					if(is_numeric($humanpart)){
						$humanpart = $tk;
					}				
					if($prefixe != ""){
						$humanpart = str_replace($prefixe,"",$humanpart);
					}
					$humanpart = str_replace($spart."_","",$humanpart);
					$humanpart = str_replace("adeli_","",$humanpart);
					$humanpart = str_replace(">$spart","",$humanpart);
					$humanpart = str_replace("-$spart","",$humanpart);
					$humanpart = str_replace(">"," ",$humanpart);	
					$humanpart = ucfirst($humanpart);
				
					$menuonglets.="<li>";
				
					$comportement=array();
					if(isset($types[$tk])){
						$comportement = explode(",",$types[$tk]);
					}
					if(isset($_GET[$tk]) ||  (isset($_GET['part']) && $_GET['part']==$tk)){
						$part = $tk;
						$modul_part = $spart;
						$parto = $humanpart;
						$menuonglets.="<a href=\"./?option=$opti&part=$tk&d=\"><b>$humanpart</b></a>";
						$db_for_mobile.="<option value='./?option=$opti&part=$tk&d' selected>$humanpart</option>";
						
						$compote = $comportement;
					}
					else{
						$menuonglets.="<a href=\"./?option=$opti&part=$tk&d=\" class='menuuu'>$humanpart</a>";
						$db_for_mobile.="<option value='./?option=$opti&part=$tk&d'>$humanpart</option>";
						
					}
					
					
					if( (($option=="site" || $option=="worknet"  || $option=="gestion" ) && mysql_query("SHOW COLUMNS FROM `$tk`") && !in_array("nonew",$comportement)) || ($option=="compta" && $affspart=='Vente') ){
						$menuonglets.=" <a href=\"./?option=$opti&part=$tk&d=&edit\" class='info'><img src='$style_url/$theme/+.png' align='absmiddle' alt='+' border='none'><span>Ajouter un élément pour : $humanpart</span></a> ";							
						
					}
					$menuonglets.="</li>";
					
					
			   }
			   $menuonglets.="</ul></div></li>";			   
			   $db_for_mobile.="</optgroup>";		
			
		   }
		   $menuonglets.="</ul></li>";
	   }  

$menuonglets.="</ul>";
		
		
		echo"</div>";
///end dash
$order = get_pref("ordre.$part");


if(isset($_GET['order']) && $_GET['order']!=""){
	$order = stripslashes($_GET['order']);
	set_pref("ordre.$part",$order);
}

if($order==''){
	$order ="`id`DESC";
}
$_SESSION['order']=$order;
$titro = $parto;
if(ereg("=",$parto)){
	$titro = substr($parto,0,strpos($parto,"=",0));
}
$titro = ereg_replace("_","&nbsp;",$titro);
if($prefixe!=""){
	$titro = ereg_replace($prefixe,"",$titro);
}



echo"
	<tr><td align='center' valign='top' style='background:none'>
	
<script language=\"JavaScript\">
	ulogin = '$u_nom';
	uid = '$u_id';
	part = '$part';	
</script>
	<table width='100%' cellspacing='0' cellpadding='0'>		
		<tr><td  valign='top' align=\"left\">	
		<table cellspacing='0' cellpadding='3' border='0' width='100%' style='height:90%'><tr>
		
";

if($debit==0){
	echo"<td valign='top' align='center' width='10'>";

$panw = get_pref("panw");
if($panw=='') $panw=1;
$panwd=array(1,190);
$panwo=array(1,190);


$aidew=abs(get_pref("aidew"));
if($aidew==0){
	$aidew=600;
}



///////////////////////////////////////////////// TOTEM
echo"	
		
	
		
		<table  cellspacing='0' cellpadding='0'><tr>
		<td valign='top'><div id='panelp'  style='display:block;width:$panw"."px;padding:0px;'>
		
			
			<script language='javascript' type='text/javascript'>
			var aidew=$aidew;	
			var pwn = $panw;
			var panwi = $panw;	
			function openaide(){
				aide = open('http://urbancube.fr/adeli?v=1.2', 'aide', 'width='+aidew+',height='+screen.height+',top=0,left=0,resizable=1');
				aide.onresize= function(){
					widthaide(wi);
				}
				
			}
			heur(); 
			
			</script><br>";

	
if(in_array("compta",$optico) ){  ///////// COMPTA
	  $jsoptio.="
	  jsoptio['devis']='compta';
	  jsoptio['facture']='compta';
	  jsoptio['commande']='compta';
	  jsoptio['achat']='compta';
	  ";						
	  $db_for_lalie.="<optgroup label='Compta'>
	  	<option value='devis'>Devis</option>
	  	<option value='facture'>Facture</option>
	  	<option value='commande'>Commandes</option>
	  	<option value='achat'>Achat</option>
	  </optgroup>";		
}	

if(in_array("site",$optico) && in_array("gestion",array_keys($menu_site)) ){ ////////// GESTION
	$langue_db = "gestion_langue";
	$rayons_db = "gestion_rayons";
	$articles_db = "gestion_articles";
	$rappel_db = "gestion_rappel";
	
	if(isset($gestion_db["langue"])) $langue_db=$gestion_db["langue"];
	if(isset($gestion_db["rayons"])) $rayons_db=$gestion_db["rayons"];
	if(isset($gestion_db["articles"])) $articles_db=$gestion_db["articles"];
	if(isset($gestion_db["rappel"])) $rappel_db=$gestion_db["rappel"];
}


$ajaxf = "";
$totem_mes = "";
insert("inc_ajax");

if(in_array("agenda",$optico) ){  ///////// AGENDA

	
if(isset($agenda_base)){
$sqlnow_date = date("Y-m-d");
$sqlnow_time = date("H:i:s");
	$totem_mes .= "<div class='buttontd'><span class='textegrasfonce'>Agenda</span>
			<a href='./?option=agenda&jour' class='info'>j<span>Voir : jour</span></a>
			<a href='./?option=agenda&semaine' class='info'>s<span>Voir : semaine</span></a>
			<a href='./?option=agenda&mois' class='info'>m<span>Voir : mois</span></a>
			<a href='./?option=agenda&annee' class='info'>a<span>Voir : année</span></a>
			<a  style=\"cursor:pointer\" onClick=\"contextage('".$_SESSION["date"]."','10:00:00',event,'Ajouter une date','add&mois','99CCCC',0,'agenda_totem','$sqlnow_date&h=none&print=1')\" class='info'><img src='$style_url/$theme/+.png' alt='+' border='none'><span>Nouvelle date</span></a>
			</div>
			<div class='cadrebas'><div style='width:160' id='agenda_totem'>";
		if(is_file("bin/inc_ajax.php")){
			$ajaxf.="
envoyer('bin/inc_ajax.php?scan=agenda','scan','&dest=agenda_totem&print=2&before=1&onlyon=1','agenda_totem');\n";
		}	
		else{
			$res = mysql_query("SELECT * FROM `$agenda_base` WHERE `date`<='$sqlnow_date' AND `etat`='0'  ORDER BY `date`,`heure`");
			$totalage = mysql_num_rows($res);
			if($totalage > 0){
				
				while($rowage=mysql_fetch_object($res)){
					$date = $rowage->date;
					$heure = $rowage->heure;
					$hdat = $heure;
					$mdat = date("Ymd",strtotime($date));
					$client = $rowage->client;
					$qui = $rowage->qui;
					$note = $rowage->note;
					$type = $rowage->type;
					$etat = $rowage->etat;	
					$only = $rowage->only;				
					$mid = $rowage->id;	
					$priority = $rowage->priority;				
					$couleur = $rowage->couleur;
					$printki=$client;
					if($priority==0){
						$priority=1;
					}
					 if(is_numeric($client) && mysql_query("SHOW COLUMNS FROM `clients`") ){
						$ris = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$client'");
						if($ris && mysql_num_rows($ris)==1){
							$ri = mysql_fetch_object($ris);
							$printki=$ri->nom;
						}
					 }
					 
					 $agebody='';
					$bodi=explode("\n",strip_tags(trim($note)));
					for($e=0 ; $e<sizeof($bodi) ; $e++){
						$agebody.=trim(trim($bodi[$e]))." ";
					}
					$note = urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode(str_replace('"',"`",$agebody)))));
					
					$totem_mes.="- <a href='#' onclick=\"contextage('$date','$heure',event,'Modifier','update=$mid&jour','$couleur',$mid,'agenda_totem','$sqlnow_date&h=none&print=1');
					document.agendaform.qui.value='$qui';
					document.agendaform.type.value='$type';
					document.agendaform.client.value='$client';				
					document.agendaform.clients.value='$client';			
					document.agendaform.only.value='$only';				
					document.agendaform.etat.value='$etat';
					document.agendaform.priority.value='$priority';
					document.agendaform.note.value='".str_replace("'","\\'",$note)."';\" class='info'>";
					if($rowage->date<$sqlnow_date || ($rowage->date==$sqlnow_date && $rowage->heure <= $sqlnow_time)){
						$totem_mes .= "<b>$printki</b>&nbsp;<span>
						$hdat<br>$qui, $note</span></a>";
					}
					else{
						$totem_mes .= "<font class='petit'>$printki</font></b>&nbsp;<span>
						$hdat<br>$qui, $note</span></a>";
					}
					$totem_mes .= " ".str_repeat('*',$priority)."<br>";
				}
				
			}
			else{
				$totem_mes .= "aucun événement aujourd'hui>";
			}
		}
		$totem_mes .= "</div></div>";
}
	
}
if(in_array("mail",$optico)){  ///////// MAIL
	
	if(is_file("bin/inc_ajax.php")){
		$totem_mes .= "
		<div class='buttontd'><span class='textegrasfonce'>Mails</span></div>
		<div class='cadrebas' id='ajax_mail'></div>
		";
		$ajaxf.="scanlogges('bin/inc_ajax.php?scan=mail','ajax_mail',780000,true)";
	}
	
}
if(in_array("site",$optico) && isset($gestion_rappel)){  ///////// RAPPEL
	
	
	$keep_rappel_alive = abs(get_pref("rappel.conf"));
	if(isset($_GET["is_rappel_alive"])){ 
		$is_rappel_alive = $_GET["is_rappel_alive"];
		set_pref("rappel.alive.conf",$is_rappel_alive,"x");	
	}
	else{ 
		$is_rappel_alive = get_pref("rappel.alive.conf",'x');	
	}
	
	$ch_kra = abs($is_rappel_alive-1);
$totem_mes .= "<div class='buttontd'><span class='textegrasfonce'>Rappels</span>
			<a href='./?option=gestion&gestion_rappel&is_rappel_alive=$ch_kra' class='info'>
			<img src='$style_url/$theme/v$is_rappel_alive.gif' border='none' alt='actif'><span>changer de status</span></a>
			</div>
			<div class='cadrebas'  id='rappelmoi'>";
	$ros = mysql_query("SELECT * FROM `gestion_rappel` WHERE `active`='0' ORDER BY `rappel`ASC");
	if(mysql_num_rows($ros)>0){
		while($rew=mysql_fetch_object($ros)){
			$commentaires  = $rew->commentaires ;
			$telephone = $rew->telephone;
			$dat = date("d/m/y H:i",strtotime($rew->rappel));
			$mid = $rew->id;
			$totem_mes .= "
				- <a href='./?option=gestion&gestion_rappel&edit=$mid' class='info'><b>$dat</b><span>
				$telephone<br>
				$commentaires
				</span></a><br>
				";
		}
	}
	else{
		$totem_mes .= "aucun rappel en attente";
	}
	$totem_mes .= "</div>";
	$ajaxf.="scanlogges('bin/inc_ajax.php?scan=rappel','rappelmoi',30000,true)";

}
/*if(in_array("site",$optico) && isset($menu_site["worknet"]) && in_array("adeli_messages",$menu_site["worknet"])){  ///////// MESSAGERIE WORKNET

	$totem_mes .= "<div class='buttontd'>
	<a href='./?option=worknet&adeli_messages'><span class='textegrasfonce'>Messages client</span></a></div>
	<div class='cadrebas'>";

	$ros = mysql_query("SELECT * FROM `adeli_messages` WHERE `dest`='0' AND `etat`='0' ORDER BY `date`DESC");
	if($ros && mysql_num_rows($ros)>0){
		while($rew=mysql_fetch_object($ros)){
			$prov = $rew->prov;
			$sujet = $rew->sujet;
			$dat = date("d/m/y H:i",strtotime($rew->date));
			$mid = $rew->id;
			$ros = mysql_query("SELECT `nom` FROM `clients` WHERE `id`='$prov'");
			$rows = mysql_fetch_object($ros);
			$prov=$rows->nom; 
			$totem_mes .= "
				<p class='wrong'>
				<a href='./?option=worknet&adeli_messages&edit=$mid' class='info'><b>$prov</b><span>
				$hdat<br>
				$sujet
				</span></a>
				</p>";
		}
	}
	else{
		$totem_mes .= "aucun message non lu";
	}
	$totem_mes .= "</div>";

	
}*/
if(in_array("groupe",$optico)){  ///////// GROUPE
	$totem_mes .= "
	<div class='buttontd'><span class='textegrasfonce'>10 derniers connectés</span></div>
			<div class='cadrebas'><ul>";
	$rescontacts = mysql_query("SELECT * FROM `adeli_users` ORDER BY 'last'DESC LIMIT 0,10");
	while($row = mysql_fetch_object($rescontacts)){
		$c_id = $row->id;
		$c_login = $row->login;	
		$c_nom = $row->nom;	
		if($c_nom==''){ 
			$c_nom=ucfirst($c_login);
		}
		$allfriends[$c_id] = $c_nom;
		$m_last = $row->last;
		$mlh = substr($m_last,11,5);
		$mlj = substr($m_last,0,10);
		if($mlj == date("Y-m-d")){
			$mlj = "aujourd'hui";
		}
		elseif($mlj == date("Y-m-d",strtotime("-1 day",mktime(0,0,0,date("m"),date("d"),date("Y"))))){
			$mlj = "hier";
		}
		else{
			$mlj = substr($m_last,8,2)."/".substr($m_last,5,2)."/".substr($m_last,0,4);
		}
		$totem_mes .="<li><a href='./?option=groupe&utilisateurs&id=$c_id' class='info'><b>$c_nom</b> <span class='petittext'>$mlj $mlh</span></a></li>";
	}
	
		
	$totem_mes .="</ul></div>";
}	

		if($debit==0){
			//
			echo $menuonglets;
			//
		}
		elseif($db_for_mobile!=''){
			echo"<select name='menuonglets' onchange='document.location=this.value;'><option value=''>".ucfirst($option)."</option>$db_for_mobile</select>";	
		}
		echo $totem_mes;
		
if(mysql_query("SHOW COLUMNS FROM `adeli_rss`")  ){
	$res = mysql_query("SELECT * FROM `adeli_rss` WHERE (public='$u_id' OR public=0) AND active=1 AND `type`=1 AND emplacement=0");
	if($res && mysql_num_rows($res)>0){
		while($ro = mysql_fetch_object($res)){
			$type=$ro->type;
			$url=$ro->url;
			$limite=$ro->limite;
			$nom=ucfirst($ro->nom);
			$rss=$ro->id;
			echo"
			<div class='buttontd' align='left'><span class='textegrasfonce'>$nom</span>
			<a href='./?option=reglages&adeli_rss&edit=$rss' class='info'><img src='$style_url/$theme/modif.gif' height='16' alt='modifier' border='none'><span>Modifier les paramètres</span></a>
			";
			$urle = split(';',$url);
				echo"&nbsp;<a href='./?option=site&$urle[0]&edit' class='info'><img src='$style_url/$theme/+.png' alt='+' border='none'><span>Nouveau : $nom</span></a>";
				echo"</div>
			<div class='cadrebas'>
			<div style='position:relative;width:165px;overflow-x:hidden'>";
			parse_int($urle,$limite);
			echo"</div></div>";
		}
	}
}
echo"
		</div>	
		</td>
		<td onClick='widthpanel();' id='cntrl_panel' class='colofon'>.<br>.<br>.<br>.</td>
		
		</tr></table>
		
		<script language='javascript' type='text/javascript'>
			var urlserveur = '$urlserveur';
			var part = \"$part\";
			var edit = \"$edit\";
			var panw1=\"$panwo[1]\";
			var panw0=\"$panwo[0]\";
			var wpanw1=\"$panwd[1]\";
			var wpanw0=\"$panwd[0]\";
			var pwn = $panw;
			var panwi = $panw;	
		</script>
		
		

</td><td align='left' valign='top'";

	if($ima=="mine" && file_exists("mconfig/$u_id.bg.jpg")){
		echo" style=\"background:#$co url(mconfig/$u_id.bg.jpg) fixed $bgmod;\"";
	}
	elseif($ima=="none"){
		echo" style=\"background:#$co\"";
	}
	else{
		echo" class='fondimage'";
	}
echo">
<div id='menu_date' style=\"position:absolute;left:0px;top:0px;width:175px;height:275px;visibility:hidden;padding:10px;z-index:210\">
	 <div class=\"bando\">
	  <iframe name='cal' src='about:blank' width='170' height='270' scrolling='no' frameborder='0'></iframe>
	</div>
</div>

<div id='menu_context' class=\"popupload\" style=\"position:absolute;left:0px;top:0px;visibility:hidden;z-index:510\">
	<table style=\"width:100%;height:100%\">
	 <tr><td  align='center' valign='middle'>
	 
	 <table class=\"buttontd\">
	 <tr><td  align='right' valign='middle'><a style='cursor:pointer' onclick=\"document.getElementById('menu_context').style.visibility='hidden';document.body.style.overflow='scroll';\" title=\"annuler\">[x]</a></td></tr>
	 <tr><td  align='center' valign='middle' id='menu_context_canvas'>	 
		  	 
	</td></tr>
	</table>

	</td></tr></table>
</div>

<div id='menu_code' class=\"popupload\" style=\"position:absolute;left:0px;top:0px;visibility:hidden;z-index:510\">
	<table style=\"width:100%;height:100%\">
	 <tr><td  align='center' valign='middle'>
	 
	 <table class=\"buttontd\">
	 <tr><td  align='right' valign='middle'><a style='cursor:pointer' onclick=\"document.getElementById('menu_context').style.visibility='hidden';document.body.style.overflow='scroll';\" title=\"annuler\">[x]</a></td></tr>
	 <tr><td  align='center' valign='middle' id='menu_context_canvas'>	 
		  	 
	</td></tr>
	</table>

	</td></tr></table>
</div>
	  
<div id='menu_color' style=\"position:absolute;left:0px;top:0px;width:440px;height:220px;visibility:hidden;padding:0px;z-index:210\">	  
";
if(!is_file("bin/inc_ajax.php")){
	colorpicker('global','FFFFFF',"effeccolor('COLOR')",0,"palette de couleur",10,false);
}
else{
	echo"<table class=\"buttontd\" style=\"position:absolute;top:0px;left:0px;\"><tr><td valign=\"top\">
								<div id=\"divoglobal\" style=\"background-color:#FFFFFF;padding:3px;position:relative;height:70px;width:50px;border-style:solid;border-color:#000000;border-width:1px\"></div><br>
								<input type='text' id=\"pickercode\" size='6' maxlength='6' value='$field_value' onkeyup=\"document.getElementById('divoglobal').style.backgroundColor=this.value;\"/><a  onclick=\"effeccolor(document.getElementById('pickercode').value)\">ok</a><br>
								
								</td><td valign=\"top\" align=\"right\">
								<a style=\"cursor:pointer\" onclick=\"document.getElementById('menu_color').style.visibility='hidden'\" class=\"buttontd\" >x</a>
								<br>
								<div id='color_ajax'>
								
								</div>
								</td></tr></table>";
}
echo"</div>
<div id='bulle' class='cadre' style='position:absolute;top:100px;left:100px;z-index:200;visibility:hidden;padding:4px;text-align:left;font-size:11px;box-shadow: 0px 1px 9px #000;-moz-box-shadow: 0px 1px 9px #000; -webkit-box-shadow:0px 1px 9px #000;	filter:progid:DXImageTransform.Microsoft.Shadow(color='#000000', Direction=180, Strength=3);'></div>  
	  ";
if(in_array('agenda',$opt)){
//this.action+='&d='+this.date.value.replace('-','').replace('-','');this.submit();
	echo"<div id='contda' class='cadre'>
	<table width='220' cellpadding='1' cellspacing='0' border='0' style='width:220px;height:420px'>
	<tr class='buttontd'><td><b><span id='contti'>menu</span></b></td><td align='right' style='width:20px;text-align:right;'><a style='cursor:pointer' onclick=\"closcont()\" title=\"fermer la fenêtre\">[x]</a></td>
	<tr><td colspan='2'>
	<div id=\"divcol\" style=\"background-color:#99CCCC;padding:3px;\">
	<form action='#' method='post' name='agendaform' onsubmit=\"return false;validagenda(tit,id,maj,src);return false;\">
	<table cellspacing='0' cellpadding='0'  border='0'>
	<tr><td colspan='2'><select name='usr'>";
	$rescontacts = mysql_query("SELECT * FROM `adeli_users` ORDER BY `nom`");
	while($row = mysql_fetch_object($rescontacts)){
		$c_id = $row->id;
		$c_nom = $row->nom;	
		$s='';
		if($c_id==$u_id) $s='selected';
		echo"<option value='$c_id'>$c_nom</option>";
	}
	echo"</select>
		</td></tr>
		<tr><td>
		Date :</td><td><img src='$style_url/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"contextdate(event);cal.location='$opencalendar&x_id=$x_id&amp;cible=agendaform.date&amp;date='+document.agendaform.date.value+'&amp;type=date'\"><input type='text' name=\"date\" value=\"$lannee-$lemois-$lejour\" maxlength=\"10\" style=\"width:80px;background:none;\">
		</td></tr>
		<tr><td>
		Heure : </td><td><img src='$style_url/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"contextdate(event);cal.location='$opencalendar&x_id=$x_id&amp;cible=agendaform.heure&amp;date='+document.agendaform.heure.value+'&amp;type=time'\"><input type='text' name=\"heure\" value=\"00:00:00\" maxlength=\"10\" style=\"width:60px;background:none;\">
		</td></tr>
		<tr><td>
		Avec :</td><td><input type='text' name=\"client\" value=\"\" style=\"width:150px;background:none;\">
		</td></tr>
		<tr><td valign='top'>
		Type : <br><br></td><td valign='top'><div style='position:relative'>
		<a id='typo_link' onclick=\"document.getElementById('typo_sel').style.visibility='visible';document.getElementById('typo_link').innerHTML='';\"></a>
		<input id='typo_txt' type='text' name='type' value='' style=\"position:absolute;visibility:hidden;top:0px;left:0px\">
									<select id='typo_sel' name='typo' onchange=\"javascript:if(this.value=='.xcrea.'){this.style.visibility='hidden';document.getElementById('typo_txt').style.visibility='visible';}else{document.agendaform.type.value=this.value;}\" style=\"position:absolute;visibility:hidden;top:0px;left:0px\">
									<option value=''>Sélectionnez</option>
									";
									
									$ret = mysql_query("SELECT DISTINCT `type` FROM `$agenda_base` WHERE `type`!='' ORDER BY`type`");
									while($rot=mysql_fetch_object($ret)){
										echo"<option>".($rot->type)."</option>";
									}
									
									echo"
									<option value='.xcrea.'>nouveau</option>
									</select>
				</div>
		</td></tr>
		<tr><td colspan='2'>
		<textarea name=\"note\" style=\"width:190px;height:120px;font-size:12px;background:none;\"></textarea>
		</td></tr>		
		<tr><td>
		Lieu :</td><td> <input type='text' name=\"qui\" value=\"Moi\" style=\"width:150px;background:none;\">
		</td></tr>
		<tr><td>
		&Eacute;tat : </td><td><select name=\"etat\" style=\"background:none;\">
									<option value=\"0\">$statg[0]</option>
									<option value=\"1\">$statg[1]</option>
									</select>
		</td></tr>
		<tr><td>
		Priorité : </td><td><select name=\"priority\" style=\"background:none;\">
									<option>1</option>
									<option>2</option>
									<option>3</option>
									</select>
		</td></tr>
		<tr><td>
		Visible : </td><td><select name=\"only\" style=\"background:none;\">
									<option value='0'>Par tous les utilisateurs</option>
									<option value='$u_id'>Seulement moi</option>
									</select>
		</td></tr>	
		<tr><td>Lien :</td><td><input type='hidden' name='lien' value=''>
		<select name='link_db' onchange='document.agendaform.lien.value=\"@\"+this.value; age_link_db()'><option value=''></option>$db_for_lalie</select>
		<div id='link_id'><input type='hidden' name='link_id' /></div>
		<span id='link_link'></span> <a onclick='age_link_af()'>Modifier</a>
		</td></tr>
		<tr><td colspan='2' align='center'><div style='position:relative'>
		<input type=\"hidden\" name=\"couleur\" value=\"99CCCC\" style='position:absolute;'>
							</div><br>
							<table><tr><td>";
							
							$ret = mysql_query("SELECT DISTINCT `couleur` FROM `$agenda_base` WHERE `couleur`!='' ORDER BY`couleur`");
							if($ret && mysql_num_rows($ret)>0){
								while($rot=mysql_fetch_object($ret)){
									echo"<div style='background-color:#".($rot->couleur).";float:left;border:#000 1px solid;' onclick=\"document.agendaform.couleur.value='".($rot->couleur)."';document.getElementById('divcol').style.backgroundColor='".($rot->couleur)."'\">&nbsp;&nbsp;</div>";
								}
							}
							
							//colorpicker("couleur","99CCCC","document.agendaform.couleur.value='COLOR';document.getElementById('divcol').style.backgroundColor='COLOR'",-30,"<font size='1'>autre</font>",5)
							echo "<div style='float:left;border:#000 1px solid;'  style='cursor:pointer' onclick=\"choosecolor('','','','age',event)\">&nbsp;+&nbsp;</div></td></tr></table>
							<div style='position:relative'></div>
							
		</td></tr>
		<tr><td colspan='2'  align='right'>
		<span id='isdel'><input type='button' onclick='validagenda(\"Ajouter une date\",0)' class='buttontd' value='ok'><span>
		</td></tr>
		</table>
	
		</form></div>
	</td></tr></table>
	</div>";
}
	echo"";
}
else{
	echo"<td align='left' valign='top'>";
}

echo"<div id='adeli_body'>";
$fpart=$part;
$_SESSION['agenda_base'] = $agenda_base;
if($option=='lalie'){
	$fpart=$laliedb;
}
if($option=='agenda'){
	$fpart=$agenda_base;
}
if($option=='compta'){
	$fpart=$compta_base;
}
if($option=='mail'){
	$fpart=$mail_base;
}
if($option=='ftp'){
	$fpart=$ftp_base;
}

if(!isset($fichiers[$part])){
	if(isset($compote) && in_array("ico",$compote)){
			$fichiers[$part]['Icone']=array("../$part/","$edit.ico");
	}
	if(isset($compote) && in_array("dir",$compote)){
			$fichiers[$part]['Dossier de fichiers']=array("../$part/$edit/",".dir");
	}
}

if(isset($_GET['add'])){
		
	if(insertintodb($base,$fpart)){
		$edit = mysql_insert_id($conn);
		$ed="&edit=$edit";
		if(isset($_GET['new'])){
			$ed='&edit';
		}		
		elseif(!isset($_GET['edit'])){
			$ed='';
		}
		$return.=returnn("Ajout effectué avec succès dans $part<br>identifiant généré : <b>$edit</b>
		<script language='javascript'>document.location='./?option=$option&part=$part$ed' ;</script>","009900",$vers,$theme);
		//
		//@ob_end_clean();
		//header("location: ./?option=$option&part=$part$ed");
		if(is_numeric($_POST['clon'])){
			if(isset($fichiers[$part])){		//////////////////////////////////////////// 			CUSTOM FILES 	
				foreach ($fichiers[$part] as $custom_name){
					$custom_dir = substr($custom_name[0],strpos($custom_name[0],"/"),strlen($custom_name[0]));
					$custom_file = $custom_name[1];
					if(is_dir("../$custom_dir") && ( strpos($custom_dir,$_GET['edit']) || strpos($custom_file,$_GET['edit']))){
						
						$ext = strtolower(substr(strrchr($custom_file,"."),1));
						$custom_file = str_replace(".$ext","",$custom_file);
						if($ext=="ico"){
							for($ic=0; $ic<sizeof($imacool) ; $ic++){
								if(is_file("../".$custom_dir.$custom_file.".".$imacool[$ic])){
										
										if(copy("../".$custom_dir.$custom_file.".".$imacool[$ic],str_replace($_GET['edit'],$edit,"../".$custom_dir.$custom_file.".".$imacool[$ic]))){
											echo "../".$custom_dir.$custom_file.".".$imacool[$ic];
										}
										break;
								}
							}
						}
						elseif($ext=="dir"){
								$newdir=str_replace($_GET['edit'],$edit,"../$custom_dir$custom_file");
							   if(!is_dir($newdir)){
									if(!mkdir($newdir,0777)) echo"création de dossier echouée<br>";
								}
								if(is_dir("../$custom_dir$custom_file") && is_dir($newdir)){
									$dir = dir("../$custom_dir$custom_file");
									$nbfil=0;
									$totpds=0;
									$nbent=0;
									while($entry = $dir->read()){
										if($entry != "." && $entry != ".."){
											if(is_file("../$custom_dir$custom_file/$entry")){
												if(copy("../$custom_dir$custom_file/$entry","$newdir/$entry")){
													echo "$newdir/$entry";
												}
											}
										}
									}
								}								
						}
						else{
							if( is_file("../$custom_dir$custom_file.$ext") ){
								if(copy("../$custom_dir$custom_file.$ext",str_replace($_GET['edit'],$edit,"../$custom_dir$custom_file.$ext"))){
									echo "../$custom_dir$custom_file.$ext";
								}
							}
						}
					}
				}
			}
		}
	}
	else{
		$return.=returnn("L'ajout dans $part a échouée","990000",$vers,$theme);
	}
	
}
if(isset($_GET['addfile'])){
	$return.=returnn("Chargement de fichier dans $edit@$part ...","999999",$vers,$theme);
	if($_FILES['file']['name'][0] != NULL){
		if(addfile($_GET['addfile'], $_FILES['file']['name'][0], $_FILES['file']['tmp_name'][0], $dangerous)){
			$return.=returnn($file_name." chargé avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn($file_name." n'a pu être chargé correctement","990000",$vers,$theme);
		}
	}
}
if(isset($_GET['update']) || isset($_GET['refresh'])){
	
	$updt = $_GET['edit'];
	if($updt==''){
		$updt = $_GET['update'];
	}
	if(isset($_GET['update']) ){
		if(updatedb($base,$fpart,$updt)){
			$return.=returnn("Modification de $updt@$part effectuée avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La modification de $updt@$part a échouée","990000",$vers,$theme);
		}
	}
	/**/
		if(isset($fichiers[$part]) && !ereg('gestion_',$part)){	
					$custom_files = $fichiers[$part];
					$custom_keys = array_keys($custom_files);
					$i=0;
					while($i<sizeof($custom_keys)){
						$custom_name = $custom_keys[$i];
						$custom_dir = $custom_files[$custom_name][0];
						$custom_file = $custom_files[$custom_name][1];
						if($_FILES['file']['name'][$i] !=''){
							if(addfile($custom_dir."/".$custom_file, $_FILES['file']['name'][$i], $_FILES['file']['tmp_name'][$i], $dangerous)){
								$return.=returnn($custom_name." chargé avec succès","009900",$vers,$theme);
							}
							else{
								$return.=returnn($custom_name." n'a pu être chargé correctement","990000",$vers,$theme);
							}
						}
						$i++;
					}
			}
	
	
}
if(isset($_GET['del'])){
	
	$dbp = $fpart;
	if(isset($_GET['effdb'])){
		$dbp = $_GET['effdb'];
	}
	if(deletefromdb($base,$dbp,$_GET['del'])){
		$return.=returnn("Suppression effectuée avec succès","009900",$vers,$theme);
	}
	else{
		$return.=returnn("La suppression a échouée","990000",$vers,$theme);
	}
	
}

if(isset($_GET['delfile'])){
	$fnom = substr(strrchr($_GET['delfile'],"/"),1);
	if(delfile($_GET['delfile'])){
		$return.=returnn("Fichier <b>$fnom</b> supprimé avec succès","009900",$vers,$theme);
	}
	else{
		$return.=returnn("Le fichier <b>$fnom</b> n'a pu être supprimé correctement","990000",$vers,$theme);
	}	
}

if(isset($_GET['multi']) && ($cur_opt=="site" || $cur_opt=="worknet" || $cur_opt=="gestion")){
	$wereid="id=0 ";
	foreach($_POST as $keyname=>$value) {
		if(substr($keyname,0,3)=='sel' && is_numeric(substr($keyname,3,strlen($keyname)))){
			$tid=substr($keyname,3,strlen($keyname));
			$wereid.=" OR `id`='$tid'";
		}
	}
	
	switch($_GET['multi']){
		case 'active': 
			if(mysql_query("UPDATE `$part` SET `active`=1 WHERE $wereid")){
				$return.=returnn("Multi-activation dans $part effectuée avec succès","009900",$vers,$theme);
			} 
			else{
				$return.=returnn("La multi-activation dans $part a échouée","990000",$vers,$theme);
			}
		break;
		case 'desactive': 
			if(mysql_query("UPDATE `$part` SET `active`=0 WHERE $wereid")){
				$return.=returnn("Multi-désactivation dans $part effectuée avec succès","009900",$vers,$theme);
			} 
			else{
				$return.=returnn("La multi-désactivation dans $part a échouée","990000",$vers,$theme);
			}
		break;
		case 'delete': 
			foreach($_POST as $keyname=>$value) {
				if(substr($keyname,0,3)=='sel' && is_numeric(substr($keyname,3,strlen($keyname)))){
					$tid=substr($keyname,3,strlen($keyname));
					if(deletefromdb($base,$part,$tid)){
						$return.=returnn("Multi-suppression dans $part effectuée avec succès","009900",$vers,$theme);
					}
					else{
						$return.=returnn("Multi-suppression dans $part a échouée","990000",$vers,$theme);
					}
				}
			}
		break;		
		
	}
}
?>