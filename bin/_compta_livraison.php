<?php // 5 > Contrôle des livraisons ;
	echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>$part</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='left'>
	";
	///////////////////////////////////////////////////////////////////////////////////////////// EDITION
	if(isset($_GET['edit'])){
		$result = mysql_query("SELECT * FROM $compta_base WHERE id='$edit'");
		$row = mysql_fetch_object($result);
		$pdfact=array();
		$uid = $edit;
		$numero = $row->numero;
		$adresse = $row->adresse;
		$clientfacture = $row->client;
		$type = $row->type;
		$code = $row->code;
		$intitule=$row->intitule;
		$date=$row->date;
		$acompte=$row->acompte;
		$acomptele = $row->acomptele;
		$etat = $row->etat;
		$mode = $row->mode;
		$content = explode("<!>",$row->content);
		$i=0;
		if(ereg("Livraison",$adresse)){
			$adresse = substr($adresse,strpos($adresse,"Livraison"),strlen($adresse));
		}
		if(ereg("livraison",$adresse)){
			$adresse = substr($adresse,strpos($adresse,"livraison"),strlen($adresse));
		}
		echo"
		$code$numero
		<p>$adresse</p>
		<style>
			#formrecep form{
				white-space:nowrap;
			}
		</style>
		<script language='javascript'>
			function addquant(li,qu){
				
			}
			function defart(li,qu){
				if(!envoyer('bin/inc_ajax.php?scan=compta_livraison','w','defart&ref=$edit&ligne='+li+'&def='+qu,'live'+li)){
					document.getElementById('live'+li).innerHTML =\"Erreur\";
				}
			}
		</script>
		<table class='cadre' cellpadding='5' id='formrecep'>
		<tr class='buttontd'>
			<td>Article</td>
			<td>Commandé</td>
			<td>Déjà reçu</td>
			<td>Livré</td>
		</tr>";
		foreach($content as $ligne ){
			$lignecontent = explode("<>",$ligne);
			$label= $lignecontent[0];
			$type= $lignecontent[1];
			$pu= $lignecontent[2];
			$tva= $lignecontent[3];
			$quant= $lignecontent[4];
			$afid= str_replace('GAF_','',$lignecontent[5]);
			$arid= get('gestion_artfour','art',$afid);
			$arcode = get('gestion_artfour','reference',$afid);
			$id= get('gestion_artstock','ref',$arid);
			$coderef= $lignecontent[6];
			$hidden = $lignecontent[7];
			$remise = abs($lignecontent[8]);
			$i++;
			if($label!=''){
			echo"<tr>
				<td><b>$label</b><br>$coderef
				<div id='live$i'>";
				if($id!='' && false !== $res=mysql_query("SELECT `id` FROM `gestion_articles` WHERE `id`='$id'") && $arcode==$coderef){
					 echo" <a href='./?option=gestion&part=gestion_articles&edit=$id'>Consulter</a>";
				}
				else{
					if(!isset($replaceval)){
						$replaceval="
						L'article $arcode n'a pas été reconnu . 
						<select name='kelval'><option value=''>Définir l'artile</option>";								
						$listrays = mysql_query("SELECT DISTINCT `$rayons_db`.`id`,`gestion_raytrad`.`nom`
						FROM `$rayons_db`,`gestion_raytrad` 
						WHERE 
							`gestion_raytrad`.`ref`=`$rayons_db`.`id`
						GROUP BY `$rayons_db`.`id`
						ORDER BY `gestion_raytrad`.`nom`");
						while($raylist = mysql_fetch_object($listrays)){
							$raaynom = $raylist->nom;
							$raayid = $raylist->id;				
							$listres = mysql_query("SELECT DISTINCT `$articles_db`.`id`,`gestion_artrad`.`nom`,`gestion_artstock`.`id` ,`gestion_artstock`.`code` ,`gestion_artfour`.`reference` ,`gestion_artfour`.`id` 
							FROM `gestion_articles`,`gestion_artrad` ,`gestion_artstock` ,`gestion_artfour` 
							WHERE 
								`$articles_db`.`rayon`='$raayid'
							AND	`gestion_artstock`.`ref`=`gestion_articles`.`id`
							AND	`gestion_artstock`.`id`=`gestion_artfour`.`art`
							AND	`gestion_artfour`.`four`='$clientfacture'
							AND	`gestion_artrad`.`ref`=`gestion_articles`.`id`
							
							ORDER BY `gestion_artrad`.`nom`");
							//GROUP BY `gestion_articles`.`id`
							$replaceval.="<optgroup label='$raaynom'>";						
							while($rowlist = mysql_fetch_array($listres)){
								$rowvalue = $rowlist[1];
								$rowid = $rowlist[2];
								$rowref = $rowlist[3];
								$rowcode = $rowlist[4];
								$rowfid = $rowlist[5];
								$replaceval.="<option value='$rowfid'>$rowcode $rowvalue $rowref</option>";
							}
							$replaceval.="</optgroup>";
						}
						$replaceval.="</select><input type='submit' class='buttontd' value='ok' />
						";
					}
					echo "<form action='#' method='post' name='form$i'  id='retou$i' onsubmit='defart($i,this.kelval.value); return false;'>$replaceval</form>";
				}
				echo"</div></td>
				<td align='center'>$quant</td>
				<td><input type='text' name='lastquant$i' value='0' readonly size='3'></td>
				<td>";
				if($id!=''){
					 echo"<form action='#' method='post' name='form$i'  id='retou$i' onsubmit='alert(\"false\"); return false;'>
						<input type='text' size='3' min='0' value='0'>
						<input type='submit' class='buttontd' value='ok' />
					</form>";
				}
				echo"
				</td>
			</tr>";
			}
		}
		echo"</table>";

	}
	///////////////////////////////////////////////////////////////////////////////////////////// LISTE
	else{
		 $wheredb="WHERE `type`='achat'";
		 echo"
	<script language='javascript'>
	function sela(k){
		var allche = document.listage.getElementsByTagName(\"input\");
		for (var i=0; i<allche.length; i++) {
			allche[i].checked=k;
		}
	}
	</script>
	<form name='listage' action='./?option=$option&part=$part' method='post'>
	
	";
	$tabledb = $compta_base;
	insert("inc_cliste");
	if(is_file("bin/inc_cliste.php")){
		include("bin/inc_cliste.php");
	}
	else{
		include("$style_url/inc_cliste.php");
	}
	echo"</form>";
	}
	echo"</td></tr></table>";
?>