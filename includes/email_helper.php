<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function sendAppointmentConfirmation($patientEmail, $patientName, $doctorName, $appointmentDate, $appointmentTime, $appointmentNumber) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kelvin.makwani@strathmore.edu'; 
        $mail->Password   = 'rwdf izqe sxvp lpob';   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('kelvin.makwani@strathmore.edu', 'Doctor Appointment System');
        $mail->addAddress($patientEmail, $patientName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Appointment Confirmation';

        // Email body
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #f8f9fa; padding: 20px; text-align: center;'>
                <h2 style='color: #28a745;'>Appointment Confirmed!</h2>
            </div>
            
            <div style='padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-top: 20px;'>
                <p>Dear $patientName,</p>
                
                <p>Your appointment has been successfully booked. Here are the details:</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Doctor:</strong> Dr. $doctorName</p>
                    <p><strong>Date:</strong> $appointmentDate</p>
                    <p><strong>Time:</strong> $appointmentTime</p>
                    <p><strong>Appointment Number:</strong> $appointmentNumber</p>
                </div>
                
                <p>Please arrive 15 minutes before your scheduled appointment time.</p>
                
                <p>If you need to reschedule or cancel your appointment, please log in to your account or contact us.</p>
                
                <p style='margin-top: 20px;'>Best regards,<br>Doctor Appointment System Team</p>
            </div>
            
            <div style='text-align: center; margin-top: 20px; color: #666; font-size: 12px;'>
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>";

        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

// Add a test function
function testEmailConfiguration() {
    try {
        $result = sendAppointmentConfirmation(
            'test@example.com',
            'Test Patient',
            'Dr. Test',
            date('Y-m-d'),
            '10:00 AM',
            '001'
        );
        
        if($result) {
            echo "Test email configuration successful!";
        } else {
            echo "Test failed. Check error logs for details.";
        }
    } catch (Exception $e) {
        echo "Test failed: " . $e->getMessage();
    }
}


?>