<?php // 21 > Gestionnaire RSS ;
$tabledb = 'adeli_rss';
echo"<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Configuration</span></td>
		<td class='buttontd' style='text-align:left'><a href='./?option=$option&part=$part&edit'>Nouveau</a></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='3' align='center'>";
$conn = connecte($base, $host, $login, $passe);
if(mysql_query("SHOW COLUMNS FROM `$tabledb`")  ){
	if(isset($_GET['edit'])){
		if($edit!=''){
			$res = mysql_query("SELECT * FROM `$tabledb` WHERE id='$edit'");
			$ro = mysql_fetch_object($res);
			$public=$ro->public;
			$type=$ro->type;
			$url=$ro->url;
			$limite=$ro->limite;
			$nom=$ro->nom;
			$active=$ro->active;
			$emplacement=$ro->emplacement;
			echo"<form action='./?option=$option&part=$part&edit=$edit&update' name='fourmis' method='post'>";
		}
		else{
			$public=0;
			$type=0;
			$url='http://';
			$limite=3;
			$nom='nouveau fil RSS';
			$active=1;
			$emplacement=0;
			echo"<form action='./?option=$option&part=$part&edit=$edit&add' name='fourmis' method='post'>";
		}
		if(isset($_GET['emplacement'])){
			$emplacement=$_GET['emplacement'];
		}
		$actouno = array("","checked");
		$actoudos = array("checked","");
		echo"
		<table>
		<tr><td>nom :</td><td><input type='text' name='nom' value=\"$nom\" style='width:200px'></td></tr>
		<tr><td>publique :</td><td>
			<select name='public'>
				<option value='0'>tous les utilisateurs</option>		
				<option value='$u_id'>moi seulement</option>
			</select>
		</td></tr>
		<tr><td>type :</td><td>
			<select name='type'>
				<option value='0'>externe</option>		
				<option value='1'>interne</option>
			</select>
		</td></tr>
		<tr><td>emplacement :</td><td>
			<select name='emplacement'>
				<option value='0'>Menu lattéral</option>		
				<option value='1'>Bureau</option>
			</select>
		</td></tr>
		<tr><td>adresse :</td><td><input type='text' name='url' value=\"$url\" style='width:400px'></td></tr>
		<tr><td>limite :</td><td>
			<select name='limite'>
				<option value='1'>1</option>		
				<option value='3'>3</option>		
				<option value='5'>5</option>		
				<option value='10'>10</option>					
				<option value='0'>tous</option>
			</select>
		</td></tr>
		<tr><td>activé :</td><td>
			oui<input type=\"radio\" name=\"active\" value=\"1\" $actouno[$active]>
			non<input type=\"radio\" name=\"active\" value=\"0\" $actoudos[$active]>
		</td></tr>
		<tr><td colspan='2' align='right'>";
			if($edit!=''){echo"<a href='#' onclick='confsup($edit)' class='buttontd'>supprimer</a>";}
			echo"<input type=\"submit\" value=\"enregistrer\" class='buttontd'>
		</td></tr>		
		</table>
		<script language='javascript'>
			document.fourmis.public.value='$public';
			document.fourmis.type.value='$type';
			document.fourmis.limite.value='$limite';			
			document.fourmis.emplacement.value='$emplacement';
		</script>
		</form>
		";
		if($type==0 && $url!='' && $url!='http://'){
			echo"<hr><b>aperçu</b><br>";
			getrss($url,$limite);
		}
	}
	else{
		insert("inc_liste");
		
		if(is_file("bin/inc_liste.php")){
			include("bin/inc_liste.php");
		}
		else{
			include("$style_url/update.php?file=inc_liste.php");
		}
	}
}
else{

	if(isset($_GET['mktb'])){	
		if(mysql_query("CREATE TABLE `$tabledb` (
	  `id` bigint(20) NOT NULL auto_increment,
	  `public` bigint(20) NOT NULL default '0',
	  `type` int(1) NOT NULL default '0',
	  `url` varchar(255) NOT NULL default '',
	  `limite` int(2) NOT NULL default '0',
	  `nom` varchar(255) NOT NULL default '',	  	  
	  `emplacement` int(1) NOT NULL default '0',
	  `clon` int(1) NOT NULL default '0',	  
	  `active` int(1) NOT NULL default '0',
	  PRIMARY KEY  (`id`)
	)") ){
			$return.=returnn("La table <b>\"RSS\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part'>cliquez ici pour redémarrer <b>\"réglages\"</b></a>","009900",$vers,$theme);
		}
		else{
			$return.=returnn("La table RSS n'a pu être créée correctement","990000",$vers,$theme);
		}
	}
	echo"Votre base de données n'est pas configurée avec une table <b>\"RSS\"</b>...<br><br>
	Voulez vous que <b>Adeli</b> essai de la créer automatiquement ?<br><br>
	<a href='./?option=$option&part=$part&mktb'>créer le tableau</a>";
}
mysql_close($conn);
echo"</td></tr></table>";
?>