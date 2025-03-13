<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../connection.php');
require_once('../vendor/autoload.php'); // For PDF generation

use Dompdf\Dompdf;
use Dompdf\Options;

function getReportData($type) {
    global $database;
    
    switch($type) {
        case 'patients':
            $sql = "SELECT pname as Name, pemail as Email, ptel as Phone FROM patient";
            break;
        case 'doctors':
            $sql = "SELECT docname as Name, docemail as Email, specialties as Specialties FROM doctor";
            break;
        case 'appointments':
            $sql = "SELECT 
                    a.apponum as 'Appointment No',
                    a.appodate as Date,
                    p.pname as Patient,
                    d.docname as Doctor
                FROM appointment a
                LEFT JOIN patient p ON a.pid = p.pid
                LEFT JOIN schedule s ON a.scheduleid = s.scheduleid
                LEFT JOIN doctor d ON s.docid = d.docid
                ORDER BY a.appodate DESC";
            break;
        default:
            die("Invalid report type");
    }
    
    return $database->query($sql);
}

if(isset($_GET['type']) && isset($_GET['format'])) {
    $type = $_GET['type'];
    $format = $_GET['format'];
    $result = getReportData($type);
    
    if($format === 'pdf') {
        // Generate PDF using DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        
        // Build HTML content
        $html = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                }
                h1 {
                    color: #333;
                    text-align: center;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                th, td {
                    padding: 10px;
                    border: 1px solid #ddd;
                    text-align: left;
                }
                th {
                    background-color: #f4f4f4;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #f8f8f8;
                }
                .report-header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .report-date {
                    text-align: right;
                    margin-bottom: 20px;
                    font-size: 0.9em;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class='report-header'>
                <h1>" . ucfirst($type) . " Report</h1>
                <div class='report-date'>Generated on: " . date('Y-m-d H:i:s') . "</div>
            </div>";
        
        // Add table content
        if($result->num_rows > 0) {
            $html .= "<table>";
            $first = $result->fetch_assoc();
            
            // Headers
            $html .= "<tr>";
            foreach($first as $key => $value) {
                $html .= "<th>" . htmlspecialchars($key) . "</th>";
            }
            $html .= "</tr>";
            
            // First row
            $html .= "<tr>";
            foreach($first as $value) {
                $html .= "<td>" . htmlspecialchars($value) . "</td>";
            }
            $html .= "</tr>";
            
            // Rest of the rows
            while($row = $result->fetch_assoc()) {
                $html .= "<tr>";
                foreach($row as $value) {
                    $html .= "<td>" . htmlspecialchars($value) . "</td>";
                }
                $html .= "</tr>";
            }
            
            $html .= "</table>";
        }
        
        $html .= "</body></html>";
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $type . '_report.pdf"');
        echo $dompdf->output();
        
    } elseif($format === 'excel') {
        // Generate Excel file
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $type . '_report.xls"');
        
        echo "<table border='1'>";
        
        if($result->num_rows > 0) {
            $first = $result->fetch_assoc();
            
            // Headers
            echo "<tr>";
            foreach($first as $key => $value) {
                echo "<th style='background-color: #f4f4f4; font-weight: bold;'>" 
                    . htmlspecialchars($key) . "</th>";
            }
            echo "</tr>";
            
            // First row
            echo "<tr>";
            foreach($first as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
            
            // Rest of the rows
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
        }
        
        echo "</table>";
    }
}
?>