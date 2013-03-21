<?php // 26 > Gestion des demande de rappel ;
	if(isset($_GET["keep_rappel_alive"])){ 
		$keep_rappel_alive = $_GET["keep_rappel_alive"];
		set_pref("rappel.conf",$keep_rappel_alive,"x");
	}

echo"<tr><td colspan='3'>
Status : <a href='./?option=$option&part=$part&is_rappel_alive=$ch_kra' class='info'>
			<img src='$style_url/$theme/v$is_rappel_alive.gif' border='none'><span>changer de status</span></a><br>
			<form action='./?option=$option&part=$part' method='get'>
			<input type='hidden' name='$part'>
			désactiver <b>Rappel</b> si je suis absent depuis plus de
			<input type='text' name='keep_rappel_alive' value='$keep_rappel_alive'> secondes
			<input type='submit' value='ok'>
			</form>
			</td></tr>
<tr><td colspan='3'><img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>
<tr><td colspan='3'>
Rappels demandés :
<script language='javascript'>
	function sela(k){
		var allche = document.listage.getElementsByTagName(\"input\");
		for (var i=2; i<allche.length; i++) {
			allche[i].checked=k;
		}
	}
	function conmulti(k){
		var transk = new Array();
		transk['active']='activer';
		transk['desactive']='désactiver';
		transk['delete']='supprimer';
		nbsel=0;
		var allche = document.listage.getElementsByTagName(\"input\");
		for (var i=2; i<allche.length; i++) {
			if(allche[i].checked==true) nbsel++;
		}
		if(nbsel>0){
			pro = confirm(\"êtes vous certain de vouloir \"+transk[k]+\" les \"+nbsel+\" objets sélectionnés ?\");
			if(pro){
				document.listage.action+='&multi='+k;
				document.listage.submit();
			}
		}
		else{
			alert(\"aucun objet n'est sélectionné\");
		}
	}
	</script>
	<form name='listage' action='./?option=$option&part=$part' method='post'><table width='100%'><tr>

<td align='left'><input type='checkbox' onclick='sela(this.checked)'>
	 -	
	<a href='#' onclick=\"conmulti('active')\"><img src='$style_url/$theme/v1.gif' border='none' alt='activer'></a>
	<a href='#' onclick=\"conmulti('desactive')\"><img src='$style_url/$theme/v0.gif' border='none' alt='désactiver'></a>
	<a href='#' onclick=\"conmulti('delete')\"><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>
	</td>
	<td align='right'>
		n'afficher que les non rappelés ";
		if($affdesac==0){
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=1'\">";
		}
		else{
			echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=0'\" checked>";
			$incwhere.=" AND `active`=0";
		}		
		echo"
		<td>
	</tr>
	<tr><td colspan='2'>";
$conn = connecte($base, $host, $login, $passe);
if(isset($_GET['edit'])){
	$ros = mysql_query("SELECT * FROM `gestion_rappel` WHERE `id`='".str_replace("'","''",$_GET['edit'])."'");
	if($ros && mysql_num_rows($ros)==1){
		$rew=mysql_fetch_object($ros);
		$commentaires  = nl2br($rew->commentaires);
		$telephone = $rew->telephone;
		$active = $rew->active;
		$cre = date("d/m/y H:i",strtotime($rew->date));
		$dat = date("d/m/y H:i",strtotime($rew->rappel));
		$goleft='';
		$goright='';
		
		$rus = mysql_query("SELECT `id` FROM `gestion_rappel` WHERE `id`<'".str_replace("'","''",$_GET['edit'])."' LIMIT 0,1");
		if($rus && mysql_num_rows($rus)==1){
			$ru=mysql_fetch_array($rus);
			$goleft="<a href='./?option=$option&part=$part&edit=$ru[0]' class='info'><img src='$style_url/$theme/fl_g.png' alt='<<' border='none'><span>rappel précédent</span></a>";
		}
		$rus = mysql_query("SELECT `id` FROM `gestion_rappel` WHERE `id`>'".str_replace("'","''",$_GET['edit'])."' LIMIT 0,1");
		if($rus && mysql_num_rows($rus)==1){
			$ru=mysql_fetch_array($rus);
			$goright="<a href='./?option=$option&part=$part&edit=$ru[0]' class='info'><img src='$style_url/$theme/fl_r.png' alt='>>' border='none'><span>rappel suivant</span></a>";
		}
		
		echo"
		<hr>
		$goleft <span class='petittext'>rappel enregistré le $cre :</span> $goright<br><br>
		<span class='textegrasfonce'>$dat</span><br>
			<b>$telephone</b><br>
			$commentaires
			</span></a><br><br>
			<a href='./?option=$option&part=$part'><span class='petittext'>Fermer</span></a>
			
			";

	}	
}
$al="l";	
insert("inc_gliste");
if(is_file("bin/inc_gliste.php")){
	include("bin/inc_gliste.php");
}
else{
	include("$style_url/update.php?file=inc_gliste.php");
}


mysql_close($conn);
echo"</td></tr>
		</table>
		</form></td></tr></table></td></tr><tr><td colspan='3'><img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>";
?>