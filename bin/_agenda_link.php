<?php // 16 > Gestionnaire d'agenda pour site ;
if(isset($_GET['edit']) && $_GET['edit']!='' && mysql_query("SHOW COLUMNS FROM `$agenda_base`")){
echo"<div class='buttontd' onclick=\"sizpa('agenda_insidepanel')\" style='cursor:pointer'>Agenda</div>
<div class='cadrebas' id='agenda_insidepanel' style='height:1px;padding:0px;overflow-y:hidden' ><div id='agenda_inside'>";

	$resa = mysql_query("SELECT * FROM `$agenda_base` WHERE `lien`='$edit@$part' AND (`only`='0' OR `only`='$u_id') ORDER BY `date`,`heure`");
			while($roa = mysql_fetch_object($resa)){
				 $m_id = $roa->id;
				 $m_heure = $roa->heure;
				 $m_usr = $roa->usr;
				 $m_date = $roa->date;
				 $m_qui = addslashes($roa->qui);
				 $m_type = addslashes($roa->type);
				 $m_client = addslashes($roa->client);
				 $m_note = addslashes($roa->note);
				 $m_etat = $roa->etat;
				 $m_priority = $roa->priority;
				 $m_only = $roa->only;
				 $m_lien = $roa->lien;
				 
				 
				 $printki=$m_client;
				 $nots=substr($m_note,0,20);
				 $m_couleur = $roa->couleur;
					$size=2;
					$marj=1;
					$b_couleur = "#$m_couleur";
					$co=' style="font-weight:bold" ';
						if($m_etat==1){
							$size=1;
							$marj=0;
							$co="color='#$m_couleur'";
							$b_couleur = 'none';
							$nots='';
						}
						if($m_date==$sqldate && $m_heure>date('h:i:s') && $onlyon==1){
							$size=1;	
							//$b_couleur = ":#$m_couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
						}
						if($u_id!=$m_usr){
							$b_couleur = "#$m_couleur url(http://www.adeli.wac.fr/vers/1.2/algues/bgalpha.gif)";
						}

				if($m_priority==0) $m_priority=1;
				
				
				$agebody='';
				$bodi=split("\n",strip_tags(trim($m_note)));
				for($e=0 ; $e<sizeof($bodi) ; $e++){
					$agebody.=trim(trim($bodi[$e]))." ";
				}
				$m_note = urldecode(str_replace('%0D',' ',str_replace('%2C',' ',urlencode(str_replace('"',"`",$agebody)))));
				$dat = date('d/m/y H:i',strtotime("$m_date $m_heure"));
				if($debit==0){		
					
						$prio = "<font style='font-size:".($m_priority*3)."px'>".str_repeat("*",$m_priority)."</font>";		 
						$printo="<table cellpadding='1' cellspacing='0' id='$sqldate$h"."_$m_id' width='90%' style='margin:$marj;background:$b_couleur;cursor:default;z-index:150;";
						if(!is_numeric($h) && $midi==0 && str_replace(':','',$m_heure)>120000){
							 $midi=1;
							 $printo.="margin-top:10px;";
						 }
						$printo.="' onClick=\"fillage('$m_qui','$m_type','$m_client','$m_priority','$m_etat','$m_note','$m_only','$m_usr','$m_lien'); contextage('$m_date','$m_heure',event,'Modifier','update=$m_id&$part','$m_couleur',$m_id,'agenda_inside','$sqldate&h=$h&print=$print');\"><tr><td>$dat :<font size='$size' $co>$printki </font>$m_note</td><td align='right'>$prio</td></tr></table>";
					
				}
				else{
					
						$prio = "<font style='font-size:".($m_priority*3)."px'>".str_repeat("*",$m_priority)."</font>";		 
						$printo="<table cellpadding='1' cellspacing='0' width='90%' style='margin:$marj;background:$b_couleur;cursor:default;z-index:150;'><tr><td><a href='./?option=$option&part=$part&id=$m_id'>$dat : <font size='$size' $co>$printki</font></a></td><td align='right'>$prio</td></tr></table>";
					
				}
				echo $printo;
			}
echo"</div><a  style=\"cursor:pointer\" onClick=\"fillage('$part','','','1','0','','','$u_id','$edit@$part'); contextage('".$_SESSION["date"]."','10:00:00',event,'Ajouter une date','add&mois','99CCCC',0,'agenda_inside','$sqlnow_date&h=none&print=1')\" class='info'><img src='$style_url/$theme/+.png' alt='+' border='none'><span>Nouvelle date</span></a></div>";
	$ouvert = abs(get_pref("ouvert.$part.agenda_insidepanel.conf"));
	if($ouvert>5){										
		echo"<script language='javascript'>
		sizpa('agenda_insidepanel');
		</script>";
	}	
}
?>