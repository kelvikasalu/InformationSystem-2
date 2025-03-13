<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../vendor/autoload.php');
use Dompdf\Dompdf;
use Dompdf\Options;

try {
    // Initialize dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // Simple HTML
    $html = '<h1>Test PDF</h1><p>This is a test PDF generation.</p>';

    // Load HTML
    $dompdf->loadHtml($html);
    
    // Set paper size
    $dompdf->setPaper('A4', 'portrait');
    
    // Render the HTML as PDF
    $dompdf->render();
    
    // Output the generated PDF (1 = download and 0 = preview)
    $dompdf->stream("test.pdf", array("Attachment" => 0));

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>