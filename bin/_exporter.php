<?php // 92 > Exportation de données ;

/*
if($field_type=='date'){
				if(isset($_POST["rule_s_$field_name"])){
					$r_s = $_POST["rule_s_$field_name"];
					$r_j = $_POST["rule_j_$field_name"];
					$r_m = $_POST["rule_m_$field_name"];
					$r_a = $_POST["rule_a_$field_name"];
					$_SESSION["stat.$part.$field_name"] = "$r_s'$r_a-$r_m-$r_j'";
				}
				echo"
				<br>&nbsp;&nbsp;<font class='petittext'>Ajouter une règle</font>
				<br>&nbsp;&nbsp;
				<select name='rule_s_$field_name'>
					<option value='>'>après</option>
					<option value='<'>avant</option>
				</select>
				<select name='rule_j_$field_name'>";
					for($j=1 ; $j<=31 ; $j++){
						echo"<option>$j</option>";
					}
				echo"</select>
				<select name='rule_m_$field_name'>";
					for($j=1 ; $j<=12 ; $j++){
						echo"<option>$j</option>";
					}
				echo"</select>
				<select name='rule_a_$field_name'>";
					$rjs = mysql_query("SELECT `$field_name` FROM `$tabledb` ORDER BY `$field_name` LIMIT 0,1");
					$rjw = mysql_fetch_array($rjs);
					$rjs = mysql_query("SELECT `$field_name` FROM `$tabledb` ORDER BY `$field_name`DESC LIMIT 0,1");
					$raw = mysql_fetch_array($rjs);
					$deb = abs($rjw[0]);
					if($deb==0) $deb=1900;
					$fin = abs($raw[0]);
					if($fin==0) $fin=date('Y');
					for($j=$fin ; $j>=$deb ; $j--){
						echo"<option>$j</option>";
					}
				echo"</select>
				<input type='submit' value='ok' class='buttontd'> 
				";
			}
		*/
	insert("output");
	insert("outdown");
	insert('_compta_pdf');
	if(is_file('bin/_compta_pdf.php')){
		$openexpo='./?incpath=bin/';
	}
	else{
		$openexpo='$style_url/update.php?file=';
		//include("$style_url/update.php?file=$incfich.php");
	}
	if(isset($_GET["incwhere"]) && $_GET["incwhere"]!=''){
		$incwhere = urldecode($_GET["incwhere"]);
	}
	$lnc="";
	 $wheredb="WHERE `clon`='0'";
	  if($incwhere !== null){
		$wheredb = $incwhere;
		$lnc="&incwhere=".urlencode($incwhere)."&";
	  }
	   if(isset($_GET["filt"]) && isset($_GET["filtv"])){
	  	$filt=stripslashes($_GET["filt"]);
		$filtv=stripslashes($_GET["filtv"]);
	  	$wheredb = " WHERE `$filt`='$filtv' ";
		$lnc="&filt=$filt&filtv=$filtv";
		if(isset($_GET["solo"])){
			$lnc.="&solo=".stripslashes($_GET["solo"]);
		}
	  }
	  if(isset($_GET["selected"])){
	  	$selected=explode("sel",$_GET["selected"]);
		$wheredb .= "AND ( `id`='0". implode("' OR `id`='",$selected) ."' )";
		$lnc="&selected=".$_GET["selected"];
	  }
	  $conn = connecte($base, $host, $login, $passe);
		if(isset($u_restreint) && $u_restreint[1]==$part){
			$wheredb.=" AND `$u_restreint[2]`='$u_d'";
		}
		if(isset($u_restreint) && $u_restreint[1]!=$part){
		for ($i = 0; $i < $columns; $i++) {
			$field_name = mysql_field_name($res_field, $i);
			$field_act = $field_name;
			if(isset($r_alias[$part][$field_name])){
				$field_act = $r_alias[$part][$field_name];
			}
			if($field_act == "$u_restreint[1]_$u_restreint[2]_$u_restreint[3]"){
				$wheredb.=" AND `$field_name`='$u_d'";
			}
		}	
		}
	  
	  $result = mysql_query("SELECT `id` FROM `$tabledb` $wheredb");
	  $totro = mysql_num_rows($result);	
	  mysql_close($conn);	
	if(!isset($_POST["frm"])){
		echo"<b>$totro lignes</b> seront exportées.
		<br><br>
		<form action='./?option=$option&part=$part&subpart=exporter$lnc' method='post'>
		<table class='buttontd' width='500'>
		<tr><td width='150'><b>Options d'exportation:</b></td><td>&nbsp;</td></tr>
		<tr><td class='cadrebas' colspan='2' style='padding:5px'>
		
		<p align='left'>
		Format :
		<blockquote style='text-align:left'>
		<input type='radio' name='frm' value='csv' checked> Csv <span class='petittext'>(compatible Microsoft Excell)</span><br>";
		if($tabledb=="gestion_articles"){
			echo"<input type='radio' name='frm' value='pdf' disabled> Pdf <span class='petittext'>(pour étiquetage)</span><br>";
		}
		elseif($tabledb!="gestion_rayons"){
			echo"<input type='radio' name='frm' value='sql'> Sql <span class='petittext'>(pour internet)</span><br>";
		}
		echo"<input type='radio' name='frm' value='txt'> Txt <span class='petittext'>(texte brut)</span><br>		
		<input type='radio' name='frm' value='log'> Log <span class='petittext'>(texte brut sans ponctuation)</span><br>		
		<input type='radio' name='frm' value='xml'> Xml <span class='petittext'>(base de données)</span><br>
		<input type='radio' name='frm' value='html'> Html <span class='petittext'>(prêt à imprimer)</span>
		</blockquote>
		</p>
		<hr>
		<p align='left'>
		";
		///////////////////////////////////////////// GESTION
		if($tabledb!="gestion_articles" && $tabledb!="gestion_rayons"){	
				echo"
				Exporter :
				<blockquote style='text-align:left'>
				
				";
				for ($i = 0; $i < $columns; $i++) {
					$fieldo_name =$field_name = mysql_field_name($res_field, $i);
					if(!isset($_GET["solo"]) || $field_name==stripslashes($_GET["solo"])){
						if(isset($alias[$part][$field_name])) $fieldo_name = $alias[$part][$field_name];
						echo"<input type='checkbox' name='col_$field_name' value='1' checked>&nbsp;$fieldo_name<br>";	
					}
				}
				echo"
				</blockquote>
				</p>
				<hr>
				<p align='left'>
				Options suplémentaires
				
				<blockquote style='text-align:left'>
				Classer par : <select name='ordre'>
				";
				for ($i = 0; $i < $columns; $i++) {
					$fieldo_name =$field_name = mysql_field_name($res_field, $i);
					if(!isset($_GET["solo"]) || $field_name==stripslashes($_GET["solo"])){
						if(isset($alias[$part][$field_name])) $fieldo_name = $alias[$part][$field_name];
						echo"<option value='$field_name'>$fieldo_name</option>";	
					}
				}
				echo"</select><br>
				<input type='checkbox' name='nioi' value='1' checked>&nbsp;Exporter des noms plutôt que des identifiants
				</blockquote>
				</p>
				";
		}
		if($tabledb=="gestion_articles"){
			echo"
			<p align='left'>
				Options suplémentaires
				
				<blockquote style='text-align:left'>
				
				";
				 $conn = connecte($base, $host, $login, $passe);
				$ris = mysql_query("SELECT * FROM `$langue_db`");
				if($ris && mysql_num_rows($ris)>0){
					echo"Choix de la langue : <select name='langue'>";
					while($riw=mysql_fetch_object($ris)){
						$lng_code = $riw->code;
						$lng_nom = $riw->nom;
						echo"<option value='$lng_code'>$lng_nom</option>";	
					}
					echo"</select><br>";
				}
				mysql_close($conn);	
				echo"
				<input type='checkbox' name='exalias' value='1' checked>&nbsp;Exporter également les alias 
				</blockquote>
				</p>
				";
		}
		echo"
		
		</p>
		<p align='right'>
		<input type='submit' value='exporter' class='buttontd'>
		</p>
		</td></tr></table>
		</form>";
	}
	////////////////////////////////////////// SITE
	else{
		$frm = $_POST["frm"];
		$ordre = $_POST["ordre"];
		$_SESSION["outpufi"]="$part$now.$frm";
		echo"exportation au format $frm (".$_SESSION["outpufi"].")
		<br>
		<a href='./?option=$optionpart=$part&subpart=exporter$lnc'><b>retour au choix de format</b></a>
		
		<hr>
		<table class='buttontd' style='width:100%;height:320px'><tr><td align='left'>
		<a href='$openexpo"."outdown.php' target='expo' class='buttontd'>enregistrer</a>
		<a href='#' onclick=\"poprint=window.open('$openexpo"."output.php&print','poprint','width=600,height=600, scrollbars=1, resizable=1');poprint.print()\" class='buttontd'>imprimer</a>
		<br>
		";
		function func_html($str){
			if(preg_match("<!>",$str) && preg_match("<>",$str)){
				$str='<table><tr><td>'.str_replace('<!>','</td></tr><tr><td>',str_replace('<>','</td><td>',nl2br($str))).'</td></tr></table>';
			}
			else if(strlen($str)==13 && is_numeric($str)){
				$str="<img src='bin/_ean.php?1&ean=$str' alt='$str'>";
			}
			return $str;
		}
		function func_log($str){
			$str =  ereg_replace("[,;]","",$str);
			$taxt = split("[ \n]",$str);
			$str="";
			$nbt = sizeof($taxt);
			for($i=0 ; $i<$nbt ; $i++){
			  $str.= trim($taxt[$i])." ";
			}
			return trim($str);
		}
		function func_xml($str){
			if(!is_numeric($str)){
				if(preg_match("<!>",$str) && preg_match("<>",$str)){
				$str='<contenu><ligne><valeur><![CDATA['.str_replace('<!>','</valeur></ligne><ligne><valeur>',str_replace('<>','</valeur><valeur>',$str)).']]></valeur></ligne></contenu>';
				}
				else{
					$str = "<![CDATA[$str]]>";
				}
			}
			return $str;
		}
		function func_sql($str){
			return ereg_replace("'","''",$str);
		}
		function func_txt($str){
			return $str;
		}
		function func_csv($str){
			//$str =  ereg_replace("[,;]"," - ",$str);
			$taxt = split("[ \n]",$str);
			$str="";
			$nbt = sizeof($taxt);
			for($i=0 ; $i<$nbt ; $i++){
			  $str.= trim($taxt[$i])." ";
			}
			return trim($str);
		}
		switch($frm){
			case "html": 
					$str_de="<table bgcolor='#999999' cellspacing='1' cellpadding='3'>"; 
					$str_st="
					<tr bgcolor='#FFFFFF'><td><font face='arial'>"; 
					$str_mi="</font></td><td><font face='arial'>";  
					$str_fi="</font></td></tr>
					";  
					$str_en="</table>"; 
					$_SESSION["outpumim"]="text/html";
					break;
			case "xml": 
					$str_de="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<$part>
 <columns>"; 
					$str_st="\n<item>"; 
					$str_mi="</item>\n\t<item>";  
					$str_fi="</item>\n";  
					$str_en="
</$part>"; 
					$_SESSION["outpumim"]="text/xml";
					break;
			case "sql": 	
					$str_de="# TABLE $tabledb\n#"; 
					$str_st="INSERT INTO `$tabledb` VALUES('"; 
					$str_mi="','";  
					$str_fi="');\n";  
					$str_en="# END OF TABLE"; 
					$_SESSION["outpumim"]="text/plain";
					break;
			case "txt": 
					$str_de="-------------------------------------------------------------------"; 
					$str_st="\n"; 
					$str_mi=" | ";  
					$str_fi="\n-------------------------------------------------------------------\n";  
					$str_en=""; 
					$_SESSION["outpumim"]="text/plain";
					break;
			case "log": 
					$str_de=""; 
					$str_st=""; 
					$str_mi=" ";  
					$str_fi="\n";  
					$str_en=""; 
					$_SESSION["outpumim"]="text/plain";
					break;
			case "csv": 
					$str_de=""; 
					$str_st=""; 
					$str_mi=" ;";  
					$str_fi="\n";  
					$str_en="\n\n"; 
					$_SESSION["outpumim"]="text/plain";
					break;
			default: 
					$str_de=""; 
					$str_st="\n"; 
					$str_mi=",";  
					$str_fi=";\n\t";  
					$str_en=""; 
					$_SESSION["outpumim"]="text/plain";
					break;
		}
		$conn = connecte($base, $host, $login, $passe);
		$result = mysql_query("SELECT * FROM `$tabledb` $wheredb ORDER BY `$ordre`");
		$funk = "func_$frm";
		
		function itn($field,$value,$rowid,$str_mi=' '){
			global $r_alias,$tabledb,$part;
			$field_act = $field;
			if(isset($r_alias[$part][$field])){
				$field_act = $r_alias[$part][$field];
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
			
			if( ereg("_",$field_act) && mysql_query("SHOW COLUMNS FROM `$mot`") ){
				$refiled = $mot;//substr($field_name,0,strpos($field_name,"_"));				
				$nameifthefield = $refiled;
				$fieldoptions = split("[_>]",$fieldoption);
				$fieldoptionprint = $fieldoptions[1];
				if(strpos($fieldoptionprint,'/')>-1){
					$fopa = explode('/',$fieldoptionprint);	
					$fieldoptionprint="CONCAT(''";
					foreach($fopa as $fopv){
						$fieldoptionprint.=",'<>',`$fopv`";
					}
					$fieldoptionprint.=")";
				}
				$fieldoption = $fieldoptions[0];		
				$refiled = trim($refiled);	
				if((sizeof($fieldoptions)==3 && $fieldoptions[2]=='ch') || (sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlch')){
					if(sizeof($fieldoptions)==3){
						$nval='';
					$listres = mysql_query("SELECT $fieldoptionprint,`$fieldoption`,`id` FROM `$refiled` ORDER BY 1");
						while($rowlist = mysql_fetch_array($listres)){
							$rowvalue = $rowlist[0];
							$rowid = $rowlist[1];
							$roid = $rowlist[2];
							if(ereg('<'.$rowid.'>',$value)){
								$nval.="- ".str_replace($str_mi,'',$rowvalue)."\n";
							}							
						}
						$value=$nval;
					} 
					if(sizeof($fieldoptions)==2){
						$value =  str_replace('><','<br>','>'.$value.'<');	
					} 
					 
				 }
				 elseif(sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlse'){
					  $value = $value;
				 }
				 else{
					$listres = mysql_query("SELECT $fieldoptionprint  FROM `$refiled` WHERE `$fieldoption`='$value' ORDER BY 1");
					$rowlist = mysql_fetch_array($listres);
					if(substr($rowlist[0],0,2)=='<>'){
						$value = str_replace('<>',$str_mi,str_replace($str_mi,'',substr($rowlist[0],2)));
					}
					else{
						$value = $rowlist[0];
					}
					
				}
			}
			return $value;
		}
		
		/////////////////////////////////////////////////////: GESTION ARTICLES		
		if($tabledb=="gestion_articles"){	
				if($frm=='xml') $str_de="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?><$part>"; 
				$_SESSION["outputxt"]=$str_de;
				$columns=array("categorie","identifiant_unique","titre","prix","prix_pro","url_produit","image","description","frais_de_livraison","prix_barre","prix_barre_pro","reference_modele","code_barre","devise");
				
				$ismark=false;
				$verifupdt = mysql_query("DESC `$tabledb`");
				$allchamps = array();
				while($ro = mysql_fetch_object($verifupdt)){
					array_push($allchamps,$ro->Field);
				}
				if(in_array("marque",$allchamps)){
					array_unshift ($columns, "marque");
						$ismark=true;
				}
			if($frm!="xml"){
				$_SESSION["outputxt"].=$str_st;		
					for ($i=0 ; $i<sizeof($columns) ; $i++) {
						$_SESSION["outputxt"].=$columns[$i].$str_mi;	
					}
				$_SESSION["outputxt"].=$str_fi;	
			}
			$lang = $_POST['langue'];
			function rayonentier($r,$str){
				global $lang;
				$rus = mysql_query("SELECT `nom` FROM `gestion_raytrad` WHERE `lng`='$lang' AND `ref`='$r'");
				$ruw = mysql_fetch_array($rus);
				$str="$ruw[0]/$str";
				
				$ris = mysql_query("SELECT `ref` FROM `gestion_rayons` WHERE `id`='$r'");
				$riw = mysql_fetch_array($ris);
				if($riw[0]!=0){
					$str = rayonentier($riw[0],$str);	
				}
				return $str;
			}
			function marquentier($r){
				$rus = mysql_query("SELECT `nom` FROM `marques` WHERE `id`='$r'");
				$ruw = mysql_fetch_array($rus);
				return $ruw[0];
			}
			
			$p=0;
			$suprech='';
			if(isset($_POST['exalias'])) $suprech='AND `clon`=0';
			$ism='';
			if($ismark) $ism=",`marque`";
			$result = mysql_query("SELECT `id`,`rayon` $ism FROM `gestion_articles` WHERE 1 $suprech");
			while($ro = mysql_fetch_array($result)){
			$p++;
				$id = $ro[0];
				$categorie = rayonentier($ro[1],'');
				if($ismark) $marque=marquentier($ro[2]);
				$rus = mysql_query("SELECT * FROM `gestion_artrad` WHERE `lng`='$lang' AND `ref`='$id'");
				$ruw = mysql_fetch_object($rus);
				$art_nom = $ruw->nom;
				$art_desc = $ruw->desc;
				$rus = mysql_query("SELECT * FROM `gestion_artstock` WHERE `ref`='$id'");
				$ruw = mysql_fetch_object($rus);
				$art_code = $ruw->code;
				$art_prix = $ruw->prix;
				$art_red = $ruw->promo;
				$art_prixpro = $ruw->prix_pro;
				$art_promopro = $ruw->promo_pro;
				$art_tva = $ruw->tva;
				$art_stock = $ruw->stock;				
				$art_ean = $ruw->ean;
				$art_red="";
				
				switch($frm){
					case "xml":	
							//"$urlserveur/produit.php?lang=fr&cat=".urlencode($categorie)."&id=$id"				
							$_SESSION["outputxt"].= "
							<product place=\"$p\">";
								if($ismark) $_SESSION["outputxt"].= "<marque>".$funk($marque)."</marque>";								
								$_SESSION["outputxt"].= "<categorie>".$funk($categorie)."</categorie>
								<identifiant_unique>$id</identifiant_unique>
								<titre>".$funk($art_nom)."</titre>
								<prix currency=\"EUR\">$art_prix</prix>
								<url_produit>".$funk("")."</url_produit>";
								if(is_file("../gestion_articles/$id.jpg")){
									$_SESSION["outputxt"].= "<url_image>".$funk("$urlserveur/gestion_articles/$id.jpg")."</url_image>";
								}
								else{
									$_SESSION["outputxt"].= "<url_image>".$funk("")."</url_image>";
								}
								$_SESSION["outputxt"].= "<description>".$funk($art_desc)."</description>
								<frais_de_livraison>$livraison</frais_de_livraison>
								<prix_barre currency=\"EUR\">$art_red</prix_barre>
								<reference_modele>$art_code</reference_modele>
								<code_barre>$art_ean</code_barre>
								<devise>EUR</devise>
							</product>";
					break;
					case "html": 
						//$_SESSION["outputxt"].= "$p\n";
							$_SESSION["outputxt"].= $str_st;
								if($ismark) $_SESSION["outputxt"].=$funk($marque).$str_mi;								
								$_SESSION["outputxt"].= $funk($categorie)."$str_mi$id$str_mi".$funk($art_nom)."$str_mi$art_prix$str_mi$art_prixpro$str_mi".$funk("")."$str_mi";
							if(is_file("../gestion_articles/$id.jpg")){
								$_SESSION["outputxt"].= "<img src='../gestion_articles/$id.jpg' alt='image' width='120'>";
							}
							
							$_SESSION["outputxt"].= "$str_mi".$funk($art_desc)."$str_mi$livraison$str_mi$art_red$str_mi$art_redpro$str_mi$art_code$str_mi".$funk($art_ean)."$str_mi"."EUR$str_mi$str_fi";
					break;
					default: 
						//$_SESSION["outputxt"].= "$p\n";
							$_SESSION["outputxt"].= $str_st;
								if($ismark) $_SESSION["outputxt"].=$funk($marque).$str_mi;								
								$_SESSION["outputxt"].= $funk($categorie)."$str_mi$id$str_mi".$funk($art_nom)."$str_mi$art_prix$str_mi$art_prixpro$str_mi".$funk("")."$str_mi".$funk("$urlserveur/gestion_articles/$id.jpg")."$str_mi".$funk($art_desc)."$str_mi$livraison$str_mi$art_red$str_mi$art_redpro$str_mi$art_code$str_mi".$funk($art_ean)."$str_mi"."EUR$str_mi$str_fi";
					break;	
				}	
			}
			$_SESSION["outputxt"].=$str_en;	
		}
				/////////////////////////////////////////////////////: GESTION RAYONS		
		elseif($tabledb=="gestion_rayons"){	
			if($frm=='xml') $str_de="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?><$part>";
				$_SESSION["outputxt"]=$str_de;
				$columns=array("identifiant","nom","image","description");
			if($frm!="xml"){
				$_SESSION["outputxt"].=$str_st;		
					for ($i=0 ; $i<sizeof($columns) ; $i++) {
						$_SESSION["outputxt"].=$columns[$i].$str_mi;	
					}
				$_SESSION["outputxt"].=$str_fi;	
			}
			
			$p=0;
			$result = mysql_query("SELECT `id` FROM `gestion_rayons`");
			while($ro = mysql_fetch_array($result)){
			$p++;
				$id = $ro[0];
				$rus = mysql_query("SELECT `nom`,`description` FROM `gestion_raytrad` WHERE `lng`='fr' AND `ref`='$id'");
				$ruw = mysql_fetch_array($rus);
				$categorie = $ruw[0];
				$desc = $ruw[1];	
				switch($frm){
					case "xml": 
						$_SESSION["outputxt"].= "$str_st<identifiant>$id</identifiant><categorie>".$funk($categorie)."</categorie><image>";
						if(is_file("../gestion_rayons/$id.jpg")){
							$_SESSION["outputxt"].= $funk("$urlserveur/gestion_rayons/$id.jpg");
						}						
						$_SESSION["outputxt"].= "</image><description>".$funk($desc)."</description>$str_fi";
						break;
					default:			
						$_SESSION["outputxt"].= $str_st."$id$str_mi".$funk($categorie)."$str_mi";
						if(is_file("../gestion_rayons/$id.jpg")){
							if($frm=='html'){
								$_SESSION["outputxt"].= "<img src='../gestion_rayons/$id.jpg' alt='image' width='120'>";
							}	
							else{
								$_SESSION["outputxt"].= $funk("$urlserveur/gestion_rayons/$id.jpg");
							}
						}						
						$_SESSION["outputxt"].= "$str_mi".$funk($desc)."$str_mi$str_fi";
				}

			}
			$_SESSION["outputxt"].=$str_en;	
		}
		/////////////////////////////////////////////////////: DEFAULT
		else{
			$_SESSION["outputxt"]=$str_de;
			$_SESSION["outputxt"].=$str_st;		
				for ($i = 0; $i < $columns; $i++) {
					$fieldo_name =$field_name = mysql_field_name($res_field, $i);
					if( (!isset($_GET["solo"]) && isset($_POST["col_$field_name"])) || $field_name==stripslashes($_GET["solo"])){
						if(isset($alias[$part][$field_name])) $fieldo_name = $alias[$part][$field_name];
						
						
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
						$fieldoptions = split("[_>]",$fieldoption);
						$fieldoptionprint = $fieldoptions[1];
						if( ereg("_",$field_act) && mysql_query("SHOW COLUMNS FROM `$mot`") && strpos($fieldoptionprint,'/')>-1 ){
							$fopa = explode('/',$fieldoptionprint);	
							foreach($fopa as $fopv){
								$_SESSION["outputxt"].=$fopv.$str_mi;
							}
						}
						else{
							$_SESSION["outputxt"].=$fieldo_name.$str_mi;
						}
					}
				}
			$_SESSION["outputxt"].=$str_fi;	
			if($frm=="xml") $_SESSION["outputxt"].="</columns>";
			while($ro = mysql_fetch_object($result)){
			
			$_SESSION["outputxt"].=$str_st;	
				
			switch($frm){
			case "xml": 
					for ($i = 0; $i < $columns; $i++) {
						$field_name = mysql_field_name($res_field, $i);
						if( (!isset($_GET["solo"]) && isset($_POST["col_$field_name"]) ) || $field_name==stripslashes($_GET["solo"])){
							$_SESSION["outputxt"].= "\n\t\t<$field_name>".$funk(itn($field_name,$ro->$field_name,0,$str_mi))."</$field_name>\n";	
						}
					}
					break;
			default: 
					for ($i = 0; $i < $columns; $i++) {
						$field_name = mysql_field_name($res_field, $i);
						if( (!isset($_GET["solo"]) && isset($_POST["col_$field_name"])) || $field_name==stripslashes($_GET["solo"])){
							$_SESSION["outputxt"].= $funk(itn($field_name,str_replace($str_mi,' ',$ro->$field_name),0,$str_mi)).$str_mi;		
						}						
					}
					break;
		}
				$_SESSION["outputxt"].=$str_fi;	
				
			}
			$_SESSION["outputxt"].=$str_en;	
		}
		mysql_close($conn);	
		$fp=fopen("tmp/outpumim","w+");
		fwrite($fp,$_SESSION["outpumim"]);
		$fp=fopen("tmp/outputxt","w+");
		fwrite($fp,$_SESSION["outputxt"]);
		$fp=fopen("tmp/outpufi","w+");
		fwrite($fp,$_SESSION["outpufi"]);
		fclose($fp);
		echo "<iframe width='100%' height='300' name='expo' src='$openexpo"."output.php'></iframe>
		</td></tr></table>
		";
	}
?>