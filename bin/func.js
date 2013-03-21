//  150 > Bibliotheque d'interface utilisateur ;
tinyMCE=false;
document.write("<div id='debug'></div>");
colorz = new Array('000000','FFFFFF','FFFF00','FF9900','FF6600','FF0000','FF0066','FF0099','FF00FF','9900FF','6600FF','0066FF','0099FF','00FFFF','00FF00','009900','008844','97ADC1','E4F1FF','cfd3d7','EEEEEE','CCCCCC','999999','666666','333333');
months = new Array("jan","f&eacute;v","mar", "avr","mai","juin","juil","aou","sept","oct","nov","d&eacute;c");
days = new Array("dim","lun","mar","mer", "jeu","ven","sam");
function deconnect(){
	document.getElementById('deconmask').style.visibility='visible';
	window.scrolling='yes';
}
function reconnect(){
	document.getElementById('deconmask').style.visibility='hidden';
	window.scrolling='yes';
}
function affichload(){
	document.getElementById('loadmask').style.visibility='visible';
	document.body.style.overflow='hidden';
	window.scroll(0,0);
}
function unaffichload(){
	document.getElementById('loadmask').style.visibility='hidden';
	document.body.style.overflow='scroll'; 
}
function str_replace(aiguille,paille,foin){
	aiguille = new RegExp(aiguille,"g");
    return foin.replace(aiguille,paille);
}
function strip_tags(str) {
   return str.replace(/<BR>/gi, "\n").replace(/<\/P>/gi, "\n").replace(/&nbsp;/gi, " ").replace(/<\/?[^>]+>/gi, "");
}
function substr_count (haystack, needle, offset, length) {
    var pos = 0, cnt = 0;

    haystack += '';
    needle += '';
    if (isNaN(offset)) {offset = 0;}
    if (isNaN(length)) {length = 0;}
    offset--;

    while ((offset = haystack.indexOf(needle, offset+1)) != -1){
        if (length > 0 && (offset+needle.length) > length){
            return false;
        } else{
            cnt++;
        }
    }

    return cnt;
}							
function renam(path,old,ne){
		if(!ne) ne = prompt("Veuillez saisir le nouveau nom de fichier",old);
		ok=0;
		if(ne!='' && ne!=old){
			exto = old.substr(old.lastIndexOf('.'),old.length);
			extn = ne.substr(ne.lastIndexOf('.'),ne.length);
			if(exto!=extn){
				paspa = confirm("&ecirc;tes vous sur de vouloir modifier\nl'extension du fichier de\n"+exto+" &agrave; "+extn+" ?\n\nCeci peut rendre le fichier inutilisable.");
				if(paspa){
					ok=1;
				}
			}
			else{
				ok=1;
			}
		}
		if(ok==1){
			document.fourmis.action+='&ren='+path+old.replace('&','%26')+'&nen='+ne.replace('&','%26');
			document.fourmis.submit();
		}
	}
navHover = function() {
	var tags = new  Array('ul','li','div','span','td');
	for (var v=0; v<tags.length; v++) {
		var lis = document.getElementsByTagName(tags[v]);
		for (var i=0; i<lis.length; i++) {
			lis[i].onmouseover=function() {
				this.className+=" iehover";
			}
			lis[i].onmouseout=function() {
				this.className=this.className.replace(new RegExp(" iehover"), "");
			}
		}
	}
}
	

	
if (window.attachEvent) window.attachEvent("onload", navHover);


function tout(ou,ki, name,dest,html){
	chs = ou.getElementsByTagName('input');
	chi = ki.checked;
	for(i=0 ; i<chs.length ; i++){
		if(chs[i].type=='checkbox'){
			chs[i].checked=chi;
		}
	}
	if(name!=null && name!='undefined'){
		rempli(ou,name,dest,html);
	}
}
function rempli(ou,name,dest,html){
	chs = ou.getElementsByTagName('input');
	nval='';
	chtml='';
	for(i=0 ; i<chs.length ; i++){
		if(chs[i].checked==true){
			nval+='<'+chs[i].value+'>';
			if(html!=null) chtml+=chs[i].title+'<br>';
		}
	}
	if(html!=null) html.innerHTML=chtml;
	eval(name).value=nval;
	oldv = parseInt(substr_count('>'+nval+'<','><'))-1;
	dest.value=oldv;
	hut=oldv*20;
}
/*
function comptech(cib,dest,html){
	dest.value = oldv;
}

oldv=parseInt(document.fourmis.ch_cu_$i.value);
if(this.checked==true){
	if(document.fourmis.$field_name.value.indexOf('<$rowid>')==-1){
		document.fourmis.$field_name.value+='<$rowid>'; 
		document.getElementById('chu_$i').innerHTML+='$rowvaluu<br>';
		oldv++;
	}
}
else{
	document.fourmis.$field_name.value=document.fourmis.$field_name.value.replace('<$rowid>','');
	document.getElementById('chu_$i').innerHTML=document.getElementById('chu_$i').innerHTML.replace('$rowvaluu<br>','');
	oldv--;
}
document.fourmis.ch_cu_$i.value=oldv;
hut=oldv*20;
*/
function confsup(id,effdb){
	pl='&noef';
	if(effdb!=null){
		pl='&effdb='+effdb;		
	}
	is_confirmed = confirm('&ecirc;tes vous s&ucirc;r de vouloir supprimer d&eacute;finitivement l\'enregistrement '+id+' ?');
	if (is_confirmed) {
		document.location='./?option='+option+'&part='+part+'&d='+d+'&del='+id+pl;
	}
}
function delfile(file){
	is_confirmed = confirm('&ecirc;tes vous s&ucirc;r de vouloir supprimer d&eacute;finitivement ce fichier ?');
	if (is_confirmed) {
		document.location='./?option='+option+'&part='+part+'&edit='+edit+'&delfile='+file;
	}
}
function delfiles(form){
	var lis = form.getElementsByTagName('input');
	chi=0;
	for(i=0 ; i<lis.length ; i++){
		if(lis[i].type=='checkbox' && lis[i].className=='multidel' && lis[i].checked){
			chi++;
		}
	}
	if(chi>0){
		is_confirmed = confirm('&ecirc;tes vous s&ucirc;r de vouloir supprimer d&eacute;finitivement les '+chi+' fichiers s&eacute;lectionn&eacute;s ?');
		if (is_confirmed) {
			form.action='./?option='+gop+'&part='+part+'&edit='+edit+'&delfiles=1&refresh';
			form.submit();
		}
	}
}


var chang_pw=false;

var panwi=0;
function widthpanel(w){
	if(panwi>5) panwi=0;
	else panwi=200;
	
	if(!envoyer('bin/inc_ajax.php?scan=panelw','w',panwi,'htmlreturn')){
		document.getElementById('htmlreturn').innerHTML ="erreur d'enregistrement des pr&eacute;f&eacute;rences";
	}
	totref();
}

function ajaxfil(s,w){
	if(!envoyer('bin/inc_ajax.php?scan='+s,'w',w,'htmlreturn')){
		document.getElementById('htmlreturn').innerHTML ="erreur d'enregistrement des pr&eacute;f&eacute;rences";
	}
}
function widthaide(w){
	if(!envoyer('bin/inc_ajax.php?scan=aidew','w',w,'htmlreturn')){
		document.getElementById('htmlreturn').innerHTML ="erreur d'enregistrement des pr&eacute;f&eacute;rences";
	}
}
function locfil(f,w){
	if(!envoyer('bin/inc_ajax.php?scan=locfil','w',w+'&f='+f,'htmlreturn')){
		document.getElementById('htmlreturn').innerHTML ="erreur d'enregistrement des pr&eacute;f&eacute;rences...";
	}
}
function exifchange(f,c,v){
	if(!envoyer('bin/inc_ajax.php?scan=exif','w','1&f='+f+'&c='+c+'&v='+v,'htmlreturn')){
		document.getElementById('htmlreturn').innerHTML ="erreur d'enregistrement des d&eacute;tails sur l'image "+f+"...";
	}
}
var tg = new Array('note','calc','rss','compta');
var tl=new Array();

for(i=0 ; i<tg.length ; i++){
	tl[tg[i]]=1;	
}
function totref(){
	xd=0;
	for(i=0 ; i<tg.length ; i++){
		if(document.getElementById('panelr_'+tg[i])){
		var gci = document.getElementById('panelr_'+tg[i]).style;
		wi = tl[tg[i]];
		le = parseInt(gci.width);
		if(isNaN(le)) le=0;
		le += (wi-le)/2;
		xd+=Math.abs(wi-le);
		gci.width=le;
		if (navigator.appName.indexOf('Microsoft')!=-1) gci.left=-(le+5);
		else gci.left=-(le+15);
		if(wi>0){
			gci.padding='8px';	
		}
		else{
			gci.padding='0px';
		}	
		}
	}
	var gci = document.getElementById('panelp').style;
	le = parseInt(gci.width);
	if(le>120){
		gci.overflowX='visible';
		gci.overflowy='visible';	
	}
	else{
		gci.overflowX='hidden';
		gci.overflowY='scroll';
	}
	if(isNaN(le)) le=0;
	le += (panwi-le)/2;	
	gci.width=le;
	// if (navigator.appName.indexOf('Microsoft')!=-1) document.getElementById('cntrl_panel').style.left = le+20;	
	// else 
	//document.getElementById('cntrl_panel').style.left = le+1;	
	//document.getElementById('adeli_body').style.left = le+30;
	if( Math.abs(le-panwi) > 1 || xd>1){
		setTimeout('totref()',25);	
		redimfen();
	}
}
function efto(ki){
	for(i=0 ; i<tg.length ; i++){
		if(tg[i]!=ki ) tl[tg[i]]=0;	
	}
	if(tl[ki]>0) tl[ki]=0;
	else tl[ki]=200;
	 totref();
}
function redimfen(){
 if (navigator.appName.indexOf('Microsoft')!=-1) {
	var winW = parseInt(document.body.offsetWidth)-10;
	var winH = parseInt(document.body.offsetHeight-10);
 }
 else {
	var winW = window.innerWidth;
	var winH = window.innerHeight;
 }
	//pnw = parseInt(document.getElementById('panelp').style.width)+45;
	//if(isNaN(pnw)) pnw=190;
//winW-=pnw;
 if(winW<300 || isNaN(winW)) winW=300;
 if(winH<300 || isNaN(winH)) winH=300;
 winH-=95;
 winW-=20;
 

/*
 var adelihead=document.getElementById('panelp').style;
 adelihead.display='block';
 adelihead.position='relative';

 if (navigator.appName.indexOf('Microsoft')!=-1) {
 	adelihead.overflowY='visible';	
	adelihead.overflowX='visible';
 }
 else{	
  adelihead.overflowX='visible';
  adelihead.overflowY='visible';
  //adelihead.height=winH+'px';
 }*/
 winH-=75;
 /* if (navigator.appName.indexOf('Microsoft')==-1) {
	 var adelibody=document.getElementById('adeli_body').style;
	 adelibody.display='block';
	 adelibody.position='relative';
	 adelibody.width=winW+'px';
	 adelibody.overflowY='scroll'; 
	 adelibody.height=winH+'px';
 }*/
 pretu = document.getElementById('phpreturn');
 if(pretu.innerHTML!=''){
	 if( parseInt(pretu.offsetHeight)>=winH-30){
		pretu.style.height = (winH-30)+'px';
		pretu.style.overflowY='scroll';
	 }	 
 }
 else{
	pretu.style.height = '1px'; 
	pretu.style.overflowY='hidden'; 
 } 
}
/*var tl=1;
function totref(){
	ie=false;
	if (navigator.appName.indexOf("Microsoft")!=-1) {
	  	var winW = document.body.scrollLeft+parseInt(document.body.offsetWidth);
		ie=true;
	 }
	 else{
		var winW = document.body.scrollLeft+parseInt(window.innerWidth);
	 }
	to = document.getElementById('panelr').style;
	bo = document.getElementById('panelrbt').style;
	
	le = parseInt(to.width);
	le += (tl-le)/2;
	to.width=le;
	to.top=document.body.scrollTop+104;
	bo.top=to.top;
	if(tl<6){
		document.getElementById('panelr').style.padding='0px';
		to.left = winW-le-22;
		if(ie) le-= 5;
		bo.left=winW-le-46;		
	}
	else{
		document.getElementById('panelr').style.padding='8px';
		to.left = winW-le-35;
		if(ie) le-= 5;
		bo.left=winW-le-59;
	}	
	setTimeout('totref()',20);
	
}
function efto(){
	if(tl>5) tl=1;
	else tl=150;
}

*/

function contextbulle(e,txt){	
	document.getElementById('bulle').innerHTML=txt;
	if(txt=='' || txt==null){
		document.getElementById('bulle').style.visibility="hidden";
	}
	else{
		if(document.all){
			curX = event.clientX;
			curY = document.body.scrollTop+event.clientY;
		}			
		//netscape 4
		else if(document.layers){
			curX = e.pageX;
			curY = e.pageY;
		}			
		//mozilla
		else if(document.getElementById){
			curX = e.clientX;
			curY = document.body.scrollTop+e.clientY;
		}		
		document.getElementById('bulle').style.left=curX+10;
		document.getElementById('bulle').style.top=curY+10;
		document.getElementById('bulle').style.visibility="visible";
		
		wi = parseInt(document.getElementById('bulle').scrollWidth);
		he = parseInt(document.getElementById('bulle').scrollHeight);
	}
	
}
function infotobulle(){
	if(document.all){
		lis = new Array();
		lisi = document.getElementsByTagName("a");
		for (var e=0; e<lisi.length; e++) {
			if(lisi[e].className.indexOf('info')>-1){
				lis.push(lisi[e]);
			}
		}
	}
	else{
		var lis = document.getElementsByClassName("info");
	}
	for (var i=0; i<lis.length; i++) {
		lis[i].onmouseover=function(event) {
			les = this.getElementsByTagName("span");
			txt='';
			for (var e=0; e<les.length; e++) {
				txt+=les[e].innerHTML+' ';
				les[e].style.visibility='hidden';
				les[e].style.display='none';
			}
			contextbulle(event,txt);
		}
		lis[i].onmouseout=function(event) {
			contextbulle(event,null);
		}
	}
}
function age_link_af(){
	document.agendaform.link_db.style.display="block";
	age_link_db();
		
}
function age_link_db(){
	var lien = document.agendaform.lien.value.split('@');
	if(document.agendaform.link_db.style.display=="block"){
		
		
		if(lien.length==2){
			resu = envoyer('bin/inc_ajax.php?scan=agenda','linkdb',lien[1]);	
			if(resu){
				document.getElementById('link_id').innerHTML="<select name='link_id' onchange='document.agendaform.lien.value=this.value+\"@\"+document.agendaform.link_db.value;'>"+resu+"</select>";	
			}
			else{
				document.getElementById('link_id').innerHTML="<input type='hidden' name='link_id' />";	
			}
		}
		document.agendaform.link_db.value=lien[1];			
		
	}
	if(lien[0]!=''){
		if(document.agendaform.link_id) document.agendaform.link_id.value=lien[0];
		document.getElementById('link_link').innerHTML="<a href='./?option="+jsoptio[lien[1]]+"&part="+lien[1]+"&edit="+lien[0]+"'>Suivre</a>";
	}
	else{
		if(document.agendaform.link_id) document.agendaform.link_id.value='';
		document.getElementById('link_link').innerHTML='';
	}
}

var lastagej='';
var lasageda='';
function fillage(qui,type,client,prio,etat,note,only,usr,lien){
	document.agendaform.qui.value=qui;
	document.agendaform.type.value=type;
	document.agendaform.client.value=client;				
	document.agendaform.priority.value=prio;				
	document.agendaform.etat.value=etat;
	document.agendaform.note.value=note;	
	document.agendaform.only.value=only;
	document.agendaform.usr.value=usr;	
	document.agendaform.lien.value=lien;	
	age_link_db();
}
function contextage(d,h,e,tit,action,coloration,id,maj,src){	
		document.agendaform.link_db.style.display="none";
		document.getElementById('link_id').innerHTML="<input type='hidden' name='link_id' />";
		document.agendaform.link_db.value='';
		document.getElementById('typo_sel').style.visibility='hidden';
		document.getElementById('typo_txt').style.visibility='hidden';
		type = document.agendaform.type.value;
		if(type=='') type='D&eacute;finir un type';
		document.getElementById('typo_link').innerHTML=type;
		
		if (navigator.appName.indexOf("Microsoft")!=-1) {
			var winW = parseInt(document.body.scrollLeft)+parseInt(document.body.offsetWidth);
			var winH = parseInt(document.body.scrollTop)+parseInt(document.body.offsetHeight);
		 }
		 else{
			var winW = parseInt(document.body.scrollLeft)+parseInt(window.innerWidth);
			var winH = parseInt(document.body.scrollTop)+parseInt(window.innerHeight);
		 }
		if(maj==null){
			maj='agenda_totem';	
		}
		if(src==null){
			src='';	
		}
		if(document.all){
			curX = event.clientX;
			curY = event.clientY;
		}			
		//netscape 4
		if(document.layers){
			curX = e.pageX;
			curY = e.pageY;
		}			
		//mozilla
		if(document.getElementById){
			curX = e.clientX;
			curY = e.clientY;
		}
		curX += 5;
		curY += document.body.scrollTop;
		fenw = parseInt(document.getElementById('contda').style.width);
		fenh = parseInt(document.getElementById('contda').style.height);
		if(isNaN(fenw)) fenw=220;
		if(isNaN(fenh)) fenh=420;
		
		
		if(curX+fenw > winW-50) curX = 	winW-fenw-50;
		if(curY+fenh > winH-40) curY = 	winH-fenh-40;
		document.getElementById('contda').style.visibility="visible";
		document.getElementById('contti').innerHTML=tit;
		document.getElementById('contda').style.left=curX;
		document.getElementById('contda').style.top=curY;
		document.agendaform.date.value=d;
		document.agendaform.heure.value=h;
		document.agendaform.couleur.value=coloration;			
		document.agendaform.action='./?option=agenda&'+action;
		document.getElementById('isdel').innerHTML="<input type='button' class='buttontd' value='supprimer' onclick='confsupage("+id+")'> <input type='submit' onclick='validagenda(\""+tit+"\","+id+",\""+maj+"\",\""+src+"\")' class='buttontd' value='ok'>";
		if(tit!='Modifier'){
			document.agendaform.qui.value='';
			document.agendaform.usr.value=uid;
			document.agendaform.type.value='';
			document.agendaform.client.value='';
			document.agendaform.note.value='';
			document.agendaform.only.value=0;
			document.getElementById('isdel').innerHTML="<input type='submit' onclick='validagenda(\""+tit+"\","+id+",\""+maj+"\",\""+src+"\")' class='buttontd' value='ok'>";
		}
		lastagej = d;
		lastageh = Math.abs(h.substr(0,2).replace(':',''));
		document.getElementById('divcol').style.backgroundColor='#'+coloration;
		document.agendaform.client.focus();
}
function confsupage(id,effdb){
	pl='&noef';
	if(effdb!=null){
		pl='&effdb='+effdb;		
	}
	is_confirmed = confirm('&ecirc;tes vous s&ucirc;r de vouloir supprimer d&eacute;finitivement la date #'+id+' ?');
	if (is_confirmed) {
		if(envoyer('bin/inc_ajax.php?scan=agenda','delete',id,'htmlreturn')){
			document.getElementById('contda').style.visibility="hidden";
			document.getElementById('typo_sel').style.visibility='hidden';
			document.getElementById('typo_txt').style.visibility='hidden';
			moj=document.agendaform.heure.value.substr(0,2);
			cibl = 'agenda_'+document.agendaform.date.value+moj;
			if(!document.getElementById(cibl)){
				cibl = 'agenda_'+document.agendaform.date.value+'none';
				moj='none';
			}
			envoyer('bin/inc_ajax.php?scan=agenda','scan','&dest=agenda_totem&print=2&before=1&onlyon=1','agenda_totem');
			da = document.agendaform.date.value;
			he=Math.abs(document.agendaform.heure.value.substr(0,2).replace(':',''));
			if(document.getElementById('agenda_'+da+''+he)){
				envoyer('bin/inc_ajax.php?scan=agenda','scan',da+'&dest=agenda_'+da+he+'&h='+he+'&dos','agenda_'+da+''+he);
			}
		}
		else{
			alert("erreur agenda..."); 
		}
	}
}
function closcont(){	
		document.getElementById('contda').style.visibility="hidden";
		document.getElementById('typo_sel').style.visibility="hidden"; 
		document.getElementById('typo_txt').style.visibility="hidden";
		document.getElementById('menu_color').style.visibility="hidden";
}
function changedate(id,de,date){
		document.getElementById('contda').style.visibility="hidden";
		document.getElementById('typo_sel').style.visibility='hidden';
		document.getElementById('typo_txt').style.visibility='hidden';
		if(envoyer('bin/inc_ajax.php?scan=agenda','chdate','1&id='+id+'&date='+date,'htmlreturn'))  {
			envoyer('bin/inc_ajax.php?scan=agenda','scan','&dest=agenda_totem&print=2&before=1&onlyon=1','agenda_totem');
			j = date.substr(0,17);
			h = date.substr(17,date.length);
			da = j.substr(7,10);
			envoyer('bin/inc_ajax.php?scan=agenda','scan',da+'&dest='+j+'none&h='+h+'&dos',date);
			j = de.substr(0,17);
			h = de.substr(17,de.length);
			da = j.substr(7,10);
			envoyer('bin/inc_ajax.php?scan=agenda','scan',da+'&dest='+j+'none&h='+h+'&dos',de);
		}
		else{
			alert("erreur agenda..."); 
		}
}
function validagenda(tit,id,maj,src){			
		texte=tit+'&id='+id;
		texte += '&usr='+ document.agendaform.usr.value;
		texte += '&date='+ document.agendaform.date.value.replace('&','et');
		texte += '&heure='+ document.agendaform.heure.value.replace('&','et');
		texte += '&couleur='+  document.agendaform.couleur.value.replace('&','et');			
		texte += '&qui='+  document.agendaform.qui.value.replace('&','et');
		texte += '&type='+  document.agendaform.type.value.replace('&','et');
		texte += '&client='+  document.agendaform.client.value.replace('&','et');
		texte += '&note='+  document.agendaform.note.value.replace('&','et');
		texte += '&priority='+  document.agendaform.priority.value.replace('&','et');
		texte += '&only='+  document.agendaform.only.value;
		texte += '&etat='+  document.agendaform.etat.value.replace('&','et');
		texte += '&usr='+  document.agendaform.usr.value.replace('&','et');
		texte += '&lien='+  document.agendaform.lien.value.replace('&','et');
		
		if(envoyer('bin/inc_ajax.php?scan=agenda','maj',texte,'htmlreturn'))  {
			document.getElementById('contda').style.visibility="hidden";
			document.getElementById('typo_sel').style.visibility='hidden';
			document.getElementById('typo_txt').style.visibility='hidden';
			da = document.agendaform.date.value;
			he=Math.abs(document.agendaform.heure.value.substr(0,2).replace(':',''));
			envoyer('bin/inc_ajax.php?scan=agenda','scan','&dest=agenda_totem&print=2&before=1&onlyon=1','agenda_totem');
			if(maj!=null && src!=null && document.getElementById(maj)){
				envoyer('bin/inc_ajax.php?scan=agenda','scan',da+'&dest=agenda_'+da+'none&h=none&dos','agenda_'+da+'none');
			}
			if(document.getElementById('agenda_'+lastagej+'none')){
				envoyer('bin/inc_ajax.php?scan=agenda','scan',lastagej+'&dest=agenda_'+lastagej+'none&h=none&dos','agenda_'+lastagej+'none');
			}
			if(document.getElementById('agenda_'+lastagej+''+lastageh)){
				envoyer('bin/inc_ajax.php?scan=agenda','scan',lastagej+'&dest=agenda_'+lastagej+lastageh+'&h='+lastageh+'&dos','agenda_'+lastagej+''+lastageh);
			}
			if(document.getElementById('agenda_'+da+'none')){
				envoyer('bin/inc_ajax.php?scan=agenda','scan',da+'&dest=agenda_'+da+'none&h=none&dos','agenda_'+da+'none');
			}
			if(document.getElementById('agenda_'+da+''+he)){
				envoyer('bin/inc_ajax.php?scan=agenda','scan',da+'&dest=agenda_'+da+he+'&h='+he+'&dos','agenda_'+da+''+he);
			}
		}
		else{
			alert("erreur agenda..."); 
		}
}
function contextdate(e){	
	if (navigator.appName.indexOf("Microsoft")!=-1) {
			var winW = parseInt(document.body.scrollLeft)+parseInt(document.body.offsetWidth);
			var winH = parseInt(document.body.scrollTop)+parseInt(document.body.offsetHeight);
		 }
		 else{
			var winW = document.body.scrollLeft+parseInt(window.innerWidth);
			var winH = document.body.scrollTop+parseInt(window.innerHeight);
		 }
	if(document.all){
		curX = event.clientX;
		curY = event.clientY;
	}			
	//netscape 4
	if(document.layers){
		curX = e.pageX;
		curY = e.pageY;
	}			
	//mozilla
	if(document.getElementById){
		curX = e.clientX;
		curY = e.clientY;
	}			
	curY += document.body.scrollTop;
	fenw = parseInt(document.getElementById('menu_date').style.width);
	fenh = parseInt(document.getElementById('menu_date').style.height);
	if(curX+fenw > winW-50) curX = 	winW-fenw-50;
	if(curY+fenh > winH-10) curY = 	winH-fenh-10;
	document.getElementById('menu_date').style.visibility="visible";
	document.getElementById('menu_date').style.left=curX;
	document.getElementById('menu_date').style.top=curY;
}
	a='';
	tem='';
	fun='';
	t='';
	dejaloadcolor=false;
	lp='';
function effeccolor(bColor){
   if(t=='html'){
	   eval("editbox_"+a).document.execCommand(fun, false, bColor);
	   eval("editbox_"+a).focus();;
   }
   if(t=='hexa'){
	document.getElementById('div'+tem).style.backgroundColor=bColor;
	eval("document.fourmis."+tem+".value='"+bColor+"'");
  }
  if(t=='age'){
	document.getElementById('divcol').style.backgroundColor=bColor;
	eval("document.agendaform.couleur.value='"+bColor+"'");
  }
  if(t=='var'){
	eval(tem+"='"+bColor+"'");
  }
  if(t=='style'){
	document.getElementById(tem).style.backgroundColor=bColor;
  }
  if(t=='table'){
	  eval("tab_b_"+a+"='"+bColor+"'");
	  document.getElementById('tab_cou_'+a).style.backgroundColor=bColor;
  }
   document.getElementById('menu_color').style.visibility="hidden";
}
function choosecolor(i,func,ten,ty,e,p){
	a=i;
	fun=func;
	tem=ten;
	t=ty;
	if(e!=''){
		if (navigator.appName.indexOf("Microsoft")!=-1) {
				var winW = document.body.scrollLeft+parseInt(document.body.offsetWidth);
				var winH = document.body.scrollTop+parseInt(document.body.offsetHeight);
			 }
			 else{
				var winW = document.body.scrollLeft+parseInt(window.innerWidth);
				var winH = document.body.scrollTop+parseInt(window.innerHeight);
			 }
	   if(document.all){
			curX = event.clientX;
			curY = event.clientY;
		}			
		//netscape 4
		if(document.layers){
			curX = e.pageX;
			curY = e.pageY;
		}			
		//mozilla
		if(document.getElementById){
			curX = e.clientX;
			curY = e.clientY;
		}	
		curY += document.body.scrollTop;
		fenw = parseInt(document.getElementById('menu_color').style.width);
		fenh = parseInt(document.getElementById('menu_color').style.height);
		if(curX+fenw > winW-50) curX = 	winW-fenw-50;
		if(curY+fenh > winH-10) curY = 	winH-fenh-10;
		document.getElementById('menu_color').style.visibility="visible";
		document.getElementById('menu_color').style.left=curX;
		document.getElementById('menu_color').style.top=curY;
	}
	if(p!=null){
		p='&preci='+p;
	}
	else{
		p='';	
	}
	if(dejaloadcolor==false || lp!=p){
		if(envoyer('bin/inc_ajax.php?scan=color','w',"&field_name=global&field_value=FFFFFF&actiona=effeccolor('COLOR')&taille=10&a="+a+'&fun='+fun+'&tem='+tem+'&t='+t+p,'color_ajax')){
			dejaloadcolor=true;	
		}
	}
	lp=p;
} 

function dec(ki,co){
	document.getElementById(ki).style.height=co;
	document.getElementById(ki).scrollTop=0;
	if(co<23){
		document.getElementById(ki).style.overflow='hidden';
	}
	else{
		document.getElementById(ki).style.overflow='scroll';
	}
}
			    
function sizpa(ki,ho,limi){
	hie = parseInt(document.getElementById(ki).style.height);
	hin = parseInt(document.getElementById(ki).scrollHeight);
	if(!ho){
		if(hie < 10){
			if(hin){
				ho = hin+20;
			}
			else{
				ho = 300;
			}						
		}
		else{
			ho = 5;
		}	
	}
	if(limi && ho > limi){
		ho=limi;		
	}	
	//alert(hie+' '+hin+' '+ho);	
	if(ho<6){
		document.getElementById(ki).style.height='1px';
		document.getElementById(ki).style.padding='0px';
		document.getElementById(ki).style.overflowY='hidden';
	}
	else{
		document.getElementById(ki).style.height='auto';
		document.getElementById(ki).style.padding='5px';
		document.getElementById(ki).style.overflowY='visible';
	}	
	locfil('ouvert.'+part+'.'+ki+'.conf',ho);
}
	
/* *****************************************************************************************************
                                                       EDITOR HTML 
**********************************************************************************************************/
function stylize(e,ste){
   alert('en cours de d&eacute;veloppement...');
   eval("editbox_"+e).focus();
}
function format(e){
   var tags = /[<>]/g;
   var sampleText="";
    if (document.all) {
		var oRng = eval("editbox_"+e).document.selection.createRange();
		if(oRng.text) sampleText=oRng.text;
		sampleText = sampleText.toString().replace(tags,'');
		oRng.pasteHTML(sampleText);
	}
	else{
		if(eval("editbox_"+e).getSelection()) var sampleText = eval("editbox_"+e).getSelection();
		sampleText = sampleText.toString().replace(tags,'');
		eval("editbox_"+e).document.execCommand('insertHTML', false, sampleText);
	}
	eval("editbox_"+e).focus();
}	
function veriflink(str){
	if(str.indexOf('://')>-1 || str.indexOf('www.')>-1){
		  return str.replace(/[\s]/g,'_').replace(/[,;<>\`'|[)@€($[~!"«»']/g,'').toLowerCase().replace('é','e').replace('è','e').replace('ê','e').replace('à','a').replace('â','a').replace('á','a').replace('ï','i').replace('ù','u').replace('û','u').replace('ô','o').replace('ó ','o').replace('ç','c').replace('ñ','n');
	}
	else{	
		return  str.replace(/[\s]/g,'_').replace(/[,;:\/\`'@|><[)([~€\+%=&?\!"«»']/g,'').toLowerCase().replace('é','e').replace('è','e').replace('ê','e').replace('à','a').replace('â','a').replace('á','a').replace('ï','i').replace('ù','u').replace('û','u').replace('ô','o').replace('ó ','o').replace('ç','c').replace('ñ','n');
	}
}
function validlink(str){
	while(str != veriflink(str)){
		str= veriflink(str);	
	}
	return str;
}

function addlink(e){
	if(e==null) e=0;
	/*if (document.all) {
		eval("editbox_"+e).document.execCommand('CreateLink', true);
		//var sText =  eval("editbox_"+e).document.selection.createRange();
		//alert(sText);
	}
	else{*/
		link_url='#';
		link_target='_self';
		ancres='';
		//rangestart=tinyMCE.activeEditor.selection.getStart();
		textrange=tinyMCE.activeEditor.selection.getRng();
		console.log (textrange);
		if(tinyMCE && tinyMCE.activeEditor && tinyMCE.activeEditor.getContent()){
		  document.getElementById('vernissage').innerHTML=tinyMCE.activeEditor.getContent();
		  an = document.getElementById('vernissage').getElementsByTagName('a');
		  for(a=0 ; a<an.length; a++){
			  if(an[a].name && an[a].name!=''){
				  ancres+="<br><a style='cursor:pointer' unselectable='on' onclick=\"link_target='_self';link_url='#"+an[a].name+"';mklink("+e+")\"><img src='http://www.adeli.wac.fr/vers/1.2/images/ancre.gif' alt='ancre :'>"+an[a].name+"</a>";	
			  }
		  }
		}
		document.getElementById('menu_context').style.visibility='visible';
		document.getElementById('menu_context_canvas').innerHTML="Tapez ici l'adresse du lien : <br><input type='text' value='http://' onchange=\"link_url=this.value;\"><br><input type='checkbox' onchange=\"if(this.checked==true){link_target='blank';}else{link_target='self';}\"> ouvrir dans une nouvelle fen&ecirc;tre<br><br><input type='button' value='ok' onclick='mklink("+e+")'>"+ancres;
	  document.getElementById('menu_context').style.top=document.body.scrollTop;
	//}	
}
function addlinkancor(e){
	  link_url='#';
	  link_target='_self';
	  ancres='';
	  an = eval("editbox_"+e).document.getElementsByTagName('a');
	  for(a=0 ; a<an.length; a++){
		  if(an[a].name && an[a].name!=''){
			  ancres+="<a style='cursor:pointer' unselectable='on' onclick=\"link_target='_self';link_url='#"+an[a].name+"';mklink("+e+")\"><img src='http://www.adeli.wac.fr/vers/1.2/images/ancre.gif' alt='ancre :'>"+an[a].name+"</a><br>";	
		  }
	  }
	  document.getElementById('menu_context').style.visibility='visible';
	  document.getElementById('menu_context_canvas').innerHTML="<br>"+ancres+"<br><br><input type='button' value='ok' onclick='mklink("+e+")'>";
	  document.getElementById('menu_context').style.top=document.body.scrollTop;
		
}
function crea_link(e,linke,targ){
	link_url=linke;
	if(!targ) targ='_blank';
	link_target=targ;
	if(tinyMCE && tinyMCE.activeEditor && tinyMCE.execCommand('mceInsertLink',false,linke)){
		
	}
	else{
		mklink(e);
	}
}
function mklink(e){
	if(tinyMCE && tinyMCE.activeEditor){
		txt = tinyMCE.activeEditor.selection.getContent();
		if(txt==''){
			//txt=textrange;
			//tinyMCE.activeEditor.selection.setContent(txt)
			tinyMCE.activeEditor.selection.setRng(textrange);	
			txt = tinyMCE.activeEditor.selection.getContent();
		}
		if(txt==''){
			txt=link_url;	
		}
		tinyMCE.execCommand('mceInsertContent',false,'<a href="'+link_url+'" target="'+link_target+'">'+txt+'</a>');
	}
	/*else{
	//alert(link_url+" "+link_target);
	tagOpen='<a href="'+link_url+'" target="_'+link_target+'">';
	tagClose='</a>';
	sampleText=link_url;
	if(isNaN(e)) e=0;
	if (document.all) {
		var ec_sel = eval("editbox_"+e).document.selection;
 		if (ec_sel!=null) {
 			 var ec_rng = ec_sel.createRange();
 			 ec_rng.pasteHTML(tagOpen+sampleText+tagClose);
 		}
	}
	else{
		if(eval("editbox_"+e).getSelection()){
			if(eval("editbox_"+e).getSelection()!=''){
				var sampleText = eval("editbox_"+e).getSelection();
			}
		}
		eval("editbox_"+e).document.execCommand('insertHTML', false, tagOpen+sampleText+tagClose);
	}	
	eval("editbox_"+e).focus();
	}*/
	document.body.style.overflow='scroll';
	document.location='#txt'+e;
	document.getElementById('menu_context').style.visibility='hidden';
	
}
function addancre(e){
	if (document.all) {
		//alert("Cette fontionalit&eacute; est indisponible sur Internet Explorer\nNous vous conseillons d'utiliser un navigateur alternatif comme Firefox, Safari, Chrome ou Opera");
		///eval("editbox_"+e).document.execCommand('CreateLink', true);
		ancre_name = prompt("Tapez ici le nom de l'ancre :","ancre");
		if(ancre_name){
			mkancre(e);
		}
	}
	else{
		ancre_name='ancre';
		document.getElementById('menu_context').style.visibility='visible';
		document.getElementById('menu_context_canvas').innerHTML="Tapez ici le nom de l'ancre : <br><input type='text' value='ancre' onchange=\"ancre_name=this.value;\"><br><br><input type='button' value='ok' onclick='mkancre("+e+")'>";
	  document.getElementById('menu_context').style.top=document.body.scrollTop;
	}
	
}
function mkancre(e){
	if(tinyMCE && tinyMCE.activeEditor && tinyMCE.execCommand('mceInsertContent',false,'<a name="'+ancre_name+'" target="_self">'+tinyMCE.activeEditor.selection.getContent()+'</a>')){
		
	}
	else{
		tagOpen='<a name="'+ancre_name+'">';
		tagClose='</a>';
		sampleText=ancre_name;
		if (document.all) {
			var ec_sel = eval("editbox_"+e).document.selection;
			if (ec_sel!=null) {
				 var ec_rng = ec_sel.createRange();
				 ec_rng.pasteHTML(tagOpen+sampleText+tagClose);
			}
	
	/*
			value = tagOpen+sampleText+tagClose;
			eval("editbox_"+e).clipboardData.setData("Text", value);
			eval("editbox_"+e).document.execCommand('paste');*/
			
		}
		else{
			if(eval("editbox_"+e).getSelection()){
				if(eval("editbox_"+e).getSelection()!=''){
					var sampleText = eval("editbox_"+e).getSelection();
				}
			}
			eval("editbox_"+e).document.execCommand('insertHTML', false, tagOpen+sampleText+tagClose);
		}
	}
	eval("editbox_"+e).focus();
	document.body.style.overflow='scroll';
	document.location='#txt'+e;
	document.getElementById('menu_context').style.visibility='hidden';
}
function addcode(e){
	code_html='';
	document.getElementById('menu_context').style.visibility='visible';
	document.getElementById('menu_context_canvas').innerHTML="saisissez ici le code HTML : <br><textarea onkeyup=\"code_html=this.value;\" id=\"menu_context_textarea\"></textarea><br><br><input type='button' value='ok' onclick='mkcode("+e+")'>";
  document.getElementById('menu_context').style.top=document.body.scrollTop;
	
}
function mkcode(e){
	if (document.all) {
	  	if (eval("editbox_"+e).document.hasFocus()){
			eval("editbox_"+e).focus();
		}
		value = document.getElementById("menu_context_textarea").value;
		var ec_sel = eval("editbox_"+e).document.selection;
 		if (ec_sel!=null) {
 			 var ec_rng = ec_sel.createRange();
 			 ec_rng.pasteHTML(value);
 		}
		//
		//eval("editbox_"+e).clipboardData.setData("Text", value);
		//eval("editbox_"+e).document.execCommand('paste');
		
	}
	else{
		eval("editbox_"+e).document.execCommand('insertHTML', false, code_html);
	}	
	eval("editbox_"+e).focus();
	document.body.style.overflow='scroll';
	document.location='#txt'+e;
	document.getElementById('menu_context').style.visibility='hidden';
	
}

function colorSelect(e,func,bColor){
	eval("editbox_"+e).document.execCommand(func, false, bColor);
	document.getElementById('context_'+e).innerHTML='';
	eval("editbox_"+e).focus();
}
function sautdeligne(e){
	if(tinyMCE && tinyMCE.activeEditor && tinyMCE.execCommand('mceInsertContent',false,'<br/>')){
		
	}
	else{
	  if (document.all) {
			if (eval("editbox_"+e).document.hasFocus()){
				  eval("editbox_"+e).focus();
			  }
			var oRng = eval("editbox_"+e).document.selection.createRange();
			oRng.pasteHTML('<br>');
		}
		else{
			eval("editbox_"+e).document.execCommand('insertHTML', false, '<br>');
		}
		eval("editbox_"+e).focus();
	}
}
function putimg(e,fil){
	if(tinyMCE && tinyMCE.activeEditor && tinyMCE.execCommand('mceInsertContent',false,'<img src="http://www.'+urlserveur+'/'+fil+'" border="none" alt="'+fil+'"/>')){
		
	}
	else{
		if(e>0){
			if (document.all) {
				if (eval("editbox_"+e).document.hasFocus()){
					eval("editbox_"+e).focus();
				}
				var oRng = eval("editbox_"+e).document.selection.createRange();
				oRng.pasteHTML('<img src="http://www.'+urlserveur+'/'+fil+'" border=none alt="'+fil+'">');
			}
			else{
				eval("editbox_"+e).document.execCommand('insertHTML', false, '<img src="http://www.'+urlserveur+'/'+fil+'" border=none alt="'+fil+'">');
			}
			eval("editbox_"+e).focus();
		}
	}
}
function taille(e,tail,thi){
	if(tail == parseInt(tail) ){
		eval("editbox_"+e).document.execCommand('fontsize', false, tail); 				
	}
	else{
		if(tail.substr(tail.length-2,tail.length)=='px'){
			tagOpen = '<span style="font-size:'+tail+'">';
			tagClose = '</span>';
		}
		else if(!isNaN(tail)){
			tagOpen = '<font size="f'+tail+'">';
			tagClose = '</font>';
		}
		else{
			tagOpen = '<'+tail+'>';
			tagClose = '</'+tail+'>';
		}
		sampleText = 'ins&eacute;rez ici votre contenu';
		if (document.all) {
			//eval("editbox_"+e).focus();
			var oRng = eval("editbox_"+e).document.selection.createRange();
			if(oRng.text) sampleText=oRng.text;
			oRng.pasteHTML(tagOpen+sampleText+tagClose);
		}
		else{
			if(eval("editbox_"+e).getSelection()) var sampleText = eval("editbox_"+e).getSelection();
			eval("editbox_"+e).document.execCommand('insertHTML', false, tagOpen+sampleText+tagClose);
		}
	} 
	eval("editbox_"+e).focus();
	if(thi){
		thi.value='0';
	}
}
function float(e,sens){
	tagOpen='<table style="display:block;float:'+sens+';margin:5px;"><tr><td>';
	tagClose='</td></tr></table>';
	sampleText='ins&eacute;rez ici votre contenu';
	if (document.all) {
		eval("editbox_"+e).focus();
	  	var oRng = eval("editbox_"+e).document.selection.createRange();
		if(oRng.text) sampleText=oRng.text;
		oRng.pasteHTML(tagOpen+sampleText+tagClose);
	}
	else{
		if(eval("editbox_"+e).getSelection()){
			if(eval("editbox_"+e).getSelection()!=''){
				var sampleText = eval("editbox_"+e).getSelection();
			}
		}
		eval("editbox_"+e).document.execCommand('insertHTML', false, tagOpen+sampleText+tagClose);
	}	
	eval("editbox_"+e).focus();
}
function justify(e,sens){
	tagOpen='<div style="text-align:justify;">';
	tagClose='</div>';
	sampleText='ins&eacute;rez ici votre contenu';
	if (document.all) {
	  	var oRng = eval("editbox_"+e).document.selection.createRange();
		if(oRng.text) sampleText=oRng.text;
		oRng.pasteHTML(tagOpen+sampleText+tagClose);
	}
	else{
		if(eval("editbox_"+e).getSelection()) var sampleText = eval("editbox_"+e).getSelection();
		eval("editbox_"+e).document.execCommand('insertHTML', false, tagOpen+sampleText+tagClose);
	}	
	eval("editbox_"+e).focus();
}
function tableau(ki,x,y,b,c,s,p){
	x = parseInt(x);
	y = parseInt(y);
	if(x==0) x=1;
	if(y==0) y=1;
	//alert(ki+' '+x+' '+y+' '+b);
	inh = '<table cellspacing="'+s+'" cellpadding="'+p+'" border="'+c+'"';
	if(b.length == 6){
		inh+='bgcolor="#'+b+'"';	
	}
	inh+='>';
	for(i=0 ; i<y ; i++){
		inh+='<tr>';
		for(ii=0 ; ii<x ; ii++){
			inh+='<td>&nbsp;</td>';
		}
		inh+='</tr>';
	}
	inh += '</table>';
	/*if (document.all) {
	  	var oRng = eval("editbox_"+ki).document.selection.createRange();
		if(!oRng){ 
			alert('Veuillez s&eacute;lectionner une plage de texte');
		}
		else{
			oRng.pasteHTML(inh);
		}
	}
	else{*/
	if(tinyMCE && tinyMCE.activeEditor && tinyMCE.execCommand('mceInsertContent',inh)){
		
	}
	else{
		eval("editbox_"+ki).document.execCommand('insertHTML', false, inh);
	}
	//}
}
function mcetableau(x,y,b,c,s,p){
	x = parseInt(x);
	y = parseInt(y);
	if(x==0) x=1;
	if(y==0) y=1;
	//alert(ki+' '+x+' '+y+' '+b);
	inh = '<table cellspacing="'+s+'" cellpadding="'+p+'" border="'+c+'"';
	if(b.length == 6){
		inh+='bgcolor="#'+b+'"';	
	}
	inh+='>';
	for(i=0 ; i<y ; i++){
		inh+='<tr>';
		for(ii=0 ; ii<x ; ii++){
			inh+='<td>&nbsp;</td>';
		}
		inh+='</tr>';
	}
	inh += '</table>';
	if(tinyMCE && tinyMCE.activeEditor && tinyMCE.execCommand('mceInsertContent',false,inh)){
	}
}
/***********************************************************************************************************************/
function heur(){
	var d = new Date();
	h = d.getHours();
	m = d.getMinutes();
	s = d.getSeconds();
	j = d.getDate();
	n = days[d.getDay()];
	mo = months[d.getMonth()];
	an = d.getFullYear();
	if(h<10){
		h = '0'+h;	
	}
	if(m<10){
		m = '0'+m;	
	}
	if(s<10){
		s = '0'+s;	
	}
	
	document.getElementById('tim').innerHTML="<b>"+h+":"+m+"</b>:"+s+"<span>"+n+" "+j+" "+mo+" "+an+"</span>";
	setTimeout(heur,1000);
}

 /******************************************************************************************* AJAX */
function scanlogges(koi,ou,te,maj){ 
	var req = null; 
	if (window.XMLHttpRequest){
 			req = new XMLHttpRequest();
	} 
	else if (window.ActiveXObject){
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} 
			catch (e){
				try {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				} 
				catch (e) {}
			}
	}
	req.onreadystatechange = function(){ 
		if(req.readyState == 4){
			if(req.status == 200){
				document.getElementById(ou).innerHTML  = req.responseText;	
				if(ou=='ajax_mail' && gop=='mail' && document.lister){
					//document.lister.location = document.lister.location+'&refreshagain';
				}
			}
			else document.getElementById(ou).innerHTML="Error: " + req.status + " " + req.statusText;	
		} 
	}; 
	document.getElementById(ou).innerHTML+='<div>chargement...</div>';	
	req.open("GET", koi, true); 
	req.send(null); 
	if(te > 0 && maj)setTimeout("scanlogges('"+koi+"','"+ou+"',"+te+",true)",te);
}

function envoyer(url,key,val,ret)  { 
	var xajax = null; 
	if(document.getElementById(ret)){
		var div = document.getElementById(ret);
		//document.getElementById('debug').innerHTML  += val+'<br>';
		div.innerHTML = 'chargement...<br>'+div.innerHTML;
	}
		if(window.XMLHttpRequest) xajax = new XMLHttpRequest(); 
		else if(window.ActiveXObject) xajax = new ActiveXObject("Microsoft.XMLHTTP"); 
		else return(false); 
		 
		var str = key+"="+val; 
		 
		xajax.open("POST",url,false); 
		xajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xajax.send(str); 
		if(xajax.readyState == 4) {
			if(document.getElementById(ret)){
				div.innerHTML = xajax.responseText; 
				return true;
			}
			else{
				return xajax.responseText; 
			}
		} 		
	
	return false;
}
/************************************************************************* CALCULATRICE **************/
last=0;
 func='nul';
  function calcule(fonc){
	  
	ttv = parseFloat(document.calcul.tva.value);	  
  if(document.calcul.tape.value == ''){document.calcul.tape.value=0;}
  letape = parseFloat(document.calcul.tape.value);
  last = parseFloat(last);
  if(func == 'plu'){ document.calcul.tape.value = last + letape;      }
  if(func == 'moi'){ document.calcul.tape.value = last - letape;      }
  if(func == 'div'){ document.calcul.tape.value = last / letape;      }
  if(func == 'mul'){ document.calcul.tape.value = last * letape;      }
  if(func == 'nul'){ document.calcul.tape.value = letape;              }
  
  if(fonc == 'res'){ document.calcul.tape.value = 0; func = 'nul';     }
  if(fonc == 'ttc'){ document.calcul.tape.value = letape*(1+(ttv/100)); func = 'nul';    }
  if(fonc == 'tva+'){ document.calcul.tape.value = letape*(ttv/100); func = 'nul';    }
  if(fonc == 'ht'){ document.calcul.tape.value = letape/(1+(ttv/100)); func = 'nul';    }
  if(fonc == 'tva-'){ document.calcul.tape.value = letape-(letape/(1+(ttv/100))); func = 'nul';    }
  else{ func=fonc;                                                       }
	
  document.calcul.tape.focus();
  last=document.calcul.tape.value;
  envoyer('bin/inc_ajax.php?scan=calcul','texte',last,'calculreturn');
  envoyer('bin/inc_ajax.php?scan=calcultva','texte',ttv,'calculreturn');
 }
 function evaluat(){
 	valeur = document.calcul.tape.value;
 	derdeder = valeur.substr(valeur.length-1,valeur.length);
	if(derdeder == '+'){ calcule('plu'); document.calcul.tape.value=''; document.calcul.tape.focus();}
	if(derdeder == '-'){ calcule('moi');document.calcul.tape.value=''; document.calcul.tape.focus();	}
	if(derdeder == '/'){ calcule('div');document.calcul.tape.value=''; document.calcul.tape.focus();	}
	if(derdeder == '*' || derdeder == 'x' || derdeder == 'X'){ calcule('mul');document.calcul.tape.value=''; document.calcul.tape.focus();}
 }
 function effect(){ 
 	calcule(func); 	
	if(func == 'mul' || func == 'div'){last=1;}
	if(func == 'plu' || func == 'moi'){last=0;}
	finc = 'nul';
	document.calcul.tape.focus();
 }
 
 /////////////////////////////////:
 
function fin(){
	infotobulle();
	unaffichload();
	totref();
	efto(null);
	window.onresize=redimfen;
	//widthpanel(pwn);
	
}