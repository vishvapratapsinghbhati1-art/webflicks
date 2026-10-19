<?php
declare(strict_types=1);
/* Optional PHP contact handler. Set the destination email before deployment. */
$toEmail = 'hello@webflicksdigital.com';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.html#contact', true, 303); exit;
}
function clean_input(string $value): string {
  return trim(str_replace(["\r", "\n"], ' ', $value));
}
$name = clean_input((string)($_POST['name'] ?? ''));
$email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
$message = trim((string)($_POST['message'] ?? ''));
if ($name === '' || !$email || $message === '' || strlen($message) > 5000) {
  http_response_code(422); exit('Please provide a valid name, email and message.');
}
$subject = 'Website enquiry — WebFlicks Digital';
$body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
$headers = [
  'From' => 'website@' . ($_SERVER['HTTP_HOST'] ?? 'example.com'),
  'Reply-To' => $email,
  'Content-Type' => 'text/plain; charset=UTF-8'
];
if (@mail($toEmail, $subject, $body, $headers)) {
  header('Location: index.html?sent=1#contact', true, 303); exit;
}
http_response_code(500);
echo 'Message could not be sent. Please contact ' . htmlspecialchars($toEmail, ENT_QUOTES, 'UTF-8');
