<?php // 69 > Liste des éléments rattachés ;

$children='';  
foreach($mega_menu[$option]as $spart=>$tablo){
	$cols = sizeof($tablo);			
	$tablk = array_keys($tablo);
	$sepa='site';
	if(substr($spart,0,7)=='worknet') $sepa='worknet';
	if(substr($spart,0,7)=='gestion') $sepa='gestion';
	for($m=0; $m<sizeof($tablo) ; $m++){
		$tk = $tablk[$m];
		$tart = $tablo[$tk];
		if(!is_numeric($tk)){
			$humanpart = $tk;
		}
		else{
			$humanpart = $tablo[$tk];
			if($prefixe != ""){
				$humanpart = ereg_replace($prefixe,"",$humanpart);
			}
			$humanpart = ereg_replace($spart."_","",$humanpart);
			$humanpart = ereg_replace("adeli_","",$humanpart);
			$humanpart = ereg_replace(">$spart","",$humanpart);
			$humanpart = ereg_replace("-$spart","",$humanpart);
			$humanpart = ereg_replace(">"," ",$humanpart);	
		}
		if(mysql_query("SHOW COLUMNS FROM `$tart`")){	
			$res_field = mysql_list_fields($base,$tart);
			$columns = mysql_num_fields($res_field);
			for ($c=0 ; $c < $columns; $c++) {
				$field_name = mysql_field_name($res_field, $c);
				$field_act = $field_name;
				if(isset(  $r_alias[$tart][$field_act] )){
					$field_act = $r_alias[$tart][$field_act];
				}
				if(substr(strrev($field_act),0,3)=='hc_'){
					$mot = explode('_',strrev($field_act),4);	
					$mot = strrev($mot[3]);		
				}
				else{			
					$mot = explode('_',strrev($field_act),3);	
					$mot = strrev($mot[2]);		
				}
				if($mot == $part){
					
					$fieldoptions = explode("_",substr($field_act,strlen($mot)+1,strlen($field_act)));
					$fieldoptionprint = $fieldoptions[1];
					$fieldoption = $fieldoptions[0];
					
					$prefname = "$tart$field_name";
					echo"<div class='buttontd' onclick=\"sizpa('$prefname')\" style='cursor:pointer'>$humanpart</div>
					<div class='cadrebas' id='$prefname' style='width:272px; padding:0px; overflow-x:hidden;height:1px;overflow-y:hidden'>
					<a href='./?option=$sepa&part=$tart&s=$field_name&v=".($ro->$fieldoption)."'><b>tout voir</b></a>";
					$listres = mysql_query("SELECT * FROM `$tart` WHERE `$field_name`='".($ro->$fieldoption)."'");
						while($rowlist = mysql_fetch_object($listres)){
							$roid = $rowlist->id;
							$roac = $rowlist->active;
							$clac='';
							if($roac==0){
								$clac="class='petittext'";
							}
							echo"<div class='childrendiv'>- <a href='./?option=$sepa&part=$tart&edit=$roid' class='info'>";
							$fti='';
							$ftit='';
							for ($q=0 ; $q < $columns; $q++) {
								$ft = mysql_field_type($res_field, $q);
								$fn = mysql_field_name($res_field, $q);
								if($fn != $field_name){
									$fa = $fn;
									if(isset($r_alias[$tart][$fn])){
										$fa = $r_alias[$tart][$fn];
									}
									if(substr(strrev($fa),0,3)=='hc_'){
										$mo = explode('_',strrev($fa),4);	
										$mo = strrev($mo[3]);		
									}
									else{			
										$mo = explode('_',strrev($fa),3);	
										$mo = strrev($mo[2]);		
									}
									$fieldvalue= $rowlist->$fn;
									if($fn != "id" && $fn != "clon" && ( (isset($multiple_depend[$tart]) && $fn!=$multiple_depend[$tart][4]) || !isset($multiple_depend[$tart])  )){	
									
										if( ( ereg("_",$fa) && mysql_query("SHOW COLUMNS FROM `$mo`") ) || ereg('@',$fa) ){
											
											$refiled = $mo;
											$nameifthefield = ucfirst(str_replace("_"," ",$fn));
											$fieldoption = substr($fa,strlen($mo)+1,strlen($fa));
											if($nameifthefield == ucfirst(str_replace("_"," ",$fa))){
												$nameifthefield = ucfirst($refiled);
											}
											if(ereg(">",$fa)){
												$fieldoption = substr($fieldoption,0,strpos($fieldoption,">"));
												$nameifthefield .= " : ".substr($fa,strpos($fa,">")+1,strlen($fa));
											}
											$fti="$nameifthefield: ";
											$fv='';
											if(substr($fa,0,1) == "@"){
												// déjà bon
												$fv.=$fieldvalue.' ';	
											}
											else{				
												
												$fieldoptions = explode("_",$fieldoption);
												$fieldoptionprint = $fieldoptions[1];
												if(strpos($fieldoptionprint,'/')>-1){
													$fopa = explode('/',$fieldoptionprint);	
													$fieldoptionprint="CONCAT(' '";
													foreach($fopa as $fopv){
														$fieldoptionprint.=",' ',`$fopv`";
													}
													$fieldoptionprint.=")";
												}
												
												$fieldoption = $fieldoptions[0];
												$refiled = trim($refiled);	
												
												if($prefixe!=""){
													$nameifthefield = trim(str_replace($prefixe,"",$nameifthefield));
												}
												$nameifthefield = ucfirst(trim(str_replace("_"," ",$nameifthefield)));
												
												/*$sepa='site';
													
												for($sm=0; $sm<sizeof($menu) ; $sm++){
													$spart = $menupart[$sm];
													$tablo = $menu[$spart];
													if(in_array($refiled,$tablo)){							
														if(substr($spart,0,7)=='worknet') $sepa='worknet';
														if(substr($spart,0,7)=='gestion') $sepa='gestion';	
														break;
													}
												}*/
												
												
												
											   if((sizeof($fieldoptions)==3 && $fieldoptions[2]=='ch') || (sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlch')){
													if(sizeof($fieldoptions)==3){
														$listrest = mysql_query("SELECT $fieldoptionprint,`$fieldoption`,`id` FROM `$refiled` WHERE `$fieldoptionprint`!='' ORDER BY 1");
														while($rowlistt = mysql_fetch_array($listrest)){
															$rowvalue = $rowlistt[0];
															$rowid = $rowlistt[1];
															if(ereg('<'.$rowid.'>',$field_value)){
																$fv.="$rowvalue  ";
															}
														}
													}
													if(sizeof($fieldoptions)==2){
														$listrest = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled`");
														$rowlistt = mysql_fetch_array($listrest);
														$gvl = explode("\n",$rowlistt[0]);
														foreach($gvl as $rowvalue){
															$rowvalue=trim($rowvalue);
															if(ereg('<'.$rowvalue.'>',$field_value)){
																$fv.="$rowvalue  ";
															}
														}
													}				
												}	
												elseif(sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlse'){															
													$fv.=$fieldvalue.' ';														
												}
												else{
													$listrest = mysql_query("SELECT $fieldoptionprint  FROM `$refiled` WHERE `$fieldoption`='$fieldvalue'");
														if(isset($where_multi) && $edit!='' && isset($this_from_multiple)){
															if(mysql_query("SELECT `$m_field` FROM `$refiled`")){
																$listrest = mysql_query("SELECT DISTINCT(``),$fieldoptionprint  FROM `$refiled` WHERE `$m_field`='$this_from_multiple' ORDER BY 1");
															}
														
														}
														while($rowlistt = mysql_fetch_array($listrest)){
															$fv.= $rowlistt[0];
														}
												}
											 }	
											 $fti.=" $fv ";
											 $ftit.=" $fv ";
												
												
												
												
												
												
												
										}
										else{
											if($fn!='pass' && $fn!='id' && $fn!='clon' && $fn!='ordre'){
												$fv= strip_tags($rowlist->$fn);
												if($fv!='' && ($fti=='' || !strpos($fti,$fv))){
													$fti.="$fn: $fv ";
												}
											}
											if(($ft=='string' || $ft=='blob' || $ft=='real') ){
												$fv= strip_tags($rowlist->$fn);
												if($fv!='' && ($ftit=='' || !strpos($ftit,$fv))){
													$ftit.=$fv.' ';
												}
											}
										}
									}
								}
							}
							if(trim($ftit)==''){
								$ftit = $humanpart. $columns.'#'.$roid;
							}
							$ftit = substr($ftit,0,40);
							echo"<font $clac>$ftit</font><span class='childrenspan'>$fti</span></a></div>";
						
						}
					echo"<p><a href='./?option=$sepa&part=$tart&edit&$field_name=".($ro->$fieldoption)."' class='buttontd'>+ ajouter</a></p>
					</div>";
					$ouvert = abs(get_pref("ouvert.$part.$prefname.conf"));
					//echo "ouvert.$part.$prefname.conf = $ouvert";
					if($ouvert>5){										
					  echo"<script language='javascript'>
					  sizpa('$prefname');
					  </script>
								  <br>";	
							  }		
						  }
					  }
	}
} 
/*if($children!=''){
	echo"$children";
	}	
*/	
}
?>
<style>
.childrendiv{
	position:relative;width:140px;height:16px; white-space:nowrap;
}
.childrespan{
	white-space:normal;position:absolute;left:-140px;top:-10px;width:140px;height:140px;white-space:normal;overflow:scroll;text-align:left;
}
</style>