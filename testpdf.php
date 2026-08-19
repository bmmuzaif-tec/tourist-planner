<?php

require('fpdf/fpdf.php');

$pdf = new FPDF();

$pdf->AddPage();

$pdf->SetFont('Arial','B',20);

$pdf->Cell(0,20,'FPDF is Working Successfully!',0,1,'C');

$pdf->Ln(10);

$pdf->SetFont('Arial','',12);

$pdf->Cell(0,10,'Local Tourist Day Visit Planner',0,1,'C');

$pdf->Output();
?>