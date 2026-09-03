<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    die(json_encode(['success' => true]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Invalid request method.']));
}

try {
    @include __DIR__ . '/mail-config.php';
    
    $config = (isset($config)) ? $config : [
        'mail_to' => 'moudieid2016@gmail.com',
        'mail_from' => 'noreply@engagement.local'
    ];
    
    $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
    $message = isset($_POST['message']) ? trim((string)$_POST['message']) : '';

    if (empty($message)) {
        http_response_code(400);
        die(json_encode(['success' => false, 'message' => 'Please write a message before sending.']));
    }

    $to = $config['mail_to'] ?? 'moudieid2016@gmail.com';
    $from = $config['mail_from'] ?? 'noreply@engagement.local';
    $subject = 'New engagement wish from ' . (empty($name) ? 'Guest' : $name);
    $body = "You received a new wish for Mahmoud and Aya.\n\n";
    $body .= "Name: " . (empty($name) ? 'Guest' : $name) . "\n\n";
    $body .= "Message:\n" . $message . "\n";

    $headers = "From: {$from}\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $sendSuccess = @mail($to, $subject, $body, $headers);

    if ($sendSuccess) {
        die(json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent to Mahmoud and Aya.']));
    } else {
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Your wish could not be sent. Please try again later.']));
    }
} catch (Throwable $e) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Server error occurred.']));
}
