<?php // 2087 > Gestion de site ;
if($part=="" && isset($_GET["part"]) && $_GET["part"]!=""){
  $part = $_GET["part"];
}
if(isset($_GET["subpart"]) && $_GET["subpart"]!=""){
  $subpart = $_GET["subpart"];
}


$wheredbplus='';

$tabledb = $part;  
$comportement=array();
if(isset($types[$part])) $comportement = explode(",",$types[$part]);

if($part != ""){
if($part!='index' && is_file("$part.php")){
  echo"module personnalis&eacute;<br>";
  
  include("$part.php");
  
}
else{
if( !isset($comportement) || sizeof($comportement)==0 || (sizeof($comportement)==1 && $comportement[0]=="") || in_array("txt",$comportement) ){  

if( isset($base) && $conn){
  
  $date_in_rows=false;
  
    $verifupdt = mysql_query("DESC `$tabledb`");
    $allchamps = array();
    while($ro = mysql_fetch_object($verifupdt)){
      array_push($allchamps,$ro->Field);
      $ty = $ro->Type;
      if($ty=='date' || $ty=='datetime') $date_in_rows=true;
    }
    if(!in_array("clon",$allchamps)){
      mysql_query("ALTER TABLE `$tabledb` ADD `clon` BIGINT NOT NULL");
    }
    if(!in_array("active",$allchamps)){
      mysql_query("ALTER TABLE `$tabledb` ADD `active` INT( 1 ) NOT NULL ;");
    }

  if(isset($_GET['unsetclon'])){
    if($u_droits == '' || $u_active == 1 ){
      
      $unsetclon = $_GET['unsetclon'];
      $res = mysql_query("SELECT `clon` FROM `$tabledb` WHERE id='$unsetclon'");
      $ro = mysql_fetch_object($res);
      $ref = $ro->clon;
      if( mysql_query("UPDATE `$tabledb` SET `clon`='0',`active`='1' WHERE id='$unsetclon'") && deletefromdb($base,$part,$ref)){
        $return.=returnn("mise &agrave; jour effectu&eacute;e avec succ&eacute;s","009900",$vers,$theme);
      }
      else{
        $return.=returnn("la mise &agrave; jour a &eacute;chou&eacute;e","990000",$vers,$theme);
      }
      
    }
    else{
      $return.=returnn("Vous n'avez pas les droits pour mettre en ligne ce texte","990000",$vers,$theme);
    }
  }
  
    if(isset($_GET['indep'])){
    if($u_droits == '' || $u_active == 1 ){
     
      if( mysql_query("UPDATE `$tabledb` SET `clon`='0' WHERE id='$edit'")){
        $return.=returnn("mise &agrave; jour effectu&eacute;e avec succ&eacute;s","009900",$vers,$theme);
      }
      else{
        $return.=returnn("la mise &agrave; jour a &eacute;chou&eacute;e","990000",$vers,$theme);
      }
      
    }
    else{
      $return.=returnn("Vous n'avez pas les droits pour mettre en ligne ce texte","990000",$vers,$theme);
    }
  }
  
    if(isset($_GET['setvalid'])){
    $tmpdb=$tabledb;
    if(isset($_GET['effdb']) && $_GET['effdb']!=''){
      $tabledb=$_GET['effdb'];
    }
    if($u_droits == '' || $u_active == 1 ){
      
      $setvalid = $_GET['setvalid'];
      if( mysql_query("UPDATE `$tabledb` SET `active`='1' WHERE id='$setvalid'") ){
        $return.=returnn("mise en ligne effectu&eacute;e avec succ&eacute;s","009900",$vers,$theme);
      }
      else{
        $return.=returnn("la mise en ligne a &eacute;chou&eacute;e","990000",$vers,$theme);
      }
      
    }
    else{
      $return.=returnn("Vous n'avez pas les droits pour mettre en ligne ce texte","990000",$vers,$theme);
    }
    $tabledb=$tmpdb;
  }
  if(isset($_GET['unsetvalid'])){
    $tmpdb=$tabledb;
    if(isset($_GET['effdb']) && $_GET['effdb']!=''){
      $tabledb=$_GET['effdb'];
    }
    if($u_droits == '' || $u_active == 1 ){
      
      $unsetvalid = $_GET['unsetvalid'];
      if( mysql_query("UPDATE `$tabledb` SET `active`='0' WHERE id='$unsetvalid'") ){
        $return.=returnn("mise hors ligne effectu&eacute;e avec succ&eacute;s","009900",$vers,$theme);
      }
      else{
        $return.=returnn("la mise hors ligne a &eacute;chou&eacute;e","990000",$vers,$theme);
      }
      
    }
    else{
      $return.=returnn("Vous n'avez pas les droits pour mettre hors ligne ce texte","990000",$vers,$theme);
    }
    $tabledb=$tmpdb;
  }
  if(isset($_GET['rename']) && isset($_GET['in']) && isset($_GET['en'])){
    if($u_droits == '' || $u_active == 1 ){
      
      $rename = $_GET['rename'];
      $in = $_GET['in'];
      $en = $_GET['en'];
      if( mysql_query("UPDATE `$tabledb` SET `$in`='$en' WHERE `$in`='$rename'") ){
        $nbaf = mysql_affected_rows();
        $return.=returnn("renomage effectu&eacute; avec succ&eacute;s<br>($nbaf champs affect&eacute;s)","009900",$vers,$theme);
      }
      else{
        $return.=returnn("le renomage a &eacute;chou&eacute;e","990000",$vers,$theme);
      }
      
    }
    else{
      $return.=returnn("Vous n'avez pas les droits pour mettre hors ligne ce texte","990000",$vers,$theme);
    }
  }

  $filepart = ereg_replace(">","-",$part);
  if($filepart!='index' && file_exists("$filepart.php")){
    echo"<a class='info'><img src='$style_url/$theme/mesure.gif'>
        <span>Partie sur mesure</span></a>";
    
    include("$filepart.php");
    
  }
  else{  
  if(isset($_GET['edit'])){    
    $action="update";    
    if(!isset($_GET['add'])){
      $edit = $_GET['edit'];
    }
    if(isset($u_restreint) && $u_restreint[1]==$part && (isset($edit) || isset($_GET['edit']))){
      $edit=$u_d;
    }
    if($edit == '' || isset($_GET['clone']) || isset($_GET['new'])){
      $action='add';
      $edit=$_GET['edit'];
    }
  }

$res_field = mysql_list_fields($base,$tabledb);
$columns = mysql_num_fields($res_field);
  
    
  /*if(isset($u_restreint) && $u_restreint[1]==$part && (isset($edit) || isset($_GET['edit']))){
    $edit=$u_d;
  }*/
  if(isset($multiple_depend[$part])){
    $m_db = $multiple_depend[$part][0];
    $m_id = $multiple_depend[$part][1];
    $m_label = $multiple_depend[$part][2];
    $m_field = $multiple_depend[$part][3];
    $m_ref = $multiple_depend[$part][4];
    $r_alias[$part][$m_field] = $m_db.'_'.$m_id.'_'.$m_label;
    $refuniqid='';
  }
  
if(!isset($_SESSION['pra'])) $_SESSION['pra']=false;
if(isset($_GET['s'])){
  $sa = $_GET['s'];
  $va = stripslashes($_GET['v']);
  $_SESSION[$sa] = $va;
  $_SESSION['pra'] = false;
}
if(isset($_GET['printall'])){
  $_SESSION['pra'] = true;
}
if(isset($_GET['iniliste'])){
  $_SESSION['pra'] = false;
}  

if(!isset($_GET['alert'])){
  $txid=0;
  $prefixselection='';
  if(isset($multiple_depend[$part])){
    if(is_numeric($edit)){
      $res = mysql_query("SELECT * FROM `$tabledb` WHERE `id`='$edit'");
      $ro = mysql_fetch_object($res);
      $rms = mysql_query("SELECT `$m_id`,`$m_label` FROM `$m_db` ORDER BY `$m_label`");
      if($rms && mysql_num_rows($rms)>0){
        $mlt_k=array();
        $mlt_v=array();        
        while($rw = mysql_fetch_array($rms)){
          array_push($mlt_k,$rw[0]);
          array_push($mlt_v,$rw[1]);
        }  
        $nbml = mysql_num_rows($rms);
        
        $refuniqid=$ro->$m_ref;
        if($refuniqid==NULL){
          $refuniqid = uniqid(md5(rand()),true);
          mysql_query("UPDATE `$part` SET `$m_ref`='$refuniqid' WHERE `id`='$edit'");
        }
        
        $this_from_multiple=$ro->$m_field;
        if(!in_array($this_from_multiple,$mlt_k)){
          $this_from_multiple = $mlt_k[0];  
        }
        $where_multi  = " AND `$m_ref`='$refuniqid'";
        $mlt_links='';
        for($m=0 ; $m<$nbml ; $m++){
          if($mlt_k[$m]==$this_from_multiple){
            $mlt_links.="<b>$mlt_v[$m]</b> ";  
          }
          else{
            $oms = mysql_query("SELECT `id` FROM `$part` WHERE `$m_field`='$mlt_k[$m]' AND `$m_ref`='$refuniqid'");
            if($oms && mysql_num_rows($oms)>0){
              $rs = mysql_fetch_array($oms);
              $mlt_id = $rs[0];
            }
            elseif(mysql_query("INSERT INTO `$part` (`$m_field`,`$m_ref`) VALUES('$mlt_k[$m]','$refuniqid')")){
              $mlt_id = mysql_insert_id($conn);
            }
            else{
              $mlt_id = "&$m_ref=$refuniqid&$m_field=$mlt_k[$m]";  
            }
            $mlt_links.="<a href='./?option=$option&part=$part&edit=$mlt_id'>$mlt_v[$m]</a> ";  
          }
        }
        
      }
    }
    elseif(isset($_SESSION[$m_field])){
      $rms = mysql_query("SELECT `$m_id`,`$m_label` FROM `$m_db` ORDER BY `$m_label`");
      if($rms && mysql_num_rows($rms)>0){
        $where_multi  = " AND `$m_field`='".$_SESSION[$m_field]."'";
      }
    }
  }  
  
}

    echo"<script language='javascript' type='text/javascript'>
    var incwh='';
      function exporter(unik){
        if(document.listage){
          document.listage.action='./?option=$option&part=$part&subpart=exporter&incwhere='+incwh;
          bbsel='';
          var allche = document.listage.getElementsByTagName(\"input\");
          for (var i=2; i<allche.length; i++) {
            if(allche[i].checked==true && allche[i].className!='noche'){
              bbsel+=allche[i].name;
            }
          }
          if(bbsel!=''){
            document.listage.action+='&selected='+bbsel;
          }
          document.listage.submit();
        }
        else if(unik!=null){
          document.location='./?option=$option&part=$part&subpart=exporter&incwhere='+incwh+'&selected=sel'+unik;
        }
        else{
          document.location='./?option=$option&part=$part&subpart=exporter&incwhere=".urlencode($incwhere)."';
        }
        
      }
     </script>";
     if(!isset($_GET['edit']) || $debit==1){
       if($debit==0) echo" <table cellspacing='0' cellpadding='3' border='0' width='100%' id='menuperso'><tr style='height:20px;'><td class=\"buttontd\"  width=\"10\">&nbsp;</td>";
       else echo" <select onchange='document.location=this.value'>";
       $is_liste=false;
       if(!in_array("nonew",$comportement) && (!isset($u_restreint) || $u_restreint[1]!=$part )){
        $submen = array("liste"=>"$parto","importer"=>"importer","exporter"=>"exporter","statistiques"=>"statistiques"); //"edit"=>"nouveau",  
       }
       else{
        $submen = array("liste"=>"$parto","exporter"=>"exporter","statistiques"=>"statistiques");  
       }
      $mensub = array_keys($submen);
      $i=0;
      foreach($submen as $k=>$v){
        $gm = isget($mensub);
        if(isset($_GET[$k]) || ($i==0 && ( $gm==false || $gm=='liste' ))){
          if( $gm==false || $gm=='liste' ) $is_liste=true;        
          /*if($k=='edit' && is_numeric($_GET['edit'])){
            echo"<td class=\"menuselected\" width='80'><a href=\"./?option=$option&part=$part&option=$option&$k=".$_GET['edit']."\">Edition</a></td>";
          }
          else{*/
            if($debit==0) echo"<td class=\"menuselected\" width='80'><a href=\"./?option=$option&part=$part&subpart=$k\">".ucfirst($v)."</a></td>";
            else echo"<option value=\"./?option=$option&part=$part&subpart=$k\" selected>".ucfirst($v)."</option>";
          //}
        }
        elseif($v=='exporter'){
          if($debit==0) echo"<td class=\"buttontd\" width='80'><a href=\"#\" onclick='exporter()'>".ucfirst($v)."</a></td>";
          else echo"<option value='#' onclick='exporter()'>".ucfirst($v)."</option>";
        }
        else{
          if($debit==0) echo"<td class=\"buttontd\" width='80'><a href=\"./?option=$option&part=$part&subpart=$k\">".ucfirst($v)."</a></td>";
          else echo"<option value=\"./?option=$option&part=$part&subpart=$k\">".ucfirst($v)."</option>";
        }
        $i++;
      }
       if($debit==0)echo"<td class=\"buttontd\" align='left'>&nbsp;</td></tr></table>";
       else echo"</select>";
     }
    /*if(!isset($_GET['edit']) && !isset($_GET['exporter']) && !isset($_GET['importer'])){  
    <table width='100%'><tr><td align='left'><p align='left'>&nbsp;
      </p></td><td align='right'>";

    echo"</td></tr></table>
    }*/
    echo"
    <table cellspacing='0' cellpadding='3' border='0' width='100%'>
    <tr><td align='center' class='cadrebas'>";
    $is_printaffich=0;
    $modifouille='';
    $validouille='';
    $addlien='';
    $emptylien='';
  if($is_liste==true && !isset($_GET['edit'])){
    $oda='1px';
    if(isset($_GET['s']) || isset($_GET['al']) || isset($_GET['pp']) || isset($_GET['affdesac'])) $oda='auto';
    echo"
    <p align='left' style='font-size:10px'><a href='#' onclick=\"sizpa('printoptions')\">Options d'affichage</a></p>
    <div id='printoptions' style='display:block;width:100%;height:$oda;padding-top:10px;overflow:hidden;text-align:left;float:left;'>
    <table class='cadre'>
    <tr><td><b>Mode :</b></td><td>
    ";
    if(isset($_GET['al'])){
        $al = $_GET['al'];
        set_pref("list.$part.conf",$al);
      }
      $al = get_pref("list.$part.conf");
      if($al==""){
        $al = "l";
      }
      $list_of_views = array('list','icon','table');
      if($date_in_rows){
        array_push($list_of_views,'date');
      }
      foreach($list_of_views as $this_view){
        $border='none';
        $this_view_id = substr($this_view,0,1);
        if($this_view_id ==$al) $border='1';
        echo"<a href='./?option=$option&part=$part&al=$this_view_id'><img src='$style_url/img/view-$this_view.png' border='$border' alt='affichage $this_view'></a> ";
      }
    echo"</td></tr>";
    
    
    
    
    
  for ($i = 0; $i < $columns; $i++) {
    $field_name = mysql_field_name($res_field, $i);
    $field_act = $field_name;
    if(isset($r_alias[$part][$field_name])){
      $field_act = $r_alias[$part][$field_name];
    }
    if(substr(strrev($field_act),0,3)=='hc_'){
      $mot = explode('_',strrev($field_act),4);  
      $mot = strrev($mot[3]);    
    }
    else{      
      $mot = explode('_',strrev($field_act),3);  
      $mot = strrev($mot[2]);    
    }
    $fieldoption = substr($field_act,strlen($mot)+1,strlen($field_act));
    if(substr($field_act,0,1) == "_"){
      if(isset($alias[$part][$field_name])) $field_named = $alias[$part][$field_name];
      else   $field_named = substr($field_act,1,strlen($field_act));
      
      if($is_printaffich==0){
        $is_printaffich=1;
        echo"<tr><td><b>Filtrer les r&eacute;sultats : </b></td><td>";
      }
      $modifouille.="<div class='buttontd'><b>$field_named</b></div>
      <div class='cadrebas'><div class='sousrub'>";
      echo"$field_named :<select onchange=\"javascript:document.location='./?option=$option&part=$part&s=$field_name&v='+this.value\" style=\"width:100px\">
      <option value=\"\" selected>tou(te)s</option>";
        $allready=array();
        $listres = mysql_query("SELECT DISTINCT `$field_name` FROM `$tabledb` WHERE `$field_name`!='' ORDER BY `$field_name`");
        while($rowlist = mysql_fetch_object($listres)){
          $rowvalue = $rowlist->$field_name;
          $s="";
          if(isset($_SESSION[$field_name]) && $rowvalue == $_SESSION[$field_name] && !isset($_GET['printall']) ){
            $s = "selected";
            if(isset($r_alias[$tabledb][$field_name]) && substr($r_alias[$tabledb][$field_name],-3)=='_ch'){
              $incwhere.=" AND `$field_name`LIKE'%<".str_replace("'","''",$rowvalue).">%'";
            }
            else{
              $incwhere.=" AND `$field_name`LIKE'".str_replace("'","''",$rowvalue)."'";
            }
            $validouille .= " 
            <a href='#' onclick=\"sizpa('printoptions')\">$field_named  : $rowvalue</a>  
            <a  href=\"./?option=$option&part=$part&s=$field_name&v=\"><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a><br/>";
            $addlien.="&$field_name=$rowvalue";
          }
          elseif(isset($_GET['printall']) || isset($_GET['iniliste'])){
            unset($_SESSION[$field_name]);
          }
          echo"<option value=\"$rowvalue\" $s>$rowvalue</option>";
          $modifouille.="<div>
          <a  href=\"./?option=$option&part=$part&s=$field_name&v=".urlencode($rowvalue)."\"><b>$rowvalue</b></a>
          <a  onclick=\"changedenom('$field_named','$field_name','".str_replace("'","\'",$rowvalue)."')\">Renommer</a>
          </div>";          
        }
      echo"</select>&nbsp;<!-- _CH $incwhere -->";
      $modifouille.="<div class='clear'><a href='./?option=$option&part=$part&printall=1&s'>Tout afficher</a></div>
      
      </div></div>";
    }
    elseif(substr($fieldoption,0,1) != "@" && ereg("_",$field_act) && !ereg("nochange",$field_act) &&  mysql_query("SHOW COLUMNS FROM $mot")){
    
    if($is_printaffich==0){
        $is_printaffich=1;
        echo"<tr><td><b>Filtrer les r&eacute;sultats : </b></td><td>";
      }
        $refiled = $mot;      
        if(isset($alias[$part][$field_name])) $nameifthefield = $alias[$part][$field_name];
        elseif(!isset($r_alias[$part][$field_name]))   $nameifthefield = $refiled;
        else $nameifthefield = $field_name;
        $fieldoption = split("[_>]",$fieldoption);
        $fieldoptionprint = $fieldoption[1];
        
        $fieldoption = $fieldoption[0];    
        $refiled = trim($refiled);  
        if($prefixe!=""){
            $nameifthefield = trim(ereg_replace($prefixe,"",$nameifthefield));
          }  
        if(ereg(">",$field_act)){
            $nameifthefield .= " ".substr($field_act,strpos($field_act,">")+1,strlen($field_act));
        }

        
        echo "$nameifthefield :<select onchange=\"javascript:document.location='./?option=$option&part=$part&s=$field_name&v='+this.value\" style=\"width:100px\">
      <option value=\"\" selected>tou(te)s</option>";
        $listres = mysql_query("SELECT $fieldoptionprint,`$fieldoption` FROM `$refiled` WHERE $fieldoptionprint!='' ORDER BY $fieldoptionprint");
        if(isset($where_multi) && isset($_SESSION[$m_field]) && mysql_query("SELECT `$m_field` FROM `$refiled`") && $m_field!=$field_name){
          $listres = mysql_query("SELECT $fieldoptionprint,$fieldoption  FROM `$refiled` WHERE $fieldoptionprint!='' $where_multi ORDER BY $fieldoptionprint");
        
        }
        while($rowlist = mysql_fetch_array($listres)){
          $rowvalue = $rowlist[0];
          $rowid = $rowlist[1];
            $s = "";
            if(isset($_SESSION[$field_name]) && $rowid == $_SESSION[$field_name] && !isset($_GET['printall']) ){
              $s = "selected";
              $incwhere.=" AND `$field_name`LIKE'$rowid'";
              $validouille .= " 
            <a href='#' onclick=\"sizpa('printoptions')\">$nameifthefield : $rowvalue</a>  
            <a  href=\"./?option=$option&part=$part&s=$field_name&v=\"><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a><br/>";
              $addlien.="&$field_name=$rowvalue";
            }
            elseif(isset($_GET['printall'])){
              unset($_SESSION[$field_name]);
            }
            echo"<option value=\"$rowid\" $s>$rowvalue</option>";
        }
      echo"</select> ";
    }
    
  }
    if($is_printaffich==1){
      echo"</td></tr>";  
    }
  echo"<tr><td>n'afficher que les actifs</td><td> ";
    if($affdesac==0){
      echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=1'\">";
    }
    else{
      echo"<input type='checkbox' onclick=\"javascript:document.location='./?option=$option&part=$part&affdesac=0'\" checked>";
      $wheredbplus="AND `active`=1";
    }    
    echo"</td></tr>";
    
    
    
      if(isset($_GET['pp'])){
        $pp=abs($_GET['pp']);
        set_pref("pp.$part.conf",$pp);
      }
      $pp = abs(get_pref("pp.$part.conf"));
      if($pp==0) $pp=30;
      
      
      if(!isset($_SESSION['pa'])){
        $_SESSION['pa']=1;
      }
      if(isset($_GET['pa'])){
        $_SESSION['pa']=abs($_GET['pa']);
      }
      $pa = $_SESSION['pa'];
      $param = str_replace('&pp=','&npp=',$_SERVER['QUERY_STRING']);
      echo"<tr><td>
      Enregistrements par page : </td><td>
      <select style='font-size:10px' onchange=\"affichload;document.location='./?option=$option&$param&pp='+this.value\">";
        for($p=10 ; $p<=500 ; $p+=10){
          $s='';
          if($p==$pp) $s='selected';
          echo"<option value='$p' $s>$p</option>";
          if($p>90) $p+=40;  
        }
      echo"</select>
      </td></tr>";
    
    echo"</table></div>";
  }
  if($validouille!=''){
    echo" <p align='left'>Filtres actifs :<br/>$validouille</p>";  
  }
  
  echo"

  <script language='javascript' type='text/javascript'>
  
  var clientPC = navigator.userAgent.toLowerCase(); // Get client info
  var is_gecko = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
var is_safari = ((clientPC.indexOf('applewebkit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
// For accesskeys
var is_ff2_win = (clientPC.indexOf('firefox/2')!=-1 || clientPC.indexOf('minefield/3')!=-1) && clientPC.indexOf('windows')!=-1;
var is_ff2_x11 = (clientPC.indexOf('firefox/2')!=-1 || clientPC.indexOf('minefield/3')!=-1) && clientPC.indexOf('x11')!=-1;


  function mkpl(id){
    document.getElementById('txtap'+id).style.visibility='hidden';
    var txtarea;
    var espas = /\\n/g;
    var areas = document.getElementsByTagName('textarea');
    txtarea = areas[id];
    txtarea.focus();
  }
  function ereg_replace(rep,msk,str){
     tmp = \"\";
     var espas = /\\s/g;
     a = str.split(espas);    
     for(i=0 ; i<a.length ; i++){
       tmp += a[i].replace(rep,msk)+' ';  
     }  
       return tmp;
   } 
  function prt(id){
    document.getElementById('txtap'+id).style.visibility='visible';
    var txtarea;
    var espas = /\\n/g;
    var areas = document.getElementsByTagName('textarea');
    txtarea = areas[id];
    lmn = txtarea.value;
    lmn = lmn.replace(espas,'<br />');
    
    lmn = ereg_replace('[/]',\"</span>\",lmn);
    lmn = ereg_replace(']',\"'>\",lmn);
    lmn = ereg_replace('[',\"<span class='\",lmn);

    
    lmn = \"<body onclick='parent.mkpl(\"+id+\")'><link rel='stylesheet' type='text/css' href='http://$prov/$part/style.css'/><font face='arial'>\"+lmn+\"</font></body>\";
    
    eval('ap'+id+'.document.write(lmn);');
  }
  function mkpa(id){
    eval(\"ap\"+id+\".document.location='about:blank';\");
    setTimeout(\"prt(\"+id+\")\",500);
  }
  function insertTags(tagOpen, tagClose, sampleText, texid) {
  var txtarea;
  var areas = document.fourmis.getElementsByTagName('textarea');
  txtarea = areas[texid];


  // IE
  if (document.selection  && !is_gecko) {
    var theSelection = document.selection.createRange().text;
    if (!theSelection) {
      theSelection=sampleText;
    }
    txtarea.focus();
    if (theSelection.charAt(theSelection.length - 1) == \" \") { // exclude ending space char, if any
      theSelection = theSelection.substring(0, theSelection.length - 1);
      document.selection.createRange().text = tagOpen + theSelection + tagClose + \" \";
    } else {
      document.selection.createRange().text = tagOpen + theSelection + tagClose;
    }

  // Mozilla
  } else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
    var replaced = false;
    var startPos = txtarea.selectionStart;
    var endPos = txtarea.selectionEnd;
    if (endPos-startPos) {
      replaced = true;
    }
    var scrollTop = txtarea.scrollTop;
    var myText = (txtarea.value).substring(startPos, endPos);
    if (!myText) {
      myText=sampleText;
    }
    var subst;
    if (myText.charAt(myText.length - 1) == \" \") { // exclude ending space char, if any
      subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + \" \";
    } else {
      subst = tagOpen + myText + tagClose;
    }
    txtarea.value = txtarea.value.substring(0, startPos) + subst +
      txtarea.value.substring(endPos, txtarea.value.length);
    txtarea.focus();
    //set new selection
    if (replaced) {
      var cPos = startPos+(tagOpen.length+myText.length+tagClose.length);
      txtarea.selectionStart = cPos;
      txtarea.selectionEnd = cPos;
    } else {
      txtarea.selectionStart = startPos+tagOpen.length;
      txtarea.selectionEnd = startPos+tagOpen.length+myText.length;
    }
    txtarea.scrollTop = scrollTop;

  }
  // reposition cursor if possible
  if (txtarea.createTextRange) {
    txtarea.caretPos = document.selection.createRange().duplicate();
  }
}
</script>";
  
  if(isset($_GET['edit'])){
    if( !in_array("noedit",$comportement)){
      echo"<form action='./?option=$option&part=$part&d=$d&$action&edit=$edit' method='post' name='fourmis' enctype='multipart/form-data'  onsubmit=\"affichload()\">";
    }
    else{
      echo"<form action='./?option=$option&part=$part&edit=$edit' >";
    }
  }
    
    echo"<table cellspacing='0' cellpadding='3' border='0' width='100%'><tr><td valign='top' align='left' id='colgauche'>";
/********************************************************************************************************************

                  EDITION

**********************************************************************************************************************/

if(isset($_GET['edit'])){  
      
$ishtmlll=array();
if(isset($_GET['new'])){
  $edit='';
}  
$actouno = array("","checked");
$actoudos = array("checked","");
        

    
  $res = mysql_query("SELECT * FROM `$tabledb` WHERE `id`='$edit'");
  $ro = mysql_fetch_object($res);
  $nochnb =0;
    if(isset($editmode_forced)){
      $editmode = $editmode_forced;
    }
    else{
      $editmode = abs(get_pref("editmode.conf"));
      if(isset($_GET['modif'])){
        set_pref("editmode.conf",0);
      }
      if(isset($_GET['view'])){
        set_pref("editmode.conf",1);
      }
    }
    echo" <input class=\"buttontd\" type=\"button\" value=\"Retour\" onclick=\"document.location='./?option=$option&part=$part&d=$d';\"> &nbsp;&nbsp;&nbsp;";
      if($edit==''){
        echo"<i>Modification</i> <i>Lecture</i> <a href=\"./?option=$option&part=$part&amp;edit\"  class='buttontd'>Nouveau</a>";
      }
      elseif($editmode==0 && !in_array('noedit',$comportement) ){
        echo"<b><a href=\"./?option=$option&part=$part&amp;edit=$edit\" class='buttontd'>Modification</a></b> <a href=\"./?option=$option&part=$part&amp;edit=$edit&view\" class='buttontd'>Lire seulement</a>      
        ";
        if(!in_array("nonew",$comportement) && (!isset($u_restreint) || $u_restreint[1]!=$part )){
          echo" <a href=\"./?option=$option&part=$part&amp;edit\" class='buttontd'>Nouveau</a>";
        }
      }
      else{
        echo"<a href=\"./?option=$option&part=$part&amp;edit=$edit&modif\" class='grosbouton'>Modifier</a>   <b><a href=\"./?option=$option&part=$part&amp;edit=$edit\" class='buttontd'>Lecture</a></b>      
        ";
        if(!in_array("nonew",$comportement) && (!isset($u_restreint) || $u_restreint[1]!=$part )){
          echo" <a href=\"./?option=$option&part=$part&amp;edit\"  class='buttontd'>Nouveau</a>";
        }
      }
      if($edit=='') $editmode=0;
  echo"
  
  <script language='javascript'>
       resizteaxtarea=true;
  </script>
  ";
  if(!isset($_GET['alert'])){
  
  echo"<a name='nochange0'></a>
  <div><table width='100%'>";
  
     for ($i = 0; $i < $columns; $i++) {
      $field_name = mysql_field_name($res_field, $i);
      
      $field_act = $field_name;
      if(isset($r_alias[$part][$field_name])){
        $field_act = $r_alias[$part][$field_name];
      }
      if(substr(strrev($field_act),0,3)=='hc_'){
        $mot = explode('_',strrev($field_act),4);  
        $mot = strrev($mot[3]);    
      }
      else{      
        $mot = explode('_',strrev($field_act),3);  
        $mot = strrev($mot[2]);    
      }
      
      $field_type = mysql_field_type($res_field, $i);      
      $field_length = abs(mysql_field_len($res_field, $i));
      $field_value='';
      if(isset($_GET['refresh'])){
        $field_value = stripslashes($_POST[$field_name]);
      }
      elseif(isset($fields_values[$field_name])){
        $field_value = $fields_values[$field_name];  
      }
      elseif($edit!=''){
        $field_value = $ro->$field_name;  
      }
      $field_width=$field_length*12;
      if($field_width > 300){
        $field_width=300;
      }
      $nameifthefield = ucfirst(str_replace(">"," ",$field_name));
      $nameifthefield = trim(str_replace("_"," ",$nameifthefield));
      
      if(isset($alias[$part][$field_name])){
        $nameifthefield = $alias[$part][$field_name];
      }
      echo"<tr>";
      /////////////////////////////////////// DEPENDANCES MULTIPLES
      if(isset($multiple_depend[$part]) && $field_name==$multiple_depend[$part][3] && isset($this_from_multiple)){
        echo"<td width='100'>$nameifthefield</td><td><input type=\"hidden\" name=\"$field_name\" value=\"$this_from_multiple\">
        $mlt_links
        </td>";
      }
      elseif(isset($multiple_depend[$part]) && $field_name==$multiple_depend[$part][4] && isset($refuniqid)){
        echo"<td colspan='2'><input type=\"hidden\" name=\"$field_name\" value=\"$refuniqid\"></td>";
      }
      /////////////////////////////////////// HIDDEN
       elseif($field_act == "hidden"){
         echo"<td colspan='2'><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"></td>";
       }
       /////////////////////////////////////// NOEDIT
       elseif($field_act == "noedit"){
         echo"<td width='100'>$nameifthefield</td><td><!--input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"-->$field_value</td>";
       }
       /////////////////////////////////////// ID
       elseif($field_name == "id"){
         if(isset($_GET['clone'])){
          $field_value='';
        }
         echo"<td width='100'>Identifiant</td><td><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"><b>$field_value</b>";
         if(isset($_GET['clone'])){
          echo"<h1>Duplication de ".$_GET['edit']."@".$part."</h1>";
        }
         echo"</td>";
       }
       //////////////////////////////////////RESTREINT
       elseif(isset($u_restreint) && $field_act=="$u_restreint[1]_$u_restreint[2]_$u_restreint[3]"){
         if($edit==''){
          $field_value = $u_d;
        }
        echo"<td width='100'>$nameifthefield</td><td><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"><b>";
        if($edit!='' && $field_value != $u_d ){
          echo"Vous n'avez pas acc&eacute;s a ce fichier !<br>
          <a href='./?option=$option&part=$part'>retour a la liste</a>
          <script language='javascript'>document.location='./?option=$option&part=$part';</script>
          ";
          exit();
        }
        $resn = mysql_query("SELECT `$u_restreint[3]` FROM `$u_restreint[1]` WHERE `$u_restreint[2]`='$u_d'");
        $ron = mysql_fetch_object($resn);
        echo $ron->$u_restreint[3];
        echo"</b></td>";
       }
      /////////////////////////////////////// CLONE
       elseif($field_name == "clon"){         
         if(isset($_GET['clone'])){
          $field_value=$_GET['edit'];
        }
        $clonid = $field_value;
         echo"<td width='100'></td><td><input type=\"hidden\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"></td>";
       }
       /////////////////////////////////////// BOOL
       elseif(substr($field_act,0,4) == "bool"){
         if($editmode==0 && !in_array('noedit',$comportement) ){
           echo"<td width='100'>$nameifthefield</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
             <input type=\"radio\" name=\"$field_name\" value=\"1\" $actouno[$field_value]>Oui  &nbsp; - &nbsp;
            <input type=\"radio\" name=\"$field_name\" value=\"0\" $actoudos[$field_value]>Non
           </td>";
        }
        else{
          echo"<td>$nameifthefield</td><td><img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'></td>";
        }
       }
       /////////////////////////////////////// ACTIVE
       elseif($field_name == "active"){
         if($editmode==0 && !in_array('noedit',$comportement) ){
          if(in_array("active",$comportement)){
          echo"<td colspan='2'> </td>";
          }
          elseif($u_droits == '' || $u_active == 1 ){
           echo"<td width='100'>&Eacute;tat</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
             <input type=\"radio\" name=\"$field_name\" value=\"1\" $actouno[$field_value]>Activ&eacute;  &nbsp; - &nbsp;
            <input type=\"radio\" name=\"$field_name\" value=\"0\" $actoudos[$field_value]>D&eacute;sactiv&eacute;
           </td>";
          }
          else{
           echo"<td width='100'>activ&eacute;</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>sans droits<input type=\"hidden\" name=\"$field_name\" value=\"0\"></td>";
          }
        }
        else{
          echo"<td>activ&eacute;</td><td><img src='$style_url/$theme/v$field_value.gif' border='none' alt='actif: $field_value'></td>";
        }
       }
        /////////////////////////////////////// COULEUR
       elseif(ereg("couleur",$field_act) && $field_length==6){
          if($field_value==""){
            $field_value="FFFFFF";
          }
         echo"<td width='100'>$nameifthefield</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
           #<input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" maxlength='6' size='6' onchange=\"document.getElementById('div$field_name').style.backgroundColor='#'+this.value\">
            <div id=\"div$field_name\" style=\"background-color:#$field_value;padding:3px;position:relative;height:20px;width:30px;border-color:#000000;border-style:solid;border-width:1px\"></div>";
            if($editmode==0 && !in_array('noedit',$comportement) ){
            echo"<a href='#a$field_name' name='a$field_name' onclick=\"choosecolor($i,'Backcolor','$field_name','hexa',event)\">changer la couleur</a>
              ";  
            }            
        echo"</td>";
       }
       elseif(ereg("couleur",$field_act) && $field_length==7){
          if($field_value==""){
            $field_value="#FFFFFF";
          }
         echo"<td width='100'>$nameifthefield</td><td><img src='$style_url/$theme/mysqltype-special.png' alt='special'>
           <input type=\"color\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\"/>
            ";                        
        echo"</td>";
       }
       
       /////////////////////////////////////// CARTE
       elseif($field_type == "int" && isset($mapcoord[$part]) && is_file('../'.$mapcoord[$part]) && ($field_name == "x" || $field_name == "y") ){
         if(  $field_name == "x"){
          $cx = $ro->x;  
          $cy = $ro->y;
          $getsi  = getimagesize('../'.$mapcoord[$part]);
          $minx = $getsi[0];
          if($minx > 300) $minx=300;  
          $miny = $getsi[1];
          if($miny > 300) $miny=300;  
           echo"<td width='100'><a class='info'>Position<span>coordonn&eacute;es <b>XY</b></span></a></td><td>";
           if($editmode==0 && !in_array('noedit',$comportement) ){
           echo"
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
           
           y:<input type=\"text\" name=\"y\" value=\"$cy\" style=\"width:30px\" maxlength=\"4\" onkeyup=\"document.getElementById('poscros').style.top = (parseInt(this.value)-5)\"><br>
           <div id='position' class='cadre' style='position:relative;display:block;width:$minx;height:$miny;overflow:hidden;padding:0px' onMouseOver='this.style.width=$getsi[0];this.style.height=$getsi[1];' 
onClick='this.style.width=$getsi[0];this.style.height=$getsi[1];' onMouseOut='this.style.width=$minx;this.style.height=$miny;'
onblur='this.style.width=$minx;this.style.height=$miny;'><img id='imgcoor' src='../$mapcoord[$part]' border='none'  onclick='point_it(event)'>
           <div id='poscros' style='position:absolute;left:$cx;top:$cy;width:10px;height:10px;font-size:10px;border:#000 1px dashed;color:#FFF;background:#F00;cursor:default'>+</div>
           </div>";
           }
           else{
            echo"
          <div id='position' class='cadre' style='position:relative;display:block;padding:0px' ><img id='imgcoor' src='../$mapcoord[$part]' border='none' >
           <div id='poscros' style='position:absolute;left:$cx;top:$cy;width:10;height:10;font-size:10px;border-width:1px;border-color:#00000;border-style:dashed;color:#FFFFFF;background-color:#FF0000'>+</div>
           </div>";
           }
           echo"
           </td>";
         }
       }
         ///////////////////////////////////// NO CHANGE
       elseif(substr($field_name,0,9) == "nochange_"){
             $nochnb++;
            $nameifthefield = ereg_replace("nochange_","",$field_name);
            if($field_value===0){
              $field_value='';
            }
            if($field_value!=''){
              $field_value=' : '.$field_value;
            }
            $ouvert = abs(get_pref("ouvert.$part.noch_$nochnb.conf"));
            echo"            
            </tr></table></div><div><table width='100%'><tr><td colspan='2' class='buttontd'>
            <a href='#nochange".($nochnb-1)."' title='titre pr&eacute;c&eacute;dent'><img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></a><a href=\"#nochange".($nochnb+1)."\" title='titre suivant'><img src='$style_url/$theme/class_down_off.jpg' alt='v' border='none'></a>
            <a onclick=\"sizpa('noch_$nochnb')\" style='cursor:pointer' name='nochange$nochnb'><b>$nameifthefield $field_value</b></a></td></tr>
            </table></div>
            <div id='noch_$nochnb' style='position:relative;overflow-y:hidden;width:100%;height:$ouvert"."px'><table width='100%'><tr><td colspan='2'>";
            
        }
        
       ///////////////////////////////////// PREFIXE
       elseif(substr($field_act,0,1) == "_"){
        if($nameifthefield == $field_name){
          $nameifthefield = substr($field_name,1,strlen($field_name));
        }
        if($field_value==""){
          //$field_value=$_SESSION[$field_name];
        }
        $nameifthefield = ucfirst(trim(str_replace("_"," ",$nameifthefield)));  
        echo"<td width='100'>$nameifthefield</td><td>";
         if($editmode==0 && !in_array('noedit',$comportement) ){
         echo"<img src='$style_url/$theme/mysqltype-special.png' alt='special'><input type=\"text\"  id='pref_txt_$i' name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:300px;\"   onfocus=\"if(this.readonly=='true'){this.style.width='1px';document.getElementById('pref_sel_$i').style.display='inline';this.blur();document.fourmis.pref_sel_$i.focus();this.readonly='true';}\" onblur=\"this.readonly='true'\">
         <select id='pref_sel_$i' name='pref_sel_$i' onchange=\"javascript:set$field_name(this.value);this.style.display='none';document.getElementById('pref_txt_$i').style.width='300px';\" onblur=\"this.style.display='none';document.getElementById('pref_txt_$i').style.width='300px';\" style=\"width:300px;display:none;\">
           <option value=''></option>";
          $listres = mysql_query("SELECT DISTINCT `$field_name` FROM `$tabledb` $incwhere $prefixselection ORDER BY `$field_name`");
          $prefixselection.=" AND `$field_name`='$field_value'";
          while($rowlist = mysql_fetch_object($listres)){
            $rowvalue = $rowlist->$field_name;
            if(trim($rowvalue)!=''){
              $s='';
              if($rowvalue==$field_value) $s='selected';
              echo"<option value=\"$rowvalue\" $s>$rowvalue</option>";
            }
          }
          echo"
          <option value='_-_' style='font-weight:bold'>- Nouveau</option>
         </select>
         <script language='javascript' type='text/javascript'>
           document.fourmis.$field_name.readonly='true';
          function set$field_name(koi){
            if(koi == '_-_'){
              /*pro = prompt(\"veuillez entrer un nom pour le nouvel &eacute;l&eacute;ment\",document.fourmis.$field_name.value);
              if(pro){
                document.fourmis.$field_name.value=pro;
              }  */
              document.fourmis.$field_name.readonly='false';
              document.fourmis.$field_name.focus();
              document.getElementById('pref_sel_$i').style.display='none';
              document.getElementById('pref_txt_$i').style.width='300px';
              
            }
            else if(koi!=''){
              document.fourmis.$field_name.value=koi;
              document.fourmis.$field_name.readonly='true';
            }            
          }
         </script>";
         }
         else{
           $listres = mysql_query("SELECT `$field_name` FROM `$tabledb` $incwhere $prefixselection AND `$field_name`='$field_value'");
          $prefixselection.="";
          $rowlist = mysql_fetch_array($listres);
          echo"$rowlist[0]";
         }
         echo"         
         </td>";      
      }
      /////////////////////////////DIR SELECT FILES
      elseif(substr($field_act,0,4) == "dir:"){
        $field_act = explode(':',$field_act);
        if(sizeof($field_act)==3 && is_dir($field_act[1])){
          echo"<td width='100'>$nameifthefield</td><td>
           <select name='$field_name'><option></option>";
           $dir = scandir($field_act[1]);
           foreach($dir as $entry){
            if($entry!='.' && $entry!='..'){//is_file($field_act[1].'/'.$entry)
              if($field_act[2]=='2'){
                $fv= substr($entry,0,strrpos($entry,'.',1));  
              }
              if($field_act[2]=='1'){
                $fv= $entry;  
              }
              if($field_act[2]=='0'){
                $fv= $field_act[1].'/'.$entry;    
              }
              echo"<option value='$fv'";
              if($field_value==$fv){
                echo ' selected';  
              }              
              echo">$fv</option>";
            }
           }
           echo"</select>
           </td>";  
        }
        else{
          echo"<td width='100'>$nameifthefield</td><td>
         Erreur de configuration sur ce champs
         </td>";
        }
      }
       ///////////////////////////////////// SUFIXE       
       elseif( ( ereg("_",$field_act) && mysql_query("SHOW COLUMNS FROM `$mot`") ) || ereg('@',$field_act) ){
         $refiled = $mot;
        
        $fieldoption = substr($field_act,strlen($mot)+1,strlen($field_act));
        
        if($nameifthefield == ucfirst(str_replace("_"," ",$field_act))){
          $nameifthefield = ucfirst($refiled);
        }
        
        
        if(ereg(">",$field_act)){
          $fieldoption = substr($fieldoption,0,strpos($fieldoption,">"));
          $nameifthefield .= " : ".substr($field_act,strpos($field_act,">")+1,strlen($field_act));
        }
        
        /////////////////// DEFAULT 
        if(substr($field_act,0,1) == "@"){
          $nameofoption = substr($field_act,1,strlen($field_act));  
          $field_value = $defaultvalue[$nameofoption];  
          echo"<td width='100'>$nameifthefield</td><td>
           <img src='$style_url/$theme/mysqltype-special.png' alt='special'><input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" readonly>
           </td>";    
        }
        //////////////////////////////
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
          
          $sepa='site';
            
          for($m=0; $m<sizeof($menu) ; $m++){
            $spart = $menupart[$m];
            $tablo = $menu[$spart];
            if(in_array($refiled,$tablo)){              
              if(substr($spart,0,7)=='worknet') $sepa='worknet';
              if(substr($spart,0,7)=='gestion') $sepa='gestion';  
              break;
            }
          }
          if(isset($_GET[$field_name])) $field_value=$_GET[$field_name];
          echo"<td valign='top' width='100'>$nameifthefield";
          if($field_value!=''){
            echo"<a class='info' href='./?option=$sepa&$refiled&edit=$field_value'>";
          }
          else{
            echo"<a class='info'>";
          }
          echo"<img src='$style_url/$theme/pile.gif' border='none'><span>Ce champ est reli&eacute; au tableau <b>$refiled</b></span></a></td><td>
           <img src='$style_url/$theme/mysqltype-special.png' alt='special'>";
          /////////////////// CHECKBOX 
           if((sizeof($fieldoptions)==3 && $fieldoptions[2]=='ch') || (sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlch')){
           
             if($editmode==0 && !in_array('noedit',$comportement) ){
            echo"<input type='hidden' name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\">
          ";
            $c=0;
            $hot=46;
            $ch=0;
            $prh='';
            $hut=0;
            $seled = '';
            if(sizeof($fieldoptions)==3){
              $plussearch='';
              if(isset($w_alias[$part][$field_name]) && $w_alias[$part][$field_name]!=''){
                $plussearch='AND '.$w_alias[$part][$field_name];                
              }
              $listres = mysql_query("SELECT $fieldoptionprint,`$fieldoption`,`id` FROM `$refiled` WHERE $fieldoptionprint!='' $plussearch ORDER BY 1");
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
                $prh.="<li><input type='checkbox' name='cho$i$c'   value=\"$rowid\"  title=\"$rowvaluu\" onclick=\"rempli(document.getElementById('ulch_$i'),document.fourmis.$field_name,document.fourmis.ch_cu_$i,document.getElementById('chu_$i'))\" $se>$rowvalue <a href='./?option=$option&$refiled&edit=$roid'>></a></li>";
              }
            }
            if(sizeof($fieldoptions)==2){
              $listres = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled`");
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
            <a href='#ch$i' name='ch$i' onclick=\"dec('ch_$i',$hot);dec('chu_$i',1)\"><b><img src='$style_url/$theme/class_down_off.jpg' alt='v' border='none'> D&eacute;velopper <img src='$style_url/$theme/class_down_off.jpg' alt='v' border='none'></b></a>
            <input type='text' name=\"ch_cu_$i\" value=\"$ch\" style='border:none;background:none;width:30px;text-align:right' readonly> / $c s&eacute;lectionn&eacute;s<br>
              <div id='ch_$i' style='display:block;width:380px;height:1px;overflow:hidden;'>
            <a href='#ch$i' onclick=\"dec('ch_$i',1);dec('chu_$i',hut)\"><b><img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'> r&eacute;duire <img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
            <li><input type='checkbox' name='all$i$c' onclick=\"tout(document.getElementById('ulch_$i'),this, document.fourmis.$field_name,document.fourmis.ch_cu_$i,document.getElementById('chu_$i'))\"> Tout</li>
            <ul id='ulch_$i'>  
            
            $prh
            </ul>
            <a href='#ch$i' onclick=\"dec('ch_$i',1);dec('chu_$i',hut)\"><b><img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'> r&eacute;duire <img src='$style_url/$theme/class_up_off.jpg' alt='^' border='none'></b></a>
            </div>
            <div id='chu_$i' style='display:block;width:380px;height:$hut"."px;overflow:hidden;'  onclick=\"dec('ch_$i',$hot);dec('chu_$i',1)\">            
            $seled
            </div>
            
            ";  
            }  
            else{
              if(sizeof($fieldoptions)==3){
                $listres = mysql_query("SELECT * FROM `$refiled` ORDER BY $fieldoptionprint");
                while($rowlist = mysql_fetch_object($listres)){
                  $rowvalue = $rowlist->$fieldoptionprint;
                  $rowid = $rowlist->$fieldoption;
                  $roid = $rowlist->id;
                  if(ereg('<'.$rowid.'>',$field_value)){
                    echo"- $rowvalue<br>";
                  }
                }  
              }
              if(sizeof($fieldoptions)==3){
                echo str_replace('><','<br>','>'.$field_value.'<');  
              }
            }        
          }  
          /////////////////// SELECT 
          elseif(sizeof($fieldoptions)==2 && $fieldoptions[1]=='nlse'){
            if($editmode==0 && !in_array('noedit',$comportement) ){
             echo"<select name=\"$field_name\" style=\"width:300px\">
              <option value=' '>liste des choix</option>";
              $listres = mysql_query("SELECT  `$fieldoptions[0]` FROM `$refiled`");
              while($rowlist = mysql_fetch_array($listres)){
                $gvl = explode("\n",$rowlist[0]);
                foreach($gvl as $rowvalue){
                  $rowvalue = trim($rowvalue);
                  if($rowvalue!=''){
                    $se = "";
                    if($rowvalue == $field_value){
                      $se = "selected";
                    }
                    $rowvaluu=str_replace('"','&quot;',$rowvalue);
                    echo"<option value=\"$rowvaluu\" $se>$rowvalue</option>";
                  }
                }
              }
              echo"</select>";
            }
            else{
              echo $field_value;
            }
          }
          else{
            if($editmode==0 && !in_array('noedit',$comportement) ){
              if(substr($fieldoptionprint,0,6) != 'CONCAT') $order = substr($fieldoptionprint.',',0,strpos($fieldoptionprint.',',','));
              
             echo"<select name=\"$field_name\" style=\"width:300px\">
              <option value=' '>liste des choix</option>";
              $sq='';
              if(isset($w_alias[$part][$field_name]) && $w_alias[$part][$field_name]!=''){
                $sq='AND '.$w_alias[$part][$field_name];                
              }
              $listres = mysql_query("SELECT `$fieldoption`,$fieldoptionprint  FROM `$refiled` WHERE 1 $sq GROUP BY `$fieldoption` ORDER BY $order");
              if(isset($where_multi) && $edit!='' && isset($this_from_multiple)){
                if(mysql_query("SELECT `$m_field` FROM `$refiled`")){
                  $listres = mysql_query("SELECT `$fieldoption`,$fieldoptionprint  FROM `$refiled` WHERE `$m_field`='$this_from_multiple' $sq GROUP BY `$fieldoption` ORDER BY $order");
                  
                }
              
              }
              while($rowlist = mysql_fetch_array($listres)){
                $rowvalue = $rowlist[1];
                $rowid = $rowlist[0];
                $se = "";
                if($rowid == $field_value){
                  $se = "selected";
                }
                if($rowvalue!=''){
                echo"<option value=\"$rowid\" $se>$rowvalue</option>";
                }
              }
              echo"</select>";
            }
            else{
              $listres = mysql_query("SELECT $fieldoptionprint  FROM `$refiled` WHERE `$fieldoption`='$field_value' ");
                $rowlist = mysql_fetch_array($listres);
                echo $rowlist[0];
            }
          }
          echo"</td>";
         }   
         
      }
      /////////////////////////////////////// URL 
       elseif(ereg("url",$field_act) && $field_type=="string"){
        $okim = "notok.gif";
        if($field_value === geturl($field_value)){
          $okim = "ok.gif";
        }
         echo"<td width='100'>$nameifthefield</td><td>";
         if($editmode==0 && !in_array('noedit',$comportement) ){
         echo"<img src='$style_url/$theme/mysqltype-string.png' alt='validation'/>
        <input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" 
        onkeyup=\"javascript: if(this.value != validlink(this.value)){ document.verifurl$i.src='$style_url/$theme/notok.gif'; } else{ document.verifurl$i.src='$style_url/$theme/ok.gif';}\">
        <img src='$style_url/$theme/$okim' alt='validation' onclick=\"javascript:document.fourmis.$field_name.value=validlink(document.fourmis.$field_name.value); this.src='$style_url/$theme/ok.gif';\" name='verifurl$i'>";
         }
         else{
           echo"$field_value";
         }
         echo"
        </td>";
       }
       ////////////////////////////////////////// PASSWORD
      elseif($field_name == "pass" || $field_name == "passe"){
        echo"<td width='100'>$nameifthefield</td><td>";
         if($editmode==0 && !in_array('noedit',$comportement) ){
            echo"<img src='$style_url/$theme/mysqltype-string.png' alt='texte'>";
          if(isset($pass_sql_encode) && is_array($pass_sql_encode) && in_array($part,$pass_sql_encode)){
            echo"<input  autocomplete=\"off\" type=\"password\" name=\"$field_name\" value=\"\" onblur=\"document.fourmis.adeliconfirm_$field_name.select(); \">Ce mot de passe sera <a class='info'>crypt&eacute;<span style='left:0px;top:0px'>$field_value</span></a>.<br/>
            <img src='$style_url/$theme/mysqltype-string.png' alt='texte'><input  autocomplete=\"off\" type=\"password\" name=\"adeliconfirm_$field_name\" value=\"\" onblur=\"if(document.fourmis.$field_name.value!=this.value){ alert('Vous avez entr&eacute; deux mots de passe diff&eacute;rents !'); document.fourmis.$field_name.select(); }\"> (Confirmez)";
          }
          else{
            echo"<input  autocomplete=\"off\" type=\"password\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\">";
          }
         }
         else{
          echo str_repeat('*',strlen($field_value));
         }
        if(isset($aff_pass) && $aff_pass==true && (!isset($pass_sql_encode) || (isset($pass_sql_encode) && !in_array($part,$pass_sql_encode)))){
          echo"<a class='info'>voir<span style='left:0px;top:0px'>$field_value</span></a>";
        }
        echo"</td>";
      }
      /////////////////////////////////////// STRING
       elseif($field_type == "string"){           
         echo"<td width='100'><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td>";
         if($editmode==0 && !in_array('noedit',$comportement) ){
         echo"<img src='$style_url/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:$field_width"."px\" maxlength=\"$field_length\">";
             if(ereg('tel',$field_name) || ereg('t&eacute;l',$field_name) || ereg('fax',$field_name) || ereg('phon',$field_name) || ereg('port',$field_name) || ereg('mob',$field_name)){
              $field_value = split("[azertyuiopqsdfghjklmwxcvbn/:]",ereg_replace("[)( \n.-]",'',strtolower($field_value)).'/');  
              foreach($field_value as $numb){
                if(ereg("[0-9]",$numb) && $numb!=NULL){
                  echo " <a href='callto:$numb' class='petittext'>appeler : ".$numb."</a>";
                }
              }
            }
         }
         else{
           
            if(ereg('tel',$field_name) || ereg('t&eacute;l',$field_name) || ereg('fax',$field_name) || ereg('phon',$field_name) || ereg('port',$field_name) || ereg('mob',$field_name)){
              $field_value = split("[azertyuiopqsdfghjklmwxcvbn/:]",str_replace("[)( \n.-]",'',strtolower($field_value)).'/');  
              foreach($field_value as $numb){
                if(ereg("[0-9]",$numb) && $numb!=NULL){
                  echo "<a href='callto:$numb'>$numb</a> ";
                }
              }
            }
            else{  
               echo"$field_value";
            }
         }
         echo"</td>";
       }
       /////////////////////////////////////// INT
       elseif($field_type == "int" || $field_type == "real"){           
         echo"<td width='100'><a class='info'>$nameifthefield<span>Nombre</span></a></td><td>";
         if($editmode==0 && !in_array('noedit',$comportement) ){
           $ft='text';
           if($field_type == "int") $ft = 'number';
         echo"<img src='$style_url/$theme/mysqltype-int.png' alt='num&eacute;rique'><input type=\"$ft\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:150px\" maxlength=\"$field_length\">";
         }
         else{
         echo"$field_value";
         }
         echo"</td>";
       }
       /////////////////////////////////////// DATE
       elseif($field_type == "date" || $field_type == "time" || $field_type == "datetime"){
         if($field_value==""){
          $field_value=$defaultvalue[$field_type];
        }
        if(isset($_GET['insert_date_into']) && $edit==''){
          $field_value=$_GET['insert_date_into'];
        }  
         echo"<td width='100'><a class='info'>$nameifthefield<span>Date au format standard<br>date: aaaa-mm-jj<br>heure: hh:mm:ss</span></a></td><td>";
         if($editmode==0 && !in_array('noedit',$comportement) ){
           if($field_type=='datetime') $field_value=str_replace(' ','T',substr($field_value,0,16)).":00";
           echo"<img src='$style_url/$theme/mysqltype-date.png' alt='$field_type' style='cursor:pointer' onclick=\"javascript:contextdate(event);cal.location='$opencalendar&#63;x_id=$x_id&amp;cible=fourmis.$field_name&amp;date='+document.fourmis.$field_name.value+'&amp;type=$field_type'\"><input type='$field_type";
           if($field_type=='datetime') echo"-local' step='60'";
           echo"' name=\"$field_name\" value=\"$field_value\">";
         }
         else{
           if($field_value == "0000-00-00 00:00:00" || $field_value == "0000-00-00" || $field_value == "00:00:00"){
            $field_value = "...";  
          }
          elseif($field_type == "date"){
            $field_value = date("d/m/Y",strtotime($field_value));  
          }
          elseif($field_type == "time"){
            $field_value = substr($field_value,0,5);  
          }
          elseif($field_type == "datetime"){
            $field_value = date("d/m/Y - H:i",strtotime($field_value));  
          }
           echo $field_value;
         }
         if($editmode==0 && !in_array('noedit',$comportement) ){
           if($field_type=='datetime') echo"<a style='cursor:pointer' onclick=\"document.fourmis.$field_name.value='".str_replace(' ','T',date('Y-m-d H:i')).":00';\" class='petittext'>Maintenant</a>";
          else echo"<a style='cursor:pointer' onclick=\"document.fourmis.$field_name.value='".$defaultvalue[$field_type]."';\" class='petittext'>Maintenant</a>";
         }
        echo" </td>";
       }
      
       /////////////////////////////////////// TEXTE
       elseif($field_type == "blob"){
         $nameifthefield = ucfirst($nameifthefield);
         if($editmode==1){
          echo "<td><a class='info'>$nameifthefield</a></td> <td>".html_my_text("$field_value")."</td>";
        }
        else{
         
        $edition=0;
        $editchange=1;
         if( (!isset($types[$part]) || (isset($types[$part]) && !ereg("plain",$types[$part]))) && !ereg('code',$field_act) && !ereg('meta_',$field_act)){
         array_push($ishtmlll,array($i,$nameifthefield));
         $edition=1;        
        }
        if(ereg('code',$field_act) || ereg('meta_',$field_act) || ereg("plain",$types[$part]) || ereg("html",$types[$part]) ){
          $editchange=0;
        }
         echo"<td valign='top' colspan='2'><a class='info'>$nameifthefield<span>Texte libre multiligne</span></a>";
         //<a href='./?option=$option&part=$part&edit=$edit&plain'>
         editor($field_name,$field_value,$i,$stylo,$edition,$editchange);
        echo" </td>";
         }
       }
       /////////////////////////////////////// DEFAULT
       else{
         echo"<td width='100'><a class='info'>$nameifthefield<span>Chaine de texte libre</span></a></td><td>";
         if($editmode==0 && !in_array('noedit',$comportement) ){
         echo"<img src='$style_url/$theme/mysqltype-string.png' alt='texte'><input type=\"text\" name=\"$field_name\" value=\"".str_replace('"','&quot;',$field_value)."\" style=\"width:300px\" maxlength=\"$field_length\">";
         }
         else{
         echo"$field_value";
         }
         echo"</td>";
       }       
       echo"</tr> <tr><td colspan='2'>
      ";
      //<img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'>
       if(isset($_GET[$field_name]) && $field_name!=$part){
         echo"<script language='javascript' type='text/javascript'>
        document.fourmis.$field_name.value = \"".str_replace('"','\"',stripslashes($_GET[$field_name]))."\";
         </script>";
       }
       echo"</td></tr>";
     }
     
     echo"</table></div>
     <script language='javascript'>";
     if($nochnb>1){
       echo"
      resizteaxtarea=false;";
       /*for($l=1 ; $l<=$nochnb ; $l++){
      echo"sizpa('noch_$l',3);";
       }*/
       echo"";
     }
     if(isset($autocomplete[$part])){
      foreach($autocomplete[$part] as $autocible=>$autocmd){
        echo"
        document.fourmis.$autocmd[0].onblur=function(){
          document.fourmis.$autocible.value = $autocmd[1](document.fourmis.$autocmd[0].value);
        }
        ";
      }
     }
  $allreps=0;
  echo"</script>";
  

  echo"</td><td align='left' valign='top' id='coldroit'>";
  ///////////////////////////////////////////////////////////////////////////////////////////////////// TOTEM
  
  if($option=='worknet' && ($part=="clients" || $part=="adeli_messages" || $part=="adeli_message_template")){
      insert("_worknet_totem");
      if(is_file('bin/_worknet_totem.php')){
        include('bin/_worknet_totem.php');
      }
      else{
        include('$style_url/update.php?file=_worknet_totem.php&1');
      }
    }
  if($edit==""){
    echo"les autres options seront accessibles apr&eacute;s un premier enregistrement";                
  }
  else{
    //echo"<b>options</b>";  
    
    
    
    if(in_array('agenda',$opt)){
    if(is_file('bin/_agenda_link.php') ){
      include('bin/_agenda_link.php');
    }
    else{
      include('$style_url/update.php?file=_agenda_link.php&1');
    }  
    }
     if( (sizeof($comportement) > 1 || isset($fichiers[$part])) ){  
    insert('_fichiers');
    if(is_file('bin/_fichiers.php')){
      include('bin/_fichiers.php');
    }
    else{
      include('$style_url/update.php?file=_fichiers.php&1');
    }
    
    
     }
     ////////////////////////////////////////////////////////////////// CHILDREN
    
      insert('_children');
      if(is_file('bin/_children.php')){
        include('bin/_children.php');
      }
      else{
        include('$style_url/update.php?file=_children.php&1');
      }
     }
  
     echo"<span id='tot_droit_cont'></span>     
     
     </td></tr>
     <tr><td colspan='2'><img src='$style_url/$theme/g.gif' alt='_' height='3' width='100%'></td></tr>
    <tr><td colspan='2' align='left'>
    
    <script language='javascript' type='text/javascript'>  
  function addfile(ou){
    
      document.fourmis.action+='&addfile='+ou;
     }
    function addspan(ki,ou){           
      //document.getElementById('spa_ico').innerHTML=\"$funico\";
      //document.getElementById('spa_dir').innerHTML=\"$fundir\";
      if(ki != ''){";
      if($editmode==0 && !in_array('noedit',$comportement) ){
        echo"document.getElementById(ki).innerHTML=\"<input type='file' name='file[]' onchange=addfile('\"+ou+\"')>\";";
      }
      echo"}
    }
    addspan('','');
  </script>";
  if($editmode==0 && !in_array('noedit',$comportement) ){
    $nochnb++;
    echo"<a name='nochange$nochnb'></a>";
    if( !in_array("noedit",$comportement)){
      if($part=="adeli_messages" && $edit==''){
        echo"<p><input class=\"grosbouton\" type=\"submit\" value=\"Envoyer\"></p><br/>
          <input class=\"buttontd\" type=\"button\" value=\"Retour\" onclick=\"history.back();\">
        ";
      }
      else{
        echo"<p><input class=\"grosbouton\" type=\"submit\" value=\"Enregistrer\"></p><br/>
          <input class=\"buttontd\" type=\"button\" value=\"Retour\" onclick=\"document.location='./?option=$option&part=$part&d=$d';\">
          &nbsp;  &nbsp;  &nbsp;  &nbsp;  
        ";
        echo"<input class=\"buttontd\" type=\"button\" value=\"Enregistrer et revenir\" onclick=\"document.fourmis.action='./?option=$option&part=$part&$action=$edit';document.fourmis.submit()\"> ";
        if( !in_array("nonew",$comportement)){  
          echo"<input class=\"buttontd\" type=\"button\" value=\"Enregistrer et ajouter\" onclick=\"document.fourmis.action+='&new';document.fourmis.submit()\"> ";
        }
        if(is_numeric($edit) && ($u_droits == '' || $u_active == 1) ){
          echo"<input class=\"buttontd\" type=\"button\" value=\"Supprimer\" onclick=\"confsup($edit)\"> ";
        }
        echo"&nbsp;  &nbsp;  &nbsp;  &nbsp;";
        if( $clonid!=0 && !isset($_GET['clone'])){  
          echo"<input class=\"buttontd\" type=\"button\" value=\"Rendre ind&eacute;pendant\" onclick=\"document.fourmis.action+='&indep';document.fourmis.submit()\">
          <input class=\"buttontd\" type=\"button\" value=\"Acc&eacute;der au parent\" onclick=\"document.location='./?option=$option&part=$part&d=$d&edit=$clonid\"> ";
        }
        if( $clonid==0 ){  
          echo"<input class=\"buttontd\" type=\"button\" value=\"Cloner\" onclick=\"document.location='./?option=$option&part=$part&d=$d&edit=$edit&clone';\"> ";
        }
      }
    }
  }
  else{
    echo"<a href=\"./?option=$option&part=$part&amp;edit=$edit&modif\" class='grosbouton'>Modifier</a>";
  }
}
  if(($u_droits!="" || $u_active == 0 ) && ereg("@",$r_alerte) && !in_array('noedit',$comportement)){  
    if(!isset($_GET['alert'])){
    $path = getenv('SCRIPT_NAME');
  
    $urltovalid="http://$prov$path?option=$option&amp;part=$part&amp;setvalid=$edit";  
    if($clonid != 0){
      $urltovalid="http://$prov$path?option=$option&amp;part=$part&amp;unsetclon=$edit";
    }    
    echo"
    </td></tr>
    <tr><td colspan='2' align='center'>
    </form>
    
    envoyer une alerte pour la validation<br>
    <form action='./?option=$option&part=$part&d=$d&alert&edit=$edit' method='post' name='alert_form'>
    <textarea name='message' cols='50' rows='6'>Alerte mise &agrave; jour
    
$u_nom a modifi&eacute; un &eacute;l&eacute;ment qui doit &ecirc;tre valid&eacute; :
  
modifier l'article > http://$prov$path?option=$option&amp;part=$part&edit=$edit
valider > $urltovalid
    
    </textarea><br><br>
    <input class='buttontd' type='submit' value='envoyer'>    
      ";  
  }
  else{
    $message = stripslashes($_POST['message']);  
    //$l = $_GET['l'];
    //$p_date = date("Y-m-d H:i:s");
    if(mail($r_alerte,"alerte mise &agrave; jour",$message,"from: $u_login<$u_email>")){
      $return.=returnn("message envoy&eacute; avec succ&eacute;s &agrave; \"$r_alerte\"","009900",$vers,$theme);
    }
    else{
      $return.=returnn("votre message n'a pu &ecirc;tre envoy&eacute;","990000",$vers,$theme);
    }
    /*if(mysql_query("INSERT INTO adeli_message()  VALUES('','$x_id','$p_date','$u_id','0','alerte mise � jour','$message','0')")){
      $return.=returnn("message envoy� avec succ�s","009900",$vers,$theme);
    }
    else{
      $return.=returnn("votre message n'a pu &ecirc;tre envoy�","990000",$vers,$theme);
    }*/
  
    
    $message = nl2br($message);
    echo"
    <table cellspacing='0' cellpadding='2' class='cadrebas'>
      <tr><td class='buttontd'>Alerte</td></tr>    
      <tr><td>      
      <table>
        <tr><td>
        
        $message
        
        
        </td></tr>
        
      
       <tr><td align='right'>        
          <div class=\"buttontd\" style='width:140px'><a href=\"./?option=$option&part=$part&edit=$edit\">Retour &agrave; l'article</a></div>
          <div class=\"buttontd\" style='width:140px'><a href=\"./?option=$option&part=$part\">Retour &agrave; la liste</a></div>
        </td></tr>
      
      
      </table>
      
      </td></tr>
      </table>
    ";
    }
  }
  
    
  
  
}
/********************************************************************************************************************

                  STATISTIQUES

**********************************************************************************************************************/

elseif($subpart=="statistiques"){  
  insert('_statistiques');
  if(is_file("bin/_statistiques.php")){
    include("bin/_statistiques.php");
  }
  else{
    include("$style_url/update.php?file=_statistiques.php");
  }
}
/********************************************************************************************************************

                  EXPORTER

**********************************************************************************************************************/

elseif($subpart=="exporter"){  
  insert('_exporter');
  if(is_file("bin/_exporter.php")){
    include("bin/_exporter.php");
  }
  else{
    include("$style_url/update.php?file=_exporter.php");
  }
}
/********************************************************************************************************************

                  IMPORTER

**********************************************************************************************************************/

elseif($subpart=="importer"){  
  insert('_importer');
  if(is_file("bin/_importer.php")){
    include("bin/_importer.php");
  }
  else{
    include("$style_url/update.php?file=_importer.php");
  }
}
/********************************************************************************************************************

                  ANNUAIRE

**********************************************************************************************************************/

elseif($subpart=="annuaire"){  
  insert('_annuaire');
  if(is_file("bin/_annuaire.php")){
    include("bin/_annuaire.php");
  }
  else{
    include("$style_url/update.php?file=_annuaire.php");
  }
}
/********************************************************************************************************************

                  LISTE

**********************************************************************************************************************/

else{  
  echo"
  <tr><td valign='top' colspan='3' align='center'>
  <script language='javascript'>
  function sela(k){
    var allche = document.listage.getElementsByTagName(\"input\");

    for (var i=2; i<allche.length; i++) {
      if(allche[i].className!='noche'){
        allche[i].checked=k;
      }
    }
  }
  function conmulti(k){
    var transk = new Array();
    transk['active']='activer';
    transk['desactive']='d&eacute;&eacute;sactiver';
    transk['delete']='supprimer';
    nbsel=0;
    var allche = document.listage.getElementsByTagName(\"input\");
    for (var i=2; i<allche.length; i++) {
      if(allche[i].type=='checkbox' && allche[i].checked==true && allche[i].className!='noche') nbsel++;
    }
    if(nbsel>0){
      pro = confirm(\"&ecirc;tes vous certain de vouloir \"+transk[k]+\" les \"+nbsel+\" objets s&eacute;lectionn&eacute;s ?\");
      if(pro){
        document.listage.action+='&multi='+k;
        document.listage.submit();
      }
    }
    else{
      alert(\"aucun objet n'est s&eacute;lectionn&eacute;\");
    }
  }
  </script>
  <form name='listage' action='./?option=$option&part=$part&d=$d' method='post' style='text-align:left'>";
  if(!in_array("nonew",$comportement) && (!isset($u_restreint) || $u_restreint[1]!=$part )){
    echo"<p><a href='./?option=$option&part=$part&edit$addlien' class='grosbouton'>Nouveau</a></p><br><br>"; 
   }
  echo"
  <input type='checkbox' onclick='sela(this.checked)'>
   -  ";
   if($u_droits == '' || $u_active == 1 ){
   echo"
  <a href='#' onclick=\"conmulti('active')\"><img src='$style_url/$theme/v1.gif' border='none' alt='activer'></a>
  <a href='#' onclick=\"conmulti('desactive')\"><img src='$style_url/$theme/v0.gif' border='none' alt='d&eacute;sactiver'></a>
  <a href='#' onclick=\"conmulti('delete')\"><img src='$style_url/$theme/trash.gif' border='none' alt='supprimer'></a>
  &nbsp;&nbsp;&nbsp;";
   
  }
  
  if( !isset($_GET['exporter'])){
    if($modifouille!="" && (isset($_GET['iniliste']) || $validouille=='') && $_SESSION['pra']==false){
      echo"
      <script language='javascript' type='text/javascript'>
      function changedenom(parta,part,ki,koi){
        glok = prompt(parta+\"\\nVeuillez saisir le nouvel intitul&eacute;\",ki);  
        if(glok){
          document.location='./?option=$option&part=$part&rename='+encodeURIComponent(ki)+'&en='+encodeURIComponent(glok)+'&in='+part;
        }  
        koi.value='';
      }
      </script>    
      
      <a href='./?option=$option&part=$part&printall=1&s' class='buttontd'>Tout afficher</a>
      
      
      $modifouille";
    
    }
    else{
      if($modifouille!=""){
        echo"<a href='./?option=$option&part=$part&liste&iniliste' class='buttontd'>Retour</a>";
        if($_SESSION['pra']==false) echo"  <a href='./?option=$option&part=$part&printall=1&s' class='buttontd'>Tout afficher</a>";
      
      }    
      
      insert("inc_liste");
      if(is_file("bin/inc_liste.php")){
        include("bin/inc_liste.php");
      }
      else{
        include("$style_url/update.php?file=inc_liste.php");
      }
    }
  }  
  echo"</form>";

}

echo"</td></tr></table></td></tr></table>";      
      if(isset($_GET['edit'])){    
      echo"</form>";
    }
  }
  }
  else{
    echo"
    La communication n'a pas pu &ecirc;tre &eacute;tablie avec la base de donn&eacute;es... <br>travail impossible.<br>
    <!-- $base, $host, $login -->
    ";
  }
  
  }
  elseif( in_array("dir",$comportement) && isset($dirfiles)){
    if(is_dir("../".$dirfiles[$part]) && $dirfiles[$part]!=""){
      insert("inc_dir");
      if(is_file("bin/inc_dir.php")){
        include("bin/inc_dir.php");
      }
      else{
        include("$style_url/update.php?file=inc_dir.php");
      }
    }
    else{
      echo"<table cellspacing='0' cellpadding='2' width='80%' class='cadrebas'><tr>
    <td class='menuselected' width='80'>r&eacute;pertoire</b></td>
    <td class='buttontd'></td></tr>    
    <tr><td colspan='2'>le dossier recherch&eacute; n'existe pas</td></tr></table>";
    mkdir("../".$dirfiles[$part],0777);
    }
  }
}  
}  
else{
    echo"
  <table cellspacing='0' cellpadding='0' border='0' class='cadrebas' width='100%'>
   <tr style='height:20px'><td class='buttontd'><b>Accueil ".ucfirst($option)."</b></td></tr>
   <tr><td class='cadrebas'>
   <table style='margin:20px;width:90%'>";
 foreach($menu as $spart=>$tablo){
 
 
      $cols = sizeof($tablo);  
      //if( ($option=="site" && substr($spart,0,7)!="gestion" && substr($spart,0,7)!="worknet") || (($option==substr($spart,0,7) && substr($spart,0,7)=="worknet") || ($option==substr($spart,0,7) && substr($spart,0,7)=="gestion")) ){    
      echo"<tr class='bando'><td colspan='3'><b>$spart</b></td></tr>";
      $tablk = array_keys($tablo);
       for($m=0; $m<sizeof($tablo) ; $m++){
         $tk = $tablk[$m];
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
      $humanpart = ucfirst($humanpart);  
      
      $nbro="";
      if(mysql_query("SHOW COLUMNS FROM `$tablo[$tk]`")  ){
        $res = mysql_query("SELECT `id` FROM `$tablo[$tk]`");
        $nbro = "(".mysql_num_rows($res)." enregistrements)";
      }
      $vasaj="";
      if(isset($types[$tablo[$tk]])){
        $comportement = explode(",",$types[$tablo[$tk]]);
      }
      if(mysql_query("SHOW COLUMNS FROM `$tablo[$tk]`")){  
        if( !isset($comportement) || !in_array("nonew",$comportement) ){
          $vasaj = " | <a href='./?option=$option&$tablo[$tk]&option=$option&edit'>nouveau</a> | <a href='./?option=$option&part=$tablo[$tk]&subpart=importer'>importer</a>";
        }
        if( !isset($comportement) || in_array("txt",$comportement) || !in_array("dir",$comportement) ){  
          $vasaj .= " | <a href='./?option=$option&part=$tablo[$tk]&subpart=exporter'>exporter</a>   | <a href='./?option=$option&part=$tablo[$tk]&subpart=statistiques'>statistiques</a>";
        }
        else{
          //$vasaj .= " | <font class='petittext'>exporter</font>   | <font class='petittext'>statistiques</font> ";
        }
      }
      elseif( isset($dirfiles[$tablo[$tk]]) && is_dir('../'.$dirfiles[$tablo[$tk]]) ){
        $php = phpversion();
        $vasaj = " dossier de fichiers";
        if($php >= 5){
          $sca = scandir('../'.$dirfiles[$tablo[$tk]]);
          $nbro = '('.(sizeof($sca)-2)." &eacute;l&eacute;ments)";
        }
      }      
      
        echo"<tr><td> - <a href='./?option=$option&part=$tablo[$tk]&d=$d' class='menuuu'><b>$humanpart</b></a></td>";
        if($debit==0) echo"  <td>$vasaj</td><td>$nbro</td>";
        echo"</tr>";
      }
      //}
  }  
  echo"</table> </td></tr></table>";
}    
echo"
<script language='javascript'>
 incwh='".urlencode($incwhere)."';
</script>
<div id='delfilemask' style=\"position:absolute;left:0px;top:0px;width:100%;height:100%;visibility:hidden;background:url('$style_url/$theme/bgalpha.gif')\">
   <table style=\"width:100%;height:100%;\">
   <tr><td  align='center' valign='middle'>   
   <table width='300' cellpadding='5'  class='alert'>
   <tr><td  align='center' valign='middle'>
   <b>&ecirc;tes vous s&ucirc;r de vouloir supprimer maintenant?</b><br><br>   
   <table cellspacing='5' cellpadding='0' border='0'>     <tr>     
    <td class=\"buttontd\"><a href='./?decon'><b>oui</b></a></td>    
    <td class=\"buttontd\"><a href='#/' onClick=\"reconnect()\"><b>non</b></a></td>    
    </tr></table>   
   </td></tr>
   </table>   
   </td></tr>
   </table>
   </div>   
   ";
?>