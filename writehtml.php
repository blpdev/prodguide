<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

require('inc/fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->SetFont('Arial','',12);
$pdf->AddPage();
$text = 'This text <b>is bold</b> and this text <font color="red">is red</font>';
$pdf->WriteHTML($text);
//$pdf->Cell(0, 5, $text);
$pdf->Output('I');

?>