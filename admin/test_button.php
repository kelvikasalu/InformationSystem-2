<!DOCTYPE html>
<html>
<head>
    <title>Test Report Generation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .test-form {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        select {
            padding: 8px;
            margin-right: 10px;
        }
        button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Test Report Generation</h2>
    
    <div class="test-form">
        <form action="process_report.php" method="POST">
            <select name="report_type" required>
                <option value="">Select Report Type</option>
                <option value="patients">Patients Report</option>
                <option value="doctors">Doctors Report</option>
                <option value="appointments">Appointments Report</option>
            </select>
            <button type="submit" name="generate_report">
                <i class="fas fa-file-alt"></i> Generate Report
            </button>
        </form>
    </div>
</body>
</html>