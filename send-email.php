<?php
// Configuration
$to = "contact@maymayconsulting.ae"; // REMPLACEZ par votre email
$subject = "New Contact Form Submission - MayMay Consulting";
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: contact@maymayconsulting.ae" . "\r\n";
$headers .= "Reply-To: " . $_POST['email'] . "\r\n";

// Récupération des données du formulaire
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$service = htmlspecialchars($_POST['service']);
$message = htmlspecialchars($_POST['message']);

// Validation
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Construction du message HTML
$email_message = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0038A8; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #0038A8; }
        .footer { background: #eee; padding: 15px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
            <p>MayMay Consulting Dubai</p>
        </div>
        <div class='content'>
            <div class='field'>
                <span class='label'>Name:</span> $name
            </div>
            <div class='field'>
                <span class='label'>Email:</span> $email
            </div>
            <div class='field'>
                <span class='label'>Phone:</span> " . ($phone ? $phone : 'Not provided') . "
            </div>
            <div class='field'>
                <span class='label'>Service Interested In:</span> " . ($service ? $service : 'Not specified') . "
            </div>
            <div class='field'>
                <span class='label'>Message:</span><br>
                <p>" . nl2br($message) . "</p>
            </div>
        </div>
        <div class='footer'>
            <p>This email was sent from the contact form on MayMay Consulting website.</p>
            <p>Received at: " . date('Y-m-d H:i:s') . "</p>
        </div>
    </div>
</body>
</html>
";

// Envoi de l'email
if (mail($to, $subject, $email_message, $headers)) {
    // Optionnel: Sauvegarder dans un fichier CSV
    $csv_data = [
        date('Y-m-d H:i:s'),
        $name,
        $email,
        $phone,
        $service,
        substr($message, 0, 100) . '...'
    ];
    
    $csv_file = 'leads.csv';
    $file_exists = file_exists($csv_file);
    $fp = fopen($csv_file, 'a');
    
    if (!$file_exists) {
        fputcsv($fp, ['Date', 'Name', 'Email', 'Phone', 'Service', 'Message']);
    }
    
    fputcsv($fp, $csv_data);
    fclose($fp);
    
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again.']);
}
?>