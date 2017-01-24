<?php
require('../fpdf.php');

$x=$_POST['x'];
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,$x);
$pdf->Output();
?>
