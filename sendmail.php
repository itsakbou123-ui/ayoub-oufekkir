<?php
$isJsonRequest = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
if ($isJsonRequest) {
	header('Content-Type: application/json; charset=utf-8');
}

function respond($success, $message) {
	if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
		echo json_encode(array('success' => $success, 'message' => $message));
	} else {
		echo $message;
	}
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	respond(false, 'Invalid request.');
}

// Variables
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Email address validation - works with php 5.2+
function is_email_valid($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}


if( $name !== '' && $subject !== '' && $message !== '' && is_email_valid($email) ) {

	// Avoid Email Injection and Mail Form Script Hijacking
	$pattern = "/(content-type|bcc:|cc:|to:)/i";
	if( preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $message) ) {
		respond(false, 'Invalid message.');
	}

	// Email will be send
	$to = "Oufekkir1ayoub@gmail.com";
	$sub = $subject; // You can define email subject
	// HTML Elements for Email Body
	$body = <<<EOD
	<strong>Name:</strong> $name <br>
	<strong>Email:</strong> <a href="mailto:$email?subject=feedback" "email me">$email</a> <br> <br>
	<strong>Message:</strong> $message <br>
EOD;
//Must end on first column
	
	$headers = "From: Website Contact Form <no-reply@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . ">\r\n";
	$headers .= "Reply-To: $email\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// PHP email sender
	if (mail($to, $sub, $body, $headers)) {
		respond(true, 'Message sent successfully.');
	}
	respond(false, 'The server could not send the message.');
}

respond(false, 'Please complete all fields with a valid email address.');
?>