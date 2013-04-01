<?php
//incluindo o arquivo do fpdf
require_once("src/fpdf.php");

//defininfo a fonte !
define('FPDF_FONTPATH','src/font/');

//instancia a classe.. P=Retrato, mm =tipo de medida utilizada no casso milimetros, tipo de folha =A4
$pdf = new FPDF('L', 'cm', 'A4'); //1240, 1754 

$pdf->AddPage(); 

$pdf->Image('img/GELS123.jpg', 0, 0, 210, 295); 

$pdf->SetFont('Arial', 'B', 23); 

$name = "Guilherme Oliveira"; 

$pdf->Text(20, 20, $name); 

$pdf->Output();  

?>