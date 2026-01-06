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

$numPages = $pdf->getNumberOfPages();
header('X-Num-Pages: ' . $numPages);

if ($numPages === 1) {
    // Solo una página: devolver la imagen directamente
    $imageFilePath = "$workbenchPath/{$id}_1.jpg";

    header('Content-Description: File Transfer');
    header('Content-Type: image/jpeg');
    header('Content-Disposition: attachment; filename="'.$id.'.jpg"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($imageFilePath));
    readfile($imageFilePath);
} else {
    // Múltiples páginas: crear y devolver ZIP
    $zipFileName = "$id.zip";
    $outputZipFilePath = "$workbenchPath/$zipFileName";
    exec("cd $workbenchPath && zip $zipFileName $id*.jpg");

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($outputZipFilePath).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($outputZipFilePath));
    readfile($outputZipFilePath);
}

exec("rm $workbenchPath/$id*");
