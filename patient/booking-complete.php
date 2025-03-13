<?php

    //learn from w3schools.com

    session_start();
    require_once('../includes/email_helper.php');

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");

    if(isset($_POST["booknow"])){
        $scheduleid = $_POST["scheduleid"];
        $date = $_POST["date"];
        $apponum = $_POST["apponum"];
        
        // Get patient ID
        $userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
        $userfetch=$userrow->fetch_assoc();
        $userid= $userfetch["pid"];
        $patientName = $userfetch["pname"];
        
        // Check if patient has already booked this session
        $checkBooking = "SELECT * FROM appointment 
                        WHERE pid = ? AND scheduleid = ?";
        $stmt = $database->prepare($checkBooking);
        $stmt->bind_param("ii", $userid, $scheduleid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            // Patient has already booked this session
            echo '
            <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; margin: 20px; border-radius: 5px;">
                <h3 style="margin-top: 0;">Booking Failed</h3>
                <p>You have already booked an appointment for this session.</p>
                <a href="schedule.php" style="color: #721c24; text-decoration: underline;">Go Back to Schedule</a>
            </div>';
        } else {
            // Proceed with booking
            $sql2="insert into appointment(pid,apponum,scheduleid,appodate) values ($userid,$apponum,$scheduleid,'$date')";
            $result= $database->query($sql2);
            
            // Get session details for confirmation message
            $sqlGetSession = "SELECT s.*, d.docname 
                             FROM schedule s 
                             JOIN doctor d ON s.docid = d.docid 
                             WHERE s.scheduleid = ?";
            $stmt = $database->prepare($sqlGetSession);
            $stmt->bind_param("i", $scheduleid);
            $stmt->execute();
            $sessionResult = $stmt->get_result();
            $sessionDetails = $sessionResult->fetch_assoc();
            
            // Send email notification
            $emailSent = sendAppointmentConfirmation(
                $useremail,
                $patientName,
                $sessionDetails['docname'],
                $date,
                $sessionDetails['scheduletime'],
                $apponum
            );
            
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Booking Confirmation</title>
                <style>
                    .success-container {
                        max-width: 600px;
                        margin: 50px auto;
                        padding: 20px;
                        background-color: #d4edda;
                        border: 1px solid #c3e6cb;
                        border-radius: 5px;
                        text-align: center;
                    }
                    .success-icon {
                        color: #155724;
                        font-size: 48px;
                        margin-bottom: 20px;
                    }
                    .success-title {
                        color: #155724;
                        margin-bottom: 15px;
                    }
                    .appointment-details {
                        background-color: white;
                        padding: 15px;
                        border-radius: 5px;
                        margin: 20px 0;
                        text-align: left;
                    }
                    .back-btn {
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #28a745;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        margin-top: 15px;
                    }
                    .back-btn:hover {
                        background-color: #218838;
                    }
                    .email-success {
                        color: #155724;
                        background-color: #d4edda;
                        border: 1px solid #c3e6cb;
                        padding: 10px;
                        border-radius: 4px;
                        margin: 10px 0;
                    }
                    .email-warning {
                        color: #856404;
                        background-color: #fff3cd;
                        border: 1px solid #ffeeba;
                        padding: 10px;
                        border-radius: 4px;
                        margin: 10px 0;
                    }
                </style>
            </head>
            <body>
                <div class="success-container">
                    <div class="success-icon">✓</div>
                    <h2 class="success-title">Appointment Booked Successfully!</h2>
                    <div class="appointment-details">
                        <p><strong>Doctor:</strong> ' . htmlspecialchars($sessionDetails['docname']) . '</p>
                        <p><strong>Date:</strong> ' . htmlspecialchars($date) . '</p>
                        <p><strong>Time:</strong> ' . htmlspecialchars($sessionDetails['scheduletime']) . '</p>
                        <p><strong>Your Appointment Number:</strong> ' . htmlspecialchars($apponum) . '</p>
                    </div>
                    ' . ($emailSent ? 
                        '<p class="email-success">A confirmation email has been sent to your email address.</p>' 
                        : 
                        '<p class="email-warning">Appointment booked, but email confirmation could not be sent.</p>'
                    ) . '
                    <a href="appointment.php" class="back-btn">View My Appointments</a>
                </div>
            </body>
            </html>';
        }
    }
 ?>