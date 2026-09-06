<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed.']); exit; }
if (trim((string)($_POST['website'] ?? '')) !== '') { http_response_code(400); echo json_encode(['success' => false, 'message' => 'Unable to process this request.']); exit; }
$name = trim((string)($_POST['name'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$interest = trim((string)($_POST['interest'] ?? ''));
$preferredDate = trim((string)($_POST['preferred_date'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$allowed = ['Nail Art / Extensions', 'Manicure', 'Pedicure', 'Academy Courses'];
if ($name === '' || mb_strlen($name) > 100 || !preg_match('/^\+?[0-9\s-]{8,15}$/', $phone) || !in_array($interest, $allowed, true)) { http_response_code(422); echo json_encode(['success' => false, 'message' => 'Please provide valid enquiry details.']); exit; }
if ($preferredDate !== '') { $date = DateTimeImmutable::createFromFormat('Y-m-d', $preferredDate); if (!$date || $date->format('Y-m-d') !== $preferredDate) { http_response_code(422); echo json_encode(['success' => false, 'message' => 'Please choose a valid preferred date.']); exit; } }
if (mb_strlen($message) > 2000) { http_response_code(422); echo json_encode(['success' => false, 'message' => 'The message is too long.']); exit; }
try {
    require_once dirname(__DIR__) . '/database.php';
    $statement = database()->prepare('INSERT INTO enquiries (name, phone, interest, preferred_date, message) VALUES (:name, :phone, :interest, :preferred_date, :message)');
    $statement->execute([':name' => $name, ':phone' => $phone, ':interest' => $interest, ':preferred_date' => $preferredDate !== '' ? $preferredDate : null, ':message' => $message !== '' ? $message : null]);
    $config = require dirname(__DIR__) . '/config.php';
    $text = "Hello Polish & Glow! ✨\n\nName: {$name}\nPhone: {$phone}\nInterested in: {$interest}\nPreferred date: " . ($preferredDate !== '' ? $preferredDate : 'Not specified') . "\nMessage: " . ($message !== '' ? $message : '—');
    echo json_encode(['success' => true, 'whatsapp_url' => 'https://wa.me/' . $config['whatsapp'] . '?text=' . rawurlencode($text)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    error_log($exception->getMessage()); http_response_code(500); echo json_encode(['success' => false, 'message' => 'We could not save your enquiry. Please contact us on WhatsApp.']);
}
