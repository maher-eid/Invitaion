<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$config = require __DIR__ . '/mail-config.php';
$name = trim((string) ($_POST['name'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($message === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please write a message before sending.']);
    exit;
}

$to = $config['mail_to'];
$from = $config['mail_from'];
$subject = 'New engagement wish from ' . ($name !== '' ? $name : 'Guest');
$body = "You received a new wish for Mahmoud and Aya.\n\n";
$body .= "Name: " . ($name !== '' ? $name : 'Guest') . "\n\n";
$body .= "Message:\n" . $message . "\n";

$sendSuccess = false;

if (!empty($config['smtp_host']) && !empty($config['smtp_username']) && !empty($config['smtp_password'])) {
    require __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
    require __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_username'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = $config['smtp_encryption'];
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($from, 'Engagement Wishes');
        $mail->addAddress($to, 'Mahmoud and Aya');
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;

        $mail->send();
        $sendSuccess = true;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'The message could not be sent at this time. Please configure your SMTP settings in mail-config.php.'
        ]);
        exit;
    }
} else {
    $headers = "From: {$from}\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $sendSuccess = mail($to, $subject, $body, $headers);
}

if ($sendSuccess) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent to Mahmoud and Aya.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Your wish could not be sent right now. Please try again later.'
    ]);
}
