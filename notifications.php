<?php
declare(strict_types=1);

function notifyCourseEnquiry(array $enquiry): void
{
    if (($enquiry['interest'] ?? '') !== 'Academy Courses') return;

    $body = implode("\n", [
        'A new academy course enquiry was received.',
        '',
        'Name: ' . $enquiry['name'],
        'Phone: ' . $enquiry['phone'],
        'Email: ' . ($enquiry['email'] ?: 'Not provided'),
        'Preferred date: ' . ($enquiry['preferred_date'] ?: 'Not specified'),
        'Message: ' . ($enquiry['message'] ?: 'Not provided'),
    ]);

    sendCourseEnquiryEmails($enquiry, $body);
    sendCourseEnquiryWhatsApp($enquiry);
}

function sendCourseEnquiryEmails(array $enquiry, string $body): void
{
    $autoload = __DIR__ . '/vendor/autoload.php';
    $username = getenv('SMTP_USERNAME') ?: '';
    $password = getenv('SMTP_PASSWORD') ?: '';
    if (!is_file($autoload) || $username === '' || $password === '') return;
    require_once $autoload;

    sendSmtpMail(getenv('NOTIFICATION_EMAIL') ?: 'polishandglow15@gmail.com', 'New Polish & Glow academy course enquiry', $body, $username, $password);

    $customerEmail = trim((string)($enquiry['email'] ?? ''));
    if ($customerEmail !== '') {
        $customerBody = "Hello {$enquiry['name']},\n\nThank you for enquiring about the Polish & Glow Nail Art Academy. We have received your details and our team will contact you shortly.\n\nYour phone: {$enquiry['phone']}\nPreferred date: " . ($enquiry['preferred_date'] ?: 'Not specified') . "\n\nPolish & Glow Nail Art Studio & Academy\nWhatsApp: +91 96532 03795";
        sendSmtpMail($customerEmail, 'We received your Polish & Glow academy enquiry', $customerBody, $username, $password);
    }
}

function sendSmtpMail(string $recipient, string $subject, string $body, string $username, string $password): void
{
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int)(getenv('SMTP_PORT') ?: 587);
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($username, 'Polish & Glow');
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Throwable $exception) {
        error_log('Course enquiry email failed: ' . $exception->getMessage());
    }
}

function sendCourseEnquiryWhatsApp(array $enquiry): void
{
    $token = getenv('WHATSAPP_ACCESS_TOKEN') ?: '';
    $phoneNumberId = getenv('WHATSAPP_PHONE_NUMBER_ID') ?: '';
    $recipient = getenv('WHATSAPP_NOTIFICATION_TO') ?: '';
    $template = getenv('WHATSAPP_TEMPLATE_NAME') ?: '';
    $language = getenv('WHATSAPP_TEMPLATE_LANGUAGE') ?: 'en';
    if ($token === '' || $phoneNumberId === '' || $recipient === '' || $template === '') return;

    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => preg_replace('/\D+/', '', $recipient),
        'type' => 'template',
        'template' => [
            'name' => $template,
            'language' => ['code' => $language],
            'components' => [[
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => (string)$enquiry['name']],
                    ['type' => 'text', 'text' => (string)$enquiry['phone']],
                    ['type' => 'text', 'text' => (string)($enquiry['preferred_date'] ?: 'Not specified')],
                    ['type' => 'text', 'text' => (string)($enquiry['message'] ?: 'Not provided')],
                ],
            ]],
        ],
    ];

    $request = curl_init('https://graph.facebook.com/v23.0/' . rawurlencode($phoneNumberId) . '/messages');
    curl_setopt_array($request, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);
    $response = curl_exec($request);
    $status = (int)curl_getinfo($request, CURLINFO_HTTP_CODE);
    $error = curl_error($request);
    curl_close($request);
    if ($response === false || $status < 200 || $status >= 300) {
        error_log('WhatsApp course notification failed. HTTP ' . $status . ' ' . $error . ' ' . (string)$response);
    }
}
