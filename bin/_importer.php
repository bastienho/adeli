<?php // 24 > Importation de fichiers de base de données ;
$conn = connecte($base, $host, $login, $passe);
$res_field = mysql_list_fields($base,$tabledb);
$columns = mysql_num_fields($res_field);
//////////////////////////////////////////////////////////////////////[ IMPORT UPLOAD ]
if(isset($_POST['uploud'])){
	$allcons = explode("\n",$_SESSION["imp"]);	
	

		$chamdeva = array();
		$vadecham = array();
		$mkch = split("[,;]",$allcons[0]);
		for($e=0 ; $e<sizeof($mkch)-1 ; $e++){
			if(isset($_POST["champ$e"]) && $_POST["champ$e"]!=""){
				$chamdeva[$_POST["champ$e"]]=$e;
				$vadecham[$e]=$_POST["champ$e"];
			}
		}
		$nbc = sizeof($allcons);			
		 echo returnn("tentative d'importation de $nbc éléments","FF9900",$vers,$theme);	   
		for($m=0 ; $m<$nbc ; $m++){
			if(trim($allcons[$m])!=""){
				$verif="";
				$ligne = split("[,;]",$allcons[$m]);
					$hea = "";						   
				   $command = "";						   
				   for ($i=0 ; $i < $columns; $i++) {
					$field_name = trim(mysql_field_name($res_field, $i));
					if( isset($chamdeva[$field_name]) ){
						$field_value = '';
						for($e=0 ; $e<sizeof($mkch)-1 ; $e++){
							if($field_name == $vadecham[$e]){
								$field_value .= str_replace("'","''",unquote($ligne[$e])).' ';
							}
						}									
						$hea.="`$field_name`, ";
						$command.="'$field_value', ";
					}
				   }

			   $videcom = trim(str_replace(",","",str_replace("'","",$command)));
			  if($videcom != ""){
				   $command = substr($command,0,strlen($command)-2);
				   $hea = substr($hea,0,strlen($hea)-2);
				   if(mysql_query("INSERT INTO `$tabledb` ($hea) VALUES ($command)")){
						echo returnn("élément $m ajouté avec succès","009900",$vers,$theme);
				   }
				   else{ 
						echo returnn("élément $m n'a pas été ajouté","990000",$vers,$theme); 
				   }
			   }
			   else{
					echo returnn("erreur de commande","990000",$vers,$theme);
			   }				
				
			}

		}


}

else{ ////////////////////////////////////////////////// IMPOTER
$allecbon=1;
	if(isset($_GET['upload'])){
		$allecbon=0;
		$ext = strtolower(substr(strrchr($_FILES["impo"]["name"][0],"."),1));
		if($ext=="csv"){
			copy($_FILES["impo"]["tmp_name"][0],"tmp/import.csv");
			$fp = fopen("tmp/import.csv","rb");
			$_SESSION["imp"] = fread($fp,filesize("tmp/import.csv"));
			unlink("tmp/import.csv");
			
			$allcons = explode("\n",$_SESSION["imp"]);
			$essayon = split("[,;]",$allcons[0]);
			$essayon2 = split("[,;]",$allcons[rand(0,sizeof($allcons))]);
			$essayon3 = split("[,;]",$allcons[rand(0,sizeof($allcons))]);
			echo"
			<form name='addartosi' method='post' action='./?$part&option=$option&subpart=importer'>
			<input type='hidden' name='uploud' value='1'>
			<b>Voici 2 lignes au hasard, extraites de votre fichier</b><hr>
			Veuillez sélectionner dans quel champ Adeli doit enregistrer les données<br>
			<table class='bando'><tr class='buttontd'>";
			for($e=0 ; $e<sizeof($essayon)-1 ; $e++){
				echo"<td><select name='champ$e'>
				<option value=''>ignorer</option>";
				
				for ($i = 0; $i < $columns; $i++) {
					$field_name = mysql_field_name($res_field, $i);
					echo"<option value='$field_name'>$field_name</option>";
				}
				
				echo"</select></td>";
			}
			echo"</tr>
			<tr>";
			for($e=0 ; $e<sizeof($essayon)-1 ; $e++){
				$essayon[$e] = unquote($essayon[$e]);
				echo"<td>$essayon[$e]</td>";
			}
			echo"</tr><tr>";
			for($e=0 ; $e<sizeof($essayon)-1 ; $e++){
				$essayon2[$e] = unquote($essayon2[$e]);
				echo"<td class='cadre'>$essayon2[$e]</td>";
			}
			echo"</tr><tr>";
			for($e=0 ; $e<sizeof($essayon)-1 ; $e++){
				$essayon3[$e] = unquote($essayon3[$e]);
				echo"<td class='cadre'>$essayon3[$e]</td>";
			}
			echo"</tr>
			
			</table>
			<p align='right'>
			<input type='button' value='annuler' onclick=\"javascript:document.location='./?$part&option=$option&subpart=importer'\">
		<input type='submit' value='importer'></p>
		</form>";
		}
		else{
			$return.=returnn("Veuillez charger un fichier .csv","990000",$vers,$theme);
			$allecbon=1;
		}
	}
	elseif($allecbon==1){
		echo"
		 <form name='artos' method='post' action='./?$part&option=$option&subpart=importer&upload' enctype='multipart/form-data'>
		La fonction d'importation sert pour importer une base de données à partir d'un fichier unique de type excell.<br> Si vous souhaitez ajouter des éléments un à un, à partir de fichiers séparés, vous devrier plutôt créer un <a href='./?option=$option&part=$part&edit'><b>nouvel enregistrement</b></a>, et procéder par \"copier/coller\".<br><br>
	<span class='gras'>importer un fichier</span> <a class='info'>(csv, champs séparés par une virgule)
	<span>Pour générer ce type de fichier, utiliser les fonction d'exportation de Outlook ou Excell et choisissez le format csv, ou texte séparé par une virgule</span>
	</a><br>	   
		    
		<input type='file' name='impo[]'  onfocus='keltype(2)' onclick='keltype(2)'>
		</blockquote>
		
		<br>
		<p align=\"right\">		
		<input type='button' value='annuler' onclick=\"javascript:document.location='./?part=$part'\">
		<input type='submit' value='exécuter'>
		</p></form><br>";
	}   

}
mysql_close($conn);
?>