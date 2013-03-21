<?php // 584 > Gestion des articles ;

//$u_dg
 if(!file_exists("mconfig/$u_id.ga.four.conf")){		@fopen("mconfig/$u_id.ga.four.conf","w");	} 
		  $fp = @fopen("mconfig/$u_id.ga.four.conf","r");
		 $co = abs(@fread($fp,7));
		 @fclose($fp);
		 $visus = array('none','block');
		 $vis = $visus[$co];
		if(isset($_GET['fours'])){ 
			$vis='block'; 
			$fp = fopen("../mconfig/$u_id.ga.four.conf","w+");
			fwrite($fp,1);
		}
		$visy='block';
		if($vis=='block') $visy='none';
		
		$ga_parts = array('colonel','stoc','achafour','otherplus');
		$ga_partn = array('Détails','Vente/Stock','Achats/fournisseurs','Plus');
		
		
		$liens='';
		echo"</table>
		<script language='javascript'>
		var ga_parts = new Array('".implode($ga_parts,"','")."');
		function inpar(ki){
			";
			foreach($ga_parts as $k=>$v){
				echo"
				document.getElementById('$v').style.display='none';
				document.getElementById('lien_$v').className='menuselected';
				";	
				if($k==$co){
					$liens.="<a class='buttontd' id='lien_$v' style='cursor:pointer' onclick=\"inpar($k)\">$ga_partn[$k]</a>";	
					${'vis'.$k}='block';
				}
				else{
					$liens.="<a class='menuselected' id='lien_$v' style='cursor:pointer' onclick=\"inpar($k)\">$ga_partn[$k]</a>";
					${'vis'.$k}='none';	
				}
				
			}
			echo"
			document.getElementById(ga_parts[ki]).style.display='block';
			document.getElementById('lien_'+ga_parts[ki]).className='buttontd';
			ajaxfil('gafour',ki);
		}
		</script>
		
		
				$liens";
		
		
$special_files = array(
			  "Aperçu"=>array("../gestion_articles/","$edit.jpg"),
			  "Deuxième vue"=>array("../gestion_articles/","g$edit.jpg"),
			  "Autres vues"=>array("../gestion_articles/$edit/",".dir","jpg,jpeg,gif,png"),
			  );



$legal_entrys = array(id,clon,rayon,nouveaute,conseil,modification,active);

insert('_ean');
if(is_file('bin/_ean.php')){
	$openean='bin/_ean.php?1';
}
else{
	$openean="$style_url/update.php?file=_ean.php&1";
}

	$art_rayon = $ro->rayon;
	$art_nouveaute = abs($ro->nouveaute);
	$art_conseil = $ro->conseil;
	$art_modification = $ro->modification;
	$art_active = abs($ro->active);
	if($ro->id != $edit){
		if(isset($_GET['redo'])){
			mysql_query("INSERT INTO `$articles_db` (`id`) VALUES ('$edit')");
		}
		elseif(isset($_GET['clean'])){
			mysql_query("DELETE FROM `gestion_artrad` WHERE `ref`='$edit'");
			mysql_query("DELETE FROM `gestion_artstock` WHERE `ref`='$edit'");
		}		
		else{
			echo"<hr>Article supprimé. <br>
			- <a href='./?option=$option&part=$part&edit=$edit&redo'>recréer l'article</a>
			- <a href='./?option=$option&part=$part&edit=$edit&clean'>nettoyer la base de données</a><hr>";	
		}
	}
	
	if(!is_dir("../gestion_articles/sia/")){
		mkdir("../gestion_articles/sia/",0777);
	}
		

if($edit=="" && isset($_GET["ray"])){
	$art_rayon=$_GET["ray"];
}	
if((isset($_GET['update'])||isset($_GET['refresh'])) && $_FILES['file']['name'][0] !=''){
	if(addfile("../gestion_articles/$edit.jpg", $_FILES['file']['name'][0], $_FILES['file']['tmp_name'][0], $dangerous)){
		$return.=returnn("aperçu chargé avec succès","009900",$vers,$theme);
	}
	else{
		$return.=returnn("aperçu n'a pu être chargé correctement","990000",$vers,$theme);
	}
}
if((isset($_GET['update'])||isset($_GET['refresh'])) && $_FILES['file']['name'][1] !=''){
	if(addfile("../gestion_articles/g$edit.jpg", $_FILES['file']['name'][1], $_FILES['file']['tmp_name'][1], $dangerous)){
		$return.=returnn("deuxième vue chargée avec succès","009900",$vers,$theme);
	}
	else{
		$return.=returnn("deuxième vue n'a pu être chargée correctement","990000",$vers,$theme);
	}
}

if(isset($fichiers[$part]) && (isset($_GET['update'])||isset($_GET['refresh']))){		//////////////////////////////////////////// 			CUSTOM FILES	
	$return.=returnn("chargement personnalisé","FF9900",$vers,$theme);
	$fich_temp = $fichiers["gestion_articles"];
	$fichiers["gestion_articles"]=array_merge($special_files,$fich_temp);

	$custom_files = $fichiers[$part];
	$custom_keys = array_keys($custom_files);
	$i=0;
	while($i<sizeof($custom_keys)){
		$r=$i;
		$custom_name = $custom_keys[$i];
		$custom_dir = $custom_files[$custom_name][0];
		$custom_file = $custom_files[$custom_name][1];
		if($_FILES['file']['name'][$r] !=''){
			if(addfile($custom_dir."/".$custom_file, $_FILES['file']['name'][$r], $_FILES['file']['tmp_name'][$r], $dangerous)){
				$return.=returnn($custom_name." chargé avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn($custom_name." n'a pu être chargé correctement","990000",$vers,$theme);
			}
		}
		$i++;
	}
	$fichiers["gestion_articles"] = $fich_temp;
}

		$actouno = array("","checked");
		$actoudos = array("checked","");
		//`id`, `ref`, `lng`, `nom`, `desc`, `plus1`, `plus2`
	  echo"
	  <style>
	  .gaf_prix{
		width:50px;
		font-size:12px;
		font-weight:bold;
		color:#000;
		border:#999 2px inset;
		text-align:right;
	  }
	  .gaf_txt, .outilpromo input, .gaf_txt option{
		width:32px;
		font-size:10px;
		color:#000;
		border:#999 1px inset;
		text-align:center;
	  }
	  .gaf_ro{
		width:30px;
		font-size:10px;
		color:#333;
		border:none;
		text-align:right;
	  }
	  .gaf_table td{
		font-size:10px;
		white-space:nowrap;
	  }
	  .gaf_table td b{
		font-size:11px;  
	  }
	  .outilpromo{
		position:relative;  
	  }
	  .outilpromo table{
		position:absolute; 
		left:-370px;
		width:450px;
		top:-30px;
		visibility:hidden;
		background:#FFF;
	  }
	  .outilpromo td{
		padding:5px;
	  }
	  </style>
	  <script language='javascript' type='text/javascript'>
	 	function addinref(koi,addf,notforall){
			var fa='forall';
			if(notforall) fa='';
			if(!addf) addf = prompt(\"Veuillez saisir le nom de votre nouvelle \"+koi+\"\\ndans le champs ci-dessous\",'');
			if(addf){
				document.fourmis.action='./?$part&edit=$edit&update&'+fa+'&addref='+koi+'&val='+addf+'#_'+addf;
				document.fourmis.submit();
			}
		}
		function ch_achat(li,val,ref){
			val = parseFloat(val);
			eval('commi = parseFloat(document.fourmis.com'+ref+'.value)');
			eval('remf = parseFloat(document.fourmis.remisepa_'+li+'.value)');
			val-= Math.round((val*remf/100)*100)/100;
			eval('document.fourmis.apach'+li+'.value=\"'+val+'\";'); 
			eval('ventpar = parseFloat(document.fourmis.ventepa_'+ref+'.value);');
			eval('ventpro = parseFloat(document.fourmis.ventepro_'+ref+'.value);');
			
			eval('tcom = parseFloat(document.fourmis.comw'+ref+'.value)');
			if(tcom==0){
				ventpro-=Math.round((ventpro*commi/100)*100)/100;
				ventpar-=Math.round((ventpar*commi/100)*100)/100;
			}
			if(tcom==1){
				ventpro-=Math.round(((ventpro-val)*commi/100)*100)/100;
				ventpar-=Math.round(((ventpar-val)*commi/100)*100)/100;
			}
			//eval('document.fourmis.margepa_'+li+'.value=\"'+(ventpar-val)+'\";'); 
			//eval('document.fourmis.coefpa_'+li+'.value=\"'+(Math.round(ventpar/val*100)/100)+'\";'); 
			//eval('document.fourmis.margepro_'+li+'.value=\"'+(ventpro-val)+'\";'); 
			//eval('document.fourmis.coefpro_'+li+'.value=\"'+(Math.round(ventpro/val*100)/100)+'\";'); 
			
		}
		function ch_marge(li,co,val,ref){
			eval('acha = document.fourmis.prixpa_'+li+'.value;');
			eval('remf = parseFloat(document.fourmis.remisepa_'+li+'.value)');
			eval('commi = parseFloat(document.fourmis.com'+ref+'.value)');
			acha = parseFloat(acha);			
			
			eval('tcom = parseFloat(document.fourmis.comw'+ref+'.value)');
			if(tcom==0){
				
			}
			if(tcom==1){
				//alert(\"ce calcul n'a pas encore été implémenté\");
			}
			
			eval('marg = parseFloat(document.fourmis.marge'+co+'_'+li+'.value);');									
			ven = parseFloat(acha)+parseFloat(marg);			
			ven= Math.round(((acha+marg)/(1-(commi/100)))*100)/100;
			eval('document.fourmis.vente'+co+'_'+ref+'.value=\"'+ven+'\";');
			ven-=Math.round((ven*commi/100)*100)/100;
			eval('document.fourmis.coef'+co+'_'+li+'.value=\"'+(Math.round(ven/acha*100)/100)+'\";');
			if(co=='pa') co='';
			if(co=='pro') co='_pro';
			eval('document.fourmis.prix'+co+ref+'.value=\"'+ven+'\";');	
		}
		function ch_coef(li,co,val,ref){
			eval('acha = parseFloat(document.fourmis.prixpa_'+li+'.value);');
			eval('remf = parseFloat(document.fourmis.remisepa_'+li+'.value)');
			acha-= Math.round((acha*remf/100)*100)/100;
			eval('tcom = parseFloat(document.fourmis.comw'+ref+'.value)');
			if(tcom==0){
				
			}
			if(tcom==1){
				//alert(\"ce calcul n'a pas encore été implémenté\");
			}
			eval('coef = parseFloat(document.fourmis.coef'+co+'_'+li+'.value);');
			eval('commi = parseFloat(document.fourmis.com'+ref+'.value)');
			ven= Math.round(((acha*coef)/(1-(commi/100)))*100)/100;
			eval('document.fourmis.vente'+co+'_'+ref+'.value=\"'+ven+'\";');
			eval('document.fourmis.marge'+co+'_'+li+'.value=\"'+(Math.round(((ven-(ven*commi/100))-acha)*100)/100)+'\";');
			if(co=='pa') co='';
			if(co=='pro') co='_pro';
			eval('document.fourmis.prix'+co+ref+'.value=\"'+ven+'\";');	
		}
		function ch_vente(ref,co,val){
			eval('document.fourmis.vente'+co+'_'+ref+'.value=\"'+val+'\";');
		}
	 	function defi(type,li,ref){
			orf(type,li,ref);
			ex='';
			if(type=='pro') ex='_pro';
			eval('document.fourmis.prix'+ex+ref+'.value=\"'+np+'\";');
			eval('document.fourmis.vente'+type+'_'+ref+'.value=\"'+np+'\";');
		}
		function prom(type,li,ref){
			orf(type,li,ref);
			ex='';
			if(type=='pro') ex='_pro';
			eval('document.fourmis.promo'+ex+ref+'.value=\"'+pvb+'\";');
			eval('document.fourmis.prix'+ex+ref+'.value=\"'+np+'\";');				
			eval('document.fourmis.vente'+type+'_'+ref+'.value=\"'+np+'\";');
		}
		function orf(type,li,ref){
			eval('pvb = document.fourmis.vente'+type+'_'+ref+'.value;');
			eval('pco = document.fourmis.or'+type+li+'.value;');
			eval('pac = document.fourmis.apach'+li+'.value;');
			eval('commi = parseFloat(document.fourmis.com'+ref+'.value)');
			pvb = parseFloat(pvb);
			pco = parseFloat(pco);
			pac = parseFloat(pac);

			np = Math.round((pvb-(pvb*pco/100))*100)/100;
			mv = Math.round(np*commi)/100;
			mb = Math.round((np-pac-mv)*100)/100;
			document.getElementById('or'+type+'c'+li).innerHTML=np;
			document.getElementById('or'+type+'v'+li).innerHTML=mv;
			document.getElementById('or'+type+'m'+li).innerHTML=mb;
		}
	 
	 
		   function confsupt(id,foc){
			is_confirmed = confirm('êtes vous sûr de vouloir supprimer définitivement cette ligne ?');
			if (is_confirmed) {
				document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&delref='+id+'#f'+foc;
				document.fourmis.submit();
			}
		   }
		   function confsupa(id,foc){
			is_confirmed = confirm('êtes vous sûr de vouloir supprimer définitivement cette entrée ?');
			if (is_confirmed) {
				document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&fours='+foc+'&delach='+id+'#four'+foc;
				document.fourmis.submit();
			}
		   }
		   
		   function confsupf(ki,koi){
			is_confirmed = confirm(\"êtes vous sûr de vouloir supprimer définitivement\\n l'ensemble des \"+ki+\" : \"+koi+\" ?\");
			if (is_confirmed) {
				document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&delens='+ki+'&ref='+koi+'#chif';
				document.fourmis.submit();
			}
		   }
		   
		   function achete(k,verb){
				document.fourmis.gafref.value=k;
				document.getElementById('comref').innerHTML=verb;
				document.getElementById('quantfour').style.visibility='visible';
				//document.getElementById('quantfour').style.top=h+55;
				curqu = envoyer('bin/inc_ajax.php?scan=gestion_artfour','w','read&ref='+k);
				if(curqu==false) curqu=0;
				else curqu = parseInt(curqu);
				document.fourmis.gafquant.value=curqu;
			 }
			 function decom(){
				 document.getElementById('quantfour').style.visibility='hidden';
				 document.fourmis.gafref.value='0';
			 }
			 function commande(){
				 if(!envoyer('bin/inc_ajax.php?scan=gestion_artfour','w','commande&ref='+document.fourmis.gafref.value+'&quant='+document.fourmis.gafquant.value,'gaf_retour')){
					document.getElementById('gaf_retour').innerHTML =\"Erreur\";
				}
				decom();
			 }
		</script>
	";
	
/**************************************************************************************************************************** DESCRIPTION
*/
////////////////////////////////////////////////////////////////// GENERALITE
	
	echo"
	<div id='colonel' class='buttontd' style='position:relative;display:$vis0; border-width:2px;text-align:left;'>
	
	<table class='cadre'><tr><td valign='top' align='left'>
	
	<b>Généralités</b>
	  <br><br>
	  <input type='hidden' name='modification' value='$mysqlnow'>
	  	<u>focus</u>: <br>
		<img src='$style_url/images/star$art_nouveaute.gif' border='none' alt='Focus: $art_nouveaute'>";
		if($u_droits == '' || $u_dgw == 1 ){
			echo"oui<input type=\"radio\" name=\"nouveaute\" value=\"1\" $actouno[$art_nouveaute]>
			non<input type=\"radio\" name=\"nouveaute\" value=\"0\" $actoudos[$art_nouveaute]>";
		}
	  echo"<br><br>
	<u>activé</u>:<br>
		";
		if($u_droits == '' || $u_active == 1 ){
		 echo"<img src='$style_url/$theme/v$art_active.gif' border='none' alt='actif: $art_active'>
			oui<input type=\"radio\" name=\"active\" value=\"1\" $actouno[$art_active]>
			non<input type=\"radio\" name=\"active\" value=\"0\" $actoudos[$art_active]>
		 ";
		}
		else{
		 echo"<img src='$style_url/$theme/mysqltype-special.png' alt='special'>sans droits<input type=\"hidden\" name=\"active\" value=\"0\">";
		}
	
	 echo"
	<br><br>
	<u>classement</u>:<br>";
	if($u_droits == '' || $u_dgw == 1 ){
		echo"<select name='rayon'><option></option>";	
		empil(0,0,$art_rayon);
		echo"</select>";
	}
	else{
		echo"<b>".get_item_trans($art_rayon,"ray")."</b>";
	}
	echo"<a href='./?gestion_rayons&edit=$art_rayon'>></a><br><br>";
	
	if($edit!=0){
		echo"
		Alias <a class='info'><font color='#FF0000'>/!\</font><span>Compatible avec les boutiques développées à partir de 2010</span></a><br>
		";
		if($u_droits == '' || $u_dgw == 1 ){
			if(isset($_GET['add_alias'])){
				$aa = $_GET['add_alias'];
				mysql_query("INSERT INTO `$articles_db` (`clon`,`rayon`,`active`) VALUES ($edit,$aa,$art_active)");
			}
			if(isset($_GET['supp_alias'])){
				$aa = $_GET['supp_alias'];
				mysql_query("DELETE FROM `$articles_db` WHERE `id`='$aa'");
			}
			
			$res=mysql_query("SELECT `id`,`rayon` FROM `$articles_db` WHERE `clon`='$edit'");
			if($res && mysql_num_rows($res)>0){
				while($rox=mysql_fetch_array($res)){
					if(isset($_GET['update']) && isset($_POST["clon_$rox[0]"])){
						if(mysql_query("UPDATE `$articles_db` SET `rayon`='".$_POST["clon_$rox[0]"]."',`active`='$art_active' WHERE `id`='$rox[0]'")){
							$rox[1] = $_POST["clon_$rox[0]"];
						}
					}
					echo"<select name='clon_$rox[0]'>";	
					empil(0,0,$rox[1]);
					echo"</select><a onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&supp_alias=$rox[0]&refresh=1';document.fourmis.submit();\"><img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a><br><br>";	
				}
			}
		
		echo"
			<select name='clon_add' onchange=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&add_alias='+this.value+'&refresh=1';document.fourmis.submit();\"><option value=''>Ajouter un alias</option>";	
				empil(0,0,0);
				echo"</select><br><br>";
		}
		else{
			$res=mysql_query("SELECT `id`,`rayon` FROM `$articles_db` WHERE `clon`='$edit'");
			if($res && mysql_num_rows($res)>0){
				while($rox=mysql_fetch_array($res)){
					echo"<b>".get_item_trans($rox[1],"ray")."</b>";	
				}
			}
		}
	}
	echo"
	</td>
	";
////////////////////////////////////////////////////////////////// TRADUCTION
	
	echo"
	<td valign='top' style='border-width:0px;border-right-width:1px;border-color:#CCCCCC;border-style:solid'>
	<b>Appelation</b><br>";
	$i=0;
	$unictrad='';
	  $ris = mysql_query("SELECT * FROM `$langue_db`");
		if($ris && mysql_num_rows($ris)>0){
			while($riw=mysql_fetch_object($ris)){
				$lng_code = $riw->code;
				$lng_nom = $riw->nom;
				$i++;
				$lng_nome=str_replace("'","''",$_POST["trad_nom_$lng_code"]);
				$lng_dese=str_replace("'","''",$_POST["trad_des_$lng_code"]);
				$lng_pl1e=str_replace("'","''",$_POST["trad_pl1_$lng_code"]);
				$lng_pl2e=str_replace("'","''",$_POST["trad_pl2_$lng_code"]);
				if($unictrad=='') $unictrad = $lng_nome; 	   
				   
				if(trim($unictrad)=="")  $unictrad=$lng_nome="$lng_code #$edit";
				if( ( isset($_GET['update'])|| isset($_GET['add'])) && isset($_POST["trad_nom_$lng_code"]) && is_numeric($edit)){
					if(mysql_query("UPDATE `gestion_artrad` SET `nom`='$lng_nome',`desc`='$lng_dese',`plus1`='$lng_pl1e',`plus2`='$lng_pl2e' WHERE `ref`='$edit' AND `lng`='$lng_code'")){
						$return.=returnn("mise à jour de traduction \"$lng_code\" effectuée avec succès","009900",$vers,$theme);
					}
					else{
						$return.=returnn("mise à jour de traduction \"$lng_code\" échouée","990000",$vers,$theme);
					}
				}
				$ras = mysql_query("SELECT * FROM `gestion_artrad` WHERE `ref`=$edit AND `lng`='$lng_code'");
				if($ras && mysql_num_rows($ras)==1){
					$raw=mysql_fetch_object($ras);
					$lng_nom_val=$raw->nom;
					$lng_des_val=$raw->desc;
					$lng_pl1_val=$raw->plus1;
					$lng_pl2_val=$raw->plus2;
				}
				elseif(is_numeric($edit)){
					if(mysql_query("INSERT INTO `gestion_artrad` (`ref`,`lng`,`nom`,`desc`,`plus1`,`plus2`) VALUES('$edit', '$lng_code', '$lng_nome', '$lng_dese', '$lng_pl1e', '$lng_pl2e')")){
						$return.=returnn("création de traduction \"$lng_code\" effectuée avec succès","009900",$vers,$theme);
					}
					else{
						$return.=returnn("création de traduction \"$lng_code\" échouée","990000",$vers,$theme);
					}
					$lng_nom_val=$lng_nome;
					$lng_des_val="";
					$lng_pl1_val="";
					$lng_pl2_val="";
				}
				
				echo"<br>		
				<a onclick=\"sizpa('tr_$lng_code')\" style='cursor:pointer'><b>$lng_nom</b><span style='display:inline-block;height:12px; width:200px; position:relative; overflow:hidden; padding-left:10px;'><i>$lng_nom_val $lng_des_val</i></span></a>
				<div id='tr_$lng_code' style='width:480px;height:5px;overflow:hidden'>
				<table class='cadre' style='float:left;width:450px;height:130px;margin:2px;'>";
				if($u_droits == '' || $u_dgw == 1 ){
					echo"
					<tr><td>Nom:</td>
						<td><input type=\"texte\" name=\"trad_nom_$lng_code\" value=\"$lng_nom_val\" size=\"30\" style='width:270px;'></td>
					</tr>
					<tr><td colspan='2'>Description:<br>";
					$edition=0;
					if( ereg("</",$lng_des_val) || ereg("<br>",$lng_des_val) ||  ereg("<BR>",$lng_des_val) ){
						$edition=1;				
					}
					editor("trad_des_$lng_code",$lng_des_val,$i,'',$edition,1);
					//<textarea name=\"trad_des_$lng_code\"  style='width:270px;height:70px;'>$lng_des_val</textarea></td>
					echo"</tr>
					<tr><td>Complément 1 :</td>
						<td><input type=\"texte\" name=\"trad_pl1_$lng_code\" value=\"$lng_pl1_val\" size=\"30\" style='width:270px;'></td>
					</tr>
					<tr><td>Complément 2:</td>
						<td><input type=\"texte\" name=\"trad_pl2_$lng_code\" value=\"$lng_pl2_val\" size=\"30\" style='width:270px;'></td>
					</tr>";
				}
				else{
					echo"
					<tr><td>nom:</td>
						<td>$lng_nom_val</td>
					</tr>
					<tr><td>description:</td>
						<td>".nl2br($lng_des_val)."</td>
					</tr>
					<tr><td>complément 1 :</td>
						<td>$lng_pl1_val</td>
					</tr>
					<tr><td>complément 2:</td>
						<td>$lng_pl2_val</td>
					</tr>";
				}
				echo"</table></div>";
			}
		}	  
	  echo"</td>
	  
	";
////////////////////////////////////////////////////////////////// IMAGE
	
	echo"<td valign='top'><b>Aperçu</b><br><br>";
	if($edit!=""){
		$fich_temp = $fichiers["gestion_articles"];
		$fichiers["gestion_articles"]=$special_files;
			
			insert('_fichiers');
			  if(is_file('bin/_fichiers.php')){
				  include('bin/_fichiers.php');
			  }
			  else{
				  include('$style_url/update.php?file=_fichiers.php&1');
			  }

			echo"<a class='info'><font color='#FF0000'>/!\</font><span>Compatible avec les boutiques développées  à partir de 2010</span></a><br>";
			$fichiers["gestion_articles"] = $fich_temp;
	}
	else{
		echo"le chargement d'une image sera possible après un premier enregistrement";
	}
	echo"</td></tr></table>
	</div>
	";
/**************************************************************************************************************************** DESCRIPTION
*/
////////////////////////////////////////////////////////////////// CHIFFRES
	if(is_numeric($edit)){

	 $assoclas = array("col"=>"couleur","tai"=>"taille");
	 $assoclasd = array("tai"=>"couleur","col"=>"taille");
	 $rang_names = array('taille'=>$taille_g_nom,'couleur'=>$couleur_g_nom);
	 
	 $clasg = array_flip($assoclas);
	 $fp = @fopen("mconfig/$u_id.gestion.articles.classpar","rb");
	 $claspar = trim(@fread($fp,10));
	 //echo $claspar;
	 @fclose($fp);
	 if(isset($_GET['clp']) && ($_GET['clp']=='tai' || $_GET['clp']=='col')){
	 	 $claspar=$_GET['clp'];
		 $fp = @fopen("mconfig/$u_id.gestion.articles.classpar","w+");
		 fwrite($fp,$claspar);
		 @fclose($fp); 
	 }
	 if($claspar=="") $claspar="tai";
	 $clasnom = $assoclas[$claspar];
	 $clasoth = $assoclasd[$claspar];
	 $getoth = $clasg[$clasoth];
$fourh=0;
	 	 
	 
///////////////////////////////////////////////////// MODIF REF	 
	 if(isset($_GET['delref']) ){
		if(deletefromdb($base,"gestion_artstock",$_GET['delref'])){
			$return.=returnn("suppression effectuée avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("la suppression a échouée","990000",$vers,$theme);
		}
	 }
	 if(isset($_GET['delach']) ){
		if(deletefromdb($base,"gestion_artfour",$_GET['delach'])){
			$return.=returnn("suppression effectuée avec succès","009900",$vers,$theme);
		}
		else{
			$return.=returnn("la suppression a échouée","990000",$vers,$theme);
		}
	 }
	 if(isset($_GET['delens']) && in_array($_GET['delens'],$assoclas) && isset($_GET['ref']) && $_GET['ref']!=''){
	 	$delens = $_GET['delens'];
		$ref = $_GET['ref'];
		$res = mysql_query("SELECT * FROM `gestion_artstock` WHERE `ref`='$edit' AND `$delens`='".str_replace("'","''",$ref)."'");
		 while($rou=mysql_fetch_object($res)){
		 	$s_id = $rou->id;
			if(deletefromdb($base,"gestion_artstock",$s_id)){
				$return.=returnn("suppression effectuée avec succès","009900",$vers,$theme);
			}
			else{
				$return.=returnn("la suppression a échouée","990000",$vers,$theme);
			}
		}
		
	 }
	
	if(isset($_GET['update'])){
		$res = mysql_query("SELECT `id` FROM `gestion_artstock` WHERE `ref`='$edit'");
		while($rou=mysql_fetch_object($res)){
			$s_id = $rou->id;
			if(updatedb($base,"gestion_artstock",$s_id,$s_id)){
				$return.=returnn("modification de chiffre effectuée avec succès","009900",$vers,$theme);
				if($_FILES["fs$s_id"]["name"]!=NULL && is_dir("../gestion_articles/sia/")){
					if(addfile("../gestion_articles/sia/$s_id.jpg", $_FILES["fs$s_id"]['name'], $_FILES["fs$s_id"]['tmp_name'], $dangerous)){
						$return.=returnn( $_FILES["fs$s_id"]['name']." chargé avec succès","009900",$vers,$theme);
					}
					else{
						$return.=returnn($_FILES["fs$s_id"]['name']." n'a pu être chargé correctement","990000",$vers,$theme);
					}
				}
			}
			else{
				$return.=returnn("la modification de chiffre a échouée","990000",$vers,$theme);
			}			
			if(isset($fournisseurs_db) &&  mysql_query("SHOW COLUMNS FROM $fournisseurs_db") && mysql_query("SHOW COLUMNS FROM `gestion_artfour`") ){
				$ref = mysql_query("SELECT `id` FROM `gestion_artfour` WHERE `art`='$s_id'");
				if($ref && mysql_num_rows($ref)>0){
					while($fo=mysql_fetch_array($ref)){
						if(updatedb($base,"gestion_artfour",$fo[0],"pa_$fo[0]")){
							$return.=returnn("modification de prix d'achat effectuée avec succès","009900",$vers,$theme);
						}
						else{
							$return.=returnn("la modification de prix d'achat a échouée","990000",$vers,$theme);
						}
					}
				}
			}
		}
	}
	
	if(isset($_GET['addref'])){
		$addref = $_GET['addref'];
		$val = $_GET['val'];
		$quiquequoi = "taille";
		if($addref=="taille") $quiquequoi = "couleur";
		$allkoi=array();
		
		if(isset($_GET['forall'])){
			 $res = mysql_query("SELECT DISTINCT `$quiquequoi` FROM `gestion_artstock` WHERE `ref`='$edit' $plr");
			 if($res && mysql_num_rows($res)>0){
				while($rou=mysql_fetch_array($res)){
					array_push($allkoi,$rou[0]);
				}
			 }
		}
		else{
			$allkoi[0]='';	
		}
		 if(is_numeric($edit)){
			  for($i=0 ; $i<sizeof($allkoi) ; $i++){
				$ligne = $allkoi[$i];
				if(!mysql_query("INSERT INTO `gestion_artstock` (`ref`,`$addref`,`$quiquequoi`,`active`) VALUES ($edit,'$val','$ligne',1)")){
					$return.=returnn("impossible d'insérer la ligne $val...","990000",$vers,$theme);
				}
				else{
					$return.=returnn("insertion de $val...","009900",$vers,$theme);
				}
			 }
		 }
	}
	if(isset($_GET['addfour']) && isset($_POST['selfour_'.$_GET['addfour']])){
		$addfour = $_GET['addfour'];
		$four = $_POST['selfour_'.$addfour];
		if(!mysql_query("INSERT INTO `gestion_artfour` (`art`,`four`,`active`) VALUES ('$addfour','$four',1)")){
			$return.=returnn("impossible d'insérer la ligne fournisseur $four...","990000",$vers,$theme);
		}
	}
	elseif(isset($_GET['addfour']) && $_GET['addfour']=='all'){
		$res = mysql_query("SELECT `id` FROM `gestion_artstock` WHERE `ref`='$edit'");
		 while($rou=mysql_fetch_array($res)){
		 	$s_id = $rou[0];
			if(isset($_POST["selfour_$s_id"]) && $_POST["selfour_$s_id"]!=NULL){
				$four = $_POST["selfour_$s_id"];
				if(!mysql_query("INSERT INTO `gestion_artfour` (`art`,`four`,`active`) VALUES ('$s_id','$four',1)")){
					$return.=returnn("impossible d'insérer la ligne fournisseur $four...","990000",$vers,$theme);
				}	
			}
		}
	}
///////////////////////////////////////////////////// CHANGE LES LES FAMILLES 
	if(isset($_GET['update'])){
	 $allkoi=array();
	 $res = mysql_query("SELECT DISTINCT `$clasnom` FROM `gestion_artstock` WHERE `ref`='$edit'");
	 if($res && mysql_num_rows($res)>0){
	 	while($rou=mysql_fetch_array($res)){
			array_push($allkoi,$rou[0]);
		}
	 }
	 for($i=0 ; $i<sizeof($allkoi) ; $i++){
	 	$ligne = $allkoi[$i];
		if(isset($_POST["$clasnom"."_$i"]) && $_POST["$clasnom"."_$i"]!=$ligne){
			if(mysql_query("UPDATE `gestion_artstock` SET `$clasnom`='".str_replace("'","''",$_POST["$clasnom"."_$i"])."' WHERE `ref`='$edit' AND `$clasnom`='".str_replace("'","''",$ligne)."'")){			
				$return.=returnn("modification de la famille $clasnom: $ligne","009900",$vers,$theme);
			}
			else{			
				$return.=returnn("modification de la famille $clasnom: $ligne échouée...","990000",$vers,$theme);
			}
		}
	 }
	}
	 
	 
///////////////////////////////////////////////////// LISTE LES FAMILLES (FINAL)	 
	 $allkoi=array();
	 $res = mysql_query("SELECT DISTINCT `$clasnom` FROM `gestion_artstock` WHERE `ref`='$edit'");
	 if($res && mysql_num_rows($res)>0){
	 	while($rou=mysql_fetch_array($res)){
			array_push($allkoi,$rou[0]);
		}
	 }
	 
	 if(sizeof($allkoi)==0 && is_numeric($edit)){
	 	if(!mysql_query("INSERT INTO `gestion_artstock` (`ref`,`couleur`,`taille`,`active`) VALUES ($edit,'','',1)")){
			$return.=returnn("impossible de gérer les chiffres...","990000",$vers,$theme);
		}
	 }
	 	$verifupdt = mysql_query("DESC `gestion_artstock`");
		$allchamps = array();
		while($roi = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$roi->Field);
		}
		if(!in_array("libre",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `libre` varchar(255) NOT NULL default ''");
		}
		if(!in_array("libre_pro",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `libre_pro` varchar(255) NOT NULL default ''");
		}
		if(!in_array("ean",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `ean` varchar(13) NOT NULL default ''");
		}
		if(!in_array("active",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `active` int(1) NOT NULL default '0'");
		}
		if(!in_array("prix_pro",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `prix_pro` float(10,2) NOT NULL default '0'");
		}
		if(!in_array("promo_pro",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `promo_pro` float(10,2) NOT NULL default '0'");
		}
		if(!in_array("prix_rev",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `prix_rev` float(10,2) NOT NULL default '0'");
		}		
		
		if(!in_array("poids",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `poids` float(10,2) NOT NULL default '0'");
		}
		if(!in_array("com",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `com` float(10,2) NOT NULL default '0'");
		}
		if(!in_array("comw",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artstock` ADD `comw` int(1) NOT NULL default '0'");
		}
		$verifupdt = mysql_query("DESC `gestion_artfour`");
		$allchamps = array();
		while($roi = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$roi->Field);
		}
		if(!in_array("remise",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artfour` ADD `remise` float(5,2) NOT NULL default '0'");
		}
	 //`id`, `ref`, `ordre`, `code`, `ean`, `couleur`, `taille`, `prix`, `tva`, `promo`, `stock`, `active`
	 if($u_droits == '' || $u_dgw == 1 ){
		 if(isset($fournisseurs_db)){
			 if(mysql_query("SHOW COLUMNS FROM $fournisseurs_db") && mysql_query("SHOW COLUMNS FROM `gestion_artfour`") ){
				 
				 $fourtruc='';
				 $ref = mysql_query("SELECT `id`,`nom` FROM `$fournisseurs_db` ORDER BY `nom`");
				 if($ref && mysql_num_rows($ref)>0){
					$foursel="<select name='FOUR_NAME'><option value=''>Ajouter</option>"; 
					while($fo=mysql_fetch_array($ref)){
						$foursel.="<option value='$fo[0]'>".ucfirst($fo[1])."</option>"; 	
					}
					$foursel.="</select>"; 
				 }
			 }
			 
		 }
	 }
	 
	
	 echo"
	 <div  id='stoc' class='buttontd' style='position:relative;display:$vis1;  border-width:2px; text-align:left;'>
		Vente / stocks
	 <table cellspacing='0' cellpadding='2' class='cadre'>";
	 if($taxe_cible=='HT'){
		 echo"
		 <tr class='buttontd'>
			<td colspan='9' align='right'>Particuliers</td>
			<td></td>		
			<td colspan='2' class='cadre' style='border-bottom-width:0px'>PRO</td>
			<td colspan='3'></td>
		 </tr>";
	 }
	 else{
		 echo"
		 <tr class='buttontd'>
			<td colspan='7'></td>
			<td colspan='2' class='cadre' style='border-bottom-width:0px'>Particuliers</td>
			<td></td>	
			<td colspan='5' align='left'>PRO</td>
		 </tr>";
	 }
	 echo"
	 <tr class='buttontd'>
	 	<td width='10'><b>".ucfirst($rang_names[$clasnom])."</b></td>
		<td width='10'>^v</td>
		<td>Référence</td>
		<td>Code barre</td>
		<td><a href='./?$part&edit=$edit&clp=$getoth' name='chif'><u>".ucfirst($rang_names[$clasoth])."</u></a></td>
		<td><a class='info'>Poids<span>(Kg)</span></a>";
	if($u_droits != '' && $u_dgw != 1 ){
		echo"&nbsp;Commission";
	}
		echo"</td>
		<td>$desc_g_nom</td>
		";
	 if($taxe_cible=='HT'){
		 echo"
		<td><a class='info'>Prix<span> (€)</span></a></td>
		<td><a class='info'>Prix barré<span> (€)</span></a></td>
		<td>Desc. pro</td>
		<td class='cadre' style='border-width:0px;border-left-width:1px'><a class='info'>Prix<span>(€)</span></a></td>
		<td class='cadre' style='border-width:0px;border-right-width:1px'><a class='info'>Prix barré<span> (€)</span></a></td>";
	 }
	 else{
		 echo"
		<td class='cadre' style='border-width:0px;border-left-width:1px'><a class='info'>Prix<span> (€)</span></a></td>
		<td class='cadre' style='border-width:0px;border-right-width:1px'><a class='info'>Prix barré<span> (€)</span></a></td>
		<td>Desc. pro</td>
		<td><a class='info'>Prix<span>(€)</span></a></td>
		<td><a class='info'>Prix barré<span> (€)</span></a></td>";
	 }
	 echo"
		
		<td><a class='info'>TVA<span>(%)</span></a></td>
		<td>Stock</td>
		<td width='60'>";
		if($u_droits == '' || ($u_active == 1 && $u_dgw==1)){
			echo"
			<script language='javascript'>
	function conmulti(k){
		var transk = new Array();
		transk['active']='activer';
		transk['desactive']='désactiver';
		transk['delete']='supprimer';
		nbsel=0;
		posch='';
		var allche = document.getElementById('stoc').getElementsByTagName(\"input\");
		var selche=new Array();
		for (var i=2; i<allche.length; i++) {
			if(allche[i].type == 'checkbox' && allche[i].checked==true){
				nbsel++;
				posch+='&stch'+allche[i].value+'=1';
				selche.push(allche[i].value);
			}
		}
		if(nbsel>0){
			pro = confirm(\"êtes vous certain de vouloir \"+transk[k]+\" les \"+nbsel+\" objets sélectionnés ?\");
			if(pro){
				envht=envoyer('bin/inc_ajax.php?scan=gestion_artstock','w','&multi=$edit&action='+k+''+posch);
				if(envht===false){
					alert('erreur');
				}
				else{
					if(envht=='0'){
						for (var i=0; i<selche.length; i++) {
							if(k=='delete') document.getElementById('tr'+selche[i]).innerHTML='';
							if(k=='active') document.getElementById('img'+selche[i]).src='$style_url/$theme/v1.gif';
							if(k=='desactive') document.getElementById('img'+selche[i]).src='$style_url/$theme/v0.gif';
						}	
					}
				}
			}
		}
		else{
			alert(\"aucun objet n'est sélectionné\");
		}
	}
	</script>
			
			<input type='checkbox' name='all$i$c' onclick=\"tout(document.getElementById('stoc'),this)\">&nbsp;<a href='#suppall' name='suppall' onclick=\"conmulti('delete')\"><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>&nbsp;<a href='#suppall' onclick=\"conmulti('active')\" class='info'><img src='$style_url/$theme/v1.gif' border='none' alt='v'><span>Activer les articles cochés</span></a>&nbsp;<a href='#suppall' onclick=\"conmulti('desactive')\" class='info'><img src='$style_url/$theme/v0.gif' border='none' alt='o'><span>Désactiver les articles cochés</span></a>
			";
		}
		else{
			echo".";
		}
		
		echo"</td>
	 </tr>";
	 for($i=0 ; $i<sizeof($allkoi) ; $i++){
	 	$ligne = $allkoi[$i];
		echo"<tr class='buttontd'><td align='left'><a name='f$i'></a>";
		if($u_droits == '' || $u_dgw == 1 ){
			if(isset(${"gestion_articles_".$clasnom."_liste"})){
				echo"<select name='$clasnom"."_$i'>";
				foreach(${"gestion_articles_".$clasnom."_liste"} as $accval){
					$s='';
					if($accval==$ligne) $s='selected';
					echo"<option value=\"$accval\" $s>$accval</option>";
				}
				echo'</select>';
			}
			else{
				echo"<input type='text' name='$clasnom"."_$i' value=\"$ligne\">";
			}
		}
		else{
			echo"<b>$ligne</b>";
		}
		echo"<a name=\"_$ligne\"></td>";
		 if($taxe_cible=='HT'){
			 echo"
			 <td colspan='9'></td>
			 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
			 <td colspan='2'></td>
			";
		 }
		 else{
			 echo"
			<td colspan='7'></td>
			 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
			 <td colspan='4'></td>";
		 }
		
	 echo"<td align='right'>
		";
		if(isset($fourtruc)  ){
			$fourtruc.="<tr>
				<td class='buttontd'><b>$ligne</b></td>
				<td class='buttontd'>Fournisseur</td>
				<td class='buttontd'>Ref.</td>
				<td class='buttontd'><a class='info'>P. base<span>Prix de base</span></a></td>
				<td class='buttontd'><a class='info'>Rem.<span>Remise fourniseur</span></a></td>
				<td class='buttontd'><a class='info'>P. achat<span>prix de revient</span></a></td>
				<td class='buttontd'><a class='info'>P. de revient<span>prix de base</span></a></td>
				<td class='buttontd'>TVA</td>
				<td class='buttontd'><a class='info'>Achat<span>Commander des articles</span></a></td>
				<td class='buttontd'></td>
				<td class='buttontd'><a class='info'>Com.<span>Commission vendeur</span></a></td>
				<td class='buttontd'><img src='$style_url/images/marge_par.png' border='none' alt='Particuliers'>Vente</td>
				<!--td class='buttontd'>Marge</td>
				<td class='buttontd'>Coef.</td-->
				<td class='buttontd'><img src='$style_url/images/marge_pro.png' border='none' alt='Professionels'>Vente</td>
				<!--td class='buttontd'>Marge</td>
				<td class='buttontd'>Coef.</td-->
				<td class='buttontd'>Outils</td>
				<td class='buttontd'><a class='info'>Sup.<span>Supprimer</span></a></td>			
			</tr>";
			//$fourh+=60;
		}			
		if($u_droits == '' || $u_dgw == 1 ){
		echo"<input type='button' value='Ajouter' class='buttontd' onclick=\"addinref('$clasnom','$ligne',true);\">";
		}
		echo"</td></tr>";
		$res = mysql_query("SELECT * FROM `gestion_artstock` WHERE `ref`='$edit' AND `$clasnom`='".str_replace("'","''",$ligne)."' ORDER BY `ordre`");
		 while($rou=mysql_fetch_object($res)){
		 	$s_id = $rou->id;
			$s_ordre = $rou->ordre;
			$s_code = $rou->code;
			$s_ean = $rou->ean;
			$s_other = $rou->$clasoth;
			$s_libre = $rou->libre;
			$s_libre_pro = $rou->libre_pro;
			$s_prix_rev = $rou->prix_rev;
			$s_prix = $rou->prix;
			$s_promo = $rou->promo;
			$s_prix_pro = $rou->prix_pro;
			$s_promo_pro = $rou->promo_pro;
			$s_tva = $rou->tva;
			$s_stock = $rou->stock;
			$s_poids = $rou->poids;
			$s_active = $rou->active;
			$s_com = $rou->com;
			$s_comw = $rou->comw;
			$fourh+=23;
			if($autoref==1){
				if($s_code==''){
					$s_code = "$art_rayon-$s_id";
				}
				if($s_ean==''){
					$s_ean = str_repeat('0',4-strlen($art_rayon)).$art_rayon.str_repeat('0',9-strlen($s_id)).$s_id;
				}
			}
			
			if(isset($fourtruc) && ($u_droits == '' || $u_dgw == 1) ){
				$ref = mysql_query("SELECT `id`,`four`,`prix`,`reference`,`tva`,`remise` FROM `gestion_artfour` WHERE `art`='$s_id' ORDER BY `prix`");
				$fourtruc.="<tr><td id='fourefid_$s_id' valign='top' rowspan='".(abs(mysql_num_rows($ref))+1)."'><b>$s_code</b><br>$s_other</td>";
				 $li=0;
				if($ref && mysql_num_rows($ref)>0){
					while($fo=mysql_fetch_array($ref)){
						$numref = mysql_num_rows($ref);
						$fourn=get($fournisseurs_db,'nom',$fo[1]);
						
						$pach = $fo[2]-round($fo[2]*$fo[5]/100,2);
						$pvpa = $s_prix-round($s_prix*$s_com/100,2);
						$pvpro = $s_prix_pro-round($s_prix_pro*$s_com/100,2);
						if($li>0) $fourtruc.="<tr>";
						
						$fourtruc.="<td align='left'>$fourn</td>
						<td valign='top'>
							<input type='text' name='referencepa_$fo[0]' value='$fo[3]' style='width:100px'>
						</td>
						<td>
							<input type='text' name='prixpa_$fo[0]' value='$fo[2]' class='gaf_prix' onkeyup='ch_achat($fo[0],this.value,$s_id)'>
						</td>
						<td>
							<input type='text' name='remisepa_$fo[0]' value='$fo[5]' class='gaf_txt' onkeyup='ch_achat($fo[0],document.fourmis.prixpa_$fo[0].value,$s_id)'>%
						</td>
						<td align='right'>
						<input type='text' name='apach$fo[0]' value='$pach'   class='gaf_ro' readonly>
						</td>";
						
						if($li==0){
							if($s_prix_rev==0){
								$s_prix_rev = 	$pach;
							}
							$fourtruc.="<td align='right' valign='middle' rowspan=$numref'><input type='text' name='prix_rev$s_id' value='$s_prix_rev' class='gaf_prix'>&euro;</td>";
						}
						$fourtruc.="
						<td>
							<input type='text' name='tvapa_$fo[0]' value='$fo[4]' class='gaf_txt'>%
						</td>
						<td>
							<a onclick='achete($fo[0],\"$s_code : $fourn\")'><img src='$style_url/images/caddie.gif' border='none' alt='Commander'></a>
						</td>
						<td class='buttontd'></td>
						";							
							if($li==0){
								$fourtruc.="<td valign='middle' rowspan=$numref'><input type='text' name='com$s_id' value='$s_com' class='gaf_txt' onkeyup='ch_achat($fo[0],document.fourmis.prixpa_$fo[0].value,$s_id)'>%<br><select name='comw$s_id' onchange='ch_achat($fo[0],document.fourmis.prixpa_$fo[0].value,$s_id)' class='gaf_txt'>";
								if($s_comw==0) $fourtruc.="<option value='0' selected>p.vente</option><option value='1'>marge</option>";	
								else $fourtruc.="<option value='0'>p.vente</option><option value='1' selected>marge</option>";	
								$fourtruc.="</select></td>";
							}
						$fourtruc.="
						";							
							if($li==0)$fourtruc.="<td align='right' valign='middle' rowspan=$numref'><input type='text' name='ventepa_$s_id' value='$s_prix'   class='gaf_ro' readonly>&euro;</td>";
						/*$fourtruc.="
						<td>							
							<input type='text' name='margepa_$fo[0]' value='".($pvpa-$pach)."' class='gaf_txt' onkeyup='ch_marge($fo[0],\"pa\",this.value,$s_id)'  onfocus='ch_achat($fo[0],document.fourmis.prixpa_$fo[0].value,$s_id)'> &euro;
						</td>
						<td>
							<input type='text' name='coefpa_$fo[0]' value='".round($pvpro/$pach,1)."'  class='gaf_txt' onkeyup='ch_coef($fo[0],\"pa\",this.value,$s_id)' onfocus='ch_achat($fo[0],document.fourmis.prixpa_$fo[0].value,$s_id)'>
						</td>
							";	*/					
							if($li==0) $fourtruc.="<td align='right' valign='middle' rowspan=$numref'><input type='text' name='ventepro_$s_id' value='$s_prix_pro'   class='gaf_ro' readonly>&euro;</td>";
						/*$fourtruc.="
						<td>
							<input type='text' name='margepro_$fo[0]' value='".($pvpro-$pach)."'   class='gaf_txt' onkeyup='ch_marge($fo[0],\"pro\",this.value,$s_id)' onfocus='ch_achat($fo[0],document.fourmis.prixpa_$fo[0].value,$s_id)'>&euro;
						</td>
						<td>
							<input type='text' name='coefpro_$fo[0]' value='".round($pvpro/$pach,1)."'   class='gaf_txt' onkeyup='ch_coef($fo[0],\"pro\",this.value,$s_id)' onfocus='ch_achat($fo[0],document.fourmis.prixpa_$fo[0].value,$s_id)'>
						</td>";*/
						if($u_droits == '' || $u_active == 1 ){
							if($li==0){
								$fourtruc.="
								<td valign='middle' rowspan=$numref'>
								<a onclick=\"document.getElementById('org$fo[0]').style.visibility='visible';\"><img src='$style_url/img/calc.png' alt='promo'/></a>
								<div class='outilpromo'>
								<table id='org$fo[0]' class='cadre' cellspacing='0'>
									<tr class='buttontd'><td></td><td>Remise</td><td>p. vente</td><td>Com. vendeur</td><td>Marge</td><td>Appliquer</td><td><a onclick=\"document.getElementById('org$fo[0]').style.visibility='hidden';\">Fermer</a></td></tr>
									<tr><td>Part.</td><td><input type='text' name='orpa$fo[0]' value='0.00' onkeyup=\"orf('pa',$fo[0],$s_id)\">%</td><td><span id='orpac$fo[0]'>$s_prix</span>&euro;</td><td><span id='orpav$fo[0]'>".round($pvpa*$s_com/100,2)."</span>&euro;</td><td><span id='orpam$fo[0]'>".($pvpa-$pach)."</span>&euro;</td><td>
									<a onclick=\"defi('pa',$fo[0],$s_id)\">Définitif</a> /
									<a onclick=\"prom('pa',$fo[0],$s_id)\">Promo</a>
									</td><td></td></tr>
									<tr><td>Pro.</td><td><input type='text' name='orpro$fo[0]' value='0.00' onkeyup=\"orf('pro',$fo[0],$s_id)\">%</td><td><span id='orproc$fo[0]'>$s_prix_pro</span>&euro;</td><td><span id='orprov$fo[0]'>".round($pvpro*$s_com/100,2)."</span>&euro;</td><td><span id='orprom$fo[0]'>".($pvpro-$pach)."</span>&euro;</td><td>
									<a onclick=\"defi('pro',$fo[0],$s_id)\">Définitif</a> /
									<a onclick=\"prom('pro',$fo[0],$s_id)\">Promo</a>
									</td><td></td></tr>
								</table>
								</div>
								</td>
								";
							}
							$fourtruc.="<td>
							<a onclick='confsupa($fo[0],$s_id)'><img src='$style_url/$theme/trash.gif' border='none' alt='Supprimer'></a>";
						}
						$li++;
					$fourtruc.="</td>
						</tr>
						";
						
					}
				}
				//<a class='buttontd' name='four$s_id' href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&fours=$s_id&addfour=$s_id#four$s_id';document.fourmis.submit();\">ok</a>
				$fourtruc.="<tr>
				<td colspan='8' valign='top'>
				".str_replace('FOUR_NAME',"selfour_$s_id",$foursel)."
				</td><td class='buttontd'></td>
				<td colspan='5'>
				</tr>";
			}
			
			echo"
			<tr id='tr$s_id'><td align='right'>";
			
			if(is_dir("../gestion_articles/sia/")){
				$txtch="Image";
				$supch='';
				if(is_file("../gestion_articles/sia/$s_id.jpg")){
					echo"<a class='info'><img src='./?incpath=_ima.php&file=gestion_articles/sia/$s_id.jpg&jeveuxH=20' alt='icone' height='20'><span><img src='./?incpath=_ima.php&file=gestion_articles/sia/$s_id.jpg&jeveuxH=100' alt='icone' height='100'></span></a>";
					if($u_droits == '' || $u_dgw == 1 ){
						$txtch="Changer";
						$supch="<br><a href=\"#\" onclick=\"delfile('</gestion_articles/sia/$s_id.jpg')\">Supprimer</a>";
					}
				}
				echo"<span style='position:relative; display:inline-block;font-size:10px'>
					<a onclick=\"document.getElementById('chim$s_id').style.visibility='visible';\" id='txtim$s_id'>$txtch</a>
					$supch";
					if($u_droits == '' || $u_dgw == 1 ){
						echo"<div id='chim$s_id' style='position:absolute; background:#FFF; border:#999 1px solid; padding:5px; z-index:300; visibility:hidden'>
					<input type='file' name='fs$s_id' onchange=\"document.getElementById('txtim$s_id').innerHTML='Changer';document.getElementById('chim$s_id').style.visibility='hidden';\">&nbsp;<a onclick=\"document.getElementById('chim$s_id').style.visibility='hidden';\">Annuler</a>
					</div>";
					}
				echo"</span>";
			}
			if($u_droits == '' || $u_dgw == 1 ){
				echo"
				</td>
				<td width='10'><input type='text' name='ordre$s_id' value='$s_ordre' size='1'>
				<input type='hidden' name='$clasnom$s_id' value='$ligne'></td>
				<td><input type='text' name='code$s_id' value='$s_code' size='4'></td>
				<td><input type='text' name='ean$s_id' value='$s_ean' size='13' maxlength='13' style='font-size:10px'  onchange=\"document.im_ean$s_id.src='$openean&ean='+this.value; document.img_ean$s_id.src='$openean&ean='+this.value;\"><a class='info'>|||<span><img name='img_ean$s_id' src='$openean&ean=$s_ean' alt='$s_ean' border='none'></span></a>
				</td>
				<td>";
				if(isset(${"gestion_articles_".$clasoth."_liste"})){
					echo"<select name='$clasoth$s_id'>";
					foreach(${"gestion_articles_".$clasoth."_liste"} as $accval){
						$s='';
						if($accval==$s_other) $s='selected';
						echo"<option value=\"$accval\" $s>$accval</option>";
					}
					echo'</select>';
				}
				else{
					echo"<input type='text' name='$clasoth$s_id' value='$s_other' size='4'>";
				}
				echo"
				</td>
				<td><input type='text' name='poids$s_id' value='$s_poids' size='3'></td>
				<td><input type='text' name='libre$s_id' value='$s_libre' size='8'></td>";
			}
			else{
				echo"
				</td>
				<td width='10'>$s_ordre</td>
				<td>$s_code</td>
				<td><a class='info'>|||<span><img name='img_ean$s_id' src='$openean&ean=$s_ean' alt='$s_ean' border='none'></span></a></td>
				<td>";
				if(isset(${"gestion_articles_".$clasoth."_liste"})){
					echo"$accval";
				}
				else{
					echo"$s_other";
				}
				echo"
				</td>
				<td>$s_poids&nbsp;-&nbsp;$s_com%</td>
				<td>$s_libre</td>
		";
			}
	 if($taxe_cible=='HT'){
		 if($u_droits == '' || $u_dgw == 1 ){
			 echo"
			 <td><input type='text' name='prix$s_id' value='$s_prix' size='4' style='text-align:right' onkeyup='ch_vente($s_id,\"pa\",this.value)'></td>
			<td><input type='text' name='promo$s_id' value='$s_promo' size='4' style='text-align:right'></td>
			<td><input type='text' name='libre_pro$s_id' value='$s_libre_pro' size='8'></td>
			<td class='cadre' style='border-width:0px;border-left-width:1px;'><input type='text' name='prix_pro$s_id' value='$s_prix_pro' size='4' style='text-align:right'  onkeyup='ch_vente($s_id,\"pro\",this.value)'></td>
			<td class='cadre' style='border-width:0px;border-right-width:1px;'><input type='text' name='promo_pro$s_id' value='$s_promo_pro' size='4' style='text-align:right'></td>
		";
		 }
		 else{
			 echo"
			 <td align='right'><a class='info'>".number_format($s_prix,2,',','')."<span>(".number_format($s_prix*$s_com/100,2)."&euro;)</span></a></td>
			<td align='right'><strike>".number_format($s_promo,2,',','')."</strike></td>
			<td><input type='text' name='libre_pro$s_id' value='$s_libre_pro' size='8'></td>
			<td class='cadre' style='border-width:0px;border-left-width:1px;text-align:right'><a class='info'>".number_format($s_prix_pro,2,',','')."<span>(".number_format($s_prix_pro*$s_com/100,2)."&euro;)</span></a></td>
			<td class='cadre' style='border-width:0px;border-right-width:1px;text-align:right'><strike>".number_format($s_promo_pro,2,',','')."</strike></td>
			";
		 }
	 }
	 else{
		 if($u_droits == '' || $u_dgw == 1 ){
			 echo"
			 <td class='cadre' style='border-width:0px;border-left-width:1px'><input type='text' name='prix$s_id' value='$s_prix' size='4' style='text-align:right'  onkeyup='ch_vente($s_id,\"pa\",this.value)'></td>
			<td class='cadre' style='border-width:0px;border-right-width:1px;'><input type='text' name='promo$s_id' value='$s_promo' size='4' style='text-align:right'></td>
			<td><input type='text' name='libre_pro$s_id' value='$s_libre_pro' size='8'></td>
			<td><input type='text' name='prix_pro$s_id' value='$s_prix_pro' size='4' style='text-align:right'  onkeyup='ch_vente($s_id,\"pro\",this.value)'></td>
			<td><input type='text' name='promo_pro$s_id' value='$s_promo_pro' size='4' style='text-align:right'></td>
			";
		 }
		 else{
			 echo"
			 <td class='cadre' style='border-width:0px;border-left-width:1px;text-align:right'><a class='info'>".number_format($s_prix,2,',','')."<span>(".number_format($s_prix*$s_com/100,2,',','')."&euro;)</span></a></td>
			<td class='cadre' style='border-width:0px;border-right-width:1px;text-align:right'><strike>".number_format($s_promo,2,',','')."</strike></td>
			<td><input type='text' name='libre_pro$s_id' value='$s_libre_pro' size='8'></td>
			<td align='right'><a class='info'>".number_format($s_prix_pro,2)."<span>(".number_format($s_prix_pro*$s_com/100,2,',','')."&euro;)</span></a></td>
			<td align='right'><strike>".number_format($s_promo_pro,2,',','')."</strike></td>
			";
		 }
	 }
	 if($u_droits == '' || $u_dgw == 1 ){
	 	echo"				
		<td><input type='text' name='tva$s_id' value='$s_tva' size='3'></td>
		<td><input type='text' name='stock$s_id' value='$s_stock' size='2'></td>
		<td width='60' valign='middle' align='left'><a name='l$s_id'></a>";
		if($u_droits == '' || $u_active == 1 ){
			//echo"
			//<a href='#l$s_id' onclick='confsupt($s_id,$i)'><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>";
			echo"<input type='checkbox' name='stch$s_id' value='$s_id'>&nbsp;<a href='#l$s_id' onclick=\"document.fourmis.action+='&l_a&$setopo[$s_active]=$s_id&effdb=gestion_artstock#l$s_id';document.fourmis.submit()\" class='info'>&nbsp;<img src='$style_url/$theme/v$s_active.gif' id='img$s_id' border='none' alt='actif: $s_active'><span>$setopot[$s_active]</span></a>";
		}
		else{
			echo"<img src='$style_url/$theme/v$s_active.gif' border='none' alt='actif: $s_active'>";
		}
	 }
	 else{
	 	echo"				
		<td>$s_tva</td>
		<td>$s_stock</td>
		<td width='60' valign='middle' align='left'>
		<img src='$style_url/$theme/v$s_active.gif' border='none' alt='actif: $s_active'>";
	 }
					
					
					echo"</td>
	 		</tr>";
		 }
	
/*	if($u_droits == '' || $u_dgw == 1 ){	
	 echo"<tr class='buttontd'> ";
	 if($taxe_cible=='HT'){
		 echo"
		 <td colspan='9'></td>
		 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
		 <td colspan='3' align='right'>
		";
	 }
	 else{
		 echo"
		<td colspan='7'></td>
		 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
		 <td colspan='4' align='right'>";
	 }
	 echo"
		 <input type='button' value='Ajouter' class='buttontd' onclick=\"addinref('$clasnom','$ligne',true);\">
		 	
			
		 </td></tr>
		 <tr>";
	} */
	 if($taxe_cible=='HT'){
		 echo"
		 <td colspan='10'></td>
		 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
		 <td colspan='3'></td>
		";
	 }
	 else{
		 echo"
		<td colspan='8'></td>
		 <td colspan='2' class='cadre' style='border-width:0px;border-left-width:1px;border-right-width:1px'>&nbsp;</td>
		 <td colspan='5'></td>";
	 }
	 echo"</tr>";
	  	if(isset($fourtruc)  ){
			//$fourtruc.="<tr><td style='height:60px;_height:75px;'>&nbsp;</td></tr>";
			$fourh+=60;
		}
	 }
	 echo"
	 ";
	 if($u_droits == '' || $u_dgw == 1 ){
		 echo"<tr>";
		 if($taxe_cible=='HT'){
			 echo"
			 <td colspan='10' align='left' valign='top'>
			 </td>
			 <td colspan='2' class='cadre' style='border-width:1px;border-top-width:0px'>&nbsp;</td>
			 <td colspan='3 align='right' valign='bottom'>
					
			 </td>
			";
		 }
		 else{
			 echo"
			<td colspan='8' align='left' valign='top'>
			</td>
			 <td colspan='2' class='cadre' style='border-width:1px;border-top-width:0px'>&nbsp;</td>
			 <td colspan='5' align='right' valign='bottom'>
					
			 </td>";
		 }
		 echo"</tr>";
	 }
	 echo"</table>
	 <a onclick=\"document.getElementById('newtai').style.visibility='visible';\" class='buttontd'>Ajouter ".$rang_names['taille']."</a>
		<a onclick=\"document.getElementById('newcol').style.visibility='visible';\" class='buttontd'>Ajouter ".$rang_names['couleur']."</a>
		
		<div style='position:relative'>
	 	<div id='newtai' class='buttontd' style='position:absolute; visibility:hidden;'>
		<b>".ucfirst($rang_names['taille'])." : </b>
			<table><tr><td>entrées existantes</td><td>Nouvelle entrée</td><td></td></tr>
			<tr><td>
			<select onchange=\"addinref('taille',this.value);\">
			<option value=''></option>
				";
				
				if(isset($gestion_articles_taille_liste)){
					foreach($gestion_articles_taille_liste as $accval){
						echo"<option value=\"$accval\">$accval</option>";
					}
					echo'</select>';
				}
				else{
					$res = mysql_query("SELECT DISTINCT `taille` FROM `gestion_artstock`");
				 if($res && mysql_num_rows($res)>0){
					while($rou=mysql_fetch_array($res)){
						echo"<option value=\"$rou[0]\">".ucfirst($rou[0])."</option>";
					}
				 }
				echo"
			</select>
			</td><td><input type='text' value='' name='newtail'><input type='button' value='ok' class='buttontd' onclick=\"addinref('taille',document.fourmis.newtail.value);\">";
				}
				
			
			echo"
			
			</td><td>
			<a onclick=\"document.getElementById('newtai').style.visibility='hidden';\" class='buttontd'>Annuler</a>
			</td></tr></table>
		</div>
		<div id='newcol' class='buttontd' style='position:absolute; visibility:hidden;'>
		<b>".ucfirst($rang_names['couleur'])." : </b>
			<table><tr><td>entrées existantes</td><td>Nouvelle entrée</td><td></td></tr>
			<tr><td>
			<select onchange=\"addinref('couleur',this.value);\">
			<option value=''></option>
				";
				if(isset($gestion_articles_couleur_liste)){
					foreach($gestion_articles_couleur_liste as $accval){
						echo"<option value=\"$accval\">$accval</option>";
					}
					echo'</select>';
				}
				else{
				$res = mysql_query("SELECT DISTINCT `couleur` FROM `gestion_artstock`");
				 if($res && mysql_num_rows($res)>0){
					while($rou=mysql_fetch_array($res)){
						echo"<option value=\"$rou[0]\">".ucfirst($rou[0])."</option>";
					}
				 }
				echo"
			</select>
			</td><td><input type='text' value='' name='newcolo'><input type='button' value='ok' class='buttontd' onclick=\"addinref('couleur',document.fourmis.newcolo.value);\">";
				}
			echo"
			
			</td><td>
			<a onclick=\"document.getElementById('newcol').style.visibility='hidden';\" class='buttontd'>Annuler</a>
			</td></tr></table>
		</div>
	 </div>
	 
	 
</div>";
	
	
	
	
/**************************************************************************************************************************** AHATS
*/

echo"<div id='achafour' class='buttontd' style='position:relative;display:$vis2;  border-width:2px; text-align:left;'>
				
					 <b>Prix d'achats</b><br>&nbsp;<span id='gaf_retour'></span>
					 
					 <table id='quantfour' style='background:#DDD ;position:absolute;visibility:hidden; top:35px; left:0px; width:895px;height:$fourh"."px;'><tr><td valign='middle' align='center'>";
	 if(isset($fournisseurs_db) && ($u_droits == '' || $u_dgw == 1) &&  mysql_query("SHOW COLUMNS FROM $fournisseurs_db") && mysql_query("SHOW COLUMNS FROM `gestion_artfour`") ){
		 
		 $verifupdt = mysql_query("DESC `gestion_artfour`");
		$allchamps = array();
		while($roi = mysql_fetch_object($verifupdt)){
			array_push($allchamps,$roi->Field);
		}
		if(!in_array("tva",$allchamps)){
			mysql_query("ALTER TABLE `gestion_artfour` ADD `tva` float(10,2) NOT NULL default '0'");
		}
		 
		 
		 
		 

	$fourh+=30;
			echo"
					 <span id='comref'></span><br>
					 <b>En commande :</b><br>
			 			<input type='hidden' name='gafref' value='0'>
						<input type='text' name='gafquant' value='1' size='4'>
						<input type='button' class='buttontd' value='ok' onclick=\"commande()\">
						<a class='buttontd' style='cursor:pointer' onclick=\"decom()\">Annuler</a>
					 </td></tr></table>
					 
					 
					 <br><br>
					 <div id='achatfour' class='cadre'>
					 <table cellspacing='0' cellpadding='2' class='gaf_table'>
						$fourtruc
					</table>
					 </div>
					 <br>
					 <a class='buttontd' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&update&fours=all&addfour=all#four$s_id';document.fourmis.submit();\">Ajouter</a>
					 <br>
				 </div>			 
			
			";
	 }
	 else{
		 echo"<a class='buttontd' href='./?option=$option&part=$part&edit=$edit&create_fournisseurs_db=1'>Installer le module achats / fournisseurs</a>";
	 }
	 echo"
	 
	";
	 }
/**************************************************************************************************************************** AUTRES
*/ 
//////////////////////////////////////////////////////////////////////// ASSOC
	 echo"
	 <div id='otherplus' class='buttontd' style='position:relative;display:$vis3;  border-width:2px; text-align:left;'>
	 
	<div class='cadre'>
	
	<b>Articles associés</b><hr>
	 ";
	 if($u_droits == '' || $u_dgw == 1 ){
		echo"<input type='hidden' name=\"conseil\" value=\"$art_conseil\">
						
				  ";
		$c=0;
		$hot=46;
		$ch=0;
		$prh='';

$listrays = mysql_query("SELECT DISTINCT `$rayons_db`.`id`,`gestion_raytrad`.`nom` FROM `$rayons_db`,`gestion_raytrad` 
WHERE 
	`gestion_raytrad`.`ref`=`$rayons_db`.`id`
GROUP BY `$rayons_db`.`id`
ORDER BY `gestion_raytrad`.`nom`");
		while($raylist = mysql_fetch_object($listrays)){
			$raaynom = $raylist->nom;
			$raayid = $raylist->id;				
						$listres = mysql_query("SELECT DISTINCT `$articles_db`.`id`,`gestion_artrad`.`nom` FROM `gestion_articles`,`gestion_artrad` 
WHERE 
	`$articles_db`.`rayon`='$raayid'
AND	`gestion_artrad`.`ref`=`gestion_articles`.`id`
AND	`gestion_articles`.`id`!='$edit'
GROUP BY `gestion_articles`.`id`
ORDER BY `gestion_artrad`.`nom`");
				$prh.="<br><b>$raaynom</b>";
				$hot+=23;
						while($rowlist = mysql_fetch_object($listres)){
							$rowvalue = $rowlist->nom;
							$rowid = $rowlist->id;
							$se = '';
							$c++;
							if(ereg('<'.$rowid.'>',$art_conseil)){
								$se = 'checked';
								$ch++;
							}
							$hot+=23;
							$prh.="<li><input type='checkbox' name='cons$c' onclick=\"oldv=parseInt(document.fourmis.ch_co.value);if(this.checked==true){if(document.fourmis.conseil.value.indexOf('<$rowid>')==-1){document.fourmis.conseil.value+='<$rowid>';oldv++;}}else{document.fourmis.conseil.value=document.fourmis.conseil.value.replace('<$rowid>','');oldv--;}document.fourmis.ch_co.value=oldv;\" $se>$rowvalue</li>";
						}
			}
						if($hot>300) $hot=300;
						echo"<a href='#conse' name='conse' onclick=\"dec('conseil',$hot)\"><b>v Développer v</b></a>
						<input type='text' name=\"ch_co\" value=\"$ch\" style='border:none;background:none;width:30px;text-align:right' readonly> / $c sélectionnés<br>
				  		<div id='conseil' style='display:block;width:380px;height:$hot;overflow:scroll;'>
						<a href='#conse' onclick=\"dec('conseil',1)\"><b>^ réduire ^</b></a>
						<ul>						
						$prh
						</ul>
						<a href='#conse' onclick=\"dec('conseil',1)\"><b>^ réduire ^</b></a>
						</div>
						<script language='javascript' type='text/javascript'>
						dec('conseil',1);
						</script>";	 
	 }
	 else{
		
$listrays = mysql_query("SELECT DISTINCT `$rayons_db`.`id`,`gestion_raytrad`.`nom` FROM `$rayons_db`,`gestion_raytrad` 
WHERE 
	`gestion_raytrad`.`ref`=`$rayons_db`.`id`
GROUP BY `$rayons_db`.`id`
ORDER BY `gestion_raytrad`.`nom`");
		while($raylist = mysql_fetch_object($listrays)){
			$raaynom = $raylist->nom;
			$raayid = $raylist->id;				
						$listres = mysql_query("SELECT DISTINCT `$articles_db`.`id`,`gestion_artrad`.`nom` FROM `gestion_articles`,`gestion_artrad` 
WHERE 
	`$articles_db`.`rayon`='$raayid'
AND	`gestion_artrad`.`ref`=`gestion_articles`.`id`
AND	`gestion_articles`.`id`!='$edit'
GROUP BY `gestion_articles`.`id`
ORDER BY `gestion_artrad`.`nom`");
				while($rowlist = mysql_fetch_object($listres)){
					$rowvalue = $rowlist->nom;
					$rowid = $rowlist->id;
					if(ereg('<'.$rowid.'>',$art_conseil)){
						echo"<br><b>$raaynom</b> : $rowvalue";
					}
					
				}
			}
				
	
	
	 }
	 
	 $row = $columns-sizeof($legal_entrys);
	if($row<=0)$row=1;
	$row++;
	 echo"	<table>
<tr>
		<td colspan='2' valign='top'><br><br><b>Champs supplémentaires</b><hr></td>
		<td rowspan='$row' valign='top'><br><br><br><hr>
		";
		if($u_droits == '' || $u_dgw == 1 ){
			if(isset($fichiers[$part])){	
			insert('_fichiers');
			if(is_file('bin/_fichiers.php')){
				include('bin/_fichiers.php');
			}
			else{
				include('$style_url/update.php?file=_fichiers.php&1');
			}
		   }
		}
		echo"
		</td>
		</tr>";
//////////////////////////////////////////////////////: CHAMPS PERSONNALISES
for ($i = 0; $i < $columns; $i++) {
	$field_name = mysql_field_name($res_field, $i);
	if(!in_array($field_name,$legal_entrys)){
		
			$field_act = $field_name;
			if(isset($r_alias[$part][$field_name])){
				$field_act = $r_alias[$part][$field_name];
			}
			$field_type = mysql_field_type($res_field, $i);			
			$field_length = abs(mysql_field_len($res_field, $i));
			if(isset($_GET['refresh'])){
				$field_value = stripslashes($_POST[$field_name]);
			}
			else{
				$field_value = $ro->$field_name;	
			}
			$field_width=$field_length*12;
			if($field_width > 300){
				$field_width=300;
			}
			$nameifthefield = ereg_replace(">"," ",$field_name);	
			if(isset($alias[$part][$field_name])){
				$nameifthefield = $alias[$part][$field_name];
			}
			//$baz = substr($field_name,0,strpos($field_name,'_'));
			echo"<tr>";
			/////////////////////////////////////// COULEUR
			if(ereg("couleur",$field_name) && $field_length==6){
					if($field_value==""){
						$field_value="FFFFFF";

}
					if($u_droits == '' || $u_dgw == 1 ){
			 	echo"<td valign='top'>couleur</td><td valign='top'><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
				 	#<input type=\"text\" name=\"$field_name\" value=\"$field_value\" maxlength='6' size='4' onchange=\"document.getElementById('div$field_name').style.backgroundColor='#'+this.value\">
						<div id=\"div$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:20px;width:30px;border-color:#000000;border-style:solid;border-width:1px\"></div>
						<a href='#a$field_name' name='a$field_name' onclick=\"choosecolor($i,'Backcolor','$field_name','hexa',event)\">changer la couleur</a>
				 </td>";
					}
					else{
						echo"<td valign='top'>couleur</td><td><div id=\"div$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:20px;width:30px;border-color:#000000;border-style:solid;border-width:1px\">&nbsp;</div> </td>";
					}
			 }
			 /////////////////////////////////////// CARTE
			 elseif($field_type == "int" && isset($mapcoord[$part]) && is_file('../'.$mapcoord[$part]) && ($field_name == "x" || $field_name == "y") ){
			 	if(	$field_name == "x"){
					$cx = $ro->x;	
					$cy = $ro->y;
					$getsi  = getimagesize('../'.$mapcoord[$part]);
					$minx = $getsi[0];
					if($minx > 300) $minx=300;  
					$miny = $getsi[1];
					if($miny > 300) $miny=300;  
					 echo"<td valign='top'><a class='info'>Position<span>coordonnées <b>XY</b></span></a></td><td valign='top'>
					<script language=\"JavaScript\">
					function point_it(event){
						pos_x = event.offsetX?(event.offsetX):event.pageX-document.getElementById(\"position\").offsetLeft;
						pos_y = event.offsetY?(event.offsetY):event.pageY-document.getElementById(\"position\").offsetTop;
						document.getElementById(\"poscros\").style.left = (pos_x-5) ;
						document.getElementById(\"poscros\").style.top = (pos_y-5) ;
						document.fourmis.x.value = pos_x;
						document.fourmis.y.value = pos_y;
					}
					</script>

					 x:<input type=\"text\" name=\"x\" value=\"$cx\" style=\"width:30px\" maxlength=\"4\" onkeyup=\"document.getElementById('poscros').style.left = (parseInt(this.value)-5)\">
					 |
					 y:<input type=\"text\" name=\"y\" value=\"$cy\" style=\"width:30px\" maxlength=\"4\" onkeyup=\"document.getElementById('poscros').style.top = (parseInt(this.value)-5)\"><br>
					 <div id='position' class='cadre' style='position:relative;display:block;width:$minx;height:$miny;overflow:hidden;padding:0px' onMouseOver='this.style.width=$getsi[0];this.style.height=$getsi[1];' 
onClick='this.style.width=$getsi[0];this.style.height=$getsi[1];' onMouseOut='this.style.width=$minx;this.style.height=$miny;'
onblur='this.style.width=$minx;this.style.height=$miny;'><img id='imgcoor' src='../$mapcoord[$part]' border='none'  onclick='point_it(event)'>
					 <div id='poscros' style='position:absolute;left:$cx;top:$cy;width:10;height:10;font-size:10px;border-width:1px;border-color:#00000;border-style:dashed;font-color:#FFFFFF;background-color:#FF0000'>+</div>
					 </div>
					 </td>";
				 }
			 }
				
			 ///////////////////////////////////// PREFIXE
			 elseif(substr($field_name,0,1) == "_"){
				if($nameifthefield == $field_name){
					$nameifthefield = substr($field_name,1,strlen($field_name));
				}
				if($field_value==""){
					$field_value=$_SESSION[$field_name];
				}
				if($u_droits == '' || $u_dgw == 1 ){
				echo"<td valign='top'>$nameifthefield</td><td valign='top'><img src='$style_url/$theme/mysqltype-special.png' alt='special'><input type=\"text\"  id='pref_txt_$i' name=\"$field_name\" value=\"$field_value\" style=\"width:300px;border:none\" class=\"bando\" readonly onfocus=\"this.style.width='1px';document.getElementById('pref_sel_$i').style.display='inline';document.pref_sel_$i.focus();\">
				 <select id='pref_sel_$i' name='pref_sel_$i' onchange=\"javascript:set$nameifthefield(this.value);this.value=' ';document.fourmis.action=document.fourmis.action.replace(new RegExp('&update'), '&refresh').replace(new RegExp('&add'), '&refresh');document.fourmis.submit();\" onblur=\"this.style.display='none';document.getElementById('pref_txt_$i').style.width='300px';\" style=\"width:300px;display:none;\">
				 	<option value=' '>-- $nameifthefield --</option>";
					$listres = mysql_query("SELECT DISTINCT `$field_name` FROM `$tabledb` $incwhere $prefixselection");
					$prefixselection.=" AND `$field_name`='$field_value'";
					while($rowlist = mysql_fetch_object($listres)){
						$rowvalue = $rowlist->$field_name;
						$s='';
						if($rowvalue==$field_value) $s='selected';
						echo"<option value=\"$rowvalue\" $s>$rowvalue</option>";
					}
					echo"
					<option value=''>-- nouveau -- </option>
				 </select>
				 <script language='javascript' type='text/javascript'>
				 	function set$nameifthefield(koi){
						if(koi == ''){
							pro = prompt(\"veuillez entrer un nom pour le nouvel élément\",\"nouveau\");
							if(pro){
								document.fourmis.$field_name.value=pro;
							}							
						}
						else{
							document.fourmis.$field_name.value=koi;
						}						
					}
				 </script>				 
				 </td>";	
				}
				else{
					echo"<td valign='top'>$nameifthefield</td><td valign='top'>$field_value</td>";
				}
			}
			 ///////////////////////////////////// SUFIXE
			 elseif( ereg("_",$field_act) && ( mysql_query("SHOW COLUMNS FROM ".substr($field_act,0,strpos($field_act,'_'))) || ereg('@',$field_act) ) ){
			 	$refiled = substr($field_act,0,strpos($field_act,"_"));
				$fieldoption = substr($field_act,strpos($field_act,"_")+1,strlen($field_act));
				if($nameifthefield == $field_act){
					$nameifthefield = $refiled;
				}
				if(ereg(">",$field_act)){
					$fieldoption = substr($fieldoption,0,strpos($fieldoption,">"));
					$nameifthefield .= " : ".substr($field_act,strpos($field_act,">")+1,strlen($field_act));
				}
				
				
				if(substr($fieldoption,0,1) == "@"){
					$nameofoption = substr($fieldoption,1,strlen($fieldoption));	
					$field_value = $_SESSION[$nameofoption];	
					echo"<td valign='top'>$nameifthefield <a class='info'><img src='$style_url/$theme/pile.gif'>
					<span>Ce champ est à un élément personnel de session <b>$nameofoption</b></span></a></td><td valign='top'>
					 <img src='$style_url/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:100px\" class=\"bando\" readonly>
					 </td>";		
				}
				else{				
					$fieldoptions = split("_",$fieldoption);
					$fieldoptionprint = $fieldoptions[1];
					$fieldoption = $fieldoptions[0];
					$refiled = trim($refiled);	
					if($prefixe!=""){
						$nameifthefield = trim(ereg_replace($prefixe,"",$nameifthefield));
					}
					echo"<td valign='top' valign='top'>$nameifthefield <a class='info'><img src='$style_url/$theme/pile.gif'>
					<span>Ce champ est relié au tableau <b>$refiled</b></span></a></td><td valign='top'>
					 <img src='$style_url/$theme/mysqltype-special.png' alt='special'>";
				   if((sizeof($fieldoptions)==3 && $fieldoptions[2]=='ch') || (sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlch')){
				   		if($u_droits == '' || $u_dgw == 1 ){
					  echo"<input type='hidden' name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\">
				  ";
						$c=0;
						$hot=46;
						$ch=0;
						$prh='';
						$hut=0;
						$seled = '';
						if(sizeof($fieldoptions)==3){
							$listres = mysql_query("SELECT `$fieldoptionprint`,`$fieldoption`,`id` FROM `$refiled`  WHERE `$fieldoptionprint`!='' ORDER BY `$fieldoptionprint`");
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
								$prh.="<li><input type='checkbox' name='cho$i$c'   value=\"$rowid\"  title=\"$rowvaluu\" onclick=\"rempli(document.getElementById('ulch_$i'),document.fourmis.$field_name,document.fourmis.ch_cu_$i,document.getElementById('chu_$i'))\" $se>$rowvalue <a href='./?$refiled&edit=$roid'>></a></li>";
							}
						}
						if(sizeof($fieldoptions)==2){
							$listres = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled` WHERE `$fieldoptions[0]`!=''");
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
								$prh.="<li><input type='checkbox' name='cho$i$c' value=\"$rowvaluu\"  title=\"$rowvaluu\"  onclick=\"rempli(document.getElementById('ulch_$i'),document.fourmis.$field_name,document.fourmis.ch_cu_$i,document.getElementById('chu_$i'))\" $se>$rowvalue</li>";
							}
						}
						
						if($hot>300) $hot=300;
						echo"
						<script language=\"JavaScript\">
						hut = $hut;
						</script>
						<a href='#ch$i' name='ch$i' onclick=\"dec('ch_$i',$hot);dec('chu_$i',1)\"><b><img src='$style_url/$theme/class_down_off.jpg' alt='v' border='none'> Développer <img src='$style_url/$theme/class_down_off.jpg' alt='v' border='none'></b></a>
						<input type='text' name=\"ch_cu_$i\" value=\"$ch\" style='border:none;background:none;width:30px;text-align:right' readonly> / $c sélectionnés<br>
				  		<div id='ch_$i' style='display:block;width:380px;height:1px;overflow:hidden;'>
						<a href='#ch$i' onclick=\"dec('ch_$i',1);dec('chu_$i',hut)\"><b><img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'> réduire <img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
						<li><input type='checkbox' name='all$i$c' onclick=\"tout(document.getElementById('ulch_$i'),this, document.fourmis.$field_name,document.fourmis.ch_cu_$i,document.getElementById('chu_$i'))\"> Tout</li>
						<ul id='ulch_$i'>	
						
						$prh
						</ul>
						<a href='#ch$i' onclick=\"dec('ch_$i',1);dec('chu_$i',hut)\"><b><img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'> réduire <img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
						</div>
						<div id='chu_$i' style='display:block;width:380px;height:$hut"."px;overflow:hidden;'  onclick=\"dec('ch_$i',$hot);dec('chu_$i',1)\">						
						$seled
						</div>
						
						";	
						}
						else{					  
							if(sizeof($fieldoptions)==3){
								$listres = mysql_query("SELECT `$fieldoptionprint`,`$fieldoption`,`id` FROM `$refiled`  WHERE `$fieldoptionprint`!='' ORDER BY `$fieldoptionprint`");
								while($rowlist = mysql_fetch_array($listres)){
									$rowvalue = $rowlist[0];
									$rowid = $rowlist[1];
									if(ereg('<'.$rowid.'>',$field_value)){
										echo"$rowvalue<br>";
									}
								}
							}
							if(sizeof($fieldoptions)==2){
								$listres = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled` WHERE `$fieldoptions[0]`!=''");
								$rowlist = mysql_fetch_array($listres);
								$gvl = explode("\n",$rowlist[0]);
								foreach($gvl as $rowvalue){
									$rowvalue=trim($rowvalue);
									if(ereg('<'.$rowvalue.'>',$field_value)){
										echo"$rowvalue<br>";
									}
								}
							}	
						}
		
					
		
					}	
					else{
						if($u_droits == '' || $u_dgw == 1 ){
							 echo"<select name=\"$field_name\" style=\"width:300px\">
							<option value=' '>liste des choix</option>";
							$listres = mysql_query("SELECT * FROM `$refiled` ORDER BY `$fieldoptionprint`");
							while($rowlist = mysql_fetch_object($listres)){
								$rowvalue = $rowlist->$fieldoptionprint;
								$rowid = $rowlist->$fieldoption;
								$se = "";
								if($rowid == $field_value){
									$se = "selected";
								}
								echo"<option value=\"$rowid\" $se>$rowvalue</option>";
							}
							echo"</select>";
						}
						else{
							 
							$listres = mysql_query("SELECT `$fieldoptionprint` FROM `$refiled` WHERE `$rowid`='$field_value' ORDER BY `$fieldoptionprint`");
							$rowlist = mysql_fetch_array($listres);
							echo $rowlist[0];
						}
					}
					echo"</td>";
				 }	 
				 
			}
			/////////////////////////////////////// STRING
			 elseif($field_type == "string"){		
			 	if($u_droits == '' || $u_dgw == 1 ){
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td valign='top'><img src='$style_url/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:$field_width"."px\" maxlength=\"$field_length\"></td>";
				}
				else{
					echo"<td valign='top'>$nameifthefield</td><td valign='top'>$field_value</td>";
				}
			 }
			 /////////////////////////////////////// INT
			 elseif($field_type == "int" || $field_type == "real"){	
			 	if($u_droits == '' || $u_dgw == 1 ){
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Nombre</span></a></td><td valign='top'><img src='$style_url/$theme/mysqltype-int.png' alt='numérique'><input type=\"text\" name=\"$field_name\" value=\"$field_value\" style=\"width:150px\" maxlength=\"$field_length\"></td>";
				 }
				else{
					echo"<td valign='top'>$nameifthefield</td><td valign='top'>$field_value</td>";
				}
			 }
			 /////////////////////////////////////// DATE
			 elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
			 	if($field_value==""){
					$field_value=$defaultvalue[$field_type];
				}
				if($u_droits == '' || $u_dgw == 1 ){
				 echo"<td valign='top'><a class='info'>$nameifthefield<span>Date au format standard<br>date: aaaa-mm-jj<br>heure: hh:mm:ss</span></a></td><td valign='top'>
				 <img src='$style_url/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"javascript:contextdate(event);cal.location='./?incpath=calendar.php&#63;x_id=$x_id&amp;cible=fourmis.$field_name&amp;date='+document.fourmis.$field_name.value+'&amp;type=$field_type'\"><input type='text' name=\"$field_name\" value=\"$field_value\" maxlength=\"$field_length\">
				 </td>";
				}
				else{
					echo"<td valign='top'>$nameifthefield</td><td valign='top'>$field_value</td>";
				}
			 }
		
		
		
		
		
		
		
		
		
		
		
		
		
		echo"
		</tr>
		";
	}
	echo"";
}	 
	 
	 echo"</table>
	 </div></div> ";

?>