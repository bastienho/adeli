<?php // 12 > Gestionnaire de mises à jour ;
echo"<table cellspacing='0' cellpadding='3' border='0' width='600'>
	<tr>
		<td valign='top' class='menuselected' width='150'><span class='gras'>Configuration</span></td>
		<td class='buttontd' style='text-align:left'>&nbsp;<td>
	</tr>
	<tr><td valign='top' class='cadrebas' colspan='2' align='center'><br><br>
	<form action='./?maj' method='post'>
	Politique de mises à jour : <br>
	<select name='pol_maj'>";
	for($i=0 ; $i<sizeof($politique_maj); $i++){
		$s='';
		if($i==$pol_maj){
			$s='selected';
		}
		echo"<option value='$i' $s>$politique_maj[$i]</option>";
	}
echo"</select>
<input type='submit' value='ok'><br><br><br><br><br><br><br>
</form></td></tr></table>";
//<a href='./?maj&conf'>mettre à jour le fichier de configuration</a>
?>