<?php // 241 > Moteur Adeli ;
if(isset($_SESSION['x_id']) && isset($_SESSION['u_id'])){
$menu=array(" "=>array());
$prefix='adeli';
$edit='';
if(isset($_GET["edit"])){		
		$edit=$_GET["edit"];
}
if(!is_file("mconfig/adeli.php") ){
	header("location: ./");
}
else{
	include("mconfig/adeli.php");
}
$options=explode(',',$opt);
if(!function_exists('date_default_timezone_set')){
	function date_default_timezone_set($str){
		return true;	
	}
}

$noupdate = array("aide");
date_default_timezone_set('Europe/Paris') ;
$return ='';
$ajaxf='';
$life_log=0;
//$option='';
$u_droits='';
$r_alerte='';
$debit=0;
$debugtime = time();
function connecte($base, $host, $login, $passe) {
	$conn = mysql_connect($host, $login, $passe);
	mysql_select_db($base);
	return $conn;
}
function deconnecte($conn) {
	mysql_close($conn);
}

$conn = connecte($base, $host, $login, $passe);
if($conn==false){
	echo"Nous n'arrivons pas à nous connecter à la base de donn&eacute;es";
	exit;	
}

function insert($incfich){		
	global $noupdate, $vers, $query, $u_id, $x_id, $pol_maj, $return, $option;
	if($pol_maj != 3 || isset($_GET["updadeli"])){
		$file=$incfich;
		if(!strpos($file,'.')) $file.='.php';
		$getnom=$incfish = str_replace("inc","",str_replace("_","",$incfich));
		
		$pa = "bin/";
		if($file=="index.php"){
			$pa="";
		}
		if(isset($_SESSION['app_versions'][$file])){
			$getcurrentvers = $_SESSION['app_versions'][$file];
		}
		else{
			$fo  = @fopen("http://www.adeli.wac.fr/vers/$vers/update.php?file=$file","rb");
			$ginf = trim(str_replace("//","",str_replace("/*","",str_replace("*/","",str_replace("<?","",str_replace("<?php","",str_replace("<!--","",str_replace("-->","", @fread($fo,100)))))))));
			if(strpos($ginf,">")>-1){
				$getcurrentvers = abs(substr($ginf, 0, strpos(">",$ginf)-1 ));
				//$getnom = trim(substr($ginf, strpos($ginf,">")-1, strpos($ginf,";")-strpos($ginf,">") )); 
			}
			else{
				$getcurrentvers = abs($ginf);
			}
		}
		if(is_file("$pa$file")){
			//@chmod("$pa$incfich.php",0777);
			$fo = @fopen("$pa$file","r");
			$ginf = trim(str_replace("//","",str_replace("*/","",str_replace("/*","",str_replace("<?","",str_replace("<?php","",str_replace("<!--","",str_replace("-->","", @fread($fo,100)))))))));
			if(strpos($ginf,">")>-1){
				$getvers = abs(substr($ginf, 0, strpos(">",$ginf)-1 ));
			}
			else{
				$getvers = abs($ginf);
			}
		}			
		//echo"\n\t<!-- $getnom / version disponible : $getcurrentvers / votre version : $getvers --> \n";
		if((isset($_GET["updateprogadeli"]) || !is_file("$pa$file") || $getvers<$getcurrentvers || $getcurrentvers==0) && !in_array($option,$noupdate)){
			if($pol_maj == 0 || $pol_maj == 1 || isset($_GET["updadeli"])){
				$fd  = @fopen("http://www.adeli.wac.fr/vers/$vers/update.php?file=$file","rb");
				$fc = @fopen("$pa$file","w+");
				$maj=false;
				if($fd && $fc){
					$maj=true;
					while (!feof ($fd)) {
							$buffer = @fgets($fd, 4096);
							if(!fwrite($fc,$buffer)) $maj=false;
					}
				}
				if($maj == true){
					if($getcurrentvers!=0){
						if($pol_maj == 0 || isset($_GET["updadeli"])){
							$return.="$getnom a bien &eacute;t&eacute; mis à jour...<br>";						
						}
						//echo"<!-- $getnom a bien &eacute;t&eacute; mis à jour...-->";
					}
				}
				else{
					//echo"<!-- $getnom non mis à jour...-->";
				}
				@fclose($fd);
				@fclose($fc);
				$_SESSION["update$incfich"]=true;
			}
			elseif($pol_maj == 2){
				$return.="Une mise à jour de $getnom est disponible<br>
				<a href='./?$query&updadeli'>mettre à jour</a>
				<a href='./?option=reglages&maj'>changer la politique de mises à jour</a>";
			}						
		}
	}
}

insert("inc_func");
if(is_file("bin/inc_func.php")){
	include("bin/inc_func.php");
}
else{
	include("http://www.adeli.wac.fr/vers/$vers/inc_func.php");
}

/////////////// PREFERENCES
$preference_base = 'adeli_preferences';
if(!mysql_query("SHOW COLUMNS FROM $preference_base")  ){
	if(isset($_GET['mkapb'])){	
		if(mysql_query("CREATE TABLE `$preference_base` (
  `id` bigint(20) NOT NULL auto_increment,
  `user` bigint(20) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` longtext NOT NULL,
  PRIMARY KEY  (`id`)
)") ){
			$return.=returnn("La table <b>\"Pr&eacute;f&eacute;rences\"</b> a &eacute;t&eacute; cr&eacute;&eacute;e correctement<br><br><a href='./?option&newpref'>cliquez ici pour recharger Adeli</a>","009900",$vers,$theme);
			$dir = scandir('mconfig');
			foreach($dir as $entry){
				$ext=getext($entry);
				$entr = explode('.',$entry);
				$uuid = array_shift($entr);
				if($ext!='jpg' && $ext!='php' && is_numeric($uuid)){					
					if(false !== $fp = fopen("mconfig/$entry","r")){
						$ws = fread($fp,255);
						//echo $ws." ".implode('.',$entr);
						if(set_pref(implode('.',$entr),$ws,$uuid)){
							unlink("mconfig/$entry");
						}
					}
				}
			}
		}
		else{
			$return.=returnn("La table <b>\"Pr&eacute;f&eacute;rences\"</b> n'a pu être cr&eacute;&eacute;e correctement","990000",$vers,$theme);
		}
	}
	$return.=returnn("Un nouveau gestionnaire de pr&eacute;f&eacute;rence est disponible <a href='./?mkapb'>installer</a>","009900",$vers,$theme);

}


if(isset($_POST['bg'])){
  if(set_pref('bg.conf',$_POST['bg'])){
	  $return.=returnn("pr&eacute;f&eacute;rence d'image de fond modifi&eacute;e ($bg) !","FF9900",$vers,$theme);			
  }
  set_pref('bgmod.conf',$_POST['mode']);
  if($_POST['bg']=="mine"){
	  if($_FILES['file']['name'][0]!=NULL){
		  $ext = strtolower(substr(strrchr($_FILES['file']['name'][0],"."),1));
		  if($ext=='jpg'){
			  if(copy($_FILES['file']['tmp_name'][0],"mconfig/$u_id.bg.jpg")){
										  
			  }
			  else{
				  $return.=returnn("Erreur de chargement de fichier !","990000",$vers,$theme);
			  }
		  }
		  else{
			  $return.=returnn($_FILES['file']['name'][0]." n'est pas une image jpg valide !","990000",$vers,$theme);
		  }
	  }
  }
  $co = $_POST['co'];
  if($co=="default"){
	  set_pref('co.conf','');
  }
  if($co=="mine"){
	  set_pref('co.conf',$_POST['couleur']);
  }
}

$alertprintmode = get_pref('printalert.conf','x');

  
  
if(isset($_POST['setlogs'])){
  if(set_pref('logs.conf',abs($_POST['setlogs']))){
	  $return.=returnn("la dur&eacute;e de conservation des logs a bien &eacute;t&eacute; chang&eacute;e","009933",$vers,$theme);
  }
  else{
	  $return.=returnn("Une erreur s'est produite lors du changement de dur&eacute;e de conservation des logs","990000",$vers,$theme);
  }
}
$life_log = get_pref('logs.conf');

if(isset($_GET['affdesac'])){
  set_pref('affdesac',$_GET['affdesac']);
}
if(isset($_GET['set_panw'])){
  set_pref('panw',$_GET['set_panw']);
}
$affdesac=get_pref('affdesac');


if(isset($_POST['pol_maj'])){
  if(set_pref('maj',$_POST['pol_maj'])){
	  $return.=returnn("Votre politique de mise à jour a bien &eacute;t&eacute; chang&eacute;","009933",$vers,$theme);
  }
  else{
	  $return.=returnn("Une erreur s'est produite lors du changement de politique de mise à jour","990000",$vers,$theme);
  }
  $pol_maj = get_pref('maj');
}


/**************** END PREFERENCES ***/

$pol_maj = abs(get_pref('maj'));
$r_alerte = abs(get_pref('r_alerte.conf','x'));

$politique_maj = array(
	"Mises à jour automatiques avec avertissement",
	"Mises à jour automatiques silencieuses",
	"Alerte de mise à jour disponible",
	"Mises à jour manuelles"
);

/******************************							A D E L I 				***********************************/
$delais =  60*60*15;
if(!isset($_SESSION['app_versions'])) $_SESSION['app_versions']=array();
if(!isset($_SESSION['lastupdtim'])) $_SESSION['lastupdtim']=$debugtime-$delais;
if($pol_maj != 3){
	if($debugtime-$_SESSION['lastupdtim']>100  || isset($_GET["updadeli"])){
		$_SESSION['app_versions']=array();		
		if(false !== $fo = fopen("http://www.adeli.wac.fr/vers/$vers/versions.php","rb")){
			while (!feof ($fo)) {
				$buffer = explode(':',fgets($fo, 4096));
				$_SESSION['app_versions'][$buffer[0]]=$buffer[1];
			}	
		}
		$_SESSION['lastupdtim']=$debugtime;
	}
	else{
		//echo "<!-- MàJ dans ".($delais-($debugtime-$_SESSION['lastupdtim'])). "sec -->";
	}
}





if($prefix != ""){$prefix .= "_";}

if(!isset($style_url)) $style_url="http://www.adeli.wac.fr/vers/$vers";

$verifupdt = mysql_query("DESC `adeli_users`");
$allchamps = array();
while($ro = mysql_fetch_object($verifupdt)){
	array_push($allchamps,$ro->Field);
}
if(!in_array("nom",$allchamps)){
	mysql_query("ALTER TABLE `adeli_users` ADD `nom` varchar(255) NOT NULL default ''");
}
if(!in_array("onlyip",$allchamps)){
	mysql_query("ALTER TABLE `adeli_users` ADD `onlyip` varchar(20) NOT NULL default ''");
}
else{
	mysql_query("UPDATE `adeli_users` SET `onlyip`='$ip' WHERE `id`='$u_id'");
}
if(!in_array("md5",$allchamps)){
	mysql_query("ALTER TABLE `adeli_users` ADD `md5` varchar(100) NOT NULL default ''");
}


sql_open();
$res = mysql_query("SELECT * FROM `adeli_users` WHERE `id`='$u_id'");
if(!$res || mysql_num_rows($res)!=1){
	session_unset();
	//echo"$x_id / $r_id / $u_id >".mysql_num_rows($res);
	header("location: ./");
}
else{
	$ro = mysql_fetch_object($res);
	$u_login = $ro->login;
	$u_nom = $ro->nom;
	$u_pass = $ro->pass;
	$u_email =$ro->email;
	$u_md5 = $ro->md5;
	$u_d= $ro->d;
	$u_g = $ro->g;
	$u_dgw=1;
	if($u_nom==''){
		$u_nom = ucfirst($u_login);
		mysql_query("UPDATE `adeli_users` SET `nom`='$u_nom' WHERE `id`='$u_id'");
	}
	if($u_md5==''){
		$u_md5 = uniqid(md5(rand()),true);
		mysql_query("UPDATE `adeli_users` SET `md5`='$u_md5' WHERE `id`='$u_id'");
	}
	$_SESSION['u_md5']=$u_md5;
	if($u_g!=0){
		$res = mysql_query("SELECT * FROM `adeli_groupe` WHERE id=$u_g");
		$ro = mysql_fetch_object($res);
		$u_droits= $ro->droits;
		$u_active= $ro->da;
		$u_dgw= $ro->dgw;
		$u_depend= $ro->depend;
		$u_gname = $ro->nom;
		$u_login = "$u_login";
		if(substr_count($u_depend,":")==3){
			$u_restreint = split(":",$u_depend);
		}
		if(isset($u_restreint) && (isset($_GET[$u_restreint[1]]) || (isset($_GET['part']) && $_GET['part']==$u_restreint[1])) && (isset($edit) || isset($_GET['edit']))){
			$edit=$u_d;
			include("mconfig/adeli.php");
		}
	}
}
mysql_query("UPDATE `adeli_users` SET `last`=NOW() WHERE `id`='$u_id'");	

if($u_droits!=""){
	$optico = explode(",",$u_droits);				
	$opt="";
	for($o=0 ; $o<sizeof($optico) ; $o++){
		$thisdroit = $optico[$o];
		if(ereg("<",$thisdroit)){
			$thisdroit = substr($thisdroit,0,strpos($thisdroit,"<"));
		}
		$opt.="$thisdroit,";
	}
	$opt.="reglages,";
	//$opt = substr($opt,0,strlen($opt)-1);						


  for($o=0 ; $o<sizeof($optico) ; $o++){
	  $thisdroit = $optico[$o];
	  if(ereg("<",$thisdroit)){
		  $thisdroito = substr($thisdroit,0,strpos($thisdroit,"<"));
		  if($thisdroito == 'site'){
			  
			  $menu_temp = array();
			  $thisdroit = explode(";",substr($thisdroit,strpos($thisdroit,"<")+1,strlen($thisdroit)-(strpos($thisdroit,"<")+2)));
			  
			  foreach($menu as $spart=>$tablo){
				  foreach($tablo as $k=>$v){
					  if(in_array($v,$thisdroit)){
						  $menu_temp[$spart][$k]=$v;
					  }
				  }								
			  }
			  $menu  = $menu_temp;	
		  }
	  }
  }
}
$_SESSION['menu_site'] = $menu_site = $menu;
function isopt($op){
	global $menu_site;
	foreach($menu_site as $k=>$v){
		if($op==$k) return true;
	}
	return false;
}
function isoption($op){
	global $options;
	return(in_array($op,$options));
}

$optstring=$opt;
$optico = explode(",",$opt);

if(isopt('worknet') || isopt('gestion')){
	$opty="";
	foreach($optico as $v){
		$opty.="$v,";
		if($v=='site'){
			if(isopt('worknet')) $opty.="worknet,";
			if(isopt('gestion')) $opty.="gestion,";
		}
	}	
	$opt = $opty;
	$optico = explode(",",$opt);				
}




$opt = $optico;
$urlserveur = str_replace('www.','',$prov);
$prov = str_replace('www.','',getenv("SERVER_NAME"));
$_SESSION["r_alerte"]=$r_alerte;
$_SESSION["date"]=date("Y-m-d");
$mysqlnow=$_SESSION["datetime"]=date("Y-m-d H:i:s");
$agent=getenv("HTTP_USER_AGENT");

$mail_base="adeli_mailbox";
$users_table="adeli_users";
$agenda_base="adeli_agenda";
$ftp_base="adeli_ftp";

$fields_values=array();




//require('http://www.adeli.wac.fr/libs/fpdf.ext');
/*if(true!==$incf = includ('http://www.adeli.wac.fr/libs/fpdf.ext')){
  eval ($incf);
}
else{
	include('http://www.adeli.wac.fr/libs/fpdf.ext');	
}*/
define('FPDF_FONTPATH','http://www.adeli.wac.fr/libs/fpdf/font/');	




$QUERY_STRING=$query;
$parto = explode ('&', urldecode($QUERY_STRING));
$parto = $parto[0];	
$_SESSION["mail_base"]=$mail_base;
$_SESSION["db_base"]=$base;
$_SESSION["db_user"]=$login;
$_SESSION["db_pass"]=$passe;
$_SESSION["db_host"]=$host;
$part="";

if(!is_dir("tmp")){		@mkdir("tmp",0777);	} 
if(!is_dir("bin")){		@mkdir("bin",0777);	} 
if(!is_dir("mconfig") && is_dir("config")){		@rename("config","mconfig");	} 
if(!is_dir("mconfig")){	@mkdir("mconfig",0777);	}

insert("index");

if(isset($_GET['option'])){
	$option = $_GET['option'];
	$titro = "";
}
else{
	$option="accueil";
}
	
	$mega_menu=array();
	
	foreach($menu as $spart=>$tablo){
		$cols = sizeof($tablo);	
		if( substr($spart,0,7)=="worknet" ){
			$mega_menu['worknet'][$spart]=$tablo;
		}
		elseif( substr($spart,0,7)=="gestion" ){
			$mega_menu['gestion'][$spart]=$tablo;
		}
		else{
			$mega_menu['site'][$spart]=$tablo;
		}
	}
	if(isoption("agenda")){
		$mega_menu['agenda']=array(
		"calendrier"=>array("jour","semaine","mois","ann&eacute;e"=>"annee","liste"),
		"edition"=>array("nouveau")
		);
	}
	if(isoption("messagerie")){
		$mega_menu['messagerie']=array("messages"=>array("tous","reception","envoi","nouveau"));
	}
	if(isoption("ftp")){
		$mega_menu['ftp']=array("gestion ftp"=>array("comptes"));
	}
	if(isoption("mail")){
		$mega_menu['mail']=array(
		"messages"=>array("lecture","nouveau"),
		"configuration"=>array("comptes","signature"
		));
	}
	if(isoption("compta")){
		$mega_menu['compta']=array(
		"vente"=>array("Devis"=>"devis","Commandes"=>"commande","Factures"=>"facture","avoirs"=>"avoir"),
		"achat"=>array("Achats / Bon de commande"=>"achat","livraison","Frais personnel"=>"frais"),
		"outils"=>array("statistiques","bilan","r&eacute;glages"=>"reglages")
		);
	}
	if(isoption("groupe")){
		$mega_menu['groupe']=array(
		"suivi"=>array("groupes","utilisateurs")
		);
	}
	if(isoption("picto")){
		$mega_menu['picto']=array(
		"picto"=>array("picto")
		);
	}
	
	
	if(isoption("lalie")){
		$mega_menu['lalie']=array(
		"Contact"=>array("contacts","importer"),
		"edition"=>array("edition_mail","sms","lettre"),
		"historique"=>array("archives","historique"));
		if(ereg("site",$optstring)){
			$mega_menu['lalie']["Contact"][2]="exporter";
		}
	}
	$mega_menu['reglages']=array(
	"personnalisation"=>array("lecteur RSS"=>"adeli_rss","compte","affichage"=>"personnalisation"),
	"configuration"=>array("mises à jour"=>"maj","alerte","logs","Paramètres de s&eacute;curit&eacute;"=>"secure")
	);
	
$menupart = array_keys($menu);

	insert("_ima");
	insert("inc_header");
	if(is_file("bin/inc_header.php")){
		include("bin/inc_header.php");
	}
	else{
		include("http://www.adeli.wac.fr/vers/$vers/update.php?file=inc_header.php");
	}
	$ok_sec=true;
	$compo="";
	
	if( isset($r_mdp_secure) &&  in_array("groupe",$optico) && $r_mdp_secure!='' ){
		$groupe_mdp_secure = split(',',$r_mdp_secure.',');
		$sign_assoc = array("="=>"!=",">"=>"<=","<"=>">=");
		$sign_verboc = array("="=>" &eacute;gale à ",">"=>" plus de ","<"=>" moins de ");
		
		foreach($groupe_mdp_secure as $sec){
			$psec = split("[>=<]",$sec);
			if(sizeof($psec)==2 && is_numeric($psec[1]) ){
				$sign = substr($sec,strlen($psec[0]),1);				
				if($psec[0]=="length"){
					
					$compo.="<br>- Il doit contenir '.$sign_verboc[$sign].' '.$psec[1].' caractère(s)";
					if(eval( strlen($u_pass)." $sign_assoc[$sign] ".$psec[1])){
						$ok_sec=false;
						$compo.="<img src='http://www.adeli.wac.fr/vers/$vers/algues/notok.gif' alt='notok'>";
					}
					else{
						$compo.="<img src='http://www.adeli.wac.fr/vers/$vers/algues/ok.gif' alt='ok'>";
					}
					
				}
				elseif(substr($psec[0],0,1)=='[' && substr($psec[0],strlen($psec[0])-1,1)==']' ){
					$hsec=$psec[0];
					switch($hsec){
						case '[a-z]': $hsec=" caractère(s) en minuscule"; break;
						case '[A-Z]': $$hsec=" caractère(s) en majuscule"; break;
						case '[[:punct:]]': $hsec=" signes de ponctuation"; break;
						case '[[:alnum:]]': $hsec=" caractère(s) alpha-num&eacute;rique(s)"; break;
						case '[0-9]': $hsec=" chiffre(s)"; break;
					}					
					
					$compo.="<br>- Il doit contenir '.$sign_verboc[$sign].' '.$psec[1].' '.$hsec.'";	
					preg_match("['.$psec[0].']",$u_pass,$mat, PREG_OFFSET_CAPTURE);
					if(is_array($mat) && eval( sizeof($mat)." $sign_assoc[$sign] ".$psec[1])){
						$ok_sec=false;
						$compo.=", il en contient ".sizeof($mat)." <img src='http://www.adeli.wac.fr/vers/$vers/algues/notok.gif' alt='notok'>";
					}
					else{
						$compo.="<img src='http://www.adeli.wac.fr/vers/$vers/algues/ok.gif' alt='ok'>";
					}
				}
			}
		}
		
	}
	

	if($ok_sec == false){
		echo"
		Votre mot de passe ne correspond pas aux normes de s&eacute;curit&eacute; exig&eacute;es par votre administrateur.<br>$compo
		<br><br>";
	}
	if($ok_sec == true ||( $option=="reglages" && isset($_GET["compte"]) )){
		if(in_array($option,$noupdate)){
			include("_$option.php");
		}
		else{
			if( in_array("picto",$opt)){
				insert("_picto");
				insert("_picto_file");
			}	
			$incfich = "_".$option;
			if($option=="site" && $modul_part=="gestion"  && !isset($_GET["exporter"]) && !isset($_GET["importer"]) && !isset($_GET["statistiques"])){
				$incfich = "_gestion";
			}
			if($option=="site" && $modul_part=="worknet"  && !isset($_GET["exporter"]) && !isset($_GET["importer"]) && !isset($_GET["statistiques"])){
				$incfich = "_worknet";
			}
			
			$menu = $mega_menu[$option];
			insert($incfich);
			echo"";
			if(is_file("bin/$incfich.php")){
				include("bin/$incfich.php");
			}
			else{			
				include("http://www.adeli.wac.fr/vers/$vers/update.php?file=$incfich.php");
			}
			echo"";
		}
	}
	else{
		echo"
		vous devez le modifier en vous rendant dans votre <a href='./?option=reglages&compte'>compte utilisateur</a>
		";
	}
//include("http://www.adeli.wac.fr/vers/$vers/_$option.php?x_id=$x_id&u_id=$u_id&$query");
echo"</div>";


if($debit==0){

?>


<?php
$stylo="";
	$stylefile='';
	if(is_file("../$part/style.css")){
		$stylefile="../$part/style.css";
	}
	elseif(is_file("style.css")){
		$stylefile="style.css";
	}
	if($stylefile!=''){
		  $stylo.=',
		  content_css : "'.$stylefile.'"';
	}
?>
		<script type="text/javascript" src="http://adeli.wac.fr/vers/<?=$vers?>/tiny_mce/tiny_mce.js"></script>
           
		<script language="javascript" type="text/javascript">
		function editor_oninit(ed) {
			// Add hook for onContextMenu so that Insert Image can be removed
			//ed.plugins.contextmenu.onContextMenu.add(editor_remove_items);
		}
		tinyMCEmode=new Array();
		function toogleEditorMode(sEditorID,ki) { 		
			if(tinyMCEmode[sEditorID]==undefined || tinyMCEmode[sEditorID]==true) { 
				tinyMCE.execCommand('mceRemoveControl',false,sEditorID);
				tinyMCEmode[sEditorID] = false; 
				ki.innerHTML="Editeur / <b>Source</b>";
				
			} else { 
				tinyMCE.execCommand('mceAddControl',false,sEditorID);
				tinyMCEmode[sEditorID] = true; 
				ki.innerHTML="<b>Editeur</b> / Source";
			} 			
		}
		
		/*function editor_remove_items(sender, menu) {
			 var otherItems = {};
			 var noms='';
			for (var itemName in menu.items) {
				if (/^mce_/.test(itemName)) {
					var item = menu.items[itemName];
					if (item.settings) {
						if (item.settings.cmd == "mceImage" || item.settings.cmd == "mceAdvImage" || item.settings.cmd == "mceInsertTable" || item.settings.cmd == "mceTable" || item.settings.cmd == "mceAdvTable" || item.settings.cmd == "mceLink" || item.settings.cmd == "mceAdvLink" || item.settings.cmd == "Copy" || item.settings.cmd == "Paste" || item.settings.cmd == "Cut") {
							item.disabled = 1;
							//break;
						}
						else{
							otherItems[itemName] = item;
						}
					}
				}
			}
			menu.items = otherItems;
		}*/
		tinyMCE.init({
				theme : "advanced",
				skin : "o2k7",
				language : "fr",
				plugins : "table,-externalplugin",
				mode : "specific_textareas",
				elements : 'absurls',
        		editor_selector : "editor",
				//imagemanager_contextmenu: false,
				document_base_url : "http://<?=$serv?>/",
				relative_urls : false,
				/*imagemanager_contextmenu: false,
				theme_advanced_resizing : true,
				theme_advanced_resizing_min_height : 280,
				theme_advanced_resizing_max_height : 400,*/
				theme_advanced_blockformats : "p,div,h1,h2,h3,h4,h5,h6,blockquote,code",
				theme_advanced_toolbar_location : "top",
   				theme_advanced_toolbar_align : "left",
				theme_advanced_more_colors : false,
				theme_advanced_buttons1 : "bold,italic,underline,fontselect,fontsizeselect,formatselect,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,forecolor,backcolor",
				theme_advanced_buttons2 : "undo,redo,separator,outdent,indent,bullist,numlist,separator,hr,adelisaut,removeformat,cleanup,visualaid,separator,sub,sup,separator,adelilink,unlink,adeliancre,adelitable,delete_col,delete_row,col_after,row_after,split_cells,merge_cells",
				theme_advanced_buttons3 : "",
				setup : function(ed) {
					// Add a custom button
					ed.addButton(
						'adelilink', {
							title : 'Lien hypertexte',
							image : '<?=$style_url?>/images/link.gif',
							onclick : function() {
								// Add you own code to execute something on click
								ed.focus();
								addlink();
								//ed.selection.setContent('Hello world!');
								return false;
							}
						}
					);
					ed.addButton(
						'adeliancre', {
							title : 'Ancre',
							image : '<?=$style_url?>/images/ancre.gif',
							onclick : function() {
								// Add you own code to execute something on click
								ed.focus();
								addancre();
								//ed.selection.setContent('Hello world!');
								return false;
							}
						}
					);
					
					ed.addButton(
						'adelisaut', {
							title : 'Retour à la ligne',
							image : '<?=$style_url?>/images/br.gif',
							onclick : function() {
								// Add you own code to execute something on click
								ed.focus();
								sautdeligne();
								//ed.selection.setContent('Hello world!');
								return false;
							}
						}
					);
					ed.addButton(
						'adelitable', {
							title : 'Tableau',
							image : '<?=$style_url?>/images/table.gif',
							onclick : function() {
								// Add you own code to execute something on click
								ed.focus();
								document.getElementById('tableau').style.display='block';
								//ed.selection.setContent('Hello world!');
								return false;
							}
						}
					);
					ed.onInit.add(editor_oninit);
				}<?=$stylo?>
				
		});
		
		tab_x=1;
		tab_y=1;
		tab_b='';
		tab_c=1;
		tab_s=0;
		tab_p=3;
		</script>
        <style>
		.mceIframeContainer{
			margin:0 !important;
			padding:0 !important;	
		}
		
		.mceIframeContainer iframe{
			min-height:280px;
		}
		</style>
       <div id='tableau'>
          <b>Ins&eacute;rer un tableau</b><hr/>
            <table>
            <tr><td>Colones : </td><td><input type='number' value='1' onkeyup='javascript:tab_x=this.value' size='2' step='1' min='1'></td></tr>
            <tr><td>Lignes : </td><td><input type='number' value='1' onkeyup='javascript:tab_y=this.value' size='2' step='1' min='1'></td></tr>
            <tr><td>Contour : </td><td><input type='number' value='1' onkeyup='javascript:tab_c=this.value' size='2' step='1' min='0'>px</td></tr>
           <tr><td colspan='2'>Cellules</td></tr>
           <tr><td>Espacement : </td><td><input type='number' value='0' onkeyup='javascript:tab_s=this.value' size='2' step='1' min='0'>px</td></tr>
            <tr><td>marge int&eacute;rieure : </td><td><input type='number' value='3' onkeyup='javascript:tab_p=this.value' size='2' step='1' min='0'>px</td></tr>
            
            <tr><td colspan='2' align='right'>
            <input type='button' value='annuler' class='buttontd' onclick="document.getElementById('tableau').style.display='none';tab_x=1;tab_y=1;tab_b='';tab_c=1;tab_s=0;tab_p=3;">
            <input type='button' value='ins&eacute;rer' class='buttontd' onclick="mcetableau(tab_x,tab_y,tab_b,tab_c,tab_s,tab_p);document.getElementById('tableau').style.display='none';tab_x=1;tab_y=1;tab_b='';tab_c=1;tab_s=0;tab_p=3;">
            </td></tr>
            </table>
        </div>

<?php
	echo"</td>
	
	<td valign='top' id='pplus'>	
	<div id='panel_plus'>";
	if(is_file("bin/inc_ajax.php")){
		$notepad=get_pref('notepad.txt');
		$calcul=get_pref('calcul.txt');
		$calcultva=get_pref('calcutva.txt');
		if($calcultva=='') $calcutva='19.6';
		echo"
		<a href='./?option=reglages'><img alt='R' src='http://www.adeli.wac.fr/vers/$vers/img/reglages.png' border='none'></a>
		<br />
		<a href='#' onclick=\"openaide()\" class='info'><img src=\"http://www.adeli.wac.fr/vers/$vers/$theme/aide.gif\" border=\"none\" alt=\"aide\" align=\"absmiddle\" width='20'>
				<span>Aide</span></a>
		<br/><br/><br/><br/><br/><br/>
		<div style='width:20px;height:20px;position:relative;z-index:301'>
			<img alt='R' src='http://www.adeli.wac.fr/vers/$vers/img/rss.png'  onClick='efto(\"rss\");'>
			<div id='panelr_rss' class='gadget colofon' style='height:300;width:0'>
				RSS :<br>
				<div style='background-color:#FFF; width:190px; height:250px; position:relative; overflow-y:scroll; overflow-x:hidden' id='rsscontent'>
				";
				if(mysql_query("SHOW COLUMNS FROM `adeli_rss`")  ){
					$res = mysql_query("SELECT * FROM `adeli_rss` WHERE (public='$u_id' OR public=0) AND active=1 AND `type`=0 AND emplacement=0");
					if($res && mysql_num_rows($res)>0){
						while($ro = mysql_fetch_object($res)){
							$type=$ro->type;
							$url=$ro->url;
							$limite=$ro->limite;
							$nom=ucfirst($ro->nom);
							$rss=$ro->id;
							echo"<font color='#000000'><b>$nom</b> </font>
							<a href='./?option=reglages&adeli_rss&edit=$rss' class='info'><img src='http://www.adeli.wac.fr/vers/$vers/skins/$theme/modif.gif' height='16' alt='modifier' border='none'><span>Modifier les paramètres</span></a>
							<div style='position:relative;width:165px;overflow-x:hidden'>";
							//if($type==0){
								getrss($url,$limite,24);
							/*}
							if($type==1){
								parse_int($urle,$limite);
							}*/
							echo"</div>";
						}
					}
				}
				echo"
				</div>
				<a href='./?option=reglages&adeli_rss&edit&emplacement=0'><font size='1'>ajouter un fil RSS ici</font> </a>
				<span id='rssreturn' class='petittext'></span>
			</div>
		</div>
		<div style='width:20px;height:20px;position:relative;z-index:301'>
			<img alt='N' src='http://www.adeli.wac.fr/vers/$vers/img/notepad.png'  onClick='efto(\"note\");'>
			<div id='panelr_note' class='gadget colofon' style='height:200;width:0'>
				Notes :<br>
				<textarea name='notepad' style='width:190px;height:190px;' onchange=\"envoyer('bin/inc_ajax.php?scan=notepad','texte',this.value,'notepadreturn')\">$notepad</textarea><br>
				<span id='notepadreturn' class='petittext'></span>
			</div>
		</div>
		<div style='width:20px;height:20px;position:relative;z-index:301'>
			<img alt='C' src='http://www.adeli.wac.fr/vers/$vers/img/calc.png'  onClick='efto(\"calc\");'>
			<div id='panelr_calc' class='gadget colofon' style='height:90;width:0'>
				<form name='calcul' onSubmit=\"calcule('ega');return false;\">
				Calculatrice :<br>
				<input type='text' name='tape' value='$calcul' onfocus='this.select()' onKeyup='evaluat()' style='width:130px;font-size:16px;font-weight:bold'>
				<br><span class='petittext'>
				<a value='ttc' onclick=\"calcule('ttc')\">ttc</a>
				<a value='tva+' onclick=\"calcule('tva+')\">tva+</a>
				<a value='ht' onclick=\"calcule('ht')\">ht</a>
				<a value='tva-' onclick=\"calcule('tva-')\">tva-</a><br>
				<input type='text' name='tva' value='$calcutva' size='4'>%
				<input type='submit' value='=' style='display:none'>
				</span>
				</form>
				<span id='calculreturn' class='petittext'></span>
			</div>
		</div>";
		if(in_array("compta",$optico) ){  ///////// COMPTA
echo"<div style='width:20px;height:20px;position:relative;z-index:301'>
			<img alt='C' src='http://www.adeli.wac.fr/vers/$vers/img/favoris.png'  onClick='efto(\"compta\");'>
			<div id='panelr_compta' class='gadget colofon' style='height:90;width:0'>
			Favoris<br />
			<a href='./?option=compta&devis&edit&freecontent' >Nouveau devis</a><br />
			<a href='./?option=compta&facture&edit&freecontent' >Nouvelle facture</a><br />
			<a href='./?option=compta&commande&edit&freecontent' >Nouvelle commande</a>
			</div>
		</div>";
		}		
		echo"
		
	";
	}
	echo"</div></td>";
}
else{
	//echo"<div id='panelr' style='display:none;width:10px;height:10px;'></div>";
}
echo"
</tr></table> </td></tr></table>";
  
	
if(file_exists("inc_foot.php")){
	include("inc_foot.php");
} 	 

if($life_log > 0){
	if(!is_dir("mconfig/logs")){
		mkdir("mconfig/logs");
	}
	if(is_dir("mconfig/logs")){
		$sem = date("YW");
		$limilfe = date("YW",strtotime("-$life_log Weeks"));
		$dir=dir("mconfig/logs");
		while(false !== $entry = $dir->read()){
			if(is_file("mconfig/logs/$entry")){
				if($entry < $limilfe){
					unlink("mconfig/logs/$entry");
				}
			}
		}
		$in_log = date("d/m/Y H:i:s")."<br>utilisateur : $u_login ($u_id)<br><a class='info'>$option > $part $edit
		<span>$query</span>
		</a><br>$return<hr>";
		$fp = fopen("mconfig/logs/$sem.log","a+");
		fwrite($fp,$in_log);
		fclose($fp);
	}
}
if($return!="" && $alertprintmode==1){
	$return="
	<div style='width:320px;background:#FFFFFF;border:#000000 thin solid;color:#000; padding:5px;'><center>
	$return		
	<a style='cursor:pointer;display:block' onclick=\"document.getElementById('phpreturn').innerHTML='';document.getElementById('phpreturn').style.display='none';document.getElementById('phpreturn').style.height='1px';\"><font class='petittext'>ok</font></a>
	</center></div>";	
}
else{
	$return='';
}
	  echo"
	  </center>
</td></tr>
";
if(!isset($_SESSION['u_debit']) || $_SESSION['u_debit']==0){
	echo"<tr><td valign='bottom' id='bottom'><a href='http://www.adeli.wac.fr' target='_blank'>adeli</a> > $prov</td></tr>";
}
echo"
</table>	 ";
if($debit==0){
echo"
	 <div id='deconmask' class=\"popup\">
	 <table style=\"width:100%;height:100%\">
	 <tr><td  align='center' valign='middle'>	 
	 <table width='300' cellpadding='5' class='alert'>
	 <tr><td  align='center' valign='middle'>
	 <b>êtes vous sûr de vouloir vous d&eacute;connecter maintenant?</b><br><br>	 
	 <table cellspacing='5' cellpadding='0' border='0'><tr>	   
	  <td class=\"buttontd\"><a href=\"./?decon\"><b>oui</b></a></td>	  
	  <td class=\"buttontd\"><a href=\"#\" onclick=\"reconnect()\"><b>non</b></a></td>	  
	  </tr></table>	 
	 </td></tr>
	 </table>	 
	</td></tr>
	 </table>
	 </div>	
	 
	 <div id='phpreturn'>$return</div>
	 <div id='htmlreturn' style='position:absolute;top:40px;width:325px;overflow-x:hidden;right:5px;text-align:center;z-index:490;padding:0px'></div>
		<script language='javascript' type='text/javascript'>				
				fin();
				$ajaxf				
				
		</script>
				<link rel='stylesheet' href='http://adeli.wac.fr/vernissage/vernissage.css' type='text/css'>
				<script language='javascript' type='text/javascript' src='http://adeli.wac.fr/vernissage/vernissage.js'></script>";
}	

	echo"
	 
  
	</body>
	</html>";
}
else{
	echo"Configuration error.";	
}
?>