<?php // 88 > Gabarits PDF ;
/*******************************************************************************
* Logiciel : FPDF                                                              *
* Version :  1.53                                                              *
* Date :     31/12/2004                                                        *
* Auteur :   Olivier PLATHEY                                                   *
* Licence :  Freeware                                                          *
*                                                                              *
* Vous pouvez utiliser et modifier ce logiciel comme vous le souhaitez.        *
*******************************************************************************/

if(!class_exists('FPDF')){
define('FPDF_VERSION','1.53');

class FPDF
{
//Private properties
var $page;               //current page number
var $n;                  //current object number
var $offsets;            //array of object offsets
var $buffer;             //buffer holding in-memory PDF
var $pages;              //array containing pages
var $state;              //current document state
var $compress;           //compression flag
var $DefOrientation;     //default orientation
var $CurOrientation;     //current orientation
var $OrientationChanges; //array indicating orientation changes
var $k;                  //scale factor (number of points in user unit)
var $fwPt,$fhPt;         //dimensions of page format in points
var $fw,$fh;             //dimensions of page format in user unit
var $wPt,$hPt;           //current dimensions of page in points
var $w,$h;               //current dimensions of page in user unit
var $lMargin;            //left margin
var $tMargin;            //top margin
var $rMargin;            //right margin
var $bMargin;            //page break margin
var $cMargin;            //cell margin
var $x,$y;               //current position in user unit for cell positioning
var $lasth;              //height of last cell printed
var $LineWidth;          //line width in user unit
var $CoreFonts;          //array of standard font names
var $fonts;              //array of used fonts
var $FontFiles;          //array of font files
var $diffs;              //array of encoding differences
var $images;             //array of used images
var $PageLinks;          //array of links in pages
var $links;              //array of internal links
var $FontFamily;         //current font family
var $FontStyle;          //current font style
var $underline;          //underlining flag
var $CurrentFont;        //current font info
var $FontSizePt;         //current font size in points
var $FontSize;           //current font size in user unit
var $DrawColor;          //commands for drawing color
var $FillColor;          //commands for filling color
var $TextColor;          //commands for text color
var $ColorFlag;          //indicates whether fill and text colors are different
var $ws;                 //word spacing
var $AutoPageBreak;      //automatic page breaking
var $PageBreakTrigger;   //threshold used to trigger page breaks
var $InFooter;           //flag set when processing footer
var $ZoomMode;           //zoom display mode
var $LayoutMode;         //layout display mode
var $title;              //title
var $subject;            //subject
var $author;             //author
var $keywords;           //keywords
var $creator;            //creator
var $AliasNbPages;       //alias for total number of pages
var $PDFVersion;         //PDF version number

/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
function FPDF($orientation='P',$unit='mm',$format='A4'){
	//Some checks
	$this->_dochecks();
	//Initialization of properties
	$this->page=0;
	$this->n=2;
	$this->buffer='';
	$this->pages=array();
	$this->OrientationChanges=array();
	$this->state=0;
	$this->fonts=array();
	$this->FontFiles=array();
	$this->diffs=array();
	$this->images=array();
	$this->links=array();
	$this->InFooter=false;
	$this->lasth=0;
	$this->FontFamily='';
	$this->FontStyle='';
	$this->FontSizePt=12;
	$this->underline=false;
	$this->DrawColor='0 G';
	$this->FillColor='0 g';
	$this->TextColor='0 g';
	$this->ColorFlag=false;
	$this->ws=0;
	//Standard fonts
	$this->CoreFonts=array('courier'=>'Courier','courierB'=>'Courier-Bold','courierI'=>'Courier-Oblique','courierBI'=>'Courier-BoldOblique',
		'helvetica'=>'Helvetica','helveticaB'=>'Helvetica-Bold','helveticaI'=>'Helvetica-Oblique','helveticaBI'=>'Helvetica-BoldOblique',
		'times'=>'Times-Roman','timesB'=>'Times-Bold','timesI'=>'Times-Italic','timesBI'=>'Times-BoldItalic',
		'symbol'=>'Symbol','zapfdingbats'=>'ZapfDingbats');
	//Scale factor
	if($unit=='pt')
		$this->k=1;
	elseif($unit=='mm')
		$this->k=72/25.4;
	elseif($unit=='cm')
		$this->k=72/2.54;
	elseif($unit=='in')
		$this->k=72;
	else
		$this->Error('Incorrect unit: '.$unit);
	//Page format
	if(is_string($format))
	{
		$format=strtolower($format);
		if($format=='a3')
			$format=array(841.89,1190.55);
		elseif($format=='a4')
			$format=array(595.28,841.89);
		elseif($format=='a5')
			$format=array(420.94,595.28);
		elseif($format=='letter')
			$format=array(612,792);
		elseif($format=='legal')
			$format=array(612,1008);
		else
			$this->Error('Unknown page format: '.$format);
		$this->fwPt=$format[0];
		$this->fhPt=$format[1];
	}
	else
	{
		$this->fwPt=$format[0]*$this->k;
		$this->fhPt=$format[1]*$this->k;
	}
	$this->fw=$this->fwPt/$this->k;
	$this->fh=$this->fhPt/$this->k;
	//Page orientation
	$orientation=strtolower($orientation);
	if($orientation=='p' || $orientation=='portrait')
	{
		$this->DefOrientation='P';
		$this->wPt=$this->fwPt;
		$this->hPt=$this->fhPt;
	}
	elseif($orientation=='l' || $orientation=='landscape')
	{
		$this->DefOrientation='L';
		$this->wPt=$this->fhPt;
		$this->hPt=$this->fwPt;
	}
	else
		$this->Error('Incorrect orientation: '.$orientation);
	$this->CurOrientation=$this->DefOrientation;
	$this->w=$this->wPt/$this->k;
	$this->h=$this->hPt/$this->k;
	//Page margins (1 cm)
	$margin=28.35/$this->k;
	$this->SetMargins($margin,$margin);
	//Interior cell margin (1 mm)
	$this->cMargin=$margin/10;
	//Line width (0.2 mm)
	$this->LineWidth=.567/$this->k;
	//Automatic page break
	$this->SetAutoPageBreak(true,2*$margin);
	//Full width display mode
	$this->SetDisplayMode('fullwidth');
	//Enable compression
	$this->SetCompression(true);
	//Set default PDF version number
	$this->PDFVersion='1.3';
}

var $angle=0;

function Rotate($angle,$x=-1,$y=-1){
    if($x==-1)
        $x=$this->x;
    if($y==-1)
        $y=$this->y;
    if($this->angle!=0)
        $this->_out('Q');
    $this->angle=$angle;
    if($angle!=0)    {
        $angle*=M_PI/180;
        $c=cos($angle);
        $s=sin($angle);
        $cx=$x*$this->k;
        $cy=($this->h-$y)*$this->k;
        $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    }
}

function SetMargins($left,$top,$right=-1){
	//Set left, top and right margins
	$this->lMargin=$left;
	$this->tMargin=$top;
	if($right==-1)
		$right=$left;
	$this->rMargin=$right;
}

function SetLeftMargin($margin){
	//Set left margin
	$this->lMargin=$margin;
	if($this->page>0 && $this->x<$margin)
		$this->x=$margin;
}

function SetTopMargin($margin){
	//Set top margin
	$this->tMargin=$margin;
}

function SetRightMargin($margin){
	//Set right margin
	$this->rMargin=$margin;
}

function SetAutoPageBreak($auto,$margin=0){
	//Set auto page break mode and triggering margin
	$this->AutoPageBreak=$auto;
	$this->bMargin=$margin;
	$this->PageBreakTrigger=$this->h-$margin;
}

function SetDisplayMode($zoom,$layout='continuous'){
	//Set display mode in viewer
	if($zoom=='fullpage' || $zoom=='fullwidth' || $zoom=='real' || $zoom=='default' || !is_string($zoom))
		$this->ZoomMode=$zoom;
	else
		$this->Error('Incorrect zoom display mode: '.$zoom);
	if($layout=='single' || $layout=='continuous' || $layout=='two' || $layout=='default')
		$this->LayoutMode=$layout;
	else
		$this->Error('Incorrect layout display mode: '.$layout);
}

function SetCompression($compress){
	//Set page compression
	if(function_exists('gzcompress'))
		$this->compress=$compress;
	else
		$this->compress=false;
}

function SetTitle($title){
	//Title of document
	$this->title=$title;
}

function SetSubject($subject){
	//Subject of document
	$this->subject=$subject;
}

function SetAuthor($author){
	//Author of document
	$this->author=$author;
}

function SetKeywords($keywords){
	//Keywords of document
	$this->keywords=$keywords;
}

function SetCreator($creator){
	//Creator of document
	$this->creator=$creator;
}

function AliasNbPages($alias='{nb}'){
	//Define an alias for total number of pages
	$this->AliasNbPages=$alias;
}

function Error($msg){
	//Fatal error
	die('<B>FPDF error: </B>'.$msg);
}

function Open(){
	//Begin document
	$this->state=1;
}

function Close(){
	//Terminate document
	if($this->state==3)
		return;
	if($this->page==0)
		$this->AddPage();
	//Page footer
	$this->InFooter=true;
	$this->Footer();
	$this->InFooter=false;
	//Close page
	$this->_endpage();
	//Close document
	$this->_enddoc();
}

function AddPage($orientation=''){
	//Start a new page
	if($this->state==0)
		$this->Open();
	$family=$this->FontFamily;
	$style=$this->FontStyle.($this->underline ? 'U' : '');
	$size=$this->FontSizePt;
	$lw=$this->LineWidth;
	$dc=$this->DrawColor;
	$fc=$this->FillColor;
	$tc=$this->TextColor;
	$cf=$this->ColorFlag;
	if($this->page>0)
	{
		//Page footer
		$this->InFooter=true;
		$this->Footer();
		$this->InFooter=false;
		//Close page
		$this->_endpage();
	}
	//Start new page
	$this->_beginpage($orientation);
	//Set line cap style to square
	$this->_out('2 J');
	//Set line width
	$this->LineWidth=$lw;
	$this->_out(sprintf('%.2f w',$lw*$this->k));
	//Set font
	if($family)
		$this->SetFont($family,$style,$size);
	//Set colors
	$this->DrawColor=$dc;
	if($dc!='0 G')
		$this->_out($dc);
	$this->FillColor=$fc;
	if($fc!='0 g')
		$this->_out($fc);
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;
	//Page header
	$this->Header();
	//Restore line width
	if($this->LineWidth!=$lw)
	{
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.2f w',$lw*$this->k));
	}
	//Restore font
	if($family)
		$this->SetFont($family,$style,$size);
	//Restore colors
	if($this->DrawColor!=$dc)
	{
		$this->DrawColor=$dc;
		$this->_out($dc);
	}
	if($this->FillColor!=$fc)
	{
		$this->FillColor=$fc;
		$this->_out($fc);
	}
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;
}

function Header(){
	//To be implemented in your own inherited class
}

function Footer(){
	//To be implemented in your own inherited class
}

function PageNo(){
	//Get current page number
	return $this->page;
}

function SetDrawColor($r,$g=-1,$b=-1){
	//Set color for all stroking operations
	if(($r==0 && $g==0 && $b==0) || $g==-1)
		$this->DrawColor=sprintf('%.3f G',$r/255);
	else
		$this->DrawColor=sprintf('%.3f %.3f %.3f RG',$r/255,$g/255,$b/255);
	if($this->page>0)
		$this->_out($this->DrawColor);
}

function SetFillColor($r,$g=-1,$b=-1){
	//Set color for all filling operations
	if(($r==0 && $g==0 && $b==0) || $g==-1)
		$this->FillColor=sprintf('%.3f g',$r/255);
	else
		$this->FillColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
	$this->ColorFlag=($this->FillColor!=$this->TextColor);
	if($this->page>0)
		$this->_out($this->FillColor);
}

function SetTextColor($r,$g=-1,$b=-1){
	//Set color for text
	if(($r==0 && $g==0 && $b==0) || $g==-1)
		$this->TextColor=sprintf('%.3f g',$r/255);
	else
		$this->TextColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
	$this->ColorFlag=($this->FillColor!=$this->TextColor);
}

function GetStringWidth($s){
	//Get width of a string in the current font
	$s=(string)$s;
	$cw=&$this->CurrentFont['cw'];
	$w=0;
	$l=strlen($s);
	for($i=0;$i<$l;$i++)
		$w+=$cw[$s{$i}];
	return $w*$this->FontSize/1000;
}

function SetLineWidth($width){
	//Set line width
	$this->LineWidth=$width;
	if($this->page>0)
		$this->_out(sprintf('%.2f w',$width*$this->k));
}

function Line($x1,$y1,$x2,$y2){
	//Draw a line
	$this->_out(sprintf('%.2f %.2f m %.2f %.2f l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
}

function Rect($x,$y,$w,$h,$style=''){
	//Draw a rectangle
	if($style=='F')
		$op='f';
	elseif($style=='FD' || $style=='DF')
		$op='B';
	else
		$op='S';
	$this->_out(sprintf('%.2f %.2f %.2f %.2f re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
}

function AddFont($family,$style='',$file=''){
	//Add a TrueType or Type1 font
	$family=strtolower($family);
	if($file=='')
		$file=str_replace(' ','',$family).strtolower($style).'.php';
	if($family=='arial')
		$family='helvetica';
	$style=strtoupper($style);
	if($style=='IB')
		$style='BI';
	$fontkey=$family.$style;
	if(isset($this->fonts[$fontkey]))
		$this->Error('Font already added: '.$family.' '.$style);
	include($this->_getfontpath().$file);
	if(!isset($name))
		$this->Error('Could not include font definition file');
	$i=count($this->fonts)+1;
	$this->fonts[$fontkey]=array('i'=>$i,'type'=>$type,'name'=>$name,'desc'=>$desc,'up'=>$up,'ut'=>$ut,'cw'=>$cw,'enc'=>$enc,'file'=>$file);
	if($diff)
	{
		//Search existing encodings
		$d=0;
		$nb=count($this->diffs);
		for($i=1;$i<=$nb;$i++)
		{
			if($this->diffs[$i]==$diff)
			{
				$d=$i;
				break;
			}
		}
		if($d==0)
		{
			$d=$nb+1;
			$this->diffs[$d]=$diff;
		}
		$this->fonts[$fontkey]['diff']=$d;
	}
	if($file)
	{
		if($type=='TrueType')
			$this->FontFiles[$file]=array('length1'=>$originalsize);
		else
			$this->FontFiles[$file]=array('length1'=>$size1,'length2'=>$size2);
	}
}

function SetFont($family,$style='',$size=0){
	//Select a font; size given in points
	global $fpdf_charwidths;

	$family=strtolower($family);
	if($family=='')
		$family=$this->FontFamily;
	if($family=='arial')
		$family='helvetica';
	elseif($family=='symbol' || $family=='zapfdingbats')
		$style='';
	$style=strtoupper($style);
	if(strpos($style,'U')!==false)
	{
		$this->underline=true;
		$style=str_replace('U','',$style);
	}
	else
		$this->underline=false;
	if($style=='IB')
		$style='BI';
	if($size==0)
		$size=$this->FontSizePt;
	//Test if font is already selected
	if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
		return;
	//Test if used for the first time
	$fontkey=$family.$style;
	if(!isset($this->fonts[$fontkey]))
	{
		//Check if one of the standard fonts
		if(isset($this->CoreFonts[$fontkey]))
		{
			if(!isset($fpdf_charwidths[$fontkey]))
			{
				//Load metric file
				$file=$family;
				if($family=='times' || $family=='helvetica')
					$file.=strtolower($style);
				//include($this->_getfontpath().$file.'.ext');
				if(true!==$incf = includ($this->_getfontpath().$file.'.ext')){
				  eval ($incf);
				}
				else{
					include($this->_getfontpath().$file.'.ext');	
				}
				if(!isset($fpdf_charwidths[$fontkey]))
					$this->Error('Could not include font metric file');
			}
			$i=count($this->fonts)+1;
			$this->fonts[$fontkey]=array('i'=>$i,'type'=>'core','name'=>$this->CoreFonts[$fontkey],'up'=>-100,'ut'=>50,'cw'=>$fpdf_charwidths[$fontkey]);
		}
		else
			$this->Error('Undefined font: '.$family.' '.$style);
	}
	//Select it
	$this->FontFamily=$family;
	$this->FontStyle=$style;
	$this->FontSizePt=$size;
	$this->FontSize=$size/$this->k;
	$this->CurrentFont=&$this->fonts[$fontkey];
	if($this->page>0)
		$this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function SetFontSize($size){
	//Set font size in points
	if($this->FontSizePt==$size)
		return;
	$this->FontSizePt=$size;
	$this->FontSize=$size/$this->k;
	if($this->page>0)
		$this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function AddLink(){
	//Create a new internal link
	$n=count($this->links)+1;
	$this->links[$n]=array(0,0);
	return $n;
}

function SetLink($link,$y=0,$page=-1){
	//Set destination of internal link
	if($y==-1)
		$y=$this->y;
	if($page==-1)
		$page=$this->page;
	$this->links[$link]=array($page,$y);
}

function Link($x,$y,$w,$h,$link){
	//Put a link on the page
	$this->PageLinks[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);
}

function Text($x,$y,$txt){
	//Output a string
	$s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
	if($this->underline && $txt!='')
		$s.=' '.$this->_dounderline($x,$y,$txt);
	if($this->ColorFlag)
		$s='q '.$this->TextColor.' '.$s.' Q';
	$this->_out($s);
}

function AcceptPageBreak(){
	//Accept automatic page break or not
	return $this->AutoPageBreak;
}

function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link=''){
	//Output a cell
	$k=$this->k;
	if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
	{
		//Automatic page break
		$x=$this->x;
		$ws=$this->ws;
		if($ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		$this->AddPage($this->CurOrientation);
		$this->x=$x;
		if($ws>0)
		{
			$this->ws=$ws;
			$this->_out(sprintf('%.3f Tw',$ws*$k));
		}
	}
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$s='';
	if($fill==1 || $border==1)
	{
		if($fill==1)
			$op=($border==1) ? 'B' : 'f';
		else
			$op='S';
		$s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
	}
	if(is_string($border))
	{
		$x=$this->x;
		$y=$this->y;
		if(strpos($border,'L')!==false)
			$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'T')!==false)
			$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		if(strpos($border,'R')!==false)
			$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'B')!==false)
			$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}
	if($txt!=='')
	{
		if($align=='R')
			$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
		elseif($align=='C')
			$dx=($w-$this->GetStringWidth($txt))/2;
		else
			$dx=$this->cMargin;
		if($this->ColorFlag)
			$s.='q '.$this->TextColor.' ';
		$txt2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
		$s.=sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
		if($this->underline)
			$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
		if($this->ColorFlag)
			$s.=' Q';
		if($link)
			$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
	}
	if($s)
		$this->_out($s);
	$this->lasth=$h;
	if($ln>0)
	{
		//Go to next line
		$this->y+=$h;
		if($ln==1)
			$this->x=$this->lMargin;
	}
	else
		$this->x+=$w;
}

function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0){
	//Output text with automatic or explicit line breaks
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
		$nb--;
	$b=0;
	if($border)
	{
		if($border==1)
		{
			$border='LTRB';
			$b='LRT';
			$b2='LR';
		}
		else
		{
			$b2='';
			if(strpos($border,'L')!==false)
				$b2.='L';
			if(strpos($border,'R')!==false)
				$b2.='R';
			$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
		}
	}
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$ns=0;
	$nl=1;
	while($i<$nb)
	{
		//Get next character
		$c=$s{$i};
		if($c=="\n")
		{
			//Explicit line break
			if($this->ws>0)
			{
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
			continue;
		}
		if($c==' ')
		{
			$sep=$i;
			$ls=$l;
			$ns++;
		}
		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($i==$j)
					$i++;
				if($this->ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			}
			else
			{
				if($align=='J')
				{
					$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
					$this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
				}
				$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
		}
		else
			$i++;
	}
	//Last chunk
	if($this->ws>0)
	{
		$this->ws=0;
		$this->_out('0 Tw');
	}
	if($border && strpos($border,'B')!==false)
		$b.='B';
	$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
	$this->x=$this->lMargin;
}

function Write($h,$txt,$link=''){
	//Output text in flowing mode
	$cw=&$this->CurrentFont['cw'];
	$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		//Get next character
		$c=$s{$i};
		if($c=="\n")
		{
			//Explicit line break
			$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			if($nl==1)
			{
				$this->x=$this->lMargin;
				$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			}
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($this->x>$this->lMargin)
				{
					//Move to next line
					$this->x=$this->lMargin;
					$this->y+=$h;
					$w=$this->w-$this->rMargin-$this->x;
					$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
					$i++;
					$nl++;
					continue;
				}
				if($i==$j)
					$i++;
				$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
			}
			else
			{
				$this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			if($nl==1)
			{
				$this->x=$this->lMargin;
				$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			}
			$nl++;
		}
		else
			$i++;
	}
	//Last chunk
	if($i!=$j)
		$this->Cell($l/1000*$this->FontSize,$h,substr($s,$j),0,0,'',0,$link);
}

function Image($file,$x,$y,$w=0,$h=0,$type='',$link=''){
	//Put an image on the page
	if(!isset($this->images[$file]))
	{
		//First use of image, get info
		if($type=='')
		{
			$pos=strrpos($file,'.');
			if(!$pos)
				$this->Error('Image file has no extension and no type was specified: '.$file);
			$type=substr($file,$pos+1);
		}
		$type=strtolower($type);
		$mqr=get_magic_quotes_runtime();
		set_magic_quotes_runtime(0);
		if($type=='jpg' || $type=='jpeg')
			$info=$this->_parsejpg($file);
		elseif($type=='png')
			$info=$this->_parsepng($file);
		else
		{
			//Allow for additional formats
			$mtd='_parse'.$type;
			if(!method_exists($this,$mtd))
				$this->Error('Unsupported image type: '.$type);
			$info=$this->$mtd($file);
		}
		set_magic_quotes_runtime($mqr);
		$info['i']=count($this->images)+1;
		$this->images[$file]=$info;
	}
	else
		$info=$this->images[$file];
	//Automatic width and height calculation if needed
	if($w==0 && $h==0)
	{
		//Put image at 72 dpi
		$w=$info['w']/$this->k;
		$h=$info['h']/$this->k;
	}
	if($w==0)
		$w=$h*$info['w']/$info['h'];
	if($h==0)
		$h=$w*$info['h']/$info['w'];
	$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
	if($link)
		$this->Link($x,$y,$w,$h,$link);
}

function Ln($h=''){
	//Line feed; default value is last cell height
	$this->x=$this->lMargin;
	if(is_string($h))
		$this->y+=$this->lasth;
	else
		$this->y+=$h;
}

function GetX(){
	//Get x position
	return $this->x;
}

function SetX($x){
	//Set x position
	if($x>=0)
		$this->x=$x;
	else
		$this->x=$this->w+$x;
}

function GetY(){
	//Get y position
	return $this->y;
}

function SetY($y){
	//Set y position and reset x
	$this->x=$this->lMargin;
	if($y>=0)
		$this->y=$y;
	else
		$this->y=$this->h+$y;
}

function SetXY($x,$y){
	//Set x and y positions
	$this->SetY($y);
	$this->SetX($x);
}

function Output($name='',$dest=''){
	//Output PDF to some destination
	//Finish document if necessary
	if($this->state<3)
		$this->Close();
	//Normalize parameters
	if(is_bool($dest))
		$dest=$dest ? 'D' : 'F';
	$dest=strtoupper($dest);
	if($dest=='')
	{
		if($name=='')
		{
			$name='doc.pdf';
			$dest='I';
		}
		else
			$dest='F';
	}
	switch($dest)
	{
		case 'I':
			//Send to standard output
			ob_end_clean();
			if(ob_get_contents())
				$this->Error('Some data has already been output, can\'t send PDF file');
			if(php_sapi_name()!='cli')
			{
				//We send to a browser
				header('Content-Type: application/pdf');
				if(headers_sent())
					$this->Error('Some data has already been output to browser, can\'t send PDF file');
				header('Content-Length: '.strlen($this->buffer));
				header('Content-disposition: inline; filename="'.$name.'"');
			}
			echo $this->buffer;
			break;
		case 'D':
			//Download file
			if(ob_get_contents())
				$this->Error('Some data has already been output, can\'t send PDF file');
			if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
				header('Content-Type: application/force-download');
			else
				header('Content-Type: application/octet-stream');
			if(headers_sent())
				$this->Error('Some data has already been output to browser, can\'t send PDF file');
			header('Content-Length: '.strlen($this->buffer));
			header('Content-disposition: attachment; filename="'.$name.'"');
			echo $this->buffer;
			break;
		case 'F':
			//Save to local file
			$f=fopen($name,'wb');
			if(!$f)
				$this->Error('Unable to create output file: '.$name);
			fwrite($f,$this->buffer,strlen($this->buffer));
			fclose($f);
			break;
		case 'S':
			//Return as a string
			return $this->buffer;
		default:
			$this->Error('Incorrect output destination: '.$dest);
	}
	return '';
}

/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/
function _dochecks(){
	//Check for locale-related bug
	if(1.1==1)
		$this->Error('Don\'t alter the locale before including class file');
	//Check for decimal separator
	if(sprintf('%.1f',1.0)!='1.0')
		setlocale(LC_NUMERIC,'C');
}

function _getfontpath(){
	if(!defined('FPDF_FONTPATH') && is_dir(dirname(__FILE__).'/font'))
		define('FPDF_FONTPATH',dirname(__FILE__).'/font/');
	return defined('FPDF_FONTPATH') ? FPDF_FONTPATH : '';
}

function _putpages(){
	$nb=$this->page;
	if(!empty($this->AliasNbPages))
	{
		//Replace number of pages
		for($n=1;$n<=$nb;$n++)
			$this->pages[$n]=str_replace($this->AliasNbPages,$nb,$this->pages[$n]);
	}
	if($this->DefOrientation=='P')
	{
		$wPt=$this->fwPt;
		$hPt=$this->fhPt;
	}
	else
	{
		$wPt=$this->fhPt;
		$hPt=$this->fwPt;
	}
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	for($n=1;$n<=$nb;$n++)
	{
		//Page
		$this->_newobj();
		$this->_out('<</Type /Page');
		$this->_out('/Parent 1 0 R');
		if(isset($this->OrientationChanges[$n]))
			$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$hPt,$wPt));
		$this->_out('/Resources 2 0 R');
		if(isset($this->PageLinks[$n]))
		{
			//Links
			$annots='/Annots [';
			foreach($this->PageLinks[$n] as $pl)
			{
				$rect=sprintf('%.2f %.2f %.2f %.2f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
				$annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
				if(is_string($pl[4]))
					$annots.='/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
				else
				{
					$l=$this->links[$pl[4]];
					$h=isset($this->OrientationChanges[$l[0]]) ? $wPt : $hPt;
					$annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>',1+2*$l[0],$h-$l[1]*$this->k);
				}
			}
			$this->_out($annots.']');
		}
		$this->_out('/Contents '.($this->n+1).' 0 R>>');
		$this->_out('endobj');
		//Page content
		$p=($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
		$this->_newobj();
		$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
		$this->_putstream($p);
		$this->_out('endobj');
	}
	//Pages root
	$this->offsets[1]=strlen($this->buffer);
	$this->_out('1 0 obj');
	$this->_out('<</Type /Pages');
	$kids='/Kids [';
	for($i=0;$i<$nb;$i++)
		$kids.=(3+2*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$nb);
	$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
	$this->_out('>>');
	$this->_out('endobj');
}

function _putfonts(){
	$nf=$this->n;
	foreach($this->diffs as $diff)
	{
		//Encodings
		$this->_newobj();
		$this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
		$this->_out('endobj');
	}
	$mqr=get_magic_quotes_runtime();
	set_magic_quotes_runtime(0);
	foreach($this->FontFiles as $file=>$info)
	{
		//Font file embedding
		$this->_newobj();
		$this->FontFiles[$file]['n']=$this->n;
		$font='';
		$f=fopen($this->_getfontpath().$file,'rb',1);
		if(!$f)
			$this->Error('Font file not found');
		while(!feof($f))
			$font.=fread($f,8192);
		fclose($f);
		$compressed=(substr($file,-2)=='.z');
		if(!$compressed && isset($info['length2']))
		{
			$header=(ord($font{0})==128);
			if($header)
			{
				//Strip first binary header
				$font=substr($font,6);
			}
			if($header && ord($font{$info['length1']})==128)
			{
				//Strip second binary header
				$font=substr($font,0,$info['length1']).substr($font,$info['length1']+6);
			}
		}
		$this->_out('<</Length '.strlen($font));
		if($compressed)
			$this->_out('/Filter /FlateDecode');
		$this->_out('/Length1 '.$info['length1']);
		if(isset($info['length2']))
			$this->_out('/Length2 '.$info['length2'].' /Length3 0');
		$this->_out('>>');
		$this->_putstream($font);
		$this->_out('endobj');
	}
	set_magic_quotes_runtime($mqr);
	foreach($this->fonts as $k=>$font)
	{
		//Font objects
		$this->fonts[$k]['n']=$this->n+1;
		$type=$font['type'];
		$name=$font['name'];
		if($type=='core')
		{
			//Standard font
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/BaseFont /'.$name);
			$this->_out('/Subtype /Type1');
			if($name!='Symbol' && $name!='ZapfDingbats')
				$this->_out('/Encoding /WinAnsiEncoding');
			$this->_out('>>');
			$this->_out('endobj');
		}
		elseif($type=='Type1' || $type=='TrueType')
		{
			//Additional Type1 or TrueType font
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/BaseFont /'.$name);
			$this->_out('/Subtype /'.$type);
			$this->_out('/FirstChar 32 /LastChar 255');
			$this->_out('/Widths '.($this->n+1).' 0 R');
			$this->_out('/FontDescriptor '.($this->n+2).' 0 R');
			if($font['enc'])
			{
				if(isset($font['diff']))
					$this->_out('/Encoding '.($nf+$font['diff']).' 0 R');
				else
					$this->_out('/Encoding /WinAnsiEncoding');
			}
			$this->_out('>>');
			$this->_out('endobj');
			//Widths
			$this->_newobj();
			$cw=&$font['cw'];
			$s='[';
			for($i=32;$i<=255;$i++)
				$s.=$cw[chr($i)].' ';
			$this->_out($s.']');
			$this->_out('endobj');
			//Descriptor
			$this->_newobj();
			$s='<</Type /FontDescriptor /FontName /'.$name;
			foreach($font['desc'] as $k=>$v)
				$s.=' /'.$k.' '.$v;
			$file=$font['file'];
			if($file)
				$s.=' /FontFile'.($type=='Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
			$this->_out($s.'>>');
			$this->_out('endobj');
		}
		else
		{
			//Allow for additional types
			$mtd='_put'.strtolower($type);
			if(!method_exists($this,$mtd))
				$this->Error('Unsupported font type: '.$type);
			$this->$mtd($font);
		}
	}
}

function _putimages(){
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	reset($this->images);
	while(list($file,$info)=each($this->images))
	{
		$this->_newobj();
		$this->images[$file]['n']=$this->n;
		$this->_out('<</Type /XObject');
		$this->_out('/Subtype /Image');
		$this->_out('/Width '.$info['w']);
		$this->_out('/Height '.$info['h']);
		if($info['cs']=='Indexed')
			$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
		else
		{
			$this->_out('/ColorSpace /'.$info['cs']);
			if($info['cs']=='DeviceCMYK')
				$this->_out('/Decode [1 0 1 0 1 0 1 0]');
		}
		$this->_out('/BitsPerComponent '.$info['bpc']);
		if(isset($info['f']))
			$this->_out('/Filter /'.$info['f']);
		if(isset($info['parms']))
			$this->_out($info['parms']);
		if(isset($info['trns']) && is_array($info['trns']))
		{
			$trns='';
			for($i=0;$i<count($info['trns']);$i++)
				$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
			$this->_out('/Mask ['.$trns.']');
		}
		$this->_out('/Length '.strlen($info['data']).'>>');
		$this->_putstream($info['data']);
		unset($this->images[$file]['data']);
		$this->_out('endobj');
		//Palette
		if($info['cs']=='Indexed')
		{
			$this->_newobj();
			$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
			$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
			$this->_putstream($pal);
			$this->_out('endobj');
		}
	}
}

function _putxobjectdict(){
	foreach($this->images as $image)
		$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
}

function _putresourcedict(){
	$this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
	$this->_out('/Font <<');
	foreach($this->fonts as $font)
		$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
	$this->_out('>>');
	$this->_out('/XObject <<');
	$this->_putxobjectdict();
	$this->_out('>>');
	
	// COLOR	
	$this->_out('/ColorSpace <<');
	foreach($this->SpotColors as $color)
		$this->_out('/CS'.$color['i'].' '.$color['n'].' 0 R');
	$this->_out('>>');
	
	// ALPHA
	$this->_out('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_out('>>');
}

function _putresources(){
	$this->_putextgstates();
	$this->_putspotcolors();
	$this->_putfonts();
	$this->_putimages();
	//Resource dictionary
	$this->offsets[2]=strlen($this->buffer);
	$this->_out('2 0 obj');
	$this->_out('<<');
	$this->_putresourcedict();
	$this->_out('>>');
	$this->_out('endobj');
	
}

function _putinfo(){
	$this->_out('/Producer '.$this->_textstring('FPDF '.FPDF_VERSION));
	if(!empty($this->title))
		$this->_out('/Title '.$this->_textstring($this->title));
	if(!empty($this->subject))
		$this->_out('/Subject '.$this->_textstring($this->subject));
	if(!empty($this->author))
		$this->_out('/Author '.$this->_textstring($this->author));
	if(!empty($this->keywords))
		$this->_out('/Keywords '.$this->_textstring($this->keywords));
	if(!empty($this->creator))
		$this->_out('/Creator '.$this->_textstring($this->creator));
	$this->_out('/CreationDate '.$this->_textstring('D:'.date('YmdHis')));
}

function _putcatalog(){
	$this->_out('/Type /Catalog');
	$this->_out('/Pages 1 0 R');
	if($this->ZoomMode=='fullpage')
		$this->_out('/OpenAction [3 0 R /Fit]');
	elseif($this->ZoomMode=='fullwidth')
		$this->_out('/OpenAction [3 0 R /FitH null]');
	elseif($this->ZoomMode=='real')
		$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
	elseif(!is_string($this->ZoomMode))
		$this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
	if($this->LayoutMode=='single')
		$this->_out('/PageLayout /SinglePage');
	elseif($this->LayoutMode=='continuous')
		$this->_out('/PageLayout /OneColumn');
	elseif($this->LayoutMode=='two')
		$this->_out('/PageLayout /TwoColumnLeft');
}

function _putheader(){
	$this->_out('%PDF-'.$this->PDFVersion);
}

function _puttrailer(){
	$this->_out('/Size '.($this->n+1));
	$this->_out('/Root '.$this->n.' 0 R');
	$this->_out('/Info '.($this->n-1).' 0 R');
}

function _enddoc(){
	//alpha
	if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
			
			
			
	$this->_putheader();
	$this->_putpages();
	$this->_putresources();
	//Info
	$this->_newobj();
	$this->_out('<<');
	$this->_putinfo();
	$this->_out('>>');
	$this->_out('endobj');
	//Catalog
	$this->_newobj();
	$this->_out('<<');
	$this->_putcatalog();
	$this->_out('>>');
	$this->_out('endobj');
	//Cross-ref
	$o=strlen($this->buffer);
	$this->_out('xref');
	$this->_out('0 '.($this->n+1));
	$this->_out('0000000000 65535 f ');
	for($i=1;$i<=$this->n;$i++)
		$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
	//Trailer
	$this->_out('trailer');
	$this->_out('<<');
	$this->_puttrailer();
	$this->_out('>>');
	$this->_out('startxref');
	$this->_out($o);
	$this->_out('%%EOF');
	$this->state=3;
}

function _beginpage($orientation){
	$this->page++;
	$this->pages[$this->page]='';
	$this->state=2;
	$this->x=$this->lMargin;
	$this->y=$this->tMargin;
	$this->FontFamily='';
	//Page orientation
	if(!$orientation)
		$orientation=$this->DefOrientation;
	else
	{
		$orientation=strtoupper($orientation{0});
		if($orientation!=$this->DefOrientation)
			$this->OrientationChanges[$this->page]=true;
	}
	if($orientation!=$this->CurOrientation)
	{
		//Change orientation
		if($orientation=='P')
		{
			$this->wPt=$this->fwPt;
			$this->hPt=$this->fhPt;
			$this->w=$this->fw;
			$this->h=$this->fh;
		}
		else
		{
			$this->wPt=$this->fhPt;
			$this->hPt=$this->fwPt;
			$this->w=$this->fh;
			$this->h=$this->fw;
		}
		$this->PageBreakTrigger=$this->h-$this->bMargin;
		$this->CurOrientation=$orientation;
	}
}

function _endpage(){
	//End of page contents
	$this->state=1;
}

function _newobj(){
	//Begin a new object
	$this->n++;
	$this->offsets[$this->n]=strlen($this->buffer);
	$this->_out($this->n.' 0 obj');
}

function _dounderline($x,$y,$txt){
	//Underline text
	$up=$this->CurrentFont['up'];
	$ut=$this->CurrentFont['ut'];
	$w=$this->GetStringWidth($txt)+$this->ws*substr_count($txt,' ');
	return sprintf('%.2f %.2f %.2f %.2f re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt);
}

function _parsejpg($file){
	//Extract info from a JPEG file
	$a=GetImageSize($file);
	if(!$a)
		$this->Error('Missing or incorrect image file: '.$file);
	if($a[2]!=2)
		$this->Error('Not a JPEG file: '.$file);
	if(!isset($a['channels']) || $a['channels']==3)
		$colspace='DeviceRGB';
	elseif($a['channels']==4)
		$colspace='DeviceCMYK';
	else
		$colspace='DeviceGray';
	$bpc=isset($a['bits']) ? $a['bits'] : 8;
	//Read whole file
	$f=fopen($file,'rb');
	$data='';
	while(!feof($f))
		$data.=fread($f,4096);
	fclose($f);
	return array('w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>$bpc,'f'=>'DCTDecode','data'=>$data);
}

function _parsepng($file){
	//Extract info from a PNG file
	$f=fopen($file,'rb');
	if(!$f)
		$this->Error('Can\'t open image file: '.$file);
	//Check signature
	if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
		$this->Error('Not a PNG file: '.$file);
	//Read header chunk
	fread($f,4);
	if(fread($f,4)!='IHDR')
		$this->Error('Incorrect PNG file: '.$file);
	$w=$this->_freadint($f);
	$h=$this->_freadint($f);
	$bpc=ord(fread($f,1));
	if($bpc>8)
		$this->Error('16-bit depth not supported: '.$file);
	$ct=ord(fread($f,1));
	if($ct==0)
		$colspace='DeviceGray';
	elseif($ct==2)
		$colspace='DeviceRGB';
	elseif($ct==3)
		$colspace='Indexed';
	else
		$this->Error('Alpha channel not supported: '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Unknown compression method: '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Unknown filter method: '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Interlacing not supported: '.$file);
	fread($f,4);
	$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
	//Scan chunks looking for palette, transparency and image data
	$pal='';
	$trns='';
	$data='';
	do
	{
		$n=$this->_freadint($f);
		$type=fread($f,4);
		if($type=='PLTE')
		{
			//Read palette
			$pal=fread($f,$n);
			fread($f,4);
		}
		elseif($type=='tRNS')
		{
			//Read transparency info
			$t=fread($f,$n);
			if($ct==0)
				$trns=array(ord(substr($t,1,1)));
			elseif($ct==2)
				$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
			else
			{
				$pos=strpos($t,chr(0));
				if($pos!==false)
					$trns=array($pos);
			}
			fread($f,4);
		}
		elseif($type=='IDAT')
		{
			//Read image data block
			$data.=fread($f,$n);
			fread($f,4);
		}
		elseif($type=='IEND')
			break;
		else
			fread($f,$n+4);
	}
	while($n);
	if($colspace=='Indexed' && empty($pal))
		$this->Error('Missing palette in '.$file);
	fclose($f);
	return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
}

function _freadint($f){
	//Read a 4-byte integer from file
	$a=unpack('Ni',fread($f,4));
	return $a['i'];
}

function _textstring($s){
	//Format a text string
	return '('.$this->_escape($s).')';
}

function _escape($s){
	//Add \ before \, ( and )
	return str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$s)));
}

function _putstream($s){
	$this->_out('stream');
	$this->_out($s);
	$this->_out('endstream');
}

function _out($s){
	//Add a line to the document
	if($this->state==2)
		$this->pages[$this->page].=$s."\n";
	else
		$this->buffer.=$s."\n";
}
//End of class
var $SpotColors=array();
	
	function AddSpotColor($name, $c, $m, $y, $k){
		if(!isset($this->SpotColors[$name]))
		{
			$i=count($this->SpotColors)+1;
			$this->SpotColors[$name]=array('i'=>$i,'c'=>$c,'m'=>$m,'y'=>$y,'k'=>$k);
		}
	}
	
	function SetDrawSpotColor($name, $tint=100){
		if(!isset($this->SpotColors[$name]))
			$this->Error('Undefined spot color: '.$name);
		$this->DrawColor=sprintf('/CS%d CS %.3F SCN',$this->SpotColors[$name]['i'],$tint/100);
		if($this->page>0)
			$this->_out($this->DrawColor);
	}
	
	function SetFillSpotColor($name, $tint=100){
		if(!isset($this->SpotColors[$name]))
			$this->Error('Undefined spot color: '.$name);
		$this->FillColor=sprintf('/CS%d cs %.3F scn',$this->SpotColors[$name]['i'],$tint/100);
		$this->ColorFlag=($this->FillColor!=$this->TextColor);
		if($this->page>0)
			$this->_out($this->FillColor);
	}
	
	function SetTextSpotColor($name, $tint=100){
		if(!isset($this->SpotColors[$name]))
			$this->Error('Undefined spot color: '.$name);
		$this->TextColor=sprintf('/CS%d cs %.3F scn',$this->SpotColors[$name]['i'],$tint/100);
		$this->ColorFlag=($this->FillColor!=$this->TextColor);
	}
	
	function _putspotcolors(){
		foreach($this->SpotColors as $name=>$color)
		{
			$this->_newobj();
			$this->_out('[/Separation /'.str_replace(' ','#20',$name));
			$this->_out('/DeviceCMYK <<');
			$this->_out('/Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0] ');
			$this->_out(sprintf('/C1 [%.3F %.3F %.3F %.3F] ',$color['c']/100,$color['m']/100,$color['y']/100,$color['k']/100));
			$this->_out('/FunctionType 2 /Domain [0 1] /N 1>>]');
			$this->_out('endobj');
			$this->SpotColors[$name]['n']=$this->n;
		}
	}
	
	/*function _putresourcedict(){
		parent::_putresourcedict();
		$this->_out('/ColorSpace <<');
		foreach($this->SpotColors as $color)
			$this->_out('/CS'.$color['i'].' '.$color['n'].' 0 R');
		$this->_out('>>');
	}*/
	
	/*function _putresources(){
		$this->_putspotcolors();
		parent::_putresources();
	}*/
	
	// ALPHA
	
	var $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    function SetAlpha($alpha, $bm='Normal')    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

   /* function _enddoc()    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }*/

    function _putextgstates()    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_out('<</Type /ExtGState');
            foreach ($this->extgstates[$i]['parms'] as $k=>$v)
                $this->_out('/'.$k.' '.$v);
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

   /* function _putresourcedict()    {
        parent::_putresourcedict();
        $this->_out('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_out('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }*/
}

//Handle special IE contype request
if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']=='contype'){
	header('Content-Type: application/pdf');
	exit;
}

}
////////////////////////////////////////////////////////////////////////////////////////// FACTURE
class FACTURE extends FPDF{
	function ajout($nom,$url,$url_cgv,$reference,$clientfacture,$date,$type,$ref,$adresse,$intitule,$entete_img,$taxe='HT',$bdl=false,$page='',$pages=true,$devise='EUR'){
		$date = date("d/m/Y",strtotime($date));
		if(!ereg("http",$url) && $url!=""){
			$url = "http://$url";
		}
		if(!ereg("http",$url_cgv) && $url_cgv!=""){
			$url = "http://$url_cgv";
		}
		if($taxe!='HT' && $taxe!='TTC'){
			$taxe='HT';
		}
		$priurl = ereg_replace("hpp://","",$url);
		$priurl = ereg_replace("hpps://","",$priurl);
		$this->AddPage();
		
		$this->SetXY(120,0);
		$this->SetFillColor(250,250,250);
		$this->Cell(90,280, '', 0, 2, 'L', 1); 
		
		$this->SetXY(5,5);
	
		if($entete_img!=""){
			$siz = getimagesize($entete_img);
			$imw = round($siz[0]*595/21);
			$imh = round($siz[1]*595/21);
			if($imh>38){
				$imh = 38;
				$imw = $imh/$siz[1]*$siz[0];
			}
			
			$this->Image($entete_img,0,0,$imw,$imh);
		
		}
		
			$this->SetFont('Arial','',9.5);
			$this->SetXY(5,40);
			//$this->Cell(20,12,$nom, 0,2, "L", 0);
			$this->MultiCell(80,2,$nom, 0, "L", 0); 
		
		$this->SetXY(180,5);
		$this->SetFont('Arial','',9);
		$this->Cell(30,9, $priurl, 0, 2, "R", 0,$url); 
		$this->SetXY(110,10);
		$this->SetFont('Arial','',7);
		if(trim($url_cgv)!=""){$this->Cell(100,9, 'conditions générales de vente: '.$url_cgv, 0, 2, "R", 0,$url_cgv); }
		
		
		
		$this->SetXY(5,287);
		$this->SetTextColor(1, 1, 1);
		$this->SetFont('Arial','',7);
		$this->MultiCell(200,3,$reference, 0, "C", 0); 
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',7);
		$this->SetXY(5,45);
		$this->Cell(20,12,'identifiant', 0,2, "L", 0);
		$this->SetXY(5,48);
		$this->Cell(20,12,'client', 0,2, "L", 0); 
		$this->SetXY(20,45);
		$this->SetTextColor(100, 100, 100);
		$this->SetFont('Arial','',12);
		$this->Cell(80,12, $clientfacture, 0, 2, "L", 0); 
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',7);
		$this->SetXY(5,53);
		$this->Cell(20,12,'date:/', 0,2, "L", 0);
		$this->SetXY(20,53);
		$this->SetTextColor(100, 100, 100);
		$this->SetFont('Arial','',12);
		$this->Cell(30,12, $date, 0, 2, "L", 0); 
		
		$this->SetXY(5,60);
		$this->SetTextColor(150, 150, 150);
		$this->SetFont('Arial','B',25);
		$this->Cell(100,12, strtoupper($type), 0, 2, "L", 0); 
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',7);
		$this->SetXY(5,67);
		$this->Cell(10,12,'réf n°', 0,2, "L", 0);
		$this->SetXY(20,67);
		$this->SetTextColor(100, 100, 100);
		$this->SetFont('Arial','',12);
		$this->Cell(20,12, $ref , 0, 0, "L", 0); 
		
		$this->SetFont('Arial','',7);
		$this->SetXY(5,73);	
		$sur = '';
		if($pages) $sur = '/{nb}';
		$this->Cell(12,12,'Page '.$page.$sur,0,0,'L');
					//$this->PageNo()
				
		
		
		$this->SetXY(105,38);
		$this->SetFont('Arial','',13);
		$this->SetTextColor(0, 0, 0);
		$this->SetFillColor(255,255,255);
		$this->SetDrawColor(200,200,200);
		$this->Cell(105,40,'', 1,0, "L", 1);
		$this->SetXY(112,45);
		$this->MultiCell(100,5,$adresse, 0, "L", 0); 
		
		$this->SetDrawColor(0,0,0);
		$this->SetXY(5,81);
		$this->SetTextColor(150, 150, 150);
		$this->SetFont('Arial','B',13);
		$this->Cell(150,6,$intitule, 0,0, "L", 0);
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,90);
		$this->Cell(15,6,'Réf.', 'B',0, "L", 0);
		$this->SetX(20);
		$this->Cell(100,6,'Désignation', 'B',0, "L", 0);
		$this->SetX(120);
		if($bdl==false) $this->Cell(30,6,"PU $taxe", 'B',0, "L", 0);
		else $this->Cell(30,6,"", 'B',0, "L", 0);
		$this->SetX(140);
		$this->Cell(20,6,'Quant.', 'B',0, "L", 0);
		$this->SetX(155);
		if($bdl==false) $this->Cell(30,6,'Remise', 'B',0, "L", 0);
		else $this->Cell(20,6,'', 'B',0, "L", 0);
		$this->SetX(175);
		if($bdl==false) $this->Cell(30,6,'TVA', 'B',0, "L", 0);
		else $this->Cell(10,6,'', 'B',0, "L", 0);
		$this->SetX(185);
		if($bdl==false) $this->Cell(30,6,"Total ( $devise $taxe)", 'B',0, "L", 0);
		else $this->Cell(30,6,"", 'B',0, "L", 0);
	}
	function ajout2($nom,$url,$url_cgv,$reference,$clientfacture,$date,$type,$ref,$adresse,$intitule,$entete_img,$taxe='HT',$bdl=false,$page='',$pages=true,$devise='EUR'){
		$date = date("d/m/Y",strtotime($date));
		if(!ereg("http",$url) && $url!=""){
			$url = "http://$url";
		}
		if(!ereg("http",$url_cgv) && $url_cgv!=""){
			$url = "http://$url_cgv";
		}
		if($taxe!='HT' && $taxe!='TTC'){
			$taxe='HT';
		}
		$this->AddPage();
		
		$this->SetXY(120,0);
		$this->SetFillColor(250,250,250);
		$this->Cell(90,280, '', 0, 2, 'L', 1); 
		

		$this->SetXY(5,287);
		$this->SetTextColor(1, 1, 1);
		$this->SetFont('Arial','',7);
		$this->MultiCell(200,3,$reference, 0, "C", 0); 
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',7);
		$this->SetXY(5,4);
		$this->Cell(20,12,'identifiant', 0,2, "L", 0);
		$this->SetXY(5,6);
		$this->Cell(20,12,'client', 0,2, "L", 0); 
		$this->SetXY(20,5);
		$this->SetTextColor(100, 100, 100);
		$this->SetFont('Arial','',12);
		$this->Cell(80,12, $clientfacture, 0, 2, "L", 0); 
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',7);
		$this->SetXY(35,5);
		$this->Cell(20,12,'date:/', 0,2, "L", 0);
		$this->SetXY(50,5);
		$this->SetTextColor(100, 100, 100);
		$this->SetFont('Arial','',12);
		$this->Cell(30,12, $date, 0, 2, "L", 0); 
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',7);
		$this->SetXY(85,5);
		$this->Cell(10,12,'réf n°', 0,2, "L", 0);
		$this->SetXY(95,5);
		$this->SetTextColor(100, 100, 100);
		$this->SetFont('Arial','',12);
		$this->Cell(20,12, $ref , 0, 0, "L", 0); 
		
		$sur = '';
		if($pages) $sur = '/{nb}';
		$this->SetXY(180,5);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',7);
		$this->Cell(20,12,'Page '.$page.$sur,0,0,'R');
		
		$this->SetXY(4,13);
		$this->SetTextColor(150, 150, 150);
		$this->SetFont('Arial','B',25);
		$this->Cell(100,12, strtoupper($type), 0, 2, "L", 0); 
		
		$this->SetDrawColor(0,0,0);
		$this->SetXY(5,23);
		$this->SetTextColor(150, 150, 150);
		$this->SetFont('Arial','B',11);
		$this->Cell(150,6,$intitule, 0,0, "L", 0);
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,30);
		$this->Cell(15,6,'Réf.', 'B',0, "L", 0);
		$this->SetX(20);
		$this->Cell(100,6,'Désignation', 'B',0, "L", 0);
		$this->SetX(120);
		if($bdl==false) $this->Cell(30,6,"PU $taxe", 'B',0, "L", 0);
		else $this->Cell(30,6,"", 'B',0, "L", 0);
		$this->SetX(140);
		$this->Cell(20,6,'Quant.', 'B',0, "L", 0);
		$this->SetX(155);
		if($bdl==false) $this->Cell(30,6,'Remise', 'B',0, "L", 0);
		else $this->Cell(20,6,'', 'B',0, "L", 0);
		$this->SetX(175);
		if($bdl==false) $this->Cell(30,6,'TVA', 'B',0, "L", 0);
		else $this->Cell(10,6,'', 'B',0, "L", 0);
		$this->SetX(185);
		if($bdl==false) $this->Cell(30,6,"Total ( $devise $taxe)", 'B',0, "L", 0);
		else $this->Cell(30,6,"", 'B',0, "L", 0);
	}
	function titre($y,$des){
		$this->SetFillColor(230,230,230);
		$this->SetFont('Arial','B',11);
		$this->SetTextColor(100, 100, 100);
		$yo = $y;
		$this->SetXY(15,$yo);
		$this->Cell(210,6,$des, 'H',0, "L", 1);
		$_SESSION['y']+=6;
		$this->SetTextColor(0, 0, 0);
	}
	function comment($y,$des){
		$this->SetFont('Arial','',9);
		$this->SetTextColor(50, 50, 50);
		$yo = $y;
		$this->SetXY(20,$yo);
		$this->MultiCell(100,3.8,$des, 0, "L", 0); 
		$_SESSION['y'] =  ($this->GetY())+2;
		$this->SetTextColor(0, 0, 0);
	}
	function ligne($y,$des,$pu,$quant,$tva,$libre='',$coderef='',$remise=0){
		global $remise_app;
		$yo = $y;
		$this->SetFont('Arial','',7.5);
		$this->SetTextColor(20, 20, 20);
		$this->SetXY(5,$yo);
		$this->MultiCell(15,3.5,$coderef, 0,"L", 0);		
		$this->SetFont('Arial','',9.5);
		$this->SetTextColor(0, 0, 0);
		$this->SetY($yo);
		$this->SetX(20);
		$this->MultiCell(100,3.5,$des, 0, "L", 0); 
		$cury =  $this->GetY();
		$this->SetY($yo);
		$this->SetFillColor(240,240,240);
		$this->SetX(120);
		$this->Cell(15,6,number_format($pu,2,',',''), 0,0, "R", 1);
		$this->SetX(135);
		$this->Cell(15,6,$quant, 0,0, "C", 1);
		$this->SetX(145);
		$this->Cell(20,6,$remise."%", 0,0, "R", 1);
		$this->SetX(165);
		$this->Cell(22,6,number_format($tva,2,',','')."%", 0,0, "R", 1);
		$this->SetX(187);
		if($remise_app==1)  $this->Cell(20,6,number_format( round($pu*(100-$remise)/100,2)*$quant  ,2,',',''), 0,0, "R", 1);	
		else $this->Cell(20,6,number_format(($pu*$quant)*(100-$remise)/100,2,',',''), 0,0, "R", 1);	
		
		$this->SetX(207);
		$this->Cell(10,6,'', 0,0, "R", 1);
		$this->SetDrawColor(200,200,200);
		$this->Line(5,$cury+1,210,$cury+1);	
		$_SESSION['y'] = $cury+2;
	}
	function lignel($y,$des,$quant,$libre='',$coderef=''){
		$yo = $y;
		$this->SetFont('Arial','',9.5);
		$this->SetTextColor(0, 0, 0);
		$this->SetXY(5,$yo);
		$this->Cell(15,6,$coderef, 0,0, "L", 0);
		$this->SetTextColor(0, 0, 0);
		$this->SetX(20);
		$this->MultiCell(100,3.5,$des, 0, "L", 0);  
		$cury =  $this->GetY();
		$this->SetY($yo);
		$this->SetFillColor(240,240,240);
		//$this->SetX(120);
		//$this->Cell(15,6,number_format($pu,2,',',''), 0,0, "R", 1);
		$this->SetX(135);
		$this->Cell(15,6,$quant, 0,0, "C", 1);
		//$this->SetX(150);
		//$this->Cell(20,6,number_format($tva,2,',','')."%", 0,0, "R", 1);
		//$this->SetX(170);
		//$this->Cell(30,6,number_format($pu*$quant,2,',',''), 0,0, "R", 1);	
		$this->SetX(200);
		$this->Cell(10,6,'', 0,0, "R", 1);	
		$_SESSION['y'] = $cury+2;
	}
}

////////////////////////////////////////////////////////////////////////////////////////// LETTRE
class LETTRE extends FPDF{
	function ajout($titre,$date,$adresse,$pied,$entete_img,$fond_img,$co,$ca,$li){
		$date = date("d/m/Y",strtotime($date));

		$this->AddPage();
		
		
		
		if($fond_img!=""){$this->SetXY(0,0);$this->Image($fond_img,0,0,210);}
		$this->SetDrawColor(200,200,200);
		if($co!=''){
			$setco = split(',',$co);
			$this->SetXY(0,5);
			$this->SetFillColor($setco[0],$setco[1],$setco[2]);
			$this->Cell(210,33, '', 0, 2, "R", 1); 
			$this->SetFillColor(255,255,255);
		}
		if($entete_img!=""){$this->SetXY(5,5);$this->Image($entete_img,5,5,100,33);}
		
		if($li==1){
			$this->Line(5,280,200,280);
		}
		
		$this->SetXY(5,287);
		$this->SetTextColor(1, 1, 1);
		$this->SetFont('Arial','',7);
		$this->MultiCell(200,3,$pied, 0, "C", 0); 
		
		$this->SetXY(180,5);
		$this->SetFont('Arial','',9);
		$this->Cell(30,9, 'le '.$date, 0, 2, "R", 0); 
		$this->SetXY(110,10);
		$this->SetFont('Arial','',7);
		
			
		$this->SetXY(110,38);
		$this->SetFont('Arial','',11);
		$this->SetTextColor(0, 0, 0);
		//
		
		$this->Cell(102,35,'', $ca,0, "L", 0);
		$this->SetXY(112,40);
		$this->MultiCell(100,5,$adresse, 0, "L", 0); 
		
		$this->SetDrawColor(0,0,0);
		$this->SetXY(20,80);
		$this->SetTextColor(150, 150, 150);
		$this->SetFont('Arial','B',11);
		$this->Cell(150,6,$titre, 0,0, "L", 0);
	}
	function WriteHTML($html,$bi){
        //remove all unsupported tags
        $this->bi=$bi;
        if ($bi)
            $html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr><b><i><u><strong><em>");
        else
            $html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr>");
        $html=str_replace("\n",' ',$html); //replace carriage returns by spaces
        // debug
        if ($this->debug) { echo $html; exit; }

        $html = str_replace('&trade;','™',$html);
        $html = str_replace('&copy;','©',$html);
        $html = str_replace('&euro;','€',$html);

        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        $skip=false;
        foreach($a as $i=>$e){
            if (!$skip) {
                if($this->HREF)
                    $e=str_replace("\n","",str_replace("\r","",$e));
                if($i%2==0)
                {
                    // new line
                    if($this->PRE)
                        $e=str_replace("\r","\n",$e);
                    else
                        $e=str_replace("\r","",$e);
                    //Text
                    if($this->HREF) {
                        $this->PutLink($this->HREF,$e);
                        $skip=true;
                    } else
                        $this->Write(5,stripslashes(txtentities($e)));
                } else {
                    //Tag
                    if (substr(trim($e),0,1)=='/')
                        $this->CloseTag(strtoupper(substr($e,strpos($e,'/'))));
                    else {
                        //Extract attributes
                        $a2=explode(' ',$e);
                        $tag=strtoupper(array_shift($a2));
                        $attr=array();
                        foreach($a2 as $v) if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3)) {
                            $attr[strtoupper($a3[1])]=$a3[2];
                        }
                        $this->OpenTag($tag,$attr);
                    }
                }
            } else {
                $this->HREF='';
                $skip=false;
            }
        }
    }

	///////////////////////
	
	function hex2dec($color = '000000'){
		$tbl_color = array();
		if(!ereg('#',$color)) $color='#'.$color;
		$tbl_color['R']=hexdec(substr($color, 1, 2));
		$tbl_color['G']=hexdec(substr($color, 3, 2));
		$tbl_color['B']=hexdec(substr($color, 5, 2));
		return $tbl_color;
	}
	
	function px2mm($px){
		return $px*25.4/72;
	}
	
	function txtentities($html){
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans = array_flip($trans);
		return strtr($html, $trans);
	}

    function _convert($s) {
        if ($this->useiconv)
            return iconv($this->from,$this->to,$s);
        else
            return $s;
    }

    function _iso2ascii($s) {
        $iso="áèïéìíåµòóø¹»úùý¾äëöüÁÈÏÉÌÍÅ¥ÒÓØ©«ÚÙÝ®ÄËÖÜ";
        $asc="acdeeillnorstuuyzaeouACDEEILLNORSTUUYZAEOU";
        return strtr($s,$iso,$asc);
    }

    function _makeFileName($title) {
        $title = $this->_iso2ascii(strip_tags(trim($title)));
        preg_match_all('/[a-zA-Z0-9]+/', $title, $nt);
        return implode('-',$nt[0]);
    }
	
    function OpenTag($tag,$attr){
        //Opening tag
		$this->SetFont('Arial');
        switch($tag){
            case 'STRONG':
            case 'B':
                if ($this->bi)
                    $this->SetStyle('B',true);
                else
                    $this->SetStyle('U',true);
                break;
            case 'H1':
                $this->Ln(5);
                $this->SetTextColor(150,0,0);
                $this->SetFontSize(22);
                break;
            case 'H2':
                $this->Ln(5);
                $this->SetFontSize(18);
                $this->SetStyle('U',true);
                break;
            case 'H3':
                $this->Ln(5);
                $this->SetFontSize(16);
                $this->SetStyle('U',true);
                break;
            case 'H4':
                $this->Ln(5);
                $this->SetTextColor(102,0,0);
                $this->SetFontSize(14);
                if ($this->bi)
                    $this->SetStyle('B',true);
                break;
            case 'PRE':
                $this->SetFont('Courier','',11);
                $this->SetFontSize(11);
                $this->SetStyle('B',false);
                $this->SetStyle('I',false);
                $this->PRE=true;
                break;
            case 'RED':
                $this->SetTextColor(255,0,0);
                break;
            case 'BLOCKQUOTE':
                $this->SetTextColor(100,0,45);
                $this->Ln(3);
                break;
            case 'BLUE':
                $this->SetTextColor(0,0,255);
                break;
            case 'I':
            case 'EM':
                if ($this->bi)
                    $this->SetStyle('I',true);
                break;
            case 'U':
                $this->SetStyle('U',true);
                break;
            case 'A':
                $this->HREF=$attr['HREF'];
                break;
            case 'IMG':
                if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
                    if(!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if(!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                    $this->Ln(3);
                }
                break;
            case 'LI':
                $this->Ln(2);
                $this->SetTextColor(190,0,0);
                $this->Write(5,'     » ');
                $this->SetTextColor(-1);
                break;
            case 'TR':
                $this->Ln(7);
                $this->PutLine();
                break;
            case 'BR':
                $this->Ln(2);
                break;
            case 'P':
                $this->Ln(5);
                break;
            case 'HR':
                $this->PutLine();
                break;
            case 'FONT':
				if (isset($attr['COLOR']) and $attr['COLOR']!='') {
					$color=$attr['COLOR'];
					if(!ereg('#',$color)) $color='#'.$color;
					$tbl_color = array();
					$tbl_color['R']=hexdec(substr($color, 1, 2));
					$tbl_color['G']=hexdec(substr($color, 3, 2));
					$tbl_color['B']=hexdec(substr($color, 5, 2));
					$coul=$tbl_color;
					$this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
                    $this->issetcolor=true;
                }
                if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont=true;
                }
                break;
        }
    }

    function CloseTag($tag,$font='Arial'){
        //Closing tag
        if ($tag='H1' || $tag='H2' || $tag='H3' || $tag='H4'){
            $this->Ln(6);
            $this->SetFont($font,'',12);
            $this->SetFontSize(12);
            $this->SetStyle('U',false);
            $this->SetStyle('B',false);
            $this->SetTextColor(-1);
        }
        if ($tag='PRE'){
            $this->SetFont($font,'',12);
            $this->SetFontSize(12);
            $this->PRE=false;
        }
        if ($tag='RED' || $tag='BLUE')
            $this->SetTextColor(-1);
        if ($tag='BLOCKQUOTE'){
            $this->SetTextColor(0,0,0);
            $this->Ln(3);
        }
        if($tag=='STRONG')
            $tag='B';
        if($tag=='EM')
            $tag='I';
        if((!$this->bi) && $tag=='B')
            $tag='U';
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='FONT'){
            if ($this->issetcolor==true) {
                $this->SetTextColor(0,0,0);
            }
            if ($this->issetfont) {
                $this->SetFont($font,'',12);
                $this->issetfont=false;
            }
        }
		$this->SetFont($font,'',12);
    }
	function SetStyle($tag,$enable){
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
            if($this->$s>0)
                $style.=$s;
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt){
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(-1);
    }

    function PutLine(){
        $this->Ln(2);
        $this->Line($this->GetX(),$this->GetY(),$this->GetX()+187,$this->GetY());
        $this->Ln(3);
    }

    function mySetTextColor($r=-1,$g=0,$b=0){
        if ($r==-1)
            $this->SetTextColor(0,0,0);
        else {
            $this->SetTextColor($r,$g,$b);
        }
    }


}



class PDF_Rotate extends FPDF{
	var $angle=0;
	
	function Rotate($angle,$x=-1,$y=-1){
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}
	
	function _endpage(){
		if($this->angle!=0)
		{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
}

class PDF extends PDF_Rotate{
	function RotatedText($x,$y,$txt,$angle)	{
		//Rotation du texte autour de son origine
		$this->Rotate($angle,$x,$y);
		$this->Text($x,$y,$txt);
		$this->Rotate(0);
	}
	
	function RotatedImage($file,$x,$y,$w,$h,$angle)	{
		//Rotation de l'image autour du coin supérieur gauche
		$this->Rotate($angle,$x,$y);
		$this->Image($file,$x,$y,$w,$h);
		$this->Rotate(0);
	}
}


//////////////////////////////////// SPOT COLOR
class PDF_SpotColor extends FPDF{
	
}
?>
