<?php
require('../plugins/fpdf/fpdf.php');


class pdf_estruct_gen extends FPDF
{
var $widths;
var $aligns;
protected $B = 0;
protected $I = 0;
protected $U = 0;
protected $HREF = '';


function Header()
    {

        //session_start();
        
        //$this->Image('../images/logo_mini.png',270,7,20);
        $this->SetFont('Arial','B',14);
        
        if($_SESSION["reporte"]!="hist")
        {
            $this->Cell(0,3,utf8_decode('REPORTE DE PERSONAS '.strtoupper($_SESSION["reporte"]).',  FECHA: '.date('d-m-Y')),2,1,'L');
        }
        else
        {
            $this->Cell(0,3,utf8_decode('REPORTE DE PERSONAS '.strtoupper("historial cortes").',  FECHA: '.date('d-m-Y')),2,1,'L');
        }

        $this->Ln();
    }
    
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-20);
        // Arial italic 8
        $this->SetFont('Arial','I',6);
        // Número de página
        $this->Cell(0,3,utf8_decode(''),0,1,'L');
        $this->SetFont('Arial','B',7);
        $this->Ln(3);
        $this->Cell(0,3,'Hoja '.$this->PageNo(),0,0,'C');
        
    }


function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function encabezado($data)
{

    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=12*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h,$data);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->SetFillColor(36,138,175);
        $this->SetTextColor(255,255,255);
        //$this->Rect($x,$y,$w,$h,'F');
        //Print the text
        $this->MultiCell($w,10,utf8_decode($data[$i]),1,$a,true); //Se usa el decode porque los valores que recibe se escribieron en el archivo que implementa
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line

    $this->Ln($h);
}

function fila($cont,$row,$header)
{
    //var_dump($row);
       // echo count($row);
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($row);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$row[$i]));
    $h=17*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h,$header);
    
    //Draw the cells of the row
    $i=0;
    foreach($cont as $id)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->SetFont('','',10);
        $this->MultiCell($w,5,"\n".utf8_decode($id),'T',$a);   //No se usa el decode porque los valores que recibe vienen codificados desde la BD
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
        
    }
    foreach($row as $col)
    {
        $w=$this->widths[$i+1];
        $a=isset($this->aligns[$i+1]) ? $this->aligns[$i+1] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->SetFont('','',10);
        $this->MultiCell($w,5,"\n".utf8_decode($col),'T',$a);   //No se usa el decode porque los valores que recibe vienen codificados desde la BD
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
        $i++;
    }
    //Go to the next line
 
    $this->Ln($h);
}


function CheckPageBreak($h,$header)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
    {

        $this->AddPage($this->CurOrientation);
        $this->encabezado($header);
    }

    
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

function WriteHTML($html)
{
    // Intérprete de HTML
    $html = str_replace("\n",' ',$html);
    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            // Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            // Etiqueta
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                // Extraer atributos
                $a2 = explode(' ',$e);
                $tag = strtoupper(array_shift($a2));
                $attr = array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])] = $a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    // Etiqueta de apertura
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF = $attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    // Etiqueta de cierre
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF = '';
}

function SetStyle($tag, $enable)
{
    // Modificar estilo y escoger la fuente correspondiente
    $this->$tag += ($enable ? 1 : -1);
    $style = '';
    foreach(array('B', 'I', 'U') as $s)
    {
        if($this->$s>0)
            $style .= $s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    // Escribir un hiper-enlace
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}



}//fin de la clase
?>