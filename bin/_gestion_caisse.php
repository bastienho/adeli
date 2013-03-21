<?php // 30 > Caisse enregistreuse ;
insert("_gestion_caisse_ticket");
	if(!isset($_SESSION['totnumven'])){
		$_SESSION['totnumven'] = 0;
	}
	if(!isset($_SESSION['caisid'])){
		$_SESSION['caisid'] = "k0";
	}
	if(isset($_GET['caisid'])&&$_GET['caisid']!=NULL){
		$_SESSION['caisid'] = $_GET['caisid'];
	}
	if(!isset($_SESSION["ventes"])){
		$_SESSION["ventes"] = array("k0"=>date("d/m/Y - H:i:s"));
		$_SESSION['clientidc']['k0'] = 0;
	}
	if(isset($_GET['newvente'])){
		$_SESSION['totnumven']++;
		$idnextvent = $_SESSION['totnumven'];
		$_SESSION['caisid']="k$idnextvent";
		$_SESSION["ventes"]["k$idnextvent"] = date("d/m/Y - H:i:s");	
		$_SESSION['clientidc']['k'.$idnextvent] = 0;
	}
	$caisid = $_SESSION['caisid'];
	$totnumven = $_SESSION['totnumven'];
	
	if(!isset($_SESSION["osier$caisid"])){
		$_SESSION["osier$caisid"] = array(
		"content"=>array(),
		"mode"=>"");
	}
	
	if(!isset($_SESSION["osier$caisid"]["mode"]) || !is_array($_SESSION["osier$caisid"]["mode"])){
		$_SESSION["osier$caisid"]["mode"]=array();
	}
	if(!isset($_SESSION["osier$caisid"]["rendu"])){
		$_SESSION["osier$caisid"]["rendu"]=0;
	}
	
	$modechoix = array(
	"es"=>"espèces",
	"ch"=>"chèque",
	"cb"=>"carte bancaire",
	"pp"=>"Paypal",
	"ba"=>"bon d'achat"
	);
	
	
	
	
$keyvente = array_keys($_SESSION["ventes"]);
?>
<script language="JavaScript">
	function effectnumber(numb){
		numb = Math.abs(document.nume.numbo.value);
		var numA = new RegExp("^[0-9]+$","g");
		//document.caisse.histo.value = "-"+document.caisse.act.value+"\n"+document.caisse.histo.value;
		/*if(numA.test(numb) && numb > 0){
			last = document.caisse.act.value;
			if(last.substr(0,16) == "nouvel article :" || last.substr(0,10) == "quantité :"){
				document.caisse.act.value = 'quantité : '+numb;				
			}
			else{
				document.caisse.histo.value = "- sélectionnez d'abord un article\n"+document.caisse.histo.value;
			}
		}*/
		document.nume.numbo.value = '';
	}
	function addnumber(numb){
		document.nume.numbo.value += numb;
		document.nume.numbo.focus();
		//document.cbcode.codebarre.focus();
	}
	function addcb(){
		/*/document.caisse.histo.value = "-"+document.caisse.act.value+"\n"+document.caisse.histo.value;
		if(document.cbcode.codebarre.value != ""){
			newcb = document.cbcode.codebarre.value;
			document.caisse.act.value = "nouvel article : "+newcb;
			document.cbcode.codebarre.value = "";
		}
		else{
			document.caisse.histo.value = "- entrez d'abord un code barre\n"+document.caisse.histo.value;
		}*/
	}
	function reduc(type){
		if(type==1){
			last=document.nume.last.value;
			efrec = prompt("indiquez le pourcentage de remise pour l'article sélectionné");
			if(efrec){
				suivi.location="_gestion_caisse_ticket.php?red=pa&art="+last+"&rem="+efrec;
			}
		}
		if(type==2){
			last=document.nume.last.value;
			efrec = prompt("indiquez le prix forcé pour l'article sélectionné");
			if(efrec){
				suivi.location="_gestion_caisse_ticket.php?red=pfa&art="+last+"&rem="+efrec;
			}
		}
		if(type==3){
			last=document.nume.last.value;
			efrec = prompt("indiquez le pourcentage de remise globale");
			if(efrec){
				suivi.location="_gestion_caisse_ticket.php?red=pt&rem="+efrec;
			}
		}
		if(type==4){
			last=document.nume.last.value;
			efrec = prompt("indiquez le prix forcé global");
			if(efrec){
				suivi.location="_gestion_caisse_ticket.php?red=pft&rem="+efrec;
			}
		}
	}
	function jeve(koi){
		document.caisse.histo.value = "-"+document.caisse.act.value+"\n"+document.caisse.histo.value;
		document.caisse.act.value = "action : "+koi;
		leretur="";
		if(koi == 'caisse'){
			leretur="<b>Caisse</b><br>";
		}
		if(koi == 'reduction'){
			leretur="<b>Réduction</b><br><br>- <a href='#' onclick='reduc(1)'>pourcentage article</a><br>- <a href='#' onclick='reduc(2)'>prix forcé article</a><br>- <a href='#' onclick='reduc(3)'>pourcentage total</a><br>- <a href='#' onclick='reduc(4)'>prix forcé total</a><br>";
		}
		if(koi == 'archives'){
			leretur="<b>Archives</b><br>";
		}
		if(koi == 'bon achat'){
		leretur="<b>Bons d'achat</b><br><?
		   $res = mysql_query("SELECT * FROM bons WHERE valid=1 AND mont>0");
			while($row = mysql_fetch_object($res)){
				$prest = $row->prest;
				$alpha = $row->alpha;
				$num = $row->num;
				$usr = $row->usr;
				$mont=$row->mont;
				$valid=$row->valid;
				$id = $row->id;
			   echo"- <a href='_gestion_caisse_ticket.php?codebon&bondachat=$alpha$num' target='suivi'>$mont&euro; (client $usr)</a><br>";
	
			}
		?>";
		}
		if(koi == 'type de vente'){
			leretur="<b>Type de vente</b><br>-<a href='_gestion_caisse_ticket.php?ventetype=détail' target='suivi'>détail</a><br>-<a href='_gestion_caisse_ticket.php?ventetype=demi-gros' target='suivi'>demi-gros</a><br>-<a href='_gestion_caisse_ticket.php?ventetype=gros' target='suivi'>gros</a>";
		}
		if(koi == 'retour article'){
			leretur="</form><b>Retour d'article</b><br><form action='_gestion_caisse_ticket.php' target='suivi' method='get'><input type='text' name='codebarre'><input type='hidden' name='ret' value='1'><input type='submit' value='ok'></form>";
		}
		document.getElementById('effect').innerHTML=leretur;
	}
	function reso(last,total){
		//document.nume.last.value=last;
		//document.nume.total.value=total;
		document.nume.last_art.value=last;
		//preview.location="preview.php?id="+last;
		//document.getElementById('totall').innerHTML=total+" &euro;";
	}
</script>
<style>
		#pavenum{
			display:block;
			width:210px;
		}
		#pavenum a, #pavenum input{
			font-size:30px;
			display:block;
			float:left;
			width:35px;
			height:35px;	
		}
		#pavenum a, #pavenum input[type=submit]{
			padding:10px;	
		}
		#csuivi input, #csuivi textarea{
			background:#CCCCEE;width:200px;
		}
		#moyen{
			list-style:none;
		}
		</style>
<table>
	<tr>
    	<td valign="top">
            <div class='buttontd'>
            
                    <a href='#' onclick="jeve('reduction')">Réduction</a>
                    <a href='#' onclick="jeve('bon achat')">Bons</a><br>
                    <a href='#' onclick="jeve('type de vente')">Gros</a>
                    <a href='#' onclick="jeve('retour article')">Retour</a>
                    
            </div>
            <ul class='buttontd'>
                <b>ventes en cours</b>
                
                <?php
                for($i=0 ; $i<sizeof($_SESSION['ventes']) ; $i++){
                    $idven = $keyvente[$i];
                    $daven = $_SESSION['ventes'][$idven];
                    if($caisid == $idven){
                       $daven = '>'.$daven;
                    }
                    echo"<li><a href='./?option=gestion&part=gestion_caisse&caisid=$idven'>$daven</a> ($idven)</li>";
                }
                        
                ?>
                <li><a href='./?option=gestion&part=gestion_caisse&newvente'>nouvelle vente</a>	</li>
             </ul>		
		</td>
		<td valign="top">
		
<!-- caisse pavé numerique -->
		
		<form name='nume' action='bin/_gestion_caisse_ticket.php' method='get' target='suivi' id='pavenum' onsubmit="glos();">
		
		<input type='text' name='numbo' class='caisse' style='width:200' onkeyup='gesword()'>
         <div style='position:relative'>
				<div style='position:absolute;left:-5;top:20;z-index:450;width:150px;'>
					  <span id='gesluto'></span>
				</div> 
		 </div>
        <input type='hidden' name='last' value=''>
		<input type='hidden' name='last_art' value=''>
        <input type='hidden' name='ade' value=''>
		<input type='hidden' name='pu' value=''>
		<input type='hidden' name='tva' value=''>
		<input type='hidden' name='ref' value=''>
		
		<a href='#' onclick="addnumber('7')" title='7' class='buttontd' accesskey='7'>7</a>
		<a href='#' onclick="addnumber('8')" title='8' class='buttontd'>8</a>
		<a href='#' onclick="addnumber('9')" title='9' class='buttontd'>9</a><br>
		
		<a href='#' onclick="addnumber('4')" title='4' class='buttontd'>4</a>
		<a href='#' onclick="addnumber('5')" title='5' class='buttontd'>5</a>
		<a href='#' onclick="addnumber('6')" title='6' class='buttontd'>6</a><br>
				
		<a href='#' onclick="addnumber('1')" title='1' class='buttontd'>1</a>
		<a href='#' onclick="addnumber('2')" title='2' class='buttontd'>2</a>
		<a href='#' onclick="addnumber('3')" title='3' class='buttontd'>3</a><br>
		
		<a href='#' onclick="addnumber('0')" title='0' class='buttontd'>0</a>
		<a href='#' onclick="addnumber('.')" title='.' class='buttontd'>,</a>
		<input type='submit' class='buttontd' value='ok'>
		
		</form>		   
		  <script language='javascript'> 
				function glos(){
					document.nume.numbo.value='';
					document.getElementById('gesluto').innerHTML='';
				}
				function gesword(){
					clef = document.nume.numbo.value.toLowerCase();
					if(!isNaN(clef)){
						document.getElementById('gesluto').innerHTML='';
					}
					else if(clef != ''){
						document.nume.ref.value=clef;
						envoyer('bin/inc_ajax.php?scan=gestion_article','q',clef+'&client='+suivi.farme.client.value+'&part=gestion_caisse','gesluto');
					}
					else{
						document.getElementById('gesluto').innerHTML='';
					}
				}
				function ajoutlign(s,p,q,t,r){			
					document.nume.ade.value=s;
					document.nume.pu.value=p;
					document.nume.numbo.value=q;
					document.nume.tva.value=t;
					document.nume.ref.value=r;
					document.nume.submit();	
					glos();
				}
			</script>
        Mode de paiement :
        <ul id='moyen'>
        <?php
		foreach($modechoix as $k=>$v){
			?>
            <li><a href='bin/_gestion_caisse_ticket.php?mode=<?php echo $k; ?>' target="suivi"><?php echo $v; ?></a></li>
            <?php	
		}		
		?>
        </ul>
		
		</td><td valign='top'>
<!-- fin pavé num -->
		
		<iframe src='bin/_gestion_caisse_ticket.php' width='300' height='350' frameborder='none' name='suivi'></iframe>
		
		
		
		</td>
	</tr>
</table>
