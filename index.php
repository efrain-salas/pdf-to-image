<?php

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require 'form.html';
    exit;
}

$id = $_POST['id'] ?? uniqid();
$workbenchPath = '/workbench';
$pdfFilePath = "$workbenchPath/$id.pdf";
move_uploaded_file($_FILES['file']['tmp_name'], $pdfFilePath);

$pdf = new Spatie\PdfToImage\Pdf($pdfFilePath);
$pdf->setCompressionQuality(100);
$pdf->saveAllPagesAsImages($workbenchPath, $id.'_');

$outputZipFilePath = "$workbenchPath/$id.zip";
exec("zip $outputZipFilePath $workbenchPath/*.jpg");

header('X-Num-Pages: ' . $pdf->getNumberOfPages());
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($outputZipFilePath).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($outputZipFilePath));
readfile($outputZipFilePath);

exec("rm $workbenchPath/$id*");
