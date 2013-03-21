<?php // 46 > Annuaire téléphonique ;
$conn = connecte($base, $host, $login, $passe);
$res_field = mysql_list_fields($base,$tabledb);
$columns = mysql_num_fields($res_field);

echo"<hr>"; 

$wheredb="WHERE `clon`='0'";
  
if($incwhere !== null){
  $wheredb = "$incwhere AND `clon`='0'";
}
if(isset($wheredbplus)){
  $wheredb.=" $wheredbplus";
}

$allcols=array();	

echo"
<div id='liste_larg' style='position:relative;width:100%;overflow-x:auto;'>
<table cellspacing='1' cellpadding='0' border='0' bgcolor='#EEEEEE'><tr class='buttontd'>";
$nowi = 1;
$fielfortitre='id';
for ($i = 0; $i < $columns; $i++) {
	if(mysql_field_type($res_field, $i)=='string' || mysql_field_type($res_field, $i)=='blob'){
		$field_name = mysql_field_name($res_field, $i);
		$fname = strtolower($field_name);
		if((ereg('nom',$fname) || ereg('name',$fname) || ereg('pseudo',$fname) || ereg('raison',$fname) || ereg('enseigne',$fname) || ereg('sigle',$fname)) && substr_count($fname,'_')<2){
			$fielfortitre = $field_name;
			break;
		}		   
	}
}
$l=0;
$result = mysql_query("SELECT * FROM `$tabledb` $wheredb ORDER BY `$fielfortitre`");
if(mysql_num_rows($result) > 0){	
	while ($row = mysql_fetch_object($result)) {	   
	   $nom = ucfirst(substr($row->$fielfortitre,0,20));
		$resu="";
		for ($i = 0; $i < $columns; $i++) {
			$field_name = mysql_field_name($res_field, $i);
			$fname = strtolower($field_name);
			$field_type = mysql_field_type($res_field, $i);
			if($field_type=='string' || $field_type=='blob' || $field_type=='real' || $field_type=='int'){
				if(ereg('tel',$fname) || ereg('tél',$fname) || ereg('fax',$fname) || ereg('phon',$fname) || ereg('port',$fname) || ereg('mob',$fname)){
					$field_value = split("[azertyuiopqsdfghjklmwxcvbn/:]",ereg_replace("[)( \n.-]",'',strtolower($row->$field_name)).'/');	
					foreach($field_value as $numb){
						if(ereg("[0-9]",$numb) && $numb!=NULL){
							$resu.="<td>".ucfirst($field_name)." : <a href='callto:$numb'>$numb</a></td>";
						}
					}
				}
			}		  
	   	}
	    if($resu!=""){
			$l++;
		   if($bgtd == '1'){
			$bgtd='2';
			echo"<tr class='listone' ondblclick=\"javascript:document.location='index.php?$part&amp;edit=$this_id'\">";
		   }
		   else{
			$bgtd='1';
			echo"<tr class='listtwo' ondblclick=\"javascript:document.location='./?option=$option&part=$part&d=$d&amp;edit=$this_id'\">";
		   }
		   echo"<td>
		   <b>$nom</b>
		   </td>$resu</tr>";
		}
	 }
}
if($l==0){
  	echo"<td colspan='$columns' align='center'><br>ce tableau est vide...<br>
	><a href=\"./?option=$option&part=$part&d=$d&amp;edit\">ajouter</a>
	<br><br></td></tr>\n";
}
echo"</table></div>\n";
mysql_close($conn);
?>