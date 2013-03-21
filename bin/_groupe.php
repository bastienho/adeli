<?php // 101 > Gestion de comptes utilisateurs ;
$verifupdt = mysql_query("DESC `adeli_groupe`");
$allchamps = array();
while($ro = mysql_fetch_object($verifupdt)){
	array_push($allchamps,$ro->Field);
}
if(!in_array("dgw",$allchamps)){
	mysql_query("ALTER TABLE `adeli_groupe` ADD `dgw` INT NOT NULL default '0'");
	mysql_query("UPDATE `adeli_groupe` SET `dgw`= '1'");
}
$menuitem = array('nom',"login","last","email","droits","depend");
$menutranslate = array(
	"nom"=>"Utilisateur",
	"login"=>"Accès",
	"last"=>"Dernière connexion",
	"email"=>"Email",
	"droits"=>"Groupe",
	"depend"=>"Limite"
);
$menuitemg = array("nom","nombre","droits","depend","da");
$menutranslateg = array(
	"nom"=>"Nom du groupe",
	"nombre"=>"Utilisateurs",
	"droits"=>"Droits",
	"depend"=>"Limite",
	"da"=>"Mise en Ligne"
);

$ouinon = array("Ne peut pas activer d'éléments","Peut activer les éléments");
$readwrite = array("Accès en lecture seul","Accès en lecture/écriture");
$bool = array("non","oui");
				

///////////////////////////////////////////////////////// UPDATE USER
if(isset($_GET['updateuser'])){
	$id = $_GET['updateuser'];
	$nom = str_replace("'","''",$_POST['nom']);
	$login = str_replace("'","''",$_POST['login']);
	$email = $_POST['email'];
	$g = $_POST['g'];
	$d = $_POST['d'];
	$pass = str_replace("'","''",$_POST['pass']);
	$pd='';
	if($pass!=''){
		if( isset($pass_sql_encode) && in_array('adeli_users',$pass_sql_encode)){
			$pd=",`pass`=PASSWORD('$pass') ";
		}
		else{
			$pd=",`pass`='$pass'";
		}
		
	}
	$res = mysql_query("SELECT * FROM `adeli_users` WHERE `login`='$login' AND `id`!='$id'");
	if(mysql_num_rows($res)==0){
		if(mysql_query("UPDATE `adeli_users` SET `login`='$login',`nom`='$nom',`email`='$email',`g`='$g',`d`='$d' $pd WHERE `id`='$id'")){
			$return.=returnn("modification effectuée avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("modification échouée","990000",$vers,$theme);
		}
	}
	else{
		$return.=returnn("Cet utilisateur existe déjà !","FF9900",$vers,$theme);
	}
}
///////////////////////////////////////////////////////// ADD USER
if(isset($_GET['adduser'])){
	$login = ereg_replace("'","''",$_POST['login']);
	$nom = ereg_replace("'","''",$_POST['nom']);
	$pass = ereg_replace("'","''",$_POST['pass']);
	$email = $_POST['email'];
	$g = $_POST['g'];
	$d = $_POST['d'];
	$res = mysql_query("SELECT * FROM `adeli_users` WHERE `login`='$login'");
	if(mysql_num_rows($res)==0){
		if( isset($pass_sql_encode) && in_array('adeli_users',$pass_sql_encode)){
			$pass="PASSWORD('$pass') ";
		}
		else{
			$pass="'$pass'";
		}
		if(mysql_query("INSERT INTO `adeli_users` (`nom`,`login`,`pass`,`last`,`email`,`g`,`d`) VALUES ('$nom','$login', $pass, '0000-00-00 00:00:00', '$email', '$g','$d')")){
			$inid = mysql_insert_id($conn);
			$return.=returnn("enregistrement effectuée avec succès","009900",$vers,$theme);
			if($g==''){
				mysql_query("INSERT INTO `adeli_groupe` (`id`) VALUES ('')");
				$inig = mysql_insert_id($conn);
				mysql_query("UPDATE `adeli_users` SET `g`='$inig' WHERE `id`='$inid'");
			}
			echo"<script language='javascript'>document.location='./?option=groupe&utilisateurs&id=$inid'</script>";
		}
		else{
			$return.=returnn("enregistrement échoué","990000",$vers,$theme);
		}
	}
	else{
		$return.=returnn("Cet utilisateur existe déjà !","FF9900",$vers,$theme);
	}
}
///////////////////////////////////////////////////////// DEL USER
if(isset($_GET['deluser'])){
	$deluser = $_GET['deluser'];
	if(mysql_query("DELETE FROM `adeli_users` WHERE id='$deluser'")){
		$return.=returnn("effacement effectuée avec succès","009900",$vers,$theme);
	}
	else{
		$return.=returnn("effacement échoué","990000",$vers,$theme);
	}
}


$order="id";
if(isset($_GET['order'])){
	$order = $_GET['order'];
}
	
	
if($part != ""){

//////////////////////////////////////////////////////////////////////////////////////////////////////// GROUPE
if($part == "groupes"){		
		
		if(isset($_GET['edit'])){
			
			$edit = $_GET['edit'];
			if(isset($_GET['maj']) || isset($_GET['ajout'])){
				$m_droits='';
				foreach($opt as $op){
					if($op!='' &&  $op!='worknet' && $op!='gestion' && $op!='reglages'){
						if(isset($_POST[$op])) $m_droits.=$op;
						if($op=='site'){
							$sm='';
							for($i=0; $i<sizeof($menu_site) ; $i++){
								$spart = $site_menupart[$i];
								$tablo = $menu_site[$spart];
								$cols = sizeof($tablo);			
								$tablk = array_keys($tablo);
								for($m=0; $m<sizeof($tablo) ; $m++){
									$tk = $tablk[$m];
									if(isset($_POST["$op:$tablo[$tk]"])) $sm.=$tablo[$tk].';';
								}														
							}
							$sm = substr($sm,0,strlen($sm)-1);
							if($sm!='') $m_droits.="<$sm>";
						}
						$m_droits.=',';
					}
				}
				$m_droits = substr($m_droits,0,strlen($m_droits)-1);
				$depend='';
				if($_POST['zon']!=''){					
					$zon = $_POST['zon'];
					$dep = $_POST['dep'];
					$depend = "site:$zon:id:$dep";
				}
				$da = $_POST['da'];
				$dgw = $_POST['dgw'];
				if(isset($_GET['maj'])){
					if(mysql_query("UPDATE `adeli_groupe` SET `nom`='".str_replace("'","''",$_POST['nom'])."',`droits`='$m_droits',`depend`='$depend',`da`='$da', `dgw`='$dgw' WHERE `id`='$edit'")){
						$return.=returnn("Mise à jour  de <b>$edit@Groupes</b> effectuée avec succès","009900",$vers,$theme);
					}
					else{
						$return.=returnn("La mise à jour à échouée","990000",$vers,$theme);
					}
				}
				if(isset($_GET['ajout'])){
					if(mysql_query("INSERT INTO `adeli_groupe` (`nom`,`droits`,`depend`,`da`,`active`) 
					VALUES
					('".str_replace("'","''",$_POST['nom'])."','$m_droits','$depend','$da',1)")){
						$edit = mysql_insert_id($conn);
						$return.=returnn("Nouveau groupe ajouté avec succès <br>identifiant généré : <b>$edit</b>","009900",$vers,$theme);
					}
					else{
						$return.=returnn("L'ajout de groupe a échoué","990000",$vers,$theme);
					}
				}				
			}
			
			
			$res = mysql_query("SELECT * FROM `adeli_groupe` WHERE `id`='".str_replace("'","''",$edit)."'");
			if($res && mysql_num_rows($res)==1){
			 	$ro = mysql_fetch_object($res);
				$m_id = $ro->id;
				$m_nom = $ro->nom;
				$m_droits = trim($ro->droits);
				$m_limite = trim($ro->depend);
				$m_da = trim($ro->da);
				$m_dgw = trim($ro->dgw);
				$action="&edit=$edit&maj";
			}
			else{
				$m_nom = 'Nouveau groupe';
				$m_droits = '';
				$m_limite = '';
				$m_da = '';
				$action="&edit&ajout";
			}
			
			echo"<form action='./?option=$option&part=$part$action' method='post' name='artos'>
		<table cellspacing='5' cellpadding='5' class='cadrebas' width='90%'>
			<tr><td colspan='3'>
			Intitulé : <input type='text' name='nom' value=\"$m_nom\">
			</td></tr>
			<tr><td valign='top' id='dr' class='buttontd' style='text-align:left' rowspan='2'>
			<b>Menu</b><hr>
			<a style='cursor:pointer'>Tous</a> |
			<a style='cursor:pointer'>Aucun</a><br>";
			$mm_droits_keys = explode(",",$m_droits); 
			$m_droits=array();
			for($m=0 ; $m<sizeof($mm_droits_keys) ; $m++){
				$thisdroit = trim($mm_droits_keys[$m]);
				if(ereg("<",$thisdroit)){
					$optthisd = substr($thisdroit,strpos($thisdroit,"<")+1,strlen($thisdroit)-(strpos($thisdroit,"<")+2));
					$thisdroit = substr($thisdroit,0,strpos($thisdroit,"<"));
					$m_droits[$thisdroit]=explode(";",$optthisd);
				}
				else{
					$m_droits[$thisdroit]=array();
				}
			}
			$allparts=array();
			foreach($opt as $op){
				if($op!='' &&  $op!='worknet' && $op!='gestion' && $op!='reglages'){
					echo"<h2><input type='checkbox' name='$op' ";
					if(isset($m_droits[$op]) || sizeof($mm_droits_keys)==0) echo'checked=checked';
					echo"> $op</h2>";
					if($op=='site'){
						echo"<ul>";
						for($i=0; $i<sizeof($menu_site) ; $i++){
							
							$spart = $site_menupart[$i];
							$sepa='site';
							if(substr($spart,0,7)=='worknet') $sepa='worknet';
							if(substr($spart,0,7)=='gestion') $sepa='gestion';
							
							
								echo"</ul><b>$spart</b><ul>";
								$tablo = $menu_site[$spart];
								$cols = sizeof($tablo);			
								$tablk = array_keys($tablo);
								for($m=0; $m<sizeof($tablo) ; $m++){
									$tk = $tablk[$m];
									if(!is_numeric($tk)){
										$humanpart = $tk;
									}
									else{
										$humanpart = $tablo[$tk];
										if($prefixe != ""){
											$humanpart = str_replace($prefixe,"",$humanpart);
										}
										$humanpart = str_replace($spart."_","",$humanpart);
										$humanpart = str_replace("adeli_","",$humanpart);
										$humanpart = str_replace(">$spart","",$humanpart);
										$humanpart = str_replace("-$spart","",$humanpart);
										$humanpart = str_replace(">"," ",$humanpart);	
									}
									array_push($allparts,"$op:$tablo[$tk]");
									$humanpart = ucfirst($humanpart);	
									echo"<li><input type='checkbox' name='$op:$tablo[$tk]' ";
									if( isset($m_droits[$op]) && ( in_array($tablo[$tk],$m_droits[$op]) || sizeof($m_droits[$op])==0)) echo'checked=checked';
									echo">$humanpart</li>";
								}				
									
						}
						echo"</ul>";
					}
				}
			}
			
				$limi = array('','','');
				if(substr_count($m_limite,":")==3){
					$limi = explode(":",$m_limite);
				}
				echo"
					</td><td valign='top' class='buttontd' style='text-align:left'>
					<b>Limitation</b><hr>
					<select name='zon' onchange='selzon(this.value)'>
					<option value=''>Aucune</option>";
					$jar="var chps=new Array();";
					for($i=0; $i<sizeof($menu_site) ; $i++){							
						$spart = $site_menupart[$i];
													
							$tablo = $menu_site[$spart];
							$cols = sizeof($tablo);			
							$tablk = array_keys($tablo);
							for($m=0; $m<sizeof($tablo) ; $m++){
								$tk = $tablk[$m];
								if(!is_numeric($tk)){
									$humanpart = $tk;
								}
								else{
									$humanpart = $tablo[$tk];
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
								$s='';
								if($limi[1] == $tablo[$tk]){
									$s='selected';
								}
								echo"<option value='$tablo[$tk]' $s>$humanpart</option>";
								
								$jar.="\nchps['$tablo[$tk]']=new Array();\n";
								$res_field = mysql_list_fields($base,$tablo[$tk]);
								$columns = mysql_num_fields($res_field);
								for ($c = 0; $c < $columns; $c++) {
									$field_name = mysql_field_name($res_field, $c);
									if($field_name!='id' && $field_name!='clon' && $field_name!='active'){ 
										$jar.="chps['$tablo[$tk]'].push('$field_name');\n";		
									}			
								}
							}				
						}		
						
				echo"</select><br>
				<span id='zone'></span>
				<script language='javascript'>
				$jar
				
				function selzon(ki){
					cib = document.getElementById('zone');
					if(ki!=''){
						re=\"Afficher : <select name='dep'>\";
						src = chps[ki];
						for(i=0 ; i<src.length ; i++){
							if(src[i]=='$limi[3]'){
								re+=\"<option value='\"+src[i]+\"' selected>\"+src[i]+\"</option>\";
							}
							else{
								re+=\"<option value='\"+src[i]+\"'>\"+src[i]+\"</option>\";
							}
						}
						re+=\"</select>\";
						cib.innerHTML=re;
					}
					else{
						cib.innerHTML=\"<input type='hidden' name='depend' value=''>\";
					}
				}
				
				selzon('$limi[1]');
				</script>
					</td></tr><tr>
					<td valign='top' class='buttontd' style='text-align:left'>
					<b>Droits</b><hr>
					Les utilisateur aurons :
					<br>le droit d'activation :
					<select name='da'>
					<option value='0'>$bool[0]</option>
					<option value='1'>$bool[1]</option>
					</select>
					<script language='javascript'>
					document.artos.da.value=$m_da;
					</script>
					";
				if(in_array('site:gestion_articles',$allparts)){
					echo"<br>
					Le droit d'écrite sur la boutique :
					<select name='dgw'>
					<option value='0'>$bool[0]</option>
					<option value='1'>$bool[1]</option>
					</select>
					<script language='javascript'>
					document.artos.dgw.value=$m_dgw;
					</script>
					";
				}
				echo"
					</td>
				</tr><tr><td colspan='3'>
			<input type='submit' value=\"Enregistrer\" class='buttontd'>
			</td></tr>";
		
		
		}
		else{
			echo"<form action='./?option=$option&part=$part&updategroupe=$id' method='post' name='artos'>
			<table cellspacing='0' cellpadding='5' class='cadrebas' width='90%'>
			<tr><td class='buttontd'>v</td>";
			for($m=0 ; $m<sizeof($menuitemg) ; $m++){
				$thisitem = $menuitemg[$m]; 
				if( $thisitem == $order){
					echo"<td class='menuselected'>$menutranslateg[$thisitem]</td>";
				}
				else{
					echo"<td class='buttontd'><a href='./?option=$option&part=$part&order=$thisitem'>$menutranslateg[$thisitem]</a></td>";
				}
			}	
	
			echo"	
			</tr>
			<tr><td colspan='7' class='fondclair'><br>
		<a class='buttontd' href='./?option=$option&part=$part&edit'>Nouveau</a><br>
		</td></tr>
			<tr><td colspan='7' class='fondclair'></td></tr>";
			$res = mysql_query("SELECT * FROM `adeli_groupe` WHERE `nom`!='' ORDER BY `$order`");
			$bgtd=1;
			while($ro = mysql_fetch_object($res)){
				$m_id = $ro->id;
				$m_nom = $ro->nom;
				$m_droits = trim($ro->droits);
				$m_limite = trim($ro->depend);
				$m_da = trim($ro->da);
				$m_dgw = trim($ro->dgw);
				$resn = mysql_query("SELECT * FROM `adeli_users` WHERE `g`='$m_id'");
				$m_nombre = mysql_num_rows($resn);
				
				
				if($m_droits == ""){
					$m_droits = $touslesdoits;
				}
				elseif(ereg(",",$m_droits)){
					$mm_droits_keys = explode(",",$m_droits); 
					$m_droits=array();
					for($m=0 ; $m<sizeof($mm_droits_keys) ; $m++){
						$thisdroit = trim($mm_droits_keys[$m]);
						if(ereg("<",$thisdroit)){
							$optthisd = substr($thisdroit,strpos($thisdroit,"<")+1,strlen($thisdroit)-(strpos($thisdroit,"<")+2));
							$thisdroit = substr($thisdroit,0,strpos($thisdroit,"<"));
							$m_droits[$thisdroit]=$optthisd;
						}
						else{
							$m_droits[$thisdroit]='';
						}
					}
				}
				
				if($bgtd == 1){
					$bgtd=2;
					echo"<tr class='listone'>";
			   }
			   else{
					$bgtd=1;
					echo"<tr class='listtwo'>";
			   }
	
				//$m_droits = array('.$m_droits.');			
				$m_droitsk = array_keys($m_droits);			
				$tous_droits = $optico;			
				$tous_droitsk = array_keys($tous_droits);
				
				
				if(substr_count($m_limite,":")==3){
					$limi = explode(":",$m_limite);
					$m_limite="<img src='$style_url/img/$limi[0].png' alt=\"$limi[0]\"> $limi[1]";
				}
				
	
				$mes_droits="";
				$allparts=array();
				for($m=0 ; $m<sizeof($m_droits) ; $m++){
					$genredroit = $m_droitsk[$m];
					$mes_droits.="<a class='info'><img src='$style_url/img/$genredroit.png' height='20' alt=\"$genredroit\"><span>";
					if($m_droits[$genredroit] != ""){
						$mes_droits.="";
						$thsregd = explode(";",$m_droits[$genredroit]);
						for($d=0 ; $d<sizeof($thsregd) ;$d++){
							$mes_droits.=" $thsregd[$d] ";
							array_push($allparts,$thsregd[$d]);
						}
						$mes_droits.="";					
					}
					else{
						$mes_droits.="Tout";
					}
					$mes_droits.="</span></a>";	
				}
				$gbd='';
				if(in_array('gestion_articles',$allparts)){
					$gbd = '<br>Boutique : '.$readwrite[$m_dgw];
				}
				echo"
					<td></td>
					<td><a href='./?option=$option&part=$part&edit=$m_id'><b>$m_nom</b></a></td>
					<td align='center'>$m_nombre</td>
					<td>$mes_droits</td>					
					<td>$m_limite</td>										
					<td>$ouinon[$m_da] $gbd</td>
				</tr>
				
				
				<tr><td colspan='7'><img src='$style_url/$theme/g.gif' alt='_' height='1' width='100%'></td></tr>";
			}
			echo"<tr><td colspan='7' class='fondclair'><br>
		<a class='buttontd' href='./?option=$option&part=$part&edit'>Nouveau</a><br>
		</td></tr>";	
		}			
		echo"
		
		</table>
		</form>";
}
//////////////////////////////////////////////////////////////////////////////////////////////////////// USERS
elseif($part == "utilisateurs"){
	if(!isset($_GET['nouveau'])){
		$id = $_GET["id"];
		echo"<form action='./?option=$option&part=$part&updateuser=$id' method='post' name='artos'>
		<table cellspacing='0' cellpadding='2' class='cadrebas' width='90%'>
		<tr><td class='buttontd'>v</td>";
		for($m=0 ; $m<sizeof($menuitem) ; $m++){
			$thisitem = $menuitem[$m]; 
			if( $thisitem == $order){
				echo"<td class='menuselected'>$menutranslate[$thisitem]</td>";
			}
			else{
				echo"<td class='buttontd'><a href='./?option=$option&part=$part&order=$thisitem'>$menutranslate[$thisitem]</a></td>";
			}
		}	
		
		echo"	
		<td class='buttontd'>v</td></tr>
		<tr><td colspan='9' class='fondclair'>
		<a class='buttontd' href='./?option=$option&part=$part&nouveau'>Nouveau</a><br>
		</td></tr>";
		$res = mysql_query("SELECT * FROM `adeli_users`ORDER BY '$order'");
		$nbres = mysql_num_rows($res);
		$bgtd=1;
			// id   	  ref   	  login   	  pass   	  last   	  email   	  droits
		while($ro = mysql_fetch_object($res)){
			$m_id = $ro->id;
			$m_login = $ro->login;
			$m_nom = $ro->nom;
			$m_pass = $ro->pass;
			$m_last = $ro->last;			
			$m_dep = $ro->d;
			$m_last = substr($m_last,8,2)."/".substr($m_last,5,2)."/".substr($m_last,0,4).' '.substr($m_last,11,5);
			$m_email = $ro->email;
			$m_g = $ro->g;
			if($m_g == 0){
				$ms_g="Super administrateur";
				$ms_d = '';
			}
			else{
				$resn = mysql_query("SELECT * FROM `adeli_groupe` WHERE `id`='$m_g'");
				$ron = mysql_fetch_object($resn);
				$ms_g = $ron->nom;
				$ms_d = $ron->depend;
			}
			if($ms_g==''){
				$ms_g = 'Droits personnalisés';
			}
			
			
			if($bgtd == 1){
				$bgtd=2;
				echo"<tr class='listone' ondblclick=\"javascript:document.location='./?option=$option&part=$part&id=$m_id'\">";
		   }
		   else{
				$bgtd=1;
				echo"<tr class='listtwo' ondblclick=\"javascript:document.location='./?option=$option&part=$part&id=$m_id'\">";
		   }
	
			if((isset($_GET['id']))&&($_GET['id'] == $m_id)){
				echo"
					<td><a href='./?option=$option&part=$part#is$m_id' name='is$m_id'><input type='hidden' name='id' value='$id'><input type='hidden' name='clid' value='$clid'><img src='$style_url/$theme/arrow-edit.gif' border='none' alt='annuler'></a>					
					</td>
					<td>
						<input type='text' name='nom' value='$m_nom' style='width:80px;font-size:10px;'>
					</td>
					<td>
						<input type='text' name='login' value='$m_login' style='width:80px;font-size:10px;'><br>
						<input type='password' name='pass' value='' style='width:80px;font-size:10px;'>
					</td>
					<td>$m_last</td>
					<td>
						<input type='text' name='email' value='$m_email' style='width:160px;font-size:10px;'>
					</td>
					<td>
					<select name='g'><option value=''>Super administrateur</option>";
					$dp=false;
					$resn = mysql_query("SELECT * FROM `adeli_groupe`");
					while($ron = mysql_fetch_object($resn)){
						$mss_g = $ron->nom;
						$ms_id = $ron->id;
						if($mss_g!= '' || $ms_id == $m_g){
							$s="";
							if($ms_id == $m_g){
								$s="selected";
							}
							if($mss_g == ''){
								$mss_g="Droits personnalisés";
								$dp=true;
							}
							echo"<option value='$ms_id' $s>$mss_g</option>";
						}
					}
					
					echo"</select><a style='cursor:pointer' onclick=\"document.location='./?option=$option&part=groupes&edit='+document.artos.g.value\">&Eacute;diter les droits</a>	
					</td>					
					<td>";
					if(substr_count($ms_d,":")==3){
						$li = split(":",$ms_d);
						echo"<select name='d'>";
						$resn = mysql_query("SELECT `$li[2]`,`$li[3]` FROM `$li[1]` ORDER BY `$li[3]`");
						while($ron = mysql_fetch_object($resn)){
							$ms_k = $ron->$li[2];
							$ms_v = $ron->$li[3];
							$s="";
							if($ms_k == $m_dep){
								$s="selected";
							}
							echo"
							<!-- 
							$ms_k = $li[2]
							$ms_v = $li[3]
							 -->
							<option value='$ms_k' $s>$ms_v</option>";
						}
						echo"</select>";
						
					}
					else{
						echo"<input type='hidden' name='d' value=''>";
					}
					
					echo"</td>
					<td><input type='image' src='$style_url/lalie/contact-rec.gif' border='none' alt='valider' style='background-color:$bcouleur'></td>
				</tr>";
			}
			else{
					if(substr_count($ms_d,":")==3){
						if($m_dep==0 || $m_dep==''){
							$m_dep = "<a href='./?option=$option&part=$part&id=$m_id#is$m_id' name='is$m_id'><u>définir</u></a>";
						}
						else{
							$li = split(":",$ms_d);
							$resn = mysql_query("SELECT `$li[3]` FROM `$li[1]` WHERE `$li[2]`='$m_dep'");
							$ron = mysql_fetch_object($resn);
							$m_dep = "#".$ron->$li[3];
						}
					}
					else{
						$m_dep='';
					}
				echo"
					<td><a href='./?option=$option&part=$part&id=$m_id#is$m_id' name='is$m_id'><img src='$style_url/$theme/arrow-edi.gif' border='none' alt='éditer'></a></td>
					<td><b>$m_nom</b></td>
					<td>$m_login</td>
					<td>$m_last</td>
					<td>$m_email</td>
					<td><a href='./?option=groupe&part=groupes&edit=$m_g'>$ms_g</a></td>
					<td>$m_dep</td>
					<td>";
					if($nbres>1){
						echo"<a href='./?option=$option&part=$part&deluser=$m_id#is$m_id' name='is$m_id'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
					}
					echo"</td>
				</tr>";
			}
			echo"
			<tr><td colspan='9'><img src='$style_url/$theme/g.gif' alt='_' height='1' width='100%'></td></tr>";
		}				
		echo"</table>
		</form>";
	
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////// NOUVEAU
	else{
	echo"
	<form action='./?option=$option&part=$part&adduser' method='post' name='artos'>
		<table cellspacing='0' cellpadding='2' class='cadrebas' width='90%'>
		<tr><td class='buttontd'>Nouveau contact</td></tr>		
		<tr><td>
		
		<table>
			<tr><td>Nom</td><td><input type='text' name='nom' value='' style='width:240px;font-size:10px;'></td></tr>
			<tr><td>Login</td><td><input type='text' name='login' value='' style='width:240px;font-size:10px;'></td></tr>
			<tr><td>Mot de passe</td><td><input type='text' name='pass' value='' style='width:240px;font-size:10px;'></td></tr>
			<tr><td>Email</td><td><input type='text' name='email' value='' style='width:240px;font-size:10px;'></td></tr>
			<tr><td>Droits</td><td><select name='g'><option value=''>Super administrateur</option>";
					$resn = mysql_query("SELECT * FROM `adeli_groupe`");
					while($ron = mysql_fetch_object($resn)){
						$mss_g = $ron->nom;
						if($mss_g!=''){
						$ms_id = $ron->id;
						$s="";
						if($ms_id == $m_g){
							$s="selected";
						}
						echo"<option value='$ms_id' $s>$mss_g</option>";
						}
					}
					
					echo"<option value=''>Configuration personnalisée</option></select><a style='cursor:pointer' onclick=\"document.location='./?option=$option&part=groupes&edit='+document.artos.g.value\">&Eacute;diter les droits</a>
					</td></tr>
		
		 <tr><td colspan='2' align='right'>				
				<input class=\"buttontd\" type=\"reset\" value=\"Rétablir\">
				<input class=\"buttontd\" type=\"submit\" value=\"Enregistrer\">
			</td></tr>
		
		
		</table>
		
		</td></tr>
		</table>
	</form>
	";
}

}



}	
else{
		echo"<table cellspacing='0' cellpadding='5' border='0' class='cadrebas' width='90%'>
   <tr style='height:20px'><td class='buttontd'><b>Accueil Groupe</b></td></tr>
   <tr><td class='cadrebas'>
  
  <a href='./?option=$option&part=groupes'>Voir les groupes d'utilisateurs</a>
  <br>
  <a href='./?option=$option&part=groupes&edit'>Nouveau groupe</a>
  <br><br>
  <a href='./?option=$option&part=utilisateurs'>Gérer les utilisateurs</a>
  <br>
  <a href='./?option=$option&part=utilisateurs&nouveau'>Ajouter un utilisateur</a>
  
  </td></tr></table>";
}

?>