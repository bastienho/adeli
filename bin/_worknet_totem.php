<?php // 10 > Gestion spécialisée de comptes clients ;


	   			 
	   		
  if($part=="adeli_messages" && $edit==''){
		  
		   if(mysql_query("SHOW COLUMNS FROM `adeli_message_template`")  ){
			  echo"
			  <script>
			  function assvar(koi){
				  ";
				  if($ishtml){
				  echo"
				  if (document.all) {
					  var oRng = eval('editbox_$idtxt').document.selection.createRange();
					  if(oRng.text) sampleText=oRng.text;
					  oRng.pasteHTML('$'+koi);
				  }
				  else{
					  eval('editbox_$idtxt').document.execCommand('insertHTML', false, '$'+koi);
				  }	
				  eval('editbox_$idtxt').focus();
				  ";
				  }
				  else{
					  echo"insertTags('$'+koi, '', '',0);";
				  }
				  echo"
			  }	
			 			
			  </script>
			  ";
			  $colcli=0;
			  if(mysql_query("SHOW COLUMNS FROM `clients`")){
				  $res_cl = mysql_list_fields($dbase,'clients');
				  $colcli = mysql_num_fields($res_cl);
			  }
			  echo"
			  
			  <b>Modèles de messages</b><br><br>
			  <a href='#' onclick=\"document.getElementById('new_temp').style.height='".(300+($colcli*14))."px';document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh&new';\">
			  <b>+</b> enregistrer le texte courant comme modèle</a>
			<div id='new_temp' style='display:block;width:280px;height:1px;overflow:hidden;'>
			  <a href='#' onclick=\"document.getElementById('new_temp').style.height='1px';document.fourmis.action='./?option=$option&part=$part&$action&edit=$edit';\" class='buttontd'>annuler</a>
			  <br><br>
			  insérer une variable :<br>
			  - <a href='#' onclick=\"assvar('variable');\">variable dynamique</a><br>
			  ou :
			  <br>
			  ";
			  if($colcli!=0){
				  for ($i = 0; $i < $colcli; $i++) {
					  $field_name = mysql_field_name($res_cl, $i);
					  echo"- <a href='#' onclick=\"assvar('$field_name');\">$field_name</a><br>";
				  }
			   }
			  echo"
			  <br><br>
			  nom du nouveau modèle :<br>
			  <input type='text' name='adeli_message_tit' value='nouveau modèle' onfocus=\"this.value='';\">
			  <br><br>
			  <input class='buttontd' type='submit' value='ok'>
			  </div>
			  <br><br>
			  ";
			  if(isset($_GET['new'])){
				  $tit = str_replace("'","''",stripslashes($_POST['adeli_message_tit']));
				  $tex = str_replace("'","''",stripslashes($_POST['text']));
				  if(!mysql_query("INSERT INTO `adeli_message_template` (`nom`,`texte`,`active`) VALUES ('$tit','$tex',1)")){
					  echo"une erreur est survenue...<br>";
				  }
			  }
			  $rem = mysql_query("SELECT `id`,`nom` FROM `adeli_message_template` WHERE `active`=1 ORDER BY `nom`");
			  if($rem && mysql_num_rows($rem)>0){
				  echo"
				  Nouveau message à partir d'un modèle<br><br>
				  <input type='text' name='adeli_message_var' value='' onfocus=\"this.value='';\"><br>
				  <table>";
				  while($rom = mysql_fetch_array($rem)){
					  echo"<tr><td>- <a href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh&rec=$rom[0]&dest='+document.fourmis.dest.value;document.fourmis.submit()\">
			  $rom[1]</a></td><td><a href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh&del=$rom[0]&effdb=adeli_message_template';document.fourmis.submit()\">
			  <img src=\"$style_url/$theme/trash.gif\" border='none' alt='supprimer'></a></td></tr>";
				  }
				  echo"</table>";
			  }
		   }
		   else{
			  if(isset($_GET['mktb'])){	
				  if(mysql_query("CREATE TABLE `adeli_message_template` (
				`id` bigint(20) NOT NULL auto_increment,
				`nom` varchar(255) NOT NULL default '',	  	  
				`texte` text NOT NULL,
				`clon` int(1) NOT NULL default '0',	  
				`active` int(1) NOT NULL default '0',
				PRIMARY KEY  (`id`)
			  )") ){
					  echo"La base de donnée <b>\"Modèles de messages\"</b> a été créée correctement<br><br><a href='./?option=$option&part=$part&edit=$edit'>cliquez ici pour l'utiliser</a>";
				  }
				  else{
					  echo"La table <b>\"Modèles de messages\"</b> n'a pu être créée correctement";
				  }
			  }
			  else{
				  echo"<a href='#' onclick=\"document.fourmis.action='./?option=$option&part=$part&edit=$edit&refresh&mktb';document.fourmis.submit()\">configurer les <b>Modèles de messages</b></a>";
			  }
		   }
		   		
	  }
  elseif($part=="clients"  && !isset($_GET['clone']) && $_GET['edit']!=''){ ///////////////:MESSAGES
	  
		  if(in_array("adeli_messages",$menu["worknet"])){
			  echo"
			  <script language='javascript'>	    
			  function det(ki){
				  document.getElementById('msg_cli').style.height=ki;
			  }
			</script>
			";
			 
			   echo" <div class='buttontd' onclick=\"sizpa('messagepanel')\" style='cursor:pointer'>Communication</div>
<div class='cadrebas' id='messagepanel' style='width:272px; overflow-x:hidden;height:1px;padding:0px;overflow-y:hidden'>
			  <a  href='#' onclick=\"document.fourmis.action='./?option=worknet&part=adeli_messages&edit&dest=$edit';document.fourmis.submit()\" class='buttontd'><b>&Eacute;crire</b></a><br>";
			  /* $ros = mysql_query("SELECT * FROM `adeli_messages` WHERE `dest`='$edit' OR `prov`='$edit' ORDER BY `date`DESC");
			   while($rew=mysql_fetch_object($ros)){
				  $dest = $rew->dest;
				  $prove = $rew->prov;
				  $sujet = $rew->sujet;
				  $etat = $rew->etat;
				  $dat = date("d/m/y H:i",strtotime($rew->date));
				  $mid = $rew->id;
				  if($prove==0){ 
					  $prove="moi"; 
				  }
				  else{
					  $prove=$ro->nom;  
				  }
				  if($dest==0){ 
					  $dest="moi"; 
				  }
				  else{
					  $dest=$ro->nom;  
				  }
				  echo"<span style='white-space:nowrap'><a href='./?adeli_messages&edit=$mid'>$prove > $dest : $sujet</a> $dat</span><br>";
			  }*/
			  echo"";
			  if(mysql_query("SHOW COLUMNS FROM `adeli_message_template`")  ){
				  $rem = mysql_query("SELECT `id`,`nom` FROM `adeli_message_template` WHERE `active`=1 ORDER BY `nom`");
				  if($rem && mysql_num_rows($rem)>0){
					  echo"
					  envoyer :<br>
					  <input type='text' name='adeli_message_var' value='' onfocus=\"this.value='';\"><br>
					  avec le modèle :<br>";
					  while($rom = mysql_fetch_array($rem)){
						  echo"- <a href='#' onclick=\"document.fourmis.action='./?option=worknet&part=adeli_messages&edit&rec=$rom[0]&dest=$edit&date=$mysqlnow&sujet=$rom[1]';document.fourmis.submit()\">
				  $rom[1]</a><br>";
					  }
				  }
			   }			
			  if(is_file('bin/_transfert.php')){
				  echo"
				  <style type='text/css'>
					  .joinfich{
						  display:none;
						  margin-right:15px;
					  }
				  </style>
				  <script language='javascript'>
					   function affichfichs(){
						  chs = document.getElementById('coldroit').getElementsByTagName('input');
						  for(i=0 ; i<chs.length ; i++){
							  if(chs[i].type=='checkbox' && chs[i].className=='joinfich'){
								  chs[i].style.display='inline';
							  }
						  }
					   }						
				  </script>
				  <br>
				  
				  <a onclick=\"affichfichs()\">Joindre les fichiers</a>";
			  }
			  echo"</div>";
				$ouvert = abs(get_pref("ouvert.$part.messagepanel.conf"));
				if($ouvert>5){										
					echo"<script language='javascript'>
					sizpa('messagepanel');
					</script>";
				}	
		  }
		  else{
			  //echo"pas de système de messagerie client...<hr>";
		  }
		  /////////////////////////////////////////////////////: COMPTA
		  
		  if(isset($compta_base) && mysql_query("SHOW COLUMNS FROM $compta_base") && in_array('compta',$opt) ){
			  
			  $lastyp='';
			  $subto=0;
			  $subdehors=0;
			  $dismoidehors='';
			  $dismoidedans='<table>';
			
			  $rus = mysql_query("SELECT DISTINCT(`type`) FROM `$compta_base` WHERE `client`='$edit' ORDER BY `type`DESC");
			  while($ruw=mysql_fetch_array($rus)){
				  $type = $ruw[0];
				  if($type!=$lastyp){
					  if($lastyp!=''){
						  $dismoidedans.="<tr><td></td><td align='right'>$subto&euro;<td></td></tr>";
						  $dismoidehors.="$lastyp: $subdehors&euro; &nbsp; ";
						  $subdehors=0;
						  $subto=0;
					  }
					   $dismoidedans.="<tr><td colspan='3'><b>$type</td></tr>";
				  }
				  $lastyp = $type;
				  
				  $ros = mysql_query("SELECT * FROM `$compta_base` WHERE `client`='$edit' AND `type`='$type' ORDER BY `numero`DESC");
				  while($rew=mysql_fetch_object($ros)){							
					  $code = $rew->code;
					  $numero = $rew->numero;
					  $intitule = $rew->intitule;
					  $montant = $rew->montant;
					  $acompte = $rew->acompte;
					  
					  $solde = round($montant-$acompte,2);
					  $acc='';
					  if($acompte > 0){
						  $acc="<br>solde sur $montant&euro;";
					  }
					  $etat = $rew->etat;
					  $subto+=$solde;
					  if($etat==0){
						  $subdehors+=$solde;
					  }
					  $dat = date("d/m/y",strtotime($rew->date));
					  $mid = $rew->id;
					   $dismoidedans.="<tr><td>";
					  if(is_file('bin/_transfert.php') && in_array("adeli_messages",$menu["worknet"])){
						   $dismoidedans.="<input type='checkbox' name='join_compta_$mid' value='".ucfirst($type)." $code$numero' class='joinfich'>";	
					  }
					   $dismoidedans.="&nbsp;<span style='white-space:nowrap'><a href='./?option=compta&$type&edit=$mid&getcontent' class='info'>$code$numero<span><b>$intitule</b><br><font size='1'> $dat$acc</font></span></a>
					  <td align='right'><a href='./?option=compta&$type&edit=$mid&getcontent'><font color='#$colorstatut[$etat]'>$solde&euro;</font></font></td>
					  </td><td><a href='#' onclick=\"javascript:open('$openpdf&mkpdf=$mid','pdf','width=400,height=500,scrollbars=1,resizable=1,top='+((screen.height-500)/2)+',left='+((screen.width-400)/2))\" class='info'><img src='http://www.adeli.wac.fr/icos/pdf.gif' border='none' alt='éditer'><span>voir le pdf</span></a></span></td>
					  <td align='right'><font color='#$colorstatut[$etat]'>$defstat[$etat]</font></td>
					  </tr>";
				  }
			  }
			  $dismoidehors.="$lastyp: $subdehors&euro; &nbsp; ";
			   $dismoidedans.="</table>";
			  echo"
			 <div class='buttontd' onclick=\"sizpa('cmt_cli')\" style='cursor:pointer'>Compta</div>
				  <div id='cmt_cli' style='display:block;width:280px;height:1px;padding:0px;overflow:hidden;' class='cadrebas'>
				  <span class='petittext'>$dismoidehors</span>
				  <a href=\"./?option=compta&edit&freecontent&forclient=$edit\" class='info'><img src='$style_url/$theme/+.png' alt='+' border='none'><span>nouveau document compta</span></a> $dismoidedans
			  </div>";
			 $ouvert = abs(get_pref("ouvert.$part.cmt_cli.conf"));
			  if($ouvert>5){										
				  echo"<script language='javascript'>
				  sizpa('cmt_cli');
				  </script>";
			  }
		  }
		  else{
			  //echo"<!-- sans compta -->";
		  }
		  
	 }

?>
