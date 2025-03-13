<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once('../connection.php');

// Check if user is logged in
session_start();
if(!isset($_SESSION["user"]) || $_SESSION["user"]=="" || $_SESSION['usertype']!='a'){
    header("location: ../login.php");
    exit();
}

// Simple HTML table generation
function generateReport($type) {
    global $database;
    
    // Select query based on type
    switch($type) {
        case 'patients':
            $sql = "SELECT pname as Name, pemail as Email, ptel as Phone FROM patient";
            break;
        case 'doctors':
            $sql = "SELECT docname as Name, docemail as Email, specialties as Specialties FROM doctor";
            break;
        case 'appointments':
            $sql = "SELECT a.apponum as 'Appointment No', 
                          a.appodate as Date,
                          p.pname as Patient,
                          d.docname as Doctor
                   FROM appointment a
                   JOIN patient p ON a.pid = p.pid
                   JOIN schedule s ON a.scheduleid = s.scheduleid
                   JOIN doctor d ON s.docid = d.docid
                   ORDER BY a.appodate DESC";
            break;
        default:
            die("Invalid report type");
    }
    
    // Run query
    $result = $database->query($sql);
    
    if (!$result) {
        die("Query failed: " . $database->error);
    }
    
    // Output HTML
    echo '<!DOCTYPE html>
          <html>
          <head>
              <title>' . ucfirst($type) . ' Report</title>
              <style>
                  body { font-family: Arial, sans-serif; padding: 20px; }
                  .header { 
                      display: flex; 
                      justify-content: space-between; 
                      align-items: center; 
                      margin-bottom: 20px;
                  }
                  .download-buttons {
                      display: flex;
                      gap: 10px;
                  }
                  .download-btn {
                      padding: 10px 20px;
                      color: white;
                      text-decoration: none;
                      border-radius: 5px;
                      cursor: pointer;
                  }
                  .pdf-btn { background-color: #dc3545; }
                  .excel-btn { background-color: #28a745; }
                  table { 
                      border-collapse: collapse; 
                      width: 100%; 
                      margin-top: 20px;
                  }
                  th, td { 
                      border: 1px solid #ddd; 
                      padding: 12px; 
                      text-align: left; 
                  }
                  th { background-color: #f8f9fa; }
              </style>
          </head>
          <body>
              <div class="header">
                  <h1>' . ucfirst($type) . ' Report</h1>
                  <div class="download-buttons">
                      <a href="download_report.php?type=' . $type . '&format=pdf" class="download-btn pdf-btn">
                          Download PDF
                      </a>
                      <a href="download_report.php?type=' . $type . '&format=excel" class="download-btn excel-btn">
                          Download Excel
                      </a>
                  </div>
              </div>';
    
    if ($result->num_rows > 0) {
        echo "<table>";
        
        // Headers
        $first = $result->fetch_assoc();
        echo "<tr>";
        foreach ($first as $key => $value) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr>";
        
        // First row
        echo "<tr>";
        foreach ($first as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
        
        // Rest of the rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No data found</p>";
    }
    
    echo "</body></html>";
}

// Process request
if (isset($_POST['generate_report'])) {
    generateReport($_POST['report_type']);
} else {
    echo "No report type specified";
}
?>